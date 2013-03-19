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

var getMatchingNGrams = function( reference, translation ) {
	var matching = intersection( reference, translation );
	var matchingInReference = guessPositionsOfMatching( matching, reference );
	var matchingInTranslation = guessPositionsOfMatching( matching, translation ); 

	return { "reference": matchingInReference, "translation": matchingInTranslation }; 
}

var guessPositionsOfMatching = function( matching, all ) {
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
					tmpScore[index] = idOrDefault( tmpScore[index], 0 ) + 1;
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

var idOrDefault = function( value, defaultValue ) {
	if( typeof value == "undefined" ) {
		return defaultValue;
	}

	return value;
}

var getNotMatchingNGrams = function( reference, translation ) {
	return diff( reference, translation );
}

var getImproving = function( matchingA, matchingB ) {
	return diff( matchingA, matchingB );
}

var getWorsening = function( notMatchingA, notMatchingB ) {
	return diff( notMatchingA, notMatchingB );
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


var reference = getNGrams( "a b c d e f g bla bla bla bla bla a b c d e f g h" );
var translation = getNGrams( "a b c d e f g h" );


console.log( getMatchingNGrams( reference, translation ).reference );
