<?php

use \Nette\Application\UI\Form;
use \Nette\Forms\Controls;


class ExperimentsPresenter extends BasePresenter {

	private $experimentsModel;
	private $tasksModel;

	public function __construct( Experiments $experimentsModel, Tasks $tasksModel ) {
		$this->experimentsModel = $experimentsModel;
		$this->tasksModel = $tasksModel;
	}

	public function renderList() {
		$this->template->experiments = $this->experimentsModel->getExperiments();
	}

	public function renderDownload() {
		$output = fopen( "php://output", "w" ) or die( "Can't open php://output" );
		header( "Content-Type:application/csv" );
		header( "Content-Disposition:attachment;filename=statistics.csv" );

		$metricNames = array();
		foreach( $this->experimentsModel->getExperiments() as $experiment ) {
			foreach( $this->tasksModel->getTasks( $experiment->id ) as $task ) {
				$row = array();
				$row[] = $experiment->name;
				$row[] = $task->name;
				$row[] = $task->description;

				$metrics = $this->tasksModel->getTaskMetrics( $task->id );
				if( !$metricNames ) {
					$metricNames = array_keys( $metrics );
				}

				foreach( $metricNames as $metricName ) {
					$row[] = $metrics[ $metricName ];
				}

				$data[] = $row;
			}
		}

		$header = array( "Experiment", "Task", "Description" );
		$header = array_merge( $header, $metricNames );
		fputcsv( $output, $header );

		foreach( $data as $row ) {
			fputcsv( $output, $row );
		}

		fclose( $output ) or die( "Can't close php://output" );
		$this->terminate();
	}

	public function actionEdit( $id ) {
		$data = $this->experimentsModel->getExperimentById( $id );
		$this->getComponent( 'editForm' )->setDefaults( $data );
	}

	public function saveEditForm( Form $form ) {
		$data = $form->getValues();
		$id = $data[ 'id' ];
		$name = $data[ 'name' ];
		$description = $data[ 'description' ];

		$this->experimentsModel->updateExperiment( $id, $name, $description );

		$this->flashMessage( 'Experiment was successfully updated.', 'alert-success' );
		$this->redirect( 'list' );
	}

	public function actionDelete( $id ) {
		$this->experimentsModel->deleteExperiment( $id );

		$this->redirect( 'list' );
	}

	protected function createComponentEditForm() {
		$form = new Form( $this, 'editForm' );
		$form->addText( 'name', 'Name' )
			->addRule( Form::FILLED, 'Please, fill in a name of the experiment.' );
		$form->addTextArea( 'description', 'Description' )
			->addRule( Form::FILLED, 'Please, fill in a description of the experiment.' );
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
