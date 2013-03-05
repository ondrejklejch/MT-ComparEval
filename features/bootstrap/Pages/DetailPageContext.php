<?php

class DetailPageContext extends BasePageContext {

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
	 * @When /^I open page with comparison$/
	 */
	public function iOpenPageWithComparison() {
		$this->openPage( 'comparison' );
	}

	/**
	 * @When /^I open page with result$/
	 */
	public function iOpenPageWithResult() {
		$this->openPage( 'result' );
	}

	private function openPage( $view ) {
		switch( $view ) {
			case 'result':
				$this->getSession()->visit( $this->getUrl( 'tasks/1/detail' ) );
				break;
			case 'comparison':
				$this->getSession()->visit( $this->getUrl( 'tasks/1-2/compare' ) );
				break;
		}

		$this->getSession()->wait(200);
		$this->page = new TaskDetailPage( $this->getSession()->getPage() );
	}

	/**
	 * @When /^I sort sentences by (.*) in (.*) order$/
	 */
	public function iSortSentences( $orderBy, $order ) {
		switch( $order ) {
			case 'ascending':
				$this->page->sortSentencesByMetric( 'asc' );
				break;
			case 'descending':
				$this->page->sortSentencesByMetric( 'desc' );
				break;
		}

		$this->getSession()->wait(200);
	}

	/**
	 * @When /^I cancel this sort$/
	 */
	public function iCancelThisSort() {
		$this->page->sortSentencesById();
		$this->getSession()->wait(200);
	}

	/**
	 * @When /^I choose (.*) metric$/
	 */
	public function iChooseAnotherMetric( $metric ) {
		$this->page->chooseMetric( $metric );
	}

	/**
	 * @When /^part of the result is already shown$/
	 */
	public function partOfTheResultIsAlreadyShown()	{
		$this->sentencesBeforeLoad = $this->page->getSentences();
	}

	/**
	 * @When /^I scroll down$/
	 */
	public function iScrollDown() {
		$this->page->scrollDown();
		$this->getSession()->wait(200);
	}

	/**
	 * @Then /^sentences should be shown$/
	 */
	public function sentencesShouldBeShown() {
		$sentences = $this->page->getSentences();

		$this->assert( 
			count( $sentences ) == 10,
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
	 * @Given /^every sentence should have diff metric$/
	 */
	public function everySentenceShouldHaveDiffMetric() {
		$sentences = $this->page->getSentences();

		foreach( $sentences as $sentence ) {
			$metric = $sentence->getDiffMetric();

			$this->assert( !$this->isNull( $metric ), 'Not every sentence has diff metric' );
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
	 * @Then /^sentences should be sorted by (.*) in (.*) order$/
	 */
	public function sentencesShouldBeSorted( $orderBy, $order ) {
		$sentences = $this->page->getSentences();
		$getCurrentValue = $this->getValueAccessor( $orderBy );
		$compare = $this->getComparator( $order );
		$lastValue = ( $order == 'ascending' ) ? -PHP_INT_SIZE : PHP_INT_SIZE;

		foreach( $sentences as $sentence ) {
			$currentValue = $getCurrentValue( $sentence );

			$this->assert(
				$compare( $lastValue, $currentValue ),
				'Sentences are not sorted by ' . $orderBy . ' in ' . $order . ' order' 
			);

			$lastValue = $currentValue;
		}
	}

	/**
	 * @Then /^(.*) metric should be active$/
	 */
	public function anotherMetricShouldBeActive( $metric ) {
		$currentMetric = $this->page->getActiveMetric();

		$this->assert( $currentMetric === $metric, 'Current metric is not correct' );
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
	 * @Given /^new sentences should have (smaller|bigger) (.*) than old sentences$/
	 */
	public function newSentencesShouldPreserveOrder( $comparsion, $metric ) {
		$sentences = $this->page->getSentences();
		$newSentences = $this->removeOldSentences( $sentences ); 
		$getCurrentValue = $this->getValueAccessor( $metric );
		$compare = $this->getComparator( $comparsion );

		foreach( $newSentences as $newSentence ) {
			foreach( $this->sentencesBeforeLoad as $oldSentence ) {
				$newValue = $getCurrentValue( $newSentence );
				$oldValue = $getCurrentValue( $oldSentence );

				$this->assert(
					$compare( $oldValue, $newValue ),
					'Loaded sentences do not preserve order'
				);
			}
		}
	}


	private function getValueAccessor( $metric ) {
		switch( $metric ) {
			case 'id':
				return function( $sentence ) {
					return $sentence->getId();
				};
			case 'metric':
				return function( $sentence ) {
					$translations = $sentence->getTranslations();
					
					return $translations[0]->getMetric();
				};
			case 'diff metric':
				return function( $sentence ) {
					$translations = $sentence->getTranslations();
					
					return $translations[0]->getMetric() - $translations[1]->getMetric();
				};
		}
	}


	private function getComparator( $comparsion ) {
		$lte = function( $a, $b ) { return $a <= $b; };
		$gte = function( $a, $b ) { return $a >= $b; };

		switch( $comparsion ) {
			case 'bigger':
				return $lte;
			case 'smaller':
				return $gte;
			case 'ascending':
				return $lte;
			case 'descending':
				return $gte;
		}
	}

	private function removeOldSentences( $sentences ) {
		$oldSentencesIds = array_flip( array_map( function( $sentence ) {
			return $sentence->getId();
		}, $this->sentencesBeforeLoad ) );

		return array_filter( $sentences, function( $sentence ) use ( $oldSentencesIds ) {
			return !isset( $oldSentencesIds[ $sentence->getId() ] );
		} );
	}


}
