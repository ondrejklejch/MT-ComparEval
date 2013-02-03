@tasksImport @import
Feature: Tasks background import
	In order to be able to automate tasks import
	As a MT developer
	I need to be able to copy task to its experiments folder and create new task from it

	Scenario: Tasks watcher is watching given folder
		Given there is a folder where I can upload tasks
		When I start tasks watcher
		Then tasks watcher should watch that folder

	Scenario: New tasks in imported experiments are found
		Given there is already imported experiment called "old-experiment"
		And tasks watcher is running
		When I upload task called "new-task" to "old-experiment"
		Then tasks watcher should find "new-task" in "old-experiment"
		
	Scenario: Tasks in unimported experiments are not found
		Given there is unimported experiment called "new-experiment"
		And tasks watcher is running
		When I upload task called "new-task" to "new-experiment"
		Then tasks watcher should not find "new-task" in "new-experiment"

	Scenario: Imported tasks are not imported again 
		Given there is already imported experiment called "old-experiment"
		And tasks watcher is running
		When there is already imported task called "old-task" in "old-experiment"
		Then tasks watcher should not find "old-task" in "old-experiment"

	Scenario: New task is imported only once
		Given there is already imported experiment called "old-experiment"
		And tasks watcher is running
		When I upload task called "new-task" to "old-experiment"
		Then task watcher should find "new-task" in "old-experiment" only once
