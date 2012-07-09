<?php

namespace WorkersModule;

class DiscoveryPresenter extends \BasePresenter {
	
	/** @var Experiments */
	private $experimentsModel;

	/** @var Tasks */
	private $tasksModel;


	public function startup() {
		parent::startup();

		$this->experimentsModel = $this->getService( 'experimentsModel' );
		$this->tasksModel = $this->getService( 'tasksModel' );
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


	public function renderFindNewTasks( $dir ) {
		$checkFile = "$dir/tasks-check";

		// @TODO add check that experiment has been processed
		$newTasks = \Nette\Utils\Finder::findFiles( "*/*/task.neon" )
			->from( $dir )
			->limitDepth( 2 )
			->filter( function( $file ) use ( $checkFile ) {
				return $file->getMTime() > filemtime( $checkFile );
			} );

		$response = "";
		foreach( $newTasks as $task ) {
			$config = \Nette\Utils\Neon::decode( file_get_contents( $task->getPathname() ) );
			$config[ 'path' ] = $task->getPathname();

			if( !isset( $config[ 'name' ] ) || isset( $config[ 'id' ] ) ) {
				continue;
			}

			if( !isset( $config[ 'translation' ] ) ) {
				$config[ 'translation' ] = new \SplFileInfo( $task->getPath() . "/translation" );
			}

			$this->tasksModel->createTask( $config );
			$response .= sprintf( "Task '%s' successfully imported.\n", $config[ 'name' ] );
		}	

		touch( $checkFile );
		$response .= "Tasks imported\n";
		$this->sendResponse( new \Nette\Application\Responses\TextResponse( $response ) );
	}

}
