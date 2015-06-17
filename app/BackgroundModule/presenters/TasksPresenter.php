<?php

namespace BackgroundModule;

/**
 * Implementation of ImporterPresenter for running process for importing tasks
 *
 * Can be run by php -f www/index.php Background:Tasks:Import --folder=./data/experiment
 */
class TasksPresenter extends ImporterPresenter {

	public function __construct( \TasksImporter $importer ) {
		$this->importer = $importer;
	}

}


