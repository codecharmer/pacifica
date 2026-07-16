# Architecture

How the Pacífica site is put together, why it is split the way it is, and where to
extend it. Pair this with [`brand-brief.md`](brand-brief.md) (the design-token and
content source of truth) and [`setup.md`](setup.md) (install steps).

---

## 1. Guiding principle: theme presents, plugin decides

There is exactly one architectural rule everything else follows:

> **The theme is presentation-only. All business logic lives in `pacifica-core`.**

The theme (`functions.php` and its `inc/` includes) wires up theme supports, enqueues
assets, registers patterns/block-styles, and exposes **block bindings** so editors can
surface business data (address, hours, phone) without hardcoding it. It contains no
ordering, payment, SMS, or data logic. That lets the client restyle or replace the
theme without endangering commerce, and lets us ship plugin upgrades without touching
templates.

---

## 2. Directory tree

```
pacifica/
├── composer.json                 # Root PHP tooling (phpcs, phpstan, stubs)
├── phpcs.xml.dist                # WordPress + WordPress-Extra + PHP 8.3 compat ruleset
├── phpstan.neon.dist             # Static analysis, level 6, WP/Woo stubs
├── package.json                  # npm workspaces root (delegates to theme)
├── .wp-env.json                  # Local WordPress (Docker) definition
├── .nvmrc / .editorconfig / .gitignore
├── .github/workflows/ci.yml      # Lint + static analysis on push/PR
├── docs/                         # This documentation set
└── wp-content/
    ├── themes/pacifica/
    │   ├── style.css             # Theme header only (tokens live in theme.json)
    │   ├── theme.json            # v3. Color, type, spacing, shadow, radius tokens
    │   ├── functions.php         # Bootstrap → requires inc/*
    │   ├── inc/
    │   │   ├── setup.php          # add_theme_support, nav, image sizes
    │   │   ├── assets.php         # Enqueue assets/css/*.css + assets/js/enhance.js, font preload
    │   │   ├── block-styles.php   # register_block_style()
    │   │   ├── patterns.php       # Pattern categories / registration
    │   │   └── block-bindings.php # pacifica/business binding source
    │   ├── templates/            # front-page.html, page templates (block markup)
    │   ├── parts/                # header, header-transparent, footer, footer-minimal
    │   ├── patterns/             # hero, philosophy, process, faq, testimonials…
    │   ├── styles/              # theme.json style variations
    │   ├── assets/
    │   │   ├── css/              # HAND-AUTHORED runtime CSS (theme/woocommerce/editor)
    │   │   ├── js/               # HAND-AUTHORED enhance.js (dependency-free)
    │   │   ├── fonts/            # Self-hosted Fraunces + Inter (binaries not committed)
    │   │   ├── images/           # Theme chrome imagery
    │   │   └── build/            # @wordpress/scripts output (gitignored)
    │   └── src/
    │       ├── js/index.js       # Optional compiled JS entry
    │       └── scss/index.scss   # Optional compiled SCSS entry
    └── plugins/pacifica-core/
        ├── pacifica-core.php     # Bootstrap (see §4)
        ├── composer.json         # PSR-4 autoload + dev tooling
        ├── src/                  # Pacifica\Core\… (see §5)
        ├── data/                 # Seed catalog + content for `wp pacifica install`
        │   └── media/            # Placeholder photos + importer targets
        ├── templates/emails/     # Transactional email/SMS-adjacent templates
        ├── assets/{css,js}/      # Admin dashboard styles/scripts
        └── languages/            # .pot / translations (text domain pacifica-core)
```

---

## 3. The theme (FSE)

### 3.1 Tokens (`theme.json`, version 3)

Every color, font, size, space, shadow, and radius is a preset. Templates and CSS
reference the emitted custom properties (`--wp--preset--color--clay`,
`--wp--custom--radius--md`, …) — **never a raw hex value**. Default palettes,
gradients, and font sizes are disabled so only the "Horno & Trigo" system is offered
in the editor. Fluid typography is on; the two variable fonts (Fraunces, Inter) are
self-hosted via `@font-face` and gracefully fall back to `Georgia`/`system-ui` until
the binaries are present.

### 3.2 Templates, parts, patterns

- **Templates** (`templates/*.html`) and **template parts** (`parts/*.html`) are pure
  block markup. `theme.json` registers custom templates (`page-no-title`, `page-wide`,
  `template-thank-you`) and four template-part areas (two headers, two footers).
