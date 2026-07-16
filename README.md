# Pacífica Panadería — WordPress Build

A premium WordPress site for **Pacífica Panadería**, an artisan sourdough bakery and
café in Cuernavaca, Morelos, México. Built as a clean two-part system: a
presentation-only **block theme** and a **custom plugin** that owns all commerce and
business logic.

> _Masa madre, tiempo y fuego._ — Sourdough, time, and fire.

---

## What this is

- **`wp-content/themes/pacifica`** — a Full Site Editing (FSE) block theme. Design
  tokens live in `theme.json` (the "Horno & Trigo" system); templates, template
  parts, and patterns are block markup only. **No business logic in templates.**
- **`wp-content/plugins/pacifica-core`** — the engine. Reserve-&-pickup ordering on
  top of WooCommerce, the Stripe payment flow, the Twilio SMS workflow, SEO schema,
  and the admin operations dashboard. Namespaced `Pacifica\Core\` (PSR-4 → `src/`).

The split is deliberate: swap or restyle the theme without touching commerce, and
upgrade commerce without risking the presentation layer.

## Architecture at a glance

```
pacifica/
├── wp-content/
│   ├── themes/pacifica/          # FSE block theme (presentation only)
│   │   ├── theme.json            # Design tokens: color, type, spacing, shadow…
│   │   ├── templates/ parts/     # Block templates + template parts (.html)
│   │   ├── patterns/             # Registered block patterns (.php)
│   │   ├── inc/                  # Theme supports, asset enqueue, block bindings
│   │   ├── assets/               # Hand-authored runtime css/js, fonts, images
│   │   └── src/                  # Optional @wordpress/scripts build sources
│   └── plugins/pacifica-core/    # All business logic
│       ├── pacifica-core.php     # Bootstrap: constants, autoloader, HPOS, guards
│       ├── src/                  # Pacifica\Core\… (PSR-4)
│       ├── data/                 # Seed catalog + content for `wp pacifica install`
│       └── templates/            # Email/SMS-adjacent templates
├── docs/                         # Architecture, setup, verification, brand brief
├── composer.json / phpcs.xml.dist / phpstan.neon.dist   # PHP tooling
├── package.json / .wp-env.json   # Node tooling + local WordPress
└── .github/workflows/ci.yml      # Lint + static analysis
```

The plugin boots a small ordered **service container** (`src/Plugin.php`): Options →
WooCommerce foundation → Ordering → SMS → SEO → REST/CLI → Admin. Missing modules are
skipped gracefully. See [`docs/architecture.md`](docs/architecture.md) for the full
module map and order-lifecycle data flow.

## Features

- **Reserve & pickup ordering** — pickup date + capacity-limited time slots, lead
  time and open days configured in _Ajustes → Pacífica → Recolección_. Pickup-only,
  no shipping. Prices in MXN.
- **Stripe payments** — via the WooCommerce Stripe gateway.
- **Twilio SMS workflow** — new paid order texts staff; staff reply keys
  (`1=Preparando 2=Listo 3=Entregado 4=Cancelado`) drive WooCommerce status
  transitions; each customer-facing change texts the customer. All messages logged
  to a custom table with an admin "SMS" screen. Inbound webhook is signature-checked.
- **Admin operations dashboard** — production calendar, reports, at-a-glance order ops.
- **SEO schema** — `LocalBusiness`/`Bakery`, product, and breadcrumb structured data
  plus meta tags, under REST namespace `pacifica/v1` for supporting endpoints.
- **HPOS-ready** — declares WooCommerce High-Performance Order Storage + cart/checkout
  blocks compatibility.

## Requirements

| Component     | Version |
| ------------- | ------- |
| WordPress     | 6.8+    |
| PHP           | 8.3+    |
| WooCommerce   | 8+ (9+ recommended) |
| Node.js       | LTS (see `.nvmrc`) |
| Composer      | 2.x     |

## Quick start (local, via @wordpress/env)

```bash
# 0. Prerequisites: Docker running, Node LTS, Composer 2, PHP 8.3 CLI.
nvm use                                   # picks up .nvmrc (Node LTS)

# 1. PHP tooling + plugin autoloader
composer install                          # root: phpcs, phpstan, stubs
composer install -d wp-content/plugins/pacifica-core   # generates the plugin's vendor/autoload.php

# 2. Node tooling (workspaces: root + theme)
npm install

# 3. Boot local WordPress (WP latest, PHP 8.3, WooCommerce, this theme + plugin)
npm run env:start                         # wraps `wp-env start`

# 4. Activate + seed demo content
npm run env:cli -- theme activate pacifica
npm run env:cli -- plugin activate woocommerce pacifica-core
npm run env:install-content               # wraps `wp pacifica install`

# 5. (Optional) compile future SCSS/JS entries
npm run build
```

Site: <http://localhost:8888> · Admin: <http://localhost:8888/wp-admin> (`admin` /
`password`). Full walkthrough — including installing on a **real host** — is in
[`docs/setup.md`](docs/setup.md).

## Honest caveats

Read these before you judge the site "incomplete":

- **Real photography is client-supplied.** Instagram/Google image CDNs are
  login/anti-bot gated, so the repo ships labeled placeholders that share the final
  filenames. Drop real Pacífica photos per `data/media/source` and its README, then
  run the media importer (`wp pacifica import-media`) — no code changes needed.
- **Stripe & Twilio need live credentials.** Nothing is hardcoded. Add Stripe keys in
  the WooCommerce Stripe gateway and Twilio SID/token/from in _Ajustes → Pacífica →
  SMS_ (or via `wp-config` constants). Details in [`docs/setup.md`](docs/setup.md).
- **Performance / accessibility targets are design goals, not self-certified.**
  Lighthouse 95+, Core Web Vitals, and WCAG 2.1 AA are the bar the design was built
  to; verify them on a real deploy with real assets per
  [`docs/verification.md`](docs/verification.md).
- **Prices are estimates until confirmed.** Estimated catalog prices carry meta
  `_pacifica_price_estimate=1` and surface in admin as "revisar precio."

## Documentation

| Doc | What's in it |
| --- | --- |
| [`docs/brand-brief.md`](docs/brand-brief.md) | Brand voice, IA, product taxonomy, and the design-token source of truth. |
| [`docs/architecture.md`](docs/architecture.md) | Deep architecture: directory tree, theme + plugin internals, order data flow, extension points. |
| [`docs/setup.md`](docs/setup.md) | Step-by-step install for a real host **and** wp-env; Stripe/Twilio config; secrets. |
| [`docs/verification.md`](docs/verification.md) | How to verify performance, a11y, payments, SMS, and schema; pre-launch checklist. |

## Tooling reference

```bash
# PHP
composer run lint          # phpcs (WordPress + WordPress-Extra + PHP 8.3 compat)
composer run lint:fix      # phpcbf auto-fix
composer run analyze       # phpstan level 6

# JS / CSS (root delegates to the theme workspace)
npm run lint:js            # @wordpress/scripts lint-js
npm run lint:css           # stylelint (@wordpress/stylelint-config)
npm run format             # wp-scripts format
npm run build              # compile src/ -> assets/build/

# Local WordPress
npm run env:start / env:stop / env:clean
npm run env:cli -- <wp-cli args>
```

## License

GPL-2.0-or-later. Font binaries are **not** committed (licensing); see
`wp-content/themes/pacifica/assets/fonts/README.md` for the exact files to drop in.
