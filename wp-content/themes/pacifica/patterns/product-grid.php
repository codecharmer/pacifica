<?php
/**
 * Title: Cuadrícula de productos
 * Slug: pacifica/product-grid
 * Categories: pacifica-commerce
 * Description: Colección de productos de WooCommerce con encabezado, estilo premium y consulta configurable.
 * Keywords: productos, tienda, woocommerce, cuadrícula, menú, collection
 * Viewport Width: 1400
 */
?>
<!-- wp:group {"align":"full","backgroundColor":"masa","style":{"spacing":{"padding":{"top":"var:preset|spacing|xxl","bottom":"var:preset|spacing|xxl"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-masa-background-color has-background" style="padding-top:var(--wp--preset--spacing--xxl);padding-bottom:var(--wp--preset--spacing--xxl)"><!-- wp:group {"layout":{"type":"constrained","contentSize":"640px"}} -->
<div class="wp-block-group"><!-- wp:heading {"level":2,"textColor":"clay-deep","className":"is-style-eyebrow","fontSize":"sm"} -->
<h2 class="wp-block-heading is-style-eyebrow has-clay-deep-color has-text-color has-sm-font-size">Nuestro menú</h2>
<!-- /wp:heading -->

<!-- wp:heading {"level":3,"fontSize":"xl"} -->
<h3 class="wp-block-heading has-xl-font-size">Reserva y recoge</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"crust-soft","fontSize":"base"} -->
<p class="has-crust-soft-color has-text-color has-base-font-size">Elige tus piezas y agrega al carrito. Todo es para recoger en Cuernavaca, recién horneado.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:woocommerce/product-collection {"queryId":0,"query":{"perPage":9,"pages":0,"offset":0,"postType":"product","order":"asc","orderBy":"title","search":"","exclude":[],"inherit":false,"taxQuery":{},"isProductCollectionBlock":true,"woocommerceOnSale":false,"woocommerceStockStatus":["instock","onbackorder"],"woocommerceAttributes":[],"woocommerceHandPickedProducts":[]},"tagName":"div","displayLayout":{"type":"flex","columns":3},"style":{"spacing":{"margin":{"top":"var:preset|spacing|xl"}}}} -->
<div class="wp-block-woocommerce-product-collection" style="margin-top:var(--wp--preset--spacing--xl)"><!-- wp:woocommerce/product-template -->
<!-- wp:woocommerce/product-image {"imageSizing":"thumbnail","isDescendentOfQueryLoop":true} /-->

<!-- wp:post-title {"textAlign":"left","level":3,"isLink":true,"__woocommerceNamespace":"woocommerce/product-collection/product-title","fontSize":"lg"} /-->

<!-- wp:woocommerce/product-price {"isDescendentOfQueryLoop":true,"fontSize":"base"} /-->

<!-- wp:woocommerce/product-button {"textAlign":"left","isDescendentOfQueryLoop":true,"fontSize":"base"} /-->
<!-- /wp:woocommerce/product-template -->

<!-- wp:query-pagination {"layout":{"type":"flex","justifyContent":"center"},"style":{"spacing":{"margin":{"top":"var:preset|spacing|lg"}}}} -->
<!-- wp:query-pagination-previous /-->

<!-- wp:query-pagination-numbers /-->

<!-- wp:query-pagination-next /-->
<!-- /wp:query-pagination -->

<!-- wp:woocommerce/product-collection-no-results -->
<!-- wp:paragraph {"align":"center","textColor":"crust-soft"} -->
<p class="has-text-align-center has-crust-soft-color has-text-color">Por ahora no hay piezas disponibles. Vuelve pronto: horneamos todos los días de apertura.</p>
<!-- /wp:paragraph -->
<!-- /wp:woocommerce/product-collection-no-results --></div>
<!-- /wp:woocommerce/product-collection --></div>
<!-- /wp:group -->
