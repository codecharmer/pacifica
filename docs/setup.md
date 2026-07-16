# Setup & installation

Two paths are covered:

- **A. Local development** with `@wordpress/env` (Docker) — fastest way to a running
  site.
- **B. A real host** (staging/production) — the same steps against a normal WordPress
  install.

Then: **C. Stripe**, **D. Twilio SMS**, **E. real photography**, **F. secrets**, and
**G. build**.

---

## Prerequisites

| Tool | Version | Notes |
| ---- | ------- | ----- |
| Docker | current | Only for path A (`wp-env`). Must be running. |
| Node.js | LTS | `nvm use` reads `.nvmrc`. |
| npm | 10+ | Ships with Node LTS. |
| Composer | 2.x | For PHP tooling + the plugin autoloader. |
| PHP CLI | 8.3+ | For `composer`, `phpcs`, `phpstan`. |
| WP-CLI | current | Bundled inside `wp-env`; install separately for a real host. |

---

## A. Local development with @wordpress/env

```bash
git clone <repo> pacifica && cd pacifica
nvm use                                   # Node LTS from .nvmrc

# PHP tooling (root) + the plugin's runtime autoloader
composer install
composer install -d wp-content/plugins/pacifica-core

# Node tooling (root + theme workspace)
npm install

# Boot WordPress (latest), PHP 8.3, WooCommerce, this theme + plugin, WP_DEBUG on
npm run env:start                         # == wp-env start
```

`.wp-env.json` mounts `wp-content/plugins/pacifica-core` and
`wp-content/themes/pacifica`, pulls **WooCommerce** from wordpress.org, pins **PHP
8.3**, and enables `WP_DEBUG` / `WP_DEBUG_LOG` / `SCRIPT_DEBUG`.

Activate and seed content:

```bash
npm run env:cli -- theme activate pacifica
npm run env:cli -- plugin activate woocommerce pacifica-core
npm run env:install-content               # == wp-env run cli wp pacifica install
```

- Site: <http://localhost:8888>
- Admin: <http://localhost:8888/wp-admin> — user `admin`, password `password`

Everyday commands:

```bash
npm run env:start          # start (or resume)
npm run env:stop           # stop containers
npm run env:clean          # reset the environment (wp-env clean all)
npm run env:cli -- <args>  # run any WP-CLI command, e.g. `-- option get siteurl`
```

> `wp pacifica install` is idempotent-by-design content seeding (pages, product
> categories, demo catalog with estimated prices). Re-run safely after code changes.

---

## B. Installing on a real host

1. **Provision** WordPress 6.8+ on **PHP 8.3+** with a MySQL/MariaDB database.
2. **Deploy the code** so the two directories land in the site's `wp-content/`:
   - `wp-content/plugins/pacifica-core`
   - `wp-content/themes/pacifica`
   (Deploy the repo and symlink/copy, or make `wp-content` your deploy target.)
3. **Generate the plugin autoloader** (optional but recommended — the plugin falls
   back to its own PSR-4 loader if absent):
   ```bash
   composer install --no-dev -d wp-content/plugins/pacifica-core
   ```
4. **Install WooCommerce** (8+, 9+ recommended) and the **WooCommerce Stripe Gateway**
   plugin from the WordPress.org repository:
   ```bash
   wp plugin install woocommerce woocommerce-gateway-stripe --activate
   ```
5. **Activate theme + plugin**:
   ```bash
   wp theme activate pacifica
   wp plugin activate pacifica-core
   ```
6. **Seed content**:
   ```bash
   wp pacifica install
   ```
7. **Set permalinks** to "Post name" (Ajustes → Enlaces permanentes) so the
   `pacifica/v1` REST routes resolve cleanly.
8. Continue with Stripe (§C), Twilio (§D), photography (§E), secrets (§F), build (§G).

---

## C. Stripe (payments)

Stripe is handled through WooCommerce's official gateway — the plugin does **not**
store card credentials.

1. Install/activate **WooCommerce Stripe Gateway** (see step B4).
2. _WooCommerce → Ajustes → Pagos → Stripe → Gestionar_.
3. Enter your **test** keys first (`pk_test_…` / `sk_test_…`), enable **test mode**,
   and verify a purchase with card `4242 4242 4242 4242` (any future expiry, any CVC)
   — see [`verification.md`](verification.md).
4. Swap to **live** keys and disable test mode for launch.

Currency must be **MXN** and shipping stays disabled (pickup-only) — both are the
Pacífica defaults enforced by `Woo\Support`.

---

## D. Twilio SMS

The SMS workflow (staff notification, reply-key status transitions, customer updates)
needs Twilio credentials and one inbound webhook. Credentials can be set **either** in
the admin UI **or** as `wp-config` constants (constants win and keep secrets out of the
database — preferred for production).

