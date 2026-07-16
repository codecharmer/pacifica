<?php
/**
 * Title: Del obrador — editorial con vitrina
 * Slug: pacifica/obrador
 * Categories: pacifica-page
 * Description: Sección editorial asimétrica: fotografía grande del obrador junto a la lista de precios de la vitrina con nombres en display y precios alineados.
 * Keywords: obrador, menu, vitrina, precios, masa madre, editorial
 * Viewport Width: 1400
 */
?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|xxl","bottom":"var:preset|spacing|xxl"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull" style="padding-top:var(--wp--preset--spacing--xxl);padding-bottom:var(--wp--preset--spacing--xxl)"><!-- wp:columns {"verticalAlignment":"center","align":"wide","className":"pf-obrador","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|xl"}}}} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center pf-obrador"><!-- wp:column {"verticalAlignment":"center","width":"52%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:52%"><!-- wp:image {"aspectRatio":"4/5","scale":"cover","sizeSlug":"large"} -->
<figure class="wp-block-image size-large"><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/obrador-pastries.jpg" alt="Kouign-amann dorados y caramelizados en canastas sobre la barra" style="aspect-ratio:4/5;object-fit:cover;border-radius:2px"/></figure>
<!-- /wp:image -->

<!-- wp:paragraph {"className":"pf-tag"} -->
<p class="pf-tag">Recién horneado</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"48%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:48%"><!-- wp:paragraph {"textColor":"rosa-deep","className":"pf-eyebrow"} -->
<p class="pf-eyebrow has-rosa-deep-color has-text-color">Del obrador</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"fontSize":"xxl","style":{"spacing":{"margin":{"top":"var:preset|spacing|xs","bottom":"var:preset|spacing|sm"}}}} -->
<h2 class="wp-block-heading has-xxl-font-size" style="margin-top:var(--wp--preset--spacing--xs);margin-bottom:var(--wp--preset--spacing--sm)">Masa madre, 36 horas, horno de leña.</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"crust-soft","style":{"spacing":{"margin":{"bottom":"var:preset|spacing|md"}}}} -->
<p class="has-crust-soft-color has-text-color" style="margin-bottom:var(--wp--preset--spacing--md)">Fermentamos lento para que la miga tenga carácter y la corteza cante. Cada pieza se lamina, se forma y se hornea a mano — como se ha hecho siempre.</p>
<!-- /wp:paragraph -->

<!-- wp:group {"className":"pf-vitrina","layout":{"type":"default"}} -->
<div class="wp-block-group pf-vitrina"><!-- wp:group {"className":"pf-row","layout":{"type":"default"}} -->
<div class="wp-block-group pf-row"><!-- wp:paragraph {"className":"pf-name"} -->
<p class="pf-name">Croissant de mantequilla<span>Laminado 3 días · 27 capas</span></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"pf-price"} -->
<p class="pf-price"><small>$</small>36</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"pf-row","layout":{"type":"default"}} -->
<div class="wp-block-group pf-row"><!-- wp:paragraph {"className":"pf-name"} -->
<p class="pf-name">Multigrano<span>Masa madre · semillas tostadas</span></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"pf-price"} -->
<p class="pf-price"><small>$</small>73</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"pf-row","layout":{"type":"default"}} -->
<div class="wp-block-group pf-row"><!-- wp:paragraph {"className":"pf-name"} -->
<p class="pf-name">Algarrobo<span>Hogaza de fermentación natural</span></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"pf-price"} -->
<p class="pf-price"><small>$</small>73</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"pf-row","layout":{"type":"default"}} -->
<div class="wp-block-group pf-row"><!-- wp:paragraph {"className":"pf-name"} -->
<p class="pf-name">Frutos secos<span>Nuez, arándano y semillas</span></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"pf-price"} -->
<p class="pf-price"><small>$</small>110</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
