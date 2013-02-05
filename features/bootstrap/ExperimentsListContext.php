<?php

class ExperimentsListContext extends BasePageContext {

	/**
	 * @When /^I open page with experiments list$/
	 */
	public function iOpenPageWithExperimentsList() {
		$this->openPage( 'experiments list' );
	}
	
	/**
	 * @Then /^I should see "([^"]*)" in the experiments list$/
	 */
	public function iShouldSeeInTheExperimentsList( $experimentName ) {
		$uploadedExperiments = $this->page->getExperimentsNames();

		$this->assert(
			in_array( $experimentName, $uploadedExperiments ),
			'Uploaded experiment is not in list'
		);
	}


	public function openPage( $view ) {
		switch( $view ) {
			case 'experiments list':
				$this->getSession()->visit( $this->getUrl( '' ) );
				break;
		}

		$this->getSession()->wait(200);
		$this->page = new ExperimentsListPage( $this->getSession()->getPage() );
	}


}

