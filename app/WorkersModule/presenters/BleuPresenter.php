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

		$matchingNGrams = $this->groupByLength( $this->ngramsModel->getMatchingNGramsCountsByLength( $id ) ); 
		$translationNGrams = $this->groupByLength( $this->ngramsModel->getTranslationNGramsCountsByLength( $id ) );
		$translationLength = $this->sentencesModel->getTranslationLength( $id );
		$referenceLength = $this->sentencesModel->getReferenceLength( $task->experiment_id ); 

		$geometricAverage = $this->computeGeometricAverage( $matchingNGrams, $translationNGrams );	
		$brevityPenalty = $this->computeBrevityPenalty( $translationLength, $referenceLength );

		$bleu = number_format( $brevityPenalty * exp( $geometricAverage ), 4 );
		$this->tasksModel->setBleu( $task, $bleu );
	
		$this->sendResponse( new \Nette\Application\Responses\TextResponse( "Bleu computed\n" ) );
	}


	private function groupByLength( $ngrams ) {
		$ngramsByLength = array();
		foreach( $ngrams as $ngram ) {
			$ngramsByLength[ $ngram->length ] = $ngram->count;
		}

		return $ngramsByLength;
	}


	public function renderComputeDiffBleuForTask( $id ) {
		$sentences = $this->sentencesModel->getSentencesIdsForTask( $id );
		$matchingNgramsBySentence = $this->ngramsModel->getMatchingNgramsCountsBySentenceAndLength( $id );	
		$translationNgramsBySentence = $this->ngramsModel->getTranslationNgramsCountsBySentenceAndLength( $id );

		foreach( $sentences as $sentence ) {
			if( !isset( $matchingNgramsBySentence[ $sentence->id ] ) ) {
				$bleu = 0;
			} else {
				$geometricAverage = $this->computeGeometricAverage( 
					$matchingNgramsBySentence[ $sentence->id ], $translationNgramsBySentence[ $sentence->id ]
				);

				$bleu = number_format( exp( $geometricAverage ), 4 ); 
			}

			$this->sentencesModel->setDiffBleu( $id, $sentence->id, $bleu );
		}

		$this->sendResponse( new \Nette\Application\Responses\TextResponse( "DiffBleu computed\n" ) );
	}


	private function computeGeometricAverage( $matchingNGrams, $translationNGrams ) {
		$geometricAverage = 0;

		for( $length = 1; $length <= 4; $length++ ) {
			if( !isset( $matchingNGrams[ $length ] ) ) {
				continue;
			}

			$precision = $matchingNGrams[ $length ] / $translationNGrams[ $length ];
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
