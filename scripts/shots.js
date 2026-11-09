// Usage: node scripts/shots.js <baseUrl> <outDir> [widths]
// Full-page screenshots of every page at each width, plus overflow, console and link checks.
const { chromium } = require('playwright');
const fs = require('fs');
const base = (process.argv[2] || 'http://localhost:8765').replace(/\/$/, '');
const out = process.argv[3] || 'shots';
const widths = (process.argv[4] || '390,768,1440').split(',').map(Number);
const pages = { home: '/', automotive: '/automotive/', surfboards: '/surfboards/', course: '/shaping-course/', shop: '/shop/', about: '/about/' };
(async () => {
  fs.mkdirSync(out, { recursive: true });
  const browser = await chromium.launch();
  const report = [];
  for (const w of widths) {
    const ctx = await browser.newContext({ viewport: { width: w, height: w < 500 ? 844 : 900 }, deviceScaleFactor: 1 });
    for (const [name, path] of Object.entries(pages)) {
      const page = await ctx.newPage();
      const errors = [];
      page.on('console', m => { if (m.type() === 'error') errors.push(m.text()); });
      page.on('pageerror', e => errors.push(String(e)));
      page.on('requestfailed', r => errors.push('FAILED ' + r.url()));
      const resp = await page.goto(base + path, { waitUntil: 'networkidle' });
      const h = await page.evaluate(() => document.documentElement.scrollHeight);
      for (let y = 0; y < h; y += 500) { await page.evaluate(v => window.scrollTo(0, v), y); await page.waitForTimeout(120); }
      await page.evaluate(() => window.scrollTo(0, 0));
      await page.waitForTimeout(2600);
      const info = await page.evaluate(() => ({
        overflow: document.documentElement.scrollWidth - document.documentElement.clientWidth,
        noAlt: [...document.images].filter(i => !i.hasAttribute('alt')).length,
        h1: document.querySelectorAll('h1').length,
        title: document.title,
        desc: (document.querySelector('meta[name=description]') || {}).content,
        robots: (document.querySelector('meta[name=robots]') || {}).content,
        external: [...document.querySelectorAll('link[href],script[src],img[src]')].map(e => e.href || e.src).filter(u => u && !u.startsWith(location.origin) && !u.startsWith('data:')),
      }));
      await page.screenshot({ path: `${out}/${name}-${w}.png`, fullPage: true });
      report.push({ w, name, status: resp.status(), ...info, errors });
      await page.close();
    }
    await ctx.close();
  }
  await browser.close();
  for (const r of report) console.log(JSON.stringify(r));
})();
