'use strict';

(() => {
    const form = document.querySelector('[data-core-journal]');
    if (!form) return;
    const body = form.querySelector('[data-journal-rows]');
    const add = form.querySelector('[data-add-journal-row]');
    const message = form.querySelector('[data-journal-message]');
    const totals = form.querySelector('[data-journal-totals]');
    const rows = () => Array.from(body.querySelectorAll('[data-journal-row]'));
    const template = rows()[0]?.cloneNode(true);
    if (!template) return;

    const amount = (text) => {
        const value = text.trim();
        if (value === '') return 0n;
        if (!/^[0-9]+(?:\.[0-9]{1,4})?$/.test(value)) return null;
        const [whole, fraction = ''] = value.split('.');
        return BigInt(whole) * 10000n + BigInt(fraction.padEnd(4, '0'));
    };
    const format = (value) => {
        const sign = value < 0n ? '-' : '';
        const absolute = value < 0n ? -value : value;
        const whole = (absolute / 10000n).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        const fraction = (absolute % 10000n).toString().padStart(4, '0');
        return sign + whole + '.' + (fraction.endsWith('00') ? fraction.slice(0, 2) : fraction);
    };
    const refreshTotals = () => {
        let debit = 0n;
        let credit = 0n;
        let active = 0;
        let invalid = false;
        for (const row of rows()) {
            const fields = Array.from(row.querySelectorAll('input, select'));
            if (fields.every((field) => field.value.trim() === '')) continue;
            active++;
            const dr = amount(row.querySelector('[data-journal-amount="debit"]').value);
            const cr = amount(row.querySelector('[data-journal-amount="credit"]').value);
            if (dr === null || cr === null) {
                invalid = true;
                continue;
            }
            debit += dr;
            credit += cr;
            if ((dr > 0n) === (cr > 0n) || !row.querySelector('[data-journal-account]').value) invalid = true;
        }
        form.querySelector('[data-journal-total="debit"]').textContent = format(debit);
        form.querySelector('[data-journal-total="credit"]').textContent = format(credit);
        const balance = form.querySelector('[data-journal-balance]');
        balance.classList.toggle('core-unbalanced', invalid || debit !== credit);
        balance.textContent = invalid
            ? 'Check each account and enter a debit or credit on every used line.'
            : active === 0
                ? 'Enter journal lines to preview the balance.'
                : debit === credit
                    ? 'Balance preview: Balanced. Save to review the entry.'
                    : 'Difference (debit minus credit): ' + format(debit - credit);
    };
    const numberRows = () => {
        rows().forEach((row, index) => {
            row.querySelector('[data-line-number]').textContent = String(index + 1);
            row.querySelectorAll('[name]').forEach((field) => {
                field.name = field.name.replace(/^lines\[[^\]]*\]/, 'lines[' + index + ']');
            });
            row.querySelectorAll('[data-line-label]').forEach((label) => {
                label.textContent = label.dataset.lineLabel + ', line ' + (index + 1);
            });
            const remove = row.querySelector('[data-remove-journal-row]');
            remove.hidden = false;
            remove.setAttribute('aria-label', 'Remove line ' + (index + 1));
        });
        add.disabled = rows().length >= 100;
    };
    const clearRow = (row) => {
        row.querySelectorAll('input').forEach((field) => {
            field.value = '';
            field.removeAttribute('value');
        });
        row.querySelectorAll('select').forEach((select) => {
            select.querySelectorAll('option[data-unavailable-account]').forEach((option) => option.remove());
            select.querySelectorAll('option').forEach((option) => option.removeAttribute('selected'));
            select.value = '';
        });
    };
    add.hidden = false;
    totals.hidden = false;
    add.addEventListener('click', () => {
        if (rows().length >= 100) return;
        const row = template.cloneNode(true);
        clearRow(row);
        body.append(row);
        numberRows();
        refreshTotals();
        row.querySelector('[data-journal-account]').focus();
        message.textContent = 'Line ' + rows().length + ' added.';
    });
    body.addEventListener('click', (event) => {
        const remove = event.target.closest('[data-remove-journal-row]');
        if (!remove) return;
        const row = remove.closest('[data-journal-row]');
        const currentRows = rows();
        const index = currentRows.indexOf(row);
        if (currentRows.length === 1) {
            clearRow(row);
            row.querySelector('[data-journal-account]').focus();
        } else {
            row.remove();
            const next = rows()[Math.min(index, rows().length - 1)];
            next.querySelector('[data-journal-account]').focus();
        }
        numberRows();
        refreshTotals();
        message.textContent = 'Line ' + (index + 1) + ' removed from this draft.';
    });
    form.addEventListener('input', refreshTotals);
    form.addEventListener('change', refreshTotals);
    // Keep the no-JavaScript spare rows in HTML, but start the interactive form
    // with two lines. Never discard a row containing submitted or saved values.
    rows().slice(2).forEach((row) => {
        if (Array.from(row.querySelectorAll('input, select')).every((field) => field.value === '')) row.remove();
    });
    numberRows();
    refreshTotals();
})();
