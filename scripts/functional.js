// Usage: node scripts/functional.js <baseUrl>  -- menu, links, buttons, forms and product gallery checks.
const { chromium } = require('playwright');
const base = (process.argv[2] || 'http://localhost:8765').replace(/\/$/, '');
const paths = ['/', '/automotive/', '/surfboards/', '/shaping-course/', '/shop/', '/about/'];
const log = (ok, msg) => console.log((ok ? 'PASS ' : 'FAIL ') + msg);
(async () => {
  const browser = await chromium.launch();
  // Links: every internal href on every page must return 200.
  const ctx = await browser.newContext({ viewport: { width: 1440, height: 900 } });
  const page = await ctx.newPage();
  const links = new Set();
  for (const p of paths) {
    await page.goto(base + p, { waitUntil: 'domcontentloaded' });
    (await page.$$eval('a[href]', as => as.map(a => a.href))).forEach(h => links.add(h.split('#')[0]));
  }
  let broken = 0;
  for (const l of links) {
    if (!l.startsWith(base)) { console.log('external link: ' + l); continue; }
    const st = await page.evaluate(u => fetch(u).then(r => r.status), l);
    if (st !== 200) { broken++; console.log('  broken ' + st + ' ' + l); }
  }
  log(broken === 0, `internal links (${links.size} unique) all return 200`);
  const nf = await page.evaluate(u => fetch(u).then(r => r.status), base + '/no-such-page/');
  log(nf === 404, '404 page returns 404');

  // Home panel buttons
  await page.goto(base + '/');
  await page.click('.dl-panel >> nth=0 >> a');
  log(page.url().endsWith('/automotive/'), 'home Automotive button -> /automotive/');
  await page.goto(base + '/');
  await page.click('.dl-panel >> nth=1 >> a');
  log(page.url().endsWith('/surfboards/'), 'home Surfboards button -> /surfboards/');

  // Product: thumbnails and enquire button with product pre-selected
  await page.goto(base + '/shop/');
  const before = await page.getAttribute('.dl-pgallery__main img', 'src');
  await page.click('.dl-pgallery__thumbs li:nth-child(2) button');
  await page.waitForTimeout(500);
  log(before !== await page.getAttribute('.dl-pgallery__main img', 'src'), 'product gallery thumbnail swaps the main image');
  await page.click('.dl-product__summary .wp-block-button__link');
  await page.waitForTimeout(600);
  log(page.url().includes('/about/') && (await page.inputValue('#dl-enquiry-service')) === 'product', 'Enquire to order -> contact form with product service selected');
  log((await page.textContent('.dl-form__picked')).includes('Single Fin'), 'contact form shows the chosen product');

  // Enquiry form: validation then demo confirmation
  await page.click('#dl-enquiry button[type=submit]');
  log(await page.isHidden('#dl-enquiry .dl-form__status'), 'empty enquiry form is blocked by validation');
  await page.fill('#dl-enquiry-name', 'Test Person'); await page.fill('#dl-enquiry-email', 'test@example.com'); await page.fill('#dl-enquiry-message', 'Test entry');
  await page.click('#dl-enquiry button[type=submit]');
  log((await page.textContent('#dl-enquiry .dl-form__status')).includes('Demo form'), 'enquiry form shows the demo confirmation');

  // Course form
  await page.goto(base + '/shaping-course/');
  await page.fill('#dl-course-name', 'Test Person'); await page.fill('#dl-course-email', 'test@example.com');
  await page.selectOption('#dl-course-experience', 'none'); await page.selectOption('#dl-course-date', 'either');
  await page.click('#dl-course button[type=submit]');
  log((await page.textContent('#dl-course .dl-form__status')).includes('Demo form'), 'course form shows the demo confirmation');
  await ctx.close();

  // Mobile menu
  const m = await browser.newContext({ viewport: { width: 390, height: 844 }, hasTouch: true });
  const mp = await m.newPage();
  await mp.goto(base + '/');
  const box = await mp.locator('.dl-burger').boundingBox();
  log(box.width >= 44 && box.height >= 44, `burger tap target ${box.width}x${box.height}`);
  await mp.click('.dl-burger'); await mp.waitForTimeout(900);
  log(await mp.locator('#dl-menu').evaluate(e => getComputedStyle(e).visibility === 'visible'), 'mobile menu opens');
  const small = await mp.$$eval('#dl-menu a', as => as.filter(a => a.getBoundingClientRect().height < 44).length);
  log(small === 0, 'mobile menu links are at least 44px tall');
  await mp.screenshot({ path: process.argv[3] || 'menu.png' });
  await mp.keyboard.press('Escape'); await mp.waitForTimeout(600);
  log(await mp.locator('#dl-menu').evaluate(e => getComputedStyle(e).visibility === 'hidden'), 'Escape closes the menu');
  await mp.click('.dl-burger'); await mp.waitForTimeout(700);
  await mp.click('#dl-menu a >> text=Course'); await mp.waitForLoadState();
  log(mp.url().endsWith('/shaping-course/'), 'mobile menu link navigates');
  await browser.close();
})().catch(e => { console.log('FAIL script error: ' + e.message); process.exit(1); });
