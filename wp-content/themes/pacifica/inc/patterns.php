<?php
/**
 * Block pattern categories.
 *
 * Patterns themselves are auto-registered from /patterns/*.php by WordPress.
 * Here we only declare the categories they slot into.
 *
 * @package Pacifica
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register Pacífica pattern categories.
 */
function pacifica_register_pattern_categories(): void {
	$categories = array(
		'pacifica-hero'      => __( 'Pacífica — Portadas', 'pacifica' ),
		'pacifica-page'      => __( 'Pacífica — Secciones de página', 'pacifica' ),
		'pacifica-commerce'  => __( 'Pacífica — Tienda', 'pacifica' ),
		'pacifica-cta'       => __( 'Pacífica — Llamados a la acción', 'pacifica' ),
		'pacifica-parts'     => __( 'Pacífica — Encabezado y pie', 'pacifica' ),
	);

	foreach ( $categories as $slug => $label ) {
		register_block_pattern_category( $slug, array( 'label' => $label ) );
	}
}
add_action( 'init', 'pacifica_register_pattern_categories' );
