#!/bin/sh

dir="`pwd`/`dirname $0`"
sourceFile=$1
experimentId=$2
database=$3

sh $dir/save_reference_ngrams.sh $sourceFile $experimentId $database
sh $dir/save_reference_sentences.sh $sourceFile $experimentId $database
sqlite3 $database "UPDATE experiments SET state = 1 WHERE id = $experimentId"
