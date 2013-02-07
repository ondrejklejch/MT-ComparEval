<?php

class TasksImporter extends Importer {

	private $experimentsModel;
	private $tasksModel;

	public function __construct( Experiments $experimentsModel, Tasks $tasksModel ) {
		$this->experimentsModel = $experimentsModel;
		$this->tasksModel = $tasksModel;
	}

	protected function logImportStart( $config ) {
		$this->logger->log( "New task called {$config['url_key']} was found in experiment {$config['experiment']['url_key']}" );
	}

	protected function logImportSuccess( $config ) {
		$this->logger->log( "Task {$config['url_key']} uploaded successfully" );
	}

	protected function processMetadata( $config ) {
		$data = array(
			'name' => $config['name'],
			'description' => $config['description'],
			'url_key' => $config['url_key'],
			'experiments_id' => $config['experiment']['id'],
		);

		return array( 'task_id' => $this->tasksModel->saveTask( $data ) );
	}

	protected function processSentences( $config, $metadata, $sentences ) {
		$iterator = new \ZipperIterator( $sentences, TRUE );
		$this->tasksModel->addSentences( $metadata['task_id'], $iterator );
	}

	protected function parseResources( Folder $folder, $config ) {
		$sentences = parent::parseResources( $folder, $config );
		$sentences['experiment'] = $this->experimentsModel->getSentences( $config['experiment']['id'] );

		return $sentences;
	}

	protected function getResources() {
		return array( 'translation' );
	}

	protected function getDefaults( Folder $folder ) {
		return array(
			'name' => $folder->getName(),
			'url_key' => $folder->getName(),
			'experiment' => $this->experimentsModel->getExperimentByName( $folder->getParent()->getName() ),
			'description' => '',
			'translation' => 'translation.txt'
		);
	}

}
