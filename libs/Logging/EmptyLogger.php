<?php

/**
 * Logger implementation that output nothing
 */
class EmptyLogger implements Logger {

	public function log( $message ) {
		//We don't want to output anything.
	}

}
