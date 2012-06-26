<?php


class Sentences {

	private $connection;

	public function __construct( \Nette\Database\Connection $connection ) {
		$this->connection = $connection;
	}


	public function getSentencesForTask( $id ) {
		$sql = 'SELECT t.text AS tst, r.text AS ref, s.text AS src, t.diff_bleu AS bleu '
			. 'FROM translation_sentences AS t '
			. 'JOIN source_sentences AS s ON s.experiment_id = t.experiment_id AND s.position = t.position '
			. 'JOIN reference_sentences AS r ON r.experiment_id = t.experiment_id AND r.position = t.position '
			. 'WHERE t.task_id = ?';

		return $this->connection->query( $sql, $id );
	}


	public function getSentencesPairsForTasks( $id1, $id2 ) {
		$sql = 'SELECT t1.text AS tst1, t1.diff_bleu AS bleu1, t2.text AS tst2, t2.diff_bleu AS bleu2, r.text AS ref, s.text AS src '
			. 'FROM translation_sentences AS t1 '
			. 'JOIN source_sentences AS s ON s.experiment_id = t1.experiment_id AND s.position = t1.position '
			. 'JOIN reference_sentences AS r ON r.experiment_id = t1.experiment_id AND r.position = t1.position '
			. 'JOIN translation_sentences AS t2 ON t2.position = t1.position '
			. 'WHERE t1.task_id = ? AND t2.task_id = ?';

		return $this->connection->query( $sql, $id1, $id2 );
	}

}
