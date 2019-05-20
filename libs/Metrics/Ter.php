<?php

/**
 * TER external metric implementation
 */
class Ter implements IMetric {

	private $referenceText = array();
	private $translationText = array();

	private $cache = null;
	private $externalCommand = "external/tercpp.0.6.2";

	public function __construct(Nette\Caching\Cache $cache) {
		$this->cache = $cache;
	}

	public function init() {
		$this->referenceText = array();
		$this->translationText = array();
	}

	public function addSentence( $reference, $translation, $meta ) {
		$this->referenceText []= $reference;
		$this->translationText  []= $translation;

		return 0;
	}

	public function getScore() {
		if (count($this->referenceText) == 0 || count($this->translationText) == 0) {
			return -1;
		}
		#echo("Getting score of TER");
		list($return, $output) = $this->cache->call(array($this, 'runExternalCommandOnSentences'), $this->referenceText, $this->translationText);
		#list($return, $output) = $this->runExternalCommandOnSentences($this->referenceText, $this->translationText);
		#echo($output);
		if ($return != 0){
			return -1;
		}

		return $this->processOutput($output);
	}

	public function runExternalCommandOnSentences($referenceText, $translationText) {
		$reference = $this->saveSentencesToFile($referenceText, "temp/ref");
		$hypothesis  = $this->saveSentencesToFile($translationText, "temp/hyp");
		$cmd = sprintf("%s -r %s -h %s --noTxtIds --HTER --tercom --TokenizedText", $this->externalCommand, $reference, $hypothesis);
		exec($cmd, $output, $return);
		unlink($reference);
		unlink($hypothesis);
		return array($return, $output);
	}

	private function saveSentencesToFile($sentences, $path) {
		$path .= md5(time());
		file_put_contents($path, implode("\n", $sentences));

		return $path;
	}

	private function processOutput($output){
		// tercom 0.6 provides output that looks like:
		// HTER: 0.884615 (23/26)
		$tempArray = explode(" ", end($output));
		return floatval($tempArray[1]) * 100;
	}

}
