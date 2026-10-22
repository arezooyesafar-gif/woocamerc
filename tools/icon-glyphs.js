const { chromium } = require('/opt/node22/lib/node_modules/playwright');
const fs = require('fs');
const [,, file, out] = process.argv;
(async () => {
  const browser = await chromium.launch();
  const res = {};
  for (const width of [390, 1350]) {
    const ctx = await browser.newContext({ viewport: { width, height: 900 }, javaScriptEnabled: false });
    const page = await ctx.newPage();
    await page.route('**/*', r => r.request().url().startsWith('file:') && r.request().resourceType() === 'document' ? r.continue() : r.abort());
    await page.goto('file://' + file, { waitUntil: 'domcontentloaded', timeout: 120000 });
    const data = await page.evaluate(() => {
      const out = {};
      const add = (fam, weight, style, str) => {
        for (const ch of str) {
          const cp = ch.codePointAt(0);
          if (cp < 0xe000 || cp > 0xf8ff) continue;
          const key = fam + '|' + weight + '|' + style;
          (out[key] = out[key] || {})[cp.toString(16)] = 1;
        }
      };
      const unq = c => { if (!c || c === 'none' || c === 'normal') return ''; const m = c.match(/^"(.*)"$/s); return m ? m[1] : ''; };
      document.querySelectorAll('*').forEach(el => {
        for (const pe of ['::before', '::after']) {
          const cs = getComputedStyle(el, pe);
          const s = unq(cs.content);
          if (s) add(cs.fontFamily, cs.fontWeight, cs.fontStyle, s);
        }
        const cs = getComputedStyle(el);
        let txt = '';
        el.childNodes.forEach(n => { if (n.nodeType === 3) txt += n.nodeValue; });
        if (txt) add(cs.fontFamily, cs.fontWeight, cs.fontStyle, txt);
      });
      return out;
    });
    for (const k in data) { res[k] = Object.assign(res[k] || {}, data[k]); }
    await ctx.close();
  }
  const final = {};
  for (const k in res) final[k] = Object.keys(res[k]).sort();
  fs.writeFileSync(out, JSON.stringify(final, null, 1));
  for (const k in final) console.log(k, final[k].length, final[k].slice(0, 40).join(' '));
  await browser.close();
})();
