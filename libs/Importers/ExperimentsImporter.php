<?php

class ExperimentsImporter extends Importer {

	private $experimentsModel;

	public function __construct( Experiments $model ) {
		$this->experimentsModel = $model; 
	}

	protected function logImportStart( $config ) {
		$this->logger->log( "New experiment called {$config['url_key']} was found" );
	}

	protected function logImportSuccess( $config ) {
		$this->logger->log( "Experiment {$config['url_key']} uploaded successfully." );	
	}

	protected function processMetadata( $config ) {
		$data = array(
			'name' => $config['name'],
			'description' => $config['description'],
			'url_key' => $config['url_key']
		);

		return array( 'experiment_id' => $this->experimentsModel->saveExperiment( $data ) );
	}

	protected function processSentences( $config, $metadata, $sentences ) {
		$experimentId = $metadata['experiment_id'];

		$this->experimentsModel->addSentences( $experimentId, new \ZipperIterator( $sentences, TRUE ) );
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
