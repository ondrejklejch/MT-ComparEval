<?php

namespace WorkersModule;

class BleuPresenter extends \BasePresenter {

	/** @var Tasks */
	private $tasksModel;

	/** @var Sentences */
	private $sentencesModel;
	
	/** @var NGrams */
	private $ngramsModel;

	
	public function startup() {
		parent::startup();

		$this->tasksModel = $this->getService( 'tasksModel' );
		$this->sentencesModel = $this->getService( 'sentencesModel' );
		$this->ngramsModel = $this->getService( 'ngramsModel' );
	}


	public function renderComputeBleuForTask( $id ) {
		$task = $this->tasksModel->getTask( $id );

		$matchingNGrams = $this->ngramsModel->getMatchingNGramsCountsByLength( $id ); 
		$translationNGrams = $this->ngramsModel->getTranslationNGramsCountsByLength( $id );
		$translationLength = $this->sentencesModel->getTranslationLength( $id );
		$referenceLength = $this->sentencesModel->getReferenceLength( $task->experiment_id ); 

		$geometricAverage = $this->computeGeometricAverage( $matchingNGrams, $translationNGrams );	
		$brevityPenalty = $this->computeBrevityPenalty( $translationLength, $referenceLength );

		$bleu = $brevityPenalty * exp( $geometricAverage );

		$this->sendResponse( new \Nette\Application\Responses\TextResponse( $bleu ) );
	}


	public function renderComputeDiffBleuForTask( $id ) {


	}


	private function computeGeometricAverage( $matchingNGrams, $translationNGrams ) {
		$geometricAverage = 0;

		foreach( $matchingNGrams as $ngrams ) {
			$matching[ $ngrams->length ] = $ngrams->count;
		}

		foreach( $translationNGrams as $ngrams ) {
			$translation[ $ngrams->length ] = $ngrams->count;
		}


		for( $length = 1; $length <= 4; $length++ ) {
			if( !isset( $matching[ $length ] ) ) {
				continue;
			}

			$precision = $matching[ $length ] / $translation[ $length ];
			$geometricAverage += 1/4 * log( $precision ); 
		}

		return $geometricAverage;
	}


	private function computeBrevityPenalty( $translationLength, $referenceLength ) {
		if( $translationLength <= $referenceLength ) {
			return exp( 1 - $referenceLength / $translationLength );
		} else {
			return 1;
		}
	}

}
