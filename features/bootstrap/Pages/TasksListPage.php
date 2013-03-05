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

	public function getTask( $taskName ) {
		$xpath = "//tr[@class='task' and @data-id='$taskName']";
		$node = $this->page->find( 'xpath', $xpath );

		return new TaskListEntry( $node );
	}

	public function getValue( $taskName, $key ) {
		$xpath = "//tr[@class='task' and @data-id='$taskName']";
		$taskNode = $this->page->find( 'xpath', $xpath );

		return $taskNode->find( 'css', ".$key" )->getText();
	}
}
