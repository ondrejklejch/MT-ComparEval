<?php

class Folder {

	protected $path;

	public function __construct( $path ) {
		$this->path = $path;
	}

	public function getName() {
		return $this->path->getBaseName();
	}

	public function getParent() {
		return new \Folder( new \SplFileInfo( dirname( $this->path->getPathname() ) ) );
	}

	public function lock() {
		$lockPath = $this->path->getPathname() . '/.imported';
		touch( $lockPath );
	}
}

