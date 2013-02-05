<?php


class ExperimentsPresenter extends BasePresenter {

	public function renderList() {
		$experimentsModel = $this->getService( 'experiments' );

		$this->template->experiments = $experimentsModel->getExperiments();
	}


	public function renderSentences( $id ) {
		$experimentsModel = $this->getService( 'experiments' );

		$this->template->sentences = $experimentsModel->getSentences( $id );
	}

}
