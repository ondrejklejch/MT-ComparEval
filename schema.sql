CREATE TABLE "experiments" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "name" text NOT NULL,
  "url_key" text NOT NULL UNIQUE,
  "description" text NOT NULL,
  "visible" integer(0) NULL
);


CREATE TABLE "sentences" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "experiments_id" integer NOT NULL,
  "source" text NOT NULL,
  "reference" text NOT NULL,
  FOREIGN KEY ("experiments_id") REFERENCES "experiments" ("id") ON DELETE CASCADE
);


CREATE TABLE "tasks" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "experiments_id" integer NOT NULL,
  "name" text NOT NULL,
  "url_key" text NOT NULL,
  "description" text NULL, "visible" integer(0) NULL,
  FOREIGN KEY ("experiments_id") REFERENCES "experiments" ("id") ON DELETE CASCADE,
  UNIQUE("experiments_id","url_key")
);


CREATE TABLE "translations" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "tasks_id" integer NOT NULL,
  "sentences_id" integer NOT NULL,
  "text" text NOT NULL,
  FOREIGN KEY ("tasks_id") REFERENCES "tasks" ("id") ON DELETE CASCADE,
  FOREIGN KEY ("sentences_id") REFERENCES "sentences" ("id") ON DELETE CASCADE
);


CREATE TABLE "metrics" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "name" text NOT NULL
);

INSERT INTO "metrics" ("id", "name") VALUES (1,	'BLEU');
INSERT INTO "metrics" ("id", "name") VALUES (2,	'BLEU-cis');
INSERT INTO "metrics" ("id", "name") VALUES (3,	'PRECISION');
INSERT INTO "metrics" ("id", "name") VALUES (4,	'PRECISION-cis');
INSERT INTO "metrics" ("id", "name") VALUES (5,	'RECALL');
INSERT INTO "metrics" ("id", "name") VALUES (6,	'RECALL-cis');
INSERT INTO "metrics" ("id", "name") VALUES (7,	'F-MEASURE');
INSERT INTO "metrics" ("id", "name") VALUES (8,	'F-MEASURE-cis');

CREATE TABLE "translations_metrics" (
  "translations_id" integer NOT NULL,
  "metrics_id" integer NOT NULL,
  "score" real NOT NULL,
  FOREIGN KEY ("translations_id") REFERENCES "translations" ("id") ON DELETE CASCADE,
  FOREIGN KEY ("metrics_id") REFERENCES "metrics" ("id") ON DELETE CASCADE
);


CREATE TABLE "tasks_metrics" (
  "tasks_id" integer NOT NULL,
  "metrics_id" integer NOT NULL,
  "score" real NOT NULL,
  FOREIGN KEY ("tasks_id") REFERENCES "tasks" ("id") ON DELETE CASCADE,
  FOREIGN KEY ("metrics_id") REFERENCES "metrics" ("id") ON DELETE CASCADE
);


CREATE TABLE "tasks_metrics_samples" (
  "tasks_id" integer NOT NULL,
  "metrics_id" integer NOT NULL,
  "sample_position" integer NOT NULL,
  "score" real NOT NULL,
  FOREIGN KEY ("tasks_id") REFERENCES "tasks" ("id") ON DELETE CASCADE,
  FOREIGN KEY ("metrics_id") REFERENCES "metrics" ("id") ON DELETE CASCADE
);


CREATE TABLE "confirmed_ngrams" (
  "translations_id" integer NOT NULL,
  "text" text NOT NULL,
  "length" integer NOT NULL,
  "position" integer NOT NULL,
  FOREIGN KEY ("translations_id") REFERENCES "translations" ("id") ON DELETE CASCADE
);


CREATE TABLE "unconfirmed_ngrams" (
  "translations_id" integer NOT NULL,
  "text" text NOT NULL,
  "length" integer NOT NULL,
  "position" integer NOT NULL,
  FOREIGN KEY ("translations_id") REFERENCES "translations" ("id") ON DELETE CASCADE
);
