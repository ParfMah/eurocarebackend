/**
 * =====================================================
 * EUROCARE HUMANITAIRE — Navigation JS
 * Fichier : public/assets/js/nav.js
 * Description : Menu mobile, dropdowns, notifications
 *   en temps réel, menu utilisateur.
 * =====================================================
 */

'use strict';

/* =====================================================
   MENU MOBILE
   ===================================================== */
function initMobileNav() {
  const toggle   = document.getElementById('menuToggle');
  const nav      = document.getElementById('mobileNav');
  const overlay  = document.getElementById('mobileOverlay');
  const closeBtn = document.getElementById('mobileNavClose');
  if (!toggle || !nav) return;

  let isOpen = false;

  function openMenu() {
    isOpen = true;
    nav.classList.add('active');
    overlay && overlay.classList.add('active');
    toggle.classList.add('open');
    toggle.setAttribute('aria-expanded', 'true');
    nav.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
    // Focus pour accessibilité
    const firstFocusable = nav.querySelector('a, button');
    firstFocusable && setTimeout(() => firstFocusable.focus(), 100);
  }

  function closeMenu() {
    isOpen = false;
    nav.classList.remove('active');
    overlay && overlay.classList.remove('active');
    toggle.classList.remove('open');
    toggle.setAttribute('aria-expanded', 'false');
    nav.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
    toggle.focus();
  }

  toggle.addEventListener('click', () => isOpen ? closeMenu() : openMenu());
  closeBtn && closeBtn.addEventListener('click', closeMenu);
  overlay && overlay.addEventListener('click', closeMenu);

  // Fermeture avec Escape
  document.addEventListener('keydown', e => {
    if (e.key === 'Escape' && isOpen) closeMenu();
  });

  // Sous-menus mobiles
  document.querySelectorAll('.mobile-nav-link.has-sub').forEach(btn => {
    btn.addEventListener('click', () => {
      const subId  = btn.dataset.sub;
      const sub    = document.getElementById(`sub-${subId}`);
      const isOpen = sub && sub.classList.contains('open');

      // Fermer tous les autres sous-menus
      document.querySelectorAll('.mobile-submenu.open').forEach(s => s.classList.remove('open'));
      document.querySelectorAll('.mobile-nav-link.has-sub').forEach(b => b.classList.remove('open'));

      if (!isOpen && sub) {
        sub.classList.add('open');
        btn.classList.add('open');
      }
    });
  });
}

/* =====================================================
   MENU UTILISATEUR (header)
   ===================================================== */
function initUserMenu() {
  const trigger = document.getElementById('userMenuBtn');
  if (!trigger) return;

  const navItem = trigger.closest('.nav-item');

  trigger.addEventListener('click', (e) => {
    e.stopPropagation();
    const isOpen = navItem.classList.contains('open');
    // Fermer tous les autres dropdowns ouverts
    document.querySelectorAll('.nav-item.open').forEach(item => item.classList.remove('open'));
    navItem.classList.toggle('open', !isOpen);
    trigger.classList.toggle('open', !isOpen);
    trigger.setAttribute('aria-expanded', !isOpen);
  });

  document.addEventListener('click', () => {
    navItem.classList.remove('open');
    trigger.classList.remove('open');
    trigger.setAttribute('aria-expanded', 'false');
  });
}

/* =====================================================
   DROPDOWN NOTIFICATIONS
   ===================================================== */
