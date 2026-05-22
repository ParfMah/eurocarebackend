/**
 * =====================================================
 * EUROCARE HUMANITAIRE — JavaScript Principal
 * Fichier : public/assets/js/main.js
 * Description : Interactions globales, animations,
 *   compteurs, toasts, modals, révélation au scroll.
 * =====================================================
 */

'use strict';

/* =====================================================
   UTILITAIRES
   ===================================================== */
const $ = (sel, ctx = document) => ctx.querySelector(sel);
const $$ = (sel, ctx = document) => [...ctx.querySelectorAll(sel)];
const on = (el, ev, fn, opts) => el && el.addEventListener(ev, fn, opts);
const off = (el, ev, fn) => el && el.removeEventListener(ev, fn);

/**
 * Émet une requête fetch JSON avec CSRF
 */
async function apiFetch(url, options = {}) {
  const defaults = {
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-Token': window.CSRF_TOKEN || '',
      'X-Requested-With': 'XMLHttpRequest',
    },
  };
  const res = await fetch(url, { ...defaults, ...options, headers: { ...defaults.headers, ...(options.headers || {}) } });
  if (!res.ok) throw new Error(`HTTP ${res.status}`);
  return res.json();
}

/* =====================================================
   CHARGEUR DE PAGE
   ===================================================== */
function initPageLoader() {
  const loader = $('#pageLoader');
  if (!loader) return;

  window.addEventListener('load', () => {
    setTimeout(() => {
      loader.classList.add('fade-out');
      setTimeout(() => loader.remove(), 350);
    }, 300);
  });
}

/* =====================================================
   TOAST NOTIFICATIONS
   ===================================================== */
const Toast = {
  container: null,

  init() {
    this.container = $('#toast-container');
    if (!this.container) {
      this.container = document.createElement('div');
      this.container.id = 'toast-container';
      document.body.appendChild(this.container);
    }
  },

  show(message, type = 'info', title = '', duration = 4000) {
    if (!this.container) this.init();

    const icons = {
      success: '✓', error: '✕', warning: '⚠', info: 'ℹ',
      succes: '✓', erreur: '✕', attention: '⚠',
    };
    const titles = {
      success: 'Succès', error: 'Erreur', warning: 'Attention', info: 'Information',
      succes: 'Succès', erreur: 'Erreur', attention: 'Attention',
    };

    const toast = document.createElement('div');
    toast.className = `toast toast-${type === 'succes' ? 'success' : type === 'erreur' ? 'error' : type === 'attention' ? 'warning' : type}`;
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
      <span class="toast-icon">${icons[type] || 'ℹ'}</span>
      <div class="toast-body">
        <div class="toast-title">${title || titles[type] || 'Notification'}</div>
        <div class="toast-msg">${message}</div>
      </div>
      <button class="toast-close" aria-label="Fermer">&times;</button>
      <div class="toast-progress"></div>
    `;

    this.container.appendChild(toast);

    // Fermeture manuelle
    on($('.toast-close', toast), 'click', () => this.dismiss(toast));

    // Auto-dismiss
    const timer = setTimeout(() => this.dismiss(toast), duration);

    // Pause au survol
    on(toast, 'mouseenter', () => clearTimeout(timer));
    on(toast, 'mouseleave', () => setTimeout(() => this.dismiss(toast), 1500));

    return toast;
  },

  dismiss(toast) {
    if (!toast || !toast.parentNode) return;
    toast.classList.add('hiding');
    setTimeout(() => toast.remove(), 320);
  },

  success(msg, title = '') { return this.show(msg, 'success', title); },
  error(msg, title = '')   { return this.show(msg, 'error', title); },
  warning(msg, title = '') { return this.show(msg, 'warning', title); },
  info(msg, title = '')    { return this.show(msg, 'info', title); },
};

/* =====================================================
   MODAL
   ===================================================== */
const Modal = {
  open(modalId) {
    const overlay = $(`#${modalId}`);
    if (!overlay) return;
    overlay.classList.add('active');
    document.body.style.overflow = 'hidden';
    overlay.setAttribute('aria-hidden', 'false');
    // Focus sur premier élément focusable
    const focusable = $('button, input, select, textarea, a', overlay);
    focusable && focusable.focus();
  },

  close(modalId) {
    const overlay = modalId ? $(`#${modalId}`) : $('.modal-overlay.active');
    if (!overlay) return;
    overlay.classList.remove('active');
    document.body.style.overflow = '';
    overlay.setAttribute('aria-hidden', 'true');
  },

  init() {
    // Fermeture au clic sur overlay
    $$('.modal-overlay').forEach(overlay => {
      on(overlay, 'click', e => {
        if (e.target === overlay) this.close();
      });
    });

    // Boutons de fermeture
    $$('[data-modal-close]').forEach(btn => {
      on(btn, 'click', () => {
        const target = btn.dataset.modalClose;
        this.close(target || undefined);
      });
    });

    // Boutons d'ouverture
    $$('[data-modal-open]').forEach(btn => {
      on(btn, 'click', () => this.open(btn.dataset.modalOpen));
    });

    // Fermeture avec Escape
    on(document, 'keydown', e => {
      if (e.key === 'Escape') this.close();
    });
  },
};

