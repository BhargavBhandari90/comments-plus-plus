/* global BWPCPP, jQuery */

let currentRequest = null;

( function ( $ ) {
	'use strict';

	/**
	 * Fetch suggestion from API
	 * @return {void}
	 */
	function fetchSuggestion() {
		let lastText = '';
		let suggestion = '';

		const text = textarea.val().trim();
		if ( text === '' || text === lastText ) {
			return;
		}

		lastText = text;

		const fragment = getCurrentSentence( text );

		if ( fragment === '' || typeof fragment === 'undefined' ) {
			return;
		}

		currentRequest = $.ajax( {
			url: BWPCPP.ajax_url,
			method: 'POST',
			data: {
				action: 'bwp_cpp_autosuggest',
				prompt: fragment,
				security: BWPCPP.bwp_cpp_nonce,
			},
			beforeSend() {
				if ( currentRequest ) {
					currentRequest.abort();
				}
			},
			success( response ) {
				if ( response.success && response.data ) {
					suggestion = response.data.suggestion.trim();
					showGhostSuggestion( fragment, suggestion, text );
				}
			},
			complete() {
				currentRequest = null; // Clear reference when done
			},
		} );
	}

	/**
	 * Show ghost suggestion in the comment mirror.
	 *
	 * @param {string} currentText
	 * @param {string} fullSuggestion
	 * @param {string} fullText
	 * @return {void}
	 */
	function showGhostSuggestion( currentText, fullSuggestion, fullText ) {
		if ( ! fullSuggestion.includes( currentText ) ) {
			return;
		}

		const mirror = $( '#comment-mirror' );
		const ghost = $( '#ghost-suggestion' );

		const ghostText = fullSuggestion.substring( currentText.length );
		mirror.html(
			fullText + '<span class="ghost">' + ghostText + '</span>'
		);
		ghost.text( fullSuggestion ).show();
	}

	/**
	 * Get the current sentence being typed.
	 *
	 * @param {string} text
	 * @return {string} The current sentence being typed.
	 */
	function getCurrentSentence( text ) {
		const sentences = text
			.split( /(?<=[.?!])\s+/ )
			.map( ( s ) => s.trim() )
			.filter( ( s ) => ! /[.?!]$/.test( s ) );

		return sentences[ sentences.length - 1 ];
	}

	const textarea = $( '#comment' );

	const CommentsPlusPlusScript = {
		init() {
			this.bwp_cpp_fetch_suggestion();
			this.bwp_cpp_accept_suggestion();
		},

		bwp_cpp_fetch_suggestion() {
			let typingTimer;
			const typingDelay = 500;
			const mirror = $( '#comment-mirror' );
			const ghost = $( '#ghost-suggestion' );

			if ( $( '#comment-mirror' ).length === 0 ) {
				$( '.comment-form-comment' ).append(
					'<div class="ghost-comment-wrapper"><label>Comment:</label><div id="comment-mirror"></div></div>'
				);
			}

			textarea.on( 'input', function () {
				const ghostSuggestionEl =
					$( '#comment-mirror' ).find( '.ghost' );
				clearTimeout( typingTimer );
				mirror.text( $( this ).val() );
				ghost.text( '' ).hide();
				ghostSuggestionEl.text( '' );
				typingTimer = setTimeout( fetchSuggestion, typingDelay );
			} );
		},

		bwp_cpp_accept_suggestion() {
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
		CommentsPlusPlusScript.init();
	} );
} )( jQuery );
