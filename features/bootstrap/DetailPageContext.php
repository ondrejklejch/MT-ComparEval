<?php

class DetailPageContext extends BasePageContext {


	private $page;
	private $sentencesBeforeLoad = array();

	/**
	 * @Given /^there is a result for machine translation$/
	 */
	public function thereIsAResultForMachineTranslation() {
		return true;
	}

	/**
	 * @Given /^there are two results for machine translations$/
	 */
	public function thereAreTwoResultsForMachineTranslations() {
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
	 * @When /^I open page with comparison$/
	 */
	public function iOpenPageWithComparison() {
		$this->getSession()->visit( $this->getUrl( 'tasks/1-2/compare' ) );
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
	 * @When /^I sort sentences by metric in ascending order$/
	 */
	public function iSortSentencesByMetricInAscendingOrder() {
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
		$this->sentencesBeforeLoad = $this->page->getSentences();
		$this->getSession()->wait(100);
	}

	/**
	 * @When /^I scroll down$/
	 */
	public function iScrollDown() {
		$this->getSession()->wait(500);
		$this->page->scrollDown();
		$this->getSession()->wait(500);
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
	 * @Then /^every sentence should have id, source, reference and (\d+) translations?$/
	 */
	public function everySentenceShouldHaveIdSourceReferenceAndTranslations( $translationsCount ) {
		$sentences = $this->page->getSentences();

		foreach( $sentences as $sentence ) {
			$id = $sentence->getId();
			$source = $sentence->getSource();
			$reference = $sentence->getReference();
			$translations = $sentence->getTranslations();

			$this->assert( !$this->isNull( $id ), 'Not every sentence has id' );
			$this->assert( !$this->isNull( $source ), 'Not every sentence has source' );
			$this->assert( !$this->isNull( $reference ), 'Not every sentence has reference' );
			$this->assert( count( $translations ) == $translationsCount, 'Not every sentence has ' . $translationsCount . ' translations' );
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

		$this->assert( $currentSentencesCount > count( $this->sentencesBeforeLoad ), 'No sentence loaded' );
	}

	/**
	 * @Then /^sentences should be unique$/
	 */
	public function sentencesShouldBeUnique() {
		$sentences = $this->page->getSentences();
		$uniqueSentencesIds = array_unique( array_map( function( $sentence ) {
			return $sentence->getId();
		}, $sentences ) );

		$this->assert( count( $sentences ) == count( $uniqueSentencesIds ), 'Sentences are not unique' );
	}

	/**
	 * @Given /^new sentences should have bigger score than old sentences$/
	 */
	public function newSentencesShouldHaveBiggerScoreThanOldSentences() {
		$sentences = $this->page->getSentences();
		$newSentences = $this->removeOldSentences( $sentences ); 

		foreach( $newSentences as $newSentence ) {
			foreach( $this->sentencesBeforeLoad as $oldSentence ) {
				$newTranslations = $newSentence->getTranslations();
				$oldTranslations = $oldSentence->getTranslations();


				$this->assert(
					$newTranslations[0]->getMetric() >= $oldTranslations[0]->getMetric(),
					'Loaded sentences do not preserve order'
				);
			}
		}
	}

	/**
	 * @Given /^new sentences should have smaller score than old sentences$/
	 */
	public function newSentencesShouldHaveSmallerScoreThanOldSentences() {
		$sentences = $this->page->getSentences();
		$newSentences = $this->removeOldSentences( $sentences ); 

		foreach( $newSentences as $newSentence ) {
			foreach( $this->sentencesBeforeLoad as $oldSentence ) {
				$newTranslations = $newSentence->getTranslations();
				$oldTranslations = $oldSentence->getTranslations();


				$this->assert(
					$newTranslations[0]->getMetric() <= $oldTranslations[0]->getMetric(),
					'Loaded sentences do not preserve order'
				);
			}
		}
	}



	private function removeOldSentences( $sentences ) {
		$oldSentencesIds = array_map( function( $sentence ) {
			return $sentence->getId();
		}, $this->sentencesBeforeLoad );

		return array_filter( $sentences, function( $sentence ) use ( $oldSentencesIds ) {
			return !isset( $oldSentencesIds[ $sentence->getId() ] );
		} );
	}


}
