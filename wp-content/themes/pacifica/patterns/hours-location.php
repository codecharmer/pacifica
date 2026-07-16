<?php
/**
 * Title: Horario y ubicación
 * Slug: pacifica/hours-location
 * Categories: pacifica-page
 * Description: Horario y dirección en ficha de datos, enlazados por el binding pacifica/business, con mapa y botón de indicaciones.
 * Keywords: horario, ubicación, dirección, mapa, contacto, indicaciones
 * Viewport Width: 1400
 */
?>
<!-- wp:group {"align":"full","backgroundColor":"porcelain","className":"pf-band","style":{"spacing":{"padding":{"top":"var:preset|spacing|xxl","bottom":"var:preset|spacing|xxl"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull pf-band has-porcelain-background-color has-background" style="padding-top:var(--wp--preset--spacing--xxl);padding-bottom:var(--wp--preset--spacing--xxl)"><!-- wp:columns {"verticalAlignment":"center","align":"wide","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|xl","left":"var:preset|spacing|xl"}}}} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center","width":"46%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:46%"><!-- wp:group {"className":"pf-head","style":{"spacing":{"blockGap":"0"}},"layout":{"type":"default"}} -->
<div class="wp-block-group pf-head"><!-- wp:paragraph {"className":"pf-eyebrow"} -->
<p class="pf-eyebrow">Visítanos</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"fontSize":"xxl"} -->
<h2 class="wp-block-heading has-xxl-font-size">Horario y ubicación</h2>
<!-- /wp:heading --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"pf-facts","style":{"spacing":{"blockGap":"0","margin":{"top":"var:preset|spacing|lg"}}},"layout":{"type":"default"}} -->
<div class="wp-block-group pf-facts" style="margin-top:var(--wp--preset--spacing--lg)"><!-- wp:paragraph {"className":"pf-facts__k"} -->
<p class="pf-facts__k">Horario</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"metadata":{"bindings":{"content":{"source":"pacifica/business","args":{"key":"hours_summary"}}}},"className":"pf-facts__v"} -->
<p class="pf-facts__v">Miércoles a domingo, 9:00–15:00</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"pf-facts__k"} -->
<p class="pf-facts__k">Cerrado</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"metadata":{"bindings":{"content":{"source":"pacifica/business","args":{"key":"hours_closed"}}}},"className":"pf-facts__v"} -->
<p class="pf-facts__v">Cerrado lunes y martes</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"pf-facts__k"} -->
<p class="pf-facts__k">Dónde</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"metadata":{"bindings":{"content":{"source":"pacifica/business","args":{"key":"address"}}}},"className":"pf-facts__v"} -->
<p class="pf-facts__v">Tulipán 302, Col. Delicias, 62330 Cuernavaca, Morelos</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"pf-facts__k"} -->
<p class="pf-facts__k">Teléfono</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"metadata":{"bindings":{"content":{"source":"pacifica/business","args":{"key":"phone"}}}},"className":"pf-facts__v"} -->
<p class="pf-facts__v">+52 777 773 2179</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:buttons {"style":{"spacing":{"margin":{"top":"var:preset|spacing|lg"}}}} -->
<div class="wp-block-buttons" style="margin-top:var(--wp--preset--spacing--lg)"><!-- wp:button {"className":"is-style-ink","metadata":{"bindings":{"url":{"source":"pacifica/business","args":{"key":"maps_url"}}}}} -->
<div class="wp-block-button is-style-ink"><a class="wp-block-button__link wp-element-button" href="https://maps.google.com/?q=Pac%C3%ADfica+Panader%C3%ADa+Cuernavaca">Cómo llegar</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"54%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:54%"><!-- wp:image {"sizeSlug":"large","className":"pf-figure pf-figure--wide"} -->
<figure class="wp-block-image size-large pf-figure pf-figure--wide"><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/placeholder-mapa.svg" alt="Mapa de la ubicación de Pacífica Panadería en Cuernavaca"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
