<?php

class EmptyLogger implements Logger {

	public function log( $message ) {
		//We don't want to output anything.
	}

}
