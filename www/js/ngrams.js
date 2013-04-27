var getNGrams = function( sentence ) {
	var tokens = tokenize( sentence );
	var ngrams = {};
	
	ngrams[1] = tokens;
	for( var i = 1; i < 4; i++ ) {
		ngrams[i+1] = [];

		for( var j = 0; i + j < tokens.length; j++ ) {
			ngrams[i+1].push( ngrams[i][j] + " " + tokens[i+j] );	
		}
	}

	return ngrams;
}

var tokenize = function( sentence ) {
	return sentence.split( /\s+/ );
}

var getMatchingPositions = function( referenceNGrams, translationNGrams ) {
	var matchingNGrams = intersection( referenceNGrams, translationNGrams );
	var matchingPositions = globalAlignment( referenceNGrams[1], translationNGrams[1] );

	var matchingInReference = guessAllMatchingPositions( matchingNGrams, referenceNGrams, matchingPositions.reference );
	var matchingInTranslation = guessAllMatchingPositions( matchingNGrams, translationNGrams, matchingPositions.translation ); 

	return {
		"reference": matchingInReference,
		"translation": matchingInTranslation
	}; 
}


var guessAllMatchingPositions = function( matchingNGrams, allNGrams, alignedPositions ) {
	var isMatching = [];
	for( var i = 0; i < allNGrams[1].length; i++ ) {
		isMatching[i] = false;
	}

	var matchingPositionsByLength = guessPositionsOfMatching( matchingNGrams, allNGrams, alignedPositions );
	for( var length in matchingPositionsByLength ) {
		for( var ngram in matchingPositionsByLength[ length ] ) {
			matchingPositionsByLength[ length ][ ngram ].forEach( function( position ) {
				for( var i = 0; i < length; i++ ) {
					isMatching[ position + i ] = true;
				}
			} );
		}
	}

	return isMatching;
}

var guessPositionsOfMatching = function( matching, all, alignedPositions ) {
	var matchingOccurences = countOccurences( matching );
	var allOccurences = countOccurences( all );
	var allPositions = getPositions( all );

	var positions = {};
	var score = {};
	for( var i = 4; i >= 1; i-- ) {
		positions[i] = [];

		// UPDATE TMP SCORE FOR EACH NGRAM
		var tmpScore = score;
		for( var key in matchingOccurences[i] ) {
			for( var pos in allPositions[i][key] ) {
				for( var j = 0; j < i; j++ ) {
					var index = allPositions[i][key][pos] + j; 
					tmpScore[index] = idOrDefault( tmpScore[index], 0 ) + idOrDefault( alignedPositions[index], 0 ) + 1;
				}	
			}
		}

		for( var key in matchingOccurences[i] ) {
			if( matchingOccurences[i][key] == allOccurences[i][key] ) {
				positions[i][key] = allPositions[i][key];
			} else {
				positions[i][key] = allPositions[i][key]
					.map( function( position ) {
						var localScore = 0;
						for( var pos = 0; pos < i; pos++ ) {
							localScore += idOrDefault( tmpScore[position + pos] );
						}

						return {
							"ngram": key,
							"position": position,
							"score": localScore 
						};	
					} )
					.sort( function( a, b ) {
						return b.score - a.score;
					} )
					.slice( 0, matchingOccurences[i][key] )
					.map( function( item ) {
						return item.position;
					} );
			}

			// UPDATE SCORE FOR EACH NGRAM
			for( var pos in positions[i][key] ) {
				for( var j = 0; j < i; j++ ) {
					var index = positions[i][key][pos] + j; 

					score[index] = idOrDefault( score[index], 0 ) + 1;
				}	
			}
		}
	}
	
	return positions;
}

var globalAlignment = function( reference, translation ) {
	var traceBack = computeTracebackMatrix( reference, translation, function( a, b ) { return ( a == b ) ? 1 : 0; }, -1 );

	var matchingPositions = {
		'reference': {},
		'translation': {},
	};
	var i = reference.length;
	var j = translation.length;
	while( i != 0 || j != 0 ) {
		switch( traceBack[j][i] ) {
			case 'M':
				i--;
				j--;
				matchingPositions.reference[i] = 1;
				matchingPositions.translation[j] = 1;
				break;
			case 'D':
				j--;
				break;
			case 'I':
				i--;
				break;
		}
	}

	return matchingPositions;
}

var computeTracebackMatrix = function( reference, translation, s, d ) {
	var matrix = [];
	var traceBack = [];
	for( var j = 0; j <= translation.length; j++ ) {
		traceBack[j] = [];
		traceBack[j][0] = 'D';
		matrix[j] = [];
		matrix[j][0] = j * d;
	}

	for( var i = 0; i <= reference.length; i++ ) {
		traceBack[0][i] = 'I';
		matrix[0][i] = i * d;
	}

	for( var j = 1; j <= translation.length; j++ ) {
		for( var i = 1; i <= reference.length; i++ ) {
			var match = matrix[j-1][i-1] + s( reference[i-1], translation[j-1] );
			var del = matrix[j-1][i] + d;
			var ins = matrix[j][i-1] + d;

			matrix[j][i] = Math.max( match, Math.max( del, ins ) );
			if( match == matrix[j][i] ) {
				traceBack[j][i] = 'M';
			} else if( del == matrix[j][i] ) {
				traceBack[j][i] = 'D';
			} else {
				traceBack[j][i] = 'I';
			}
		}
	}

	return traceBack;
}

