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
class TasksImportContext extends BaseImportContext {

	/**
	 * @BeforeScenario @tasksImport
	 */	
	public static function setUp() {
		self::$watcher = new BackgroundCommandWatcher( 'Tasks', self::$dataFolder );
	}

	/**
	 * @Given /^there is a folder where I can upload tasks$/
	 */
	public function thereIsAFolderWhereICanUploadTasks() {
		$this->assert( is_dir( self::$dataFolder ), 'Target folder does not exist' );
	}

	/**
	 * @When /^I start tasks watcher$/
	 */
	public function iStartTasksWatcher() {
		self::$watcher->start();
	}

	/**
	 * @Then /^tasks watcher should watch that folder$/
	 */
	public function tasksWatcherShouldWatchThatFolder() {
		$pattern = 'Tasks watcher is watching folder: ./data';
		$message =  'Tasks watcher is not watching given folder';

		$this->assertLogContains( $pattern, $message );
	}

}