### D.1 Credentials — Option 1: admin UI

_Ajustes → Pacífica → SMS_:

- **Account SID**, **Auth Token**, **From number** (your Twilio number, E.164, e.g.
  `+52771…`).
- **Staff numbers** to notify on new orders.
- **Reply keywords** (defaults `1=Preparando 2=Listo 3=Entregado 4=Cancelado`).

### D.2 Credentials — Option 2: `wp-config` constants (preferred for prod)

```php
// wp-config.php — above the "That's all, stop editing!" line.
define( 'PACIFICA_TWILIO_SID',        'ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx' );
define( 'PACIFICA_TWILIO_AUTH_TOKEN', 'your-twilio-auth-token' );
define( 'PACIFICA_TWILIO_FROM',       '+527770000000' );
```

When these constants are defined, they take precedence over the stored SMS settings.

### D.3 Inbound webhook

Point your Twilio number's **Messaging → "A message comes in"** webhook at:

```
POST https://YOUR-SITE/wp-json/pacifica/v1/sms/inbound
```

- Method: **HTTP POST**.
- The endpoint validates the **`X-Twilio-Signature`** header against your Auth Token —
  the webhook rejects unsigned/forged requests, so the public URL is safe to expose.
- Locally, expose `http://localhost:8888` with a tunnel (e.g. `ngrok http 8888`) and
  use the tunnel URL as the webhook target for end-to-end testing.

Flow to sanity-check: place an order → staff phone receives `NUEVO PEDIDO…` → reply
`2` → order moves to "Listo" → customer receives a status SMS. Full script in
[`verification.md`](verification.md).

---

## E. Real photography

The repo ships **labeled placeholder images that share the final filenames**, so
swapping in real photos needs zero code changes.

1. Obtain the real Pacífica photos (client-supplied; Instagram/Google CDNs are
   login/anti-bot gated and cannot be scraped).
2. Drop them into the media importer's source directory per
   `wp-content/plugins/pacifica-core/data/media/README.md`
   (`data/media/source/…`), keeping the documented filenames.
3. Import:
   ```bash
   wp pacifica import-media           # local: npm run env:cli -- pacifica import-media
   ```
4. Confirm hero, product, and gallery imagery updated on the front end.

---

## F. Secrets — `wp-config.php` reference block

Keep all secrets in `wp-config.php` (or environment) — never in the database dump or
git. Recommended block:

```php
// --- Pacífica: environment & secrets -------------------------------------

// Environment type (affects WP + plugin behavior).
define( 'WP_ENVIRONMENT_TYPE', 'production' ); // 'local' | 'staging' | 'production'

// Debugging (turn OFF display in production).
define( 'WP_DEBUG',         false );
define( 'WP_DEBUG_LOG',     true  );
define( 'WP_DEBUG_DISPLAY', false );

// Twilio SMS (preferred over the admin UI for production).
define( 'PACIFICA_TWILIO_SID',        'ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx' );
define( 'PACIFICA_TWILIO_AUTH_TOKEN', 'your-twilio-auth-token' );
define( 'PACIFICA_TWILIO_FROM',       '+527770000000' );

// Stripe keys are configured in the WooCommerce Stripe gateway UI, not here.

// -------------------------------------------------------------------------
```

Rotate any credential that has ever been committed or shared in plaintext.

---

## G. Build assets

The shipped runtime CSS/JS (`assets/css/*.css`, `assets/js/enhance.js`) is
hand-authored and already committed — **no build step is required to run the site**.
The `@wordpress/scripts` pipeline is for **future compiled** SCSS/JS entries.

```bash
npm run build      # compile src/js/index.js + src/scss/index.scss -> assets/build/
npm run start      # same, in watch mode during development
```

Lint before committing:

```bash
composer run lint      # PHP (phpcs)
composer run analyze   # PHP static analysis (phpstan)
npm run lint:js        # JS
npm run lint:css       # SCSS/CSS
```

---

## Troubleshooting

| Symptom | Fix |
| ------- | --- |
| Admin notice: "Pacífica Core requiere PHP 8.3…" | Host is below PHP 8.3. Upgrade PHP. |
| Plugin won't activate | WooCommerce must be installed first (`Requires Plugins: woocommerce`). |
| REST route 404 (`pacifica/v1/...`) | Set permalinks to "Post name" and flush (`wp rewrite flush`). |
| Twilio replies do nothing | Check the webhook URL, HTTP POST, and that the Auth Token used for signature validation matches the number. |
| Fonts look like Georgia/system | Font binaries aren't committed; add them per `assets/fonts/README.md`. |
| `wp-env` won't start | Ensure Docker is running; try `npm run env:clean` then `npm run env:start`. |
