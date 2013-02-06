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
		And there is no task called "new-task" in "old-experiment"
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
		And there is no task called "new-task" in "old-experiment"
		When I upload task called "new-task" to "old-experiment"
		Then task watcher should find "new-task" in "old-experiment" only once

	Scenario: Watcher is using default paths without config
		Given there is already imported experiment called "old-experiment"
		And tasks watcher is running
		And there is no task called "new-task" in "old-experiment"
		When I upload task called "new-task" to "old-experiment"
		And I forget to upload "config.neon" for "old-experiment/new-task"
		Then tasks watcher should use "translation.txt" for "translation sentences" in "new-task"

	Scenario: Watcher is using paths provided in config.neon
		Given there is already imported experiment called "old-experiment"
		And tasks watcher is running
		And there is no task called "new-task" in "old-experiment"
		When I upload task called "new-task" to "old-experiment"
		And "old-experiment/new-task" has "config.neon" with contents:
		"""
		translation: config-translation.txt
		"""
		And "old-experiment/new-task" has "config-translation.txt" with contents:
		"""
		"""
		Then tasks watcher should use "config-translation.txt" for "translation sentences" in "new-task"

	Scenario: Watcher is using default paths if path is missing in config.neon
		Given there is already imported experiment called "old-experiment"
		And tasks watcher is running
		And there is no task called "new-task" in "old-experiment"
		When I upload task called "new-task" to "old-experiment"
		And "old-experiment/new-task" has "config.neon" with contents:
		"""
		"""
		Then tasks watcher should use "translation.txt" for "translation sentences" in "new-task"

	Scenario: Watcher is complaining about missing resources
		Given there is already imported experiment called "old-experiment"
		And tasks watcher is running
		And there is no task called "new-task" in "old-experiment"
		When I upload task called "new-task" to "old-experiment"
		And I forget to upload "translation.txt" for "old-experiment/new-task"
		Then tasks watcher should complain about missing "translation sentences" for "new-task"
		And tasks watcher should not parse "translation sentences" in "translation.txt" for "new-task"
		And tasks watcher should abort parsing of "new-task" 

	Scenario: Watcher will inform that is starting to parse resource
		Given there is already imported experiment called "old-experiment"
		And tasks watcher is running
		And there is no task called "new-task" in "old-experiment"
		When I upload task called "new-task" to "old-experiment"
		Then tasks watcher should parse "translation sentences" in "translation.txt" for "new-task"

	Scenario: Watcher can parse sentences from files
		Given there is already imported experiment called "old-experiment"
		And tasks watcher is running
		And there is no task called "new-task" in "old-experiment"
		When I upload task called "new-task" to "old-experiment"
		And "old-experiment/new-task" has "translation.txt" with contents:
		"""
		Line1
		Line2
		Line3
		"""
		Then tasks watcher should say that "new-task" has 3 "translation sentences"

	Scenario: Watcher asserts that has correct number of translation sentences
		Given there is already imported experiment called "old-experiment"
		And tasks watcher is running
		And there is no task called "new-task" in "old-experiment"
		When I upload task called "new-task" to "old-experiment"
		And "old-experiment/new-task" has "translation.txt" with contents:
		"""
		Line1
		Line2
		Line3
		"""
		And "old-experiment" has "reference.txt" with contents:
		"""
		Line1
		Line2
		"""
		Then tasks watcher should say that "new-task" has bad translations sentences count
		And tasks watcher should abort parsing of "new-task" 

	Scenario: Successfully uploaded task should appear in the tasks list
		Given there is already imported experiment called "old-experiment"
		And tasks watcher is running
		And there is no task called "new-task" in "old-experiment"
		When I upload task called "new-task" to "old-experiment"
		And task "new-task" is uploaded successfully
		And I open page with experiments list
		And I click on "tasks" link of "old-experiment"
		Then I should see "new-task" in the tasks list

	Scenario: Tasks can have custom names and descriptions
		Given there is already imported experiment called "old-experiment"
		And tasks watcher is running
		And there is no task called "new-task" in "old-experiment"
		When I upload task called "new-task" to "old-experiment"
		And "old-experiment/new-task" has "config.neon" with contents:
		"""
		name: My name
		description: My description 
		translation: translation.txt
		"""
		And task "new-task" is uploaded successfully
		And I open page with experiments list
		And I click on "tasks" link of "old-experiment"
		Then I should see "new-task" in the tasks list
		And task "new-task" should have "name" == "My name"
		And task "new-task" should have "description" == "My description"
