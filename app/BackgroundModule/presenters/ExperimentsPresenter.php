<?php

namespace BackgroundModule;

class ExperimentsPresenter extends ImporterPresenter {

	public function __construct( \ExperimentsImporter $importer ) {
		$this->importer = $importer;
	}

}
