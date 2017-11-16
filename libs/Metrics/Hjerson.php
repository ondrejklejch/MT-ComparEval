<?php

/**
 * Hjerson external metric implementation
 */
class Hjerson implements IMetric {

	private $referenceText = array();
	private $translationText = array();
	private $type = null;
	private $cache = null;
	private $externalCommand = "python";
	private $externalScript = "external/hjerson+.py";

	public function __construct($type = "", Nette\Caching\Cache $cache) {
		$this->type = $type;
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

		list($return, $output) = $this->cache->call(array($this, 'runExternalCommandOnSentences'), $this->referenceText, $this->translationText);
		echo( $return );
		print_r( $output );
		echo( $cmd );
		if ($return != 0){
			return -1;
		}

		return $this->processOutput($output);
	}

	public function runExternalCommandOnSentences($referenceText, $translationText) {
		$reference = $this->saveSentencesToFile($referenceText, "temp/ref");
		$hypothesis  = $this->saveSentencesToFile($translationText, "temp/hyp");
		$cmd = sprintf("%s %s -R %s -H %s", $this->externalCommand, realpath($this->externalScript), $reference, $hypothesis);
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
		foreach ($output as $line){
			$tempArray = explode("\t", $line);
			$typeName = trim(str_replace(":","",$tempArray[0]));
			if ($typeName == $this->type){
				return $tempArray[1]; // if you want to use % representation, use [2] instead
			}
		}
	}

}
