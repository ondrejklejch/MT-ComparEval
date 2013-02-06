<?php

class SentencesListPage {

	private $page;

	public function __construct( $page ) {
		$this->page = $page;
	}

	public function getSentences() {
		$sentenceNodes = $this->page->findAll( 'css', '.sentence' );

		return array_map( function( $node ) {
			return new Sentence( $node );
		}, $sentenceNodes );
	}


}
