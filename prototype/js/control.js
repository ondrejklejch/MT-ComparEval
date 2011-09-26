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
		toggleDiff($(this), ".tst1 ins, .tst1 del");
		
		return false;
	});

	$("#diff2-control").click(function() {
		toggleDiff($(this), ".tst2 ins, .tst2 del");
		
		return false;	
	});
	
	function toggleDiff($control, selector) {
		if( $control.children("span").text() == "Display" ) {
			$(selector).addClass("active");
			$control.children("span").text("Hide");
		} else {
			$(selector).removeClass("active");
			$control.children("span").text("Display");
		}
	}
	
	
	$(".display-diff").click(function() {
		$(this).closest("tr").find("ins, del").toggleClass("active");
	
		return false;
	});
});
