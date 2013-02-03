<?php

namespace BackgroundModule;

class TasksPresenter extends \Nette\Application\UI\Presenter {

	public function renderWatch( $folder ) {
		echo "Tasks watcher is watching folder: $folder\n";
	
		while( true ) {
			$tasks = \Nette\Utils\Finder::findDirectories( '*/*' )->from( $folder );	
			foreach( $tasks as $task ) {
				$experimentFolder = new \SplFileInfo( dirname( $task->getPathname() ) );
				if( !file_exists( $experimentFolder->getPathname() . '/.imported' ) ) {
					continue;
				}


				echo "New task called ". $task->getBaseName() ." was found in experiment " . $experimentFolder->getBaseName() . "\n";
			}

			usleep( 500000 );
		}
		$this->terminate();
	}
}
