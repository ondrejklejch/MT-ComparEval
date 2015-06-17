<?php

/** 
 * Importer implementation for importing tasks into MT-ComparEval
 *
 * TasksImporter loads translations from specified file (either by configuration or default value).
 * For all loaded sentences it computes all metrics in 'case-sensitive' and 'case-insensitive' mode.
 * It also computes metrics for whole documents. Then it computes significance intervals using
 * Bootstrap Resampling and then it searches for top improving and worsening n-grams.
 *
 * All these functionalities are provided to TasksImporter by DI via __construct.
 */
class TasksImporter extends Importer {

	private $experimentsModel;
	private $ngramsModel;
	private $tasksModel;
	private $sampler;
	private $preprocessor;
	private $metrics;

	public function __construct( Experiments $experimentsModel, Tasks $tasksModel, NGrams $ngramsModel, BootstrapSampler $sampler, Preprocessor $preprocessor, $metrics ) {
		$this->experimentsModel = $experimentsModel;
		$this->ngramsModel = $ngramsModel;
		$this->tasksModel = $tasksModel;
		$this->sampler = $sampler;
		$this->preprocessor = $preprocessor;
		$this->metrics = $metrics;
	}

	protected function logImportStart( $config ) {
		$this->logger->log( "Importing task: {$config['experiment']['url_key']}:{$config['url_key']}" );
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

		foreach( array( FALSE, TRUE ) as $isCaseSensitive ) {
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

				$this->tasksModel->addMetric( $metadata['task_id'], $name, $metric->getScore() ); 
			}

			foreach( $this->metrics as $name => $metric ) {
				$name = $this->getMetricName( $name, $isCaseSensitive );

				$this->logger->log( "Generating $name samples for {$config['url_key']}." ); 
				$samples = $this->sampler->generateSamples( $metric, iterator_to_array( $sentences ) );
				$this->tasksModel->addSamples( $metadata['task_id'], $name, $samples );
				$this->logger->log( "Samples generated." );
			}
		}

		$this->tasksModel->addSentences( $metadata['task_id'], $sentences, $metrics );

		if( $config[ 'precompute_ngrams' ] ) {
			$this->logger->log( "Precomputing n-grams for {$config['url_key']}." );
			$this->ngramsModel->precomputeNgrams( $config['experiment']['id'], $metadata['task_id'] ); 
			$this->logger->log( "N-grams precomputation done." );
		}
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
			'translation' => 'translation.txt',
			'precompute_ngrams' => true
		);
	}

	protected function deleteUnimported( $metadata ) {
		$this->tasksModel->deleteTask( $metadata[ 'task_id' ] );
	}

	protected function showImported( $metadata ) {
		$this->tasksModel->setVisible( $metadata[ 'task_id' ] );
	}

}
