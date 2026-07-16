<?php
/**
 * Admin asset loader.
 *
 * Enqueues the Pacífica admin stylesheet + script ONLY on Pacífica operations
 * screens and the WooCommerce order screens where pickup meta is surfaced. Keeps
 * the rest of wp-admin untouched.
 *
 * @package Pacifica\Core
 */

declare( strict_types=1 );

namespace Pacifica\Core\Support;

use Pacifica\Core\Admin\Dashboard;
use Pacifica\Core\Contracts\Bootable;
use Pacifica\Core\Setup\Activator;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Assets implements Bootable {

	private const HANDLE = 'pacifica-admin';

	public function boot(): void {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
	}

	/**
	 * Resolve the shared parent menu slug without hard-coupling to the Admin module.
	 */
	private function parent_slug(): string {
		if ( class_exists( Dashboard::class ) && defined( Dashboard::class . '::SLUG' ) ) {
			return (string) Dashboard::SLUG;
		}
		return 'pacifica';
	}

	/**
	 * Whether the current admin screen is one we should style.
	 */
	private function is_pacifica_screen( string $hook ): bool {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		$id     = $screen ? (string) $screen->id : $hook;

		// Any Pacífica menu/submenu page (all slugs are prefixed with the parent slug).
		if ( false !== strpos( $id, $this->parent_slug() ) || false !== strpos( $hook, $this->parent_slug() ) ) {
			return true;
		}

		// WooCommerce order screens (HPOS list/edit + legacy post-type screens).
		$woo_screens = array( 'woocommerce_page_wc-orders', 'shop_order', 'edit-shop_order' );
		return in_array( $id, $woo_screens, true );
	}

	/**
	 * Whether the current screen is the Ajustes settings page (needs the media picker).
	 */
	private function is_settings_screen( string $hook ): bool {
		return false !== strpos( $hook, $this->parent_slug() . '-ajustes' );
	}

	public function enqueue( string $hook ): void {
		if ( ! $this->is_pacifica_screen( $hook ) ) {
			return;
		}

		$ver = defined( 'PACIFICA_CORE_VERSION' ) ? PACIFICA_CORE_VERSION : '1.0.0';
		$url = defined( 'PACIFICA_CORE_URL' ) ? PACIFICA_CORE_URL : plugin_dir_url( dirname( __DIR__, 2 ) . '/pacifica-core.php' );

		wp_enqueue_style( self::HANDLE, $url . 'assets/css/admin.css', array(), $ver );
		wp_enqueue_script( self::HANDLE, $url . 'assets/js/admin.js', array(), $ver, true );

		// The Settings SEO tab uses the WP media modal for image pickers.
		if ( $this->is_settings_screen( $hook ) && function_exists( 'wp_enqueue_media' ) ) {
			wp_enqueue_media();
		}

		wp_localize_script(
			self::HANDLE,
			'pacificaAdmin',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonces'  => array(
					'transition' => wp_create_nonce( 'pacifica_order_transition' ),
					'testSms'    => wp_create_nonce( 'pacifica_test_sms' ),
				),
				'caps'    => array(
					'manage' => current_user_can( Activator::CAP ),
				),
				'strings' => array(
					'confirmTransition' => __( '¿Actualizar el estado de este pedido?', 'pacifica-core' ),
					'working'           => __( 'Actualizando…', 'pacifica-core' ),
					'sending'           => __( 'Enviando…', 'pacifica-core' ),
					'sent'              => __( 'Mensaje de prueba enviado.', 'pacifica-core' ),
					'error'             => __( 'Ocurrió un error. Inténtalo de nuevo.', 'pacifica-core' ),
					'confirmInstall'    => __( '¿Instalar el contenido de demostración? Esto puede crear productos y páginas.', 'pacifica-core' ),
					'selectImage'       => __( 'Seleccionar imagen', 'pacifica-core' ),
					'useImage'          => __( 'Usar esta imagen', 'pacifica-core' ),
					'remove'            => __( 'Quitar', 'pacifica-core' ),
				),
			)
		);
	}
}
