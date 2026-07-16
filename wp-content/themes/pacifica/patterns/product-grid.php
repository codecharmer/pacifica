<?php
/**
 * Title: Cuadrícula de productos
 * Slug: pacifica/product-grid
 * Categories: pacifica-commerce
 * Description: Colección de productos de WooCommerce con encabezado editorial, tarjetas en display y consulta configurable.
 * Keywords: productos, tienda, woocommerce, cuadrícula, menú, collection
 * Viewport Width: 1400
 */
?>
<!-- wp:group {"align":"full","backgroundColor":"porcelain","className":"pf-band","style":{"spacing":{"padding":{"top":"var:preset|spacing|xxl","bottom":"var:preset|spacing|xxl"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull pf-band has-porcelain-background-color has-background" style="padding-top:var(--wp--preset--spacing--xxl);padding-bottom:var(--wp--preset--spacing--xxl)"><!-- wp:group {"className":"pf-head","style":{"spacing":{"blockGap":"0"}},"layout":{"type":"default"}} -->
<div class="wp-block-group pf-head"><!-- wp:paragraph {"className":"pf-eyebrow"} -->
<p class="pf-eyebrow">Nuestro menú</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"fontSize":"xxl"} -->
<h2 class="wp-block-heading has-xxl-font-size">Reserva y recoge</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"pf-lede"} -->
<p class="pf-lede">Elige tus piezas y agrega al carrito. Todo es para recoger en Cuernavaca, recién horneado.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:woocommerce/product-collection {"queryId":0,"query":{"perPage":9,"pages":0,"offset":0,"postType":"product","order":"asc","orderBy":"title","search":"","exclude":[],"inherit":false,"taxQuery":{},"isProductCollectionBlock":true,"woocommerceOnSale":false,"woocommerceStockStatus":["instock","onbackorder"],"woocommerceAttributes":[],"woocommerceHandPickedProducts":[]},"tagName":"div","align":"wide","className":"pf-product-grid","displayLayout":{"type":"flex","columns":3},"style":{"spacing":{"margin":{"top":"var:preset|spacing|xl"}}}} -->
<div class="wp-block-woocommerce-product-collection alignwide pf-product-grid" style="margin-top:var(--wp--preset--spacing--xl)"><!-- wp:woocommerce/product-template -->
<!-- wp:group {"className":"pf-product-card","style":{"spacing":{"blockGap":"var:preset|spacing|xs"}},"layout":{"type":"default"}} -->
<div class="wp-block-group pf-product-card"><!-- wp:woocommerce/product-image {"imageSizing":"thumbnail","aspectRatio":"4/5","scale":"cover","isDescendentOfQueryLoop":true} /-->

<!-- wp:post-title {"textAlign":"left","level":3,"isLink":true,"__woocommerceNamespace":"woocommerce/product-collection/product-title","fontSize":"lg"} /-->

<!-- wp:woocommerce/product-price {"isDescendentOfQueryLoop":true,"fontSize":"base"} /-->

<!-- wp:woocommerce/product-button {"textAlign":"left","isDescendentOfQueryLoop":true,"fontSize":"sm"} /--></div>
<!-- /wp:group -->
<!-- /wp:woocommerce/product-template -->

<!-- wp:query-pagination {"className":"pf-pagination","layout":{"type":"flex","justifyContent":"center"},"style":{"spacing":{"margin":{"top":"var:preset|spacing|xl"}}}} -->
<!-- wp:query-pagination-previous {"label":"Anterior"} /-->

<!-- wp:query-pagination-numbers /-->

<!-- wp:query-pagination-next {"label":"Siguiente"} /-->
<!-- /wp:query-pagination -->

<!-- wp:woocommerce/product-collection-no-results -->
<!-- wp:paragraph {"align":"center","textColor":"crust-soft"} -->
<p class="has-text-align-center has-crust-soft-color has-text-color">Por ahora no hay piezas disponibles. Vuelve pronto: horneamos todos los días de apertura.</p>
<!-- /wp:paragraph -->
<!-- /wp:woocommerce/product-collection-no-results --></div>
<!-- /wp:woocommerce/product-collection --></div>
<!-- /wp:group -->
