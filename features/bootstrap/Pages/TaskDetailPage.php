<?php

class TaskDetailPage {

	private $page;

	
	public function __construct( $page ) {
		$this->page = $page;
	}

	public function chooseMetric( $metric ) {
		$this->page->find( 'css', '#metrics' )->selectOption( $metric, FALSE );
	}

	public function sortSentencesById() {
		$this->page->find( 'css', '#sort-cancel' )->click();
	}

	public function sortSentencesByMetric( $order ) {
		$this->page->find( 'css', '#sort-' . $order )->click();
	}

	public function scrollDown() {
		$this->page->getSession()->executeScript( 'window.scrollBy( 0, document.height );' );
	}

	public function getSentences() {
		$sentenceNodes = $this->page->findAll( 'css', '.sentence' );

		return array_map( function( $node ) {
			return new Sentence( $node );
		}, $sentenceNodes );
	}

	public function getActiveMetric() {
		return $this->page->find( 'css', '#metrics' )->getValue();
	}

}
