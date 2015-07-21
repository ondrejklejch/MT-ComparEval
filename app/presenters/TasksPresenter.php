<?php

use \Nette\Application\UI\Form;
use \Nette\Forms\Controls;


class TasksPresenter extends BasePresenter {

	private $tasksModel;

	private $experimentsModel;

	public function __construct( Tasks $tasksModel, Experiments $experimentsModel ) {
		$this->tasksModel = $tasksModel;
		$this->experimentsModel = $experimentsModel;
	}

	public function renderList( $experimentId ) {
		$this->template->experimentId = $experimentId;
		$this->template->experiment = $this->experimentsModel->getExperimentById( $experimentId );
	}

	public function renderCompare( $id1, $id2 ) {
		$experimentId = $this->tasksModel->getTask( $id1 )->experiments_id;
		$this->template->experimentId = $experimentId;
		$this->template->experiment = $this->experimentsModel->getExperimentById( $experimentId );
		$this->template->taskIds = array( $id1, $id2 );
	}

	public function actionEdit( $id ) {
		$data = $this->tasksModel->getTaskById( $id );
		$this->getComponent( 'editForm' )->setDefaults( $data );
	}

	public function saveEditForm( Form $form ) {
		$data = $form->getValues();
		$id = $data[ 'id' ];
		$name = $data[ 'name' ];
		$description = $data[ 'description' ];

		$this->tasksModel->updateTask( $id, $name, $description );
		$experimentId = $this->tasksModel->getTask( $id )->experiments_id;

		$this->flashMessage( 'Task was successfully updated.', 'alert-success' );
		$this->redirect( 'list', $experimentId );
	}

	public function actionDelete( $taskId ) {
		$experimentId = $this->tasksModel->getTask( $taskId )->experiments_id;
		$this->tasksModel->deleteTask( $taskId );

		$this->redirect( 'list', $experimentId );
	}

	protected function createComponentEditForm() {
		$form = new Form( $this, 'editForm' );
		$form->addText( 'name', 'Name' )
			->addRule( Form::FILLED, 'Please, fill in a name of the task.' );
		$form->addTextArea( 'description', 'Description' )
			->addRule( Form::FILLED, 'Please, fill in a description of the task.' );
		$form->addHidden( 'id' );
		$form->addSubmit('save', 'Save');
		$form->onSubmit[] = array( $this, 'saveEditForm' );

		$this->setupRenderer( $form );

		return $form;
	}

	private function setupRenderer( $form ) {
		$renderer = $form->getRenderer();
		$renderer->wrappers[ 'controls' ][ 'container' ] = NULL;
		$renderer->wrappers[ 'pair' ][ 'container' ] = 'div class=control-group';
		$renderer->wrappers[ 'pair' ][ '.error' ] = 'error';
		$renderer->wrappers[ 'control' ][ 'container' ] = 'div class=controls';
		$renderer->wrappers[ 'label' ][ 'container' ] = 'div class=control-label';
		$renderer->wrappers[ 'control' ][ 'description' ] = 'span class=help-inline';
		$renderer->wrappers[ 'control' ][ 'errorcontainer' ] = 'span class=help-inline';
		$form->getElementPrototype()->class( 'form-horizontal' );

		foreach ($form->getControls() as $control) {
			if ( $control instanceof Controls\Button ) {
				$control->getControlPrototype()->addClass( empty( $usedPrimary ) ? 'btn btn-primary' : 'btn' );
				$usedPrimary = TRUE;
			} else if ( $control instanceof Controls\TextInput || $control instanceof Controls\TextArea ) {
				$control->getControlPrototype()->addClass( 'input-block-level' );
			}
		}

	}

}
