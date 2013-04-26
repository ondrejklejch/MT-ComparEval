<?php

namespace BackgroundModule;

class ExperimentsPresenter extends \Nette\Application\UI\Presenter {

	private $importer;

	public function __construct( \ExperimentsImporter $importer ) {
		$this->importer = $importer;
	}

	public function renderWatch( $folder, $sleep = 500000 ) {
		echo "Experiments watcher is watching folder: $folder\n";
		
		while( TRUE ) {
			usleep( $sleep );

			foreach( $this->getUnimportedExperiments( $folder ) as $experiment ) {
				$this->importer->importFromFolder( new \Folder( $experiment ) );
			}
		}

		$this->terminate();
	}

	private function getUnimportedExperiments( $folder ) {
		return \Nette\Utils\Finder::findDirectories( '*' )
			->in( $folder )
			->imported( FALSE );	
	}

}
