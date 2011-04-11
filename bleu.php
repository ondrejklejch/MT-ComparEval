<?php

define('REFERENCE_TRANSLATION_FILE', './translations/reference.txt');
define('MACHINE_TRANSLATION_FILE', './translations/1.txt');


$referenceTranslationsText = file_get_contents(REFERENCE_TRANSLATION_FILE);
$referenceTranslations = explode("\n", $referenceTranslationsText);

$machineTranslationsText = file_get_contents(MACHINE_TRANSLATION_FILE);
$machineTranslations = explode("\n", $machineTranslationsText);

$missing = array();
$redundant = array();

foreach($referenceTranslations as $key => $referenceTranslation) {
    $machineTranslation = $machineTranslations[$key];

    echo "$key:\t" . countBleu($referenceTranslation, $machineTranslation) . "\n";
    countMissingNGrams($machineTranslation, $referenceTranslation, $redundant);
    countMissingNGrams($referenceTranslation, $machineTranslation, $missing);
}

echo "\nMissing ngrams:";
print_r($missing);

echo "\nRedundant ngrams:";
print_r($redundant);



function countBleu($referenceTranslation, $machineTranslation) {
	$brevityPenalty = countBrevityPenalty($referenceTranslation, $machineTranslation);
	$geometricAverage = countGeometricAverage($referenceTranslation, $machineTranslation);	
	
	$bleu = $brevityPenalty * exp($geometricAverage);
	
	return $bleu;
}


function countBrevityPenalty($referenceTranslation, $machineTranslation) {
	$referenceTranslationLength = strlen($referenceTranslation);
	$machineTranslationLength = strlen($machineTranslation);
	
	if($machineTranslationLength <= $referenceTranslationLength) {
		$brevityPenalty = exp(1 - $referenceTranslationLength / $machineTranslationLength);
	} else {
		$brevityPenalty = 1;
	}
	
	return $brevityPenalty;
}


function countGeometricAverage($referenceTranslation, $machineTranslation) {
	$referenceTranslationsNGrams = countNGrams($referenceTranslation);
	$commonNGrams = countCommonNGrams($referenceTranslation, $machineTranslation);
	
	$geometricAverage = 0;
	for($i = 1; $i <= 4; $i++) {
		if(!isset($commonNGrams[$i])) {
			return -INF;
		}
		
		$commonNGramsCount = 0;
		foreach($commonNGrams[$i] as $count) {
			$commonNGramsCount += $count;
		}
		
		$availableNGramsCount = count($referenceTranslationsNGrams[$i]);
		$nGramPrecision = $commonNGramsCount / $availableNGramsCount;	
		$geometricAverage += 1/4 * log($nGramPrecision);	
	}

	return $geometricAverage;
}


function countCommonNGrams($referenceTranslation, $machineTranslation) {
	$referenceTranslationNGrams = countNGrams($referenceTranslation);
	$machineTranslationNGrams = countNGrams($machineTranslation);

	$commonNGrams = array();

	foreach($referenceTranslationNGrams as $length => $ngrams) {
		foreach($ngrams as $hash => $count) {
			if(!isset($machineTranslationNGrams[$length][$hash])) {
				continue;
			}
		
			$commonNGrams[$length][$hash] = min($count, $machineTranslationNGrams[$length][$hash]);	
		}
	}
	
	return $commonNGrams;
}


function countMissingNGrams($referenceTranslation, $machineTranslation, & $missingNGrams) {
	$referenceTranslationNGrams = countNGrams($referenceTranslation);
	$machineTranslationNGrams = countNGrams($machineTranslation);

	foreach($referenceTranslationNGrams as $length => $ngrams) {
		foreach($ngrams as $hash => $count) {
			if(!isset($machineTranslationNGrams[$length][$hash])) {
                $missingNGrams[$length][$hash] = $count;
			} else if($machineTranslationNGrams[$length][$hash] < $count) {
                $missingNGrams[$length][$hash] = $count - $machineTranslationNGrams[$length][$hash];
            }
		}
	}

	return $missingNGrams;
}


function countNGrams($text) {
	$tokens = explode(" ", $text);
	$ngrams = array();

	for($i = 1; $i <= 4; $i++) {
		$ngram = array(null);
	
		for($j = 0; $j < $i - 1; $j++) {
			array_push($ngram, $tokens[$j]);
		}
	
		for($j = $i - 1; $j < count($tokens); $j++) {
			array_shift($ngram);
			array_push($ngram, $tokens[$j]);
			incrementNGramCount($ngram, $ngrams);
		}
	}
	
	return $ngrams;
}


function incrementNGramCount($ngram, &$ngrams) {
	$hash = implode(' ', $ngram);
	$length = count($ngram);
	
	if(!isset($ngrams[$length][$hash])) {
		$ngrams[$length][$hash] = 0;
	}
	
	$ngrams[$length][$hash]++;
}
