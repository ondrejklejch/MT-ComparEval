@tasksImport @import
Feature: Tasks background import
	In order to be able to automate tasks import
	As a MT developer
	I need to be able to copy task to its experiments folder and create new task from it

	Scenario: Tasks watcher is watching given folder
		Given there is a folder where I can upload tasks
		When I start tasks watcher
		Then tasks watcher should watch that folder
