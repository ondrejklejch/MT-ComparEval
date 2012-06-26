#!/bin/sh

dir="`pwd`/`dirname $0`"
sourceFile=$1
experimentId=$2
database=$3

sh $dir/save_source_sentences.sh $sourceFile $experimentId $database
