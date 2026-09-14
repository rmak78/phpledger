'use strict';
(() => {
    const root = document.querySelector('[data-pos-root]');
    if (!root) return;
    root.querySelector('[data-pos-print]')?.addEventListener('click', () => window.print());
    const form = root.querySelector('[data-pos-form]');
    if (!form) return;
    const products = [...form.querySelectorAll('[data-pos-product]')];
    const cart = form.querySelector('[data-pos-cart]');
    const cash = form.querySelector('[data-pos-cash]');
    const checkout = form.querySelector('[data-pos-checkout]');
    const initiallyDisabled = checkout.disabled;
    let submitting = false;
    const money = (text) => /^(?:0|[1-9][0-9]{0,15})(?:\.[0-9]{1,4})?$/.test(text)
        ? BigInt(text.split('.')[0]) * 10000n + BigInt((text.split('.')[1] || '').padEnd(4, '0')) : null;
    const formatted = (value) => {
        const whole = (value / 10000n).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        const fraction = (value % 10000n).toString().padStart(4, '0');
        return whole + '.' + (fraction.endsWith('00') ? fraction.slice(0, 2) : fraction);
    };
    let valid = false;
    const update = () => {
        cart.replaceChildren();
        let total = 0n;
        let units = 0;
        let quantitiesValid = true;
        products.forEach((product) => {
            const input = product.querySelector('[data-pos-quantity]');
            if (!/^(?:0|[1-9][0-9]?)$/.test(input.value)) { quantitiesValid = false; return; }
            const quantity = Number(input.value);
            if (quantity === 0) return;
            units += quantity;
            const lineTotal = money(product.dataset.price) * BigInt(quantity);
            total += lineTotal;
            const row = document.createElement('div'); row.className = 'pos-cart-row';
            const info = document.createElement('div');
            const name = document.createElement('strong'); name.textContent = product.dataset.name;
            const detail = document.createElement('small'); detail.textContent = quantity + ' × ' + formatted(money(product.dataset.price));
            const remove = document.createElement('button'); remove.type = 'button'; remove.className = 'pos-cart-remove'; remove.textContent = 'Remove'; remove.setAttribute('aria-label', 'Remove ' + product.dataset.name);
            remove.addEventListener('click', () => { input.value = '0'; update(); input.focus(); });
            info.append(name, detail, remove);
            const amount = document.createElement('strong'); amount.className = 'amount'; amount.textContent = formatted(lineTotal);
            row.append(info, amount); cart.append(row);
        });
        if (!units) { const empty = document.createElement('p'); empty.className = 'muted'; empty.textContent = 'Your cart is empty. Add a product to begin.'; cart.append(empty); }
        form.querySelector('[data-pos-total]').textContent = formatted(total);
        const received = money(cash.value);
        const enough = received !== null && received >= total;
        form.querySelector('[data-pos-change]').textContent = received === null ? 'Enter cash received to see change.' : enough ? 'Change: ' + root.dataset.currency + ' ' + formatted(received - total) : 'Amount still due: ' + root.dataset.currency + ' ' + formatted(total - received);
        form.querySelector('[data-pos-feedback]').textContent = !quantitiesValid ? 'Use whole quantities from 0 to 99.' : units > 200 ? 'Use no more than 200 units in one sale.' : units + (units === 1 ? ' unit' : ' units') + ' · Prices checked at checkout';
        valid = quantitiesValid && units > 0 && units <= 200 && enough;
        checkout.disabled = initiallyDisabled || !valid;
    };
    products.forEach((product) => {
        const quantity = product.querySelector('[data-pos-quantity]');
        quantity.addEventListener('input', update);
        product.querySelector('[data-pos-add]').addEventListener('click', () => { const current = /^(?:0|[1-9][0-9]?)$/.test(quantity.value) ? Number(quantity.value) : 0; quantity.value = String(Math.min(99, current + 1)); update(); });
    });
    cash.addEventListener('input', update);
    let category = 'all';
    const search = form.querySelector('[data-pos-search]');
    const filter = () => {
        let count = 0;
        const term = search.value.trim().toLocaleLowerCase();
        products.forEach((product) => { const visible = (category === 'all' || product.dataset.category === category) && (product.dataset.name + ' ' + product.dataset.sku).toLocaleLowerCase().includes(term); product.hidden = !visible; if (visible) count++; });
        form.querySelector('[data-pos-no-results]').hidden = count > 0;
    };
    search.addEventListener('input', filter);
    form.querySelectorAll('[data-pos-category]').forEach((button) => button.addEventListener('click', () => { category = button.dataset.posCategory; form.querySelectorAll('[data-pos-category]').forEach((other) => other.setAttribute('aria-pressed', String(other === button))); filter(); }));
    // Enter edits/searches; only deliberate activation of checkout records a sale.
    form.addEventListener('keydown', (event) => {
        if (event.key === 'Enter' && event.target instanceof HTMLInputElement) event.preventDefault();
    });
    form.addEventListener('submit', (event) => {
        if (event.submitter !== checkout || submitting) { event.preventDefault(); return; }
        update();
        if (!valid || initiallyDisabled) { event.preventDefault(); return; }
        submitting = true;
        // Keep the named submitter successful so the server receives its explicit intent.
        checkout.setAttribute('aria-disabled', 'true');
        checkout.textContent = 'Recording sale…';
    });
    update();
})();
