<?php
/**
 * Title: Producto destacado
 * Slug: pacifica/featured-product
 * Categories: pacifica-commerce
 * Description: Llamado editorial de un solo producto insignia: fotografía vertical, ficha de rasgos y botón de reserva.
 * Keywords: producto, destacado, insignia, hero, compra, masa madre
 * Viewport Width: 1400
 */
?>
<!-- wp:group {"align":"full","backgroundColor":"porcelain-deep","className":"pf-band","style":{"spacing":{"padding":{"top":"var:preset|spacing|xxl","bottom":"var:preset|spacing|xxl"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull pf-band has-porcelain-deep-background-color has-background" style="padding-top:var(--wp--preset--spacing--xxl);padding-bottom:var(--wp--preset--spacing--xxl)"><!-- wp:columns {"verticalAlignment":"center","align":"wide","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|xl","left":"var:preset|spacing|xl"}}}} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center","width":"48%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:48%"><!-- wp:image {"sizeSlug":"large","className":"pf-figure pf-figure--tall"} -->
<figure class="wp-block-image size-large pf-figure pf-figure--tall"><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/placeholder-producto-masa-madre.svg" alt="Hogaza de masa madre partida mostrando la miga"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"52%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:52%"><!-- wp:group {"className":"pf-head","style":{"spacing":{"blockGap":"0"}},"layout":{"type":"default"}} -->
<div class="wp-block-group pf-head"><!-- wp:paragraph {"className":"pf-eyebrow"} -->
<p class="pf-eyebrow">La pieza de la casa</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"fontSize":"xxl"} -->
<h2 class="wp-block-heading has-xxl-font-size">Hogaza de Masa Madre</h2>
<!-- /wp:heading --></div>
<!-- /wp:group -->

<!-- wp:paragraph {"textColor":"crust-soft","fontSize":"base","style":{"typography":{"lineHeight":"1.7"},"spacing":{"margin":{"top":"var:preset|spacing|md"}}}} -->
<p class="has-crust-soft-color has-text-color has-base-font-size" style="margin-top:var(--wp--preset--spacing--md);line-height:1.7">Fermentada más de veinticuatro horas y horneada en horno de leña. Corteza oscura y crujiente, miga húmeda y aireada, sabor con carácter. Una hogaza que aguanta varios días y solo mejora tostada.</p>
<!-- /wp:paragraph -->

<!-- wp:list {"className":"is-style-checkmarks","style":{"spacing":{"margin":{"top":"var:preset|spacing|md"}}}} -->
<ul class="wp-block-list is-style-checkmarks" style="margin-top:var(--wp--preset--spacing--md)"><!-- wp:list-item -->
<li>Fermentación natural, sin levadura comercial</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Horneada en leña el mismo día que la recoges</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Solo harina, agua, sal y tiempo</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list -->

<!-- wp:buttons {"style":{"spacing":{"margin":{"top":"var:preset|spacing|lg"}}}} -->
<div class="wp-block-buttons" style="margin-top:var(--wp--preset--spacing--lg)"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="/producto/masa-madre">Reservar hogaza</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
