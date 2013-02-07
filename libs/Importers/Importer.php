<?php

abstract class Importer {

	protected $logger;

	public function __construct() {
		$this->logger = new EmptyLogger();
	}

	public function setLogger( $logger ) {
		$this->logger = $logger;
	}

	public function importFromFolder( Folder $folder ) {
		$config = $this->getConfig( $folder );

		$this->logImportStart( $config );
		try {
			$sentences = $this->parseResources( $folder, $config );
			$metadata = $this->processMetadata( $config );
			$this->processSentences( $config, $metadata, $sentences );

			$this->logImportSuccess( $config );
			$folder->lock();
		} catch( \IteratorsLengthsMismatchException $exception ) {
			$this->handleNotMatchingNumberOfSentences( $config['url_key'] );
		} catch( \ImporterException $exception ) {
			$this->logImportAbortion( $config, $exception );
		}
	}

	protected abstract function logImportStart( $config );

	protected abstract function logImportSuccess( $config );

	protected function logImportAbortion( $config, ImporterException $exception ) {
		$this->logger->log( "{$exception->getMessage()}" );
		$this->logger->log( "Parsing of {$config['url_key']} aborted!" );
	}

	protected abstract function processMetadata( $config );

	protected abstract function processSentences( $config, $metadata, $sentences );

	protected abstract function getResources();

	protected abstract function getDefaults( Folder $folder );

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

	private function parseResource( $folder, $resource, $config ) {
		try {
			$this->logger->log( "{$config[$resource]} will be used as a $resource sentences source in {$folder->getName()}" );		

			$sentences =  $this->getSentences( $folder, $config[$resource] );
			$this->logger->log( "Starting parsing of $resource sentences located in {$config[$resource]} for {$folder->getName()}" );
			$count = $sentences->count();

			$this->logger->log( "{$folder->getName()} has $count $resource sentences" );

			return $sentences;
		} catch( InvalidSentencesResourceException $exception ) {
			throw new ImporterException( "Missing {$resource} sentences in {$config['url_key']}" );
		}
	}

	protected function handleNotMatchingNumberOfSentences( $name ) {
		$this->logger->log( "$name has bad number of sentences" );
		$this->logger->log( "Parsing of $name aborted!" );
	}

	protected function getConfig( Folder $folder ) {
		$configPath = $folder->getChildrenPath( 'config.neon' );
		$defaults = $this->getDefaults( $folder );
	
		return new \ResourcesConfiguration( $configPath, $defaults );
	}

}
