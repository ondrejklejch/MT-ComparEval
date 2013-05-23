CREATE TABLE "experiments" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "name" text NOT NULL,
  "url_key" text NOT NULL UNIQUE,
  "description" text NOT NULL
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
  "description" text NULL,
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

INSERT INTO "metrics" ("name") VALUES ("BLEU");
INSERT INTO "metrics" ("name") VALUES ("BLEU-cis");
INSERT INTO "metrics" ("name") VALUES ("PRECISION");
INSERT INTO "metrics" ("name") VALUES ("PRECISION-cis");
INSERT INTO "metrics" ("name") VALUES ("RECALL");
INSERT INTO "metrics" ("name") VALUES ("RECALL-cis");
INSERT INTO "metrics" ("name") VALUES ("F-MEASURE");
INSERT INTO "metrics" ("name") VALUES ("F-MEASURE-cis");


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

CREATE TRIGGER delete_tasks_in_experiment AFTER DELETE ON experiments
  FOR EACH ROW BEGIN
    DELETE FROM tasks WHERE tasks.experiments_id = OLD.id;
  END;

CREATE TRIGGER delete_sentences_in_experiment BEFORE DELETE ON experiments
  FOR EACH ROW BEGIN
    DELETE FROM sentences WHERE sentences.experiments_id = OLD.id;
  END;

CREATE TRIGGER delete_translations_in_task BEFORE DELETE ON tasks
  FOR EACH ROW BEGIN
    DELETE FROM translations WHERE translations.tasks_id = OLD.id;
  END;

CREATE TRIGGER delete_metrics_for_task BEFORE DELETE ON tasks
  FOR EACH ROW BEGIN
    DELETE FROM tasks_metrics WHERE tasks_metrics.tasks_id = OLD.id;
  END;

CREATE TRIGGER delete_metrics_samples_for_task BEFORE DELETE ON tasks
  FOR EACH ROW BEGIN
    DELETE FROM tasks_metrics_samples WHERE tasks_metrics_samples.tasks_id = OLD.id;
  END;

CREATE TRIGGER delete_metrics_for_translation BEFORE DELETE ON translations
  FOR EACH ROW BEGIN
    DELETE FROM translations_metrics WHERE translations_metrics.translations_id = OLD.id;
  END;

CREATE TRIGGER delete_confirmed_ngrams_for_translation BEFORE DELETE ON translations
  FOR EACH ROW BEGIN
    DELETE FROM confirmed_ngrams WHERE confirmed_ngrams.translations_id = OLD.id;
  END;

CREATE TRIGGER delete_unconfirmed_ngrams_for_translation BEFORE DELETE ON translations
  FOR EACH ROW BEGIN
    DELETE FROM unconfirmed_ngrams WHERE unconfirmed_ngrams.translations_id = OLD.id;
  END;

-- 
