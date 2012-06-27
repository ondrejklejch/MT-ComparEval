<?php

namespace WorkersModule;

class DiscoveryPresenter extends \BasePresenter {
	
	/** @var Experiments */
	private $experimentsModel;


	public function startup() {
		parent::startup();

		$this->experimentsModel = $this->getService( 'experimentsModel' );
	}


	public function renderFindNewExperiments( $dir ) {
		$checkFile = "$dir/experiments-check";

		$newExperiments = \Nette\Utils\Finder::findFiles( "*/experiment.neon" )
			->from( $dir )
			->limitDepth( 1 )
			->filter( function( $file ) use ( $checkFile ) {
				return $file->getMTime() > filemtime( $checkFile );
			} );
		
		$response = "";
		foreach( $newExperiments as $experiment ) {
			$config = \Nette\Utils\Neon::decode( file_get_contents( $experiment->getPathname() ) );
			$config[ 'path' ] = $experiment->getPathname();

			if( !isset( $config[ 'name' ] ) || isset( $config[ 'id' ] ) ) {
				continue;
			}
	
			// @TODO add checks that files exists
			if( !isset( $config[ 'source' ] ) ) {
				$config[ 'source' ] = new \SplFileInfo( $experiment->getPath() . "/source" );
			}
	
			if( !isset( $config[ 'reference' ] ) ) {
				$config[ 'reference' ] = new \SplFileInfo( $experiment->getPath() . "/reference" );
			}

			$this->experimentsModel->createExperiment( $config );
			$response .= sprintf( "Experiment '%s' successfully imported.\n", $config[ 'name' ] );
		}


		touch( $checkFile );
		$response .= "Experiments imported\n";
		$this->sendResponse( new \Nette\Application\Responses\TextResponse( $response ) );
		
	}

}
