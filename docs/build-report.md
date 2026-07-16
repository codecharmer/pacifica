# Pacífica Panadería — Build Report & Handoff

_Agency build. Status at handoff: **feature-complete codebase, statically verified.**_

## 1. What was delivered

A production-shaped WordPress project — a Full-Site-Editing **block theme** (`pacifica`)
and a **custom plugin** (`pacifica-core`) — cleanly separated so all commerce/ordering/
SMS/business logic lives in the plugin and the theme stays presentation-only.

| Area | Count / detail |
| --- | --- |
| Block templates | 17 (`front-page`, `home`, `single`, `page(+variants)`, `404`, `search`, `archive`, + 6 WooCommerce) |
| Template parts | 4 (header, transparent header, footer, minimal footer) |
| Block patterns | 20 (hero, philosophy, process, product highlights, testimonials, hours/location, FAQ, newsletter, catering/order CTAs, reserve steps, commerce grids, header/footer) |
| Style variations | 2 (dark "Horno de Noche", light "Mostrador") |
| Design tokens | `theme.json` v3 — 12-color palette, Fraunces+Inter fluid type scale, spacing/radius/shadow/motion |
| Plugin services | 17 `Bootable` modules behind a container + PSR-4 autoloader |
| Seed products | 33 across 7 categories (all prices flagged `_pacifica_price_estimate` for owner review) |
| Seed pages | 14 incl. Aviso de Privacidad / Términos / Reembolsos (Spanish, LFPDPPP-aware, "borrador para revisión legal") |
| Placeholder art | 7 on-brand SVGs sharing final filenames (drop-in photo swap) |
| Docs | README + architecture / setup / verification + this report |

**Systems implemented:** reserve-&-pickup ordering (pickup date + capacity-limited time
slots, classic **and** block checkout), Stripe via the WooCommerce Stripe gateway,
inventory→"Agotado" sold-out, custom order statuses (`preparing`/`ready`), Twilio SMS
(staff new-order alert, digit-reply status control with signature-validated webhook,
automatic customer status SMS), admin operations dashboard (ops summary, production
calendar, reports, SMS history, tabbed settings), and full SEO (OG/Twitter meta +
consolidated JSON-LD: Bakery/LocalBusiness, WebSite, Product, BreadcrumbList).

## 2. Verification performed in-build (static)

- **PHP lint:** all 54 PHP files pass `php -l`.
- **PSR-4:** every plugin class's namespace matches its path; all 17 service classes in
  `Plugin.php` resolve to files.
- **Cross-module contracts reconciled:** pickup meta keys + custom status slugs shared by
  Ordering ↔ SMS ↔ Admin; `TwilioClient::send()`/`validate_signature()` signatures match
  callers; `OrderMeta::label()` consumed by SMS; `pacifica_run_content_install` fired by
  Settings, handled by CLI/Installer.
- **Content graph:** 7 category slugs identical across the `category-tiles` pattern, the
  installer's term map, and all 33 products; all 15 page→pattern references resolve; all
  `customTemplates` + template parts exist.
- **Data integrity:** 33 products / 7 categories / 33 unique slugs+keys / 33 prices flagged;
  14 pages / exactly 1 front page; navigation has primary + footer.
- **Tokens:** zero hardcoded hex in any pattern/template/part.

## 3. Honest caveats — what still needs a human/live environment

1. **Real photography.** Instagram/Google image CDNs are login/anti-bot gated and were not
   scraped. Drop authorized Pacífica photos into `wp-content/plugins/pacifica-core/data/media/source/`
   named by `image_key` (list in that folder's README) and run `wp pacifica import-media`.
   Until then the branded placeholders render.
2. **Live credentials.** Stripe (WooCommerce → Payments) and Twilio (Ajustes → Pacífica →
   SMS, or `PACIFICA_TWILIO_*` constants) require real keys. Point Twilio's inbound webhook
   at `https://SITE/wp-json/pacifica/v1/sms/inbound`.
3. **Runtime targets are unverified here.** Lighthouse 95+, Core Web Vitals, and WCAG AA
   are engineered for but must be measured on a running install — see `verification.md`.
4. **Prices are estimates** flagged for the owner to confirm before launch.
5. **Legal pages are drafts** for professional review.

## 4. Go-live sequence

```bash
nvm use && composer install && composer install -d wp-content/plugins/pacifica-core
npm install && npm run env:start
npm run env:cli -- theme activate pacifica
npm run env:cli -- plugin activate woocommerce pacifica-core
npm run env:cli -- pacifica install     # seeds categories, products, pages, nav, front page
```

Then: add real photos → `wp pacifica import-media`; set Stripe + Twilio credentials;
confirm prices; legal review; run the `verification.md` checklist. Full detail in
[`setup.md`](setup.md) and [`architecture.md`](architecture.md).
