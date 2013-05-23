<?php


interface IMetric {

	public function init();
	public function addSentence( $reference, $translation, $meta );
	public function getScore();

}
