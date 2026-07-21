<?php
/**
 * Ajustes — Pacífica settings screen.
 *
 * Tabbed settings UI (Negocio / Recolección / SMS / SEO) built on the WordPress
 * Settings API. The whole form posts to options.php with settings_fields('pacifica')
 * so the sanitizers registered in Options handle every write; all four option
 * groups are rendered in one form (tabs are presentational) so switching tabs and
 * saving never wipes another tab's values.
 *
 * Secrets are rendered masked; when a value is supplied through a PACIFICA_TWILIO_*
 * constant the field becomes read-only/informational. Two side tools live outside
 * the Settings-API form with their own nonces: "Enviar SMS de prueba" (AJAX) and
 * "Instalar contenido de demostración" (admin-post → fires pacifica_run_content_install).
 *
 * @package Pacifica\Core
 */

declare( strict_types=1 );

namespace Pacifica\Core\Setup;

use Pacifica\Core\Admin\Dashboard;
use Pacifica\Core\Contracts\Bootable;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Settings implements Bootable {

	private const SLUG          = 'pacifica-ajustes';
	private const SETTINGS_GROUP = 'pacifica';

	/** Secret keys → the constant that, when defined, overrides them. */
	private const SECRET_CONSTANTS = array(
		'twilio_sid'            => 'PACIFICA_TWILIO_SID',
		'twilio_token'          => 'PACIFICA_TWILIO_AUTH_TOKEN',
		'twilio_from'           => 'PACIFICA_TWILIO_FROM',
		'messaging_service_sid' => 'PACIFICA_TWILIO_MESSAGING_SID',
	);

	public function boot(): void {
		add_action( 'admin_menu', array( $this, 'register_menu' ), 10 );
		add_action( 'admin_post_pacifica_run_content_install', array( $this, 'handle_content_install' ) );
		add_action( 'wp_ajax_pacifica_test_sms', array( $this, 'ajax_test_sms' ) );
		add_action( 'admin_notices', array( $this, 'maybe_render_notice' ) );
	}

	public function register_menu(): void {
		add_submenu_page(
			Dashboard::SLUG,
			__( 'Ajustes de Pacífica', 'pacifica-core' ),
			__( 'Ajustes', 'pacifica-core' ),
			Activator::CAP,
			self::SLUG,
			array( $this, 'render' )
		);
	}

	/* ---------------------------------------------------------------------- */
	/* Screen                                                                 */
	/* ---------------------------------------------------------------------- */

	public function render(): void {
		if ( ! current_user_can( Activator::CAP ) ) {
			wp_die( esc_html__( 'No tienes permiso para ver esta página.', 'pacifica-core' ) );
		}

		$business = Options::group( Options::BUSINESS );
		$pickup   = Options::group( Options::PICKUP );
		$sms      = Options::group( Options::SMS );
		$seo      = Options::group( Options::SEO );

		$tabs   = array(
			'business' => __( 'Negocio', 'pacifica-core' ),
			'pickup'   => __( 'Recolección', 'pacifica-core' ),
			'sms'      => __( 'SMS', 'pacifica-core' ),
			'seo'      => __( 'SEO', 'pacifica-core' ),
		);
		$active = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'business'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- presentational tab.
		if ( ! isset( $tabs[ $active ] ) ) {
			$active = 'business';
		}
		$base_url = admin_url( 'admin.php?page=' . self::SLUG );
		?>
		<div class="wrap pacifica-wrap pacifica-settings" data-active-tab="<?php echo esc_attr( $active ); ?>">
			<h1 class="pacifica-title">
				<span class="dashicons dashicons-admin-settings" aria-hidden="true"></span>
				<?php esc_html_e( 'Ajustes de Pacífica', 'pacifica-core' ); ?>
			</h1>

			<nav class="nav-tab-wrapper pacifica-settings__tabs">
				<?php foreach ( $tabs as $key => $label ) : ?>
					<a class="nav-tab <?php echo $active === $key ? 'nav-tab-active' : ''; ?>"
						href="<?php echo esc_url( add_query_arg( 'tab', $key, $base_url ) ); ?>"
						data-pacifica-tab="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></a>
				<?php endforeach; ?>
			</nav>

			<form method="post" action="options.php" class="pacifica-settings__form">
				<?php settings_fields( self::SETTINGS_GROUP ); ?>

				<div class="pacifica-tab-panel" data-pacifica-panel="business" <?php echo 'business' === $active ? '' : 'hidden'; ?>>
					<?php $this->render_business( $business ); ?>
				</div>
				<div class="pacifica-tab-panel" data-pacifica-panel="pickup" <?php echo 'pickup' === $active ? '' : 'hidden'; ?>>
					<?php $this->render_pickup( $pickup ); ?>
				</div>
				<div class="pacifica-tab-panel" data-pacifica-panel="sms" <?php echo 'sms' === $active ? '' : 'hidden'; ?>>
					<?php $this->render_sms( $sms ); ?>
				</div>
				<div class="pacifica-tab-panel" data-pacifica-panel="seo" <?php echo 'seo' === $active ? '' : 'hidden'; ?>>
					<?php $this->render_seo( $seo ); ?>
				</div>

				<?php submit_button( __( 'Guardar cambios', 'pacifica-core' ) ); ?>
			</form>

			<?php $this->render_tools( $sms ); ?>
		</div>
		<?php
	}

	/* ---------------------------------------------------------------------- */
	/* Tab: Negocio                                                           */
	/* ---------------------------------------------------------------------- */

	private function render_business( array $v ): void {
		$g = Options::BUSINESS;
		echo '<table class="form-table" role="presentation"><tbody>';
		$this->text_row( $g, 'name', __( 'Nombre del negocio', 'pacifica-core' ), $v );
		$this->text_row( $g, 'tagline', __( 'Eslogan', 'pacifica-core' ), $v );
		$this->text_row( $g, 'phone', __( 'Teléfono (visible)', 'pacifica-core' ), $v );
		$this->text_row( $g, 'phone_link', __( 'Teléfono (enlace tel:)', 'pacifica-core' ), $v );
		$this->text_row( $g, 'whatsapp', __( 'WhatsApp (URL)', 'pacifica-core' ), $v, 'url' );
		$this->text_row( $g, 'email', __( 'Correo electrónico', 'pacifica-core' ), $v, 'email' );
		$this->text_row( $g, 'address', __( 'Dirección completa', 'pacifica-core' ), $v );
		$this->text_row( $g, 'address_short', __( 'Dirección corta', 'pacifica-core' ), $v );
		$this->text_row( $g, 'street', __( 'Calle', 'pacifica-core' ), $v );
		$this->text_row( $g, 'locality', __( 'Ciudad', 'pacifica-core' ), $v );
		$this->text_row( $g, 'region', __( 'Estado', 'pacifica-core' ), $v );
		$this->text_row( $g, 'postal_code', __( 'Código postal', 'pacifica-core' ), $v );
		$this->text_row( $g, 'country', __( 'País (ISO)', 'pacifica-core' ), $v );
		$this->text_row( $g, 'hours_summary', __( 'Horario (resumen)', 'pacifica-core' ), $v );
		$this->text_row( $g, 'hours_closed', __( 'Días cerrados', 'pacifica-core' ), $v );
		$this->text_row( $g, 'instagram', __( 'Instagram (URL)', 'pacifica-core' ), $v, 'url' );
		$this->text_row( $g, 'instagram_handle', __( 'Instagram (usuario)', 'pacifica-core' ), $v );
		$this->text_row( $g, 'maps_url', __( 'Google Maps (URL)', 'pacifica-core' ), $v, 'url' );
		$this->text_row( $g, 'latitude', __( 'Latitud', 'pacifica-core' ), $v );
		$this->text_row( $g, 'longitude', __( 'Longitud', 'pacifica-core' ), $v );
		echo '</tbody></table>';
	}

	/* ---------------------------------------------------------------------- */
	/* Tab: Recolección                                                       */
	/* ---------------------------------------------------------------------- */

	private function render_pickup( array $v ): void {
		$g    = Options::PICKUP;
		$days = array(
			0 => __( 'Dom', 'pacifica-core' ),
			1 => __( 'Lun', 'pacifica-core' ),
			2 => __( 'Mar', 'pacifica-core' ),
			3 => __( 'Mié', 'pacifica-core' ),
			4 => __( 'Jue', 'pacifica-core' ),
			5 => __( 'Vie', 'pacifica-core' ),
			6 => __( 'Sáb', 'pacifica-core' ),
		);
		$open = array_map( 'intval', (array) ( $v['open_days'] ?? array() ) );
		echo '<table class="form-table" role="presentation"><tbody>';

		echo '<tr><th scope="row">' . esc_html__( 'Días abiertos', 'pacifica-core' ) . '</th><td><fieldset>';
		foreach ( $days as $num => $label ) {
			printf(
				'<label class="pacifica-inline-check"><input type="checkbox" name="%1$s[open_days][]" value="%2$d" %3$s> %4$s</label>',
				esc_attr( $g ),
				(int) $num,
				checked( in_array( $num, $open, true ), true, false ),
				esc_html( $label )
			);
		}
		echo '</fieldset></td></tr>';

		$this->text_row( $g, 'open_time', __( 'Hora de apertura', 'pacifica-core' ), $v, 'time' );
		$this->text_row( $g, 'close_time', __( 'Hora de cierre', 'pacifica-core' ), $v, 'time' );
		$this->text_row( $g, 'last_pickup', __( 'Última recolección', 'pacifica-core' ), $v, 'time' );
		$this->number_row( $g, 'lead_time_hours', __( 'Anticipación (horas)', 'pacifica-core' ), $v, 0 );
		$this->number_row( $g, 'slot_minutes', __( 'Duración de cada horario (min)', 'pacifica-core' ), $v, 5 );
		$this->number_row( $g, 'slot_capacity', __( 'Capacidad por horario', 'pacifica-core' ), $v, 1 );
		$this->number_row( $g, 'max_days_ahead', __( 'Días máximos por adelantado', 'pacifica-core' ), $v, 1 );
		$this->text_row( $g, 'timezone', __( 'Zona horaria', 'pacifica-core' ), $v );
		$this->textarea_row( $g, 'instructions', __( 'Instrucciones de recolección', 'pacifica-core' ), $v );

		// Blackout dates — repeatable date list.
		$blackouts = array_values( array_filter( (array) ( $v['blackout_dates'] ?? array() ) ) );
		echo '<tr><th scope="row">' . esc_html__( 'Días bloqueados', 'pacifica-core' ) . '</th><td>';
		echo '<div class="pacifica-repeatable" data-repeatable="blackout">';
		if ( empty( $blackouts ) ) {
			$blackouts = array( '' );
		}
		foreach ( $blackouts as $date ) {
			$this->repeatable_row( $g . '[blackout_dates][]', (string) $date, 'date' );
		}
		echo '</div>';
		printf(
			'<button type="button" class="button pacifica-repeatable-add" data-target="blackout">%s</button>',
			esc_html__( 'Agregar fecha', 'pacifica-core' )
		);
		echo '</td></tr>';

		echo '</tbody></table>';
	}

	/* ---------------------------------------------------------------------- */
	/* Tab: SMS                                                               */
	/* ---------------------------------------------------------------------- */

	private function render_sms( array $v ): void {
		$g = Options::SMS;
		echo '<table class="form-table" role="presentation"><tbody>';

		$this->checkbox_row( $g, 'enabled', __( 'Activar SMS', 'pacifica-core' ), __( 'Enviar notificaciones por SMS.', 'pacifica-core' ), $v );

		// Provider is fixed to Twilio; keep it in POST so the sanitizer sees it.
		printf( '<input type="hidden" name="%s[provider]" value="twilio">', esc_attr( $g ) );

		$this->secret_row( $g, 'twilio_sid', __( 'Twilio Account SID', 'pacifica-core' ), $v );
		$this->secret_row( $g, 'twilio_token', __( 'Twilio Auth Token', 'pacifica-core' ), $v );
		$this->secret_row( $g, 'twilio_from', __( 'Número remitente (From)', 'pacifica-core' ), $v );
		$this->secret_row( $g, 'messaging_service_sid', __( 'Messaging Service SID', 'pacifica-core' ), $v );

		$this->checkbox_row( $g, 'notify_customer', __( 'Notificar al cliente', 'pacifica-core' ), __( 'Enviar SMS al cliente en cada cambio de estado.', 'pacifica-core' ), $v );
		$this->checkbox_row( $g, 'notify_staff', __( 'Notificar al personal', 'pacifica-core' ), __( 'Enviar SMS al personal cuando entra un pedido.', 'pacifica-core' ), $v );

		// Staff numbers — repeatable list.
		$numbers = array_values( array_filter( (array) ( $v['staff_numbers'] ?? array() ) ) );
		if ( empty( $numbers ) ) {
			$numbers = array( '' );
		}
		echo '<tr><th scope="row">' . esc_html__( 'Números del personal', 'pacifica-core' ) . '</th><td>';
		echo '<div class="pacifica-repeatable" data-repeatable="staff">';
		foreach ( $numbers as $number ) {
			$this->repeatable_row( $g . '[staff_numbers][]', (string) $number, 'tel' );
		}
		echo '</div>';
		printf(
			'<button type="button" class="button pacifica-repeatable-add" data-target="staff">%s</button>',
			esc_html__( 'Agregar número', 'pacifica-core' )
		);
		echo '<p class="description">' . esc_html__( 'En formato internacional, p. ej. +5217771234567.', 'pacifica-core' ) . '</p>';
		echo '</td></tr>';

		// Reply map — inbound keyword → status.
		$reply    = (array) ( $v['reply_map'] ?? array() );
		$statuses = $this->status_choices();
		echo '<tr><th scope="row">' . esc_html__( 'Respuestas entrantes', 'pacifica-core' ) . '</th><td><fieldset class="pacifica-reply-map">';
		foreach ( array( '1', '2', '3', '4' ) as $key ) {
			$current = isset( $reply[ $key ] ) ? (string) $reply[ $key ] : '';
			echo '<label class="pacifica-reply-row"><span class="pacifica-reply-key">' . esc_html( $key ) . '</span>';
			printf( '<select name="%1$s[reply_map][%2$s]">', esc_attr( $g ), esc_attr( $key ) );
			foreach ( $statuses as $slug => $label ) {
				printf(
					'<option value="%1$s" %2$s>%3$s</option>',
					esc_attr( $slug ),
					selected( $current, $slug, false ),
					esc_html( $label )
				);
			}
			echo '</select></label>';
		}
		echo '</fieldset></td></tr>';

		echo '</tbody></table>';
	}

	/* ---------------------------------------------------------------------- */
	/* Tab: SEO                                                               */
	/* ---------------------------------------------------------------------- */

	private function render_seo( array $v ): void {
		$g = Options::SEO;
		echo '<table class="form-table" role="presentation"><tbody>';

		$this->media_row( $g, 'default_og_image', __( 'Imagen Open Graph por defecto', 'pacifica-core' ), (int) ( $v['default_og_image'] ?? 0 ) );
		$this->media_row( $g, 'organization_logo', __( 'Logo de la organización', 'pacifica-core' ), (int) ( $v['organization_logo'] ?? 0 ) );
		$this->text_row( $g, 'twitter_handle', __( 'Usuario de X / Twitter', 'pacifica-core' ), $v );

		$ranges  = array( '$', '$$', '$$$', '$$$$' );
		$current = (string) ( $v['price_range'] ?? '$$' );
		echo '<tr><th scope="row"><label for="pf-price_range">' . esc_html__( 'Rango de precios', 'pacifica-core' ) . '</label></th><td>';
		printf( '<select id="pf-price_range" name="%s[price_range]">', esc_attr( $g ) );
		foreach ( $ranges as $r ) {
			printf( '<option value="%1$s" %2$s>%1$s</option>', esc_attr( $r ), selected( $current, $r, false ) );
		}
		echo '</select></td></tr>';

		echo '</tbody></table>';
	}

	/* ---------------------------------------------------------------------- */
	/* Herramientas (outside the Settings-API form; own nonces)               */
	/* ---------------------------------------------------------------------- */

	private function render_tools( array $sms ): void {
		$default_to = '';
		$numbers    = array_values( array_filter( (array) ( $sms['staff_numbers'] ?? array() ) ) );
		if ( ! empty( $numbers ) ) {
			$default_to = (string) $numbers[0];
		}
		?>
		<hr>
		<h2 class="pacifica-tools__title"><?php esc_html_e( 'Herramientas', 'pacifica-core' ); ?></h2>
		<div class="pacifica-tools">
			<div class="pacifica-tool">
				<h3><?php esc_html_e( 'Enviar SMS de prueba', 'pacifica-core' ); ?></h3>
				<p class="description"><?php esc_html_e( 'Envía un mensaje de prueba con la configuración actual de Twilio.', 'pacifica-core' ); ?></p>
				<p>
					<input type="tel" id="pacifica-test-sms-to" class="regular-text" value="<?php echo esc_attr( $default_to ); ?>" placeholder="+5217771234567">
					<button type="button" class="button button-secondary" id="pacifica-test-sms-btn"><?php esc_html_e( 'Enviar prueba', 'pacifica-core' ); ?></button>
				</p>
				<p class="pacifica-test-sms-result" role="status" aria-live="polite"></p>
			</div>

			<div class="pacifica-tool">
				<h3><?php esc_html_e( 'Contenido de demostración', 'pacifica-core' ); ?></h3>
				<p class="description"><?php esc_html_e( 'Instala productos y páginas de ejemplo para arrancar la tienda.', 'pacifica-core' ); ?></p>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<input type="hidden" name="action" value="pacifica_run_content_install">
					<?php wp_nonce_field( 'pacifica_run_content_install' ); ?>
					<button type="submit" class="button button-secondary" data-pacifica-confirm-install><?php esc_html_e( 'Instalar contenido de demostración', 'pacifica-core' ); ?></button>
				</form>
			</div>
		</div>
		<?php
	}

	/* ---------------------------------------------------------------------- */
	/* Field render helpers                                                   */
	/* ---------------------------------------------------------------------- */

	private function text_row( string $group, string $key, string $label, array $values, string $type = 'text' ): void {
		$id  = 'pf-' . $group . '-' . $key;
		$val = (string) ( $values[ $key ] ?? '' );
		echo '<tr><th scope="row"><label for="' . esc_attr( $id ) . '">' . esc_html( $label ) . '</label></th><td>';
		printf(
			'<input type="%1$s" id="%2$s" name="%3$s[%4$s]" value="%5$s" class="regular-text">',
			esc_attr( $type ),
			esc_attr( $id ),
			esc_attr( $group ),
			esc_attr( $key ),
			esc_attr( $val )
		);
		echo '</td></tr>';
	}

	private function number_row( string $group, string $key, string $label, array $values, int $min ): void {
		$id  = 'pf-' . $group . '-' . $key;
		$val = (string) ( $values[ $key ] ?? '' );
		echo '<tr><th scope="row"><label for="' . esc_attr( $id ) . '">' . esc_html( $label ) . '</label></th><td>';
		printf(
			'<input type="number" min="%1$d" step="1" id="%2$s" name="%3$s[%4$s]" value="%5$s" class="small-text">',
			(int) $min,
			esc_attr( $id ),
			esc_attr( $group ),
			esc_attr( $key ),
			esc_attr( $val )
		);
		echo '</td></tr>';
	}

	private function textarea_row( string $group, string $key, string $label, array $values ): void {
		$id  = 'pf-' . $group . '-' . $key;
		$val = (string) ( $values[ $key ] ?? '' );
		echo '<tr><th scope="row"><label for="' . esc_attr( $id ) . '">' . esc_html( $label ) . '</label></th><td>';
		printf(
			'<textarea id="%1$s" name="%2$s[%3$s]" rows="3" class="large-text">%4$s</textarea>',
			esc_attr( $id ),
			esc_attr( $group ),
			esc_attr( $key ),
			esc_textarea( $val )
		);
		echo '</td></tr>';
	}

	private function checkbox_row( string $group, string $key, string $label, string $help, array $values ): void {
		$id = 'pf-' . $group . '-' . $key;
		echo '<tr><th scope="row">' . esc_html( $label ) . '</th><td><label for="' . esc_attr( $id ) . '">';
		printf(
			'<input type="checkbox" id="%1$s" name="%2$s[%3$s]" value="1" %4$s> %5$s',
			esc_attr( $id ),
			esc_attr( $group ),
			esc_attr( $key ),
			checked( ! empty( $values[ $key ] ), true, false ),
			esc_html( $help )
		);
		echo '</label></td></tr>';
	}

	private function repeatable_row( string $name, string $value, string $type ): void {
		echo '<div class="pacifica-repeatable__row">';
		printf(
			'<input type="%1$s" name="%2$s" value="%3$s" class="regular-text">',
			esc_attr( $type ),
			esc_attr( $name ),
			esc_attr( $value )
		);
		printf(
			'<button type="button" class="button-link pacifica-repeatable-remove" aria-label="%s">&times;</button>',
			esc_attr__( 'Quitar', 'pacifica-core' )
		);
		echo '</div>';
	}

	/**
	 * Secret field: masked; read-only + informational when a PACIFICA_TWILIO_* constant defines it.
	 */
	private function secret_row( string $group, string $key, string $label, array $values ): void {
		$id       = 'pf-' . $group . '-' . $key;
		$stored   = (string) ( $values[ $key ] ?? '' );
		$const    = self::SECRET_CONSTANTS[ $key ] ?? '';
		$is_const = '' !== $const && defined( $const ) && '' !== (string) constant( $const );

		echo '<tr><th scope="row"><label for="' . esc_attr( $id ) . '">' . esc_html( $label ) . '</label></th><td>';

		if ( $is_const ) {
			$masked = $this->mask( (string) constant( $const ) );
			printf(
				'<input type="text" id="%1$s" class="regular-text" value="%2$s" readonly disabled>',
				esc_attr( $id ),
				esc_attr( $masked )
			);
			// Preserve the stored (pre-constant) value so saving other fields never wipes it.
			printf(
				'<input type="hidden" name="%1$s[%2$s]" value="%3$s">',
				esc_attr( $group ),
				esc_attr( $key ),
				esc_attr( $stored )
			);
			echo '<p class="description pacifica-const-note"><span class="dashicons dashicons-lock" aria-hidden="true"></span> ';
			printf(
				/* translators: %s: PHP constant name */
				esc_html__( 'Definido mediante la constante %s. Este campo es de solo lectura.', 'pacifica-core' ),
				'<code>' . esc_html( $const ) . '</code>'
			);
			echo '</p>';
		} else {
			printf(
				'<input type="password" id="%1$s" name="%2$s[%3$s]" value="%4$s" class="regular-text" autocomplete="off" spellcheck="false">',
				esc_attr( $id ),
				esc_attr( $group ),
				esc_attr( $key ),
				esc_attr( $stored )
			);
		}
		echo '</td></tr>';
	}

	private function media_row( string $group, string $key, string $label, int $attachment_id ): void {
		$id  = 'pf-' . $group . '-' . $key;
		$src = $attachment_id ? wp_get_attachment_image_url( $attachment_id, 'thumbnail' ) : '';
		echo '<tr><th scope="row">' . esc_html( $label ) . '</th><td>';
		echo '<div class="pacifica-media" data-pacifica-media>';
		printf(
			'<input type="hidden" id="%1$s" name="%2$s[%3$s]" value="%4$d" data-pacifica-media-input>',
			esc_attr( $id ),
			esc_attr( $group ),
			esc_attr( $key ),
			(int) $attachment_id
		);
		printf(
			'<img class="pacifica-media__preview" src="%1$s" alt="" %2$s>',
			esc_url( $src ),
			$src ? '' : 'hidden'
		);
		echo '<span class="pacifica-media__buttons">';
		printf( '<button type="button" class="button pacifica-media-select">%s</button> ', esc_html__( 'Seleccionar imagen', 'pacifica-core' ) );
		printf( '<button type="button" class="button-link pacifica-media-remove" %2$s>%1$s</button>', esc_html__( 'Quitar', 'pacifica-core' ), $src ? '' : 'hidden' );
		echo '</span></div></td></tr>';
	}

	/* ---------------------------------------------------------------------- */
	/* Tools handlers                                                         */
	/* ---------------------------------------------------------------------- */

	/**
	 * Fire the content installer. Nonce action: `pacifica_run_content_install`.
	 * The actual work is owned by the CLI/installer engineer; we just fire the hook.
	 */
	public function handle_content_install(): void {
		if ( ! current_user_can( Activator::CAP ) ) {
			wp_die( esc_html__( 'Permiso denegado.', 'pacifica-core' ), '', array( 'response' => 403 ) );
		}
		check_admin_referer( 'pacifica_run_content_install' );

		/**
		 * Trigger demo/starter content installation.
		 *
		 * @since 1.0.0
		 */
		do_action( 'pacifica_run_content_install' );

		$redirect = add_query_arg(
			array(
				'page'            => self::SLUG,
				'pacifica_notice' => 'content-install',
			),
			admin_url( 'admin.php' )
		);
		wp_safe_redirect( $redirect );
		exit;
	}

	/**
	 * Send a test SMS via TwilioClient. Nonce action: `pacifica_test_sms` (field `nonce`).
	 */
	public function ajax_test_sms(): void {
		check_ajax_referer( 'pacifica_test_sms', 'nonce' );

		if ( ! current_user_can( Activator::CAP ) ) {
			wp_send_json_error( array( 'message' => __( 'Permiso denegado.', 'pacifica-core' ) ), 403 );
		}

		$to = isset( $_POST['to'] ) ? preg_replace( '/[^\d+]/', '', (string) wp_unslash( $_POST['to'] ) ) : '';
		if ( '' === $to ) {
			wp_send_json_error( array( 'message' => __( 'Ingresa un número de destino válido.', 'pacifica-core' ) ), 400 );
		}

		$client_class = 'Pacifica\\Core\\Sms\\TwilioClient';
		if ( ! class_exists( $client_class ) || ! method_exists( $client_class, 'send' ) ) {
			wp_send_json_error( array( 'message' => __( 'El cliente de Twilio aún no está disponible.', 'pacifica-core' ) ), 501 );
		}

		$body = __( 'Pacífica: mensaje de prueba. Todo funciona correctamente.', 'pacifica-core' );

		try {
			$client = new $client_class();
			$result = $client->send( $to, $body );

			// send() always returns array{success:bool,sid:string,error:string} —
			// report the provider's own error verbatim so misconfiguration
			// (bad credentials, unverified number, trial limits) is visible here
			// instead of silently reading as success.
			if ( ! is_array( $result ) || empty( $result['success'] ) ) {
				$error = is_array( $result ) ? (string) ( $result['error'] ?? '' ) : '';
				wp_send_json_error(
					array(
						'message' => '' !== $error
							? sprintf( /* translators: %s: provider error message. */ __( 'No se pudo enviar: %s', 'pacifica-core' ), $error )
							: __( 'No se pudo enviar el mensaje.', 'pacifica-core' ),
					),
					502
				);
			}
			wp_send_json_success(
				array(
					'message' => sprintf( /* translators: %s: Twilio message SID. */ __( 'Mensaje de prueba enviado (%s).', 'pacifica-core' ), (string) ( $result['sid'] ?? '' ) ),
				)
			);
		} catch ( \Throwable $e ) {
			wp_send_json_error( array( 'message' => __( 'Error al enviar el mensaje de prueba.', 'pacifica-core' ) ), 500 );
		}
	}

	public function maybe_render_notice(): void {
		if ( ! isset( $_GET['pacifica_notice'] ) || ! current_user_can( Activator::CAP ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only display flag.
			return;
		}
		$notice = sanitize_key( wp_unslash( $_GET['pacifica_notice'] ) );
		if ( 'content-install' === $notice ) {
			echo '<div class="notice notice-success is-dismissible"><p>';
			esc_html_e( 'Se solicitó la instalación de contenido de demostración.', 'pacifica-core' );
			echo '</p></div>';
		}
	}

	/* ---------------------------------------------------------------------- */
	/* Misc helpers                                                           */
	/* ---------------------------------------------------------------------- */

	/**
	 * @return array<string,string> status slug => label
	 */
	private function status_choices(): array {
		$choices = array();
		if ( function_exists( 'wc_get_order_statuses' ) ) {
			foreach ( wc_get_order_statuses() as $slug => $label ) {
				$choices[ (string) preg_replace( '/^wc-/', '', (string) $slug ) ] = (string) $label;
			}
		}
		// Ensure the custom statuses are always selectable even before Woo registers them.
		$choices += array(
			'preparing' => __( 'Preparando', 'pacifica-core' ),
			'ready'     => __( 'Listo', 'pacifica-core' ),
			'completed' => __( 'Completado', 'pacifica-core' ),
			'cancelled' => __( 'Cancelado', 'pacifica-core' ),
		);
		return $choices;
	}

	private function mask( string $secret ): string {
		$len = strlen( $secret );
		if ( $len <= 4 ) {
			return str_repeat( '•', $len );
		}
		return str_repeat( '•', $len - 4 ) . substr( $secret, -4 );
	}
}
