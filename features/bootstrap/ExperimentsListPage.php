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


	public function clickOnButton( $experimentName, $button ) {
		$xpath = "//tr[@class='experiment' and td[@class='name']/text()='$experimentName']/td[@class='$button']/a"; 

		$button = $this->page->find( 'xpath', $xpath )->click();
	}

}
