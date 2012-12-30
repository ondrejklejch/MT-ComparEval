Feature: Sentences browsing
	In order to effectively browsing results
	As a MT developer
	I need to be able to browse and sort sentences

	Scenario Outline: Sentences can be sorted 
		Given there are two results for machine translations
		When I open page with <view>
		And I sort sentences by <orderBy> in <order> order
		Then sentences should be shown
		And sentences should be sorted by <orderBy> in <order> order

		Examples:
			| view		| orderBy 	| order		| 
			| comparison	| diff metric 	| ascending	| 
			| comparison	| diff metric 	| descending	| 
			| result	| metric 	| ascending	| 
			| result	| metric 	| descending	| 

	Scenario Outline: Sentences sort can be can canceled
		Given there is a result for machine translation
		When I open page with <view>
		And I sort sentences by <orderBy> in <order> order
		And I cancel this sort
		Then sentences should be sorted by id in ascending order

		Examples:
			| view		| orderBy 	| order		| 
			| comparison	| diff metric 	| ascending	| 
			| comparison	| diff metric 	| descending	| 
			| result	| metric 	| ascending	| 
			| result	| metric 	| descending	| 

	Scenario Outline: Sentences can be resorted by another metrics
		Given there is a result for machine translation
		When I open page with <view>
		And I choose <metric1> metric
		And I sort sentences by <orderBy> in <order> order
		And I choose <metric2> metric
		Then every translation should have text and metric
		And sentences should be sorted by <orderBy> in <order> order
		And <metric2> metric should be active

		Examples:
			| view		| orderBy 	| order		| metric1	| metric2	| 
			| comparison	| diff metric 	| ascending	| bleu		| rand		| 
			| comparison	| diff metric 	| descending	| bleu		| rand		|
			| result	| metric 	| ascending	| bleu		| rand		|
			| result	| metric 	| descending	| bleu		| rand		|
			| comparison	| diff metric 	| ascending	| rand		| bleu		| 
			| comparison	| diff metric 	| descending	| rand		| bleu		|
			| result	| metric 	| ascending	| rand		| bleu		|
			| result	| metric 	| descending	| rand		| bleu		|

	Scenario Outline: Sentences are loaded dynamically
		Given there is a result for machine translation
		When I open page with <view>
		And part of the result is already shown
		And I scroll down
		Then more sentences should load
	 	And sentences should be unique
		
		Examples:
			| view		|
			| comparison	|
			| result	|

	Scenario Outline: Dynamic loading preserves order
		Given there is a result for machine translation
		When I open page with <view>
		And I sort sentences by <orderBy> in <order> order
		And part of the result is already shown
		And I scroll down
		Then more sentences should load
	 	And sentences should be unique
		And new sentences should have <comparator> <orderBy> than old sentences

		Examples:
			| view		| orderBy 	| order		| comparator	|
			| comparison	| diff metric 	| ascending	| bigger	|
			| comparison	| diff metric 	| descending	| smaller	|
			| result	| metric 	| ascending	| bigger	|
			| result	| metric 	| descending	| smaller	|