- **Patterns** (`patterns/*.php`) are the composable building blocks — hero variants,
  philosophy, process, seasonal, product highlights, testimonials, FAQ,
  hours/location. They are content, not logic.

### 3.3 Block bindings — `pacifica/business`

Business facts (address, hours, phone, Instagram) are exposed to the editor through a
registered **block binding source** (`inc/block-bindings.php`). Editors bind a
paragraph/button attribute to the source instead of typing the phone number into
markup, so a single change propagates everywhere and stays escapable. This is the one
sanctioned bridge from presentation to data, and it is read-only.

### 3.4 Assets: authored vs. compiled

Two lanes, intentionally:

- **Authored (shipped) — `assets/css/*.css`, `assets/js/enhance.js`.** The production
  stylesheet(s) and the dependency-free progressive-enhancement script (scroll reveal,
  header state, all guarded by `prefers-reduced-motion`) are hand-written and enqueued
  directly from `inc/assets.php`. These are committed to git.
- **Compiled (optional) — `src/` → `assets/build/`.** `@wordpress/scripts` (ESNext +
  Dart Sass) is wired and runnable but currently compiles empty entry points. Reach
  for it when a feature needs a bundled view-script or authored SCSS. Output is
  gitignored; `wp-scripts` emits an `*.asset.php` with dependency + version metadata
  for `wp_enqueue_*`.

---

## 4. Plugin bootstrap (`pacifica-core.php`)

Load order, top to bottom:

1. **Guard** — exits if `ABSPATH` is undefined.
2. **Constants** — `PACIFICA_CORE_VERSION`, `_FILE`, `_DIR`, `_URL`, `_MIN_PHP` (8.3).
3. **Autoloader** — prefers Composer's `vendor/autoload.php`; if absent, falls back to
   a minimal PSR-4 loader (`src/Support/Autoloader.php`) so the plugin runs on a plain
   deploy with no `composer install`. Composer is only required for the dev toolchain.
4. **PHP floor guard** — on PHP < 8.3, prints an admin notice and returns (no boot).
5. **Lifecycle hooks** — `register_activation_hook` / `register_deactivation_hook` →
   `Setup\Activator`.
6. **Boot** — on `plugins_loaded` (priority 20, after WooCommerce) →
   `Plugin::instance()->boot()`.
7. **HPOS declaration** — on `before_woocommerce_init`, declares
   `custom_order_tables` and `cart_checkout_blocks` compatibility.

The plugin header declares `Requires Plugins: woocommerce`, so WordPress enforces the
WooCommerce dependency.

---

## 5. Plugin internals — the service container

`src/Plugin.php` is a tiny **Bootable service container**. `boot()` walks one ordered
list of `class-string`s, instantiates each, and calls `->boot()` on anything
implementing `Pacifica\Core\Contracts\Bootable`. Order matters — configuration comes
first, presentation last. Missing classes are **skipped** (logged under `WP_DEBUG`) so
the plugin degrades instead of fataling. After the loop it fires the
`pacifica_core_booted` action. Booted instances are retrievable via
`Plugin::instance()->get( Some\Service::class )`.

### 5.1 Module map (boot order)

| Order | Namespace / class | Responsibility |
| ----- | ----------------- | -------------- |
| 1 | `Setup\Options` | **Config authority.** Single typed accessor for all settings (pickup rules, SMS numbers/keywords, feature flags). Everything reads config here — nothing hardcodes scheduling or credentials. |
| 2 | `Setup\Settings` | Renders the _Ajustes → Pacífica_ admin screens (Recolección, SMS, …) that write `Options`. |
| 3 | `Support\Assets` | Enqueues plugin (admin dashboard) CSS/JS. |
| 4 | `Woo\Support` | WooCommerce foundation: MXN, pickup-only shipping posture, gateway assumptions. |
| 5 | `Woo\Inventory` | Stock management — "Agotado" at 0, hides the buy button when sold out. |
| 6 | `Ordering\PickupScheduler` | Pickup **date + time-slot** logic: open days, lead time, per-slot capacity, validation at cart/checkout. |
| 7 | `Ordering\OrderMeta` | Persists pickup date/slot on the order; surfaces it in admin, emails, SMS. |
| 8 | `Sms\Logger` | Custom DB table + admin "SMS" screen; logs every inbound/outbound message. |
| 9 | `Sms\OrderNotifications` | New paid order → staff SMS; customer-facing status change → customer SMS. Uses `Sms\TwilioClient`. |
| 10 | `Sms\InboundController` | Twilio inbound webhook: signature validation → reply key → WooCommerce status transition. |
| 11 | `Seo\MetaTags` | `<head>` meta / Open Graph / Twitter tags. |
| 12 | `Seo\SchemaGraph` | JSON-LD structured data: `Bakery`/`LocalBusiness`, `Product`, `BreadcrumbList`. |
| 13 | `Rest\Routes` | Registers the `pacifica/v1` REST namespace (incl. `sms/inbound`). |
| 14 | `Cli\Commands` | WP-CLI: `wp pacifica install`, `wp pacifica import-media`, etc. |
| 15 | `Admin\Dashboard` | Operations dashboard landing. |
| 16 | `Admin\ProductionCalendar` | Bake/pickup production calendar. |
| 17 | `Admin\Reports` | Sales / operations reporting. |

