<?php
/**
 * Plugin Name:       Pacífica Core
 * Plugin URI:        https://pacifica.mx/
 * Description:        Commerce, reserve-&-pickup ordering, Stripe, Twilio SMS workflow, SEO schema, and the admin operations dashboard for Pacífica Panadería. All business logic lives here — the theme stays presentation-only.
 * Version:           1.0.0
 * Requires at least: 6.8
 * Requires PHP:      8.3
 * Requires Plugins:  woocommerce
 * Author:            Pacífica Panadería — Agency Build
 * Author URI:        https://pacifica.mx/
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       pacifica-core
 * Domain Path:       /languages
 *
 * @package Pacifica\Core
 */

declare( strict_types=1 );

namespace Pacifica\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'PACIFICA_CORE_VERSION', '1.0.0' );
define( 'PACIFICA_CORE_FILE', __FILE__ );
define( 'PACIFICA_CORE_DIR', plugin_dir_path( __FILE__ ) );
define( 'PACIFICA_CORE_URL', plugin_dir_url( __FILE__ ) );
define( 'PACIFICA_CORE_MIN_PHP', '8.3' );

/**
 * Prefer Composer's autoloader; fall back to a lightweight PSR-4 loader so the
 * plugin runs on a plain deploy without a `composer install` step.
 */
if ( is_readable( PACIFICA_CORE_DIR . 'vendor/autoload.php' ) ) {
	require_once PACIFICA_CORE_DIR . 'vendor/autoload.php';
} else {
	require_once PACIFICA_CORE_DIR . 'src/Support/Autoloader.php';
	Support\Autoloader::register( 'Pacifica\\Core\\', PACIFICA_CORE_DIR . 'src/' );
}

/**
 * Hard requirement guard: bail with an admin notice on unsupported PHP.
 */
if ( version_compare( PHP_VERSION, PACIFICA_CORE_MIN_PHP, '<' ) ) {
	add_action( 'admin_notices', static function (): void {
		echo '<div class="notice notice-error"><p>';
		printf(
			/* translators: 1: required PHP version, 2: current PHP version */
			esc_html__( 'Pacífica Core requiere PHP %1$s o superior. Este servidor ejecuta %2$s.', 'pacifica-core' ),
			esc_html( PACIFICA_CORE_MIN_PHP ),
			esc_html( PHP_VERSION )
		);
		echo '</p></div>';
	} );
	return;
}

// Activation / deactivation lifecycle.
register_activation_hook( __FILE__, array( Setup\Activator::class, 'activate' ) );
register_deactivation_hook( __FILE__, array( Setup\Activator::class, 'deactivate' ) );

/**
 * Boot the plugin once WooCommerce and the rest of the plugin stack are ready.
 */
add_action( 'plugins_loaded', static function (): void {
	Plugin::instance()->boot();
}, 20 );

/**
 * Declare HPOS (High-Performance Order Storage) compatibility.
 */
add_action( 'before_woocommerce_init', static function (): void {
	if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', __FILE__, true );
	}
} );
