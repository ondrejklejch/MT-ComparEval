<?php

namespace BackgroundModule;

class ExperimentsPresenter extends \Nette\Application\UI\Presenter {

	public function renderWatch( $folder, $sleep = 500000 ) {
		echo "Experiments watcher is watching folder: $folder\n";
		
		while( TRUE ) {
			usleep( $sleep );

			foreach( $this->getUnimportedExperiments( $folder ) as $experiment ) {
				$this->parseExperiment( $experiment );
			}
		}

		$this->terminate();
	}

	private function getUnimportedExperiments( $folder ) {
		return \Nette\Utils\Finder::findDirectories( '*' )
			->from( $folder )
			->imported( FALSE );	
	}

	private function parseExperiment( $experiment ) {
		$experimentFolder = new \Folder( $experiment );

		$config = $this->getConfig( $experimentFolder );
		$experimentName = $experimentFolder->getName();
		echo "New experiment called $experimentName was found\n";

		try {
			$sentences = array();
			foreach( array( 'source', 'reference' ) as $resource ) {
				$sentences[$resource] = $this->parseResource( $experimentName, $experimentFolder, $resource, $config );
			}

			if( count( $sentences['source'] ) != count( $sentences['reference'] ) ) {
				$this->handleNotMatchingNumberOfSentences( $experimentName );
				continue;
			}

			$this->getService( 'experiments' )->saveExperiment( $experimentName );
			echo "Experiment $experimentName uploaded successfully.\n";	
			$experimentFolder->lock();
		} catch( \InvalidSentencesResourceException $exception ) {
			$this->handleInvalidSentencesResource( $experimentName, $resource );
			continue;
		}
	}

	private function parseResource( $experimentName, $experimentFolder, $resource, $config ) {
		echo "{$config[$resource]} will be used as a $resource sentences source in $experimentName\n";		

		$sentences =  $this->getSentences( $experimentFolder, $config[$resource] );
		echo "Starting parsing of $resource sentences located in {$config[$resource]} for $experimentName\n";
		$count = $sentences->count();

		echo "$experimentName has $count $resource sentences\n";

		return $sentences;
	}
	
	private function getSentences( \Folder $experimentFolder, $filename ) {
		$filepath = $experimentFolder->getChildrenPath( $filename );

		return new \FileSentencesIterator( $filepath );
	}

	private function handleInvalidSentencesResource( $experimentName, $resource ) {
		echo "Missing $resource sentences in $experimentName\n";
		echo "Parsing of $experimentName aborted!";
	}

	private function handleNotMatchingNumberOfSentences( $experimentName ) {
		echo "$experimentName has bad number of source/reference sentences\n";
		echo "Parsing of $experimentName aborted!";
	}

	private function getConfig( \Folder $experimentFolder ) {
		$config = array();
		if( $experimentFolder->fileExists( 'config.neon' ) ) {
			$path = $experimentFolder->getChildrenPath( 'config.neon' );

			$config = (array) \Nette\Utils\Neon::decode( file_get_contents( $path ) );
		}

		$config['source'] = $this->getFromArrayWithDefault( $config, 'source', 'source.txt' ); 
		$config['reference'] = $this->getFromArrayWithDefault( $config, 'reference', 'reference.txt' ); 

		return $config;
	}

	private function getFromArrayWithDefault( $array, $key, $default ) {
		if( isset( $array[ $key ] ) ) {
			return $array[ $key ];
		} else {
			return $default;
		}
	}

}
