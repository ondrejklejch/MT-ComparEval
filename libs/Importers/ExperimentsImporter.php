<?php

/**
 * Importer implementation for importing experiments into MT-ComparEval
 *
 * ExperimentsImporter extracts source sentences and reference sentences from the given
 * folder and imports them into the database. It uses defaults values for names of files
 * containing source and reference.
 * ExperimentsImporter choose default default name for experiment same as name of the folder
 * that the experiment is located in. The name can be overriden in configuration.
 *
 * Configuration of experiment is read from configuration.neon file.
 */
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

	protected function deleteUnimported( $metadata ) {
		$this->experimentsModel->deleteExperiment( $metadata[ 'experiment_id' ] );
	}

	protected function showImported( $metadata ) {
		$this->experimentsModel->setVisible( $metadata[ 'experiment_id' ] );
	}
}
