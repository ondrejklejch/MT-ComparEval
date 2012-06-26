CREATE TABLE experiments (
	id INTEGER NOT NULL,
	name TEXT NOT NULL,
	comment TEXT NULL,
	date DATETIME DEFAULT ( DATETIME( 'now', 'localtime' ) ),
	state INTEGER DEFAULT 0,
	PRIMARY KEY ( id )
);


CREATE TABLE tasks (
	id INTEGER NOT NULL,
	experiment_id INTEGER NOT NULL,
	name TEXT NOT NULL,
	bleu REAL NULL,
	state INTEGER DEFAULT ( 0 ),
	comment TEXT NULL,
	date DATETIME DEFAULT ( DATETIME( 'now', 'localtime' ) ),
	PRIMARY KEY ( id ),
	FOREIGN KEY ( experiment_id ) REFERENCES experiments( id ) ON DELETE CASCADE
);


CREATE TABLE translation_sentences (
	task_id INTEGER NOT NULL,
	position INTEGER NOT NULL,
	text TEXT NOT NULL,
	length INTEGER NOT NULL,
	diff_bleu REAL DEFAULT 0,
	PRIMARY KEY ( task_id, position ),
	FOREIGN KEY ( task_id ) REFERENCES tasks( id ) ON DELETE CASCADE
);


CREATE TABLE source_sentences (
	experiment_id INTEGER NOT NULL,
	position INTEGER NOT NULL,
	text TEXT NOT NULL,
	length INTEGER NOT NULL,
	PRIMARY KEY ( experiment_id, position ),
	FOREIGN KEY ( experiment_id ) REFERENCES experiments( id ) ON DELETE CASCADE
);


CREATE TABLE reference_sentences (
	experiment_id INTEGER NOT NULL,
	position INTEGER NOT NULL,
	text TEXT NOT NULL,
	length INTEGER NOT NULL,
	PRIMARY KEY ( experiment_id, position ),
	FOREIGN KEY ( experiment_id ) REFERENCES experiments( id ) ON DELETE CASCADE
);


CREATE TABLE translation_ngrams (
	task_id INTEGER NOT NULL,
	sentence_id INTEGER NOT NULL,
	position INTEGER NOT NULL,
	length INTEGER NOT NULL,
	nth INTEGER NOT NULL,
	text TEXT NOT NULL,
	PRIMARY KEY ( task_id, sentence_id, position, length ),
	FOREIGN KEY ( task_id ) REFERENCES tasks( id ) ON DELETE CASCADE
);


CREATE TABLE reference_ngrams (
	experiment_id INTEGER NOT NULL,
	sentence_id INTEGER NOT NULL,
	position INTEGER NOT NULL,
	nth INTEGER NOT NULL,
	length INTEGER NOT NULL,
	text TEXT NOT NULL,
	PRIMARY KEY ( experiment_id, sentence_id, position, length ),
	FOREIGN KEY ( experiment_id ) REFERENCES experiments( id ) ON DELETE CASCADE
);
