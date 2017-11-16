$(function(){
	$(".nav-tabs a").each( function( index ) {
		$(this).click( function(e) {
			e.preventDefault();
			$(this).tab('show');
		} );
	} );
});
