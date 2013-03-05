<?php

class TasksListContext extends BasePageContext {

	/**
	 * @Then /^I should see "([^"]*)" in the tasks list$/
	 */
	public function iShouldSeeInTheTasksList( $taskName ) {
		$this->page = new TasksListPage( $this->getSession()->getPage() );
		$uploadedTasks = $this->page->getTasksNames();

		$this->assert(
			in_array( $taskName, $uploadedTasks ),
			'Uploaded task is not in list'
		);
	}

	/**
	 * @Given /^task "([^"]*)" should have "([^"]*)" == "([^"]*)"$/
	 */
	public function taskShouldHave( $taskName, $key, $value ) {
		$this->page = new TasksListPage( $this->getSession()->getPage() );
		$actualValue = $this->page->getValue( $taskName, $key );

		$this->assert(
			$actualValue == $value,
			"Experiment had unexpected $key"
		);
	}

	/**
	 * @Given /^task "([^"]*)" should have metric "([^"]*)" == "([^"]*)"$/
	 */
	public function taskShouldHaveMetric( $taskName, $metric, $expectedValue ) {
		$this->page = new TasksListPage( $this->getSession()->getPage() );
		$task = $this->page->getTask( $taskName );
		$currentValue = $task->getMetric( $metric ); 

		$this->assert(
			$currentValue == $expectedValue,
			"Incorrect {$metric} value for {$taskName}.\n" . 
			"Expected:\t$expectedValue\n" .
			"Got:\t$currentValue"
		);
	}

	/**
	 * @Given /^I click on task "([^"]*)"$/
	 */
	public function iClickOnTask( $taskName ) {
		$this->page = new TasksListPage( $this->getSession()->getPage() );
		$this->page->getTask( $taskName )->openSentences();
		$this->getSession()->wait( 500 );
	}

}
