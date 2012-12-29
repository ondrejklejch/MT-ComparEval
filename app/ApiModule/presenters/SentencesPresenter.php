<?php

namespace ApiModule;

class SentencesPresenter extends \Nette\Application\UI\Presenter {

	public function renderDefault( $offset = 0, $orderBy, $order ) {
		if( !file_exists( "sentences.json" ) ) {
			file_put_contents( "sentences.json", file_get_contents( 'http://sentences.apiary.io/sentences' ) );
		}

		$data = json_decode( file_get_contents( "sentences.json" ) );
		$data->offset = $offset;
		$data->sentences = array_map( function( $sentence ) use ( $offset ) {
			$sentence->sentence_id += $offset;
			return $sentence;
		}, $data->sentences );

		$this->sendResponse( new \Nette\Application\Responses\JsonResponse( $data ) );
	}

}
