<?php

/**
 * Logger implementation that output everything to STDOUT
 */
class EchoLogger implements Logger {

	public function log( $message ) {
		$date = date( 'd-M-Y H:i:s' ); 
		echo "[$date]\t$message\n";
	}

}
