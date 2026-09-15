'use strict';

// Progressive enhancement: retain the complete server-rendered fallback until the first read succeeds.
document.querySelectorAll('[data-ledger-table]').forEach((original) => {
    if (typeof DataTable !== 'function') return;
    const kind = original.dataset.ledgerTable;
    const count = original.querySelectorAll('thead th').length;
    const table = original.cloneNode(true);
    table.removeAttribute('data-ledger-table');
    table.removeAttribute('id');
    table.querySelector('tbody').replaceChildren();
    const wrapper = document.createElement('div');
    wrapper.className = 'ledger-table-enhancement';
    wrapper.hidden = true;
    wrapper.append(table);
    original.after(wrapper);
    const status = document.createElement('p');
    status.className = 'muted small ledger-table-summary';
    status.setAttribute('role', 'status');
    wrapper.before(status);
    const root = original.closest('.transaction-list, .core-table-panel, .account-statement, .bank-reconciliation') || original.parentElement;
    const fallbackPagination = root.querySelectorAll('.pagination, .statement-pagination');
    let ready = false;
    const parameters = new URLSearchParams(location.search);
    let controller;
    const numeric = kind === 'account' ? [4, 5, 6] : kind === 'bank' ? [2, 3] : kind === 'transactions' ? [2] : [3, 4];
    try {
        new DataTable(table, {
            serverSide: true,
            processing: true,
            searching: true,
            searchDelay: 350,
            pageLength: 25,
            lengthMenu: [25, 50, 100],
            order: [[0, kind === 'account' || kind === 'bank' ? 'asc' : 'desc']],
            orderMulti: false,
            autoWidth: false,
            search: { search: parameters.get('search') || '' },
            columns: Array.from({ length: count }, (_, index) => ({
                data: index,
                orderable: kind !== 'general-journals' || index < 3,
                className: numeric.includes(index) ? 'amount' : ''
            })),
            createdRow(row) {
                if (kind === 'account') {
                    ['Debit', 'Credit', 'Running balance'].forEach((label, offset) => {
                        row.cells[offset + 4].dataset.label = label;
                    });
                    row.cells[6].classList.add('statement-running');
                }
            },
            ajax: async (request, callback) => {
                controller?.abort();
                controller = new AbortController();
                const query = new URLSearchParams(parameters);
                query.set('table', kind);
                query.set('company_id', document.body.dataset.companyId);
                query.set('book_id', document.body.dataset.bookId);
                query.set('draw', String(request.draw));
                query.set('start', String(request.start));
                query.set('length', String(request.length));
                query.set('column', String(request.order?.[0]?.column ?? 0));
                query.set('direction', request.order?.[0]?.dir ?? 'asc');
                query.set('search', request.search.value.slice(0, 160));
                if (kind === 'account') {
                    query.set('account_id', original.dataset.accountId);
                    query.set('as_of', original.dataset.asOf);
                    query.set('from', original.dataset.from || '');
                }
                if (kind === 'bank') query.set('statement_id', original.dataset.statementId);
                try {
                    const response = await fetch(`${document.body.dataset.tableUrl}?${query}`, { credentials: 'same-origin', headers: { Accept: 'application/json' }, signal: controller.signal });
                    if (!response.ok || !response.headers.get('content-type')?.includes('application/json')) throw new Error('Table unavailable');
                    const data = await response.json();
                    if (!Array.isArray(data.data) || !Number.isInteger(data.recordsTotal) || !Number.isInteger(data.recordsFiltered)) throw new Error('Invalid table response');
                    if (!ready) {
                        ready = true;
                        original.hidden = true;
                        wrapper.hidden = false;
                        fallbackPagination.forEach((element) => { element.hidden = true; });
                    }
                    status.textContent = data.summary || '';
                    callback(data);
                } catch (error) {
                    if (error.name === 'AbortError') return;
                    status.textContent = 'The updated table could not be loaded. Reload to renew your session, or use the original view below.';
                    original.hidden = false;
                    wrapper.hidden = true;
                    fallbackPagination.forEach((element) => { element.hidden = false; });
                }
            }
        });
    } catch {
        wrapper.remove();
        status.textContent = 'Use the original table and page links below.';
    }
});
