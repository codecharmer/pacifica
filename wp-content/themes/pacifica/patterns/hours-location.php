<?php
/**
 * Title: Horario y ubicación
 * Slug: pacifica/hours-location
 * Categories: pacifica-page
 * Description: Horario y dirección enlazados por el binding pacifica/business, con mapa y botón de indicaciones.
 * Keywords: horario, ubicación, dirección, mapa, contacto, indicaciones
 * Viewport Width: 1400
 */
?>
<!-- wp:group {"align":"full","backgroundColor":"masa","style":{"spacing":{"padding":{"top":"var:preset|spacing|xxl","bottom":"var:preset|spacing|xxl"}}},"layout":{"type":"constrained","contentSize":"1120px"}} -->
<div class="wp-block-group alignfull has-masa-background-color has-background" style="padding-top:var(--wp--preset--spacing--xxl);padding-bottom:var(--wp--preset--spacing--xxl)"><!-- wp:columns {"verticalAlignment":"center","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|xl","left":"var:preset|spacing|xl"}}}} -->
<div class="wp-block-columns are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center","width":"46%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:46%"><!-- wp:heading {"level":2,"textColor":"clay-deep","className":"is-style-eyebrow","fontSize":"sm"} -->
<h2 class="wp-block-heading is-style-eyebrow has-clay-deep-color has-text-color has-sm-font-size">Visítanos</h2>
<!-- /wp:heading -->

<!-- wp:heading {"level":3,"fontSize":"xl"} -->
<h3 class="wp-block-heading has-xl-font-size">Horario y ubicación</h3>
<!-- /wp:heading -->

<!-- wp:heading {"level":4,"fontSize":"base","textColor":"olivo","style":{"spacing":{"margin":{"top":"var:preset|spacing|md","bottom":"var:preset|spacing|xxxs"}}}} -->
<h4 class="wp-block-heading has-olivo-color has-text-color has-base-font-size" style="margin-top:var(--wp--preset--spacing--md);margin-bottom:var(--wp--preset--spacing--xxxs)">Horario</h4>
<!-- /wp:heading -->

<!-- wp:paragraph {"metadata":{"bindings":{"content":{"source":"pacifica/business","args":{"key":"hours_summary"}}}},"textColor":"crust-soft","fontSize":"md"} -->
<p class="has-crust-soft-color has-text-color has-md-font-size">Miércoles a domingo, 9:00–15:00</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"metadata":{"bindings":{"content":{"source":"pacifica/business","args":{"key":"hours_closed"}}}},"textColor":"stone","fontSize":"base"} -->
<p class="has-stone-color has-text-color has-base-font-size">Cerrado lunes y martes</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":4,"fontSize":"base","textColor":"olivo","style":{"spacing":{"margin":{"top":"var:preset|spacing|md","bottom":"var:preset|spacing|xxxs"}}}} -->
<h4 class="wp-block-heading has-olivo-color has-text-color has-base-font-size" style="margin-top:var(--wp--preset--spacing--md);margin-bottom:var(--wp--preset--spacing--xxxs)">Dirección</h4>
<!-- /wp:heading -->

<!-- wp:paragraph {"metadata":{"bindings":{"content":{"source":"pacifica/business","args":{"key":"address"}}}},"textColor":"crust-soft","fontSize":"md"} -->
<p class="has-crust-soft-color has-text-color has-md-font-size">Tulipán 302, Col. Delicias, 62330 Cuernavaca, Morelos</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"metadata":{"bindings":{"content":{"source":"pacifica/business","args":{"key":"phone"}}}},"textColor":"crust-soft","fontSize":"base"} -->
<p class="has-crust-soft-color has-text-color has-base-font-size">+52 777 773 2179</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"style":{"spacing":{"margin":{"top":"var:preset|spacing|md"}}}} -->
<div class="wp-block-buttons" style="margin-top:var(--wp--preset--spacing--md)"><!-- wp:button {"metadata":{"bindings":{"url":{"source":"pacifica/business","args":{"key":"maps_url"}}}}} -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="https://maps.google.com/?q=Pac%C3%ADfica+Panader%C3%ADa+Cuernavaca">Cómo llegar</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"54%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:54%"><!-- wp:image {"sizeSlug":"large","className":"is-style-framed"} -->
<figure class="wp-block-image size-large is-style-framed"><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/placeholder-mapa.svg" alt="Mapa de la ubicación de Pacífica Panadería en Cuernavaca"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
