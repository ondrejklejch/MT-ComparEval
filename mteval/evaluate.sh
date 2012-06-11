#!/bin/bash

files=`ls -1 data/ | grep -Po "[A-Z]*" | uniq`

echo "translator\tMTEval 11b\tMT-ComparEval"
for system in $files
do
	MTEval=`./mteval-v11b.pl -cb -r data/sample_reference.sgm -s data/sample_source.sgm -t data/sample_$system.sgm | grep "BLEU score" | grep -Po "[0-9].[0-9]{4}"`
	MTComparEval=`perl -I ../lib/ bleu.pl data/sample_reference data/sample_$system`

	echo "$system\t\t$MTEval\t\t$MTComparEval"
done