var idOrDefault = function( value, defaultValue ) {
	if( typeof value == "undefined" ) {
		return defaultValue;
	}

	return value;
}

var getMatchingNGrams = function( reference, translation ) {
	return intersection( reference, translation );
}

var getNotMatchingNGrams = function( reference, translation ) {
	return diff( translation, reference );
}

var getImproving = function( reference, translations ) {
	var matching = translations.map( function( translation ) { return getMatchingNGrams( reference, translation ); } );
	var commonMatching = intersection( matching[0], matching[1] );
	
	var improving = [];
	translations.forEach( function( translation, translationNumber ) {
		var matchingPositions = globalAlignment( reference[1], translation[1] );
		var matchingInOneTranslation = guessAllMatchingPositions( matching[ translationNumber ], translation, matchingPositions.translation ); 
		var matchingInBothTranslations = guessAllMatchingPositions( commonMatching, translation, matchingPositions.translation );
		var currentImproving = [];
		for( var i in matchingInOneTranslation ) {
			currentImproving[i] = matchingInOneTranslation[i] && !matchingInBothTranslations[i];
		}

		improving.push( currentImproving );
	} ); 

	return improving;
}

var getWorsening = function( reference, translations ) {
	var matching = translations.map( function( translation ) { return getMatchingNGrams( reference, translation ); } );
	var notMatching = translations.map( function( translation ) { return getNotMatchingNGrams( reference, translation ); } );
	var commonNotMatching = intersection( notMatching[0], notMatching[1] );
	
	var worsening = [];
	translations.forEach( function( translation, translationNumber ) {
		var notMatchingPositions = globalAlignment( translations[ 1 - translationNumber ][1], translation[1] );
		var notMatchingInOneTranslation = guessAllMatchingPositions( notMatching[ translationNumber ], translation, notMatchingPositions.translation ); 
		var notMatchingInBothTranslations = guessAllMatchingPositions( commonNotMatching, translation, notMatchingPositions.translation );

		var matchingPositions = globalAlignment( reference[1], translation[1] );
		var matchingInOneTranslation = guessAllMatchingPositions( matching[ translationNumber ], translation, matchingPositions.translation ); 

		var currentImproving = [];
		for( var i in notMatchingInOneTranslation ) {
			currentImproving[i] = notMatchingInOneTranslation[i] && !notMatchingInBothTranslations[i] && !matchingInOneTranslation[i];
		}

		worsening.push( currentImproving );
	} ); 

	return worsening;
}


var intersection = function( setA, setB ) {
	return iterateElements( setA, setB, function( aCount, bCount ) {
		return Math.min( aCount, bCount );
	} ); 
}

var diff = function( setA, setB ) {
	return iterateElements( setA, setB, function( aCount, bCount ) {
		return Math.max( 0, aCount - bCount );
	} );
}

var iterateElements = function( setA, setB, newSetElementCount ) {
	var setAOccurences = countOccurences( setA );
	var setBOccurences = countOccurences( setB );

	var multiset = {};
	for( var i = 1; i <= 4; i++ ) {
		multiset[i] = [];

		for( var item in setAOccurences[i] ) {
			var count = newSetElementCount( setAOccurences[i][item], idOrDefault( setBOccurences[i][item], 0 ) );
			for( var j = 0; j < count; j++ ) {
				multiset[i].push( item );
			}
		}
	}

	return multiset;
}

var countOccurences = function( multiset ) {
	var occurences = {};
	for( var i = 1; i <= 4; i++ ) {
		occurences[i] = {};

		for( var j in multiset[i] ) {
			occurences[i][multiset[i][j]] = idOrDefault( occurences[i][multiset[i][j]], 0 );
			occurences[i][multiset[i][j]]++;
		}
	}

	return occurences;
}

var getPositions = function( set ) {
	var positions = {};
	for( var i = 1; i <= 4; i++ ) {
		positions[i] = {};

		for( var j in set[i] ) {
			positions[i][set[i][j]] = idOrDefault( positions[i][set[i][j]], [] );
			positions[i][set[i][j]].push( parseInt( j ) );
		}
	}	

	return positions;
}

var initClasses = function( tokens ) {
	var ngrams = getNGrams( tokens.join( ' ' ) );
	var tokens = tokens.map( function( token ) {
		return {
			'token': token,
			'class': [],
			'ngrams': {}
		};
	} );

	[1,2,3,4].forEach( function( length ) {
		ngrams[ length ].forEach( function( ngram, index ) {
			for( var i = index; i < index + length; i++ ) {
				tokens[ i ].ngrams[ ngram ] = {
					'start': i == index,
					'end': i == index + length - 1
				};
			}
		} );
	} );

	console.log( tokens );

	return tokens;
}




