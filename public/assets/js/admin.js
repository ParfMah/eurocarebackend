/**
 * =====================================================
 * EUROCARE HUMANITAIRE — JavaScript Administration
 * Fichier : public/assets/js/admin.js
 * =====================================================
 */
'use strict';

/* =====================================================
   SIDEBAR
   ===================================================== */
function initAdminSidebar() {
  const sidebar  = document.getElementById('adminSidebar');
  const toggle   = document.getElementById('sidebarToggle');
  const overlay  = document.getElementById('adminOverlay');
  const content  = document.querySelector('.admin-content');
  if (!sidebar || !toggle) return;

  const isMobile = () => window.innerWidth < 1024;
  const isCollapsed = () => localStorage.getItem('sidebarCollapsed') === '1';

  function applySidebar() {
    if (isMobile()) {
      sidebar.classList.remove('collapsed');
    } else {
      sidebar.classList.toggle('collapsed', isCollapsed());
    }
  }

  applySidebar();
  window.addEventListener('resize', applySidebar);

  toggle.addEventListener('click', () => {
    if (isMobile()) {
      const isOpen = sidebar.classList.contains('open');
      sidebar.classList.toggle('open', !isOpen);
      overlay && overlay.classList.toggle('active', !isOpen);
    } else {
      const newState = !isCollapsed();
      localStorage.setItem('sidebarCollapsed', newState ? '1' : '0');
      sidebar.classList.toggle('collapsed', newState);
    }
  });

  overlay && overlay.addEventListener('click', () => {
    sidebar.classList.remove('open');
    overlay.classList.remove('active');
  });
}

/* =====================================================
   TABLEAUX TRIABLES
   ===================================================== */
function initSortableTables() {
  document.querySelectorAll('.admin-data-table th[data-sort]').forEach(th => {
    th.style.cursor = 'pointer';
    th.addEventListener('click', () => {
      const table = th.closest('table');
      const tbody = table.querySelector('tbody');
      const col   = th.cellIndex;
      const asc   = th.dataset.direction !== 'asc';
      th.dataset.direction = asc ? 'asc' : 'desc';

      // Supprimer indicateurs autres colonnes
      table.querySelectorAll('th').forEach(h => {
        h.textContent = h.textContent.replace(' ↑', '').replace(' ↓', '');
        delete h.dataset.direction;
      });
      th.textContent += asc ? ' ↑' : ' ↓';
      th.dataset.direction = asc ? 'asc' : 'desc';

      const rows = [...tbody.querySelectorAll('tr')];
      rows.sort((a, b) => {
        const A = a.cells[col]?.textContent.trim() || '';
        const B = b.cells[col]?.textContent.trim() || '';
        const numA = parseFloat(A.replace(/[^0-9.-]/g, ''));
        const numB = parseFloat(B.replace(/[^0-9.-]/g, ''));
        if (!isNaN(numA) && !isNaN(numB)) return asc ? numA - numB : numB - numA;
        return asc ? A.localeCompare(B, 'fr') : B.localeCompare(A, 'fr');
      });
      rows.forEach(r => tbody.appendChild(r));
    });
  });
}

/* =====================================================
   SÉLECTION MULTIPLE (checkboxes)
   ===================================================== */
function initBulkSelect() {
  const selectAll = document.getElementById('selectAll');
  const checkboxes = document.querySelectorAll('.table-checkbox:not(#selectAll)');
  const bulkBar    = document.getElementById('bulkActionBar');

  if (!selectAll) return;

  function updateBulkBar() {
    const checked = document.querySelectorAll('.table-checkbox:checked:not(#selectAll)').length;
    if (bulkBar) {
      bulkBar.style.display = checked > 0 ? 'flex' : 'none';
      const countEl = bulkBar.querySelector('.bulk-count');
      if (countEl) countEl.textContent = checked;
    }
  }

  selectAll.addEventListener('change', () => {
    checkboxes.forEach(cb => cb.checked = selectAll.checked);
    updateBulkBar();
  });

  checkboxes.forEach(cb => {
    cb.addEventListener('change', () => {
      selectAll.checked = [...checkboxes].every(c => c.checked);
      updateBulkBar();
    });
  });
}

/* =====================================================
   FILTRES INLINE
   ===================================================== */
function initInlineFilter() {
  const filterInput = document.getElementById('tableFilter');
  const table       = document.querySelector('.admin-data-table tbody');
  if (!filterInput || !table) return;

  filterInput.addEventListener('input', () => {
    const query = filterInput.value.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
    table.querySelectorAll('tr').forEach(row => {
      const text = row.textContent.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
      row.style.display = text.includes(query) ? '' : 'none';
    });
  });
}

/* =====================================================
   ACTIONS AJAX (changer statut, valider, etc.)
   ===================================================== */
function initAjaxActions() {
  document.addEventListener('click', async (e) => {
    const btn = e.target.closest('[data-action]');
    if (!btn) return;

    const action = btn.dataset.action;
    const url    = btn.dataset.url;
    const confirm_msg = btn.dataset.confirm;

    if (!url) return;

    if (confirm_msg && !confirm(confirm_msg)) return;

    // Désactiver le bouton
    const origContent = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<svg class="animate-spin" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 11-6.219-8.56"/></svg>';

    try {
      const formData = new FormData();
      formData.append('_csrf_token', CSRF_TOKEN);

      // Données supplémentaires depuis data-* attributes
      Object.entries(btn.dataset).forEach(([key, val]) => {
        if (!['action','url','confirm'].includes(key)) formData.append(key, val);
      });

      const res = await fetch(url, { method: 'POST', body: formData });
      const data = await res.json();

      if (data.success) {
        Toast.success(data.message || 'Action réussie.');
        // Actualisation de la page si demandé
        if (btn.dataset.refresh !== 'false') {
          setTimeout(() => location.reload(), 800);
        }
      } else {
        Toast.error(data.message || 'Une erreur est survenue.');
      }
    } catch {
      Toast.error('Erreur réseau. Veuillez réessayer.');
    } finally {
      btn.disabled = false;
      btn.innerHTML = origContent;
    }
  });
}

