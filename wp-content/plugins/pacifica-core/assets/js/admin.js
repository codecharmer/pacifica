/**
 * Pacífica — admin operations scripts.
 *
 * Dependency-free (ESNext). Powers:
 *  - Settings tab switching (no page reload).
 *  - Dashboard quick-actions (order status transitions) via admin-ajax + nonce.
 *  - "Enviar SMS de prueba" button via admin-ajax + nonce.
 *  - Repeatable field rows (staff numbers, blackout dates).
 *  - Confirm dialogs (quick actions, demo content install).
 *  - Print button (production calendar).
 *  - SEO image pickers via the WP media modal (progressive enhancement).
 */
( () => {
	'use strict';

	const cfg = window.pacificaAdmin || {};
	const strings = cfg.strings || {};

	/** Small helper: POST url-encoded form data to admin-ajax. */
	const postAjax = async ( action, data ) => {
		const body = new URLSearchParams( { action, ...data } );
		const res = await fetch( cfg.ajaxUrl, {
			method: 'POST',
			credentials: 'same-origin',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			body: body.toString(),
		} );
		return res.json();
	};

	/* --- Settings: tab switching ------------------------------------------- */
	const initTabs = () => {
		const tabs = document.querySelectorAll( '[data-pacifica-tab]' );
		if ( ! tabs.length ) {
			return;
		}
		const panels = document.querySelectorAll( '[data-pacifica-panel]' );
		const activate = ( key ) => {
			tabs.forEach( ( t ) =>
				t.classList.toggle( 'nav-tab-active', t.dataset.pacificaTab === key )
			);
			panels.forEach( ( p ) => {
				const match = p.dataset.pacificaPanel === key;
				p.hidden = ! match;
			} );
		};
		tabs.forEach( ( tab ) => {
			tab.addEventListener( 'click', ( e ) => {
				e.preventDefault();
				activate( tab.dataset.pacificaTab );
				const url = new URL( window.location.href );
				url.searchParams.set( 'tab', tab.dataset.pacificaTab );
				window.history.replaceState( {}, '', url );
			} );
		} );
	};

	/* --- Dashboard: quick actions ------------------------------------------ */
	const initQuickActions = () => {
		document.querySelectorAll( '.pacifica-quick-action' ).forEach( ( btn ) => {
			btn.addEventListener( 'click', async () => {
				if ( ! window.confirm( strings.confirmTransition || 'OK?' ) ) {
					return;
				}
				const { order, status } = btn.dataset;
				const row = document.getElementById( `pacifica-order-${ order }` );
				const buttons = row ? row.querySelectorAll( '.pacifica-quick-action' ) : [ btn ];
				buttons.forEach( ( b ) => b.classList.add( 'is-busy' ) );
				try {
					const json = await postAjax( 'pacifica_order_transition', {
						order_id: order,
						status,
						nonce: cfg.nonces?.transition || '',
					} );
					if ( json && json.success ) {
						const label = row?.querySelector( '[data-status-label]' );
						if ( label ) {
							label.textContent = json.data.label;
							label.className = `pacifica-status pacifica-status--${ json.data.status }`;
						}
					} else {
						window.alert( ( json && json.data && json.data.message ) || strings.error );
					}
				} catch ( err ) {
					window.alert( strings.error || 'Error' );
				} finally {
					buttons.forEach( ( b ) => b.classList.remove( 'is-busy' ) );
				}
			} );
		} );
	};

	/* --- Settings: test SMS ------------------------------------------------- */
	const initTestSms = () => {
		const btn = document.getElementById( 'pacifica-test-sms-btn' );
		if ( ! btn ) {
			return;
		}
		const out = document.querySelector( '.pacifica-test-sms-result' );
		const input = document.getElementById( 'pacifica-test-sms-to' );
		btn.addEventListener( 'click', async () => {
			const to = input ? input.value.trim() : '';
			if ( out ) {
				out.textContent = strings.sending || '';
				out.className = 'pacifica-test-sms-result';
			}
			btn.disabled = true;
			try {
				const json = await postAjax( 'pacifica_test_sms', {
					to,
					nonce: cfg.nonces?.testSms || '',
				} );
				const ok = json && json.success;
				if ( out ) {
					out.textContent = ok
						? json.data.message || strings.sent
						: ( json && json.data && json.data.message ) || strings.error;
					out.classList.add( ok ? 'is-success' : 'is-error' );
				}
			} catch ( err ) {
				if ( out ) {
					out.textContent = strings.error || 'Error';
					out.classList.add( 'is-error' );
				}
			} finally {
				btn.disabled = false;
			}
		} );
	};

	/* --- Settings: repeatable rows ----------------------------------------- */
	const initRepeatables = () => {
		document.querySelectorAll( '.pacifica-repeatable-add' ).forEach( ( addBtn ) => {
			addBtn.addEventListener( 'click', () => {
				const target = addBtn.dataset.target;
				const container = document.querySelector(
					`.pacifica-repeatable[data-repeatable="${ target }"]`
				);
				if ( ! container ) {
					return;
				}
				const last = container.querySelector( '.pacifica-repeatable__row' );
				if ( ! last ) {
					return;
				}
				const clone = last.cloneNode( true );
				const field = clone.querySelector( 'input' );
				if ( field ) {
					field.value = '';
				}
				container.appendChild( clone );
			} );
		} );

		// Event delegation for remove buttons (covers cloned rows too).
		document.addEventListener( 'click', ( e ) => {
			const remove = e.target.closest( '.pacifica-repeatable-remove' );
			if ( ! remove ) {
				return;
			}
			e.preventDefault();
			const container = remove.closest( '.pacifica-repeatable' );
			const rows = container
				? container.querySelectorAll( '.pacifica-repeatable__row' )
				: [];
			if ( rows.length > 1 ) {
				remove.closest( '.pacifica-repeatable__row' ).remove();
			} else {
				const field = remove
					.closest( '.pacifica-repeatable__row' )
					.querySelector( 'input' );
				if ( field ) {
					field.value = '';
				}
			}
		} );
	};

	/* --- Settings: demo content confirm ------------------------------------ */
	const initInstallConfirm = () => {
		document
			.querySelectorAll( '[data-pacifica-confirm-install]' )
			.forEach( ( btn ) => {
				btn.addEventListener( 'click', ( e ) => {
					if ( ! window.confirm( strings.confirmInstall || 'OK?' ) ) {
						e.preventDefault();
					}
				} );
			} );
	};

	/* --- Calendar: print --------------------------------------------------- */
	const initPrint = () => {
		document.querySelectorAll( '[data-pacifica-print]' ).forEach( ( btn ) => {
			btn.addEventListener( 'click', () => window.print() );
		} );
	};

	/* --- SEO: media picker ------------------------------------------------- */
	const initMedia = () => {
		const wp = window.wp;
		document.querySelectorAll( '[data-pacifica-media]' ).forEach( ( wrap ) => {
			const input = wrap.querySelector( '[data-pacifica-media-input]' );
			const preview = wrap.querySelector( '.pacifica-media__preview' );
			const selectBtn = wrap.querySelector( '.pacifica-media-select' );
			const removeBtn = wrap.querySelector( '.pacifica-media-remove' );
			let frame = null;

			if ( selectBtn && wp && wp.media ) {
				selectBtn.addEventListener( 'click', ( e ) => {
					e.preventDefault();
					if ( frame ) {
						frame.open();
						return;
					}
					frame = wp.media( {
						title: strings.selectImage,
						button: { text: strings.useImage },
						multiple: false,
					} );
					frame.on( 'select', () => {
						const att = frame.state().get( 'selection' ).first().toJSON();
						if ( input ) {
							input.value = att.id;
						}
						if ( preview ) {
							const size =
								att.sizes && att.sizes.thumbnail
									? att.sizes.thumbnail.url
									: att.url;
							preview.src = size;
							preview.hidden = false;
						}
						if ( removeBtn ) {
							removeBtn.hidden = false;
						}
					} );
					frame.open();
				} );
			}

			if ( removeBtn ) {
				removeBtn.addEventListener( 'click', ( e ) => {
					e.preventDefault();
					if ( input ) {
						input.value = '0';
					}
					if ( preview ) {
						preview.hidden = true;
						preview.src = '';
					}
					removeBtn.hidden = true;
				} );
			}
		} );
	};

	document.addEventListener( 'DOMContentLoaded', () => {
		initTabs();
		initQuickActions();
		initTestSms();
		initRepeatables();
		initInstallConfirm();
		initPrint();
		initMedia();
	} );
} )();
