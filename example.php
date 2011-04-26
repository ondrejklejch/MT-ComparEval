<?php

require_once dirname( __FILE__ ) . '/Bleu.php';

define('REFERENCE_TRANSLATION_FILE', dirname( __FILE__ ) . '/translations/reference.txt');
define('MACHINE_TRANSLATION_FILE', dirname( __FILE__ ) . '/translations/1.txt');

$referenceTranslationsText = trim(file_get_contents(REFERENCE_TRANSLATION_FILE));
$referenceTranslations = explode("\n", $referenceTranslationsText);

$machineTranslationsText = trim(file_get_contents(MACHINE_TRANSLATION_FILE));
$machineTranslations = explode("\n", $machineTranslationsText);

$bleu = new Bleu();
foreach($referenceTranslations as $key => $referenceTranslation) {
    $bleu->addSentence(
        $referenceTranslation, 
        $machineTranslations[$key]
    );
}

echo "Bleu: " . $bleu->getBleu();

echo "\nMissing ngrams:";
print_r( $bleu->getMissingNGrams() );

echo "\nRedundant ngrams:";
print_r( $bleu->getRedundantNGrams() );