/* =====================================================
   ANIMATIONS AU SCROLL (Intersection Observer)
   ===================================================== */
function initScrollAnimations() {
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
        observer.unobserve(entry.target); // Animer une seule fois
      }
    });
  }, {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px',
  });

  $$('.reveal').forEach(el => observer.observe(el));
}

/* =====================================================
   COMPTEURS ANIMÉS
   ===================================================== */
function animateCounter(el, target, duration = 2000, suffix = '') {
  const start     = parseInt(el.textContent.replace(/[^0-9]/g, '')) || 0;
  const startTime = performance.now();

  function easeOut(t) { return 1 - Math.pow(1 - t, 3); }

  function update(now) {
    const elapsed  = now - startTime;
    const progress = Math.min(elapsed / duration, 1);
    const current  = Math.floor(start + (target - start) * easeOut(progress));

    el.textContent = current.toLocaleString('fr-FR') + suffix;

    if (progress < 1) requestAnimationFrame(update);
    else el.textContent = target.toLocaleString('fr-FR') + suffix;
  }

  requestAnimationFrame(update);
}

function initCounters() {
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (!entry.isIntersecting) return;
      const el = entry.target;
      const target  = parseInt(el.dataset.count || '0');
      const suffix  = el.dataset.suffix || '';
      animateCounter(el, target, 2000, suffix);
      observer.unobserve(el);
    });
  }, { threshold: 0.5 });

  $$('[data-count]').forEach(el => observer.observe(el));
}

/* =====================================================
   BARRES DE PROGRESSION ANIMÉES
   ===================================================== */
function initProgressBars() {
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (!entry.isIntersecting) return;
      const bar   = entry.target;
      const value = parseInt(bar.dataset.progress || '0');
      setTimeout(() => { bar.style.width = value + '%'; }, 100);
      observer.unobserve(bar);
    });
  }, { threshold: 0.3 });

  $$('[data-progress]').forEach(bar => {
    bar.style.width = '0%';
    observer.observe(bar);
  });
}

/* =====================================================
   FORMULAIRES — VALIDATION CLIENT
   ===================================================== */
