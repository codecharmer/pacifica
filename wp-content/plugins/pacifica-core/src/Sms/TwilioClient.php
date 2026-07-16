<?php
/**
 * Thin Twilio REST client.
 *
 * Sends SMS via the Twilio Messages API using WordPress' HTTP layer
 * (`wp_remote_post`) — no external SDK. Also provides the X-Twilio-Signature
 * validation helper used to authenticate inbound webhooks.
 *
 * Configuration (including secrets) is read exclusively through Options::sms(),
 * which resolves PACIFICA_TWILIO_* constants over stored values. Secrets are
 * never logged or echoed.
 *
 * @package Pacifica\Core
 */

declare( strict_types=1 );

namespace Pacifica\Core\Sms;

use Pacifica\Core\Setup\Options;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class TwilioClient {

	/** Messages resource endpoint (Account SID interpolated at call time). */
	private const ENDPOINT = 'https://api.twilio.com/2010-04-01/Accounts/%s/Messages.json';

	/**
	 * Send an SMS.
	 *
	 * @param string $to   Destination number (E.164 recommended).
	 * @param string $body Message body.
	 * @return array{success:bool,sid:string,error:string}
	 */
	public function send( string $to, string $body ): array {
		$sms = Options::sms();

		if ( empty( $sms['enabled'] ) ) {
			return self::failure( __( 'La mensajería SMS está desactivada.', 'pacifica-core' ) );
		}

		$sid   = (string) ( $sms['twilio_sid'] ?? '' );
		$token = (string) ( $sms['twilio_token'] ?? '' );
		$from  = (string) ( $sms['twilio_from'] ?? '' );
		$mssid = (string) ( $sms['messaging_service_sid'] ?? '' );

		if ( '' === $sid || '' === $token ) {
			return self::failure( __( 'Faltan las credenciales de Twilio.', 'pacifica-core' ) );
		}
		if ( '' === $from && '' === $mssid ) {
			return self::failure( __( 'No hay número remitente ni Messaging Service configurado.', 'pacifica-core' ) );
		}

		$to = trim( $to );
		if ( '' === $to || '' === trim( $body ) ) {
			return self::failure( __( 'Destinatario o mensaje vacío.', 'pacifica-core' ) );
		}

		$payload = array(
			'To'   => $to,
			'Body' => $body,
		);
		// MessagingServiceSid takes precedence when both are present.
		if ( '' !== $mssid ) {
			$payload['MessagingServiceSid'] = $mssid;
		} else {
			$payload['From'] = $from;
		}

		$response = wp_remote_post(
			sprintf( self::ENDPOINT, rawurlencode( $sid ) ),
			array(
				'timeout' => 15,
				'headers' => array(
					'Authorization' => 'Basic ' . base64_encode( $sid . ':' . $token ),
					'Accept'        => 'application/json',
				),
				'body'    => $payload,
			)
		);

		if ( is_wp_error( $response ) ) {
			return self::failure( $response->get_error_message() );
		}

		$code = (int) wp_remote_retrieve_response_code( $response );
		$data = json_decode( (string) wp_remote_retrieve_body( $response ), true );
		$data = is_array( $data ) ? $data : array();

		if ( $code >= 200 && $code < 300 && ! empty( $data['sid'] ) ) {
			return array(
				'success' => true,
				'sid'     => (string) $data['sid'],
				'error'   => '',
			);
		}

		$message = isset( $data['message'] ) && '' !== (string) $data['message']
			? (string) $data['message']
			/* translators: %d: HTTP status code. */
			: sprintf( __( 'Error de Twilio (HTTP %d).', 'pacifica-core' ), $code );

		return self::failure( $message );
	}

	/**
	 * Validate an X-Twilio-Signature header.
	 *
	 * Twilio's scheme: sort the POST parameters by key, concatenate the full
	 * request URL followed by each key/value pair, then HMAC-SHA1 that string
	 * with the account auth token and base64-encode the result.
	 *
	 * @param string               $auth_token Twilio auth token (HMAC key).
	 * @param string               $url        Exact URL Twilio requested.
	 * @param array<string,mixed>  $params     POST parameters as received.
	 * @param string               $signature  Provided X-Twilio-Signature value.
	 */
	public static function validate_signature( string $auth_token, string $url, array $params, string $signature ): bool {
		if ( '' === $auth_token || '' === $signature ) {
			return false;
		}

		ksort( $params );

		$data = $url;
		foreach ( $params as $key => $value ) {
			$data .= $key . ( is_scalar( $value ) ? (string) $value : wp_json_encode( $value ) );
		}

		$computed = base64_encode( hash_hmac( 'sha1', $data, $auth_token, true ) );

		return hash_equals( $computed, $signature );
	}

	/**
	 * @return array{success:bool,sid:string,error:string}
	 */
	private static function failure( string $error ): array {
		return array(
			'success' => false,
			'sid'     => '',
			'error'   => $error,
		);
	}
}
