angular.module( 'MT-ComparEval', [] )
	.directive( 'chart', function() {
		return {
			restrict: 'E',
			template: '<div></div>',
			transclude:true,
			replace: true,

			link: function (scope, element, attrs) {
				//Update when charts data changes
				scope.$watch(function() { return attrs.value + attrs.type; }, function(value) {
					if(!attrs.value) return;

					var chartsDefaults = {
						chart: {
							renderTo: element[0],
							type: attrs.type || null,
							height: attrs.height || null,
							width: attrs.width || null,
						},
						plotOptions:{
							series: {
								events: {
									show: function(e){
										if (e.target._i >= 0){
											var index = e.target._i + 2;
											$('.tasksTable th').eq(index).show();
											$('.tasksTable td:nth-child(' + index + ')').show();
										}
									},
									hide: function(e){
										if (e.target._i >= 0){
											var index = e.target._i + 2;
											$('.tasksTable th').eq(index).hide();
											$('.tasksTable td:nth-child(' + index + ')').hide();
										}
									}
								}
							}
						}
					};

					// We need deep copy in order to NOT override original chart object.
					// This allows us to override chart data member and still the keep
					// our original renderTo will be the same
					var deepCopy = true;
					var newSettings = {};
					$.extend(deepCopy, newSettings, chartsDefaults, JSON.parse(attrs.value));
					var chart = new Highcharts.Chart(newSettings);
				});
			}
		}
	})
	.directive( 'whenScrolled', function() {
		return function(scope, elm, attr) {
			var raw = elm[0];

			$(window).bind('scroll', function() {
				var elementBottom = $(raw).offset().top + $(raw).height();
				var windowBottom = $(window).scrollTop() + $(window).height();

				if( elementBottom <= windowBottom ) {
					scope.$apply( attr.whenScrolled );
				}
			});
		};
	})
	.directive( 'collapseLong', function($compile) {
		return {
			restrict: 'A',
			scope: true,
			link: function(scope, element, attrs) {
				scope.collapsed = true;
				scope.toggleClass = "icon-plus";

				scope.toggle = function() {
					scope.collapsed = !scope.collapsed;
					scope.toggleClass = scope.collapsed ? "icon-plus" : "icon-minus";
				};

				attrs.$observe('collapseLongText', function(text) {
					var maxLength = scope.$eval(attrs.maxLength);

					if (text.length > maxLength) {
						var firstPart = String(text).substring(0, maxLength);
						var secondPart = String(text).substring(maxLength, text.length);

						console.log( firstPart, secondPart );

						var firstSpan = $compile('<span>' + firstPart + '</span>')(scope);
						var secondSpan = $compile('<span ng-show="!collapsed">' + secondPart + '</span>')(scope);
						var moreIndicatorSpan = $compile('<span ng-show="collapsed">... </span>')(scope);
						var toggleButton = $compile('<span class="collapse-text-toggle btn btn-mini" ng-click="toggle()"><i ng-class="toggleClass"></i></span>')(scope);

						element.empty();
						element.append(firstSpan);
						element.append(secondSpan);
						element.append(moreIndicatorSpan);
						element.append(toggleButton);
					}
					else {
						element.empty();
						element.append(text);
					}
				});
			}
		};
	});

