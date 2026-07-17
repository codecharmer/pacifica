<?php
/**
 * Quantity stepper for grid add-to-cart buttons (WooCommerce product-button block).
 *
 * Injects a − / count / + stepper inside the block wrapper so it inherits the
 * block's own Interactivity API context; the stepper only mutates that
 * context's `quantityToAdd`, leaving WooCommerce's add-to-cart action, button
 * animation and mini-cart badge untouched. Display binds straight to the
 * context, so no bespoke state is kept anywhere.
 *
 * @package Pacifica
 */

defined( 'ABSPATH' ) || exit;

/**
 * Enqueue the stepper's interactivity module wherever blocks render.
 */
function pacifica_qty_module(): void {
	if ( is_admin() || ! function_exists( 'wp_enqueue_script_module' ) ) {
		return;
	}
	wp_enqueue_script_module(
		'pacifica-qty',
		PACIFICA_THEME_URI . '/assets/js/quantity.js',
		array( '@wordpress/interactivity' ),
		pacifica_asset_version( 'assets/js/quantity.js' )
	);
}
add_action( 'wp_enqueue_scripts', 'pacifica_qty_module' );

/**
 * Inject the stepper markup into purchasable AJAX add-to-cart buttons.
 *
 * @param string $content Rendered block markup.
 * @return string
 */
function pacifica_qty_stepper( string $content ): string {
	// Only simple, in-stock, AJAX-purchasable buttons get a stepper; product
	// links ("Leer más", out of stock) pass through untouched.
	if ( false === strpos( $content, 'ajax_add_to_cart' ) || false === strpos( $content, 'data-wp-context' ) ) {
		return $content;
	}

	$stepper = sprintf(
		'<div class="pf-qty" data-wp-interactive="pacifica/qty">' .
			'<button type="button" class="pf-qty__btn" aria-label="%1$s" data-wp-on--click="actions.dec">&minus;</button>' .
			'<span class="pf-qty__n" aria-live="polite" data-wp-text="woocommerce/product-button::context.quantityToAdd">1</span>' .
			'<button type="button" class="pf-qty__btn" aria-label="%2$s" data-wp-on--click="actions.inc">+</button>' .
		'</div>',
		esc_attr__( 'Reducir cantidad', 'pacifica' ),
		esc_attr__( 'Aumentar cantidad', 'pacifica' )
	);

	// Place the stepper just before the button, inside the context-carrying wrapper.
	return preg_replace( '/<button\b/', $stepper . '<button', $content, 1 );
}
add_filter( 'render_block_woocommerce/product-button', 'pacifica_qty_stepper' );
