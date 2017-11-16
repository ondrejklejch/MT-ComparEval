<?php

/**
 * IMetric defines interface for various metrics
 *
 * For more information about adding new metric, please have a look at programmers documentation
 */
interface IMetric {

	public function init();
	public function addSentence( $reference, $translation, $meta );
	public function getScore();

}
