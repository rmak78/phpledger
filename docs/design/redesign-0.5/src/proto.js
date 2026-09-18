// Prototype runtime. Mirrors the production constraint: no inline handlers or inline styles,
// behaviour is attached through data-* attributes so the same patterns port to CSP-safe vanilla JS.
(() => {
  const app = document.getElementById('app');
  const manifest = JSON.parse(document.getElementById('screen-manifest').textContent);
  const store = {
    get(k) { try { return localStorage.getItem('pl-proto:' + k); } catch { return null; } },
    set(k, v) { try { localStorage.setItem('pl-proto:' + k, v); } catch { /* storage unavailable */ } },
  };

  function currentId() {
    return (location.hash.replace(/^#\/?/, '').split('?')[0]) || 'index';
  }

  function render() {
    const id = currentId();
    let tpl = document.getElementById('screen-' + id);
    if (!tpl) {
      app.innerHTML = `<div data-screen-missing class="p-8">Screen “${id}” is not in the prototype yet. <a class="link" href="#/index">Screen index</a></div>`;
      return;
    }
    const layout = tpl.dataset.layout || 'app';
    const layoutTpl = document.getElementById('layout-' + layout);
    let frag;
    if (!layoutTpl || layout === 'bare') {
      frag = tpl.content.cloneNode(true);
    } else {
      frag = layoutTpl.content.cloneNode(true);
      const slot = frag.querySelector('[data-slot="content"]');
      if (layout === 'app') {
        const inner = document.createElement('div');
        inner.className = 'shell-main-inner';
        inner.append(tpl.content.cloneNode(true));
        slot.append(inner);
      } else {
        slot.append(tpl.content.cloneNode(true));
      }
    }
    // Context strips: a screen marks its strip element with data-shell-strip (anywhere in its
    // own markup) and it moves into the shell's [data-slot="strips"] region above the content,
    // rather than rendering inline where the screen fragment happens to sit in the DOM.
    const stripsSlot = frag.querySelector('[data-slot="strips"]');
    if (stripsSlot) {
      frag.querySelectorAll('[data-shell-strip]').forEach((el) => stripsSlot.append(el));
    }
    // Active navigation
    const nav = tpl.dataset.nav;
    frag.querySelectorAll('[data-nav]').forEach((a) => {
      if (nav && a.dataset.nav === nav) a.setAttribute('aria-current', 'page'); else a.removeAttribute('aria-current');
    });
    // A collapsible nav group (currently just "Setup") auto-expands when one of its own items is
    // the active page, so landing on e.g. Tax codes doesn't leave the sidebar showing it collapsed
    // and scrolled past — see the sidebar scroll-into-view below, which depends on this having
    // already run so the active item is actually laid out (not hidden inside a closed <details>).
    frag.querySelectorAll('.nav-group-collapsible').forEach((group) => {
      if (group.querySelector('[data-nav][aria-current="page"]')) group.setAttribute('open', '');
    });
    // Breadcrumbs: JSON array of [label, href?]
    const crumbSlot = frag.querySelector('[data-slot="crumbs"]');
    if (crumbSlot) {
      const crumbs = JSON.parse(tpl.dataset.crumbs || '[]');
      crumbSlot.replaceChildren(...crumbs.map(([label, href], i) => {
        const li = document.createElement('li');
        li.className = 'crumb';
        const el = document.createElement(href && i < crumbs.length - 1 ? 'a' : 'span');
        if (el.tagName === 'A') el.href = href; else if (i === crumbs.length - 1) el.setAttribute('aria-current', 'page');
        el.textContent = label;
        li.append(el);
        return li;
      }));
    }
    app.replaceChildren(frag);
    document.title = (tpl.dataset.title || 'PHP Ledger') + ' · PHP Ledger Redesign';
    document.documentElement.dataset.screen = id;
    window.scrollTo(0, 0);
    // Scroll the active sidebar item into view inside its own scroll container (.shell-nav), not
    // the page — without this a nav item further down the list (Bank reconciliation, any Reports
    // or Setup entry) can be active but scrolled out of sight at shorter viewports (e.g. 768px
    // tall), with nothing on screen indicating where "you are" in the sidebar.
    const activeNav = app.querySelector('.shell-nav [data-nav][aria-current="page"]');
    activeNav?.scrollIntoView({ block: 'nearest' });
    enhance(app);
    window.dispatchEvent(new CustomEvent('screen:rendered', { detail: { id, manifest } }));
  }

  // Shared behaviours applied after every render (extend here, keep them attribute-driven).
  function enhance(scope) {
    const shell = scope.querySelector('[data-shell]');
    if (shell) {
      const pref = store.get('sidebar');
      if (pref === 'collapsed') shell.dataset.collapsed = 'true';
      else if (pref === 'expanded') shell.dataset.collapsed = 'false';
    }
    renderScreenIndex(scope);
  }

  // Populates [data-screen-index] (the "Screen index" page) straight from the manifest, so it
  // stays current as other builders add screens/*.html — no per-screen list to hand-maintain.
  function renderScreenIndex(scope) {
    const mount = scope.querySelector('[data-screen-index]');
    if (!mount) return;
    const groups = new Map();
    manifest.forEach((m) => {
      const g = m.group || 'Other';
      if (!groups.has(g)) groups.set(g, []);
      groups.get(g).push(m);
    });
    mount.replaceChildren();
    const order = ['Home', 'Daily work', 'Sales', 'Purchases', 'Inventory', 'Banking', 'Reports', 'Setup', 'Setup & access', 'Prototype'];
    const rank = (g) => (order.indexOf(g) + 1) || order.length + 1;
    [...groups.entries()].sort((a, b) => rank(a[0]) - rank(b[0])).forEach(([group, screens]) => {
      const section = document.createElement('section');
      const h2 = document.createElement('h2');
      h2.className = 'section-title mb-3';
      h2.textContent = group;
      section.append(h2);
      const list = document.createElement('div');
      list.className = 'grid gap-3 sm:grid-cols-2 lg:grid-cols-3';
      screens.forEach((s) => {
        const a = document.createElement('a');
        a.href = '#/' + s.id;
        a.className = 'panel flex flex-col gap-1 no-underline transition-colors hover:border-border-strong hover:bg-surface-subtle';
        const title = document.createElement('span');
        title.className = 'text-sm font-semibold text-ink';
        title.textContent = s.title || s.id;
        const meta = document.createElement('span');
        meta.className = 'text-xs text-ink-muted';
        meta.textContent = [s.route, s.pattern].filter(Boolean).join(' · ');
        a.append(title, meta);
        if (s.state) {
          const state = document.createElement('span');
          state.className = 'text-xs text-ink-faint';
          state.textContent = s.state;
          a.append(state);
        }
        list.append(a);
      });
      section.append(list);
      mount.append(section);
    });
  }

  function isTypingTarget(el) {
    return !!(el && el.closest && el.closest('input, textarea, select, [contenteditable="true"]'));
  }

  function renumberRows(container) {
    container.querySelectorAll('[data-row-index]').forEach((el, i) => { el.textContent = String(i + 1); });
  }

  // ---- Command palette (Ctrl+K / "/") --------------------------------------------------
  let paletteApi = null;
  function buildPalette() {
    const dialog = document.createElement('dialog');
    dialog.className = 'dialog command-palette';
    dialog.setAttribute('aria-label', 'Search or jump to');

    const header = document.createElement('div');
    header.className = 'dialog-header';
    const searchField = document.createElement('div');
    searchField.className = 'search-field command-palette-search';
    const input = document.createElement('input');
    input.type = 'search';
    input.placeholder = 'Search screens by name, route or group…';
    input.setAttribute('aria-label', 'Search screens');
    searchField.append(input);
    header.append(searchField);

    const body = document.createElement('div');
    body.className = 'dialog-body command-palette-list';

    const footer = document.createElement('div');
    footer.className = 'dialog-footer command-palette-footer';
    const hintEnter = document.createElement('span');
    hintEnter.className = 'kbd-group';
    hintEnter.innerHTML = '<span class="kbd">Enter</span>';
    const hintLabel1 = document.createElement('span');
    hintLabel1.className = 'command-palette-hint';
    hintLabel1.textContent = 'open';
    const hintEsc = document.createElement('span');
    hintEsc.className = 'kbd-group';
    hintEsc.innerHTML = '<span class="kbd">Esc</span>';
    const hintLabel2 = document.createElement('span');
    hintLabel2.className = 'command-palette-hint';
    hintLabel2.textContent = 'close';
    footer.append(hintEnter, hintLabel1, hintEsc, hintLabel2);

    dialog.append(header, body, footer);
    document.body.append(dialog);

    let items = [];
    let activeIndex = 0;

    function setActive(i) {
      if (!items.length) return;
      activeIndex = Math.max(0, Math.min(items.length - 1, i));
      body.querySelectorAll('.command-palette-item').forEach((el, idx) => {
        el.classList.toggle('is-active', idx === activeIndex);
      });
      body.querySelector('.is-active')?.scrollIntoView({ block: 'nearest' });
    }

    function jump(i) {
      const item = items[i];
      if (item) { dialog.close(); location.hash = '#/' + item.id; }
    }

    function renderList(filter) {
      const q = filter.trim().toLowerCase();
      items = manifest.filter((m) => !q
        || m.title?.toLowerCase().includes(q)
        || m.id.toLowerCase().includes(q)
        || m.group?.toLowerCase().includes(q)
        || m.route?.toLowerCase().includes(q));
      body.replaceChildren();
      if (!items.length) {
        const empty = document.createElement('p');
        empty.className = 'command-palette-empty';
        empty.textContent = `No screens match "${filter}".`;
        body.append(empty);
        return;
      }
      let lastGroup;
      items.forEach((m, i) => {
        if (m.group !== lastGroup) {
          const label = document.createElement('p');
          label.className = 'menu-label';
          label.textContent = m.group || 'Other';
          body.append(label);
          lastGroup = m.group;
        }
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'menu-item command-palette-item';
        const title = document.createElement('span');
        title.textContent = m.title || m.id;
        const route = document.createElement('span');
        route.className = 'command-palette-route';
        route.textContent = m.route || '';
        btn.append(title, route);
        btn.addEventListener('click', () => jump(i));
        btn.addEventListener('mouseenter', () => setActive(i));
        body.append(btn);
      });
      setActive(0);
    }

    input.addEventListener('input', () => renderList(input.value));
    input.addEventListener('keydown', (e) => {
      if (e.key === 'ArrowDown') { e.preventDefault(); setActive(activeIndex + 1); }
      else if (e.key === 'ArrowUp') { e.preventDefault(); setActive(activeIndex - 1); }
      else if (e.key === 'Enter') { e.preventDefault(); jump(activeIndex); }
    });
    dialog.addEventListener('click', (e) => { if (e.target === dialog) dialog.close(); });
    dialog.addEventListener('close', () => { input.value = ''; });

    paletteApi = {
      open() {
        renderList('');
        if (typeof dialog.showModal === 'function') dialog.showModal();
        requestAnimationFrame(() => input.focus());
      },
    };
  }

  // ---- Global click delegation ------------------------------------------------------------
  document.addEventListener('click', (e) => {
    // Disclosure toggle: data-toggle="<id>" flips [hidden] on the target.
    const toggle = e.target.closest('[data-toggle]');
    if (toggle) {
      const target = document.getElementById(toggle.dataset.toggle);
      if (target) {
        target.hidden = !target.hidden;
        toggle.setAttribute('aria-expanded', String(!target.hidden));
      }
    }

    // Dismissible context strips / alerts.
    const dismiss = e.target.closest('[data-dismiss]');
    if (dismiss) dismiss.closest('[data-dismissible]')?.setAttribute('hidden', '');

    // Sidebar: collapse-to-rail (>=900px) or open/close the overlay drawer (<900px).
    const sidebarToggle = e.target.closest('[data-sidebar-toggle]');
    if (sidebarToggle) {
      const shell = document.querySelector('[data-shell]');
      if (shell) {
        if (window.innerWidth < 900) {
          shell.dataset.drawerOpen = shell.dataset.drawerOpen === 'true' ? 'false' : 'true';
        } else {
          const next = shell.dataset.collapsed === 'true' ? 'false' : 'true';
          shell.dataset.collapsed = next;
          store.set('sidebar', next === 'true' ? 'collapsed' : 'expanded');
        }
      }
    }
    const drawerOverlay = e.target.closest('[data-drawer-overlay]');
    if (drawerOverlay) document.querySelector('[data-shell]')?.setAttribute('data-drawer-open', 'false');

    // Command palette open (topbar search trigger or any [data-command-open]).
    if (e.target.closest('[data-command-open]')) { paletteApi?.open(); }

    // Generic native <dialog> opener: data-dialog-open="<dialogId>". Closing is native
    // (a <form method="dialog"> button, or Escape/backdrop click) — no JS needed for that.
    const dialogOpen = e.target.closest('[data-dialog-open]');
    if (dialogOpen) {
      const dialog = document.getElementById(dialogOpen.dataset.dialogOpen);
      if (dialog && typeof dialog.showModal === 'function') dialog.showModal();
    }

    // Tabs: data-tabs (tablist) / data-tab (trigger) / data-panel (sibling panel, matched by value).
    const tabBtn = e.target.closest('[data-tab]');
    if (tabBtn) {
      const tabsEl = tabBtn.closest('[data-tabs]');
      if (tabsEl) {
        tabsEl.querySelectorAll('[data-tab]').forEach((b) => b.setAttribute('aria-selected', String(b === tabBtn)));
        const key = tabBtn.dataset.tab;
        const scope = tabsEl.parentElement;
        scope?.querySelectorAll(':scope > [data-panel]').forEach((p) => { p.hidden = p.dataset.panel !== key; });
      }
    }

    // Wizard stepper: data-wizard wraps .stepper-step items + [data-step-panel] sections.
    const stepBtn = e.target.closest('[data-step-next], [data-step-back]');
    if (stepBtn) {
      const wizard = stepBtn.closest('[data-wizard]');
      if (wizard) {
        const steps = [...wizard.querySelectorAll('.stepper-step')];
        const panels = [...wizard.querySelectorAll('[data-step-panel]')];
        let idx = steps.findIndex((s) => s.classList.contains('is-current'));
        if (idx === -1) idx = 0;
        const dir = stepBtn.hasAttribute('data-step-next') ? 1 : -1;
        const next = Math.min(steps.length - 1, Math.max(0, idx + dir));
        steps.forEach((s, i) => {
          s.classList.toggle('is-current', i === next);
          s.classList.toggle('is-done', i < next);
        });
        panels.forEach((p) => { p.hidden = Number(p.dataset.stepPanel) !== next; });
      }
    }

    // Line items: data-add-row="<containerId>" data-row-template="<templateId>" clones a row;
    // data-remove-row removes its own row (never the last one).
    const addBtn = e.target.closest('[data-add-row]');
    if (addBtn) {
      const target = document.getElementById(addBtn.dataset.addRow);
      const rowTpl = document.getElementById(addBtn.dataset.rowTemplate);
      if (target && rowTpl) {
        target.append(rowTpl.content.cloneNode(true));
        renumberRows(target);
        target.lastElementChild?.querySelector('input, select, textarea')?.focus();
      }
    }
    const removeBtn = e.target.closest('[data-remove-row]');
    if (removeBtn) {
      const row = removeBtn.closest('tr, [data-row]');
      const container = row?.parentElement;
      if (row && container) {
        const siblingCount = container.querySelectorAll(':scope > tr, :scope > [data-row]').length;
        if (siblingCount > 1) { row.remove(); renumberRows(container); }
      }
    }

    // Split-view: clicking a selectable row marks it selected before navigating.
    const row = e.target.closest('tr[data-href]');
    if (row && !e.target.closest('a, button, input, select, label, textarea')) {
      row.closest('tbody')?.querySelectorAll('tr[aria-selected="true"]').forEach((r) => r.removeAttribute('aria-selected'));
      row.setAttribute('aria-selected', 'true');
      location.hash = row.dataset.href;
    }

    // Close any open <details class="menu"> when the click lands outside it.
    document.querySelectorAll('details.menu[open]').forEach((d) => {
      if (!d.contains(e.target)) d.removeAttribute('open');
    });
  });

  document.addEventListener('keydown', (e) => {
    const mod = e.ctrlKey || e.metaKey;
    if (mod && e.key.toLowerCase() === 'k') { e.preventDefault(); paletteApi?.open(); return; }
    if (e.key === '/' && !isTypingTarget(e.target) && !document.querySelector('dialog[open]')) {
      e.preventDefault();
      paletteApi?.open();
      return;
    }
    if (e.key === 'Escape') {
      document.querySelectorAll('details.menu[open]').forEach((d) => d.removeAttribute('open'));
      const shell = document.querySelector('[data-shell]');
      if (shell?.dataset.drawerOpen === 'true') shell.dataset.drawerOpen = 'false';
    }
  });

  buildPalette();
  window.addEventListener('hashchange', render);
  render();
})();
