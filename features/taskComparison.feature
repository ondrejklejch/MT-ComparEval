Feature: Task detail
	In order to be able to compare machine translations
	As a MT developer
	I need to be able to compare results of two machine translations

	Scenario: Sentences are shown
		Given there are two results for machine translations
		When I open page with comparison
		Then sentences should be shown
		And every sentence should have id, source, reference and 2 translations	
		And every translation should have text and metric
		And sentences should be sorted by id 
