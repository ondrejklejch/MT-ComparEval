<?php


class Bleu {

    private $currentReferenceTranslationNGrams;

    private $currentMachineTranslationNGrams;

    private $referenceTranslationsNGrams;

    private $referenceTranslationLength;

    private $machineTranslationLength;
    
    private $commonNGrams;
    
    private $missingNGrams;

    private $redundantNGrams;
    

    public function   __construct() {
        $this->referenceTranslationLength = 0;
        $this->machineTranslationLength = 0;
        $this->referenceTranslationsNGrams = array();
        $this->commonNGrams = array();
        $this->missingNGrams = array();
        $this->redundantNGrams = array();
    }


    public function addSentence( $referenceTranslation, $machineTranslation ) {
        $this->referenceTranslationLength += strlen( $referenceTranslation );
        $this->currentReferenceTranslationNGrams = $this->countNGrams(
            $referenceTranslation
        );

        $this->machineTranslationLength += strlen( $machineTranslation );
        $this->currentMachineTranslationNGrams = $this->countNGrams(
            $machineTranslation
        );

        
        $this->addReferenceTranslationNGrams();
        $this->addCommonNGrams();
        $this->addRedundantNgrams();
        $this->addMissingNGrams();
    }


    public function getBleu() {
        $brevityPenalty = $this->countBrevityPenalty();
        $geometricAverage = $this->countGeometricAverage();

        return $brevityPenalty * exp($geometricAverage);
    }


    public function getCommonNGrams() {
        return $this->commonNGrams;
    }

    
    public function getMissingNGrams() {
        return $this->missingNGrams;
    }


    public function getRedundantNGrams() {
        return $this->redundantNGrams;
    }


    private function countBrevityPenalty() {
        if( $this->machineTranslationLength <= $this->referenceTranslationLength ) {
            $brevityPenalty = exp( 
                1 - $this->referenceTranslationLength / $this->machineTranslationLength
            );
        } else {
            $brevityPenalty = 1;
        }

        return $brevityPenalty;
    }


    private function countGeometricAverage() {
        $geometricAverage = 0;
        for($i = 1; $i <= 4; $i++) {
            if( ! isset( $this->commonNGrams[$i] ) ) {
                return -INF;
            }

            $commonNGramsCount = array_sum( $this->commonNGrams[$i] );
            $availableNGramsCount = array_sum( $this->referenceTranslationsNGrams[$i] );

            $nGramPrecision = $commonNGramsCount / $availableNGramsCount;
            $geometricAverage += 1/4 * log($nGramPrecision);
        }

        return $geometricAverage;
    }

    
    private function countNGrams( $text ) {
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
                $this->incrementNGramCount($ngram, $ngrams);
            }
        }

        return $ngrams;
    }


    private function incrementNGramCount($ngram, &$ngrams) {
        $hash = implode(' ', $ngram);
        $length = count($ngram);

        if(!isset($ngrams[$length][$hash])) {
            $ngrams[$length][$hash] = 0;
        }

        $ngrams[$length][$hash]++;
    }


    private function addReferenceTranslationNGrams() {
        $this->referenceTranslationsNGrams = $this->mergeNGramsArrays(
            $this->currentReferenceTranslationNGrams,
            $this->referenceTranslationsNGrams
        );
    }


    private function addCommonNGrams() {
        $commonNGrams = $this->findCommonNGrams();

        $this->commonNGrams = $this->mergeNGramsArrays(
            $commonNGrams,
            $this->commonNGrams
        );
    }

    
    private function findCommonNGrams() {
        $commonNGrams = array();
        foreach( $this->currentReferenceTranslationNGrams as $length => $ngrams ) {
            foreach( $ngrams as $hash => $count ) {
                if( ! isset( $this->currentMachineTranslationNGrams[$length][$hash]) ) {
                    continue;
                }

                $commonNGrams[$length][$hash] = min(
                    $count,
                    $this->currentMachineTranslationNGrams[$length][$hash]
                );
            }
        }

        return $commonNGrams;
    }


    private function addMissingNGrams() {
        $missingNGrams = $this->findMissingNGrams();

        $this->missingNGrams = $this->mergeNGramsArrays(
            $missingNGrams,
            $this->missingNGrams
        );
    }
    

    private function findMissingNGrams() {
        $missingNGrams = array();
        foreach( $this->currentReferenceTranslationNGrams as $length => $ngrams ) {
            foreach( $ngrams as $hash => $count ) {
                if( ! isset( $this->currentMachineTranslationNGrams[$length][$hash] ) ) {
                    $missingNGrams[$length][$hash] = $count;
                } else if( $this->currentMachineTranslationNGrams[$length][$hash] < $count ) {
                    $missingNGrams[$length][$hash] = $count - $this->currentMachineTranslationNGrams[$length][$hash];
                }
            }
        }

        return $missingNGrams;
    }


    private function addRedundantNgrams() {
        $redundantNGrams = $this->findRedundantNGrams();

        $this->redundantNGrams = $this->mergeNGramsArrays(
            $redundantNGrams,
            $this->redundantNGrams
        );
    }
    

    private function findRedundantNGrams() {
        $this->swap(
            $this->currentMachineTranslationNGrams,
            $this->currentReferenceTranslationNGrams
        );

        $redundantNGrams = $this->findMissingNGrams();

        $this->swap(
            $this->currentMachineTranslationNGrams,
            $this->currentReferenceTranslationNGrams
        );

        return $redundantNGrams;
    }

    private function swap( &$a, &$b ) {
        $c = $a;
        $a = $b;
        $b = $c;
    }

    
    private function mergeNGramsArrays( $a, $b ) {
        $mergedArray = array();
        foreach( $a as $length => $ngrams ) {
            foreach( $ngrams as $hash => $count ) {
                if( isset( $b[$length][$hash] ) ) {
                    $mergedArray[$length][$hash] = $count + $b[$length][$hash];
                } else {
                    $mergedArray[$length][$hash] = $count;
                }
            }
        }

        foreach( $b as $length => $ngrams ) {
            foreach( $ngrams as $hash => $count ) {
                if( ! isset( $a[$length][$hash] ) ) {
                    $mergedArray[$length][$hash] = $count;
                }
            }
        }

        return $mergedArray;        
    }

}

