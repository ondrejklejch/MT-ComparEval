<?php

namespace BackgroundModule;

/**
 * Process implementation for watching given folder and running appropriate imports
 *
 * Can be run by php -f www/index.php Backgroung:Watcher:Watch --folder=./data
 */
class WatcherPresenter extends \Nette\Application\UI\Presenter {

	public function renderWatch( $folder, $sleep = 500000 ) {
		echo "Watcher is watching folder: $folder\n";
		
		while( TRUE ) {
			usleep( $sleep );

			foreach( $this->getUnimportedExperiments( $folder ) as $experiment ) {
				$this->runImportForExperiment( $experiment );
			}

			foreach( $this->getUnimportedTasks( $folder ) as $task ) {
				$this->runImportForTask( $task );
			}
		}

		$this->terminate();
	}

	private function getUnimportedExperiments( $folder ) {
		return \Nette\Utils\Finder::findDirectories( '*' )
			->in( $folder )
			->imported( FALSE )
			->aborted( FALSE );	
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
			->imported( FALSE )
			->aborted( FALSE );
	}

	private function runImportForExperiment( $experiment ) {
		$action = "Background:Experiments:Import";		

		$this->runCommand( $action, $experiment );
	}

	private function runImportForTask( $task ) {
		$action = "Background:Tasks:Import";

		$this->runCommand( $action, $task );
	}

	private function runCommand( $action, $folder ) {
		$scriptPath = __DIR__ . '/../../../www/index.php';
		$command = "php -f $scriptPath $action --folder=$folder | tee $folder/import.log";

		$return = 0;
		passthru( $command, $return );

		if ( $return != 0 ) {
			$folder = new \Folder( $folder );
			$folder->lock( 'unimported' );
		}
	}

}
