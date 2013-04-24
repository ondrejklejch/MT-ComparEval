<?php


class RandomMetric {

	public function init() {
		return;
	}


	public function addSentence() {
		return $this->random();
	}


	public function getScore() {
		return $this->random();
	}


	private function random() {
		return rand( 0, 10000 ) / 10000;
	}

}
