<?php

class ExperimentsListPage {

	private $page;

	public function __construct( $page ) {
		$this->page = $page;
	}

	public function getExperimentsNames() {
		$experimentsNodes = $this->page->findAll( 'xpath', "//tr[@class='experiment']" );

		return array_map( function( $node ) {
			return $node->getAttribute( 'data-id' );
		}, $experimentsNodes );
	}

	public function getValue( $experimentName, $key ) {
		$xpath = "//tr[@class='experiment' and @data-id='$experimentName']";
		$experimentNode = $this->page->find( 'xpath', $xpath );

		return $experimentNode->find( 'css', ".$key" )->getText();
	}


	public function clickOnButton( $experimentName, $button ) {
		$xpath = "//tr[@class='experiment' and td[@class='name']/text()='$experimentName']/td[@class='$button']/a"; 

		$button = $this->page->find( 'xpath', $xpath )->click();
	}

}
