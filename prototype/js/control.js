$(document).ready(function() {
	$("#sort-control").click(function() {
		$(this).addClass( "processing" );
		
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
		$(this).removeClass( "processing" );

		return false;
	});

	$("#diff1-control").click(function() {
		$(this).addClass( "processing" );
		generateDiff( $(this), ".tst1" );
		toggleDiff($(this), ".tst1");
		$(this).removeClass( "processing" );
		
		return false;
	});

	$("#diff2-control").click(function() {
		$(this).addClass( "processing" );
		generateDiff( $(this), ".tst2" );
		toggleDiff($(this), ".tst2");
		$(this).removeClass( "processing" );
		
		return false;	
	});
	
	
	function generateDiff( $controler, selector ) {
		if( ! $controler.hasClass( "cached" ) ) { 
			$(selector).find("td.text").each( function( index, element ) {
				computeDiff( element );		
			});

			$controler.addClass( "cached" );
		}
	}
	
	
	function toggleDiff($control, selector) {
		if( ! $control.hasClass("active") ) {
			$(selector).addClass("active");
		} else {
			$(selector).removeClass("active");
		}
		
		$control.toggleClass("active");
	}
	
	
	$(".display-diff").click(function() {
		computeDiff( this );			
		$(this).closest("tr").toggleClass( "active" );
	
		return false;
	});
	
	
	function computeDiff( element ) {
		var $tr = $(element).closest("tr");
		
		if( !$tr.hasClass( "cached" ) ) {
			var $td = $tr.find("td.text");
			var ref = $(element).closest("table").find("tr.ref td.text").text();
			var tst = $td.text();
		
			$tr.addClass("cached");
			$td.html( diffString(ref, tst) );
		}
	}
});
