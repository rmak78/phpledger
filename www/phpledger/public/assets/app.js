'use strict';

// Shared workspace controls enhance ordinary links and native details menus.
(() => {
    const shell = document.querySelector('[data-shell]');
    if (!shell) return;
    document.documentElement.dataset.uiReady = 'true';
    const sidebar = shell.querySelector('[data-sidebar]');
    const toggle = shell.querySelector('[data-sidebar-toggle]');
    const drawerMedia = matchMedia('(max-width: 899px)');
    const railMedia = matchMedia('(max-width: 1180px)');
    let returnFocus = null;
    const sync = () => {
        const open = shell.dataset.drawerOpen === 'true';
        sidebar.inert = drawerMedia.matches && !open;
        toggle.setAttribute('aria-expanded', String(drawerMedia.matches ? open : shell.dataset.collapsed === 'false' || (!railMedia.matches && shell.dataset.collapsed !== 'true')));
    };
    const closeDrawer = () => {
        shell.dataset.drawerOpen = 'false';
        sync();
        returnFocus?.focus();
        returnFocus = null;
    };
    toggle.hidden = false;
    toggle.addEventListener('click', () => {
        if (drawerMedia.matches) {
            if (shell.dataset.drawerOpen === 'true') { closeDrawer(); return; }
            returnFocus = document.activeElement;
            shell.dataset.drawerOpen = 'true';
            sync();
            sidebar.querySelector('a, summary, button')?.focus();
        } else {
            const expanded = toggle.getAttribute('aria-expanded') === 'true';
            shell.dataset.collapsed = String(expanded);
            sync();
        }
    });
    shell.querySelector('[data-drawer-overlay]').addEventListener('click', closeDrawer);
    drawerMedia.addEventListener('change', () => { shell.dataset.drawerOpen = 'false'; sync(); });
    railMedia.addEventListener('change', sync);
    sync();
    const active = sidebar.querySelector('[aria-current="page"]');
    if (active) {
        const group = active.closest('details');
        if (group) group.open = true;
        const nav = shell.querySelector('.shell-nav');
        const bounds = active.getBoundingClientRect();
        const container = nav.getBoundingClientRect();
        if (bounds.bottom > container.bottom) nav.scrollTop += bounds.bottom - container.bottom;
        else if (bounds.top < container.top) nav.scrollTop -= container.top - bounds.top;
    }

    const palette = document.createElement('dialog');
    palette.className = 'dialog command-palette';
    palette.setAttribute('aria-label', 'Search or jump to');
    const header = document.createElement('div');
    header.className = 'dialog-header';
    const input = document.createElement('input');
    input.type = 'search';
    input.className = 'input';
    input.placeholder = 'Find a page…';
    input.setAttribute('aria-label', 'Find a page');
    const close = document.createElement('button');
    close.type = 'button';
    close.className = 'btn btn-ghost';
    close.textContent = 'Close';
    close.addEventListener('click', () => palette.close());
    header.append(input, close);
    const results = document.createElement('nav');
    results.className = 'dialog-body command-palette-list';
    results.setAttribute('aria-label', 'Matching pages');
    const entries = [...shell.querySelectorAll('.shell-nav a, .shell-sidebar-footer a')].map(link => ({text: link.textContent.trim(), href: link.href}));
    const render = () => {
        results.replaceChildren();
        const query = input.value.trim().toLowerCase();
        entries.filter(entry => entry.text.toLowerCase().includes(query)).forEach(entry => {
            const link = document.createElement('a');
            link.className = 'menu-item';
            link.href = entry.href;
            link.textContent = entry.text;
            results.append(link);
        });
        if (!results.childElementCount) results.textContent = 'No matching pages.';
    };
    input.addEventListener('input', render);
    input.addEventListener('keydown', event => {
        if (event.key === 'ArrowDown') { event.preventDefault(); results.querySelector('a')?.focus(); }
        if (event.key === 'Enter') { event.preventDefault(); results.querySelector('a')?.click(); }
    });
    palette.append(header, results);
    document.body.append(palette);
    const openPalette = () => { input.value = ''; render(); palette.showModal(); input.focus(); };
    shell.querySelectorAll('[data-command-open]').forEach(button => { button.hidden = false; button.addEventListener('click', openPalette); });
    document.addEventListener('keydown', event => {
        if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 'k') { event.preventDefault(); if (!palette.open) openPalette(); }
        if (event.key === 'Escape') {
            if (palette.open) { event.preventDefault(); palette.close(); return; }
            if (shell.dataset.drawerOpen === 'true') closeDrawer();
            shell.querySelectorAll('details.menu[open], details.company-switcher[open]').forEach(menu => { menu.open = false; menu.querySelector('summary').focus(); });
        }
        if (event.key === 'Tab' && drawerMedia.matches && shell.dataset.drawerOpen === 'true' && !palette.open) {
            const controls = [...sidebar.querySelectorAll('a, button, summary')].filter(el => el.getClientRects().length);
            const first = controls[0], last = controls.at(-1);
            if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last?.focus(); }
            else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first?.focus(); }
        }
    });
    document.addEventListener('click', event => {
        shell.querySelectorAll('details.menu[open], details.company-switcher[open]').forEach(menu => { if (!menu.contains(event.target)) menu.open = false; });
    });
})();

document.querySelectorAll('[data-demo-expiry]').forEach((element) => {
    const expires = Date.parse(element.dataset.demoExpiry);
    if (!Number.isFinite(expires)) return;
    const update = () => {
        const minutes = Math.max(0, Math.ceil((expires - Date.now()) / 60000));
        element.textContent = minutes > 0 ? `${minutes} minute${minutes === 1 ? '' : 's'} remaining` : 'Sample expired. Start a new sample and reconnect your clients.';
    };
    update();
    const timer = setInterval(() => { update(); if (Date.now() >= expires) clearInterval(timer); }, 15000);
});