function initNotifications() {
  const notifBtn      = document.getElementById('notifBtn');
  const notifDropdown = document.getElementById('notifDropdown');
  const notifList     = document.getElementById('notifList');
  if (!notifBtn || !notifDropdown) return;

  let loaded = false;

  notifBtn.addEventListener('click', async (e) => {
    e.stopPropagation();
    const navItem = notifBtn.closest('.nav-item');
    const isOpen  = navItem.classList.contains('open');

    document.querySelectorAll('.nav-item.open').forEach(i => i.classList.remove('open'));
    navItem.classList.toggle('open', !isOpen);

    // Charger les notifications au premier clic
    if (!isOpen && !loaded) {
      loaded = true;
      try {
        const res = await fetch(BASE_URL + '/api/notifications', {
          headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();

        if (data.notifications && data.notifications.length > 0) {
          notifList.innerHTML = data.notifications.map(n => `
            <a href="${n.lien || '#'}" class="notif-item" data-id="${n.id}" style="display:flex;align-items:flex-start;gap:12px;padding:12px 16px;text-decoration:none;transition:background .15s;${!n.lu ? 'background:var(--color-blue-pale)' : ''}">
              <span style="width:8px;height:8px;border-radius:50%;background:${!n.lu ? 'var(--color-blue-mid)' : 'transparent'};flex-shrink:0;margin-top:6px"></span>
              <div>
                <div style="font-size:13px;font-weight:600;color:var(--color-gray-900)">${n.titre}</div>
                <div style="font-size:12px;color:var(--color-gray-600);margin-top:2px">${n.message.substring(0, 80)}${n.message.length > 80 ? '…' : ''}</div>
                <div style="font-size:11px;color:var(--color-gray-400);margin-top:4px">${n.time_ago}</div>
              </div>
            </a>
          `).join('');

          // Marquer comme lues au survol
          notifList.querySelectorAll('.notif-item').forEach(item => {
            item.addEventListener('mouseenter', () => markAsRead(item.dataset.id));
          });
        } else {
          notifList.innerHTML = '<div style="padding:24px;text-align:center;color:var(--color-gray-500);font-size:13px">Aucune notification</div>';
        }
      } catch {
        notifList.innerHTML = '<div style="padding:16px;text-align:center;color:var(--color-gray-500);font-size:13px">Erreur de chargement</div>';
      }
    }
  });

  document.addEventListener('click', () => {
    notifBtn.closest('.nav-item')?.classList.remove('open');
  });
}

/* Marquer une notification comme lue */
async function markAsRead(notifId) {
  if (!notifId) return;
  try {
    await fetch(BASE_URL + '/api/notifications/lire', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': window.CSRF_TOKEN || '',
        'X-Requested-With': 'XMLHttpRequest',
      },
      body: JSON.stringify({ id: notifId }),
    });
    // Mettre à jour le badge
    const badge = document.querySelector('.notif-count');
    if (badge) {
      const current = parseInt(badge.textContent) - 1;
      if (current <= 0) badge.remove();
      else badge.textContent = current;
    }
  } catch {}
}

/* =====================================================
   DROPDOWNS GÉNÉRIQUES
   ===================================================== */
function initDropdowns() {
  // Ouvrir les dropdowns au clic (desktop)
  document.querySelectorAll('.nav-item > button.nav-link').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      const item   = btn.closest('.nav-item');
      const isOpen = item.classList.contains('open');
      document.querySelectorAll('.nav-item.open').forEach(i => {
        if (i !== item) i.classList.remove('open');
      });
      item.classList.toggle('open', !isOpen);
      btn.setAttribute('aria-expanded', !isOpen);
    });
  });

  // Fermeture au clic extérieur
  document.addEventListener('click', () => {
    document.querySelectorAll('.nav-item.open').forEach(i => i.classList.remove('open'));
  });

  // Fermeture avec Escape
  document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
      document.querySelectorAll('.nav-item.open').forEach(i => i.classList.remove('open'));
    }
  });

  // Garder ouvert lors du clic dans le dropdown
  document.querySelectorAll('.dropdown-menu').forEach(menu => {
    menu.addEventListener('click', e => e.stopPropagation());
  });
}

/* =====================================================
   MARQUE LA PAGE ACTIVE DANS LA NAV
   ===================================================== */
function markActiveNavLinks() {
  const path = window.location.pathname;

  document.querySelectorAll('.nav-link[href], .mobile-nav-link[href]').forEach(link => {
    const href = link.getAttribute('href');
    if (!href || href === '#') return;

    // Extraire le chemin de l'URL
    const linkPath = new URL(href, window.location.origin).pathname;

    if (path === linkPath || (linkPath !== '/' && path.startsWith(linkPath))) {
      link.classList.add('active');
    }
  });
}

/* =====================================================
   BARRE DE SCROLL EN HAUT DE PAGE (optionnel)
   ===================================================== */
function initScrollProgress() {
  const bar = document.getElementById('scrollProgress');
  if (!bar) return;

  window.addEventListener('scroll', () => {
    const total  = document.body.scrollHeight - window.innerHeight;
    const pct    = total > 0 ? (window.scrollY / total) * 100 : 0;
    bar.style.width = pct + '%';
  }, { passive: true });
}

/* =====================================================
   BOUTON RETOUR EN HAUT
   ===================================================== */
function initBackToTop() {
  const btn = document.getElementById('backToTop');
  if (!btn) return;

  window.addEventListener('scroll', () => {
    btn.classList.toggle('visible', window.scrollY > 400);
  }, { passive: true });

  btn.addEventListener('click', () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });
}

/* =====================================================
   INITIALISATION
   ===================================================== */
document.addEventListener('DOMContentLoaded', () => {
  initMobileNav();
  initUserMenu();
  initNotifications();
  initDropdowns();
  markActiveNavLinks();
  initScrollProgress();
  initBackToTop();
});
