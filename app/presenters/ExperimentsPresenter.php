<?php


class ExperimentsPresenter extends BasePresenter {

	private $experimentsModel;

	public function __construct( Experiments $experimentsModel ) {
		$this->experimentsModel = $experimentsModel;
	}

	public function renderList() {
		$this->template->experiments = $this->experimentsModel->getExperiments();
	}

	public function actionDelete( $id ) {
		$this->experimentsModel->deleteExperiment( $id );

		$this->redirect( 'list' );
	}

}
