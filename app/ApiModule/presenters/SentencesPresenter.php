<?php

namespace ApiModule;

class SentencesPresenter extends \Nette\Application\UI\Presenter {

	public function renderDefault( array $taskIds, $offset = 0, $limit = 20, $orderBy = 'id', $order = 'asc' ) {
		$taskIds = array_values( $taskIds );
		$sentencesModel = $this->getService( 'sentences' );

		$response = array();
		$response['offset'] = $offset;
		$response['has_next'] = $sentencesModel->getSentencesCount( $taskIds ) > $offset+$limit;
		$response['sentences'] = $sentencesModel->getSentences( $taskIds, $offset, $limit, $orderBy, $order );

		$this->sendResponse( new \Nette\Application\Responses\JsonResponse( $response ) );
	}

}
