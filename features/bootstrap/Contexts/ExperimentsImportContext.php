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
			`rm -rf $experimentFolder`;
		}

		$experiments = $this->getMainContext()->getSubcontext( 'nette' )->getService( 'experiments' );
		$experiments->deleteExperimentByName( $experimentName );
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
		mkdir( $experimentFolder );

		`cp -r examples/small-project/* $experimentFolder`;
		$importer = $this->getMainContext()->getSubcontext( 'nette' )->getService( 'experimentsImporter' );
		$importer->setLogger( new \EmptyLogger() );
		$importer->importFromFolder( new \Folder( new \SplFileInfo( $experimentFolder ) ) );	
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

		`cp -r examples/small-project/* $experimentFolder`;
	}

	/**
	 * @Given /^I forget to upload "([^"]*)" for "([^"]*)"$/
	 */
	public function iForgetToUploadFor( $file, $experimentName ) {
		$path = self::$dataFolder . '/' . $experimentName . '/'. $file;

		if( file_exists( $path ) ) {
			unlink( $path );
		}
	}

	/**
	 * @Given /^"([^"]*)" has "([^"]*)" with contents:$/
	 */
	public function hasWithContents( $experimentName, $filename, PyStringNode $contents ) {
		$path = self::$dataFolder . '/' . $experimentName . '/' . $filename;

		file_put_contents( $path, (string) $contents );
	}

	/**
	 * @When /^"([^"]*)" is uploaded successfully$/
	 */
	public function isUploadedSuccessfully( $experimentName ) {
		$pattern = "Experiment $experimentName uploaded successfully";
		$message = "Experiment is not uploaded successfully";

		$this->assertLogContains( $pattern, $message );
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

	/**
	 * @Then /^experiments watcher should use "([^"]*)" for "([^"]*)" in "([^"]*)"$/
	 */
	public function experimentsWatcherShouldUseForIn( $file, $resource, $experimentName ) {
		$pattern = "$file will be used as a $resource source in $experimentName";		
		$message = "Using bad source for sentences"; 

		$this->assertLogContains( $pattern, $message );
	}

	/**
	 * @Then /^experiments watcher should complain about missing "([^"]*)" for "([^"]*)"$/
	 */
	public function experimentsWatcherShouldComplainAboutMissingFor( $resource, $experimentName ) {
		$pattern = "Missing $resource in $experimentName";
		$message = "Missing file with $resource sentences";
		
		$this->assertLogContains( $pattern, $message );
	}

	/**
	 * @Then /^experiments watcher should (not )?parse "([^"]*)" in "([^"]*)" for "([^"]*)"$/
	 */
	public function experimentsWatcherShouldParseInFor( $shouldNotParse, $resource, $file, $experimentName ) {
		$pattern = "Starting parsing of $resource located in $file for $experimentName";
		$message = "Parsing of resources didn't start";

		if( $shouldNotParse ) {
			$this->assertLogDoesNotContain( $pattern, $message );
		} else {
			$this->assertLogContains( $pattern, $message );
		}
	}

	/**
	 * @Then /^experiments watcher should say that "([^"]*)" has (\d+) "([^"]*)"$/
	 */
	public function experimentsWatcherShouldSayThatHas( $experimentName, $count, $resource ) {
		$pattern = "$experimentName has $count $resource";
		$message = "Number of resources parsed correctly";
		
		$this->assertLogContains( $pattern, $message );
	}

	/**
	 * @Then /^experiments watcher should say that "([^"]*)" has bad source\/reference count$/
	 */
	public function experimentsWatcherShouldSayThatHasBadSourceReferenceCount( $experimentName ) {
		$pattern = "$experimentName has bad number of source/reference sentences";
		$message = "Number of source/reference sentences doesn't match";

		$this->assertLogContains( $pattern, $message );
	}

	/**
	 * @Given /^experiments watcher should abort parsing of "([^"]*)"$/
	 */
	public function experimentsWatcherShouldAbortParsingOf( $experimentName ) {
		$pattern = "Parsing of $experimentName aborted!";
		$message = "Parsing of experiment aborted due to incorrect input";

		$this->assertLogContains( $pattern, $message );
	}

}
