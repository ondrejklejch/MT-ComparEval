<?php

use Behat\MinkExtension\Context\MinkContext;

abstract class BasePageContext extends MinkContext {

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
