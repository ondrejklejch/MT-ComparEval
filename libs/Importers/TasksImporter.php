<?php

class TasksImporter extends Importer {

	private $experimentsModel;
	private $tasksModel;

	public function __construct( Experiments $experimentsModel, Tasks $tasksModel ) {
		$this->experimentsModel = $experimentsModel;
		$this->tasksModel = $tasksModel;
	}

	public function importFromFolder( Folder $taskFolder ) {
		$config = $this->getConfig( $taskFolder );

		echo "New task called {$config['url_key']} was found in experiment {$config['experiment']['url_key']}\n";
		try {
			$sentences = $this->parseResources( $taskFolder, $config );
			$iterator = new \ZipperIterator( $sentences, TRUE );

			$data = array(
				'name' => $config['name'],
				'description' => $config['description'],
				'url_key' => $config['url_key'],
				'experiments_id' => $config['experiment']['id'],
			);
			$taskId = $this->tasksModel->saveTask( $data );
			$this->tasksModel->addSentences( $taskId, $iterator ); 

			echo "Task {$config['url_key']} uploaded successfully\n";

			$taskFolder->lock();
		} catch( \IteratorsLengthsMismatchException $exception ) {
			$this->handleNotMatchingNumberOfSentences( $config['url_key'] );
		} catch( \ImporterException $exception ) {
			echo "{$exception->getMessage()}\n";
			echo "Parsing of {$config['url_key']} aborted!\n";
		}
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
