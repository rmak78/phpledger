<?php
declare(strict_types=1);

// Synthetic fixture for 1.0.0 route-state acceptance sweep (workflow-acceptance agent).
// Runs INSIDE the dev runtime container (phpledger-web-1: PL_ENV=local, PL_DB_NAME=phpledger).
// Creates a brand-new throwaway user/company with a random suffix; never touches other agents' data.
if (getenv('PL_ENV') !== 'local' || getenv('PL_DB_NAME') !== 'phpledger') {
    fwrite(STDERR, "This fixture is scoped to the dev runtime (PL_ENV=local, PL_DB_NAME=phpledger).\n");
    exit(2);
}
require_once dirname(__DIR__) . '/www/phpledger/includes/bootstrap.php';

$suffix = bin2hex(random_bytes(5));
$email = 'route-sweep-' . $suffix . '@example.test';
$password = 'Synthetic-browser-only-2026!';
$actor = pl_create_user($email, 'Route Sweep Owner', $password);

$f = pl_create_company($actor, 'Route Sweep Co ' . $suffix, 'USD', '2026-01-01');
$company = $f['company_id'];
$book = $f['book_id'];
$accounts = $f['accounts'];

$extra = [];
foreach (['inventory' => ['1300', 'asset'], 'grni' => ['2100', 'liability'], 'tax_in' => ['1350', 'asset'], 'tax_out' => ['2150', 'liability'], 'variance' => ['5200', 'expense']] as $name => [$code, $type]) {
    $extra[$name] = pl_save_account($actor, $company, $book, ['code' => $code, 'name' => ucwords(str_replace('_', ' ', $name)), 'type' => $type, 'role' => null, 'is_active' => true, 'reason' => 'Route sweep fixture', 'creation_key' => 'account-' . $name])['id'];
}
// Second cash/bank account for reconciliation.
$bank = pl_save_account($actor, $company, $book, ['code' => '1010', 'name' => 'Sweep Bank Account', 'type' => 'asset', 'role' => 'cash_bank', 'is_active' => true, 'reason' => 'Route sweep fixture', 'creation_key' => 'account-bank'])['id'];

foreach (['inventory', 'purchasing', 'pos-showcase'] as $id) {
    $m = pl_module_registry()[$id];
    pl_set_company_module($actor, $company, $id, true, 0, $m['digest'], 'Route sweep fixture', 'module-' . $id);
}

$party = pl_save_party($actor, $company, $book, ['legal_name' => 'Sweep Customer Supplier', 'entity_type' => 'private_company', 'country_code' => 'GB', 'is_customer' => true, 'is_vendor' => true, 'currency' => 'USD', 'reason' => 'Route sweep fixture', 'request_key' => 'party']);
$partyId = $party['id'];

$product = pl_save_inventory_product($actor, $company, $book, ['sku' => 'SWEEP-ITEM', 'name' => 'Sweep Widget', 'kind' => 'stock', 'base_unit' => 'each', 'selling_price' => '25', 'is_active' => true,
    'inventory_account_id' => $extra['inventory'], 'cogs_account_id' => $accounts['5000'], 'sales_account_id' => $accounts['4000'], 'purchase_account_id' => $accounts['5000'], 'reason' => 'Route sweep fixture', 'idempotency_key' => 'product']);
$productId = $product['id'];

$tax = pl_create_tax_code($actor, $company, $book, ['code' => 'SW5', 'name' => 'Sweep five percent', 'treatment' => 'standard', 'sales_account_id' => $extra['tax_out'], 'purchase_account_id' => $extra['tax_in'], 'reason' => 'Route sweep fixture', 'idempotency_key' => 'tax-code']);
$taxId = $tax['id'];
pl_enter_tax_rate($actor, $company, $book, ['tax_code_id' => $taxId, 'effective_from' => '2026-01-01', 'percentage' => '5', 'reason' => 'Route sweep fixture', 'idempotency_key' => 'tax-rate']);

// AR invoice, posted (for statement/journal/settlement chains and ageing).
$invoice = pl_save_ar_document($actor, $company, $book, ['kind' => 'invoice', 'party_id' => $partyId, 'date' => '2026-01-05', 'due_date' => '2026-01-20', 'currency' => 'USD', 'reference' => 'Sweep invoice', 'creation_key' => 'invoice-1',
    'lines' => [['description' => 'Sweep service', 'quantity' => '1', 'unit_price' => '300.0000', 'account_id' => $accounts['4000']]]]);
$postedInvoice = pl_post_ar_document($actor, $company, $book, $invoice['id'], $invoice['revision']);
$invoiceId = $invoice['id'];
$journalId = $postedInvoice['journal_id'];

