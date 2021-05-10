<?php


/**
 * NGrams is used for computing top improving/worsening n-grams of two tasks
 */
class NGrams {

	private $db;

	public function __construct( Nette\Database\Context $db ) {
		$this->db = $db;
	}

	public function getImproving( $task1, $task2 ) {
		$task1Ngrams = $this->getConfirmed( $task1 );
		$task2Ngrams = $this->getConfirmed( $task2 );

		list( $allImproving1, $allImproving2 ) = $this->mergeNgrams( $task1Ngrams, $task2Ngrams );
		$improving1 = $this->filterImproving( $allImproving1 );
		$improving2 = $this->filterImproving( $allImproving2 );

		$improving1 = $this->addOtherOccurences( $improving1, $allImproving2 );
		$improving2 = $this->addOtherOccurences( $improving2, $allImproving1 );

		$improving1 = $this->sortByOccurences( $improving1 );
		$improving2 = $this->sortByOccurences( $improving2 );

		$improving1 = $this->groupByLength( $improving1 );
		$improving2 = $this->groupByLength( $improving2 );

		$improving1 = $this->getTop20( $improving1 );
		$improving2 = $this->getTop20( $improving2 );

		return array(
			$task1 => $improving1,
			$task2 => $improving2
		);
	}

	private function getConfirmed( $taskId ) {
		return $this->db
			->table( 'confirmed_ngrams' )
			->select( 'translations.sentences_id' )
			->select( 'confirmed_ngrams.text, confirmed_ngrams.length, confirmed_ngrams.position' )
			->where( 'translations.tasks_id', $taskId )
			->order( 'translations.sentences_id, confirmed_ngrams.text' );
	}

	public function getWorsening( $task1, $task2 ) {
		$task1Ngrams = $this->getUnconfirmed( $task1 );
		$task2Ngrams = $this->getUnconfirmed( $task2 );

		list( $allWorsening1, $allWorsening2 ) = $this->mergeNgrams( $task1Ngrams, $task2Ngrams );
		$worsening1 = $this->filterImproving( $allWorsening1 );
		$worsening2 = $this->filterImproving( $allWorsening2 );

		$worsening1 = $this->addOtherOccurences( $worsening1, $allWorsening2 );
		$worsening2 = $this->addOtherOccurences( $worsening2, $allWorsening1 );

		$worsening1 = $this->sortByOccurences( $worsening1 );
		$worsening2 = $this->sortByOccurences( $worsening2 );

		$worsening1 = $this->groupByLength( $worsening1 );
		$worsening2 = $this->groupByLength( $worsening2 );

		$worsening1 = $this->getTop20( $worsening1 );
		$worsening2 = $this->getTop20( $worsening2 );

		return array(
			$task1 => $worsening1,
			$task2 => $worsening2
		);
	}

	private function getUnconfirmed( $taskId ) {
		return $this->db
			->table( 'unconfirmed_ngrams' )
			->select( 'translations.sentences_id' )
			->select( 'unconfirmed_ngrams.text, unconfirmed_ngrams.length, unconfirmed_ngrams.position' )
			->where( 'translations.tasks_id', $taskId )
			->order( 'translations.sentences_id, unconfirmed_ngrams.text' );
	}

