<?php

namespace BackgroundModule;

class TasksPresenter extends \Nette\Application\UI\Presenter {

	private $importer;

	public function __construct( \TasksImporter $importer ) {
		$this->importer = $importer;
	}

	public function renderWatch( $folder, $sleep = 500000 ) {
		echo "Tasks watcher is watching folder: $folder\n";

		while( TRUE ) {
			usleep( $sleep );

			foreach( $this->getUnimportedTasks( $folder ) as $task ) {
				$this->importer->importFromFolder( new \Folder( $task ) );
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
}


