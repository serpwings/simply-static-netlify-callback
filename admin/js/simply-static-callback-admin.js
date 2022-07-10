(function( $ ) {
	'use strict';

	function getNextCallbackRequestHeadersIndex() {
		return (parseInt($( '#callback_request_headers tbody > tr:nth-last-child(2)' ).attr('index')) + 1);
	}

	$( document ).ready(function() {
		$( '#callback_request_headers .add' ).on('click', function(e) {
			e.preventDefault();
			var row = $('.cloneable').clone(true);
			var nextId = getNextCallbackRequestHeadersIndex();
			row.removeClass( 'cloneable' );
			row.attr( 'index', nextId );
			row.find('input').each(function(){
				$(this).attr('name', $(this).attr('name').replace('{{index}}', nextId));
				$(this).removeAttr('disabled');
			});
			row.insertBefore( $('#callback_request_headers .cloneable') );
			return false;
		});

		$( '#callback_request_headers .remove' ).on('click', function(e) {
			e.preventDefault();
			$(this).closest('tr').remove();
			return false;
		});
	});

})( jQuery );
