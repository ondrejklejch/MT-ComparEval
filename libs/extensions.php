<?php

\Nette\Utils\Finder::extensionMethod( 'imported', function( $finder, $shouldBeImported ) {
	return $finder->filter( function( $file ) use ( $shouldBeImported ) {
		$lockFile = $file->getPathname() . '/.imported';

		return file_exists( $lockFile ) === $shouldBeImported;
	} );
} );

\Nette\Utils\Finder::extensionMethod( 'toArray', function( $finder ) {
	$files = array();
	foreach( $finder as $file ) {
		$files[] = $file->getPathname();
	}

	return $files;
} );
