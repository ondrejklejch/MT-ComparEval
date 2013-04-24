<?php

class TasksImporter extends Importer {

	private $experimentsModel;
	private $tasksModel;
	private $sampler;
	private $preprocessor;
	private $metrics;

	public function __construct( Experiments $experimentsModel, Tasks $tasksModel, BootstrapSampler $sampler, Preprocessor $preprocessor, $metrics ) {
		$this->experimentsModel = $experimentsModel;
		$this->tasksModel = $tasksModel;
		$this->sampler = $sampler;
		$this->preprocessor = $preprocessor;
		$this->metrics = $metrics;
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

	protected function processSentences( $config, $metadata, $rawSentences ) {
		$metrics = array();

		foreach( array( true, false ) as $isCaseSensitive ) {
			$preprocessor = $this->preprocessor;
			$sentences = new MapIterator( 
				new \ZipperIterator( $rawSentences, TRUE ),
				function( $sentence ) use ( $preprocessor, $isCaseSensitive ) {
					$sentence[ 'is_case_sensitive' ] = $isCaseSensitive;

					return $preprocessor->preprocess( $sentence );
				}
			);

			foreach( $this->metrics as $name => $metric ) {
				$metric->init(); 
				$name = $this->getMetricName( $name, $isCaseSensitive );
				$metrics[ $name ] = array();
			}

			foreach( $sentences as $sentence ) {
				foreach( $this->metrics as $name => $metric ) {
					$name = $this->getMetricName( $name, $isCaseSensitive );
					$metrics[ $name ][] = $metric->addSentence( $sentence['experiment']['reference'], $sentence['translation'], $sentence['meta'] );
				}
			}

			foreach( $this->metrics as $name => $metric ) {
				$name = $this->getMetricName( $name, $isCaseSensitive );
				$samples = $this->sampler->generateSamples( $metric, iterator_to_array( $sentences ) );

				$this->tasksModel->addMetric( $metadata['task_id'], $name, $metric->getScore() ); 
				$this->tasksModel->addSamples( $metadata['task_id'], $name, $samples );
			}
		}

		$this->tasksModel->addSentences( $metadata['task_id'], $sentences, $metrics );
	}

	private function getMetricName( $name, $isCaseSensitive ) {
		if( $isCaseSensitive ) {
			return $name;
		} else {
			return $name . '-cis';
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
