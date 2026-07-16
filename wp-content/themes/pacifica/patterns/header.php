<?php
/**
 * Title: Encabezado
 * Slug: pacifica/header
 * Categories: pacifica-parts
 * Description: Contenido interno del encabezado: logo, navegación principal y botón de pedido. Usado por parts/header.html.
 * Keywords: encabezado, header, navegación, menú, logo
 * Viewport Width: 1400
 * Inserter: no
 */
?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|sm","bottom":"var:preset|spacing|sm"}}},"layout":{"type":"constrained","contentSize":"1200px"}} -->
<div class="wp-block-group alignfull" style="padding-top:var(--wp--preset--spacing--sm);padding-bottom:var(--wp--preset--spacing--sm)"><!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|md"}},"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"center"}} -->
<div class="wp-block-group"><!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|xs"}},"layout":{"type":"flex","verticalAlignment":"center"}} -->
<div class="wp-block-group"><!-- wp:site-logo {"width":48} /-->

<!-- wp:site-title {"level":0} /--></div>
<!-- /wp:group -->

<!-- wp:navigation {"overlayBackgroundColor":"crust","overlayTextColor":"masa","layout":{"type":"flex","justifyContent":"right","flexWrap":"wrap"},"style":{"spacing":{"blockGap":"var:preset|spacing|md"}},"fontSize":"base"} -->
<!-- wp:navigation-link {"label":"Inicio","url":"/"} /-->
<!-- wp:navigation-link {"label":"Menú","url":"/menu"} /-->
<!-- wp:navigation-link {"label":"Historia","url":"/historia"} /-->
<!-- wp:navigation-link {"label":"Proceso","url":"/proceso"} /-->
<!-- wp:navigation-link {"label":"Catering","url":"/catering"} /-->
<!-- wp:navigation-link {"label":"Contacto","url":"/contacto"} /-->
<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"fontSize":"base"} -->
<div class="wp-block-button has-custom-font-size has-base-font-size"><a class="wp-block-button__link wp-element-button" href="/menu">Ordenar</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons -->
<!-- /wp:navigation --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
