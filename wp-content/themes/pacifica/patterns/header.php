<?php
/**
 * Title: Encabezado
 * Slug: pacifica/header
 * Categories: pacifica-parts
 * Description: Encabezado heredado del esquema anterior. Sin uso: parts/header.html trae su propio marcado desde el rediseño boutique.
 * Keywords: encabezado, header, navegación, menú, logo
 * Viewport Width: 1400
 * Inserter: no
 */

/*
 * LEGACY — no lo referencia ninguna plantilla ni parte, y con `Inserter: no`
 * tampoco puede insertarse desde el editor. El encabezado real vive en
 * parts/header.html. Se conserva para no borrar trabajo sin confirmar;
 * puede eliminarse junto con patterns/footer.php.
 */
?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|sm","bottom":"var:preset|spacing|sm"}}},"layout":{"type":"constrained","contentSize":"1200px"}} -->
<div class="wp-block-group alignfull" style="padding-top:var(--wp--preset--spacing--sm);padding-bottom:var(--wp--preset--spacing--sm)"><!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|md"}},"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"center"}} -->
<div class="wp-block-group"><!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|xs"}},"layout":{"type":"flex","verticalAlignment":"center"}} -->
<div class="wp-block-group"><!-- wp:site-logo {"width":48} /-->

<!-- wp:site-title {"level":0} /--></div>
<!-- /wp:group -->

<!-- wp:navigation {"overlayBackgroundColor":"crust","overlayTextColor":"porcelain","layout":{"type":"flex","justifyContent":"right","flexWrap":"wrap"},"style":{"spacing":{"blockGap":"var:preset|spacing|md"}},"fontSize":"base"} -->
<!-- wp:navigation-link {"label":"Inicio","url":"/"} /-->
<!-- wp:navigation-link {"label":"Menú","url":"/menu"} /-->
<!-- wp:navigation-link {"label":"Historia","url":"/nuestra-historia"} /-->
<!-- wp:navigation-link {"label":"Proceso","url":"/nuestro-proceso"} /-->
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
