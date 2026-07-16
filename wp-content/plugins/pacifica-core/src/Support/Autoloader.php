<?php
/**
 * Minimal PSR-4 autoloader used when Composer's autoloader is absent.
 *
 * @package Pacifica\Core
 */

declare( strict_types=1 );

namespace Pacifica\Core\Support;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Autoloader {

	/**
	 * Register a PSR-4 prefix → base directory mapping.
	 */
	public static function register( string $prefix, string $base_dir ): void {
		$prefix   = rtrim( $prefix, '\\' ) . '\\';
		$base_dir = rtrim( $base_dir, '/\\' ) . '/';

		spl_autoload_register( static function ( string $class ) use ( $prefix, $base_dir ): void {
			if ( 0 !== strpos( $class, $prefix ) ) {
				return;
			}
			$relative = substr( $class, strlen( $prefix ) );
			$file     = $base_dir . str_replace( '\\', '/', $relative ) . '.php';
			if ( is_readable( $file ) ) {
				require_once $file;
			}
		} );
	}
}
