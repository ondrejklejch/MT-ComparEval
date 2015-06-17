<?php

/**
 * Folder represents basic operations used by MT-ComparEval on folders
 */
class Folder {

	protected $path;

	public function __construct( SplFileInfo $path ) {
		$this->path = $path;
	}

	public function getName() {
		return $this->path->getBaseName();
	}

	public function getParent() {
		return new Folder( new SplFileInfo( dirname( $this->path->getPathname() ) ) );
	}

	public function fileExists( $filename ) {
		return file_exists( $this->getChildrenPath( $filename ) );
	}

	public function getChildrenPath( $filename ) {
		return $this->path->getPathname() . '/' . $filename;
	}

	public function lock( $name = 'imported' ) {
		$lockPath = $this->path->getPathname() . '/.' . $name;
		touch( $lockPath );
	}
}

