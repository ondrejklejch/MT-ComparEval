<?php

namespace BackgroundModule;

class TasksPresenter extends ImporterPresenter {

	public function __construct( \TasksImporter $importer ) {
		$this->importer = $importer;
	}

}


