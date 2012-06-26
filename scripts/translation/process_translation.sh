#!/bin/sh

dir="`pwd`/`dirname $0`"
sourceFile=$1
taskId=$2
database=$3

sh $dir/save_translation_ngrams.sh $sourceFile $taskId $database
sh $dir/save_translation_sentences.sh $sourceFile $taskId $database
php -f $dir/../../www/index.php Workers:Bleu:computeBleuForTask -id=$taskId
php -f $dir/../../www/index.php Workers:Bleu:computeDiffBleuForTask -id=$taskId