> The `src/` tree ships the container, contracts, bootstrap support (Autoloader),
> Setup (Options, Activator), Sms (Logger, TwilioClient), and Seo (MetaTags) today;
> remaining modules are declared in the map and skipped-if-absent by design. Treat the
> table above as the intended surface.

### 5.2 Data authority & storage

- **Options** is the only place settings are read from — pickup scheduling, SMS
  numbers/keywords, and feature flags all resolve through it. No module reaches into
  raw `get_option` scattered around the codebase.
- **HPOS** is declared, so order reads/writes go through the WooCommerce data store,
  not direct post-meta assumptions.
- **SMS log table** is a dedicated custom table (created by `Setup\Activator`),
  surfaced by `Sms\Logger`'s admin screen — kept out of `postmeta` for query-ability.
- **REST namespace** is `pacifica/v1`. The Twilio inbound endpoint is
  `POST /wp-json/pacifica/v1/sms/inbound`, signature-validated.

---

## 6. Data flow: one order, end to end

```
Customer                     WordPress / WooCommerce            Bakery staff
   │  browse products (stock-managed; "Agotado" at 0)
   │─────────────▶  Woo\Inventory gates the buy button
   │  add to cart → choose PICKUP DATE + TIME SLOT
   │─────────────▶  Ordering\PickupScheduler validates open day,
   │                 lead time, and slot capacity (from Setup\Options)
   │  pay online (Stripe via WooCommerce Stripe gateway)
   │─────────────▶  order created; Ordering\OrderMeta stores date/slot
   │                 stock decrements automatically
   │◀───── confirmation page + email + customer SMS
   │
   │                 on "paid": Sms\OrderNotifications texts staff ─────────▶ 📱
   │                   "NUEVO PEDIDO #123 / cliente / items / Recoge: fecha hora
   │                    1=Preparando 2=Listo 3=Entregado 4=Cancelado"
   │                                                          staff replies "2"
   │                 POST /wp-json/pacifica/v1/sms/inbound ◀───────────────── 📱
   │                   Sms\InboundController validates the Twilio signature,
   │                   maps "2" → wc-status transition (e.g. "Listo")
   │◀───── customer SMS on each customer-facing status change
   │
   │                 Sms\Logger records every inbound/outbound message → SMS screen
```

Lead time, open days, slot length, slot capacity, staff numbers, and reply keywords
are all editable in _Ajustes → Pacífica_ — nothing in this flow is hardcoded.

---

## 7. Extension points

Stable hooks/filters for extending without forking:

| Hook | Type | Fires / filters | Use it to |
| ---- | ---- | --------------- | --------- |
| `pacifica_core_booted` | action | After all services boot; passes the `Plugin` container. | Register add-on services, grab a service instance, wire cross-cutting features. |
| `pacifica_sms_message` | filter | The outbound SMS body (staff or customer) before send. | Localize/rewrite copy, inject order details, append a link. |
| `pacifica_run_content_install` | action | During `wp pacifica install`. | Seed extra content/products/pages alongside the default catalog. |

General conventions when extending: text domains `pacifica` (theme) /
`pacifica-core` (plugin); CSS props `--pf-*`; nonces + capability checks on every
write path; escape every output; keep `declare(strict_types=1)` and typed properties.
See [`brand-brief.md`](brand-brief.md) §8.
