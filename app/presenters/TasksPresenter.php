<?php

use \Nette\Application\UI\Form;
use \Nette\Forms\Controls;


class TasksPresenter extends BasePresenter {

	private $tasksModel;

	private $experimentsModel;

	private $metricsModel;

	public function __construct( Tasks $tasksModel, Experiments $experimentsModel, Metrics $metricsModel ) {
		$this->tasksModel = $tasksModel;
		$this->experimentsModel = $experimentsModel;
		$this->metricsModel = $metricsModel;
	}

	public function renderList( $experimentName ) {
		$this->template->experiment = $this->experimentsModel->getExperimentByName( $experimentName );
		$this->template->experimentId = $this->template->experiment->id;
	}

	public function renderDownloadPValues( $experimentId, $metricName ) {
		$output = fopen( "php://output", "w" ) or die( "Can't open php://output" );
		header( "Content-Type:application/csv" );
		header( "Content-Disposition:attachment;filename=p-values.csv" );

		$tasks = $this->tasksModel->getTasks( $experimentId )->order( "id DESC" )->fetchAll();
		$header = array("Name");
		foreach( $tasks as $task ) {
			$header[] = $task->name;
		}

		$metricId = $this->metricsModel->getMetricsId( $metricName );
		$data = array();
		foreach( $tasks as $task1 ) {
			$row = array( $task1->name );

			foreach( $tasks as $task2 ) {
				if( $task1->id <= $task2->id ) {
					$row[] = "-";
					continue;
				}

				$samples = $this->metricsModel->getMetricSamplesDiff( $metricId, $task1->id, $task2->id );
				$length = count( $samples );
				$positive = count(array_filter( $samples, function( $x ) { return $x > 0; } ) );

				$row[] = number_format( $positive / $length, 4 );
			}

			$data[] = $row;
		}

		fputcsv( $output, $header );
		foreach( $data as $row ) {
			fputcsv( $output, $row );
		}

		fclose( $output ) or die( "Can't close php://output" );
		$this->terminate();
	}

	public function renderCompare( $experimentName, $taskName1, $taskName2 ) {
		$this->template->experiment = $this->experimentsModel->getExperimentByName( $experimentName );
		$this->template->experimentId = $this->template->experiment->id;

		$id1 = $this->tasksModel->getTaskByName( $taskName1, $this->template->experimentId )->id;
		$id2 = $this->tasksModel->getTaskByName( $taskName2, $this->template->experimentId )->id;

		$this->template->taskIds = array( $id1, $id2 );
	}

	public function renderNew( $id ) {
		$this->template->experiment = $this->experimentsModel->getExperimentById( $id );
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
		$experimentName = $this->experimentsModel->getExperimentById( $experimentId )->url_key;

		$this->flashMessage( 'Task was successfully updated.', 'alert-success' );
		$this->redirect( 'list', $experimentName );
	}

	public function actionDelete( $taskId ) {
		$experimentId = $this->tasksModel->getTask( $taskId )->experiments_id;
		$experimentName = $this->experimentsModel->getExperimentById( $experimentId )->url_key;
		$this->tasksModel->deleteTask( $taskId );

		$this->redirect( 'list', $experimentName );
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
