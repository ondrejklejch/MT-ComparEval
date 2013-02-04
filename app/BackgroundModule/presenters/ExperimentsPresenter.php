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

				$experimentName = $experimentFolder->getName();
				echo "New experiment called $experimentName was found\n";
				echo "Using source.txt as source sentences source in $experimentName\n";		
				echo "Using reference.txt as reference sentences source in $experimentName\n";		
			}
		}

		$this->terminate();
	}

}