// Server-rendered routes and forms remain usable without JavaScript.
// After a rejected submission, move keyboard focus to the preserved error summary.
document.querySelector('[data-form-error]')?.focus();

document.querySelectorAll('[data-dismiss]').forEach(button => {
        button.addEventListener('click', () => button.closest('[data-dismissible], .notice')?.remove());
});

const terminalZone = Intl.DateTimeFormat().resolvedOptions().timeZone || 'UTC';
document.querySelectorAll('[data-timezone]').forEach(node => { node.textContent = terminalZone; });
document.querySelectorAll('time[data-local-time]').forEach(node => {
    const instant = new Date(node.dateTime);
    if (!Number.isNaN(instant.getTime())) {
        node.title = node.textContent;
        node.textContent = new Intl.DateTimeFormat('en', {
            dateStyle: 'medium', timeStyle: 'short', timeZone: terminalZone
        }).format(instant) + ' · ' + terminalZone;
    }
});

// Only new, untouched date defaults use the terminal day. Stored accounting dates never shift.
const today = new Date();
const localDay = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;
document.querySelectorAll('[data-local-today]').forEach(input => { input.value = localDay; });

// Keep the common year-end choices compact while retaining a no-JavaScript fallback.
// The server remains authoritative for the selected period and MM-DD validation.
document.querySelectorAll('[data-fiscal-year-end-choice]').forEach(select => {
    const targetSelector = select.dataset.fiscalCustomTarget;
    const customInput = targetSelector ? document.querySelector(targetSelector) : null;
    const customGroup = customInput?.closest('[data-fiscal-custom-group]');
    if (!customInput || !customGroup) return;
    const sync = () => {
        const isCustom = select.value === 'custom';
        customGroup.hidden = !isCustom;
        customInput.disabled = !isCustom;
        customInput.setAttribute('aria-hidden', isCustom ? 'false' : 'true');
    };
    select.addEventListener('change', sync);
    sync();
});

document.querySelectorAll('[data-record-link]').forEach(link => {
    if (window.matchMedia('(min-width: 981px)').matches) {
        const url = new URL(link.href);
        url.pathname = url.pathname.replace(/\/transactions\/detail$/, '/transactions');
        link.href = url.toString();
    }
});
document.querySelectorAll('[data-open-details]').forEach(link => {
    link.addEventListener('click', () => {
        const details = document.getElementById(link.hash.slice(1));
        if (details instanceof HTMLDetailsElement) details.open = true;
    });
});

const documentForm = document.querySelector('[data-document-form]');
if (documentForm) {
    const category = documentForm.elements.category_account_id;
    const filterCategories = () => {
        const kind = documentForm.elements.kind.value;
        [...category.options].forEach(option => {
            const mismatch = !!option.dataset.categoryKind && option.dataset.categoryKind !== kind;
            option.hidden = mismatch;
            option.disabled = mismatch;
        });
        if (category.selectedOptions[0]?.disabled) category.value = '';
    };
    documentForm.querySelectorAll('[name="kind"]').forEach(input => input.addEventListener('change', filterCategories));
    filterCategories();
    let changed = false;
    documentForm.addEventListener('input', () => { changed = true; });
    documentForm.addEventListener('submit', () => { changed = false; });
    window.addEventListener('beforeunload', event => {
        if (changed) { event.preventDefault(); event.returnValue = ''; }
    });
}

// The plotted points are presentation only. Exact scenario amounts are calculated on the server.
document.querySelectorAll('[data-forecast-chart]').forEach(canvas => {
    const values = JSON.parse(canvas.dataset.values).map(Number);
    const context = canvas.getContext('2d');
    if (!context || !values.length || values.some(value => !Number.isFinite(value))) return;
    const width = canvas.width;
    const height = canvas.height;
    const pad = {left:70, right:22, top:30, bottom:38};
    const low = Math.min(0, ...values);
    const high = Math.max(1, ...values);
    const range = high - low || 1;
    const x = index => pad.left + index * (width-pad.left-pad.right) / Math.max(1, values.length-1);
    const y = value => height-pad.bottom - (value-low) / range * (height-pad.top-pad.bottom);
    context.font = '12px Inter, sans-serif';
    context.textBaseline = 'middle';
    for (let i=0; i<=4; i++) {
        const value = low + range * i / 4;
        context.strokeStyle = '#e1e6ee';
        context.beginPath(); context.moveTo(pad.left, y(value)); context.lineTo(width-pad.right,y(value)); context.stroke();
        context.fillStyle = '#65718a'; context.textAlign='right';
        context.fillText(new Intl.NumberFormat('en',{notation:'compact',maximumFractionDigits:1}).format(value),pad.left-12,y(value));
    }
    context.beginPath();
    context.moveTo(x(0),y(values[0])); values.forEach((value,index)=>context.lineTo(x(index),y(value)));
    context.lineTo(x(values.length-1),height-pad.bottom);context.lineTo(x(0),height-pad.bottom);context.closePath();
    context.fillStyle='#eef1ff';context.fill();
    context.beginPath(); values.forEach((value,index)=>index?context.lineTo(x(index),y(value)):context.moveTo(x(index),y(value)));
    context.strokeStyle='#304dea';context.lineWidth=3;context.lineJoin='round';context.stroke();
    context.fillStyle='#65718a';context.textAlign='center';
    [0,Math.floor((values.length-1)/2),values.length-1].forEach(index=>context.fillText(index===0?'Today':`Week ${index}`,x(index),height-13));
});
