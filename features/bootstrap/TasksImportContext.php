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
	 * @When /^there is already imported task called "([^"]*)" in "([^"]*)"$/
	 */
	public function thereIsAlreadyImportedTaskCalled( $taskName, $experimentName ) {
		$taskFolder = self::$dataFolder . '/' . $experimentName . '/' . $taskName;
		$importedLock = $taskFolder . '/.imported';

		mkdir( $taskFolder );
		touch( $importedLock );	
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
	 * @Then /^tasks watcher should find "([^"]*)" in "([^"]*)"$/
	 */
	public function tasksWatcherShouldFindIn( $taskName, $experimentName ) {
		$pattern = $this->getWatcherMessage( $taskName, $experimentName );
		$message = 'New task was not found';

		$this->assertLogContains( $pattern, $message );
	}

	/**
	 * @Then /^tasks watcher should not find "([^"]*)" in "([^"]*)"$/
	 */
	public function tasksWatcherShouldNotFindIn( $taskName, $experimentName ) {
		$pattern = $this->getWatcherMessage( $taskName, $experimentName );
		$message = 'New task was found';

		$this->assertLogDoesNotContain( $pattern, $message );
	}

	/**
	 * @Then /^task watcher should find "([^"]*)" in "([^"]*)" only once$/
	 */
	public function taskWatcherShouldFindInOnlyOnce( $taskName, $experimentName ) {
		$pattern = $this->getWatcherMessage( $taskName, $experimentName );
		$message = 'New task was found more than once';
		
		$this->assertLogContainsExactlyOccurences( $pattern, $message, 1 );
	}

	private function getWatcherMessage( $taskName, $experimentName ) {
		return sprintf(  'New task called %s was found in experiment %s', $taskName, $experimentName );
	}


}

