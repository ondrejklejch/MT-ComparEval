<?php

use Behat\MinkExtension\Context\RawMinkContext;

abstract class BasePageContext extends RawMinkContext {
	
	protected $page;

	protected function getUrl( $relativeUrl ) {
		return 'http://localhost:8000/' . $relativeUrl;
	}


	protected function assert( $bool, $message ) {
		if( !$bool ) {
			throw new \Exception( $message );
		}

		return TRUE;
	}

}
