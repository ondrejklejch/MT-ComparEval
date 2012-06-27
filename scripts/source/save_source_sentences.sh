#!/bin/sh

dir=`dirname $0`
sourceFile=$1
experimentId=$2
database=$3

mkdir /tmp/$$
 
perl -I $dir/../perl $dir/../perl/generate_sentences_sql.pl $sourceFile > /tmp/$$/sentences
split --lines 500 /tmp/$$/sentences /tmp/$$/sentences-chunks

for file in /tmp/$$/sentences-chunks*
do
	values=`sed "s/SELECT /SELECT $experimentId AS \"experiment_id\",/g;2,$ s/^/UNION ALL /;" $file`
 	echo "INSERT INTO \"source_sentences\" ( \"experiment_id\", \"position\", \"length\", \"text\") ${values};" > /tmp/$$/sql

	sqlite3 $database < /tmp/$$/sql
done

rm -rf /tmp/$$ 
