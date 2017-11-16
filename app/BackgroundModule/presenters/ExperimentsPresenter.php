<?php

namespace BackgroundModule;

/**
 * Implementation of ImporterPresenter for running process for importing experiments
 *
 * Can be run by php -f www/index.php Background:Experiments:Import --folder=./data/experiment
 */
class ExperimentsPresenter extends ImporterPresenter {

	public function __construct( \ExperimentsImporter $importer ) {
		$this->importer = $importer;
	}

}
