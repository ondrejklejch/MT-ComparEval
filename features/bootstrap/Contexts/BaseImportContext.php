<?php

use Behat\Behat\Context\ClosuredContextInterface,
	Behat\Behat\Context\TranslatedContextInterface,
	Behat\Behat\Context\BehatContext,
	Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
	Behat\Gherkin\Node\TableNode;


/**
 * Features context.
 */
abstract class BaseImportContext extends BehatContext {

	protected static $watcher;
	protected static $dataFolder = './data';

	/**
	 * @AfterScenario @import
	 */
	public static function closeWatcher() {
		if( self::$watcher !== NULL ) {
			self::$watcher->kill();
			self::$watcher = NULL;
		}

		`rm -rf data/*`;
	}

	protected function assertLogContains( $pattern, $message ) {
		$logContents = self::$watcher->getOutput();
		
		$this->assert( strpos( $logContents, $pattern ) !== FALSE, $message );
	}

	protected function assertLogContainsExactlyOccurences( $pattern, $message, $occurences ) {
		$logContents = self::$watcher->getOutput();

		$this->assert( substr_count( $logContents, $pattern ) == $occurences, $message );
	}

	protected function assertLogDoesNotContain( $pattern, $message ) {
		$logContents = self::$watcher->getOutput();
		
		$this->assert( strpos( $logContents, $pattern ) === FALSE, $message );
	}

	protected function assert( $condition, $message ) {
		if( !$condition ) {
			throw new Exception( $message );
		}

		return TRUE;
	}

}

