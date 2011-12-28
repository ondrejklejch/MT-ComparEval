CREATE TABLE experiments (
	id INTEGER NOT NULL,
	name TEXT NOT NULL,
	comment TEXT NULL,
	date DATETIME DEFAULT ( DATETIME( 'now', 'localtime' ) ),
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
	id INTEGER NOT NULL,
	experiment_id INTEGER NOT NULL,
	task_id INTEGER NOT NULL,
	position INTEGER NOT NULL,
	text TEXT NOT NULL,
	length INTEGER NOT NULL,
	diff_bleu REAL NULL,
	PRIMARY KEY ( id ),
	UNIQUE ( experiment_id, task_id, position ),
	FOREIGN KEY ( experiment_id ) REFERENCES experiments( id ) ON DELETE CASCADE,
	FOREIGN KEY ( task_id ) REFERENCES tasks( id ) ON DELETE CASCADE
);


CREATE TABLE source_sentences (
	id INTEGER NOT NULL,
	experiment_id INTEGER NOT NULL,
	position INTEGER NOT NULL,
	text TEXT NOT NULL,
	PRIMARY KEY ( id ),
	UNIQUE ( experiment_id, position ),
	FOREIGN KEY ( experiment_id ) REFERENCES experiments( id ) ON DELETE CASCADE
);


CREATE TABLE reference_sentences (
	id INTEGER NOT NULL,
	experiment_id INTEGER NOT NULL,
	position INTEGER NOT NULL,
	text TEXT NOT NULL,
	length INTEGER NOT NULL,
        PRIMARY KEY ( id ),
	UNIQUE ( experiment_id, position ),
	FOREIGN KEY ( experiment_id ) REFERENCES experiments( id ) ON DELETE CASCADE
);


CREATE TABLE translation_ngrams (
	id INTEGER NOT NULL,
	sentence_id INTEGER NOT NULL,
	position INTEGER NOT NULL,
	length INTEGER NOT NULL,
	text TEXT NOT NULL,
	PRIMARY KEY ( id ),
	UNIQUE ( sentence_id, position, length ),
	FOREIGN KEY ( sentence_id ) REFERENCES translation_sentences( id ) ON DELETE CASCADE
);


CREATE TABLE reference_ngrams (
	id INTEGER NOT NULL,
	sentence_id INTEGER NOT NULL,
	position INTEGER NOT NULL,
	length INTEGER NOT NULL,
	text TEXT NOT NULL,
	PRIMARY KEY ( id ),
	UNIQUE ( sentence_id, position, length ),
	FOREIGN KEY ( sentence_id ) REFERENCES reference_sentences( id ) ON DELETE CASCADE
);
