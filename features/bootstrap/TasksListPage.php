<?php

class TasksListPage { 

	private $page;

	public function __construct( $page ) {
		$this->page = $page;
	}

	public function getTasksNames() {
		$tasksNodes = $this->page->findAll( 'css', '.task .name' );

		return array_map( function( $node ) {
			return $node->getText();
		}, $tasksNodes );
	}

}
