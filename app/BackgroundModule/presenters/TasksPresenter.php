<?php

namespace BackgroundModule;

class TasksPresenter extends \Nette\Application\UI\Presenter {

	public function renderWatch( $folder ) {
		echo "Tasks watcher is watching folder: $folder\n";
	
		$this->terminate();
	}
}
