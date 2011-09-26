$(document).ready(function() {
	$("#sort-control").click(function() {
		var callback;
		var newOrder;
		
		if($(this).children("span").text() == "DESC") {
			callback = function sortDescending(a, b) {
				var valueA = parseFloat($(a).find(".diff-bleu").first().text());
				var valueB = parseFloat($(b).find(".diff-bleu").first().text());
			
				return valueA > valueB ? -1 : 1;
			};
			newOrder = "ASC";
		} else {
			callback = function (a, b) {
				var valueA = parseFloat($(a).find(".diff-bleu").first().text());
				var valueB = parseFloat($(b).find(".diff-bleu").first().text());
			
				return valueA < valueB ? -1 : 1;
			};
			newOrder = "DESC";
		}
		
		$("ul#sentences li").sort(callback);
		$(this).children("span").text(newOrder);

		return false;
	});

	$("#diff1-control").click(function() {
		toggleDiff($(this), ".tst1");
		
		return false;
	});

	$("#diff2-control").click(function() {
		toggleDiff($(this), ".tst2");
		
		return false;	
	});
	
	function toggleDiff($control, selector) {
		if( ! $control.hasClass("active") ) {
			$(selector).addClass("active");
		} else {
			$(selector).removeClass("active");
		}
		
		$control.toggleClass("active");
	}
	
	
	$(".display-diff").click(function() {
		$(this).closest("tr").toggleClass("active");
	
		return false;
	});
});
