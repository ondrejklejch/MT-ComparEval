<?php

class ExperimentsListPage {

	private $page;

	public function __construct( $page ) {
		$this->page = $page;
	}

	public function getExperimentsNames() {
		$experimentsNodes = $this->page->findAll( 'css', '.experiment .name' );

		return array_map( function( $node ) {
			return $node->getText();
		}, $experimentsNodes );
	}

}
