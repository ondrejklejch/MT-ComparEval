<?php

namespace BackgroundModule;

class TasksPresenter extends \Nette\Application\UI\Presenter {

	public function renderWatch( $folder, $sleep = 500000 ) {
		echo "Tasks watcher is watching folder: $folder\n";

		while( TRUE ) {
			usleep( $sleep );

			foreach( $this->getUnimportedTasks( $folder ) as $task ) {
				$taskFolder = new \Folder( $task );
				$taskName = $taskFolder->getName();
				$experimentName = $taskFolder->getParent()->getName();
				$config = $this->getConfig( $taskFolder );

				echo "New task called $taskName was found in experiment $experimentName\n";
				if( !$taskFolder->fileExists( $config['translation'] ) ) {
					echo "Missing translation sentences in $taskName\n";
					echo "Parsing of $taskName aborted!\n";
	
					continue;
				} else {
					$sentences['translation'] = $this->parseResource( $taskName, $taskFolder, 'translation', $config ); 
				}	

				$experiment = $this->getService( 'experiments' )->getExperimentByName( $experimentName );
				$sentences['experiment'] = $this->getService( 'experiments' )->getSentences( $experiment['id'] );

				if( iterator_count( $sentences['experiment'] ) != iterator_count( $sentences['translation'] ) ) {
					echo "$taskName has bad number of translation sentences\n";
					echo "Parsing of $taskName aborted!\n";
					continue;
				} 
		
				$data = array(
					'name' => $config['name'],
					'description' => $config['description'],
					'url_key' => $taskName,
					'experiments_id' => $experiment['id']
				);

				$this->getService( 'tasks' )->saveTask( $data );
				echo "Task $taskName uploaded successfully\n";
				$taskFolder->lock();
			}
		}

		$this->terminate();
	}

	private function getUnimportedTasks( $folder ) {
		$importedExperiments = \Nette\Utils\Finder::findDirectories( '*' )
			->in( $folder )
			->imported( TRUE )
			->toArray();

		if( count( $importedExperiments ) == 0 ) {
			return array();
		}

		return \Nette\Utils\Finder::findDirectories( '*' )
			->in( $importedExperiments )
			->imported( FALSE );
	}

	private function parseResource( $taskName, $taskFolder, $resource, $config ) {
		echo "{$config[$resource]} will be used as a $resource sentences source in $taskName\n";		

		$sentences =  $this->getSentences( $taskFolder, $config[$resource] );
		echo "Starting parsing of $resource sentences located in {$config[$resource]} for $taskName\n";
		$count = $sentences->count();

		echo "$taskName has $count $resource sentences\n";

		return $sentences;
	}

	private function getSentences( \Folder $taskFolder, $filename ) {
		$filepath = $taskFolder->getChildrenPath( $filename );

		return new \FileSentencesIterator( $filepath );
	}

	private function getConfig( \Folder $taskFolder ) {
		$defaults = array(
			'name' => $taskFolder->getName(),
			'description' => '',
			'translation' => 'translation.txt'

		);
		$configPath = $taskFolder->getChildrenPath( 'config.neon' );
	
		return new \ResourcesConfiguration( $configPath, $defaults );
	}

}


