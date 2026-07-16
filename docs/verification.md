# Verification & QA

The quality bar for this build — **Lighthouse 95+**, **Core Web Vitals in the green**,
**WCAG 2.1 AA**, working **Stripe** and **Twilio** flows, and valid **structured
data** — cannot be self-certified in a headless build. These are design goals verified
on a real, asset-complete deploy. This doc is the concrete how-to, with commands and
URLs, plus a pre-launch checklist.

Run everything against a build that has **real fonts, real photography, and real
content** installed (see [`setup.md`](setup.md) §E and §G) — placeholders and missing
fonts skew both performance and layout metrics.

---

## 1. Performance — Lighthouse / PageSpeed (target 95+)

### CLI

```bash
npm install -g lighthouse
lighthouse https://YOUR-SITE/ \
  --only-categories=performance,accessibility,best-practices,seo \
  --preset=desktop --view

# Mobile is the stricter bar — test it too:
lighthouse https://YOUR-SITE/ --form-factor=mobile --view
```

Test at least: **Home**, a **product** page, the **shop/Menú** archive, **cart**, and
**checkout**. Target **95+** on Performance, Accessibility, Best Practices, and SEO.

### Hosted

- PageSpeed Insights: <https://pagespeed.web.dev/> (real-world CrUX + lab data).
- Chrome DevTools → Lighthouse tab (throttled, incognito, no extensions).

### If you fall short

Confirm variable fonts are self-hosted and **preloaded** (the theme preloads Fraunces
+ Inter from `wp_head` when present), images are sized/`loading="lazy"` below the fold,
and no autoplay/parallax was introduced (the brief forbids both).

---

## 2. Core Web Vitals

Measure field data (real users) and lab data (repeatable):

- **Field:** PageSpeed Insights CrUX panel, or Search Console → Core Web Vitals report.
- **Lab:** Chrome DevTools → Performance panel, or the Web Vitals extension.

Targets: **LCP < 2.5 s**, **INP < 200 ms**, **CLS < 0.1**. CLS is the one to watch on a
font-heavy design — `font-display: swap` + preload + sized media keep it low; verify no
layout shift as fonts/hero image load.

---

## 3. Accessibility — WCAG 2.1 AA

The theme is built to AA (visible focus, landmarks, skip link, reduced motion,
AA-contrast tokens). Verify, don't assume:

- **axe DevTools** (browser extension) — run on Home, product, cart, checkout,
  Contacto. Zero critical/serious violations.
