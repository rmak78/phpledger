'use strict';
(() => {
    const root = document.querySelector('[data-pos-root]');
    if (!root) return;
    root.querySelector('[data-pos-print]')?.addEventListener('click', () => window.print());
    const money = text => /^(?:0|[1-9][0-9]{0,15})(?:\.[0-9]{1,4})?$/.test(text)
        ? BigInt(text.split('.')[0]) * 10000n + BigInt((text.split('.')[1] || '').padEnd(4, '0')) : null;
    const formatted = value => {
        const whole = (value / 10000n).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        const fraction = (value % 10000n).toString().padStart(4, '0');
        return whole + '.' + (fraction.endsWith('00') ? fraction.slice(0, 2) : fraction);
    };
    // Inputs edit values; only a deliberately activated submit button continues.
    const protectInputs = form => form.addEventListener('keydown', event => {
        if (event.key === 'Enter' && event.target instanceof HTMLInputElement) event.preventDefault();
    });
    const payment = root.querySelector('[data-pos-payment]');
    if (payment) {
        const cash = payment.querySelector('[data-pos-cash]');
        const checkout = payment.querySelector('[data-pos-checkout]');
        const edit = payment.querySelector('[data-pos-edit]');
        const unavailable = checkout.disabled;
        const total = money(payment.dataset.total);
        let submitting = false;
        const update = () => {
            const received = money(cash.value);
            const enough = received !== null && received >= total;
            payment.querySelector('[data-pos-change]').textContent = received === null ? 'Enter cash received to see change.' : enough ? 'Change: ' + root.dataset.currency + ' ' + formatted(received - total) : 'Amount still due: ' + root.dataset.currency + ' ' + formatted(total - received);
            checkout.disabled = unavailable || !enough;
        };
        cash.addEventListener('input', update);
        payment.querySelector('[data-pos-exact]').addEventListener('click', () => {
            cash.value = payment.dataset.total; update(); cash.focus();
        });
        protectInputs(payment);
        payment.addEventListener('submit', event => {
            if (submitting || (event.submitter !== checkout && event.submitter !== edit)) { event.preventDefault(); return; }
            if (event.submitter === edit) return;
            update();
            if (checkout.disabled) { event.preventDefault(); return; }
            submitting = true;
            checkout.setAttribute('aria-disabled', 'true');
            checkout.textContent = 'Recording sale…';
            edit.disabled = true;
        });
        window.addEventListener('pageshow', () => { submitting = false; edit.disabled = false; checkout.removeAttribute('aria-disabled'); checkout.textContent = 'Record cash sale'; update(); });
        update();
    }
    const form = root.querySelector('[data-pos-form]');
    if (!form) return;
    root.classList.add('pos-enhanced');
    const products = [...form.querySelectorAll('[data-pos-product]')];
    const cart = form.querySelector('[data-pos-cart]');
    const review = form.querySelector('[data-pos-review]');
    const unavailable = review.disabled;
    const feedback = form.querySelector('[data-pos-feedback]');
    let valid = false;
    const button = (label, text, action) => {
        const node = document.createElement('button'); node.type = 'button'; node.textContent = text;
        node.setAttribute('aria-label', label); node.addEventListener('click', action); node.disabled = unavailable; return node;
    };
    const focusCart = (sku, action) => cart.querySelector('[data-cart-sku="' + sku + '"] [data-cart-action="' + action + '"]')?.focus();
    const change = (product, delta, fromCart = false) => {
        const input = product.querySelector('[data-pos-quantity]');
        const current = /^(?:0|[1-9][0-9]?)$/.test(input.value) ? Number(input.value) : 0;
        if (current + delta > 99) { feedback.textContent = 'Maximum 99 of ' + product.dataset.name + ' per sale.'; return; }
        input.value = String(Math.max(0, current + delta));
        update();
        feedback.textContent = product.dataset.name + ': ' + input.value + ' in cart.';
        if (fromCart) {
            if (Number(input.value) > 0) focusCart(product.dataset.sku, delta > 0 ? 'increase' : 'decrease');
            else product.querySelector('[data-pos-add]').focus();
        }
    };
    const update = (renderCart = true) => {
        if (renderCart) cart.replaceChildren();
        let total = 0n, units = 0, quantitiesValid = true;
        products.forEach(product => {
            const input = product.querySelector('[data-pos-quantity]');
            const acceptable = /^(?:0|[1-9][0-9]?)$/.test(input.value);
            const quantity = acceptable ? Number(input.value) : 0;
            if (!acceptable) quantitiesValid = false;
            product.classList.toggle('is-selected', quantity > 0);
            product.querySelector('[data-pos-selected]').textContent = quantity > 0 ? String(quantity) : '+';
            units += quantity;
            const lineTotal = money(product.dataset.price) * BigInt(quantity); total += lineTotal;
            if (!renderCart) {
                const amount = cart.querySelector('[data-cart-sku="' + product.dataset.sku + '"] > .amount');
                if (amount) amount.textContent = formatted(lineTotal);
                return;
            }
            if (quantity === 0 && acceptable) return;
            const row = document.createElement('div'); row.className = 'pos-cart-row'; row.dataset.cartSku = product.dataset.sku;
            const info = document.createElement('div');
            const name = document.createElement('strong'); name.textContent = product.dataset.name;
            const each = document.createElement('small'); each.textContent = formatted(money(product.dataset.price)) + ' each'; info.append(name, each);
            const amount = document.createElement('strong'); amount.className = 'amount'; amount.textContent = formatted(lineTotal);
            const controls = document.createElement('div'); controls.className = 'pos-quantity-controls';
            const minus = button('Decrease ' + product.dataset.name, '−', () => change(product, -1, true)); minus.dataset.cartAction = 'decrease';
            const plus = button('Increase ' + product.dataset.name, '+', () => change(product, 1, true)); plus.dataset.cartAction = 'increase';
            const editor = document.createElement('input'); editor.type = 'text'; editor.inputMode = 'numeric'; editor.maxLength = 2; editor.value = input.value; editor.disabled = unavailable;
            editor.setAttribute('aria-label', product.dataset.name + ' quantity'); editor.dataset.cartAction = 'quantity';
            // Update totals without replacing a control the user is typing in or about to click.
            editor.addEventListener('input', () => { input.value = editor.value; update(false); });
            editor.addEventListener('keydown', event => { if (event.key === 'Enter') { event.preventDefault(); update(false); } });
            controls.append(minus, editor, plus);
            const remove = button('Remove ' + product.dataset.name, 'Remove', () => { input.value = '0'; update(); product.querySelector('[data-pos-add]').focus(); }); remove.className = 'pos-cart-remove';
            row.append(info, amount, controls, remove); cart.append(row);
        });
        if (!cart.children.length) { const empty = document.createElement('p'); empty.className = 'muted pos-cart-empty'; empty.textContent = 'Your cart is empty. Tap a product to begin.'; cart.append(empty); }
        form.querySelector('[data-pos-total]').textContent = formatted(total);
        form.querySelector('[data-pos-count]').textContent = units + (units === 1 ? ' item' : ' items');
        root.querySelector('[data-pos-mobile-count]').textContent = units + (units === 1 ? ' item' : ' items') + ' · ' + formatted(total);
        feedback.textContent = !quantitiesValid ? 'Use whole quantities from 0 to 99.' : units > 200 ? 'Use no more than 200 units in one sale.' : 'Prices are verified when you review the sale.';
        valid = quantitiesValid && units > 0 && units <= 200;
        review.disabled = unavailable || !valid;
    };
    products.forEach(product => {
        product.querySelector('[data-pos-quantity]').addEventListener('input', update);
        product.querySelector('[data-pos-add]').addEventListener('click', () => change(product, 1));
    });
    let category = 'all';
    const search = form.querySelector('[data-pos-search]');
    const filter = () => {
        const term = search.value.trim().toLocaleLowerCase(); let count = 0;
        products.forEach(product => {
            const visible = (category === 'all' || product.dataset.category === category) && (product.dataset.name + ' ' + product.dataset.sku).toLocaleLowerCase().includes(term);
            product.hidden = !visible; if (visible) count++;
        });
        form.querySelector('[data-pos-no-results]').hidden = count > 0;
    };
    search.addEventListener('input', filter);
    form.querySelectorAll('[data-pos-category]').forEach(button => button.addEventListener('click', () => {
        category = button.dataset.posCategory;
        form.querySelectorAll('[data-pos-category]').forEach(other => other.setAttribute('aria-pressed', String(other === button))); filter();
    }));
    protectInputs(form);
    form.addEventListener('submit', event => { update(); if (event.submitter !== review || !valid || unavailable) event.preventDefault(); });
    window.addEventListener('pageshow', update);
    update();
})();
