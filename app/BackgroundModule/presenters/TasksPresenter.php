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
				echo "{$config['translation']} will be used as a translation sentences source in $taskName\n";		
				if( !$taskFolder->fileExists( $config['translation'] ) ) {
					echo "Missing translation sentences in $taskName\n";
				} else {
					echo "Starting parsing of translation sentences located in {$config['translation']} for $taskName";
				}	
			}
		}

		$this->terminate();
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


