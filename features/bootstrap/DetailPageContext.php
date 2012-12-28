<?php

class DetailPageContext extends BasePageContext {


	private $page;
	private $sentencesCountBeforeLoad = 0;

	/**
	 * @Given /^there is a result for machine translation$/
	 */
	public function thereIsAResultForMachineTranslation() {
		return true;
	}

	/**
	 * @When /^I open page with result$/
	 */
	public function iOpenPageWithResult() {
		$this->getSession()->visit( $this->getUrl( 'tasks/1/detail' ) );
		$this->getSession()->wait(100);

		$this->page = new TaskDetailPage( $this->getSession()->getPage() );
	}

	/**
	 * @When /^I sort sentences by metric in descending order$/
	 */
	public function iSortSentencesByMetricInDescendingOrder() {
		$this->page->sortSentencesByMetric( 'desc' );
	}

	/**
	 * @When /^I sort senteces by metric in ascending order$/
	 */
	public function iSortSentecesByMetricInAscendingOrder()	{
		$this->page->sortSentencesByMetric( 'asc' );
	}

	/**
	 * @When /^I cancel this sort$/
	 */
	public function iCancelThisSort() {
		$this->page->sortSentencesById();
	}

	/**
	 * @When /^I choose another metric$/
	 */
	public function iChooseAnotherMetric() {
		$this->page->chooseMetric( 'rand' );
	}

	/**
	 * @When /^part of the result is already shown$/
	 */
	public function partOfTheResultIsAlreadyShown()	{
		$this->sentencesCountBeforeLoad = count( $this->page->getSentences() );
	}

	/**
	 * @When /^I scroll down$/
	 */
	public function iScrollDown() {
		$this->page->scrollDown();
	}

	/**
	 * @Then /^sentences should be shown$/
	 */
	public function sentencesShouldBeShown() {
		$sentences = $this->page->getSentences();

		$this->assert( 
			count( $sentences ) > 0,
			"Sentences aren't shown."
		);
	}

	/**
	 * @Given /^every sentence should have id, source, reference and translations$/
	 */
	public function everySentenceShouldHaveIdSourceReferenceAndTranslations() {
		$sentences = $this->page->getSentences();

		foreach( $sentences as $sentence ) {
			$id = $sentence->getId();
			$source = $sentence->getSource();
			$reference = $sentence->getReference();
			$translations = $sentence->getTranslations();

			$this->assert( !$this->isNull( $id ), 'Not every sentence has id' );
			$this->assert( !$this->isNull( $source ), 'Not every sentence has source' );
			$this->assert( !$this->isNull( $reference ), 'Not every sentence has reference' );
			$this->assert( count( $translations ) > 0, 'Not every sentence has translations' );
		}
	}

	/**
	 * @Given /^every translation should have text and metric$/
	 */
	public function everyTranslationShouldHaveTextAndMetric() {
		$sentences = $this->page->getSentences();

		foreach( $sentences as $sentence ) {
			$translations = $sentence->getTranslations();

			foreach( $translations as $translation ) {
				$text = $translation->getText();
				$metric = $translation->getMetric();

				$this->assert( !$this->isNull( $text ), 'Not every translation has text' );
				$this->assert( !$this->isNull( $metric), 'Not every translation has metric' );
			}
		}
	}

	private function isNull( $value ) {
		return $value === NULL || $value === "";
	}


	/**
	 * @Then /^sentences should be sorted in descending order$/
	 */
	public function sentencesShouldBeSortedInDescendingOrder() {
		$sentences = $this->page->getSentences();

		$lastValue = 1;
		foreach( $sentences as $sentence ) {
			$translations = $sentence->getTranslations();
			$currentValue = $translations[0]->getMetric();

			$this->assert( 
				$currentValue <= $lastValue,
				'Sentences are not sorted by metric in decreasing order'
			);
			
			$lastValue = $currentValue;
		}
	}


	/**
	 * @Then /^sentences should be sorted in ascending order$/
	 */
	public function sentencesShouldBeSortedInAscendingOrder() {
		$sentences = $this->page->getSentences();

		$lastValue = 0;
		foreach( $sentences as $sentence ) {
			$translations = $sentence->getTranslations();
			$currentValue = $translations[0]->getMetric();

			$this->assert( 
				$currentValue >= $lastValue,
				'Sentences are not sorted by metric in decreasing order'
			);
			
			$lastValue = $currentValue;
		}
	}

	/**
	 * @Then /^sentences should be sorted by id$/
	 */
	public function sentencesShouldBeSortedById() {
		$sentences = $this->page->getSentences();

		$lastValue = 0;
		foreach( $sentences as $sentence ) {
			$currentValue = $sentence->getId();
			$this->assert( 
				$currentValue >= $lastValue,
				'Sentences are not sorted by id'
			);
			
			$lastValue = $currentValue;
		}
	}

	/**
	 * @Then /^another metric should be active$/
	 */
	public function anotherMetricShouldBeActive() {
		$currentMetric = $this->page->getActiveMetric();

		$this->assert( $currentMetric === 'rand', 'Current metric is not correct' );
	}

	/**
	 * @Then /^more sentences should load$/
	 */
	public function moreSentencesShouldLoad() {
		$currentSentencesCount = count( $this->page->getSentences() );

		$this->assert( $currentSentencesCount > $this->sentencesCountBeforeLoad, 'No sentence loaded' );
	}

}
