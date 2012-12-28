<?php


class Sentence {

	private $sentenceNode;

	public function __construct( $sentenceNode ) {
		$this->sentenceNode = $sentenceNode;
	}

	public function getId() {
		return $this->sentenceNode->getAttribute( 'data-id' );
	}

	public function getSource() {
		return $this->sentenceNode->find( 'css', '.source' )->getText();
	}
	
	public function getReference() {
		return $this->sentenceNode->find( 'css', '.reference' )->getText();
	}

	public function getTranslations() {
		$translationNodes = $this->sentenceNode->findAll( 'css', '.translation' );

		return array_map( function( $node ) {
			return new Translation( $node );
		}, $translationNodes );
	}

}