/* =====================================================
   ÉDITEUR DE CONTENU SIMPLE (textarea enrichi)
   ===================================================== */
function initSimpleEditor() {
  const editors = document.querySelectorAll('[data-editor]');
  editors.forEach(textarea => {
    const toolbar = document.createElement('div');
    toolbar.className = 'editor-toolbar';
    toolbar.style.cssText = 'display:flex;gap:4px;padding:8px;background:#f9fafb;border:1px solid #e5e7eb;border-bottom:none;border-radius:8px 8px 0 0;flex-wrap:wrap';

    const buttons = [
      { label: '<b>B</b>',       tag: 'strong' },
      { label: '<i>I</i>',       tag: 'em' },
      { label: '<u>U</u>',       tag: 'u' },
      { label: 'H2',             tag: 'h2' },
      { label: 'H3',             tag: 'h3' },
      { label: '≡ Liste',        tag: 'li', wrapper: 'ul' },
      { label: '🔗 Lien',        tag: 'a', href: true },
    ];

    buttons.forEach(({ label, tag, href }) => {
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.innerHTML = label;
      btn.style.cssText = 'padding:4px 10px;border:1px solid #e5e7eb;border-radius:4px;background:white;font-size:12px;cursor:pointer';
      btn.addEventListener('click', () => {
        const start = textarea.selectionStart;
        const end   = textarea.selectionEnd;
        const sel   = textarea.value.substring(start, end);
        let replacement = '';
        if (href) {
          const url = prompt('URL du lien :', 'https://');
          if (!url) return;
          replacement = `<a href="${url}">${sel || 'Texte du lien'}</a>`;
        } else {
          replacement = `<${tag}>${sel}</${tag}>`;
        }
        textarea.value = textarea.value.substring(0, start) + replacement + textarea.value.substring(end);
        textarea.focus();
      });
      toolbar.appendChild(btn);
    });

    textarea.parentNode.insertBefore(toolbar, textarea);
    textarea.style.borderRadius = '0 0 8px 8px';
  });
}

/* =====================================================
   COMPTEUR DE CARACTÈRES
   ===================================================== */
function initCharCount() {
  document.querySelectorAll('[data-maxlength]').forEach(field => {
    const max     = parseInt(field.dataset.maxlength);
    const counter = document.createElement('div');
    counter.style.cssText = 'font-size:11px;color:#9ca3af;text-align:right;margin-top:4px';
    field.parentNode.appendChild(counter);

    function update() {
      const left = max - field.value.length;
      counter.textContent = `${field.value.length} / ${max} caractères`;
      counter.style.color = left < 20 ? '#ef4444' : left < 50 ? '#f59e0b' : '#9ca3af';
    }
    field.addEventListener('input', update);
    update();
  });
}

/* =====================================================
   GRAPHIQUES SIMPLES (CSS bars)
   ===================================================== */
function initCSSCharts() {
  document.querySelectorAll('.chart-bars').forEach(chart => {
    const bars = chart.querySelectorAll('.chart-bar');
    const values = [...bars].map(b => parseFloat(b.dataset.value || 0));
    const max    = Math.max(...values, 1);

    bars.forEach((bar, i) => {
      const pct = (values[i] / max) * 100;
      bar.style.height = '0%';
      bar.style.transition = `height .6s ease ${i * 0.1}s`;
      // Animer à l'affichage
      requestAnimationFrame(() => {
        setTimeout(() => bar.style.height = pct + '%', 50);
      });
    });
  });
}

/* =====================================================
   RECHERCHE GLOBALE ADMIN
   ===================================================== */
function initGlobalSearch() {
  const input = document.getElementById('adminGlobalSearch');
  if (!input) return;

  let timer;
  input.addEventListener('input', () => {
    clearTimeout(timer);
    const q = input.value.trim();
    if (q.length < 2) return;

    timer = setTimeout(() => {
      // Rediriger vers une page de recherche
      window.location.href = BASE_URL + '/admin/utilisateurs?q=' + encodeURIComponent(q);
    }, 600);
  });
}

/* =====================================================
   AUTO-REFRESH DES STATS (60 secondes)
   ===================================================== */
function initAutoRefresh() {
  if (!document.querySelector('[data-auto-refresh]')) return;

  setInterval(async () => {
    try {
      const res  = await fetch(BASE_URL + '/api/stats', { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
      const data = await res.json();
      if (!data.stats) return;

      Object.entries(data.stats).forEach(([key, val]) => {
        const el = document.querySelector(`[data-stat="${key}"]`);
        if (el) el.textContent = typeof val === 'number' ? val.toLocaleString('fr-FR') : val;
      });
    } catch {}
  }, 60000);
}

/* =====================================================
   INITIALISATION
   ===================================================== */
document.addEventListener('DOMContentLoaded', () => {
  initAdminSidebar();
  initSortableTables();
  initBulkSelect();
  initInlineFilter();
  initAjaxActions();
  initSimpleEditor();
  initCharCount();
  initCSSCharts();
  initGlobalSearch();
  initAutoRefresh();
});
