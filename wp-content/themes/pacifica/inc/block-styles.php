<?php
/**
 * Custom block styles & pattern-friendly variations.
 *
 * @package Pacifica
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register named block styles used across patterns. CSS for each lives in
 * assets/css/theme.css keyed by the generated `is-style-{name}` class.
 */
function pacifica_register_block_styles(): void {
	$styles = array(
		'core/button'    => array(
			array( 'name' => 'ghost', 'label' => __( 'Contorno', 'pacifica' ) ),
			array( 'name' => 'ink', 'label' => __( 'Tinta', 'pacifica' ) ),
			array( 'name' => 'link-underline', 'label' => __( 'Enlace subrayado', 'pacifica' ) ),
		),
		'core/image'     => array(
			array( 'name' => 'framed', 'label' => __( 'Enmarcada', 'pacifica' ) ),
			array( 'name' => 'arch', 'label' => __( 'Arco', 'pacifica' ) ),
			array( 'name' => 'duotone-clay', 'label' => __( 'Duotono arcilla', 'pacifica' ) ),
		),
		'core/group'     => array(
			array( 'name' => 'card', 'label' => __( 'Tarjeta', 'pacifica' ) ),
			array( 'name' => 'paper', 'label' => __( 'Papel', 'pacifica' ) ),
			array( 'name' => 'hairline', 'label' => __( 'Filete', 'pacifica' ) ),
		),
		'core/heading'   => array(
			array( 'name' => 'eyebrow', 'label' => __( 'Antetítulo', 'pacifica' ) ),
			array( 'name' => 'script-accent', 'label' => __( 'Acento cursiva', 'pacifica' ) ),
		),
		'core/list'      => array(
			array( 'name' => 'wheat-marker', 'label' => __( 'Viñeta trigo', 'pacifica' ) ),
			array( 'name' => 'checkmarks', 'label' => __( 'Palomitas', 'pacifica' ) ),
		),
		'core/separator' => array(
			array( 'name' => 'wheat', 'label' => __( 'Espiga', 'pacifica' ) ),
		),
		'core/quote'     => array(
			array( 'name' => 'testimonial', 'label' => __( 'Testimonio', 'pacifica' ) ),
		),
	);

	foreach ( $styles as $block => $variations ) {
		foreach ( $variations as $variation ) {
			register_block_style( $block, $variation );
		}
	}
}
add_action( 'init', 'pacifica_register_block_styles' );
