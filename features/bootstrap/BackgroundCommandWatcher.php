<?php

class BackgroundCommandWatcher {

	private $pid;
	private $log;
	private $error;
	private $folder;
	private $isRunning = FALSE;

	public function __construct( $name, $folder ) {
		$this->name = $name;
		$this->folder = $folder;
	}

	public function __destruct() {
		$this->kill();
	}

	public function start() {
		$hash = md5( microtime() );
		$this->log = "/tmp/" . $hash . ".log"; 
		$this->error = "/tmp/" . $hash . ".error.log"; 

		$cmd = sprintf(
			"nohup php -f www/index.php Background:%s:watch --folder=%s > %s 2> %s & echo $!",
			$this->name, $this->folder, $this->log, $this->error
		); 

		$this->pid = exec( $cmd );
		$this->isRunning = TRUE;
		usleep( 200000 );
	}
	
	public function kill() {
		if( !$this->isRunning ) {
			return;
		}

		@posix_kill( $this->pid, 9 );
		@unlink( $this->log );
		@unlink( $this->error );
		$this->isRunning = FALSE;
	}

	public function getOutput( $timeout = 1 ) {
		sleep( $timeout );
		return file_get_contents( $this->log );
	}

}
