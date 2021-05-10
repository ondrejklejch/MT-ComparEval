# MT-ComparEval
MT-ComparEval is a tool for comparison and evaluation of machine translations.
It allows users to compare translations according to several criteria, such as:
 - automatic metrics of machine translation quality computed either on whole documents or single sentences
 - quality comparison of single sentence translation by highlighting conﬁrmed, improving and worsening n-grams
 - summaries of the most improving and worsening n-grams for the whole document.

MT-ComparEval also plots a chart with absolute differences of metrics computed on single sentences
  and a chart with values obtained from paired bootstrap resampling.
MT-ComparEval is distributed under Apache 2.0 license with an exception of [Highcharts.js](http://www.highcharts.com/) library
  which is distributed under [Creative Commons Attribution-NonCommercial 3.0 License](http://creativecommons.org/licenses/by-nc/3.0/).

# Try it online before installing on your server

- http://wmt.ufal.cz: all systems from the [WMT](http://www.statmt.org/wmt19/) 2014–2019
- http://mt-compareval.ufal.cz: upload and analyze your translations

# Papers
When using MT-ComparEval please cite:
 - Ondřej Klejch, Eleftherios Avramidis, Aljoscha Burchardt, Martin Popel: [MT-ComparEval: Graphical evaluation interface for Machine Translation development](http://ufal.mff.cuni.cz/pbml/104/art-klejch-et-al.pdf). The Prague Bulletin of Mathematical Linguistics, No. 104, 2015, pp. 63–74.

For a user-focused show-case study explaining most of the features, see:
 - Roman Sudarikov, Martin Popel, Ondrej Bojar, Aljoscha Burchardt, Ondřej Klejch: [Using MT-ComparEval](http://www.cracking-the-language-barrier.eu/wp-content/uploads/Sudarikov-etal.pdf). LREC 2016 [MT-Eval Workshop](http://www.cracking-the-language-barrier.eu/mt-eval-workshop-2016/). See [slides](http://ufal.mff.cuni.cz/~popel/papers/2016_05_24_using_mt-compareval.pdf) and a [poster](http://ufal.mff.cuni.cz/~popel/papers/2016_lrec_tools-and-guidelines_poster.pdf).

# Installation

## Advanced Installation with additional Metrics

The default configuration shipped with the software includes only the basic BLEU-related metrics. If you are interested only for these metrics, proceed to the next chapter "Basic Installation".

There is the possibility to enable additional metrics. At the moment, the tool "Hjerson" is supported but it is optional. Its entries are commented out in the file `config.neon` , in folder `app\config`, after downloading the software. If you are interested in enabling Hjerson, you should edit this file and uncomment (remove the leading `#` from) the lines that contain the name of the metric. Then proceed to the installation as following.

## Basic Installation

### Ubuntu/Debian Linux

MT-ComparEval has been designed and tested on Ubuntu and Debian systems. Such an operating system is therefore suggested. 

In order to be able to run MT-ComparEval several dependencies have to be installed.
On Ubuntu 20.04, these dependencies can be installed with the following commands:
```
sudo apt install sqlite3 php7.4-cli php7.4-sqlite3 curl php7.4-mbstring php7.4-xml php7.4-curl
```

On Ubuntu 18.04 use:
```
sudo apt install sqlite3 php7.2-cli php7.2-sqlite3 curl php7.2-mbstring php7.2-xml php7.2-curl
```

On Ubuntu 16.04 use:
```
sudo apt install sqlite3 php7.0-cli php7.0-sqlite3 curl php7.0-mbstring php7.0-xml
```

On Ubuntu 14.04 use:
```
sudo apt-get install sqlite3 php5-cli php5-sqlite curl
```

Download or clone the application and set your commandline within the new directory. Then the application can be installed with the following command:
```
bash bin/install.sh
```
During the installation you may be asked to enter GitHub OAuth token.
Just follow the instructions (open the url in your browser, generate the token and enter it).

### Windows

 - Download and install the following prerequisites: [GIT](https://git-scm.com/download/win), [Composer](https://getcomposer.org/download), [GNU CoreUtils Complete package, except sources](http://gnuwin32.sourceforge.net/packages/coreutils.htm) in the default locations.
 - Download [Python 2.7.x](https://www.python.org/downloads/release/python-2714) and install in the Program Files folder, in a subfolder named Python27 (The Program Files folder is normally `C:\Program Files\` but it may vary depending on the language and the installation of your system.
 - Open the Windows Explorer. Browse to the Program Files folder and create a new subfolder `sqlite`. Download [SQLite Precompiled Binaries for Windows](https://sqlite.org/download.html) (probably 64bit, this  depends on your version of Windows) and the [SQLite Tools Bundle](https://sqlite.org/download.html). Extract the contents directly in the subfolder `sqlite` that you created previously, withouth creating or including any other subfolder. A permissions question will appear, this is normal.
 - Using the Windows Explorer, go to `C:\` and create a directory `tools`. Download [MT-Compareval](https://github.com/choko/MT-ComparEval/archive/master.zip), open the zip file and copy the folder MT-ComparEval-master into `C:\tools` directory. Rename the folder from `MT-ComparEval-master` to `MT-ComparEval`.
 - Using the Windows Explorer, go to `C:\tools\MT-ComparEval\bin\win` and double click on `install` or `install.bat`. This will perform the installation. During the installation you may be asked to enter GitHub OAuth token. Just follow the instructions (open the url in your browser, generate the token and enter it).
 - If you have chosen different paths in the above steps, you have to edit install.bat and watcher.bat to reflect the accurate entries.
 

# Running MT-ComparEval

## Start the program

### Linux
To start MT-ComparEval two processes have to be run:

 - `bin/server.sh` which starts the application server on the address [localhost:8080](http://localhost:8080)
  (you can can check/adapt `app/config/config.neon` first to set the main title, set of metrics etc. See the [default config](app/config/config.neon).)
 - `bin/watcher.sh` which monitors folder `data` for new experiments and tasks (the `data` folder must exist before you run `bin/watcher.sh`.)

### Windows
 - Open Windows Explorer and navigate to C:\tools\MT-ComparEval\bin\win. 
 - Double-click on `server` or `server.bat` to start the web server. Leave the window open
 - Double-click on `watcher` or `watcher.bat` to start the watcher. Leave this window open too
 - Optionally: right click on these programs, create shortcuts and move the shortcuts on your desktop or in any convenient menu entry.
 - While these two programs are running, navigate your browser to the address [localhost:8080](http://localhost:8080)


## Structure of the `data` folder
Folder `data` contains folders with *experiments* (e.g. `EN-CS-WMT15`), which contains subfolders with *tasks* for each experiment (e.g. `MOSES`). For example:
```
data/
├─ EN-CS-WMT15/
│  ├─ source.txt
│  ├─ reference.txt
│  ├─ CHIMERA/
│  │  └─ translation.txt
│  └─ NEURAL-MT/
│     └─ translation.txt
└─ EN-DE-WMT15/
   ├─ source.txt
   ├─ reference.txt
   ├─ MOSES/
   │  └─ translation.txt
   └─ NEURAL-MT/
      └─ translation.txt
```

Each folder corresponds to one experiment and it should contain the following files:
 - `source.txt` - a plain text file with sentences in source language (one sentence per line).
 - `reference.txt` - a plain text file with reference translations (in target language).
 - `config.neon` - (optionally) a configuration file with the following structure:
```
name: Name of the experiment
description: "Description of the experiment\n can be multiline"
source: source.txt
reference: reference.txt
```
See http://ne-on.org/ for the syntax of neon files.
The `source` and `reference` needs to be defined only if you you choose non-default file names (not `source.txt` and `reference.txt`).

Individual machine translations called *tasks* are then stored in subfolders with the following files:
- `translation.txt` - a plain text file with translated sentences
- `config.neon` - (optionally) a configuration file with the following structure:
```
name: Name of the task
description: Description of the task
translation: translation.txt
precompute_ngrams: true
```

## API to create experiments and tasks
A curl command to create an experiment:
```bash
curl -X POST -F "name=experiment name" -F "description=description" -F "source=@source.txt" -F "reference=@reference.txt" http://localhost:8080/api/experiments/upload
```

A curl command to create a task:
```bash
curl -X POST -F "name=task name" -F "description=description" -F "experiment_id=1" -F "translation=@translation.txt" http://localhost:8080/api/tasks/upload
```

For deleting experiments via API use `api/experiments/delete/<id>`.

## How to remove a task manually

* Retrieve the experiment id from frontend. When you open an experiment you can see the id in the URL.
* Stop watcher
* Remove task from folder `data/.../` (or if you want to reimport the task after watcher is restarted, delete the hidden files `.imported` and `.notimported`)
* Find out task id, e.g. `sqlite3 storage/database "SELECT id, name FROM tasks WHERE experiments_id=XYZ"`;
* Delete task: `sqlite3 storage/database "sqlite3 storage/database "DELETE FROM tasks WHERE id=ABC";"`
* Restart watcher
