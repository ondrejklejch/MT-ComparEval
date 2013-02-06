<?php


class Translation {

	private $translationNode;

	
	public function __construct( $translationNode ) {
		$this->translationNode = $translationNode;
	}

	public function getText() {
		return $this->translationNode->find( 'css', '.text' )->getText();	
	}

	public function getMetric() {
		return $this->translationNode->find( 'css', '.metric' )->getText();	
	}

}
