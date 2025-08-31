/* global BWPCPP */
import '../scss/plugin.scss';

/**
 * Custom JS
 */

let currentRequest = null;

( function ( $ ) {
	'use strict';

	const textarea = $( '#comment' );

	const BWPCPP_Script = {
		init: function () {
			this.bwp_cpp_fetch_suggestion();
			this.bwp_cpp_accept_suggestion();
		},

		bwp_cpp_fetch_suggestion: function () {
			let typingTimer;
			const typingDelay = 500;
			const mirror = $( '#comment-mirror' );
			const ghost = $( '#ghost-suggestion' );

			const textarea = $( '#comment' );

			if ( $( '#comment-mirror' ).length === 0 ) {
				$( '.comment-form-comment' ).append(
					'<div class="ghost-comment-wrapper"><label>Comment:</label><div id="comment-mirror"></div></div>'
				);
			}

			textarea.on( 'input', function () {
				let showGhostSuggestion =
					$( '#comment-mirror' ).find( '.ghost' );
				clearTimeout( typingTimer );
				mirror.text( $( this ).val() );
				ghost.text( '' ).hide();
				showGhostSuggestion.text( '' );
				typingTimer = setTimeout( fetchSuggestion, typingDelay );
			} );
		},

		bwp_cpp_accept_suggestion: function () {
			textarea.on( 'keydown', function ( e ) {
				const ghostText = $( '#comment-mirror .ghost' ).text();
				const mirror = $( '#comment-mirror' );
				const ghost = $( '#ghost-suggestion' );

				if (
					( e.key === 'Tab' || e.key === 'ArrowRight' ) &&
					ghostText
				) {
					e.preventDefault();
					if ( ghostText ) {
						textarea.val( textarea.val().trim() + ghostText );
						mirror.text( textarea.val() );
					}
					ghost.text( '' ).hide();
				}
			} );
		},
	};

	$( document ).on( 'ready', function () {
		BWPCPP_Script.init();
	} );
} )( jQuery );

// Fetch suggestion from API
function fetchSuggestion() {
	const textarea = jQuery( '#comment' );
	let lastText = '';
	let suggestion = '';

	const text = textarea.val().trim();
	if ( text === '' || text === lastText ) {
		return;
	}

	lastText = text;

	let fragment = getCurrentSentence( text );

	if ( fragment === '' || typeof fragment === 'undefined' ) {
		return;
	}

	currentRequest = jQuery.ajax( {
		url: BWPCPP.ajax_url,
		method: 'POST',
		data: {
			action: 'bwp_cpp_autosuggest',
			prompt: fragment,
			security: BWPCPP.bwp_cpp_nonce,
		},
		beforeSend: function () {
			if ( currentRequest ) {
				currentRequest.abort();
			}
		},
		success: function ( response ) {
			if ( response.success && response.data ) {
				suggestion = response.data.suggestion.trim();
				showGhostSuggestion( fragment, suggestion, text );
			}
		},
		complete: function () {
			currentRequest = null; // Clear reference when done
		},
	} );
}

// Show inline ghost text (like Gmail)
function showGhostSuggestion( currentText, fullSuggestion, fullText ) {
	if ( ! fullSuggestion.includes( currentText ) ) {
		return;
	}

	const mirror = jQuery( '#comment-mirror' );
	const ghost = jQuery( '#ghost-suggestion' );

	const ghostText = fullSuggestion.substring( currentText.length );
	mirror.html( fullText + '<span class="ghost">' + ghostText + '</span>' );
	ghost.text( fullSuggestion ).show();
}

function getCurrentSentence( text ) {
	// Split text by sentence-ending punctuation.
	// const sentences = text.split(/(?<=[.?!])\s+/);
	const sentences = text
		.split( /(?<=[.?!])\s+/ ) // Split on punctuation + space
		.map( ( s ) => s.trim() )
		.filter( ( s ) => ! /[.?!]$/.test( s ) );

	// Return only the last part (new sentence being written).
	return sentences[ sentences.length - 1 ];
}
