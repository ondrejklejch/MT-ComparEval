<?php

class TasksListPage { 

	private $page;

	public function __construct( $page ) {
		$this->page = $page;
	}

	public function getTasksNames() {
		$tasksNodes = $this->page->findAll( 'css', '.task' );

		return array_map( function( $node ) {
			return $node->getAttribute( 'data-id' );
		}, $tasksNodes );
	}

	public function getValue( $experimentName, $key ) {
		$xpath = "//tr[@class='task' and @data-id='$experimentName']";
		$experimentNode = $this->page->find( 'xpath', $xpath );

		return $experimentNode->find( 'css', ".$key" )->getText();
	}
}
