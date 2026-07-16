<?php
/**
 * Configuration authority.
 *
 * Single source of truth for every editable setting. All modules read config
 * through the static accessors here — never `get_option()` directly — so the
 * schema, defaults, and secret-resolution rules live in one place.
 *
 * Secrets (Twilio auth token, Stripe keys) may be supplied via PHP constants /
 * environment for production hygiene; constants always win over stored values.
 *
 * @package Pacifica\Core
 */

declare( strict_types=1 );

namespace Pacifica\Core\Setup;

use Pacifica\Core\Contracts\Bootable;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Options implements Bootable {

	public const BUSINESS = 'pacifica_business_info';
	public const PICKUP   = 'pacifica_pickup';
	public const SMS      = 'pacifica_sms';
	public const SEO      = 'pacifica_seo';

	public function boot(): void {
		add_action( 'init', array( $this, 'register_settings' ) );
	}

	/**
	 * Register settings so they are sanitised and exposed to the REST/Site Editor.
	 */
	public function register_settings(): void {
		$groups = array(
			self::BUSINESS => array( $this, 'sanitize_business' ),
			self::PICKUP   => array( $this, 'sanitize_pickup' ),
			self::SMS      => array( $this, 'sanitize_sms' ),
			self::SEO      => array( $this, 'sanitize_seo' ),
		);
		foreach ( $groups as $name => $sanitizer ) {
			register_setting( 'pacifica', $name, array(
				'type'              => 'object',
				'sanitize_callback' => $sanitizer,
				'show_in_rest'      => false, // Contains operational config; not public.
				'default'           => array(),
			) );
		}
	}

	/* ---------------------------------------------------------------------- */
	/* Defaults                                                               */
	/* ---------------------------------------------------------------------- */

	/** @return array<string,mixed> */
	public static function defaults(): array {
		return array(
			self::BUSINESS => array(
				'name'             => 'Pacífica Panadería',
				'tagline'          => 'Artesanal no es una moda.',
				'phone'            => '+52 777 773 2179',
				'phone_link'       => '+527777732179',
				'whatsapp'         => 'https://wa.me/527777732179',
				'email'            => 'hola@pacifica.mx',
				'address'          => 'Tulipán 302, Col. Delicias, 62330 Cuernavaca, Morelos',
				'address_short'    => 'Tulipán 302, Cuernavaca',
				'street'           => 'Tulipán 302',
				'locality'         => 'Cuernavaca',
				'region'           => 'Morelos',
				'postal_code'      => '62330',
				'country'          => 'MX',
				'hours_summary'    => 'Miércoles a domingo, 9:00–15:00',
				'hours_closed'     => 'Cerrado lunes y martes',
				'instagram'        => 'https://www.instagram.com/pacifica.mx/',
				'instagram_handle' => '@pacifica.mx',
				'maps_url'         => 'https://maps.google.com/?q=Pac%C3%ADfica+Panader%C3%ADa+Cuernavaca',
				'latitude'         => '18.9186',
				'longitude'        => '-99.2342',
			),
			self::PICKUP => array(
				'open_days'       => array( 3, 4, 5, 6, 0 ), // Wed–Sun (0=Sun … 6=Sat).
				'open_time'       => '09:00',
				'close_time'      => '15:00',
				'last_pickup'     => '14:30',
				'lead_time_hours' => 24,
				'slot_minutes'    => 30,
				'slot_capacity'   => 8,
				'max_days_ahead'  => 21,
				'blackout_dates'  => array(),
				'timezone'        => 'America/Mexico_City',
				'instructions'    => 'Recoge tu pedido en Tulipán 302, Col. Delicias. Menciona tu número de pedido en el mostrador.',
			),
			self::SMS => array(
				'enabled'                => false,
				'provider'               => 'twilio',
				'twilio_sid'             => '',
				'twilio_token'           => '',
				'twilio_from'            => '',
				'messaging_service_sid'  => '',
				'staff_numbers'          => array(),
				'notify_customer'        => true,
				'notify_staff'           => true,
				'reply_map'              => array(
					'1' => 'preparing',
					'2' => 'ready',
					'3' => 'completed',
					'4' => 'cancelled',
				),
			),
			self::SEO => array(
				'default_og_image' => 0,
				'twitter_handle'   => '@pacifica.mx',
				'organization_logo'=> 0,
				'price_range'      => '$$',
			),
		);
	}

	/* ---------------------------------------------------------------------- */
	/* Accessors                                                              */
	/* ---------------------------------------------------------------------- */

	/** @return array<string,mixed> */
	public static function group( string $name ): array {
		$defaults = self::defaults()[ $name ] ?? array();
		$stored   = get_option( $name, array() );
		$stored   = is_array( $stored ) ? $stored : array();
		return array_merge( $defaults, $stored );
	}

	/** @return array<string,mixed> */
	public static function business(): array {
		return self::group( self::BUSINESS );
	}

	/** @return array<string,mixed> */
	public static function pickup(): array {
		return self::group( self::PICKUP );
	}

	/** @return array<string,mixed> */
	public static function seo(): array {
		return self::group( self::SEO );
	}

	/**
	 * SMS config with constant/env overrides applied to secrets.
	 *
	 * @return array<string,mixed>
	 */
	public static function sms(): array {
		$sms = self::group( self::SMS );

		$const_map = array(
			'twilio_sid'            => 'PACIFICA_TWILIO_SID',
			'twilio_token'          => 'PACIFICA_TWILIO_AUTH_TOKEN',
			'twilio_from'           => 'PACIFICA_TWILIO_FROM',
			'messaging_service_sid' => 'PACIFICA_TWILIO_MESSAGING_SID',
		);
		foreach ( $const_map as $key => $const ) {
			if ( defined( $const ) && '' !== (string) constant( $const ) ) {
				$sms[ $key ] = (string) constant( $const );
			}
		}
		return $sms;
	}

	/** Convenience single-value getter. */
	public static function get( string $group, string $key, mixed $default = null ): mixed {
		$data = self::group( $group );
		return $data[ $key ] ?? $default;
	}

	/** The site's configured pickup timezone as a DateTimeZone. */
	public static function timezone(): \DateTimeZone {
		$tz = (string) self::get( self::PICKUP, 'timezone', 'America/Mexico_City' );
		try {
			return new \DateTimeZone( $tz );
		} catch ( \Exception $e ) {
			return new \DateTimeZone( 'America/Mexico_City' );
		}
	}

	/**
	 * Install any missing defaults without clobbering existing values.
	 */
	public static function install_defaults(): void {
		foreach ( self::defaults() as $name => $default ) {
			$existing = get_option( $name, null );
			if ( null === $existing ) {
				add_option( $name, $default );
			} elseif ( is_array( $existing ) ) {
				update_option( $name, array_merge( $default, $existing ) );
			}
		}
	}

	/* ---------------------------------------------------------------------- */
	/* Sanitizers                                                             */
	/* ---------------------------------------------------------------------- */

	/**
	 * @param mixed $value
	 * @return array<string,string>
	 */
	public function sanitize_business( mixed $value ): array {
		$value = is_array( $value ) ? $value : array();
		$clean = array();
		foreach ( self::defaults()[ self::BUSINESS ] as $key => $default ) {
			if ( ! isset( $value[ $key ] ) ) {
				continue;
			}
			$raw = (string) $value[ $key ];
			$clean[ $key ] = in_array( $key, array( 'instagram', 'whatsapp', 'maps_url' ), true )
				? esc_url_raw( $raw )
				: ( 'email' === $key ? sanitize_email( $raw ) : sanitize_text_field( $raw ) );
		}
		return $clean;
	}

	/**
	 * @param mixed $value
	 * @return array<string,mixed>
	 */
	public function sanitize_pickup( mixed $value ): array {
		$value = is_array( $value ) ? $value : array();
		$out   = array();
		$out['open_days']       = array_values( array_unique( array_map( 'intval', (array) ( $value['open_days'] ?? array() ) ) ) );
		$out['open_time']       = preg_match( '/^\d{2}:\d{2}$/', (string) ( $value['open_time'] ?? '' ) ) ? $value['open_time'] : '09:00';
		$out['close_time']      = preg_match( '/^\d{2}:\d{2}$/', (string) ( $value['close_time'] ?? '' ) ) ? $value['close_time'] : '15:00';
		$out['last_pickup']     = preg_match( '/^\d{2}:\d{2}$/', (string) ( $value['last_pickup'] ?? '' ) ) ? $value['last_pickup'] : '14:30';
		$out['lead_time_hours'] = max( 0, (int) ( $value['lead_time_hours'] ?? 24 ) );
		$out['slot_minutes']    = max( 5, (int) ( $value['slot_minutes'] ?? 30 ) );
		$out['slot_capacity']   = max( 1, (int) ( $value['slot_capacity'] ?? 8 ) );
		$out['max_days_ahead']  = max( 1, (int) ( $value['max_days_ahead'] ?? 21 ) );
		$out['timezone']        = sanitize_text_field( (string) ( $value['timezone'] ?? 'America/Mexico_City' ) );
		$out['instructions']    = sanitize_textarea_field( (string) ( $value['instructions'] ?? '' ) );
		$dates = array_filter( array_map( 'sanitize_text_field', (array) ( $value['blackout_dates'] ?? array() ) ), static fn( $d ) => (bool) preg_match( '/^\d{4}-\d{2}-\d{2}$/', $d ) );
		$out['blackout_dates']  = array_values( $dates );
		return $out;
	}

	/**
	 * @param mixed $value
	 * @return array<string,mixed>
	 */
	public function sanitize_sms( mixed $value ): array {
		$value = is_array( $value ) ? $value : array();
		$out   = array();
		$out['enabled']               = ! empty( $value['enabled'] );
		$out['provider']              = 'twilio';
		$out['twilio_sid']            = sanitize_text_field( (string) ( $value['twilio_sid'] ?? '' ) );
		$out['twilio_token']          = sanitize_text_field( (string) ( $value['twilio_token'] ?? '' ) );
		$out['twilio_from']           = sanitize_text_field( (string) ( $value['twilio_from'] ?? '' ) );
		$out['messaging_service_sid'] = sanitize_text_field( (string) ( $value['messaging_service_sid'] ?? '' ) );
		$out['notify_customer']       = ! empty( $value['notify_customer'] );
		$out['notify_staff']          = ! empty( $value['notify_staff'] );

		$numbers = (array) ( $value['staff_numbers'] ?? array() );
		if ( 1 === count( $numbers ) && is_string( reset( $numbers ) ) && str_contains( (string) reset( $numbers ), ',' ) ) {
			$numbers = explode( ',', (string) reset( $numbers ) );
		}
		$out['staff_numbers'] = array_values( array_filter( array_map(
			static fn( $n ) => preg_replace( '/[^\d+]/', '', (string) $n ),
			$numbers
		) ) );

		$reply = array();
		foreach ( (array) ( $value['reply_map'] ?? self::defaults()[ self::SMS ]['reply_map'] ) as $k => $status ) {
			$reply[ preg_replace( '/[^\w]/', '', (string) $k ) ] = sanitize_key( (string) $status );
		}
		$out['reply_map'] = $reply;
		return $out;
	}

	/**
	 * @param mixed $value
	 * @return array<string,mixed>
	 */
	public function sanitize_seo( mixed $value ): array {
		$value = is_array( $value ) ? $value : array();
		return array(
			'default_og_image'  => (int) ( $value['default_og_image'] ?? 0 ),
			'twitter_handle'    => sanitize_text_field( (string) ( $value['twitter_handle'] ?? '@pacifica.mx' ) ),
			'organization_logo' => (int) ( $value['organization_logo'] ?? 0 ),
			'price_range'       => sanitize_text_field( (string) ( $value['price_range'] ?? '$$' ) ),
		);
	}
}
