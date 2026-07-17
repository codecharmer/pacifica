<?php
/**
 * Pacífica theme bootstrap.
 *
 * The theme is a thin, presentation-only layer. All commerce, ordering, SMS and
 * business-data logic lives in the pacifica-core plugin. This file only wires up
 * theme supports, assets, editor affordances and the block-binding sources that
 * let editors surface business data (address, hours, phone) without hardcoding.
 *
 * @package Pacifica
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'PACIFICA_THEME_VERSION', '1.0.0' );
define( 'PACIFICA_THEME_DIR', get_template_directory() );
define( 'PACIFICA_THEME_URI', get_template_directory_uri() );

/**
 * Load a theme include, failing loudly in debug only.
 */
require_once PACIFICA_THEME_DIR . '/inc/setup.php';
require_once PACIFICA_THEME_DIR . '/inc/assets.php';
require_once PACIFICA_THEME_DIR . '/inc/block-styles.php';
require_once PACIFICA_THEME_DIR . '/inc/patterns.php';
require_once PACIFICA_THEME_DIR . '/inc/block-bindings.php';
require_once PACIFICA_THEME_DIR . '/inc/quantity-stepper.php';
