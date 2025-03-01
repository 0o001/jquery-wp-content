$( function() {
	// Store modal templates
	var modalTemplate = $( "#sri-modal-template" )[ 0 ].outerHTML,
		$modal = $( "<div>" ).attr( "title", "Code Integration" );

	// Show modal on click
	$( "body" ).on( "click", ".open-sri-modal", function( event ) {
		if ( event.ctrlKey || event.metaKey ) {
			return;
		}
		var link = this;

		$modal
			.html( replace( modalTemplate, {
				link: link.href,
				hash: $( link ).attr( "data-hash" )
			} ) )
			.appendTo( "body" )
			.dialog( {
				modal: true,
				resizable: false,
				dialogClass: "sri-modal",
				draggable: false,
				close: function() {
					$( this ).remove();
				}
			} );

		$('.sri-modal-copy-btn')
			.tooltip()
			.on( "click", function() {
				var buttonElem = $( this );
				clipboard
					.writeText( buttonElem.attr( "data-clipboard-text" ) )
					.then( function() {
						buttonElem
							.tooltip( "option", "content", "Copied!" )
							.one( "mouseout", function() {
								buttonElem.tooltip( "option", "content", "Copy to clipboard!" );
							} );
					} )
					.catch( function() {
						buttonElem
							.tooltip( "option", "content", "Copying to clipboard failed!" )
							.one( "mouseout", function() {
								buttonElem.tooltip( "option", "content", "Copy to clipboard!" );
							} );
					} );
			} );
		event.preventDefault();
	} );

	// Helper functions
	function replace ( string, values ) {
		return string.replace( /\{\{([^}]+)}}/g, function( _, key ) {
			return values[key];
		} );
	}
} );