- **WAVE** — <https://wave.webaim.org/> or the extension.
- **Lighthouse Accessibility** — expect ~100.
- **Contrast** — spot-check with WebAIM Contrast Checker
  (<https://webaim.org/resources/contrastchecker/>). Per the brief, `crust`/
  `crust-soft`/`ink` on `masa`/`linen` exceed AA; **confirm `linen` text on
  `clay`/`clay-deep`** for both large and normal text.
- **Manual:**
  - Tab through every page — focus is always visible and order is logical.
  - The skip link appears on first Tab and jumps to `main`.
  - `prefers-reduced-motion` disables scroll-reveal/animations
    (DevTools → Rendering → "Emulate CSS prefers-reduced-motion: reduce").
  - Screen-reader smoke test (VoiceOver on macOS, NVDA on Windows): landmarks and
    headings are announced sensibly.

---

## 4. Payments — WooCommerce Stripe (test mode)

With Stripe in **test mode** (see [`setup.md`](setup.md) §C):

1. Add a product to the cart; pick a valid **pickup date + time slot**.
2. Checkout with test card **`4242 4242 4242 4242`**, any future expiry, any 3-digit
   CVC, any postal code.
3. Expect: order created and **paid**, confirmation page + email, **stock decremented**,
   pickup date/slot stored on the order (Pedidos → order detail).
4. Edge cases:
   - Declined card `4000 0000 0000 0002` → graceful failure, no order stuck as paid.
   - 3-D Secure `4000 0025 0000 3155` → authentication challenge completes.
   - Sold-out product → buy button hidden ("Agotado"); cannot be ordered.
   - Pickup slot at capacity or inside the lead time → rejected at checkout.

Full test-card list: <https://docs.stripe.com/testing>.

---

## 5. SMS — Twilio end-to-end

With Twilio configured and the inbound webhook set to
`POST https://YOUR-SITE/wp-json/pacifica/v1/sms/inbound` (locally via an `ngrok`
tunnel — see [`setup.md`](setup.md) §D.3):

1. **Place a paid order** (§4 above).
2. **Staff SMS** arrives at a configured staff number:
   `NUEVO PEDIDO / #{id} / {cliente} / {items} / Recoge: {fecha hora}` plus
   `1=Preparando 2=Listo 3=Entregado 4=Cancelado`.
3. **Reply `2`** from that staff phone.
4. The order transitions to **"Listo"** in WooCommerce.
5. The **customer receives** a status-update SMS.
6. **Log:** every inbound/outbound message appears on the admin **SMS** screen.
7. **Security:** send a forged POST without a valid `X-Twilio-Signature` — it must be
   **rejected** (401/403), no status change.

Use Twilio's Console message logs to confirm delivery on their side.

---

## 6. Structured data (schema)

The plugin emits `Bakery`/`LocalBusiness`, `Product`, and `BreadcrumbList` JSON-LD.

- **Google Rich Results Test:** <https://search.google.com/test/rich-results> — run on
  Home (LocalBusiness) and a product page (Product). Zero errors; warnings reviewed.
- **Schema.org validator:** <https://validator.schema.org/> — paste the URL or the
  JSON-LD.
- **Manual:** View source → confirm one coherent `<script type="application/ld+json">`
  graph per page; NAP (name/address/phone), hours (Wed–Sun 09:00–15:00), and geo match
  the brand brief; product schema reflects price (MXN) and availability.

---

## 7. Cross-browser & responsive

- Latest **Chrome, Firefox, Safari**; **iOS Safari** and **Android Chrome**.
- Breakpoints: 375, 768, 1024, 1280, 1440 px. No horizontal scroll; fluid type scales
  cleanly; header/footer template-part variants behave.

---

## 8. Pre-launch checklist

**Content & data**

- [ ] `wp pacifica install` run; pages, product categories, and catalog present.
- [ ] Real photography imported (`wp pacifica import-media`); no placeholders remain.
- [ ] Real fonts (Fraunces + Inter) dropped into `assets/fonts/`; no Georgia fallback.
- [ ] Prices confirmed by client; no lingering `_pacifica_price_estimate` / "revisar
      precio" flags on live products.
- [ ] Business NAP, hours, Instagram correct everywhere (via `pacifica/business`
      bindings).

**Commerce**

- [ ] Stripe switched to **live** keys; test mode **off**; a real small transaction
      verified and refunded.
- [ ] Currency MXN; shipping disabled (pickup-only); pickup rules (open days, lead
      time, slot capacity) set in _Ajustes → Pacífica → Recolección_.

**SMS**

- [ ] Twilio live credentials set (constants preferred); staff numbers correct.
- [ ] Inbound webhook live URL set and **signature validation passing**.
- [ ] Full order → staff SMS → reply → customer SMS loop verified in production.

**Quality gates**

- [ ] Lighthouse 95+ (desktop **and** mobile) on Home, product, shop, cart, checkout.
- [ ] Core Web Vitals green (LCP < 2.5 s, INP < 200 ms, CLS < 0.1).
- [ ] axe/WAVE: zero critical/serious a11y violations; keyboard + reduced-motion pass.
- [ ] Rich Results Test: LocalBusiness + Product valid, zero errors.

**Ops & hygiene**

- [ ] `composer run lint` and `composer run analyze` clean (or triaged).
- [ ] `npm run lint:js` / `npm run lint:css` clean; `npm run build` succeeds.
- [ ] CI green on the launch commit.
- [ ] `WP_DEBUG_DISPLAY` off in production; secrets only in `wp-config`/env, never in
      git or the DB dump.
- [ ] Permalinks = "Post name"; REST routes resolve.
- [ ] Backups + SSL confirmed; 404 and legal pages (Privacidad/Términos/Reembolsos)
      present.
