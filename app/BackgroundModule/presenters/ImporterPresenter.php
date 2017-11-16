<?php

namespace BackgroundModule;

/**
 * Template method implementation for creating processes for various importers
 *
 * Can be run by php -f www/index.php Background:Name_Of_Child:Import --folder=./some/path
 */
abstract class ImporterPresenter extends \Nette\Application\UI\Presenter {

	protected $importer;

	public function renderImport( $folder ) {
		$this->importer->importFromFolder( new \Folder( new \SplFileInfo( $folder ) ) );

		$this->terminate();
	}

}
