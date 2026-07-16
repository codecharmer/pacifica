<?php
/**
 * Seed pages — site content for Pacífica Panadería.
 *
 * Returns an ordered array of page definitions consumed by
 * {@see \Pacifica\Core\Setup\Installer::install_pages()}. Each `content` value
 * is valid block markup that composes the theme's block patterns via
 * `<!-- wp:pattern {"slug":"pacifica/..."} /-->` and inlines original
 * Spanish-first copy where a page needs bespoke text (legal, FAQ, pickup).
 *
 * Pattern slugs referenced here follow the site IA. A referenced pattern that
 * is not yet registered simply renders nothing, so pages degrade gracefully as
 * the theme's pattern library grows.
 *
 * Field reference:
 *   title     string  Page title.
 *   slug      string  post_name.
 *   template  string  '' (default page.html) | 'page-no-title' | 'page-wide'
 *                     | 'template-thank-you'.
 *   status    string  'publish'.
 *   is_front  bool    Whether this page becomes the static front page.
 *   is_blog   bool    (optional) Whether this becomes the posts page.
 *   content   string  Block markup.
 *   seo_short string  Short SEO summary.
 *   meta_description string  <=160 char meta description.
 *
 * @package Pacifica\Core
 * @return array<int,array<string,mixed>>
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * Local block-markup helpers. Defined as closures so re-including this file
 * during idempotent installer re-runs never triggers a redeclaration fatal.
 */

/** Wrap inner markup in a constrained, padded, full-width section group. */
$section = static function ( string $inner, string $bg = 'masa', string $pad = 'xl' ): string {
	return sprintf(
		'<!-- wp:group {"align":"full","backgroundColor":"%1$s","layout":{"type":"constrained"},"style":{"spacing":{"padding":{"top":"var:preset|spacing|%2$s","bottom":"var:preset|spacing|%2$s"}}}} -->' . "\n" .
		'<div class="wp-block-group alignfull has-%1$s-background-color has-background" style="padding-top:var(--wp--preset--spacing--%2$s);padding-bottom:var(--wp--preset--spacing--%2$s)">%3$s</div>' . "\n" .
		'<!-- /wp:group -->',
		$bg,
		$pad,
		$inner
	);
};

/** An eyebrow (small-caps) heading. */
$eyebrow = static function ( string $text ): string {
	return '<!-- wp:heading {"level":2,"textColor":"clay-deep","className":"is-style-eyebrow","fontSize":"sm"} -->' . "\n" .
		'<h2 class="wp-block-heading is-style-eyebrow has-clay-deep-color has-text-color has-sm-font-size">' . $text . '</h2>' . "\n" .
		'<!-- /wp:heading -->';
};

/** A display heading. */
$title = static function ( string $text, int $level = 2, string $size = 'xl' ): string {
	return sprintf(
		'<!-- wp:heading {"level":%1$d,"fontSize":"%2$s"} -->' . "\n" .
		'<h%1$d class="wp-block-heading has-%2$s-font-size">%3$s</h%1$d>' . "\n" .
		'<!-- /wp:heading -->',
		$level,
		$size,
		$text
	);
};

/** A body paragraph. */
$p = static function ( string $text ): string {
	return "<!-- wp:paragraph -->\n<p>" . $text . "</p>\n<!-- /wp:paragraph -->";
};

/** A pattern reference. */
$pattern = static function ( string $slug ): string {
	return sprintf( '<!-- wp:pattern {"slug":"pacifica/%s"} /-->', $slug );
};

/** The "borrador para revisión legal" advisory note. */
$legal_note = static function (): string {
	return '<!-- wp:group {"backgroundColor":"masa-deep","style":{"spacing":{"padding":{"top":"var:preset|spacing|sm","bottom":"var:preset|spacing|sm","left":"var:preset|spacing|md","right":"var:preset|spacing|md"}},"border":{"radius":"10px"}},"layout":{"type":"constrained"}} -->' . "\n" .
		'<div class="wp-block-group has-masa-deep-background-color has-background" style="border-radius:10px;padding-top:var(--wp--preset--spacing--sm);padding-right:var(--wp--preset--spacing--md);padding-bottom:var(--wp--preset--spacing--sm);padding-left:var(--wp--preset--spacing--md)">' . "\n" .
		'<!-- wp:paragraph {"fontSize":"sm"} -->' . "\n" .
		'<p class="has-sm-font-size"><strong>Borrador para revisión legal.</strong> Este texto es una plantilla de referencia redactada para una panadería en México que opera con recolección local y pago en línea. No constituye asesoría jurídica. Antes de publicarlo, un profesional debe revisarlo y adaptarlo a los datos, la razón social y las obligaciones fiscales reales del negocio.</p>' . "\n" .
		'<!-- /wp:paragraph --></div>' . "\n" .
		'<!-- /wp:group -->';
};

/* Convenience joiner. */
$join = static function ( array $blocks ): string {
	return implode( "\n\n", array_filter( $blocks ) );
};

/* ========================================================================== */

