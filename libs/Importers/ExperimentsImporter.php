<?php

class ExperimentsImporter extends Importer {

	private $experimentsModel;

	public function __construct( Experiments $model ) {
		$this->experimentsModel = $model; 
	}

	public function importFromFolder( Folder $experimentFolder ) {
		$experimentName = $experimentFolder->getName();
		$config = $this->getConfig( $experimentFolder );
		$this->logger->log( "New experiment called $experimentName was found" );

		try {
			$sentences = $this->parseResources( $experimentFolder, $config );

			$data = array(
				'name' => $config['name'],
				'description' => $config['description'],
				'url_key' => $experimentName
			);

			$experimentId = $this->experimentsModel->saveExperiment( $data );
			$this->experimentsModel->addSentences( $experimentId, new \ZipperIterator( $sentences, TRUE ) );

			$this->logger->log( "Experiment $experimentName uploaded successfully." );	
			$experimentFolder->lock();
		} catch( \IteratorsLengthsMismatchException $exception ) {
			$this->handleNotMatchingNumberOfSentences( $experimentName );
		} catch( \ImporterException $exception ) {
			$this->logger->log( "{$exception->getMessage()}" );
			$this->logger->log( "Parsing of {$config['url_key']} aborted!" );
		}
	}

	protected function getResources() {
		return array( 'source', 'reference' );
	}

	protected function getDefaults( Folder $experimentFolder ) {
		return array(
			'name' => $experimentFolder->getName(),
			'url_key' => $experimentFolder->getName(),
			'description' => '',
			'source' => 'source.txt',
			'reference' => 'reference.txt'
		);
	}

}
