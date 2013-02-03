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
	 * @Given /^tasks watcher is running$/
	 */
	public function tasksWatcherIsRunning() {
		self::$watcher->start();
	}

	/**
	 * @Given /^there is unimported experiment called "([^"]*)"$/
	 */
	public function thereIsUnimportedExperimentCalled( $experimentName ) {
		$experimentFolder = self::$dataFolder . '/' . $experimentName;

		mkdir( $experimentFolder );
	}

	/**
	 * @When /^I start tasks watcher$/
	 */
	public function iStartTasksWatcher() {
		self::$watcher->start();
	}

	/**
	 * @When /^I upload task called "([^"]*)" to "([^"]*)"$/
	 */
	public function iUploadTaskCalledTo( $taskName, $experimentName ) {
		$taskFolder = self::$dataFolder . '/' . $experimentName . '/' . $taskName;

		mkdir( $taskFolder );
	}

	/**
	 * @Then /^tasks watcher should watch that folder$/
	 */
	public function tasksWatcherShouldWatchThatFolder() {
		$pattern = 'Tasks watcher is watching folder: ./data';
		$message =  'Tasks watcher is not watching given folder';

		$this->assertLogContains( $pattern, $message );
	}

	/**
	 * @Then /^tasks watcher should find it$/
	 */
	public function tasksWatcherShouldFindIt() {
		$pattern = 'New task called new-task was found in experiment old-experiment';
		$message = 'New task was not found';

		$this->assertLogContains( $pattern, $message );
	}

	/**
	 * @Then /^tasks watcher should not find it$/
	 */
	public function tasksWatcherShouldNotFindIt() {
		$pattern = 'New task called new-task was found in experiment new-experiment';
		$message = 'New task was not found';

		$this->assertLogDoesNotContain( $pattern, $message );
	}

}