return array(

	/* ---------------------------------------------------------------------- */
	/* Inicio (front page)                                                    */
	/* ---------------------------------------------------------------------- */
	array(
		'title'            => 'Inicio',
		'slug'             => 'inicio',
		'template'         => 'page-no-title',
		'status'           => 'publish',
		'is_front'         => true,
		'content'          => $join( array(
			$pattern( 'hero-home' ),
			$pattern( 'product-highlights' ),
			$pattern( 'philosophy' ),
			$pattern( 'process' ),
			$pattern( 'testimonials' ),
			$pattern( 'seasonal' ),
			$pattern( 'catering-cta' ),
			$pattern( 'hours-location' ),
		) ),
		'seo_short'        => 'Pacífica Panadería: pan de masa madre artesanal, bollería y café en Cuernavaca. Reserva en línea y recoge. Artesanal no es una moda.',
		'meta_description' => 'Panadería artesanal de masa madre en Cuernavaca. Pan de fermentación lenta, bollería, dulces y café. Reserva en línea y recoge en Tulipán 302.',
	),

	/* ---------------------------------------------------------------------- */
	/* Nuestra Historia                                                       */
	/* ---------------------------------------------------------------------- */
	array(
		'title'            => 'Nuestra Historia',
		'slug'             => 'nuestra-historia',
		'template'         => '',
		'status'           => 'publish',
		'is_front'         => false,
		'content'          => $join( array(
			$pattern( 'hero-page' ),
			$section( $join( array(
				$eyebrow( 'Cómo empezó todo' ),
				$title( 'Un fermento, una colonia, una costumbre', 2, 'xl' ),
				$p( 'Pacífica nació de una obsesión pequeña y terca: hacer un pan que valiera la pena esperar. Empezamos alimentando una masa madre en una cocina de casa, en la colonia Delicias de Cuernavaca, horneando primero para la familia y después para los vecinos que tocaban la puerta preguntando por más.' ),
				$p( 'Con el tiempo, esa costumbre de barrio se volvió un oficio. Cambiamos la cocina por un obrador, pero conservamos lo esencial: el mismo fermento vivo, la misma paciencia, las mismas manos. No quisimos crecer rápido; quisimos crecer bien.' ),
			) ), 'masa', 'xl' ),
			$pattern( 'story-timeline' ),
			$section( $join( array(
				$eyebrow( 'Lo que no cambia' ),
				$title( 'Artesanal no es una moda', 2, 'xl' ),
				$p( 'Seguimos siendo una panadería de colonia, con una clientela que nos conoce por nombre. Hacemos el pan a mano, un día a la vez, y creemos que la mejor tecnología para un buen pan sigue siendo el tiempo. Esa es toda nuestra historia, y también nuestro plan.' ),
			) ), 'crust', 'xl' ),
			$pattern( 'hours-location' ),
		) ),
		'seo_short'        => 'La historia de Pacífica Panadería: de una masa madre en casa a un obrador de barrio en Cuernavaca. Artesanal no es una moda.',
		'meta_description' => 'Conoce la historia de Pacífica Panadería: de una cocina de casa en la colonia Delicias a un obrador artesanal de masa madre en Cuernavaca.',
	),

	/* ---------------------------------------------------------------------- */
	/* Menú / Tienda                                                          */
	/* ---------------------------------------------------------------------- */
	array(
		'title'            => 'Menú',
		'slug'             => 'menu',
		'template'         => 'page-wide',
		'status'           => 'publish',
		'is_front'         => false,
		'content'          => $join( array(
			$section( $join( array(
				$eyebrow( 'Del horno a tu mesa' ),
				$title( 'Nuestro menú', 1, '2xl' ),
				$p( 'Todo lo que horneamos, reunido en un solo lugar. Reserva en línea las piezas que quieras y elige el día y la hora para recogerlas en Tulipán 302. Los pasteles y algunas cajas se elaboran por encargo con anticipación; el resto sale del horno el mismo día. Todos los productos son para recolección: no hacemos envíos.' ),
			) ), 'masa', 'lg' ),
			$pattern( 'category-tiles' ),
			$section( $join( array(
				$eyebrow( 'Recién horneado' ),
				$title( 'Explora por categoría', 2, 'xl' ),
				'<!-- wp:shortcode -->' . "\n" . '[products limit="12" columns="3" orderby="menu_order" order="ASC"]' . "\n" . '<!-- /wp:shortcode -->',
			) ), 'linen', 'xl' ),
			$pattern( 'order-cta' ),
		) ),
		'seo_short'        => 'El menú completo de Pacífica: panes de masa madre, bollería, dulces, galletas, pasteles por encargo, café y cajas de regalo. Reserva y recoge.',
		'meta_description' => 'Menú de Pacífica Panadería: masa madre, croissants, roles de canela, pasteles por encargo, café y cajas de regalo. Reserva en línea y recoge en Cuernavaca.',
	),

	/* ---------------------------------------------------------------------- */
	/* Filosofía                                                              */
	/* ---------------------------------------------------------------------- */
	array(
		'title'            => 'Filosofía',
		'slug'             => 'filosofia',
		'template'         => '',
		'status'           => 'publish',
		'is_front'         => false,
		'content'          => $join( array(
			$pattern( 'philosophy' ),
			$section( $join( array(
				$eyebrow( 'En lo que creemos' ),
				$title( 'Masa madre, tiempo y fuego', 2, 'xl' ),
				$p( 'Creemos que un buen pan no se apura. La fermentación natural es lenta por naturaleza, y esa lentitud es justamente lo que la hace valiosa: transforma la harina en algo más digerible, más sabroso y más honesto. No usamos levaduras comerciales ni aditivos para acelerar el proceso, porque el atajo siempre se nota.' ),
				$p( 'Creemos en la honestidad de los ingredientes. Pocos, buenos y reconocibles: harina, agua, sal, mantequilla de verdad, fruta de temporada. Si no lo pondríamos en la mesa de nuestra casa, no lo ponemos en el mostrador.' ),
				$p( 'Y creemos en el barrio. Pacífica es de Cuernavaca y para Cuernavaca; horneamos a una escala que nos permite cuidar cada pieza y saludar a quien la compra. Crecer sin perder eso es el reto que nos gusta.' ),
			) ), 'masa', 'xl' ),
			$pattern( 'values-grid' ),
			$pattern( 'process' ),
		) ),
		'seo_short'        => 'La filosofía de Pacífica: fermentación lenta, ingredientes honestos y arraigo en el barrio. Masa madre, tiempo y fuego.',
		'meta_description' => 'La filosofía de Pacífica Panadería: fermentación natural sin atajos, ingredientes honestos y compromiso con el barrio de Cuernavaca.',
	),

	/* ---------------------------------------------------------------------- */
	/* Nuestro Proceso                                                        */
	/* ---------------------------------------------------------------------- */
	array(
		'title'            => 'Nuestro Proceso',
		'slug'             => 'nuestro-proceso',
		'template'         => '',
		'status'           => 'publish',
		'is_front'         => false,
		'content'          => $join( array(
			$pattern( 'hero-page' ),
			$section( $join( array(
				$eyebrow( 'De la harina al horno' ),
				$title( 'Cómo hacemos el pan', 2, 'xl' ),
				$p( 'Todo empieza con el refresco de la masa madre: cada día la alimentamos con harina y agua y esperamos a que despierte, burbujeante y activa. Solo cuando está en su punto amasamos, mezclando los ingredientes con la menor manipulación posible para respetar el gluten.' ),
				$p( 'Después viene la parte más larga y la más importante: el reposo. Dejamos que la masa fermente durante horas, a veces días, plegándola con cuidado para darle fuerza. Es en ese tiempo silencioso donde nace el sabor, el aroma y la miga alveolada.' ),
				$p( 'Formamos cada pieza a mano, la dejamos levar por última vez y la llevamos al horno de piedra a fuego alto, con vapor, para lograr esa corteza que cruje. Nada de esto se puede apurar; cada paso pide su tiempo, y nosotros se lo damos.' ),
			) ), 'masa', 'xl' ),
			$pattern( 'process' ),
			$pattern( 'seasonal' ),
		) ),
		'seo_short'        => 'El proceso de Pacífica, paso a paso: refresco de la masa madre, fermentación lenta, formado a mano y horno de piedra.',
		'meta_description' => 'Descubre el proceso de Pacífica Panadería: masa madre viva, fermentación de hasta dos días, formado a mano y horneado en piedra a fuego alto.',
	),

	/* ---------------------------------------------------------------------- */
	/* Temporada                                                              */
	/* ---------------------------------------------------------------------- */
	array(
		'title'            => 'Temporada',
		'slug'             => 'temporada',
		'template'         => '',
		'status'           => 'publish',
		'is_front'         => false,
		'content'          => $join( array(
			$section( $join( array(
				$eyebrow( 'Lo que da la estación' ),
				$title( 'De temporada', 1, '2xl' ),
				$p( 'La temporada manda. Trabajamos con la fruta que está en su mejor momento, así que nuestra carta de dulces, danesas y bebidas cambia a lo largo del año. Es nuestra forma de mantener el sabor fresco y de rendir homenaje a lo que la tierra ofrece en cada mes.' ),
			) ), 'masa', 'lg' ),
			$pattern( 'seasonal' ),
			$section( $join( array(
				$eyebrow( 'Ediciones limitadas' ),
				$title( 'Aquí un momento, mañana quizá no', 2, 'xl' ),
				$p( 'Muchas de nuestras piezas de temporada se hornean en cantidades pequeñas y por tiempo limitado. Síguenos en Instagram para enterarte de lo que sale del horno cada semana, y reserva pronto: lo bueno de lo estacional es también lo efímero.' ),
			) ), 'linen', 'xl' ),
		) ),
		'seo_short'        => 'Productos de temporada en Pacífica: fruta en su punto, ediciones limitadas y una carta que cambia con las estaciones.',
		'meta_description' => 'Lo de temporada en Pacífica Panadería: danesas, dulces y bebidas con la fruta en su mejor momento. Ediciones limitadas que cambian cada mes.',
	),

	/* ---------------------------------------------------------------------- */
	/* Catering                                                               */
	/* ---------------------------------------------------------------------- */
	array(
		'title'            => 'Catering',
		'slug'             => 'catering',
		'template'         => '',
		'status'           => 'publish',
		'is_front'         => false,
		'content'          => $join( array(
			$pattern( 'catering-cta' ),
			$section( $join( array(
				$eyebrow( 'Para tus eventos' ),
				$title( 'Pan artesanal para compartir', 2, 'xl' ),
				$p( 'Llevamos la mesa de Pacífica a tus reuniones, oficinas y celebraciones. Preparamos cajas y charolas surtidas de pan, bollería y dulces, así como pasteles por encargo, adaptados al número de invitados y a tus preferencias.' ),
				$p( 'Podemos considerar opciones veganas o sin nueces si nos avisas con anticipación. Para pedidos grandes o recurrentes, coordinamos contigo la fecha y la hora de recolección con calma para que todo salga fresco y a tiempo.' ),
				$p( 'Escríbenos a <a href="mailto:hola@pacifica.mx">hola@pacifica.mx</a> o llámanos al <a href="tel:+527777732179">+52 777 773 2179</a> con al menos 48 horas de anticipación y con gusto armamos una propuesta a tu medida.' ),
			) ), 'masa', 'xl' ),
			$pattern( 'order-cta' ),
		) ),
		'seo_short'        => 'Catering artesanal de Pacífica: cajas y charolas de pan, bollería y dulces para eventos y oficinas en Cuernavaca. Por encargo.',
		'meta_description' => 'Servicio de catering de Pacífica Panadería en Cuernavaca: cajas y charolas surtidas de pan artesanal y dulces para eventos y oficinas. Por encargo.',
	),

	/* ---------------------------------------------------------------------- */
	/* Preguntas Frecuentes                                                   */
	/* ---------------------------------------------------------------------- */
	array(
		'title'            => 'Preguntas Frecuentes',
		'slug'             => 'preguntas-frecuentes',
		'template'         => '',
		'status'           => 'publish',
		'is_front'         => false,
		'content'          => $join( array(
			$section( $join( array(
				$eyebrow( 'Antes de reservar' ),
				$title( 'Preguntas frecuentes', 1, '2xl' ),
				$p( '¿Tienes dudas? Aquí respondemos las más comunes. Si no encuentras lo que buscas, escríbenos por Instagram o llámanos con gusto.' ),
			) ), 'masa', 'lg' ),
			$pattern( 'faq' ),
			$section( $join( array(
				$title( '¿Hacen envíos a domicilio?', 3, 'lg' ),
				$p( 'No. Todos nuestros productos son exclusivamente para recolección en nuestro local de Tulipán 302, colonia Delicias, Cuernavaca. Al reservar eliges el día y la hora para recogerlos.' ),
				$title( '¿Cómo reservo y pago?', 3, 'lg' ),
				$p( 'Agrega los productos a tu carrito, elige una fecha y un horario de recolección disponibles, y paga en línea de forma segura con tarjeta a través de Stripe. Recibirás una confirmación con tu número de pedido.' ),
				$title( '¿Con cuánta anticipación debo pedir?', 3, 'lg' ),
				$p( 'Para el pan y la bollería del día, te pedimos reservar con al menos 24 horas de anticipación. Los pasteles y las cajas de catering se elaboran por encargo y requieren al menos 48 horas.' ),
				$title( '¿Qué pasa si no recojo mi pedido?', 3, 'lg' ),
				$p( 'Guardamos tu pedido durante el horario de recolección del día elegido. Como se trata de productos perecederos horneados especialmente para ti, los pedidos no recogidos no son reembolsables. Consulta nuestra Política de Reembolsos para más detalle.' ),
				$title( '¿Tienen opciones veganas o sin nueces?', 3, 'lg' ),
				$p( 'Varios de nuestros panes de masa madre son veganos, y muchos productos no contienen nueces. Cada ficha de producto indica sus atributos. Si tienes una alergia, escríbenos antes de reservar: elaboramos en una cocina donde se manejan gluten, lácteos, huevo y frutos secos, por lo que no podemos garantizar ausencia de trazas.' ),
				$title( '¿Cuáles son sus horarios?', 3, 'lg' ),
				$p( 'Abrimos de miércoles a domingo, de 9:00 a 15:00. Cerramos lunes y martes. Los horarios de recolección disponibles se muestran al momento de reservar.' ),
			) ), 'linen', 'xl' ),
		) ),
		'seo_short'        => 'Preguntas frecuentes de Pacífica: reservas, pago con Stripe, recolección, anticipación, opciones veganas y sin nueces, y horarios.',
		'meta_description' => 'Preguntas frecuentes de Pacífica Panadería: cómo reservar y pagar, recolección en Cuernavaca, tiempos de anticipación, alérgenos y horarios.',
	),

	/* ---------------------------------------------------------------------- */
	/* Contacto                                                               */
	/* ---------------------------------------------------------------------- */
	array(
		'title'            => 'Contacto',
		'slug'             => 'contacto',
		'template'         => '',
		'status'           => 'publish',
		'is_front'         => false,
		'content'          => $join( array(
			$pattern( 'hours-location' ),
			$section( $join( array(
				$eyebrow( 'Escríbenos' ),
				$title( 'Ponte en contacto', 2, 'xl' ),
				$p( '¿Una duda, un encargo especial o simplemente quieres saludar? Llámanos al <a href="tel:+527777732179">+52 777 773 2179</a>, escríbenos a <a href="mailto:hola@pacifica.mx">hola@pacifica.mx</a> o mándanos un mensaje por <a href="https://www.instagram.com/pacifica.mx/" rel="noopener" target="_blank">Instagram</a>. Respondemos dentro de nuestro horario, de miércoles a domingo.' ),
				'<!-- wp:paragraph {"fontSize":"sm","textColor":"stone"} -->' . "\n" . '<p class="has-stone-color has-text-color has-sm-font-size"><em>Formulario de contacto: se integrará aquí un formulario accesible (nombre, correo, mensaje) con protección anti-spam y envío al correo de la panadería. Marcador de posición pendiente de conexión.</em></p>' . "\n" . '<!-- /wp:paragraph -->',
			) ), 'masa', 'xl' ),
			$section( $join( array(
				$eyebrow( 'Cómo llegar' ),
				$title( 'Tulipán 302, Col. Delicias', 2, 'xl' ),
				$p( 'Estamos en Tulipán 302, colonia Delicias, 62330 Cuernavaca, Morelos. Encuentra la ubicación exacta y las indicaciones para llegar en el mapa.' ),
				'<!-- wp:paragraph -->' . "\n" . '<p><a href="https://maps.google.com/?q=Pac%C3%ADfica+Panader%C3%ADa+Cuernavaca" rel="noopener" target="_blank">Abrir en Google Maps</a></p>' . "\n" . '<!-- /wp:paragraph -->',
				'<!-- wp:paragraph {"fontSize":"sm","textColor":"stone"} -->' . "\n" . '<p class="has-stone-color has-text-color has-sm-font-size"><em>Marcador de posición para el mapa embebido. La configuración del bloque de mapa lee la dirección y las coordenadas desde Ajustes → Pacífica → Negocio.</em></p>' . "\n" . '<!-- /wp:paragraph -->',
			) ), 'linen', 'xl' ),
		) ),
		'seo_short'        => 'Contacto de Pacífica Panadería: Tulipán 302, Col. Delicias, Cuernavaca. Teléfono, correo, Instagram y mapa. Miércoles a domingo, 9–15 h.',
		'meta_description' => 'Contacta a Pacífica Panadería en Cuernavaca: Tulipán 302, Col. Delicias. Teléfono +52 777 773 2179, correo, Instagram y ubicación en el mapa.',
	),

	/* ---------------------------------------------------------------------- */
	/* Cómo Recoger                                                           */
	/* ---------------------------------------------------------------------- */
	array(
		'title'            => 'Cómo Recoger',
		'slug'             => 'como-recoger',
		'template'         => '',
		'status'           => 'publish',
		'is_front'         => false,
		'content'          => $join( array(
			$section( $join( array(
				$eyebrow( 'Reserva y recoge' ),
				$title( 'Cómo recoger tu pedido', 1, '2xl' ),
				$p( 'Comprar en Pacífica es sencillo: reservas en línea, pagas de forma segura y pasas por tu pedido el día y la hora que elegiste. No hacemos envíos; así garantizamos que cada pieza llegue a tus manos tan fresca como salió del horno.' ),
			) ), 'masa', 'lg' ),
			$pattern( 'reserve-steps' ),
			$section( $join( array(
				$eyebrow( 'Paso a paso' ),
				$title( 'Los cuatro pasos', 2, 'xl' ),
				'<!-- wp:list {"ordered":true} -->' . "\n" . '<ol class="wp-block-list">' .
					'<!-- wp:list-item --><li><strong>Reserva.</strong> Agrega tus productos al carrito. El pan y la bollería piden al menos 24 horas de anticipación; los pasteles y cajas de catering, al menos 48 horas.</li><!-- /wp:list-item -->' .
					'<!-- wp:list-item --><li><strong>Elige fecha y horario.</strong> En el checkout seleccionas un día abierto y una franja de recolección disponible dentro de nuestro horario.</li><!-- /wp:list-item -->' .
					'<!-- wp:list-item --><li><strong>Paga en línea.</strong> Completa tu pago con tarjeta de forma segura mediante Stripe y recibe la confirmación con tu número de pedido.</li><!-- /wp:list-item -->' .
					'<!-- wp:list-item --><li><strong>Recoge.</strong> Preséntate en Tulipán 302 el día y la hora elegidos y menciona tu número de pedido en el mostrador. Listo.</li><!-- /wp:list-item -->' .
					'</ol>' . "\n" . '<!-- /wp:list -->',
			) ), 'linen', 'xl' ),
			$section( $join( array(
				$eyebrow( 'Dónde y cuándo' ),
				$title( 'Punto de recolección', 2, 'xl' ),
				$p( 'Recoge tu pedido en <strong>Tulipán 302, Col. Delicias, 62330 Cuernavaca, Morelos</strong>. Menciona tu número de pedido en el mostrador.' ),
				$p( 'Horario de recolección: <strong>miércoles a domingo, de 9:00 a 15:00</strong> (última recolección 14:30). Cerramos lunes y martes.' ),
				'<!-- wp:paragraph {"fontSize":"sm","textColor":"stone"} -->' . "\n" . '<p class="has-stone-color has-text-color has-sm-font-size"><em>Los días abiertos, los horarios, el tiempo mínimo de anticipación y la capacidad por franja se configuran en Ajustes → Pacífica → Recolección y se reflejan automáticamente en el checkout.</em></p>' . "\n" . '<!-- /wp:paragraph -->',
			) ), 'masa', 'xl' ),
			$pattern( 'order-cta' ),
		) ),
		'seo_short'        => 'Cómo recoger en Pacífica: reserva en línea, elige día y horario, paga con Stripe y recoge en Tulipán 302, Cuernavaca. Sin envíos.',
		'meta_description' => 'Cómo recoger tu pedido en Pacífica Panadería: reserva, elige fecha y horario, paga con Stripe y recoge en Tulipán 302, Cuernavaca. Sin envíos.',
	),

	/* ---------------------------------------------------------------------- */
	/* Gracias                                                                */
	/* ---------------------------------------------------------------------- */
	array(
		'title'            => 'Gracias',
		'slug'             => 'gracias',
		'template'         => 'template-thank-you',
		'status'           => 'publish',
		'is_front'         => false,
		'content'          => $join( array(
			$section( $join( array(
				$eyebrow( 'Pedido confirmado' ),
				$title( 'Gracias por tu pedido', 1, '2xl' ),
				$p( 'Recibimos tu reserva y ya la estamos preparando con manos y tiempo. Te enviamos por correo la confirmación con tu número de pedido y los detalles de recolección. Consérvalo a la mano para cuando pases por él.' ),
				$p( 'Recuerda: recoges en Tulipán 302, Col. Delicias, en la fecha y el horario que elegiste. Menciona tu número de pedido en el mostrador. Si necesitas hacer un cambio, escríbenos cuanto antes a <a href="mailto:hola@pacifica.mx">hola@pacifica.mx</a> o llámanos al <a href="tel:+527777732179">+52 777 773 2179</a>.' ),
				$p( 'Gracias por apoyar el pan hecho a mano, un día a la vez. Nos vemos pronto.' ),
			) ), 'masa', '2xl' ),
		) ),
		'seo_short'        => 'Gracias por tu pedido en Pacífica Panadería. Confirmación y detalles de recolección en Tulipán 302, Cuernavaca.',
		'meta_description' => 'Gracias por tu pedido en Pacífica Panadería. Te enviamos la confirmación con tu número de pedido y los detalles para recoger en Cuernavaca.',
	),

	/* ---------------------------------------------------------------------- */
	/* Aviso de Privacidad                                                    */
	/* ---------------------------------------------------------------------- */
	array(
		'title'            => 'Aviso de Privacidad',
		'slug'             => 'aviso-de-privacidad',
		'template'         => '',
		'status'           => 'publish',
		'is_front'         => false,
		'content'          => $join( array(
			$section( $join( array(
				$eyebrow( 'Protección de datos' ),
				$title( 'Aviso de Privacidad', 1, '2xl' ),
				$legal_note(),
				$p( 'En Pacífica Panadería ("Pacífica", "nosotros") valoramos tu confianza y protegemos tus datos personales. Este Aviso de Privacidad se emite en cumplimiento de la Ley Federal de Protección de Datos Personales en Posesión de los Particulares (LFPDPPP), su Reglamento y los Lineamientos aplicables en los Estados Unidos Mexicanos.' ),
				$title( '1. Responsable del tratamiento', 2, 'lg' ),
				$p( 'El responsable del tratamiento de tus datos personales es Pacífica Panadería, con domicilio en Tulipán 302, Col. Delicias, 62330 Cuernavaca, Morelos, México. Para cualquier asunto relacionado con tus datos puedes escribirnos a hola@pacifica.mx o llamar al +52 777 773 2179.' ),
				$title( '2. Datos personales que recabamos', 2, 'lg' ),
				$p( 'Para gestionar tus pedidos de recolección recabamos únicamente los datos necesarios: nombre, correo electrónico, número de teléfono, así como los detalles de tu pedido (productos, fecha y horario de recolección). No recabamos datos personales sensibles.' ),
				$p( 'Los datos de tu tarjeta o método de pago NO son recabados ni almacenados por Pacífica: se procesan directamente y de forma cifrada por nuestro procesador de pagos. Nosotros solo recibimos una confirmación del resultado de la transacción.' ),
				$title( '3. Finalidades del tratamiento', 2, 'lg' ),
				$p( 'Finalidades primarias (necesarias para la relación con nosotros): procesar y confirmar tus pedidos; coordinar la fecha y el horario de recolección; enviarte notificaciones sobre el estado de tu pedido por correo electrónico y, si lo autorizas, por mensaje de texto (SMS); atender aclaraciones, cancelaciones y reembolsos; y cumplir obligaciones fiscales y legales.' ),
				$p( 'Finalidades secundarias (opcionales): enviarte comunicaciones sobre novedades, productos de temporada y promociones. Puedes negarte a estas finalidades secundarias sin que ello afecte tus pedidos, indicándolo en cualquier momento a hola@pacifica.mx.' ),
				$title( '4. Transferencias y encargados', 2, 'lg' ),
				$p( 'Para operar utilizamos proveedores que actúan como encargados del tratamiento por cuenta nuestra: Stripe (procesamiento de pagos con tarjeta), nuestro proveedor de mensajería SMS y nuestro proveedor de alojamiento y correo electrónico. Estos terceros tratan tus datos únicamente para prestarnos el servicio y conforme a sus propias políticas de privacidad. No vendemos ni comercializamos tus datos personales. No realizamos transferencias que requieran tu consentimiento salvo las necesarias para cumplir la relación jurídica contigo o cuando la ley lo exija.' ),
				$title( '5. Derechos ARCO', 2, 'lg' ),
				$p( 'Tienes derecho a Acceder a tus datos personales, Rectificarlos cuando sean inexactos, Cancelarlos cuando consideres que no se requieren, y Oponerte a su tratamiento (derechos ARCO), así como a revocar tu consentimiento. Para ejercerlos, envía tu solicitud a hola@pacifica.mx indicando tu nombre, el derecho que deseas ejercer y una forma de contactarte. Responderemos en los plazos que marca la LFPDPPP.' ),
				$title( '6. Conservación de los datos', 2, 'lg' ),
				$p( 'Conservamos tus datos solo durante el tiempo necesario para cumplir las finalidades descritas y las obligaciones legales y fiscales aplicables, tras lo cual se eliminan o anonimizan de forma segura.' ),
				$title( '7. Uso de cookies', 2, 'lg' ),
				$p( 'Nuestro sitio utiliza cookies y tecnologías similares estrictamente necesarias para el funcionamiento del carrito y el proceso de reserva, y, con tu consentimiento, cookies de medición. Puedes gestionar tus preferencias desde la configuración de tu navegador.' ),
				$title( '8. Cambios al aviso', 2, 'lg' ),
				$p( 'Podemos actualizar este Aviso de Privacidad para reflejar cambios legales u operativos. La versión vigente estará siempre disponible en esta página, indicando la fecha de su última actualización.' ),
			) ), 'masa', 'xl' ),
		) ),
		'seo_short'        => 'Aviso de Privacidad de Pacífica Panadería conforme a la LFPDPPP: datos recabados para pedidos de recolección, Stripe como procesador y derechos ARCO.',
		'meta_description' => 'Aviso de Privacidad de Pacífica Panadería (LFPDPPP): qué datos recabamos para tus pedidos de recolección, el uso de Stripe como procesador y tus derechos ARCO.',
	),

	/* ---------------------------------------------------------------------- */
	/* Términos y Condiciones                                                 */
	/* ---------------------------------------------------------------------- */
	array(
		'title'            => 'Términos y Condiciones',
		'slug'             => 'terminos-y-condiciones',
		'template'         => '',
		'status'           => 'publish',
		'is_front'         => false,
		'content'          => $join( array(
			$section( $join( array(
				$eyebrow( 'Reserva y recolección' ),
				$title( 'Términos y Condiciones', 1, '2xl' ),
				$legal_note(),
				$p( 'Estos Términos y Condiciones regulan la compra de productos de Pacífica Panadería a través de este sitio, bajo la modalidad de reserva en línea y recolección en nuestro local. Al realizar un pedido, aceptas estos términos.' ),
				$title( '1. Modalidad: reserva y recolección', 2, 'lg' ),
				$p( 'Todos los productos se venden exclusivamente para recolección en Tulipán 302, Col. Delicias, 62330 Cuernavaca, Morelos. NO realizamos envíos ni entregas a domicilio. Al reservar, seleccionas un día y una franja horaria de recolección disponibles.' ),
				$title( '2. Horarios y anticipación', 2, 'lg' ),
				$p( 'Nuestro horario de recolección es de miércoles a domingo, de 9:00 a 15:00 (última recolección a las 14:30), salvo días de cierre que se anuncien. El pan y la bollería requieren un mínimo de 24 horas de anticipación; los pasteles y las cajas de catering se elaboran por encargo y requieren un mínimo de 48 horas. Las franjas de recolección tienen capacidad limitada y se asignan por orden de reserva.' ),
				$title( '3. Precios y pago', 2, 'lg' ),
				$p( 'Los precios se expresan en pesos mexicanos (MXN) e incluyen los impuestos aplicables, salvo indicación en contrario. Algunos precios pueden mostrarse como estimados y confirmarse antes del cobro en el caso de productos por encargo. El pago se realiza en línea con tarjeta a través de Stripe al momento de la reserva; el pedido se considera confirmado una vez recibido el pago.' ),
				$title( '4. Disponibilidad', 2, 'lg' ),
				$p( 'Trabajamos con producción artesanal y limitada. La disponibilidad de cada producto depende del horneado del día; cuando una pieza se agota, deja de poder reservarse. La composición exacta de las cajas y productos de temporada puede variar según la producción, priorizando siempre la frescura.' ),
				$title( '5. Recolección y no-show', 2, 'lg' ),
				$p( 'Debes recoger tu pedido en la fecha y la franja seleccionadas, presentando tu número de pedido. Conservamos el pedido durante el horario de recolección del día elegido. Al tratarse de productos perecederos elaborados especialmente para ti, los pedidos no recogidos dentro de ese horario (no-show) no son reembolsables ni reprogramables. Ver la Política de Reembolsos.' ),
				$title( '6. Cambios y cancelaciones', 2, 'lg' ),
				$p( 'Puedes solicitar cambios o cancelaciones escribiendo a hola@pacifica.mx o llamando al +52 777 773 2179. Las cancelaciones con al menos 24 horas de anticipación (48 horas para productos por encargo) respecto a tu franja de recolección podrán reembolsarse conforme a la Política de Reembolsos. Fuera de esos plazos no es posible garantizar reembolso, pues la producción ya habrá iniciado.' ),
				$title( '7. Alérgenos', 2, 'lg' ),
				$p( 'Nuestros productos se elaboran en una cocina donde se manejan gluten, lácteos, huevo y frutos secos. Aunque etiquetamos atributos como vegano o sin nueces, no podemos garantizar la ausencia total de trazas. Si tienes una alergia, consúltanos antes de reservar; la decisión de compra es responsabilidad del cliente.' ),
				$title( '8. Responsabilidad', 2, 'lg' ),
				$p( 'Nuestra responsabilidad se limita al valor del pedido. No respondemos por el deterioro de productos perecederos una vez recogidos ni por su conservación inadecuada posterior a la recolección.' ),
				$title( '9. Ley aplicable', 2, 'lg' ),
				$p( 'Estos términos se rigen por la legislación mexicana aplicable, incluida la Ley Federal de Protección al Consumidor. Para cualquier controversia, el consumidor podrá acudir a la Procuraduría Federal del Consumidor (PROFECO).' ),
			) ), 'masa', 'xl' ),
		) ),
		'seo_short'        => 'Términos y Condiciones de Pacífica: reserva y recolección, horarios y anticipación, pago con Stripe, sin envíos, cancelaciones y no-show.',
		'meta_description' => 'Términos y Condiciones de Pacífica Panadería: reserva y recolección local, sin envíos, horarios, pago con Stripe, cancelaciones y política de no-show.',
	),

	/* ---------------------------------------------------------------------- */
	/* Política de Reembolsos                                                 */
	/* ---------------------------------------------------------------------- */
	array(
		'title'            => 'Política de Reembolsos',
		'slug'             => 'politica-de-reembolsos',
		'template'         => '',
		'status'           => 'publish',
		'is_front'         => false,
		'content'          => $join( array(
			$section( $join( array(
				$eyebrow( 'Reembolsos y cancelaciones' ),
				$title( 'Política de Reembolsos', 1, '2xl' ),
				$legal_note(),
				$p( 'Elaboramos productos perecederos, frescos y en muchos casos por encargo. Por su naturaleza, esta política equilibra tu tranquilidad como cliente con la realidad de una producción artesanal que inicia especialmente para cada pedido.' ),
				$title( '1. Productos perecederos', 2, 'lg' ),
				$p( 'Los alimentos frescos horneados no admiten devolución una vez recogidos, salvo defecto de calidad imputable a Pacífica. Esta política aplica sin perjuicio de los derechos que la Ley Federal de Protección al Consumidor te reconoce.' ),
				$title( '2. Cancelación con anticipación', 2, 'lg' ),
				$p( 'Si cancelas con al menos 24 horas de anticipación respecto a tu franja de recolección (o al menos 48 horas para pasteles y cajas de catering por encargo), te reembolsamos el importe pagado en su totalidad. El reembolso se procesa por el mismo medio de pago, a través de Stripe.' ),
				$title( '3. Cancelación tardía', 2, 'lg' ),
				$p( 'Las cancelaciones realizadas fuera de los plazos anteriores no son reembolsables, ya que la producción de tu pedido habrá comenzado con ingredientes reservados para ti. En la medida de lo posible intentaremos ofrecerte una alternativa, pero no podemos garantizarla.' ),
				$title( '4. Pedidos no recogidos (no-show)', 2, 'lg' ),
				$p( 'Los pedidos que no se recojan dentro del horario de recolección del día elegido se consideran no-show y no son reembolsables ni reprogramables, al tratarse de productos perecederos preparados especialmente. Te sugerimos avisarnos cuanto antes si prevés no poder pasar por tu pedido.' ),
				$title( '5. Producto defectuoso o incorrecto', 2, 'lg' ),
				$p( 'Si al recoger tu pedido detectas un defecto de calidad o un error respecto a lo solicitado, avísanos en el momento, en el mostrador, o el mismo día por correo o teléfono, conservando el producto. Verificaremos el caso y, cuando proceda, ofreceremos la reposición del producto o el reembolso correspondiente.' ),
				$title( '6. Cómo solicitar un reembolso', 2, 'lg' ),
				$p( 'Escríbenos a hola@pacifica.mx o llama al +52 777 773 2179, indicando tu número de pedido y el motivo. Los reembolsos aprobados se reflejan por el medio de pago original; el tiempo en que aparecen en tu estado de cuenta depende de tu banco y del procesador (Stripe), habitualmente entre 5 y 10 días hábiles.' ),
			) ), 'masa', 'xl' ),
		) ),
		'seo_short'        => 'Política de Reembolsos de Pacífica: productos perecederos, cancelación con anticipación reembolsable, no-show no reembolsable y reembolsos vía Stripe.',
		'meta_description' => 'Política de Reembolsos de Pacífica Panadería: cancelaciones con anticipación, pedidos perecederos, no-show no reembolsable y reembolsos por Stripe.',
	),

);
