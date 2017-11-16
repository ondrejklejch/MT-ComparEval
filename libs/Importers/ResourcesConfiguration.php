<?php

/**
 * ResourcesConfiguration wraps configuration informations read from config file
 *
 * It also handles missing values by returning default values if possible. So programmers
 * can specify default values for each configuration and these values can be skipped in the
 * config file.
 */
class ResourcesConfiguration implements ArrayAccess {

	private $data;
	private $defaults;

	public function __construct( $path, $defaults ) {
		$this->data = $this->parseConfig( $path );
		$this->defaults = $defaults;
	}

	private function parseConfig( $path ) {
		if( file_exists( $path ) ) {
			return (array) \Nette\Utils\Neon::decode( file_get_contents( $path ) );
		} else {
			return array();
		}
	}

	public function offsetExists( $offset ) {
		return isset( $this->data[$offset] ) || isset( $this->defaults[$offset] );
	}	

	public function offsetGet( $offset ) {
		if( isset( $this->data[$offset] ) ) {
			return $this->data[$offset];
		} else if( isset( $this->defaults[$offset] ) ) {
			return $this->defaults[$offset];
		} else {
			throw new InvalidArgumentException( 'Given offset does not exist' );
		}
	}

	public function offsetSet( $offset, $value ) {
		throw new \Nette\NotSupportedException( 'Configuration is read-only' );
	}
	
	public function offsetUnset( $offset ) {
		throw new \Nette\NotSupportedException( 'Configuration is read-only' );
	}

}
