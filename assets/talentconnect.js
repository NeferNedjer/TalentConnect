/**
 * TalentConnect â€” JS minimal pour Symfony (nav, header, toasts, auth dÃ©coratif)
 * Compatible Turbo Drive (symfony/ux-turbo)
 */
(function () {
  'use strict';

  let globalBound = false;
  let navBound = false;

  document.addEventListener('DOMContentLoaded', boot);
  document.addEventListener('turbo:load', boot);
  document.addEventListener('turbo:render', initPage);

  function boot() {
    bindGlobalHandlers();
    initPage();
  }

  function initPage() {
    initMobileNav();
    initUserMenu();
    initHeaderScroll();
    initScrollReveal();
    initRegisterForm();
    initHomeSearch();
    initImagePreview();
    initAnnouncementForm();
  }

  function bindGlobalHandlers() {
    if (globalBound) return;
    globalBound = true;

    document.addEventListener('click', function (e) {
      if (!e.target.closest('[data-user-menu]')) {
        closeAllUserMenus();
      }

      const forgot = e.target.closest('[data-forgot-password]');
      if (forgot) {
        e.preventDefault();
        showToast('Email de rÃ©initialisation envoyÃ© â€” fonctionnalitÃ© Ã  venir.');
        return;
      }

      const oauth = e.target.closest('[data-oauth]');
      if (oauth) {
        const provider = oauth.dataset.oauth === 'google' ? 'Google' : 'Apple';
        showToast('Connexion ' + provider + ' â€” fonctionnalitÃ© Ã  venir.');
      }
    });

    document.addEventListener('keydown', function (e) {
      if (e.key !== 'Escape') return;
      closeAllUserMenus();
      const nav = document.querySelector('.nav');
      if (nav?.classList.contains('nav--open')) {
        closeMobileNav();
      }
    });
  }

  function closeAllUserMenus(exceptMenu) {
    document.querySelectorAll('[data-user-menu].user-menu--open').forEach(function (menu) {
      if (menu === exceptMenu) return;
      const trigger = menu.querySelector('.user-menu__trigger');
      const dropdown = menu.querySelector('.user-menu__dropdown');
      menu.classList.remove('user-menu--open');
      trigger?.setAttribute('aria-expanded', 'false');
      dropdown?.setAttribute('hidden', '');
    });
  }

  function initUserMenu() {
    document.querySelectorAll('[data-user-menu]').forEach(function (menu) {
      if (menu.dataset.tcUserMenuBound) return;
      menu.dataset.tcUserMenuBound = 'true';

      const trigger = menu.querySelector('.user-menu__trigger');
      const dropdown = menu.querySelector('.user-menu__dropdown');
      if (!trigger || !dropdown) return;

      function getItems() {
        return Array.from(dropdown.querySelectorAll('[role="menuitem"]'));
      }

      function isOpen() {
        return menu.classList.contains('user-menu--open');
      }

      function openMenu() {
        closeAllUserMenus(menu);
        menu.classList.add('user-menu--open');
        trigger.setAttribute('aria-expanded', 'true');
        dropdown.removeAttribute('hidden');
        getItems()[0]?.focus();
      }

      function closeMenu(restoreFocus) {
        menu.classList.remove('user-menu--open');
        trigger.setAttribute('aria-expanded', 'false');
        dropdown.setAttribute('hidden', '');
        if (restoreFocus) trigger.focus();
      }

      trigger.addEventListener('click', function (e) {
        e.stopPropagation();
        if (isOpen()) {
          closeMenu(true);
        } else {
          openMenu();
        }
      });

      trigger.addEventListener('keydown', function (e) {
        if (e.key === 'ArrowDown' || e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          if (!isOpen()) {
            openMenu();
          } else if (e.key === 'ArrowDown') {
            getItems()[0]?.focus();
          }
        }
        if (e.key === 'Escape' && isOpen()) {
          e.preventDefault();
          closeMenu(true);
        }
      });

      dropdown.addEventListener('keydown', function (e) {
        const items = getItems();
        const currentIndex = items.indexOf(document.activeElement);

        if (e.key === 'Escape') {
          e.preventDefault();
          closeMenu(true);
          return;
        }

        if (e.key === 'ArrowDown') {
          e.preventDefault();
          items[(currentIndex + 1) % items.length]?.focus();
        }

        if (e.key === 'ArrowUp') {
          e.preventDefault();
          items[(currentIndex - 1 + items.length) % items.length]?.focus();
        }

        if (e.key === 'Home') {
          e.preventDefault();
          items[0]?.focus();
        }

        if (e.key === 'End') {
          e.preventDefault();
          items[items.length - 1]?.focus();
        }
      });
    });
  }

  function initMobileNav() {
    const toggle = document.querySelector('.nav-toggle');
    const nav = document.querySelector('.nav');
    const overlay = document.querySelector('.nav-overlay');

    if (!toggle || !nav) return;

    if (navBound) return;
    navBound = true;

    toggle.addEventListener('click', function () {
      if (nav.classList.contains('nav--open')) {
        closeMobileNav();
      } else {
        openMobileNav();
      }
    });

    overlay?.addEventListener('click', closeMobileNav);

    nav.querySelectorAll('.nav__link, .header__actions--mobile a').forEach(function (link) {
      link.addEventListener('click', closeMobileNav);
    });
  }

  function openMobileNav() {
    closeAllUserMenus();
    const toggle = document.querySelector('.nav-toggle');
    const nav = document.querySelector('.nav');
    const overlay = document.querySelector('.nav-overlay');

    toggle?.classList.add('nav-toggle--open');
    nav?.classList.add('nav--open');
    overlay?.classList.add('nav-overlay--visible');
    toggle?.setAttribute('aria-expanded', 'true');
    document.body.style.overflow = 'hidden';
  }

  function closeMobileNav() {
    const toggle = document.querySelector('.nav-toggle');
    const nav = document.querySelector('.nav');
    const overlay = document.querySelector('.nav-overlay');

    toggle?.classList.remove('nav-toggle--open');
    nav?.classList.remove('nav--open');
    overlay?.classList.remove('nav-overlay--visible');
    toggle?.setAttribute('aria-expanded', 'false');
    document.body.style.overflow = '';
  }

  function initHeaderScroll() {
    const header = document.querySelector('.header');
    if (!header || header.dataset.tcScrollBound) return;

    header.dataset.tcScrollBound = 'true';

    function onScroll() {
      header.classList.toggle('header--scrolled', window.scrollY > 20);
    }

    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }

  function initScrollReveal() {
    document.querySelectorAll('.reveal').forEach(function (el) {
      /* Pas de flash au chargement Turbo sur auth et espace membre */
      if (el.closest('.auth-main') || el.closest('.dashboard')) {
        el.classList.add('reveal--visible');
        return;
      }

      el.classList.remove('reveal--visible');
    });

    const toObserve = document.querySelectorAll('.reveal:not(.reveal--visible)');
    if (!toObserve.length) return;

    const observer = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            entry.target.classList.add('reveal--visible');
            observer.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.1, rootMargin: '0px 0px -40px 0px' }
    );

    toObserve.forEach(function (el) {
      observer.observe(el);
    });
  }

  function initHomeSearch() {
    document.querySelectorAll('[data-home-search], [data-search-form]').forEach(function (form) {
      if (form.dataset.tcHomeSearchBound) return;
      form.dataset.tcHomeSearchBound = 'true';

      form.addEventListener('submit', function (e) {
        e.preventDefault();
        showToast('Recherche â€” fonctionnalitÃ© Ã  venir.');
      });
    });
  }

  function initRegisterForm() {
    const form = document.querySelector('[data-register-form]');
    if (!form || form.dataset.tcRegisterBound) return;

    form.dataset.tcRegisterBound = 'true';

    const password = form.querySelector('[data-password-main]');
    const confirm = form.querySelector('[data-password-confirm]');
    const confirmError = form.querySelector('[data-password-confirm-error]');

    if (!password || !confirm) return;

    function clearConfirmError() {
      confirm.classList.remove('form-group__input--error');
      if (confirmError) confirmError.hidden = true;
    }

    confirm.addEventListener('input', clearConfirmError);
    password.addEventListener('input', clearConfirmError);

    form.addEventListener('submit', function (e) {
      if (password.value !== confirm.value) {
        e.preventDefault();
        confirm.classList.add('form-group__input--error');
        if (confirmError) confirmError.hidden = false;
        confirm.focus();
        showToast('Les mots de passe ne correspondent pas.');
      }
    });
  }

  function initImagePreview() {
    document.querySelectorAll('input[type="file"][data-image-preview]').forEach(function (input) {
      if (input.dataset.tcImagePreviewBound) return;
      input.dataset.tcImagePreviewBound = 'true';

      input.addEventListener('change', function () {
        const targetId = input.dataset.imagePreview;
        const target = targetId ? document.getElementById(targetId) : null;
        const file = input.files && input.files[0];

        if (!target) return;

        if (!file || !file.type.startsWith('image/')) {
          return;
        }

        const reader = new FileReader();
        reader.onload = function (event) {
          if (target.dataset.placeholder === 'true') {
            const img = document.createElement('img');
            img.id = target.id;
            img.src = event.target.result;
            img.alt = '';
            img.setAttribute('data-image-preview-target', '');
            target.replaceWith(img);
          } else {
            target.src = event.target.result;
          }
        };
        reader.readAsDataURL(file);
      });
    });
  }

  function initAnnouncementForm() {
    document.querySelectorAll('[data-announcement-form]').forEach(function (form) {
      if (form.dataset.tcAnnouncementFormBound) return;
      form.dataset.tcAnnouncementFormBound = 'true';

      const typeSelect = form.querySelector('[data-remuneration-type]');
      const amountField = form.querySelector('[data-remuneration-amount-field]');
      if (!typeSelect || !amountField) return;

      const amountInput = amountField.querySelector('input');

      function updateAmountVisibility() {
        const isFixed = typeSelect.value === 'fixed';
        amountField.hidden = !isFixed;
        if (amountInput) {
          amountInput.disabled = !isFixed;
          if (!isFixed) {
            amountInput.value = '';
          }
        }
      }

      typeSelect.addEventListener('change', updateAmountVisibility);
      updateAmountVisibility();
    });
  }

  function showToast(message) {
    document.querySelector('.toast')?.remove();

    const toast = document.createElement('div');
    toast.className = 'toast';
    toast.innerHTML =
      '<svg class="toast__icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">' +
      '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>' +
      '<polyline points="22 4 12 14.01 9 11.01"/>' +
      '</svg>' +
      '<span>' + message + '</span>';

    document.body.appendChild(toast);

    requestAnimationFrame(function () {
      toast.classList.add('toast--visible');
    });

    setTimeout(function () {
      toast.classList.remove('toast--visible');
      setTimeout(function () {
        toast.remove();
      }, 300);
    }, 4000);
  }

  window.TalentConnect = { showToast: showToast };
})();

