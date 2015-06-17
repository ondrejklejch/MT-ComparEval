<?php

namespace ApiModule;

/**
 * SentencesPresenter is used for browsing sentences in REST API
 */
class SentencesPresenter extends \Nette\Application\UI\Presenter {

	private $sentencesModel;

	public function __construct( \Sentences $sentencesModel ) {
		$this->sentencesModel = $sentencesModel;
	}

	public function renderDefault( array $taskIds, $offset = 0, $limit = 20, $orderBy = 'id', $order = 'asc' ) {
		$taskIds = array_values( $taskIds );

		$response = array();
		$response['offset'] = $offset;
		$response['has_next'] = $this->sentencesModel->getSentencesCount( $taskIds ) > $offset+$limit;
		$response['sentences'] = $this->sentencesModel->getSentences( $taskIds, $offset, $limit, $orderBy, $order );

		$this->sendResponse( new \Nette\Application\Responses\JsonResponse( $response ) );
	}

	public function renderById( array $taskIds, $sentences, $offset = 0, $limit = 20 ) {
		$taskIds = array_values( $taskIds );
		$sentencesIds = explode( ',', $sentences );

		$response = array();
		$response['offset'] = $offset;
		$response['has_next'] = count( array_unique( $sentencesIds ) ) > $offset + $limit;
		$response['sentences'] = $this->sentencesModel->getSentencesByIds( $sentencesIds, $taskIds, $offset, $limit );

		$this->sendResponse( new \Nette\Application\Responses\JsonResponse( $response ) );
	}

}
