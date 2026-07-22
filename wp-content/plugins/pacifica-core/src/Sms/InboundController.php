<?php
/**
 * Inbound SMS webhook (Twilio → WooCommerce status transitions).
 *
 * Registers POST `pacifica/v1/sms/inbound`. The route is publicly reachable but
 * authenticated by the X-Twilio-Signature HMAC (validated against the configured
 * auth token and the exact request URL + POST params). Only messages from a
 * configured staff number are honoured. The reply digit/keyword maps to a
 * WooCommerce status via Options::sms()['reply_map']; the target order is either
 * an explicit "#123" reference or the most recent still-open order. Every inbound
 * message is logged and answered with TwiML.
 *
 * @package Pacifica\Core
 */

declare( strict_types=1 );

namespace Pacifica\Core\Sms;

use Pacifica\Core\Contracts\Bootable;
use Pacifica\Core\Setup\Options;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class InboundController implements Bootable {

	private const NAMESPACE = 'pacifica/v1';
	private const ROUTE     = '/sms/inbound';

	/** Open statuses eligible as the implicit target order (most recent first). */
	private const OPEN_STATUSES = array( 'processing', 'preparing', 'ready', 'pending', 'on-hold' );

	/** Human labels for reply confirmation, keyed by status slug. */
	private const STATUS_LABELS = array(
		'processing' => 'Recibido',
		'preparing'  => 'Preparando',
		'ready'      => 'Listo',
		'completed'  => 'Entregado',
		'cancelled'  => 'Cancelado',
	);

	public function boot(): void {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
		add_filter( 'restricted_site_access_is_restricted', array( $this, 'allow_provider_webhook' ), 10, 2 );
	}

	/**
	 * Keep the provider webhook reachable when the site is behind Restricted
	 * Site Access (10up).
	 *
	 * Gating the whole front end would otherwise swallow inbound status
	 * replies, silently breaking the SMS workflow. This route does not depend
	 * on site-level access control: every request is verified against the
	 * Twilio signature in handle(), so opening it changes nothing about its
	 * security posture.
	 *
	 * @param bool   $is_restricted Whether the current request is restricted.
	 * @param object $wp            The WP request object (unused).
	 * @return bool
	 */
	public function allow_provider_webhook( $is_restricted, $wp = null ) {
		unset( $wp );

		$path = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
		if ( ! is_string( $path ) || '' === $path ) {
			return $is_restricted;
		}

		$needle = '/' . trim( self::NAMESPACE, '/' ) . self::ROUTE;
		if ( false !== strpos( sanitize_text_field( $path ), $needle ) ) {
			return false;
		}

		return $is_restricted;
	}

	public function register_routes(): void {
		register_rest_route(
			self::NAMESPACE,
			self::ROUTE,
			array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'handle' ),
				// Security is enforced via the Twilio signature inside handle().
				'permission_callback' => '__return_true',
			)
		);
	}

	/**
	 * Handle an inbound Twilio webhook.
	 *
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function handle( \WP_REST_Request $request ) {
		$sms   = Options::sms();
		$token = (string) ( $sms['twilio_token'] ?? '' );

		$params    = $request->get_body_params();
		$signature = (string) $request->get_header( 'x_twilio_signature' );

		if ( ! TwilioClient::validate_signature( $token, $this->request_url(), $params, $signature ) ) {
			return new \WP_Error(
				'pacifica_sms_bad_signature',
				__( 'Firma de Twilio inválida.', 'pacifica-core' ),
				array( 'status' => 403 )
			);
		}

		$from = preg_replace( '/[^\d+]/', '', (string) ( $params['From'] ?? '' ) );
		$body = trim( (string) ( $params['Body'] ?? '' ) );
		$our  = (string) ( $sms['twilio_from'] ?? '' );

		// Accept only known staff numbers.
		if ( ! $this->is_staff_number( $from, (array) ( $sms['staff_numbers'] ?? array() ) ) ) {
			$this->log_inbound( $from, $our, $body, null, 'rejected' );
			return $this->twiml( __( 'Número no autorizado.', 'pacifica-core' ) );
		}

		// Map the leading digit/keyword to a status.
		$status = $this->map_status( $body, (array) ( $sms['reply_map'] ?? array() ) );
		if ( null === $status ) {
			$this->log_inbound( $from, $our, $body, null, 'unmatched' );
			return $this->twiml(
				__( 'No entendí el mensaje. Responde 1=Preparando 2=Listo 3=Entregado 4=Cancelado.', 'pacifica-core' )
			);
		}

		// Resolve the target order (explicit "#123" wins).
		$order = $this->resolve_order( $body );
		if ( ! $order instanceof \WC_Order ) {
			$this->log_inbound( $from, $our, $body, null, 'no_order' );
			return $this->twiml( __( 'No encontré un pedido activo para actualizar.', 'pacifica-core' ) );
		}

		$order->update_status(
			$status,
			sprintf(
				/* translators: %s: sender phone number. */
				__( 'Actualizado por SMS desde %s', 'pacifica-core' ),
				$from
			)
		);

		$this->log_inbound( $from, $our, $body, $order->get_id(), 'processed' );

		$label = self::STATUS_LABELS[ $status ] ?? $status;

		return $this->twiml(
			sprintf(
				/* translators: 1: order number, 2: status label. */
				__( 'Pedido #%1$s → %2$s ✅', 'pacifica-core' ),
				$order->get_order_number(),
				$label
			)
		);
	}

	/* ---------------------------------------------------------------------- */
	/* Resolution helpers                                                     */
	/* ---------------------------------------------------------------------- */

	/**
	 * @param array<int,string> $staff
	 */
	private function is_staff_number( string $from, array $staff ): bool {
		if ( '' === $from ) {
			return false;
		}
		foreach ( $staff as $number ) {
			$normalized = preg_replace( '/[^\d+]/', '', (string) $number );
			if ( '' !== $normalized && $normalized === $from ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Map the first digit/keyword of the body to a status slug.
	 *
	 * @param array<string,string> $reply_map
	 */
	private function map_status( string $body, array $reply_map ): ?string {
		if ( '' === $body || empty( $reply_map ) ) {
			return null;
		}

		// Leading token (digit or word), lower-cased for keyword matching.
		$token = strtolower( (string) preg_split( '/\s+/', $body )[0] );

		// Direct key match (e.g. "2", or a configured keyword key).
		if ( isset( $reply_map[ $token ] ) ) {
			return sanitize_key( $reply_map[ $token ] );
		}

		// Fall back to the first character (handles "2Listo" with no space).
		$first = mb_substr( $body, 0, 1 );
		if ( isset( $reply_map[ $first ] ) ) {
			return sanitize_key( $reply_map[ $first ] );
		}

		return null;
	}

	/**
	 * Explicit "#123" reference wins; otherwise the most recent open order.
	 */
	private function resolve_order( string $body ): ?\WC_Order {
		if ( preg_match( '/#\s*(\d+)/', $body, $m ) ) {
			$order = wc_get_order( (int) $m[1] );
			if ( $order instanceof \WC_Order ) {
				return $order;
			}
		}

		// HPOS-safe query for the newest still-open order.
		$orders = wc_get_orders(
			array(
				'type'    => 'shop_order',
				'status'  => self::OPEN_STATUSES,
				'limit'   => 1,
				'orderby' => 'date',
				'order'   => 'DESC',
			)
		);

		$order = is_array( $orders ) ? reset( $orders ) : null;

		return $order instanceof \WC_Order ? $order : null;
	}

	/* ---------------------------------------------------------------------- */
	/* IO helpers                                                             */
	/* ---------------------------------------------------------------------- */

	/**
	 * Reconstruct the exact URL Twilio signed (used only as the HMAC message).
	 */
	private function request_url(): string {
		$host = isset( $_SERVER['HTTP_HOST'] ) ? wp_unslash( $_SERVER['HTTP_HOST'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		$uri  = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		$sch  = is_ssl() ? 'https' : 'http';

		return $sch . '://' . $host . $uri;
	}

	private function log_inbound( string $from, string $to, string $body, ?int $order_id, string $status ): void {
		Logger::record(
			array(
				'direction' => 'inbound',
				'order_id'  => $order_id,
				'recipient' => $to,
				'sender'    => $from,
				'body'      => $body,
				'status'    => $status,
			)
		);
	}

	/**
	 * Build a TwiML text/xml response.
	 */
	private function twiml( string $message ): \WP_REST_Response {
		$xml = sprintf(
			'<?xml version="1.0" encoding="UTF-8"?><Response><Message>%s</Message></Response>',
			esc_html( $message )
		);

		// Serve the raw XML verbatim instead of letting core JSON-encode it.
		add_filter(
			'rest_pre_serve_request',
			static function ( $served, $result, $req ) use ( $xml ) {
				if ( $req->get_route() === '/' . self::NAMESPACE . self::ROUTE ) {
					if ( ! headers_sent() ) {
						header( 'Content-Type: text/xml; charset=utf-8' );
					}
					echo $xml; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- XML pre-escaped.
					return true;
				}
				return $served;
			},
			10,
			3
		);

		$response = new \WP_REST_Response( $xml );
		$response->header( 'Content-Type', 'text/xml; charset=utf-8' );

		return $response;
	}
}
