<?php
/**
 * Plugin container & bootstrapper.
 *
 * Instantiates and boots each Bootable service. Services are declared in one
 * ordered list; missing classes are skipped (and surfaced in debug logs) so the
 * plugin degrades gracefully rather than fataling if a module is absent.
 *
 * @package Pacifica\Core
 */

declare( strict_types=1 );

namespace Pacifica\Core;

use Pacifica\Core\Contracts\Bootable;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Plugin {

	private static ?Plugin $instance = null;

	/** @var array<string,object> Resolved service instances, keyed by class name. */
	private array $services = array();

	private bool $booted = false;

	public static function instance(): Plugin {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {}

	/**
	 * The ordered service map. Order matters: Options first (config), then domain
	 * services, then admin/presentation.
	 *
	 * @return class-string[]
	 */
	private function service_classes(): array {
		return array(
			// Configuration & settings.
			Setup\Options::class,
			Setup\Settings::class,
			Support\Assets::class,

			// WooCommerce foundation.
			Woo\Support::class,
			Woo\Inventory::class,

			// Reserve & pickup ordering.
			Ordering\PickupScheduler::class,
			Ordering\OrderMeta::class,

			// Twilio SMS workflow.
			Sms\Logger::class,
			Sms\OrderNotifications::class,
			Sms\InboundController::class,

			// SEO.
			Seo\MetaTags::class,
			Seo\SchemaGraph::class,

			// REST + CLI.
			Rest\Routes::class,
			Cli\Commands::class,

			// Admin operations.
			Admin\Dashboard::class,
			Admin\ProductionCalendar::class,
			Admin\Reports::class,
		);
	}

	/**
	 * Boot all available services exactly once.
	 */
	public function boot(): void {
		if ( $this->booted ) {
			return;
		}
		$this->booted = true;

		load_plugin_textdomain( 'pacifica-core', false, dirname( plugin_basename( PACIFICA_CORE_FILE ) ) . '/languages' );

		foreach ( $this->service_classes() as $class ) {
			if ( ! class_exists( $class ) ) {
				if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
					error_log( sprintf( '[pacifica-core] Service not found (skipped): %s', $class ) );
				}
				continue;
			}
			$service = new $class();
			if ( $service instanceof Bootable ) {
				$service->boot();
			}
			$this->services[ $class ] = $service;
		}

		/**
		 * Fires after all core services have booted.
		 *
		 * @param Plugin $plugin The plugin container.
		 */
		do_action( 'pacifica_core_booted', $this );
	}

	/**
	 * Fetch a booted service instance.
	 *
	 * @template T of object
	 * @param class-string<T> $class
	 * @return T|null
	 */
	public function get( string $class ): ?object {
		/** @var T|null */
		return $this->services[ $class ] ?? null;
	}
}
