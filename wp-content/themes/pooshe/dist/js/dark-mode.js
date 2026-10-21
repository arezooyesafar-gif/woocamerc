(function () {
  'use strict';

  var STORAGE_KEY = 'pooshe_dark_mode';
  var LIGHT_BG = '#F5F5F5';
  var DARK_BG  = '#1E293B';
  var tracked = [];

  function rgbToHex(rgb) {
    if (!rgb || rgb === 'transparent' || rgb === 'rgba(0, 0, 0, 0)') return null;
    var m = rgb.match(/^rgb\s*\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*\)$/);
    if (!m) return null;
    return '#' + [m[1], m[2], m[3]].map(function (c) {
      return ('0' + parseInt(c, 10).toString(16)).slice(-2).toUpperCase();
    }).join('');
  }

  function normalizeHex(hex) {
    return hex.toUpperCase().replace(/^#?([0-9A-F])([0-9A-F])([0-9A-F])$/, '#$1$1$2$2$3$3');
  }

  function replaceBg(el) {
    var cur = el.style.backgroundColor;
    if (cur && normalizeHex(cur) === normalizeHex(LIGHT_BG)) {
      el.style.backgroundColor = DARK_BG;
      tracked.push(el);
      return true;
    }
    var comp = getComputedStyle(el).backgroundColor;
    if (comp && rgbToHex(comp) === normalizeHex(LIGHT_BG)) {
      el.style.backgroundColor = DARK_BG;
      tracked.push(el);
      return true;
    }
    return false;
  }

  function restoreBg(el) {
    el.style.backgroundColor = '';
  }

  function scanAll() {
    var all = document.querySelectorAll('*');
    for (var i = 0; i < all.length; i++) {
      replaceBg(all[i]);
    }
  }

  var observer = null;
  function startObserver() {
    if (observer) return;
    observer = new MutationObserver(function (mutations) {
      for (var i = 0; i < mutations.length; i++) {
        var added = mutations[i].addedNodes;
        for (var j = 0; j < added.length; j++) {
          if (added[j].nodeType === 1) {
            replaceBg(added[j]);
            var subs = added[j].querySelectorAll('*');
            for (var k = 0; k < subs.length; k++) {
              replaceBg(subs[k]);
            }
          }
        }
      }
    });
    observer.observe(document.body, { childList: true, subtree: true });
  }

  function stopObserver() {
    if (observer) { observer.disconnect(); observer = null; }
  }

  function applyDarkMode(enabled) {
    var body = document.body;
    if (enabled) {
      body.classList.add('dark-mode');
      body.classList.remove('light-mode');
      scanAll();
      startObserver();
    } else {
      body.classList.remove('dark-mode');
      body.classList.add('light-mode');
      stopObserver();
      for (var i = 0; i < tracked.length; i++) {
        restoreBg(tracked[i]);
      }
      tracked = [];
    }
    var toggles = document.querySelectorAll('.pooshe-dark-mode-toggle');
    toggles.forEach(function (btn) {
      btn.classList.toggle('active', enabled);
    });
  }

  function initDarkMode() {
    var enabled = typeof POOSHE_DARK_MODE !== 'undefined' && POOSHE_DARK_MODE.enabled === '1';

    if (!enabled) {
      var toggles = document.querySelectorAll('.pooshe-dark-mode-toggle');
      toggles.forEach(function (btn) {
        btn.style.display = 'none';
      });
      return;
    }

    var scheme = getPreferredScheme();
    applyDarkMode(scheme === 'dark');

    document.addEventListener('click', function (e) {
      var btn = e.target.closest('.pooshe-dark-mode-toggle');
      if (!btn) return;
      e.preventDefault();
      var isDark = !document.body.classList.contains('dark-mode');
      applyDarkMode(isDark);
      localStorage.setItem(STORAGE_KEY, isDark ? 'dark' : 'light');
    });
  }

  function getPreferredScheme() {
    var stored = localStorage.getItem(STORAGE_KEY);
    if (stored === 'dark' || stored === 'light') return stored;
    if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) return 'dark';
    return 'light';
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initDarkMode);
  } else {
    initDarkMode();
  }
})();
