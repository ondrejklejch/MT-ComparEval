<?php

namespace BackgroundModule;

class ExperimentsPresenter extends \Nette\Application\UI\Presenter {

	public function renderWatch( $folder, $sleep = 500000 ) {
		echo "Experiments watcher is watching folder: $folder\n";
		
		while( TRUE ) {
			usleep( $sleep );

			$experiments = \Nette\Utils\Finder::findDirectories( '*' )
				->from( $folder )
				->imported( FALSE );	
			foreach( $experiments as $experiment ) {
				$experimentFolder = new \Folder( $experiment );
				$experimentFolder->lock();

				$config = $this->getConfig( $experimentFolder );

				$experimentName = $experimentFolder->getName();
				echo "New experiment called $experimentName was found\n";
				echo "{$config['source']} will be used as a source sentences source in $experimentName\n";		
				echo "{$config['reference']} will be used as a reference sentences source in $experimentName\n";		

				foreach( array( 'source', 'reference' ) as $resource ) {
					if( !$experimentFolder->fileExists( $config[$resource] ) ) {
						echo "Missing $resource sentences in $experimentName\n";
						echo "Parsing of $experimentName aborted!";
						continue 2;
					}
				}

				$sentences = array();
				foreach( array( 'source', 'reference' ) as $resource ) {
					echo "Starting parsing of $resource sentences located in {$config[$resource]} for $experimentName\n";
					$sentences[$resource] =  $this->getSentences( $experimentFolder, $config[$resource] );
					$count = count( $sentences[$resource] );

					echo "$experimentName has $count $resource sentences\n";
				}

				if( count( $sentences['source'] ) != count( $sentences['reference'] ) ) {
					echo "$experimentName has bad number of source/reference sentences\n";
					echo "Parsing of $experimentName aborted!";
					continue;
				}
			}
		}

		$this->terminate();
	}

	
	private function getSentences( \Folder $experimentFolder, $filename ) {
		$filepath = $experimentFolder->getChildrenPath( $filename );
		$contents = trim( file_get_contents( $filepath ) );

		return explode( "\n", $contents );
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
