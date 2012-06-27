<?php


class Experiments {

	private $table;

	public function __construct( \Nette\Database\Table\Selection $table ) {
		$this->table = $table;
	}


	public function getExperiments() {
		return $this->table->order( 'date DESC' );
	}

	
	public function getExperiment( $id ) {
		return $this->table->where( 'id', $id )->fetch();
	}

	
	public function createExperiment( $values ) {
		$experiment = $this->table->insert( $this->prepareExperiment( $values ) );
	
		$this->processSource( $experiment->getPrimary(), $values['source'] );
		$this->processReference( $experiment->getPrimary(), $values['reference'] );
	}

	
	private function processSource( $id, $sourceFile ) {
		$script = 'scripts/source/process_source.sh';
		$database = 'app/database';		
		$file = $sourceFile->getPathname();

		`sh $script $file $id $database`;
	}


	private function processReference( $id, $referenceFile ) {
		$script = 'scripts/reference/process_reference.sh';
		$database = 'app/database';		
		$file = $referenceFile->getPathname();

		`sh $script $file $id $database`;
	}


	public function updateExperiment( $id, $values ) {
		$this->table->where( 'id', $id )->update( $this->prepareExperiment( $values ) );
	}


	private function prepareExperiment( $values ) {
		return array(
			'name' => $values[ 'name' ],
			'comment' => $values[ 'comment' ],
		);		
	}


	public function deleteExperiment( $id ) {
		$this->table->where( 'id', $id )->delete();
	}

}
