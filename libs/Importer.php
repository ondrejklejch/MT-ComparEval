<?php

abstract class Importer {

	protected function getSentences( \Folder $folder, $filename ) {
		$filepath = $folder->getChildrenPath( $filename );

		return new \FileSentencesIterator( $filepath );
	}

	protected function parseResources( Folder $folder, $config ) {
		$sentences = array();
		foreach( $this->getResources() as $resource ) {
			$sentences[$resource] = $this->parseResource( $folder, $resource, $config );
		}

		return $sentences;
	}

	protected abstract function getResources();

	private function parseResource( $folder, $resource, $config ) {
		echo "{$config[$resource]} will be used as a $resource sentences source in {$folder->getName()}\n";		

		$sentences =  $this->getSentences( $folder, $config[$resource] );
		echo "Starting parsing of $resource sentences located in {$config[$resource]} for {$folder->getName()}\n";
		$count = $sentences->count();

		echo "{$folder->getName()} has $count $resource sentences\n";

		return $sentences;
	}

	protected function handleInvalidSentencesResource( $name ) {
		echo "Missing translation sentences in $name\n";
		echo "Parsing of $name aborted!\n";
	}

	protected function handleNotMatchingNumberOfSentences( $name ) {
		echo "$name has bad number of translation sentences\n";
		echo "Parsing of $name aborted!\n";
	}

	protected function getConfig( Folder $folder ) {
		$configPath = $folder->getChildrenPath( 'config.neon' );
		$defaults = $this->getDefaults( $folder );
	
		return new \ResourcesConfiguration( $configPath, $defaults );
	}

	protected abstract function getDefaults( Folder $folder );


}
