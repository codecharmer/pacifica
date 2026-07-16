<?php
/**
 * Title: Producto destacado
 * Slug: pacifica/featured-product
 * Categories: pacifica-commerce
 * Description: Llamado de un solo producto insignia con imagen, descripción y botón de compra.
 * Keywords: producto, destacado, insignia, hero, compra, masa madre
 * Viewport Width: 1400
 */
?>
<!-- wp:group {"align":"full","backgroundColor":"masa-deep","style":{"spacing":{"padding":{"top":"var:preset|spacing|xxl","bottom":"var:preset|spacing|xxl"}}},"layout":{"type":"constrained","contentSize":"1120px"}} -->
<div class="wp-block-group alignfull has-masa-deep-background-color has-background" style="padding-top:var(--wp--preset--spacing--xxl);padding-bottom:var(--wp--preset--spacing--xxl)"><!-- wp:columns {"verticalAlignment":"center","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|xl","left":"var:preset|spacing|xl"}}}} -->
<div class="wp-block-columns are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center","width":"50%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:50%"><!-- wp:image {"sizeSlug":"large","className":"is-style-arch"} -->
<figure class="wp-block-image size-large is-style-arch"><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/placeholder-producto-masa-madre.svg" alt="Hogaza de masa madre partida mostrando la miga"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"50%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:50%"><!-- wp:heading {"level":2,"textColor":"clay-deep","className":"is-style-eyebrow","fontSize":"sm"} -->
<h2 class="wp-block-heading is-style-eyebrow has-clay-deep-color has-text-color has-sm-font-size">La pieza de la casa</h2>
<!-- /wp:heading -->

<!-- wp:heading {"level":3,"fontSize":"xxl"} -->
<h3 class="wp-block-heading has-xxl-font-size">Hogaza de Masa Madre</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"crust-soft","fontSize":"md","style":{"typography":{"lineHeight":"1.7"}}} -->
<p class="has-crust-soft-color has-text-color has-md-font-size" style="line-height:1.7">Fermentada más de veinticuatro horas y horneada en horno de leña. Corteza oscura y crujiente, miga húmeda y aireada, sabor con carácter. Una hogaza que aguanta varios días y solo mejora tostada.</p>
<!-- /wp:paragraph -->

<!-- wp:list {"className":"is-style-checkmarks"} -->
<ul class="wp-block-list is-style-checkmarks"><!-- wp:list-item -->
<li>Fermentación natural, sin levadura comercial</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Horneada en leña el mismo día que la recoges</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Solo harina, agua, sal y tiempo</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list -->

<!-- wp:buttons {"style":{"spacing":{"margin":{"top":"var:preset|spacing|md"}}}} -->
<div class="wp-block-buttons" style="margin-top:var(--wp--preset--spacing--md)"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="/producto/masa-madre">Reservar hogaza</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
