<?php

namespace BackgroundModule;

class TasksPresenter extends \Nette\Application\UI\Presenter {

	public function renderWatch( $folder, $sleep = 500000 ) {
		echo "Tasks watcher is watching folder: $folder\n";

		while( TRUE ) {
			usleep( $sleep );

			$importedExperiments = \Nette\Utils\Finder::findDirectories( '*' )
				->in( $folder )
				->imported( TRUE )
				->toArray();

			if( count( $importedExperiments ) == 0 ) {
				continue;
			}

			$unimportedTasks = \Nette\Utils\Finder::findDirectories( '*' )
				->in( $importedExperiments )
				->imported( FALSE );

			foreach( $unimportedTasks as $task ) {
				$taskFolder = new \Folder( $task );
				$taskFolder->lock();

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
				} 
			}
		}

		$this->terminate();
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
		$config = array();
		if( $taskFolder->fileExists( 'config.neon' ) ) {
			$path = $taskFolder->getChildrenPath( 'config.neon' );

			$config = (array) \Nette\Utils\Neon::decode( file_get_contents( $path ) );
		}

		$config['translation'] = $this->getFromArrayWithDefault( $config, 'translation', 'translation.txt' ); 

		return $config;
	}

	private function getFromArrayWithDefault( $array, $key, $default ) {
		if( isset( $array[ $key ] ) ) {
			return $array[ $key ];
		} else {
			return $default;
		}
	}

}


