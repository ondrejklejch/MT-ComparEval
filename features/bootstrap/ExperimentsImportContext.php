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
class ExperimentsImportContext extends BaseImportContext {

	/**
	 * @BeforeScenario @experimentsImport
	 */	
	public static function setUp() {
		self::$watcher = new BackgroundCommandWatcher( 'Experiments', self::$dataFolder );
	}

	/**
	 * @Given /^there is a folder where I can upload experiments$/
	 */
	public function thereIsAFolderWhereICanUploadExperiments() {
		$this->assert( is_dir( self::$dataFolder ), 'Target folder does not exist' );
	}

	/**
	 * @Given /^there is no experiment called "([^"]*)"$/
	 */
	public function thereIsNoExperimentCalled( $experimentName ) {
		$experimentFolder = self::$dataFolder . '/' . $experimentName;

		if( file_exists( $experimentFolder ) ) {
			rmdir( $experimentFolder );
		}
	}

	/**
	 * @Given /^experiments watcher is running$/
	 */
	public function experimentsWatcherIsRunning() {
		self::$watcher->start();
	}

	/**
	 * @When /^there is already imported experiment called "([^"]*)"$/
	 */
	public function thereIsAlreadyImportedExperimentCalled( $experimentName ) {
		$experimentFolder = self::$dataFolder . '/' . $experimentName;
		$importedLock = $experimentFolder . '/.imported';

		if( !file_exists( $experimentFolder ) ) {
			mkdir( $experimentFolder );
			touch( $importedLock );	
		};
	}

	/**
	 * @When /^I start experiments watcher$/
	 */
	public function iStartExperimentsWatcher() {
		self::$watcher->start();
	}

	/**
	 * @When /^I upload experiment called "([^"]*)"$/
	 */
	public function iUploadExperimentCalled( $experimentName ) {
		$experimentFolder = self::$dataFolder . '/' . $experimentName;
		mkdir( $experimentFolder );
	}

	/**
	 * @Then /^experiments watcher should watch that folder$/
	 */
	public function experimentsWatcherShouldWatchThatFolder() {
		$pattern = 'Experiments watcher is watching folder: ./data';
		$message =  'Experiments watcher is not watching given folder';

		$this->assertLogContains( $pattern, $message );
	}

	/**
	 * @Then /^experiments watcher should find it$/
	 */
	public function experimentsWatcherShouldFindIt() {
		$pattern = 'New experiment called new-experiment was found';
		$message = 'New experiment was not found';

		$this->assertLogContains( $pattern, $message );
	}

	/**
	 * @Then /^experiments watcher should find only once$/
	 */
	public function experimentsWatcherShouldFindOnlyOnce() {
		$pattern = 'New experiment called new-experiment was found';
		$message = 'New experiment was not found only once';

		$this->assertLogContainsExactlyOccurences( $pattern, $message, 1 );
	}

	/**
	 * @Then /^experiments watcher should not find it$/
	 */
	public function experimentsWatcherShouldNotFindIt() {
		$pattern = 'New experiment called old-experiment was found';
		$message = 'Imported experiment was found';

		$this->assertLogDoesNotContain( $pattern, $message );
	}

}
