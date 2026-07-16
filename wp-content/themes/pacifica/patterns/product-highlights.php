<?php
/**
 * Title: Productos destacados
 * Slug: pacifica/product-highlights
 * Categories: pacifica-page
 * Description: Editorial de tres productos insignia: Masa Madre, Roles de Canela y Alfajor Peruano.
 * Keywords: productos, destacados, masa madre, roles de canela, alfajor, insignia
 * Viewport Width: 1400
 */
?>
<!-- wp:group {"align":"full","backgroundColor":"masa","style":{"spacing":{"padding":{"top":"var:preset|spacing|xxl","bottom":"var:preset|spacing|xxl"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-masa-background-color has-background" style="padding-top:var(--wp--preset--spacing--xxl);padding-bottom:var(--wp--preset--spacing--xxl)"><!-- wp:group {"layout":{"type":"constrained","contentSize":"640px"}} -->
<div class="wp-block-group"><!-- wp:heading {"level":2,"textColor":"clay-deep","className":"is-style-eyebrow","fontSize":"sm"} -->
<h2 class="wp-block-heading is-style-eyebrow has-clay-deep-color has-text-color has-sm-font-size">La casa recomienda</h2>
<!-- /wp:heading -->

<!-- wp:heading {"level":3,"fontSize":"xl"} -->
<h3 class="wp-block-heading has-xl-font-size">Nuestras piezas insignia</h3>
<!-- /wp:heading --></div>
<!-- /wp:group -->

<!-- wp:columns {"style":{"spacing":{"blockGap":{"top":"var:preset|spacing|lg","left":"var:preset|spacing|lg"},"margin":{"top":"var:preset|spacing|xl"}}}} -->
<div class="wp-block-columns" style="margin-top:var(--wp--preset--spacing--xl)"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:image {"sizeSlug":"large","className":"is-style-framed"} -->
<figure class="wp-block-image size-large is-style-framed"><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/placeholder-producto-masa-madre.svg" alt="Hogaza de masa madre con corteza dorada"/></figure>
<!-- /wp:image -->

<!-- wp:heading {"level":4,"fontSize":"lg","style":{"spacing":{"margin":{"top":"var:preset|spacing|sm"}}}} -->
<h4 class="wp-block-heading has-lg-font-size" style="margin-top:var(--wp--preset--spacing--sm)">Masa Madre</h4>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"crust-soft","fontSize":"base","style":{"typography":{"lineHeight":"1.65"}}} -->
<p class="has-crust-soft-color has-text-color has-base-font-size" style="line-height:1.65">Nuestra hogaza de fermentación lenta. Corteza que cruje, miga aireada y ese punto ácido que solo da el tiempo. El pan del que todo empezó.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:image {"sizeSlug":"large","className":"is-style-framed"} -->
<figure class="wp-block-image size-large is-style-framed"><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/placeholder-producto-roles-canela.svg" alt="Roles de canela glaseados recién horneados"/></figure>
<!-- /wp:image -->

<!-- wp:heading {"level":4,"fontSize":"lg","style":{"spacing":{"margin":{"top":"var:preset|spacing|sm"}}}} -->
<h4 class="wp-block-heading has-lg-font-size" style="margin-top:var(--wp--preset--spacing--sm)">Roles de Canela</h4>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"crust-soft","fontSize":"base","style":{"typography":{"lineHeight":"1.65"}}} -->
<p class="has-crust-soft-color has-text-color has-base-font-size" style="line-height:1.65">Masa suave enrollada con canela y mantequilla, horneada hasta dorar. Tiernos por dentro, con caramelo en cada vuelta. Un clásico de la casa.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:image {"sizeSlug":"large","className":"is-style-framed"} -->
<figure class="wp-block-image size-large is-style-framed"><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/placeholder-producto-alfajor.svg" alt="Alfajor peruano espolvoreado con azúcar glas"/></figure>
<!-- /wp:image -->

<!-- wp:heading {"level":4,"fontSize":"lg","style":{"spacing":{"margin":{"top":"var:preset|spacing|sm"}}}} -->
<h4 class="wp-block-heading has-lg-font-size" style="margin-top:var(--wp--preset--spacing--sm)">Alfajor Peruano</h4>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"crust-soft","fontSize":"base","style":{"typography":{"lineHeight":"1.65"}}} -->
<p class="has-crust-soft-color has-text-color has-base-font-size" style="line-height:1.65">Dos tapas que se deshacen en la boca, unidas por manjar blanco y vestidas de azúcar glas. Delicado, dulce y hecho con paciencia.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
