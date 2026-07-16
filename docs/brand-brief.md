# Pacífica Panadería — Brand & Design System Brief

> This document is the single source of truth for the site's identity and design
> tokens. Every template, pattern, and stylesheet must derive its values from the
> tokens defined here (mirrored in `theme.json`). It also serves as the research
> record for the build.

---

## 1. The business (verified public research)

| Field | Value |
| --- | --- |
| Name | **Pacífica Panadería** |
| Category | Artisan sourdough bakery & café (*panadería artesanal de masa madre*) |
| Location | Tulipán 302 esq., Col. Hule / Delicias, 62330 Cuernavaca, Morelos, México |
| Phone | +52 777 773 2179 |
| Instagram | [@pacifica.mx](https://www.instagram.com/pacifica.mx/) |
| Hours | Wed–Sun 09:00–15:00 · Closed Mon–Tue |
| Reputation | ≈4.6★ · 160+ Google reviews |
| Tagline (observed) | *"Artesanal no es una moda."* — Artisanal is not a trend. |

**Sources:** Google Business Profile / Wanderlog, RestaurantGuru, Nicelocal, Instagram
`@pacifica.mx`. Instagram and Google image CDNs are login/anti-bot gated, so real
photography must be imported by the client via the provided media importer
(`wp pacifica import-media`); the theme ships with labeled placeholders that share
the final filenames so no code changes are needed on swap.

### Customer sentiment (verbatim themes from reviews)
- "The best sourdough bread of my life."
- "Best bread, hand made."
- Cinnamon rolls repeatedly called the best; sourdough is the hero product.
- Perceived as premium, artisanal, worth the price, warm service.

### Positioning
An established, beloved neighborhood *panadería* with a premium artisanal
reputation. The site should feel like the natural digital evolution of a bakery
that already has a loyal following — not a startup launch. Think premium coffee
roaster / luxury chocolatier restraint, applied to Mexican artisan bread.

---

## 2. Brand voice

- **Language:** Spanish-first (primary audience is Cuernavaca locals), warm and
  unpretentious. Content is authored so an English layer can be added later.
- **Tone:** Craft, patience, honesty of ingredients. Confident but never boastful.
  Slow food. Fermentation as philosophy.
- **Do:** speak about time, hands, fire, wheat, fermentation, the neighborhood.
- **Don't:** rewrite Instagram captions, use generic bakery clichés, over-promise.

Voice anchors:
> *Masa madre, tiempo y fuego.* — Sourdough, time, and fire.
> *Hecho a mano, un día a la vez.* — Handmade, one day at a time.

---

## 3. Design tokens

These are mirrored verbatim in `wp-content/themes/pacifica/theme.json`. Never
hardcode a hex value in a template — reference the CSS custom property or the
theme.json preset slug.

### 3.1 Color palette — "Horno & Trigo" (Oven & Wheat)

| Token (slug) | Name | Hex | Role |
| --- | --- | --- | --- |
| `masa` | Masa (flour cream) | `#F6EFE4` | Primary background |
| `masa-deep` | Masa Deep | `#EDE3D2` | Secondary surface / cards |
| `crust` | Crust | `#2E2016` | Primary text / dark sections |
| `crust-soft` | Crust Soft | `#4A3626` | Body text on light |
| `clay` | Clay (terracotta) | `#B4643F` | Primary brand / CTAs |
| `clay-deep` | Clay Deep | `#8F4A2C` | CTA hover / accents |
| `olivo` | Olivo (olive) | `#6E6B4A` | Secondary accent / nature |
| `trigo` | Trigo (wheat gold) | `#D8A44A` | Highlight / seasonal |
| `ember` | Ember | `#C6733A` | Warm gradient stop |
| `linen` | Linen | `#FBF7F0` | Elevated surface / white-ish |
| `ink` | Ink | `#1C140D` | Max-contrast text |
| `stone` | Stone | `#9A8E7C` | Muted text / borders |

Semantic gradients: `oven` = 135° `ember → clay-deep`. `sunrise` = 160° `trigo → clay`.

Contrast: `crust`/`crust-soft`/`ink` on `masa`/`linen` all exceed WCAG AA (7:1+).
`linen` text on `clay`/`clay-deep` passes AA for large + normal (verify in
`docs/verification.md`).

### 3.2 Typography

| Role | Family | Notes |
| --- | --- | --- |
| Display / headings | **Fraunces** (variable, opsz+wght+SOFT) | Warm, high-craft serif. Self-hosted (`assets/fonts`). |
| Body / UI | **Inter** (variable) | Humanist sans, excellent legibility. Self-hosted. |
| Accent / eyebrow | Fraunces italic, letter-spaced small caps | Used for eyebrows/labels. |

Fluid type scale (clamp, mobile → desktop):
`sm .875→.9rem` · `base 1→1.0625rem` · `md 1.125→1.25rem` · `lg 1.375→1.75rem` ·
`xl 1.75→2.5rem` · `2xl 2.25→3.5rem` · `3xl 2.75→4.75rem` · `display 3.25→6rem`.

Body line-height 1.65; headings 1.05–1.15; measure max 66ch.

> Font binaries are **not** committed (licensing). `assets/fonts/README.md`
> lists the exact files to drop in; `theme.json` + `@font-face` reference them and
> gracefully fall back to `Georgia`/`system-ui` until present.

### 3.3 Spacing scale (rem)

`3xs .25` · `2xs .5` · `xs .75` · `sm 1` · `md 1.5` · `lg 2.5` · `xl 4` · `2xl 6` · `3xl 9`.
Section rhythm uses `xl`–`3xl`. `contentSize 720px`, `wideSize 1200px`.

### 3.4 Radius, shadow, motion

- Radius: `sm 4px` · `md 10px` · `lg 20px` · `pill 999px`.
- Shadow: `soft 0 2px 8px rgba(28,20,13,.06)` · `lift 0 16px 40px -18px rgba(46,32,22,.28)`.
- Motion: durations `fast 160ms` · `base 280ms` · `slow 520ms`; easing
  `standard cubic-bezier(.2,.6,.2,1)`. **All motion respects
  `prefers-reduced-motion`.** No parallax, no autoplay carousels.

---

## 4. Information architecture

Pages: Home · Nuestra Historia (About) · Menú / Tienda (shop) · Filosofía
(philosophy) · Nuestro Proceso (process) · Temporada (seasonal) · Catering ·
Preguntas Frecuentes (FAQ) · Contacto · Cómo Recoger (pickup info) · legal
(Privacidad, Términos, Reembolsos) · Confirmación de Pedido · Gracias · 404.

Primary nav: Inicio · Menú · Historia · Proceso · Catering · Contacto · [Ordenar].
Footer: hours, address, map, Instagram, newsletter, legal, secondary nav.

---

## 5. Product taxonomy (WooCommerce)

Categories (`product_cat`):
1. **Panes de Masa Madre** — sourdough loaves (hero)
2. **Bollería & Croissants** — viennoiserie
3. **Dulces & Postres** — sweets/pastries (cinnamon rolls, alfajor, canelé)
4. **Galletas** — cookies
5. **Pasteles** — cakes (pre-order)
6. **Café & Bebidas** — coffee/drinks
7. **Cajas & Regalo** — gift boxes / catering bundles

Attributes: `masa-madre` (yes), `vegano`, `sin-nueces`, `de-temporada`.
All physical goods are **pickup-only** (reserva y recoge) — no shipping.
Prices are in **MXN**; estimated prices are tagged with meta `_pacifica_price_estimate=1`
and surfaced in admin as "revisar precio" until the client confirms.

See `wp-content/plugins/pacifica-core/data/products.php` for the full catalog.

---

## 6. Ordering model (reserve & pickup)

1. Browse products → add to cart (stock-managed, "Agotado" when 0).
2. Cart/checkout collects **pickup date** (must be an open day, ≥ lead time) and
   **pickup time slot** (within business hours, capacity-limited per slot).
3. Pay online via **Stripe** (WooCommerce Stripe gateway).
4. Order confirmation page + email + SMS.
5. Stock decrements automatically; sold-out hides the buy button.

Lead time, open days, slot length, and slot capacity are all editable in
**Ajustes → Pacífica → Recolección**. No hardcoded scheduling.

---

## 7. SMS workflow (Twilio)

- On new paid order → SMS to bakery staff numbers:
  `NUEVO PEDIDO / #{id} / {cliente} / {items} / Recoge: {fecha hora}` + reply key
  `1=Preparando 2=Listo 3=Entregado 4=Cancelado`.
- Inbound SMS webhook maps replies → WooCommerce status transitions.
- Each customer-facing status change → automatic SMS to the customer.
- All inbound/outbound messages logged to a custom table + admin "SMS" screen.
- Signature validation on the Twilio webhook; numbers/keywords configurable.

---

## 8. Engineering conventions

- **Namespace:** PHP `Pacifica\Core\…` (plugin), theme prefix `pacifica_`.
- **Text domains:** theme `pacifica`, plugin `pacifica-core`.
- **CSS custom props:** `--pf-color-*`, `--pf-font-*`, `--pf-space-*`, `--pf-radius-*`.
- **No business logic in templates.** Templates are block markup; dynamic data
  comes from blocks, patterns, or plugin-provided block bindings / shortcodes.
- **Escaping:** every output escaped (`esc_html`, `esc_attr`, `esc_url`, `wp_kses_post`).
- **Nonces + capability checks** on every write path. Sanitize all input.
- **i18n:** all strings translatable. **a11y:** WCAG 2.1 AA, visible focus,
  landmarks, skip link, reduced motion.
- **Build:** `@wordpress/scripts` (ESNext + SCSS) → `assets/`. Sources in `src/`.
- **PHP 8.3+, WordPress 6.8+, WooCommerce 8+.** Typed properties, enums, `declare(strict_types=1)`.
