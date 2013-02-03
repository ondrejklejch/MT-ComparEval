<?php

namespace BackgroundModule;

class ExperimentsPresenter extends \Nette\Application\UI\Presenter {

	public function renderWatch( $folder ) {
		echo "Experiments watcher is watching folder: $folder\n";
		
		while( true ) {
			$experiments = \Nette\Utils\Finder::find( '*' )->in( $folder );	
			foreach( $experiments as $experiment ) {
				if( file_exists( $experiment->getPathname() . '/.imported' ) ) {
					continue;
				}

				echo "New experiment called ". $experiment->getBaseName() ." was found\n";
				touch( $experiment->getPathname() . '/.imported' );
			}

			usleep( 500000 );
		}

		$this->terminate();
	}

}
