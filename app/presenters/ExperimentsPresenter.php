<?php


class ExperimentsPresenter extends BasePresenter {

	/** @var Experiments */
	private $experimentsModel;

	/** @var Tasks */
	private $tasksModel;


	public function startup() {
		parent::startup();

		$this->experimentsModel = $this->getService( 'experimentsModel' );
		$this->tasksModel = $this->getService( 'tasksModel' );
	}


	public function renderList() {
		$this->template->experiments = $this->experimentsModel->getExperiments();
	}


	public function renderDetail( $id ) {
		$this->template->experiment = $this->experimentsModel->getExperiment( $id );
		$this->template->tasks = $this->tasksModel->getTasksForExperiment( $id );
	}


	public function actionEdit( $id ) {
		$form = $this->getComponent( 'experimentForm' );
		unset( $form['source'] );
		unset( $form['reference'] );

		$experiment = $this->experimentsModel->getExperiment( $id );

		$form->setDefaults( $experiment );
	}


	public function actionDelete( $id ) {
		$experiment = $this->experimentsModel->getExperiment( $id );
		$this->experimentsModel->deleteExperiment( $id );
		$this->flashMessage( sprintf( "Experiment %s was successfully deleted.", $experiment['name'] ) );
		
		$this->redirect( 'list' );
	}


	protected function createComponentExperimentForm() {
		$form = new \Nette\Application\UI\Form( $this, 'experimentForm' );
		$form->addText( 'name', 'Name:' )
			->setRequired();
		$form->addTextarea( 'comment', 'Comment:' );
		$form->addUpload( 'source', 'Source text:' )
			->setRequired();
		$form->addUpload( 'reference', 'Reference translation:' )
			->setRequired();
		$form->addHidden( 'id' );
		$form->addSubmit( 'save', 'Save' );

		$form->onSuccess[] = callback( $this, 'saveExperiment' );

		return $form;
	}


	public function saveExperiment( \Nette\Application\UI\Form $form ) {
		$values = $form->getValues();
		if( $values['id'] == NULL ) {
			$this->experimentsModel->createExperiment( $values );
			$this->flashMessage( sprintf( "Experiment %s was successfully created.", $values['name'] ) ); 
		} else {
			$this->experimentsModel->updateExperiment( $values['id'], $values );
			$this->flashMessage( sprintf( "Experiment %s was successfully updated.", $values['name'] ) );
		}
		
		$this->redirect( 'list' );
	}

}
