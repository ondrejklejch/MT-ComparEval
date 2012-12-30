Feature: Task detail
	In order to be able to evaluate machine translations
	As a MT developer
	I need to be able to view result for one machine translation

	Scenario: Sentences are shown
		Given there is a result for machine translation
		When I open page with result
		Then sentences should be shown
		And every sentence should have id, source, reference and 1 translation
		And every translation should have text and metric
		And sentences should be sorted by id in ascending order
