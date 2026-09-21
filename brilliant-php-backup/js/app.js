document.addEventListener('DOMContentLoaded', function () {
  var toggle = document.getElementById('navToggle');
  var mobileNav = document.getElementById('mobileNav');
  if (toggle && mobileNav) {
    toggle.addEventListener('click', function () {
      var open = mobileNav.classList.toggle('hidden') === false;
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
  }
  // Reveal-on-scroll (matches <Reveal/>)
  var revealEls = document.querySelectorAll('.reveal-on-scroll');
  if ('IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
          io.unobserve(entry.target);
        }
      });
    }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });
    revealEls.forEach(function (el) { io.observe(el); });
  } else {
    revealEls.forEach(function (el) { el.classList.add('visible'); });
  }

  // Reading progress bar (blog posts) — matches <ReadingProgress/>
  var progressEl = document.getElementById('readingProgress');
  if (progressEl) {
    var bar = progressEl.querySelector('.h-full');
    var reduceMotion = typeof window.matchMedia === 'function' &&
      window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (!reduceMotion) {
      var ticking = false;
      var updateProgress = function () {
        ticking = false;
        var doc = document.documentElement;
        var total = doc.scrollHeight - window.innerHeight;
        var value = total > 0 ? (window.scrollY / total) * 100 : 0;
        value = Math.min(100, Math.max(0, value));
        if (bar) bar.style.width = value.toFixed(1) + '%';
      };
      var onProgressScroll = function () {
        if (!ticking) {
          ticking = true;
          requestAnimationFrame(updateProgress);
        }
      };
      updateProgress();
      window.addEventListener('scroll', onProgressScroll, { passive: true });
      window.addEventListener('resize', onProgressScroll);
    } else if (bar) {
      bar.style.width = '0%';
    }
  }

  // Parallax hero (matches <Parallax/>) — [data-parallax] wrappers
  var parallaxEls = document.querySelectorAll('[data-parallax]');
  if (parallaxEls.length && 'IntersectionObserver' in window) {
    var reduceMotion2 = typeof window.matchMedia === 'function' &&
      window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (!reduceMotion2) {
      var pTick = false;
      var updateParallax = function () {
        pTick = false;
        parallaxEls.forEach(function (el) {
          var rect = el.getBoundingClientRect();
          var center = window.innerHeight / 2;
          var shift = (rect.top + rect.height / 2 - center) * -parseFloat(el.getAttribute('data-parallax') || '0.1');
          el.style.transform = 'translate3d(0, ' + shift.toFixed(1) + 'px, 0)';
        });
      };
      var onParallaxScroll = function () {
        if (!pTick) {
          pTick = true;
          requestAnimationFrame(updateParallax);
        }
      };
      updateParallax();
      window.addEventListener('scroll', onParallaxScroll, { passive: true });
      window.addEventListener('resize', onParallaxScroll);
    }
  }
});