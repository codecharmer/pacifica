<?php
/**
 * Title: Portada — Inicio
 * Slug: pacifica/hero-home
 * Categories: pacifica-hero
 * Description: Portada a sangre completa con antetítulo, título display, subtítulo y dos botones sobre fotografía real, con velo direccional para legibilidad. Pensada para encabezado transparente.
 * Keywords: portada, hero, inicio, masa madre, horno, principal
 * Viewport Width: 1400
 */
?>
<!-- wp:cover {"url":"<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/hero-baker.jpg","gradient":"hero-scrim","dimRatio":100,"minHeight":92,"minHeightUnit":"svh","align":"full","className":"pf-hero"} -->
<div class="wp-block-cover alignfull pf-hero" style="min-height:92svh"><span aria-hidden="true" class="wp-block-cover__background has-background-dim-100 has-background-dim has-background-gradient has-hero-scrim-gradient-background"></span><img class="wp-block-cover__image-background" alt="Panadera colocando croissants recién horneados sobre una rejilla de enfriado" src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/hero-baker.jpg" data-object-fit="cover"/><div class="wp-block-cover__inner-container"><!-- wp:group {"layout":{"type":"default"}} -->
<div class="wp-block-group"><!-- wp:paragraph {"className":"pf-eyebrow"} -->
<p class="pf-eyebrow">Panadería artesanal · Cuernavaca</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1,"fontSize":"display"} -->
<h1 class="wp-block-heading has-display-font-size">El pan, hecho con tiempo.</h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"pf-lede"} -->
<p class="pf-lede">Masa madre viva, laminado a mano y horno de leña. Lo que sale del obrador cada mañana no se apura.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="/carta">Ver la carta</a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"is-style-ghost"} -->
<div class="wp-block-button is-style-ghost"><a class="wp-block-button__link wp-element-button" href="/recoleccion">Reservar recolección</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group --></div></div>
<!-- /wp:cover -->
