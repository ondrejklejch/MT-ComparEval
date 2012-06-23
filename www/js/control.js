$(document).ready(function() {
	$("#sort-control").click(function( event ) {
		event.preventDefault();
       		if( $(this).hasClass( "processing" ) ) {
			return;
		}

		var className = "." + $(this).attr( 'class' );
		$(this).addClass( "processing" );
				
		var callback;
		var newOrder;
		
		if($(this).children("span").text() == "DESC") {
			callback = function sortDescending(a, b) {
				var valueA = parseFloat($(a).find( className ).first().text());
				var valueB = parseFloat($(b).find( className ).first().text());
			
				return valueA > valueB ? -1 : 1;
			};
			newOrder = "ASC";
		} else {
			callback = function (a, b) {
				var valueA = parseFloat($(a).find( className ).first().text());
				var valueB = parseFloat($(b).find( className ).first().text());
			
				return valueA < valueB ? -1 : 1;
			};
			newOrder = "DESC";
		}
		
		$("ul#sentences li").sort(callback);
		$(this).children("span").text(newOrder);
		$(this).removeClass( "processing" );

		return false;
	});

	$("#diff-control").click(function() {
		$(this).addClass( "processing" );
		generateDiff( $(this), ".tst", ".ref" );
		toggleDiff($(this), ".tst");
		$(this).removeClass( "processing" );
		
		return false;
	});
	
	$("#diff1-control").click(function() {
		$(this).addClass( "processing" );
		generateDiff( $(this), ".tst1", ".tst2" );
		toggleDiff($(this), ".tst1");
		$(this).removeClass( "processing" );
		
		return false;
	});

	$("#diff2-control").click(function() {
		$(this).addClass( "processing" );
		generateDiff( $(this), ".tst2", ".tst1" );
		toggleDiff($(this), ".tst2");
		$(this).removeClass( "processing" );
		
		return false;
	});

	function generateDiff( $controler, sourceSelector, referenceSelector ) {
		if( !$controler.hasClass( "cached" ) ) { 
			$(sourceSelector).find("td.text").each( function( index, element ) {
				computeDiff( element, referenceSelector );		
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
	
	
	function computeDiff( element, referenceSelector ) {
		var $tr = $(element).closest("tr");
		
		if( !$tr.hasClass( "cached" ) ) {
			var $td = $tr.find("td.text");
			var $refNode = $(element).closest("table").find("tr" + referenceSelector + " td.text").clone();
			$refNode.children( "del" ).remove();
	
			var ref = $refNode.text();
			var tst = $td.text();
		
			$tr.addClass("cached");
			$td.html( diffString(ref, tst) );
		}
	}
});
