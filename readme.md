# MT-ComparEval
MT-ComparEval is a tool for comparison and evaluation of machine translations.
It allows users to compare translations according to several criteria, such as:
 - automatic metrics of machine translation quality computed either on whole documents or single sentences
 - quality comparison of single sentence translation by highlighting conﬁrmed, improving and worsening n-grams
 - summaries of the most improving and worsening n-grams for the whole document.

MT-ComparEval also plots a chart with absolute diﬀerences of metrics computed on single sentences
  and a chart with values obtained from paired bootstrap resampling.


# Installation
In order to be able to run MT-ComparEval several dependencies have to be installed.
Namely, PHP version 5.4 and Sqlite 3.
On Ubuntu 14.04 these dependencies can be installed with the following commands.
```
sudo apt-get install sqlite3 php5-cli php5-sqlite
```

Then the application can be installed with the following command:
```
bash bin/install.sh
```

# Running MT-ComparEval
To start MT-ComparEval two processes have to be run.
`bin/server.sh` which starts the application server on the address [localhost:8080](http://localhost:8080)
  and `bin/watcher.sh` which monitors folder `data` for new experiments and tasks.

## Structure of Folder `data`
Folder `data` contains folders with experiments.
Each folder corresponds to one experiment and it should contain the following files:
 - `source.txt` - a plain text file with sentences in source language.
 - `reference.txt` - a plain text file with sentences in target language.
 - `config.neon` - a configuration file with the following structure:
```
name: Name of the experiment
description: Description of the experiment
source: source.txt
reference: reference.txt
```

Individual machine translations called Tasks are then stored in subfolders with the following files:
- `translation.txt` - a plain text file with translated sentences
- `config.neon` - a configuration file with the following structure:
```
name: Name of the task
description: Description of the task
translation: translation.txt
precompute_ngrams: true
```
