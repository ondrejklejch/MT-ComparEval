<?php


class ExperimentsWatcher {

	private $handle;
	private $folder;

	public function __construct( $folder ) {
		$this->folder = $folder;
	}

	public function start() {
		$cmd = sprintf( "php -f www/index.php Background:Experiments:watch --folder=%s", $this->folder ); 
		$this->handle = popen( $cmd, "r" );
	}
	
	public function kill() {
		pclose( $this->handle );
	}

	public function getOutput( $timeout = 0.5 ) {
		$microtime = microtime( TRUE ) + $timeout;
		$lastTime = time();

		$result = "";
		while( !feof( $this->handle ) ) {
			$stats = fstat( $this->handle );
			if( $lastTime <= $stats['mtime'] ) {
				$result .= fgets( $this->handle );
			}

			$lastTime = time();
			if( microtime( TRUE ) >= $microtime ) { 
				break;
			}
		}
	
		return $result;
	}

}
