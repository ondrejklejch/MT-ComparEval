Feature: Task detail
	In order to be able to compare machine translations
	As a MT developer
	I need to be able to compare results of two machine translations

	Scenario: Sentences are shown
		Given there are two results for machine translations
		When I open page with comparison
		Then sentences should be shown
		And every sentence should have id, source, reference and 2 translations	
		And every sentence should have diff metric
		And every translation should have text and metric
		And sentences should be sorted by id 

	Scenario: Sentences can be ordered by diff metric in descending order
		Given there are two results for machine translations
		When I open page with comparison
		And I sort sentences by diff metric in descending order
		Then sentences should be shown
		And sentences should be sorted by diff metric in descending order

	Scenario: Sentences can be ordered by diff metric in ascending order
		Given there are two results for machine translations
		When I open page with comparison
		And I sort sentences by diff metric in ascending order
		Then sentences should be shown
		And sentences should be sorted by diff metric in ascending order
	Scenario: Sentences sort can be can canceled
		Given there is a result for machine translation
		When I open page with comparison
		And I sort sentences by diff metric in ascending order
		And I cancel this sort
		Then sentences should be sorted by id 

	Scenario: Sentences can be sorted by various metrics
		Given there is a result for machine translation
		When I open page with comparison
		And I choose another metric
		And I sort sentences by diff metric in ascending order
		Then another metric should be active
		And every translation should have text and metric
		And sentences should be sorted by diff metric in ascending order
	Scenario: Sentences are loaded dynamically
		Given there is a result for machine translation
		When I open page with comparison
		And part of the result is already shown
		And I scroll down
		Then more sentences should load
	 	And sentences should be unique

	Scenario: Dynamic loading preserves ascending order
		Given there is a result for machine translation
		When I open page with comparison
		And I sort sentences by diff metric in ascending order
		And part of the result is already shown
		And I scroll down
		Then more sentences should load
	 	And sentences should be unique
		And new sentences should have bigger diff metric than old sentences

	Scenario: Dynamic loading preserves descending order
		Given there is a result for machine translation
		When I open page with comparison
		And I sort sentences by diff metric in descending order
		And part of the result is already shown
		And I scroll down
		Then more sentences should load
	 	And sentences should be unique
		And new sentences should have smaller diff metric than old sentences
