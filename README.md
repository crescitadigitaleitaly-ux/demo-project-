# Driftline Workshop concept demo

A front-end concept demo for a prospective client, built as a lightweight custom WordPress block theme.
Live at https://driftline.mouzzambuild.com (noindex). Concept demo by Mouzzam Channa, not a live business.

- `theme/driftline/` the theme. `theme.json` holds fonts, colours and spacing. Page content is normal block markup,
  created on first load by `inc/setup.php` from `inc/content.php`, so every page is editable in the WordPress editor.
- `scripts/deploy.sh` uploads the theme to Hostinger. `scripts/shots.js` takes screenshots and runs page checks.
- `docs/CREDITS.md` image sources and licences.

Scope: front end only. The forms validate and confirm in the browser but send and store nothing.
There is no WooCommerce and no payment. The product page is a designed catalogue page.
