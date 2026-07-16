<?php
/**
 * Production calendar.
 *
 * Groups upcoming pickup orders by date + slot and aggregates line items so the
 * bakers know exactly what to bake ("12× Masa Madre, 8× Roles de Canela"). Reads
 * orders through wc_get_orders (HPOS-safe) filtered on the `_pacifica_pickup_date`
 * meta. Includes a printable layout.
 *
 * @package Pacifica\Core
 */

declare( strict_types=1 );

namespace Pacifica\Core\Admin;

use Pacifica\Core\Contracts\Bootable;
use Pacifica\Core\Setup\Activator;
use Pacifica\Core\Setup\Options;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class ProductionCalendar implements Bootable {

	private const SLUG = 'pacifica-calendario';

	public function boot(): void {
		add_action( 'admin_menu', array( $this, 'register_menu' ), 10 );
	}

	public function register_menu(): void {
		add_submenu_page(
			Dashboard::SLUG,
			__( 'Calendario de producción', 'pacifica-core' ),
			__( 'Calendario de producción', 'pacifica-core' ),
			Activator::CAP,
			self::SLUG,
			array( $this, 'render' )
		);
	}

	public function render(): void {
		if ( ! current_user_can( Activator::CAP ) ) {
			wp_die( esc_html__( 'No tienes permiso para ver esta página.', 'pacifica-core' ) );
		}

		$tz         = Options::timezone();
		$days       = 7;
		$offset     = isset( $_GET['pf_offset'] ) ? (int) $_GET['pf_offset'] : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only navigation.
		$start      = ( new \DateTimeImmutable( 'today', $tz ) )->modify( sprintf( '%+d days', $offset * $days ) );
		$end        = $start->modify( sprintf( '+%d days', $days - 1 ) );
		$start_date = $start->format( 'Y-m-d' );
		$end_date   = $end->format( 'Y-m-d' );

		$grouped = function_exists( 'wc_get_orders' )
			? $this->grouped_orders( $start_date, $end_date )
			: array();

		$base_url = admin_url( 'admin.php?page=' . self::SLUG );
		?>
		<div class="wrap pacifica-wrap pacifica-calendar">
			<h1 class="pacifica-title">
				<span class="dashicons dashicons-calendar-alt" aria-hidden="true"></span>
				<?php esc_html_e( 'Calendario de producción', 'pacifica-core' ); ?>
			</h1>

			<div class="pacifica-toolbar pacifica-no-print">
				<a class="button" href="<?php echo esc_url( add_query_arg( 'pf_offset', $offset - 1, $base_url ) ); ?>">&laquo; <?php esc_html_e( 'Semana anterior', 'pacifica-core' ); ?></a>
				<a class="button" href="<?php echo esc_url( add_query_arg( 'pf_offset', 0, $base_url ) ); ?>"><?php esc_html_e( 'Esta semana', 'pacifica-core' ); ?></a>
				<a class="button" href="<?php echo esc_url( add_query_arg( 'pf_offset', $offset + 1, $base_url ) ); ?>"><?php esc_html_e( 'Semana siguiente', 'pacifica-core' ); ?> &raquo;</a>
				<span class="pacifica-range">
					<?php echo esc_html( wp_date( 'j M', $start->getTimestamp() ) . ' – ' . wp_date( 'j M Y', $end->getTimestamp() ) ); ?>
				</span>
				<button type="button" class="button button-primary pacifica-print" data-pacifica-print><?php esc_html_e( 'Imprimir', 'pacifica-core' ); ?></button>
			</div>

			<div class="pacifica-print-only pacifica-print-head">
				<strong><?php esc_html_e( 'Pacífica Panadería — Plan de producción', 'pacifica-core' ); ?></strong>
				<span><?php echo esc_html( wp_date( 'j M', $start->getTimestamp() ) . ' – ' . wp_date( 'j M Y', $end->getTimestamp() ) ); ?></span>
			</div>

			<?php if ( empty( $grouped ) ) : ?>
				<p class="pacifica-empty"><?php esc_html_e( 'No hay pedidos programados para este rango.', 'pacifica-core' ); ?></p>
			<?php else : ?>
				<div class="pacifica-calendar__grid">
					<?php foreach ( $grouped as $date => $data ) : ?>
						<section class="pacifica-day">
							<header class="pacifica-day__head">
								<h2><?php echo esc_html( ucfirst( (string) wp_date( 'l j \d\e F', ( new \DateTimeImmutable( $date, $tz ) )->getTimestamp() ) ) ); ?></h2>
								<span class="pacifica-day__count"><?php echo esc_html( sprintf( /* translators: %d: order count */ _n( '%d pedido', '%d pedidos', $data['order_count'], 'pacifica-core' ), $data['order_count'] ) ); ?></span>
							</header>

							<div class="pacifica-day__totals">
								<h3><?php esc_html_e( 'Para hornear', 'pacifica-core' ); ?></h3>
								<ul class="pacifica-bake-list">
									<?php foreach ( $data['totals'] as $name => $qty ) : ?>
										<li><span class="pacifica-qty"><?php echo esc_html( (string) $qty ); ?>×</span> <?php echo esc_html( $name ); ?></li>
									<?php endforeach; ?>
								</ul>
							</div>

							<div class="pacifica-day__slots">
								<?php foreach ( $data['slots'] as $slot => $orders ) : ?>
									<div class="pacifica-slot">
										<h4 class="pacifica-slot__time"><?php echo esc_html( '' !== $slot ? $slot : __( 'Sin horario', 'pacifica-core' ) ); ?></h4>
										<ul class="pacifica-slot__orders">
											<?php foreach ( $orders as $entry ) : ?>
												<li>
													<span class="pacifica-slot__order">#<?php echo esc_html( (string) $entry['id'] ); ?></span>
													<span class="pacifica-slot__customer"><?php echo esc_html( $entry['customer'] ); ?></span>
													<span class="pacifica-slot__items"><?php echo esc_html( implode( ', ', $entry['items'] ) ); ?></span>
												</li>
											<?php endforeach; ?>
										</ul>
									</div>
								<?php endforeach; ?>
							</div>
						</section>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Fetch and group orders in the date range by pickup date → slot, with per-day
	 * aggregated item totals.
	 *
	 * @return array<string,array{order_count:int,totals:array<string,int>,slots:array<string,array<int,array{id:int,customer:string,items:string[]}>>}>
	 */
	private function grouped_orders( string $start_date, string $end_date ): array {
		$orders = wc_get_orders(
			array(
				'limit'      => -1,
				'status'     => array( 'processing', 'preparing', 'ready', 'on-hold' ),
				'meta_query' => array(
					array(
						'key'     => '_pacifica_pickup_date',
						'value'   => array( $start_date, $end_date ),
						'compare' => 'BETWEEN',
						'type'    => 'DATE',
					),
				),
			)
		);
		$orders = is_array( $orders ) ? $orders : array();

		$grouped = array();
		foreach ( $orders as $order ) {
			$date = (string) $order->get_meta( '_pacifica_pickup_date' );
			$slot = (string) $order->get_meta( '_pacifica_pickup_slot' );
			if ( '' === $date ) {
				continue;
			}
			if ( ! isset( $grouped[ $date ] ) ) {
				$grouped[ $date ] = array(
					'order_count' => 0,
					'totals'      => array(),
					'slots'       => array(),
				);
			}

			$items = array();
			foreach ( $order->get_items() as $item ) {
				$name = $item->get_name();
				$qty  = (int) $item->get_quantity();
				$items[] = $qty . '× ' . $name;
				$grouped[ $date ]['totals'][ $name ] = ( $grouped[ $date ]['totals'][ $name ] ?? 0 ) + $qty;
			}

			$customer = trim( $order->get_formatted_billing_full_name() );
			$grouped[ $date ]['slots'][ $slot ][] = array(
				'id'       => $order->get_id(),
				'customer' => '' !== $customer ? $customer : __( 'Invitado', 'pacifica-core' ),
				'items'    => $items,
			);
			++$grouped[ $date ]['order_count'];
		}

		ksort( $grouped );
		foreach ( $grouped as &$day ) {
			ksort( $day['slots'] );
			arsort( $day['totals'] );
		}
		unset( $day );

		return $grouped;
	}
}
