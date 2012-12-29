Feature: Task detail
	In order to be able to compare machine translations
	As a MT developer
	I need to be able to view result for one machine translation

	Scenario: Sentences are shown
		Given there is a result for machine translation
		When I open page with result
		Then sentences should be shown
		And every sentence should have id, source, reference and translations
		And every translation should have text and metric
		And sentences should be sorted by id 

	Scenario: Sentences can be ordered by metric in descending order
		Given there is a result for machine translation
		When I open page with result
		And I sort sentences by metric in descending order
		Then sentences should be sorted in descending order

	Scenario: Sentences can be ordered by metric in ascending order
		Given there is a result for machine translation
		When I open page with result
		And I sort sentences by metric in ascending order
		Then sentences should be sorted in ascending order

	Scenario: Sentences sort can be can canceled
		Given there is a result for machine translation
		When I open page with result
		And I sort sentences by metric in ascending order
		And I cancel this sort
		Then sentences should be sorted by id 

	Scenario: Sentences can be sorted by various metrics
		Given there is a result for machine translation
		When I open page with result
		And I choose another metric
		And I sort sentences by metric in ascending order
		Then another metric should be active
		And every translation should have text and metric
		And sentences should be sorted in ascending order

	Scenario: Sentences are loaded dynamically
		Given there is a result for machine translation
		When I open page with result
		And part of the result is already shown
		And I scroll down
		Then more sentences should load
	 	And sentences should be unique

	Scenario: Dynamic loading preserves ascending order
		Given there is a result for machine translation
		When I open page with result
		And I sort sentences by metric in ascending order
		And part of the result is already shown
		And I scroll down
		Then more sentences should load
	 	And sentences should be unique
		And new sentences should have bigger score than old sentences

	Scenario: Dynamic loading preserves descending order
		Given there is a result for machine translation
		When I open page with result
		And I sort sentences by metric in descending order
		And part of the result is already shown
		And I scroll down
		Then more sentences should load
	 	And sentences should be unique
		And new sentences should have smaller score than old sentences
