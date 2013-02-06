<?php

class ExperimentsImporter {

	private $experimentsModel;
	private $logger;

	public function __construct( Experiments $model, Logger $logger ) {
		$this->experimentsModel = $model; 
		$this->logger = $logger;
	}

	public function setLogger( $logger ) {
		$this->logger = $logger;
	}

	public function importFromFolder( Folder $experimentFolder ) {
		$experimentName = $experimentFolder->getName();
		$config = $this->getConfig( $experimentFolder );
		$this->logger->log( "New experiment called $experimentName was found" );

		try {
			$sentences = array();
			foreach( array( 'source', 'reference' ) as $resource ) {
				$sentences[$resource] = $this->parseResource( $experimentName, $experimentFolder, $resource, $config );
			}

			$data = array(
				'name' => $config['name'],
				'description' => $config['description'],
				'url_key' => $experimentName
			);

			$experimentId = $this->experimentsModel->saveExperiment( $data );
			$this->experimentsModel->addSentences( $experimentId, new \ZipperIterator( $sentences, TRUE ) );

			$this->logger->log( "Experiment $experimentName uploaded successfully." );	
			$experimentFolder->lock();
		} catch( \InvalidSentencesResourceException $exception ) {
			$this->handleInvalidSentencesResource( $experimentName, $resource );
		} catch( \IteratorsLengthsMismatchException $exception ) {
			$this->handleNotMatchingNumberOfSentences( $experimentName );
		}
	}

	private function parseResource( $experimentName, $experimentFolder, $resource, $config ) {
		$this->logger->log( "{$config[$resource]} will be used as a $resource sentences source in $experimentName" );

		$sentences =  $this->getSentences( $experimentFolder, $config[$resource] );
		$this->logger->log( "Starting parsing of $resource sentences located in {$config[$resource]} for $experimentName" );
		$count = $sentences->count();

		$this->logger->log( "$experimentName has $count $resource sentences" );

		return $sentences;
	}
	
	private function getSentences( \Folder $experimentFolder, $filename ) {
		$filepath = $experimentFolder->getChildrenPath( $filename );

		return new \FileSentencesIterator( $filepath );
	}

	private function handleInvalidSentencesResource( $experimentName, $resource ) {
		$this->logger->log( "Missing $resource sentences in $experimentName" );
		$this->logger->log( "Parsing of $experimentName aborted!" );
	}

	private function handleNotMatchingNumberOfSentences( $experimentName ) {
		$this->logger->log( "$experimentName has bad number of source/reference sentences" );
		$this->logger->log( "Parsing of $experimentName aborted!" );
	}

	private function getConfig( \Folder $experimentFolder ) {
		$configPath = $experimentFolder->getChildrenPath( 'config.neon' );
		$defaults = array(
			'name' => $experimentFolder->getName(),
			'description' => '',
			'source' => 'source.txt',
			'reference' => 'reference.txt'
		);

		return new ResourcesConfiguration( $configPath, $defaults );
	}

}
