<?php
/**
 * Outbound order SMS notifications.
 *
 * Staff are alerted once per order when it is first paid; customers receive a
 * friendly Spanish update on every customer-relevant status change. Every send
 * flows through TwilioClient and is written to the Logger. All copy is filterable
 * via `pacifica_sms_message` so it can be edited without touching code.
 *
 * @package Pacifica\Core
 */

declare( strict_types=1 );

namespace Pacifica\Core\Sms;

use Pacifica\Core\Contracts\Bootable;
use Pacifica\Core\Setup\Options;
use Pacifica\Core\Ordering\OrderMeta;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class OrderNotifications implements Bootable {

	/** Order meta flag guarding against duplicate staff notifications. */
	private const STAFF_FLAG = '_pacifica_staff_notified';

	/** Statuses that trigger a customer SMS. */
	private const CUSTOMER_STATUSES = array( 'processing', 'preparing', 'ready', 'completed', 'cancelled' );

	/** Max characters for the item summary line in the staff message. */
	private const ITEMS_MAX = 160;

	public function boot(): void {
		// New paid order → notify staff once (either hook may fire first).
		add_action( 'woocommerce_order_status_processing', array( $this, 'notify_staff' ), 10, 1 );
		add_action( 'woocommerce_payment_complete', array( $this, 'notify_staff' ), 10, 1 );

		// Any status change → notify the customer.
		add_action( 'woocommerce_order_status_changed', array( $this, 'notify_customer' ), 10, 4 );
	}

	/* ---------------------------------------------------------------------- */
	/* Staff                                                                  */
	/* ---------------------------------------------------------------------- */

	/**
	 * @param int|mixed $order_id
	 */
	public function notify_staff( $order_id ): void {
		$sms = Options::sms();
		if ( empty( $sms['enabled'] ) || empty( $sms['notify_staff'] ) ) {
			return;
		}

		$numbers = array_filter( (array) ( $sms['staff_numbers'] ?? array() ) );
		if ( empty( $numbers ) ) {
			return;
		}

		$order = wc_get_order( (int) $order_id );
		if ( ! $order instanceof \WC_Order ) {
			return;
		}

		// Dedupe: both processing and payment_complete can fire for one order.
		if ( '' !== (string) $order->get_meta( self::STAFF_FLAG ) ) {
			return;
		}
		// Claim the flag up front so a second hook can't double-send.
		$order->update_meta_data( self::STAFF_FLAG, current_time( 'mysql' ) );
		$order->save();

		$body   = $this->staff_message( $order );
		$client = new TwilioClient();

		foreach ( $numbers as $to ) {
			$this->dispatch( $client, (string) $to, $body, 'outbound', $order );
		}
	}

	/**
	 * Build the staff "NUEVO PEDIDO" message.
	 */
	private function staff_message( \WC_Order $order ): string {
		$customer = trim( $order->get_formatted_billing_full_name() );
		if ( '' === $customer ) {
			$customer = __( 'Cliente', 'pacifica-core' );
		}

		$default = sprintf(
			/* translators: 1: order number, 2: customer name, 3: item summary, 4: pickup label. */
			__( "NUEVO PEDIDO\n#%1\$s\n%2\$s\n%3\$s\nRecoge: %4\$s\n\nResponde:\n1=Preparando 2=Listo 3=Entregado 4=Cancelado", 'pacifica-core' ),
			$order->get_order_number(),
			$customer,
			$this->items_summary( $order ),
			$this->pickup_label( $order )
		);

		return $this->template( 'staff_new_order', $default, $order );
	}

	/* ---------------------------------------------------------------------- */
	/* Customer                                                               */
	/* ---------------------------------------------------------------------- */

	/**
	 * @param int    $order_id
	 * @param string $from
	 * @param string $to
	 * @param mixed  $order
	 */
	public function notify_customer( $order_id, $from, $to, $order ): void {
		$sms = Options::sms();
		if ( empty( $sms['enabled'] ) || empty( $sms['notify_customer'] ) ) {
			return;
		}

		$to = (string) $to;
		if ( ! in_array( $to, self::CUSTOMER_STATUSES, true ) ) {
			return;
		}

		if ( ! $order instanceof \WC_Order ) {
			$order = wc_get_order( (int) $order_id );
		}
		if ( ! $order instanceof \WC_Order ) {
			return;
		}

		$phone = trim( (string) $order->get_billing_phone() );
		if ( '' === $phone ) {
			return;
		}

		$body = $this->customer_message( $to, $order );
		if ( '' === $body ) {
			return;
		}

		$this->dispatch( new TwilioClient(), $phone, $body, 'outbound', $order );
	}

	/**
	 * Friendly Spanish message describing the new status.
	 */
	private function customer_message( string $status, \WC_Order $order ): string {
		$number   = $order->get_order_number();
		$business = Options::business();
		$address  = (string) ( $business['address'] ?? '' );

		switch ( $status ) {
			case 'processing':
				$default = sprintf(
					/* translators: %s: order number. */
					__( '¡Gracias! Recibimos tu pedido #%s. Te avisaremos cuando esté listo. 🥖', 'pacifica-core' ),
					$number
				);
				break;
			case 'preparing':
				$default = sprintf(
					/* translators: %s: order number. */
					__( 'Manos a la masa: estamos preparando tu pedido #%s.', 'pacifica-core' ),
					$number
				);
				break;
			case 'ready':
				$default = sprintf(
					/* translators: 1: order number, 2: business address. */
					__( '¡Tu pedido #%1$s está listo para recoger! Te esperamos en %2$s.', 'pacifica-core' ),
					$number,
					$address
				);
				break;
			case 'completed':
				$default = sprintf(
					/* translators: %s: order number. */
					__( '¡Gracias por tu compra! Entregamos tu pedido #%s. ¡Buen provecho! 🌾', 'pacifica-core' ),
					$number
				);
				break;
			case 'cancelled':
				$default = sprintf(
					/* translators: %s: order number. */
					__( 'Tu pedido #%s fue cancelado. Si tienes dudas, contáctanos.', 'pacifica-core' ),
					$number
				);
				break;
			default:
				return '';
		}

		return $this->template( 'customer_' . $status, $default, $order );
	}

	/* ---------------------------------------------------------------------- */
	/* Helpers                                                                */
	/* ---------------------------------------------------------------------- */

	/**
	 * Send + log a single message.
	 */
	private function dispatch( TwilioClient $client, string $to, string $body, string $direction, \WC_Order $order ): void {
		$result = $client->send( $to, $body );

		Logger::record(
			array(
				'direction'    => $direction,
				'order_id'     => $order->get_id(),
				'recipient'    => $to,
				'sender'       => (string) ( Options::sms()['twilio_from'] ?? '' ),
				'body'         => $body,
				'status'       => $result['success'] ? 'sent' : 'failed',
				'provider_sid' => $result['sid'],
				'error'        => $result['error'],
			)
		);
	}

	/**
	 * Pickup label via the ordering module, guarded so absence never fatals.
	 */
	private function pickup_label( \WC_Order $order ): string {
		if ( class_exists( OrderMeta::class ) && method_exists( OrderMeta::class, 'label' ) ) {
			$label = (string) OrderMeta::label( $order );
			if ( '' !== $label ) {
				return $label;
			}
		}
		return __( 'por confirmar', 'pacifica-core' );
	}

	/**
	 * "Name x2, Other x1" summary, truncated for SMS.
	 */
	private function items_summary( \WC_Order $order ): string {
		$parts = array();
		foreach ( $order->get_items() as $item ) {
			if ( ! $item instanceof \WC_Order_Item_Product ) {
				continue;
			}
			$parts[] = sprintf( '%s x%d', $item->get_name(), (int) $item->get_quantity() );
		}

		$summary = implode( ', ', $parts );
		if ( mb_strlen( $summary ) > self::ITEMS_MAX ) {
			$summary = rtrim( mb_substr( $summary, 0, self::ITEMS_MAX - 1 ) ) . '…';
		}

		return '' !== $summary ? $summary : __( '(sin artículos)', 'pacifica-core' );
	}

	/**
	 * Apply the editable-copy filter.
	 *
	 * @param string $key     Template key (e.g. 'staff_new_order', 'customer_ready').
	 * @param string $default Default message body.
	 */
	private function template( string $key, string $default, \WC_Order $order ): string {
		/**
		 * Filter an outbound SMS message body before it is sent.
		 *
		 * @param string    $default Rendered default copy.
		 * @param string    $key     Template key.
		 * @param \WC_Order $order   The order the message concerns.
		 */
		return (string) apply_filters( 'pacifica_sms_message', $default, $key, $order );
	}
}