	private function mergeNgrams( $ngrams1, $ngrams2 ) {
		$merged1 = array();
		$merged2 = array();

		$ngrams1->rewind();
		$ngrams2->rewind();
		while( $ngrams1->valid() && $ngrams2->valid() ) {
			$ngram1 = $ngrams1->current();
			$ngram2 = $ngrams2->current();

			if ( !isset( $merged1[ $ngram1['text'] ] ) ) {
				$merged1[ $ngram1['text'] ] = array(
					'text' => $ngram1['text'],
					'sentences' => array(),
					'length' => $ngram1['length'],
					'all_occurences' => 0
				);
			}

			if ( !isset( $merged2[ $ngram2['text'] ] ) ) {
				$merged2[ $ngram2['text'] ] = array(
					'text' => $ngram2['text'],
					'sentences' => array(),
					'length' => $ngram2['length'],
					'all_occurences' => 0
				);
			}

			if ( $this->cmp( $ngram1, $ngram2 ) < 0 ) {
				$merged1[ $ngram1['text'] ][ 'sentences' ][] = $ngram1['sentences_id'];
				$merged1[ $ngram1['text'] ][ 'all_occurences' ]++;

				$ngrams1->next();
			} else if ( $this->cmp( $ngram1, $ngram2 ) > 0 ) {
				$merged2[ $ngram2['text'] ][ 'sentences' ][] = $ngram2['sentences_id'];
				$merged2[ $ngram2['text'] ][ 'all_occurences' ]++;

				$ngrams2->next();
			} else {
				$merged1[ $ngram1['text'] ][ 'all_occurences' ]++;
				$merged2[ $ngram2['text'] ][ 'all_occurences' ]++;

				$ngrams1->next();
				$ngrams2->next();
			}
		}

		while( $ngrams1->valid() ) {
			$ngram1 = $ngrams1->current();
			if ( !isset( $merged1[ $ngram1['text'] ] ) ) {
				$merged1[ $ngram1['text'] ] = array(
					'text' => $ngram1['text'],
					'sentences' => array(),
					'length' => $ngram1['length'],
					'all_occurences' => 0
				);
			}

			$merged1[ $ngram1['text'] ][ 'sentences' ][] = $ngram1['sentences_id'];
			$merged1[ $ngram1['text'] ][ 'all_occurences' ]++;

			$ngrams1->next();
		}

		while( $ngrams2->valid() ) {
			$ngram2 = $ngrams2->current();
			if ( !isset( $merged2[ $ngram2['text'] ] ) ) {
				$merged2[ $ngram2['text'] ] = array(
					'text' => $ngram2['text'],
					'sentences' => array(),
					'length' => $ngram2['length'],
					'all_occurences' => 0
				);
			}

			$merged2[ $ngram2['text'] ][ 'sentences' ][] = $ngram2['sentences_id'];
			$merged2[ $ngram2['text'] ][ 'all_occurences' ]++;

			$ngrams2->next();
		}

		return array( $merged1, $merged2 );
	}

	private function cmp( $ngram1, $ngram2 ) {
		if ( $ngram1[ 'sentences_id' ] == $ngram2[ 'sentences_id' ] ) {
			return strcmp( $ngram1[ 'text' ], $ngram2[ 'text' ] );
		} else {
			return $ngram1[ 'sentences_id' ] - $ngram2[ 'sentences_id' ];
		}
	}

	private function addOtherOccurences( $ngrams, $otherNgrams ) {
		foreach( $ngrams as $ngram => &$value ) {
			$value[ 'other_occurences' ] = (int) @$otherNgrams[ $value[ 'text' ] ][ 'all_occurences' ];
		}

		return $ngrams;
	}


	private function filterImproving( $ngrams ) {
		return array_filter( $ngrams, function( $ngram ) {
			return count( $ngram[ 'sentences' ] ) > 0;
		} );
	}

	private function sortByOccurences( $ngrams ) {
		usort( $ngrams, function( $a, $b ) {
			$diff = ( $b[ 'all_occurences' ] - $b[ 'other_occurences' ] ) - ( $a[ 'all_occurences' ] - $a[ 'other_occurences' ] );

			if( $diff != 0 ) {
				return $diff;
			} else {
				return count( $b[ 'sentences' ] ) - count( $a[ 'sentences' ] );
			}
		} );

		return $ngrams;
	}

	private function groupByLength( $ngrams ) {
		$grouped = array( 1 => array(), 2 => array(), 3 => array(), 4 => array() );
		foreach( $ngrams as $ngram ) {
			$grouped[ $ngram[ 'length' ] ][] = $ngram;
		}

		return $grouped;
	}

	private function getTop20( $ngramsByLength ) {
		return array_map( function( $ngrams ) {
			return array_slice( $ngrams, 0, 20 );
		}, $ngramsByLength );
	}

}