// A second, older invoice for ageing buckets.
$oldInvoice = pl_save_ar_document($actor, $company, $book, ['kind' => 'invoice', 'party_id' => $partyId, 'date' => '2026-01-05', 'due_date' => '2026-05-01', 'currency' => 'USD', 'reference' => 'Sweep ageing invoice', 'creation_key' => 'invoice-2',
    'lines' => [['description' => 'Sweep ageing service', 'quantity' => '1', 'unit_price' => '150.0000', 'account_id' => $accounts['4000']]]]);
pl_post_ar_document($actor, $company, $book, $oldInvoice['id'], $oldInvoice['revision']);

// AP bill, posted.
$bill = pl_save_ar_document($actor, $company, $book, ['kind' => 'bill', 'party_id' => $partyId, 'date' => '2026-01-06', 'due_date' => '2026-01-21', 'currency' => 'USD', 'reference' => 'Sweep bill', 'creation_key' => 'bill-1',
    'lines' => [['description' => 'Sweep supplies', 'quantity' => '1', 'unit_price' => '120.0000', 'account_id' => $accounts['5000']]]]);
pl_post_ar_document($actor, $company, $book, $bill['id'], $bill['revision']);
$billId = $bill['id'];

// Purchase order, confirmed (for purchasing list/detail routes).
$po = pl_save_and_confirm_purchase_order($actor, $company, $book, ['party_id' => $partyId, 'date' => '2026-01-07', 'currency' => 'USD', 'reference' => 'Sweep PO', 'creation_key' => 'po-1',
    'lines' => [['description' => 'Sweep purchase order line', 'quantity' => '2', 'unit_price' => '40.0000', 'account_id' => $accounts['5000'], 'product_id' => $productId]]]);
$poId = $po['id'];

// Period for /periods route (a closed second period so both open/closed states render).
$period2 = pl_create_period($actor, $company, $book, ['start_date' => '2027-01-01', 'end_date' => '2027-12-31', 'reason' => 'Route sweep fixture', 'request_key' => 'period-2']);

// Bank statement import (for /bank-reconciliation states).
$statementInput = ['account_id' => $bank, 'reference' => 'Sweep statement', 'start_date' => '2026-01-01', 'end_date' => '2026-01-31', 'opening_balance' => '0.0000', 'closing_balance' => '300.0000',
    'baseline_confirmed' => true, 'rows' => [['date' => '2026-01-20', 'reference' => 'Sweep receipt', 'description' => 'Sweep customer receipt', 'money_in' => '300.0000', 'money_out' => '0.0000']]];
$preview = pl_bank_preview_statement($actor, $company, $book, $statementInput);
$statement = pl_bank_import_statement($actor, $company, $book, $statementInput, 'statement-1', $preview['digest']);
$statementId = $statement['id'];

// Posted general journal (for /general-journals/detail, /general-journals/edit).
$generalDraft = pl_save_and_post_general_draft($actor, $company, $book, [
    'date' => '2026-01-12', 'reference' => 'Sweep GJ', 'description' => 'Sweep general journal', 'creation_key' => 'gj-1',
    'lines' => [
        ['account_id' => $accounts['5000'], 'debit' => '20.0000', 'credit' => '0.0000', 'description' => 'Sweep GJ debit'],
        ['account_id' => $accounts['1000'], 'debit' => '0.0000', 'credit' => '20.0000', 'description' => 'Sweep GJ credit'],
    ],
]);

// Simple cash receipt and expense documents (for /transactions/*, form-recovery pass).
$receiptDoc = pl_save_document($actor, $company, $book, ['kind' => 'receipt', 'date' => '2026-01-10', 'amount' => '75.0000', 'money_account_id' => $accounts['1000'], 'category_account_id' => $accounts['4000'], 'counterparty' => 'Sweep payer', 'reference' => 'Sweep receipt doc', 'creation_key' => 'receipt-doc-1']);
$expenseDoc = pl_save_document($actor, $company, $book, ['kind' => 'expense', 'date' => '2026-01-11', 'amount' => '45.0000', 'money_account_id' => $accounts['1000'], 'category_account_id' => $accounts['5000'], 'counterparty' => 'Sweep payee', 'reference' => 'Sweep expense doc', 'creation_key' => 'expense-doc-1']);

$fixture = [
    'email' => $email,
    'password' => $password,
    'actor_id' => $actor,
    'company_id' => $company,
    'book_id' => $book,
    'party_id' => $partyId,
    'product_id' => $productId,
    'tax_code_id' => $taxId,
    'accounts' => $accounts,
    'extra' => $extra,
    'bank_account_id' => $bank,
    'invoice_id' => $invoiceId,
    'bill_id' => $billId,
    'purchase_order_id' => $poId,
    'period_id' => $f['period_id'],
    'period2_id' => $period2['id'],
    'statement_id' => $statementId,
    'receipt_document_id' => $receiptDoc['id'],
    'expense_document_id' => $expenseDoc['id'],
    'journal_id' => $journalId,
    'general_draft_id' => $generalDraft['id'],
];
echo json_encode($fixture, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT) . "\n";
