<?php
/**
 * Block binding source: pacifica/business.
 *
 * Lets any bindable block (paragraph, heading, button, image) surface live
 * business data — address, hours, phone, social — without hardcoding it in
 * templates or patterns. The data is owned by the pacifica-core plugin
 * (option `pacifica_business_info`); the theme reads it defensively with
 * sensible fallbacks so patterns still render before the plugin seeds data.
 *
 * Usage in block markup:
 *   <!-- wp:paragraph {"metadata":{"bindings":{"content":{
 *     "source":"pacifica/business","args":{"key":"address"}}}}} -->
 *
 * @package Pacifica
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Default business info. The plugin overrides these via the option; kept here so
 * the theme is never blank if the plugin is briefly inactive during setup.
 *
 * @return array<string,string>
 */
function pacifica_business_defaults(): array {
	return array(
		'name'          => 'Pacífica Panadería',
		'tagline'       => 'Artesanal no es una moda.',
		'phone'         => '+52 777 773 2179',
		'phone_link'    => '+527777732179',
		'whatsapp'      => 'https://wa.me/527777732179',
		'email'         => 'hola@pacifica.mx',
		'address'       => 'Tulipán 302, Col. Delicias, 62330 Cuernavaca, Morelos',
		'address_short' => 'Tulipán 302, Cuernavaca',
		'street'        => 'Tulipán 302',
		'locality'      => 'Cuernavaca',
		'region'        => 'Morelos',
		'postal_code'   => '62330',
		'country'       => 'MX',
		'hours_summary' => 'Miércoles a domingo, 9:00–15:00',
		'hours_closed'  => 'Cerrado lunes y martes',
		'instagram'     => 'https://www.instagram.com/pacifica.mx/',
		'instagram_handle' => '@pacifica.mx',
		'maps_url'      => 'https://maps.google.com/?q=Pac%C3%ADfica+Panader%C3%ADa+Cuernavaca',
		'latitude'      => '18.9186',
		'longitude'     => '-99.2342',
	);
}

/**
 * Resolve a single business-info value.
 */
function pacifica_business_value( string $key ): string {
	$stored   = get_option( 'pacifica_business_info', array() );
	$stored   = is_array( $stored ) ? $stored : array();
	$data     = array_merge( pacifica_business_defaults(), array_filter( $stored, 'is_scalar' ) );
	$value    = $data[ $key ] ?? '';
	return (string) $value;
}

/**
 * Register the binding source.
 */
function pacifica_register_business_binding(): void {
	if ( ! function_exists( 'register_block_bindings_source' ) ) {
		return; // WordPress < 6.5.
	}

	register_block_bindings_source(
		'pacifica/business',
		array(
			'label'              => __( 'Pacífica — Datos del negocio', 'pacifica' ),
			'get_value_callback' => static function ( array $source_args ): string {
				$key = isset( $source_args['key'] ) ? sanitize_key( (string) $source_args['key'] ) : '';
				if ( '' === $key ) {
					return '';
				}
				return pacifica_business_value( $key );
			},
			'uses_context'       => array(),
		)
	);
}
add_action( 'init', 'pacifica_register_business_binding' );
