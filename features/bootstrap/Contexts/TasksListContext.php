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

}
