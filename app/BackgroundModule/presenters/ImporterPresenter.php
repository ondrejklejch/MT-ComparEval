<?php

namespace BackgroundModule;

abstract class ImporterPresenter extends \Nette\Application\UI\Presenter {

	protected $importer;

	public function renderImport( $folder ) {
		$this->importer->importFromFolder( new \Folder( new \SplFileInfo( $folder ) ) );

		$this->terminate();
	}

}
