document.addEventListener('DOMContentLoaded', function () {
  var toggle = document.getElementById('navToggle');
  var mobileNav = document.getElementById('mobileNav');
  var navIconOpen = document.getElementById('navIconOpen');
  var navIconClose = document.getElementById('navIconClose');
  if (toggle && mobileNav) {
    toggle.addEventListener('click', function () {
      var open = mobileNav.classList.toggle('hidden') === false;
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
      if (navIconOpen) navIconOpen.classList.toggle('hidden', open);
      if (navIconClose) navIconClose.classList.toggle('hidden', !open);
    });
  }
  // Navbar scrolled state (matches <Navbar/>) — header starts transparent, gains bg on scroll
  var header = document.getElementById('siteNav');
  if (header) {
    var onNavScroll = function () {
      var scrolled = window.scrollY > 40;
      if (scrolled) {
        header.classList.add('border-b', 'border-white/10', 'bg-[#111C2D]/95', 'shadow-lg', 'shadow-black/30', 'backdrop-blur-sm');
        header.setAttribute('data-scrolled', 'true');
      } else {
        header.classList.remove('border-b', 'border-white/10', 'bg-[#111C2D]/95', 'shadow-lg', 'shadow-black/30', 'backdrop-blur-sm');
        header.setAttribute('data-scrolled', 'false');
      }
    };
    onNavScroll();
    window.addEventListener('scroll', onNavScroll, { passive: true });
  }
  // Reveal-on-scroll (matches <Reveal/>)
  var revealEls = document.querySelectorAll('.reveal-on-scroll');
  revealEls.forEach(function (el) {
    var from = el.getAttribute('data-from');
    if (from === 'left') el.classList.add('reveal-from-left');
    else if (from === 'right') el.classList.add('reveal-from-right');
  });
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

  // TiltCard (matches <TiltCard/>) — [data-tilt] elements
  var tiltEls = document.querySelectorAll('.tilt-card[data-tilt]');
  if (tiltEls.length) {
    var reducedMotion3 = typeof window.matchMedia === 'function' && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (!reducedMotion3) {
      tiltEls.forEach(function (el) {
        var max = 7;
        el.style.transformStyle = 'preserve-3d';
        el.style.transition = 'transform 0.18s ease-out';
        var spot = el.querySelector('.tilt-spot');
        if (!spot) {
          spot = document.createElement('div');
          spot.setAttribute('aria-hidden', 'true');
          spot.className = 'tilt-spot pointer-events-none absolute inset-0 transition-opacity duration-300';
          spot.style.opacity = '0';
          spot.style.background = 'radial-gradient(220px circle at 50% 50%, rgba(0, 89, 187, 0.16), transparent 65%)';
          el.appendChild(spot);
        }
        var updateSpot = function (x, y, on) {
          if (!spot) return;
          spot.style.setProperty('--spot-x', x + 'px');
          spot.style.setProperty('--spot-y', y + 'px');
          spot.style.background = 'radial-gradient(220px circle at ' + x + 'px ' + y + 'px, rgba(0, 89, 187, 0.16), transparent 65%)';
          spot.style.opacity = on ? '1' : '0';
        };
        el.addEventListener('mouseenter', function () { updateSpot(0, 0, true); });
        el.addEventListener('mousemove', function (e) {
          var rect = el.getBoundingClientRect();
          var x = (e.clientX - rect.left) / rect.width - 0.5;
          var y = (e.clientY - rect.top) / rect.height - 0.5;
          el.style.transform = 'perspective(900px) rotateX(' + (-y * max).toFixed(2) + 'deg) rotateY(' + (x * max).toFixed(2) + 'deg)';
          updateSpot(e.clientX - rect.left, e.clientY - rect.top, true);
        });
        el.addEventListener('mouseleave', function () {
          el.style.transform = '';
          updateSpot(0, 0, false);
        });
      });
    }
  }

  // FAQ accordion — [data-faq] containers
  var faqContainers = document.querySelectorAll('[data-faq]');
  faqContainers.forEach(function (container) {
    var toggles = container.querySelectorAll('.faq-toggle');
    toggles.forEach(function (btn, idx) {
      btn.addEventListener('click', function () {
        var isOpen = btn.getAttribute('aria-expanded') === 'true';
        // close all
        toggles.forEach(function (b) {
          b.setAttribute('aria-expanded', 'false');
          var icon = b.querySelector('.faq-icon');
          if (icon) icon.classList.remove('rotate-45');
          var panel = b.parentElement.querySelector('.faq-panel');
          if (panel) panel.classList.replace('grid-rows-[1fr]', 'grid-rows-[0fr]');
          var span = b.querySelector('span');
          if (span) { span.classList.remove('text-tertiary'); span.classList.add('text-neutral'); }
        });
        // open clicked if was closed
        if (!isOpen) {
          btn.setAttribute('aria-expanded', 'true');
          var icon = btn.querySelector('.faq-icon');
          if (icon) icon.classList.add('rotate-45');
          var panel = btn.parentElement.querySelector('.faq-panel');
          if (panel) panel.classList.replace('grid-rows-[0fr]', 'grid-rows-[1fr]');
          var span = btn.querySelector('span');
          if (span) { span.classList.add('text-tertiary'); span.classList.remove('text-neutral'); }
        }
      });
    });
  });

  // CountUp (matches <CountUp/>) — [data-countup] elements
  var countEls = document.querySelectorAll('[data-countup]');
  if (countEls.length && 'IntersectionObserver' in window) {
    var reducedMotion4 = typeof window.matchMedia === 'function' && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    countEls.forEach(function (el) {
      var raw = el.getAttribute('data-countup') || el.textContent;
      var trimmed = raw.trim();
      var match = trimmed.match(/^([+-]?\d+(?:\.\d+)?)(.*)$/);
      if (!match) return;
      var target = parseFloat(match[1]);
      var decimals = match[1].includes('.') ? match[1].split('.')[1].length : 0;
      var suffix = match[2];
      if (reducedMotion4) return;
      var observer = new IntersectionObserver(function (entries) {
        if (!entries[0].isIntersecting) return;
        observer.disconnect();
        var start = performance.now();
        var duration = 1800;
        var tick = function (now) {
          var progress = Math.min((now - start) / duration, 1);
          var eased = 1 - Math.pow(1 - progress, 3);
          el.textContent = (target * eased).toFixed(decimals) + suffix;
          if (progress < 1) requestAnimationFrame(tick);
        };
        requestAnimationFrame(tick);
      }, { threshold: 0.4 });
      observer.observe(el);
    });
  }

  // Load More (services page) — [data-loadmore] buttons
  document.querySelectorAll('[data-loadmore]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var container = btn.closest('section').querySelector('.loadmore-container');
      if (!container) return;
      var hidden = container.querySelectorAll('.loadmore-item.hidden');
      var STEP = 3;
      for (var i = 0; i < Math.min(STEP, hidden.length); i++) {
        hidden[i].classList.remove('hidden');
      }
      var remaining = container.querySelectorAll('.loadmore-item.hidden');
      if (remaining.length === 0) btn.style.display = 'none';
    });
  });
});