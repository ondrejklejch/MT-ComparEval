<?php

class ExperimentsImporter {

	private $experimentsModel;

	public function __construct( Experiments $model ) {
		$this->experimentsModel = $model; 
	}

	public function importFromFolder( Folder $experimentFolder ) {
		$experimentName = $experimentFolder->getName();
		$config = $this->getConfig( $experimentFolder );
		echo "New experiment called $experimentName was found\n";

		try {
			$sentences = array();
			foreach( array( 'source', 'reference' ) as $resource ) {
				$sentences[$resource] = $this->parseResource( $experimentName, $experimentFolder, $resource, $config );
			}

			$experimentId = $this->experimentsModel->saveExperiment( $experimentName );
			$this->experimentsModel->addSentences( $experimentId, new \ZipperIterator( $sentences, TRUE ) );

			echo "Experiment $experimentName uploaded successfully.\n";	
			$experimentFolder->lock();
		} catch( \InvalidSentencesResourceException $exception ) {
			$this->handleInvalidSentencesResource( $experimentName, $resource );
		} catch( \IteratorsLengthsMismatchException $exception ) {
			$this->handleNotMatchingNumberOfSentences( $experimentName );
		}
	}

	private function parseResource( $experimentName, $experimentFolder, $resource, $config ) {
		echo "{$config[$resource]} will be used as a $resource sentences source in $experimentName\n";		

		$sentences =  $this->getSentences( $experimentFolder, $config[$resource] );
		echo "Starting parsing of $resource sentences located in {$config[$resource]} for $experimentName\n";
		$count = $sentences->count();

		echo "$experimentName has $count $resource sentences\n";

		return $sentences;
	}
	
	private function getSentences( \Folder $experimentFolder, $filename ) {
		$filepath = $experimentFolder->getChildrenPath( $filename );

		return new \FileSentencesIterator( $filepath );
	}

	private function handleInvalidSentencesResource( $experimentName, $resource ) {
		echo "Missing $resource sentences in $experimentName\n";
		echo "Parsing of $experimentName aborted!";
	}

	private function handleNotMatchingNumberOfSentences( $experimentName ) {
		echo "$experimentName has bad number of source/reference sentences\n";
		echo "Parsing of $experimentName aborted!";
	}

	private function getConfig( \Folder $experimentFolder ) {
		$configPath = $experimentFolder->getChildrenPath( 'config.neon' );
		$defaults = array(
			'source' => 'source.txt',
			'reference' => 'reference.txt'
		);

		return new ResourcesConfiguration( $configPath, $defaults );
	}

}
