# Pacífica — media source folder

This folder is where **authorized Pacífica photography** is dropped so the
installer and the media importer can pull real images into the WordPress media
library — replacing the branded placeholders the theme ships with.

> Instagram and Google image CDNs are login/anti-bot gated, so real photos must
> be supplied by the client. Until then, `MediaImporter` synthesizes a tasteful
> SVG placeholder per `image_key` (brand colors + the product name) so every
> product and section has a featured image and no code changes are needed on
> swap.

## How it works

Every seed product (and several page sections) declares a logical
**`image_key`** — for example `pan-de-masa-madre`. When content is installed,
`MediaImporter::ensure( $image_key, $alt )` resolves that key to an attachment:

1. **Real photo** — if a file exists at
   `data/media/source/{image_key}.{jpg,jpeg,png,webp}`, it is sideloaded into the
   media library with the Spanish alt text and set as the product's featured
   image.
2. **Placeholder** — otherwise a branded `{image_key}.svg` is generated here and
   imported instead.

The `image_key → attachment ID` map is cached in the `pacifica_media_map` option,
so imports are **idempotent** and safe to re-run.

## Dropping in real photos

1. Name each file exactly after its `image_key`, with a raster extension:
   - `pan-de-masa-madre.jpg`
   - `roles-de-canela.webp`
   - `croissant-de-almendra.png`
   Raster files take priority over any generated `.svg` placeholder.
2. Place the files directly in **`data/media/source/`** (this folder).
3. Re-run the importer:
   ```bash
   wp pacifica import-media          # re-import every key, regenerate featured images
   wp pacifica import-media --force  # force re-import even if already cached
   ```
   The importer detects that a real photo now exists where a placeholder was
   used, replaces the attachment, updates the featured image, and generates
   responsive `.webp` (and `.avif`, when Imagick supports it) siblings for every
   registered size.

## The full `image_key` list

Products:

```
pan-de-masa-madre               baguette-de-masa-madre
pan-de-centeno-rustico          pan-de-limon-y-chia
hogaza-integral-de-trigo        pan-de-aceitunas-y-romero
croissant-de-mantequilla        croissant-de-almendra
pain-au-chocolat                concha-de-masa-madre
danes-de-temporada              roles-de-canela
alfajor-peruano                 canele
tarta-de-manzana                flan-de-vainilla
brownie-de-chocolate-amargo     galleta-de-jengibre
galleta-de-avena-y-pasas        galleta-con-chispas-de-chocolate
polvoron-de-naranja             pastel-de-zanahoria
pastel-de-chocolate-y-masa-madre  cheesecake-de-temporada
tres-leches-artesanal           cafe-de-olla
espresso-de-la-casa             latte-de-especias
chocolate-caliente-artesanal    cold-brew-de-temporada
caja-manana-de-domingo          canasta-pacifica
caja-corporativa-de-catering
```

## Notes

- **Formats:** JPEG or WebP are recommended for photos. Keep the longest edge at
  roughly 2000px; WordPress and the importer generate smaller responsive sizes.
- **Licensing:** only drop in imagery you are authorized to use. Placeholders are
  original brand graphics and safe to ship.
- **Alt text** is authored in Spanish alongside each product in
  `data/products.php` and applied automatically on import.
- Generated placeholder `.svg` files may be committed or ignored; the importer
  recreates any that are missing.
