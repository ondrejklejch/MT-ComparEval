<?php


class ExperimentsWatcher {

	private $pid;
	private $log;
	private $error;
	private $folder;

	public function __construct( $folder ) {
		$this->folder = $folder;
	}

	public function start() {
		$hash = md5( microtime() );
		$this->log = "/tmp/" . $hash . ".log"; 
		$this->error = "/tmp/" . $hash . ".error.log"; 

		$cmd = sprintf(
			"nohup php -f www/index.php Background:Experiments:watch --folder=%s > %s 2> %s & echo $!",
			$this->folder, $this->log, $this->error
		); 

		$this->pid = exec( $cmd );
		usleep( 200000 );
	}
	
	public function kill() {
		@posix_kill( $this->pid, 9 );
		@unlink( $this->log );
		@unlink( $this->error );
	}

	public function getOutput( $timeout = 1 ) {
		sleep( $timeout );
		return file_get_contents( $this->log );
	}

}
