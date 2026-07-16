<?php
/**
 * Theme setup: supports, image sizes, nav, i18n.
 *
 * @package Pacifica
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register theme supports. Block themes get most defaults from theme.json;
 * this covers the runtime supports theme.json cannot express.
 */
function pacifica_setup(): void {
	load_theme_textdomain( 'pacifica', PACIFICA_THEME_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'custom-logo', array(
		'height'      => 96,
		'width'       => 320,
		'flex-height' => true,
		'flex-width'  => true,
	) );

	// Editor stylesheet so the canvas matches the front end.
	add_editor_style( array( 'assets/css/theme.css', 'assets/css/editor.css' ) );

	// Purposeful, art-directed crops for the bakery imagery pipeline.
	add_image_size( 'pacifica-hero', 2000, 1200, true );
	add_image_size( 'pacifica-card', 800, 800, true );      // square product/pattern cards
	add_image_size( 'pacifica-card-tall', 800, 1040, true ); // 3:4 editorial
	add_image_size( 'pacifica-wide', 1600, 900, true );      // 16:9 feature strips
	add_image_size( 'pacifica-thumb', 300, 300, true );
}
add_action( 'after_setup_theme', 'pacifica_setup' );

/**
 * Human-readable labels for the custom image sizes in the media UI.
 *
 * @param array<string,string> $sizes Registered sizes.
 * @return array<string,string>
 */
function pacifica_image_size_names( array $sizes ): array {
	return array_merge( $sizes, array(
		'pacifica-hero'      => __( 'Pacífica — Hero', 'pacifica' ),
		'pacifica-card'      => __( 'Pacífica — Card (1:1)', 'pacifica' ),
		'pacifica-card-tall' => __( 'Pacífica — Card (3:4)', 'pacifica' ),
		'pacifica-wide'      => __( 'Pacífica — Wide (16:9)', 'pacifica' ),
	) );
}
add_filter( 'image_size_names_choose', 'pacifica_image_size_names' );

/**
 * Add a modest set of body classes used by runtime CSS hooks.
 *
 * @param string[] $classes Body classes.
 * @return string[]
 */
function pacifica_body_classes( array $classes ): array {
	if ( is_front_page() ) {
		$classes[] = 'is-front-page';
	}
	if ( function_exists( 'is_woocommerce' ) && ( is_woocommerce() || is_cart() || is_checkout() ) ) {
		$classes[] = 'is-commerce';
	}
	return $classes;
}
add_filter( 'body_class', 'pacifica_body_classes' );

/**
 * Ship a small, safe set of preconnect hints (self-hosted fonts, so none external).
 * Kept as a hook point for the client if a CDN is later added.
 *
 * @param array<int,array<string,mixed>|string> $hints Resource hints.
 * @param string                                 $relation Relation type.
 * @return array<int,array<string,mixed>|string>
 */
function pacifica_resource_hints( array $hints, string $relation ): array {
	return $hints;
}
add_filter( 'wp_resource_hints', 'pacifica_resource_hints', 10, 2 );
