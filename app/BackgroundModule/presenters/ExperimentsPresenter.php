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
				echo "Using {$config['source']} as source sentences source in $experimentName\n";		
				echo "Using {$config['reference']} as reference sentences source in $experimentName\n";		
			}
		}

		$this->terminate();
	}


	private function getConfig( \Folder $experimentFolder ) {
		if( $experimentFolder->fileExists( 'config.neon' ) ) {
			$path = $experimentFolder->getChildrenPath( 'config.neon' );

			return \Nette\Utils\Neon::decode( file_get_contents( $path ) );
		} else {
			return array(
				'source' => 'source.txt',
				'reference' => 'reference.txt'
			);
		}
	}

}