function initFormValidation() {
  $$('form[data-validate]').forEach(form => {
    on(form, 'submit', e => {
      let valid = true;

      $$('[required]', form).forEach(field => {
        clearFieldError(field);
        if (!field.value.trim()) {
          setFieldError(field, 'Ce champ est requis.');
          valid = false;
        }
      });

      // Validation email
      $$('[type="email"]', form).forEach(field => {
        if (field.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(field.value)) {
          setFieldError(field, 'Adresse email invalide.');
          valid = false;
        }
      });

      // Validation confirmation mot de passe
      const pwd1 = $('[name="password"]', form);
      const pwd2 = $('[name="password_confirm"]', form);
      if (pwd1 && pwd2 && pwd1.value && pwd1.value !== pwd2.value) {
        setFieldError(pwd2, 'Les mots de passe ne correspondent pas.');
        valid = false;
      }

      if (!valid) {
        e.preventDefault();
        const firstError = $('.form-control.is-invalid', form);
        firstError && firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
    });

    // Validation en temps réel
    $$('.form-control', form).forEach(field => {
      on(field, 'blur', () => {
        if (field.hasAttribute('required') && !field.value.trim()) {
          setFieldError(field, 'Ce champ est requis.');
        } else {
          clearFieldError(field);
        }
      });

      on(field, 'input', () => {
        if (field.value.trim()) clearFieldError(field);
      });
    });
  });
}

function setFieldError(field, message) {
  field.classList.add('is-invalid');
  field.classList.remove('is-valid');
  const existing = field.parentNode.querySelector('.form-error');
  if (!existing) {
    const error = document.createElement('div');
    error.className = 'form-error';
    error.textContent = message;
    field.parentNode.appendChild(error);
  }
}

function clearFieldError(field) {
  field.classList.remove('is-invalid');
  const error = field.parentNode.querySelector('.form-error');
  if (error) error.remove();
}

/* =====================================================
   INDICATEUR DE FORCE DU MOT DE PASSE
   ===================================================== */
function initPasswordStrength() {
  $$('[data-password-strength]').forEach(field => {
    const indicator = $(`#${field.dataset.passwordStrength}`);
    if (!indicator) return;

    on(field, 'input', () => {
      const pwd = field.value;
      const score = calcPasswordStrength(pwd);
      const labels = ['', 'Très faible', 'Faible', 'Moyen', 'Fort', 'Très fort'];
      const colors = ['', '#ef4444', '#f97316', '#f59e0b', '#22c55e', '#10b981'];

      indicator.innerHTML = `
        <div style="height:4px;background:#e5e7eb;border-radius:9999px;overflow:hidden;margin-top:8px">
          <div style="height:100%;width:${score * 20}%;background:${colors[score]};border-radius:9999px;transition:all .3s"></div>
        </div>
        <div style="font-size:12px;margin-top:4px;color:${colors[score]}">${labels[score] || ''}</div>
      `;
    });
  });
}

function calcPasswordStrength(pwd) {
  let score = 0;
  if (!pwd) return 0;
  if (pwd.length >= 8)  score++;
  if (pwd.length >= 12) score++;
  if (/[a-z]/.test(pwd) && /[A-Z]/.test(pwd)) score++;
  if (/[0-9]/.test(pwd)) score++;
  if (/[^A-Za-z0-9]/.test(pwd)) score++;
  return Math.min(score, 5);
}

/* =====================================================
   TOGGLE VISIBILITÉ MOT DE PASSE
   ===================================================== */
function initPasswordToggle() {
  $$('[data-toggle-password]').forEach(btn => {
    const targetId = btn.dataset.togglePassword;
    const input    = $(`#${targetId}`);
    if (!input) return;

    on(btn, 'click', () => {
      const isText = input.type === 'text';
      input.type   = isText ? 'password' : 'text';
      btn.setAttribute('aria-label', isText ? 'Afficher le mot de passe' : 'Masquer le mot de passe');
      // Changer l'icône
      const eyeOpen  = $('.eye-open',  btn);
      const eyeClose = $('.eye-close', btn);
      if (eyeOpen)  eyeOpen.style.display  = isText ? 'block' : 'none';
      if (eyeClose) eyeClose.style.display = isText ? 'none'  : 'block';
    });
  });
}

/* =====================================================
   TABS
   ===================================================== */
function initTabs() {
  $$('[data-tabs]').forEach(tabGroup => {
    const tabs    = $$('[data-tab]', tabGroup);
    const panels  = $$('[data-panel]', tabGroup);

    tabs.forEach(tab => {
      on(tab, 'click', () => {
        const target = tab.dataset.tab;

        tabs.forEach(t => t.classList.remove('active'));
        panels.forEach(p => p.classList.remove('active'));

        tab.classList.add('active');
        const panel = $(`[data-panel="${target}"]`, tabGroup);
        if (panel) panel.classList.add('active');

        // Mettre à jour l'URL sans recharger
        const url = new URL(window.location);
        url.searchParams.set('tab', target);
        history.replaceState(null, '', url);
      });
    });

    // Restaurer l'onglet depuis l'URL
    const urlTab = new URLSearchParams(window.location.search).get('tab');
    if (urlTab) {
      const activeTab = $(`[data-tab="${urlTab}"]`, tabGroup);
      if (activeTab) activeTab.click();
    }
  });
}

/* =====================================================
   ACCORDÉON FAQ
   ===================================================== */
function initAccordion() {
  $$('.faq-question').forEach(question => {
    on(question, 'click', () => {
      const item      = question.closest('.faq-item');
      const isOpen    = item.classList.contains('open');
      const accordion = item.closest('.faq-accordion');

      // Fermer les autres (accordion single)
      if (accordion) {
        $$('.faq-item.open', accordion).forEach(i => {
          if (i !== item) i.classList.remove('open');
        });
      }

      item.classList.toggle('open', !isOpen);
      question.setAttribute('aria-expanded', !isOpen);
    });
  });
}

/* =====================================================
   MONTANTS DE DONS — Boutons preset
   ===================================================== */
function initDonationAmounts() {
  const amountBtns  = $$('.amount-btn');
  const amountInput = $('[name="montant"]') || $('[name="amount"]');

  amountBtns.forEach(btn => {
    on(btn, 'click', () => {
      amountBtns.forEach(b => b.classList.remove('selected'));
      btn.classList.add('selected');
      if (amountInput) {
        amountInput.value = btn.dataset.amount || btn.textContent.replace(/[^0-9]/g, '');
        amountInput.dispatchEvent(new Event('input'));
      }
    });
  });

  // Désélectionner les presets si montant personnalisé
  if (amountInput) {
    on(amountInput, 'input', () => {
      const val = amountInput.value;
      amountBtns.forEach(btn => {
        const btnAmt = btn.dataset.amount || btn.textContent.replace(/[^0-9]/g, '');
        btn.classList.toggle('selected', btnAmt === val);
      });
    });
  }
}

/* =====================================================
   UPLOAD DE FICHIERS — Preview
   ===================================================== */
function initFileUpload() {
  $$('[data-file-preview]').forEach(input => {
    const previewId = input.dataset.filePreview;
    const preview   = $(`#${previewId}`);
    if (!preview) return;

    on(input, 'change', () => {
      const file = input.files[0];
      if (!file) return;

      if (file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = e => {
          preview.innerHTML = `<img src="${e.target.result}" style="max-width:100%;max-height:200px;border-radius:8px;object-fit:cover">`;
        };
        reader.readAsDataURL(file);
      } else {
        preview.innerHTML = `<div class="file-preview-name">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
          ${file.name} (${(file.size/1024).toFixed(1)} Ko)
        </div>`;
      }
    });
  });

  // Drag & Drop
  $$('.drop-zone').forEach(zone => {
    const input = $('input[type="file"]', zone);

    on(zone, 'dragover', e => {
      e.preventDefault();
      zone.classList.add('drag-over');
    });

    on(zone, 'dragleave', () => zone.classList.remove('drag-over'));

    on(zone, 'drop', e => {
      e.preventDefault();
      zone.classList.remove('drag-over');
      if (input && e.dataTransfer.files.length) {
        input.files = e.dataTransfer.files;
        input.dispatchEvent(new Event('change'));
      }
    });

    on(zone, 'click', () => input && input.click());
  });
}

/* =====================================================
   CONFIRMATION DE SUPPRESSION
   ===================================================== */
function initConfirmActions() {
  $$('[data-confirm]').forEach(btn => {
    on(btn, 'click', e => {
      const message = btn.dataset.confirm || 'Êtes-vous sûr de vouloir effectuer cette action ?';
      if (!confirm(message)) {
        e.preventDefault();
        e.stopPropagation();
      }
    });
  });
}

/* =====================================================
   COPY TO CLIPBOARD
   ===================================================== */
function initCopyButtons() {
  $$('[data-copy]').forEach(btn => {
    on(btn, 'click', async () => {
      const text = btn.dataset.copy || btn.textContent;
      try {
        await navigator.clipboard.writeText(text);
        const orig = btn.textContent;
        btn.textContent = 'Copié !';
        setTimeout(() => btn.textContent = orig, 2000);
        Toast.success('Texte copié dans le presse-papier');
      } catch {
        Toast.error('Impossible de copier');
      }
    });
  });
}

/* =====================================================
   SMOOTH SCROLL POUR ANCRES
   ===================================================== */
function initSmoothScroll() {
  $$('a[href^="#"]').forEach(link => {
    on(link, 'click', e => {
      const href   = link.getAttribute('href');
      if (href === '#') return;
      const target = $(href);
      if (!target) return;
      e.preventDefault();
      const headerH = $('#siteHeader')?.offsetHeight || 70;
      const top     = target.getBoundingClientRect().top + window.scrollY - headerH - 20;
      window.scrollTo({ top, behavior: 'smooth' });
    });
  });
}

/* =====================================================
   STICKY HEADER — changer style au scroll
   ===================================================== */
function initStickyHeader() {
  const header = $('#siteHeader');
  if (!header) return;

  let lastScroll = 0;

  function handleScroll() {
    const current = window.scrollY;

    // Ajouter/retirer classe 'scrolled'
    header.classList.toggle('scrolled', current > 10);

    lastScroll = current;
  }

  on(window, 'scroll', handleScroll, { passive: true });
  handleScroll(); // Init
}

/* =====================================================
   NEWSLETTER AJAX
   ===================================================== */
function initNewsletter() {
  const form = $('[data-newsletter]');
  if (!form) return;

  on(form, 'submit', async e => {
    e.preventDefault();
    const email  = $('[name="email"]', form)?.value;
    const btn    = $('[type="submit"]', form);

    if (!email) return;

    btn && (btn.disabled = true);
    btn && (btn.textContent = 'Inscription...');

    try {
      const res = await apiFetch(BASE_URL + '/api/newsletter', {
        method: 'POST',
        body: JSON.stringify({ email, _csrf: CSRF_TOKEN }),
      });
      if (res.success) {
        Toast.success('Merci ! Vous êtes inscrit à notre newsletter.');
        form.reset();
      } else {
        Toast.error(res.message || 'Une erreur est survenue.');
      }
    } catch {
      Toast.error('Erreur réseau. Veuillez réessayer.');
    } finally {
      btn && (btn.disabled = false);
      btn && (btn.textContent = 'S\'inscrire');
    }
  });
}

/* =====================================================
   INITIALISATION GLOBALE
   ===================================================== */
document.addEventListener('DOMContentLoaded', () => {
  initPageLoader();
  Toast.init();
  Modal.init();
  initScrollAnimations();
  initCounters();
  initProgressBars();
  initFormValidation();
  initPasswordStrength();
  initPasswordToggle();
  initTabs();
  initAccordion();
  initDonationAmounts();
  initFileUpload();
  initConfirmActions();
  initCopyButtons();
  initSmoothScroll();
  initStickyHeader();
  initNewsletter();

  // Exposer globalement les utilitaires
  window.Toast = Toast;
  window.Modal = Modal;
});
