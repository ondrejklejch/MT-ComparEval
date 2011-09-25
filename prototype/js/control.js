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
	$(".tst1 ins").css({
		"display" : "inline",
		"background-color" : "red",	
	});
	$(".tst1 del").css({
		"display" : "inline",
		"background-color" : "blue",
	});
});


$("#diff1-control").click(function() {
	$(".tst2 ins").css({
		"display" : "inline",
		"background-color" : "red",	
	});
	$(".tst2 del").css({
		"display" : "inline",
		"background-color" : "blue",
	});
});
