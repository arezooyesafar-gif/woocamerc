/* Tarh99 Performance Suite: explicit, stoppable admin-only batch media generation. */
(function () {
  'use strict';
  const start = document.getElementById('tps-regen-start');
  const stop = document.getElementById('tps-regen-stop');
  const msg = document.getElementById('tps-regen-message');
  const cursor = document.getElementById('tps-regen-cursor');
  if (!start || !stop || !msg || typeof tpsAdmin === 'undefined') return;
  let running = false;
  let totals = {created: 0, skipped: 0, errors: 0};
  const pause = () => {running = false; start.disabled = false; stop.disabled = true;};
  stop.addEventListener('click', () => {pause(); msg.textContent = 'پردازش متوقف شد؛ پیشرفت قبلی ذخیره است.';});
  start.addEventListener('click', async () => {
    if (running) return;
    running = true; start.disabled = true; stop.disabled = false;
    totals = {created: 0, skipped: 0, errors: 0};
    while (running) {
      let result;
      try {
        const data = new URLSearchParams({action: 'tps_regen_images', nonce: tpsAdmin.nonce});
        const response = await fetch(tpsAdmin.ajaxUrl, {method:'POST', credentials:'same-origin', body:data});
        result = await response.json();
        if (!response.ok || !result.success) throw Error(result?.data?.message || 'خطا در پردازش تصاویر');
      } catch (err) {
        pause(); msg.textContent = 'خطا: ' + err.message + '؛ می‌توانید دوباره ادامه دهید.'; return;
      }
      const d = result.data;
      totals.created += d.created; totals.skipped += d.skipped; totals.errors += d.errors;
      cursor.textContent = d.offset;
      msg.textContent = 'بررسی‌شده: ' + d.offset + ' | ساخته‌شده: ' + totals.created +
        ' | نیاز نداشته: ' + totals.skipped + ' | خطا: ' + totals.errors;
      if (d.finished || d.processed === 0) {
        pause(); msg.textContent += ' — پردازش تمام شد. کش را پاک کنید.'; break;
      }
      await new Promise(resolve => setTimeout(resolve, 900));
    }
  });
})();
