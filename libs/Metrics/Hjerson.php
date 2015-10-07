<?php

/**
 * Hjerson external metric implementation
 */
class Hjerson implements IMetric {

	private $referenceText = array();
	private $translationText = array();
	private $type = null;
	private $externalCmd = "python external/hjerson+.py";

	public function __construct($type = "") {
		$this->type = $type;
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
		$hashId = md5(time());
		$reference = "temp/ref." . $hashId;
		$hypothesis  = "temp/hyp." . $hashId;
		$refTxt = implode("\n", $this->referenceText);
		$hypTxt = implode("\n", $this->translationText);
		if ($refTxt != "" && $hypTxt != ""){
			file_put_contents($reference, $refTxt);
			file_put_contents($hypothesis, $hypTxt);
			$cmd = sprintf("%s -R %s -H %s", $this->externalCmd, $reference, $hypothesis);
			exec($cmd, $output, $return);
			unlink($reference);
			unlink($hypothesis);
			if ($return != 0){
				return -1;
			} else {
				foreach ($output as $line){
					$tempArray = explode("\t", $line);
					$typeName = trim(str_replace(":","",$tempArray[0]));
					if ($typeName == $this->type){
						return $tempArray[1]; // if you want to use % representation, use [2] instead
					}
				}
			}
		}
		return -1;
	}

}
