<?php
declare(strict_types=1);

// Read-only validation of authored demonstration data. This file never bootstraps the app or connects to a database.
function sample_check(bool $ok, string $message): void
{
    if (!$ok) {
        throw new RuntimeException($message);
    }
}

function sample_amount(mixed $value, bool $signed = false): int
{
    sample_check(is_string($value) && preg_match($signed ? '/^-?\d{1,8}\.\d{4}$/' : '/^\d{1,8}\.\d{4}$/', $value) === 1, 'Expected a bounded, four-decimal string.');
    return (int)str_replace('.', '', (string)$value);
}

function sample_product(int $quantity, int $price): int
{
    sample_check(abs($quantity) <= intdiv(PHP_INT_MAX, max(1, abs($price))), 'Fixture multiplication exceeds integer range.');
    $product = $quantity * $price;
    sample_check($product % 10000 === 0, 'Fixture multiplication requires unapproved rounding.');
    return intdiv($product, 10000);
}

function sample_date(string $date): void
{
    sample_check(preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $date, $parts) === 1, 'Invalid date format.');
    sample_check(checkdate((int)$parts[2], (int)$parts[3], (int)$parts[1]), 'Invalid calendar date.');
}

function sample_index(array $records, string $field = 'id'): array
{
    $out = [];
    foreach ($records as $record) {
        sample_check(isset($record[$field]) && is_string($record[$field]) && $record[$field] !== '', 'Missing record identifier.');
        sample_check(!isset($out[$record[$field]]), 'Duplicate identifier: ' . $record[$field]);
        $out[$record[$field]] = $record;
    }
    return $out;
}

function sample_effect(array $lines, array $accounts, bool $allowEmpty = false): array
{
    sample_check($allowEmpty || count($lines) >= 2, 'A financial journal must have at least two lines.');
    $effect = array_fill_keys(array_keys($accounts), 0);
    $debits = $credits = 0;
    foreach ($lines as $line) {
        sample_check(isset($accounts[$line['account_key']]), 'Unknown journal account.');
        $debit = sample_amount($line['debit']);
        $credit = sample_amount($line['credit']);
        sample_check(($debit > 0) !== ($credit > 0), 'Journal lines must have one positive side.');
        $debits += $debit;
        $credits += $credit;
        $effect[$line['account_key']] += $debit - $credit;
    }
    sample_check($debits === $credits, 'Unbalanced journal.');
    return $effect;
}

function sample_stock_key(array $row): string
{
    return $row['item_id'] . '|' . $row['location_id'] . '|' . ($row['lot_id'] ?? 'none');
}

function sample_validate(array $p): array
{
    sample_check($p['schema_version'] === '1.0.0' && $p['pack_version'] === '1.0.0', 'Unknown schema or pack version.');
    sample_check($p['demo_only'] === true && $p['sample'] === true && $p['status'] === 'authored_candidate_not_runtime_seed', 'Missing candidate demo markers.');
    sample_check($p['isolation']['may_merge_into_real_company'] === false && $p['isolation']['automatic_posting_authorized'] === false, 'Unsafe isolation metadata.');
    sample_check($p['country_variations']['reviewed_variants'] === [] && $p['country_variations']['automatic_tax_activation'] === false, 'Unexpected country activation.');
    sample_check($p['business']['base_currency'] === 'USD' && $p['business']['country_code'] === null, 'Unexpected fixture currency or country.');
    $start = $p['business']['accounting_start_date'];
    $end = $p['business']['as_of_date'];
    sample_date($start);
    sample_date($end);
    sample_check($p['opening']['date'] === $start && $p['expected_reports']['as_of_date'] === $end, 'Cutover/report date mismatch.');
    $accounts = sample_index($p['accounts'], 'key');
    $roles = [];
    foreach ($accounts as $key => $a) {
        sample_check(in_array($a['type'], ['asset', 'liability', 'equity', 'income', 'expense'], true), 'Unknown account type.');
        sample_check(!isset($roles[$a['fixture_role']]), 'Duplicate account role.');
        $roles[$a['fixture_role']] = $key;
    }
    $accountFor = static function (string $role) use ($roles): string {
        sample_check(isset($roles[$role]), 'Missing account role: ' . $role);
        return $roles[$role];
    };
    $contacts = sample_index($p['contacts']);
    foreach ($contacts as $contact) {
        sample_check($contact['sample'] === true && str_ends_with($contact['email'], '@example.invalid'), 'Contact must be sample and non-deliverable.');
    }
    $items = sample_index($p['items']);
    $locations = sample_index($p['locations']);
    foreach ($items as $item) {
        sample_check(in_array($item['kind'], ['stock', 'service'], true), 'Unknown item kind.');
        sample_check(isset($accounts[$item['income_account_key']]) && $accounts[$item['income_account_key']]['type'] === 'income', 'Unknown item income account.');
        sample_amount($item['sale_price']);
        $cost = sample_amount($item['fixture_unit_cost']);
        sample_check($item['medical_use'] === false, 'Medical sample use must be disabled.');
        sample_check($item['kind'] === 'stock' ? $cost > 0 : $cost === 0, 'Item cost/type mismatch.');
    }
    $balances = sample_effect($p['opening']['journal'], $accounts);
    $documents = sample_index(array_merge($p['opening']['documents'], $p['documents']));
    $openBalances = [];
    $documentKinds = [];
    $openingAR = $openingAP = 0;
    foreach ($p['opening']['documents'] as $d) {
        sample_check(isset($contacts[$d['contact_id']]), 'Unknown opening contact.');
        sample_date($d['issued_on']);
        sample_date($d['due_on']);
        sample_check($d['issued_on'] < $start && $d['posting_mode'] === 'opening_control_detail_only', 'Opening document must be prior detail only.');
        $amount = sample_amount($d['outstanding_at_cutover']);
        sample_check(sample_amount($d['original_amount']) - sample_amount($d['paid_before_cutover']) === $amount, 'Opening document reconciliation failed.');
        sample_check(in_array($d['kind'], ['invoice', 'bill'], true), 'Invalid opening document kind.');
        $openBalances[$d['id']] = $amount;
        $documentKinds[$d['id']] = $d['kind'];
        if ($d['kind'] === 'invoice') { $openingAR += $amount; } else { $openingAP += $amount; }
    }
    sample_check($openingAR === $balances[$accountFor('accounts_receivable')] && $openingAP === -$balances[$accountFor('accounts_payable')], 'Opening AR/AP detail does not equal controls; possible double counting.');
    foreach ($p['documents'] as $d) {
        sample_check(isset($contacts[$d['contact_id']]), 'Unknown document contact.');
        sample_date($d['issued_on']);
        sample_date($d['due_on']);
        sample_check($d['issued_on'] >= $start && $d['issued_on'] <= $end, 'Document outside sample period.');
        $total = 0;
        foreach ($d['lines'] as $l) {
            sample_check(isset($l['item_id']) ? isset($items[$l['item_id']]) : isset($accounts[$l['account_key']]), 'Unknown document line item/account.');
            $quantity = sample_amount($l['quantity']);
            sample_check($quantity > 0, 'Document quantity must be positive.');
            $amount = sample_product($quantity, sample_amount($l['unit_price']));
            sample_check($amount === sample_amount($l['amount']), 'Document quantity and price do not equal amount.');
            $total += $amount;
        }
        sample_check($total === sample_amount($d['total']) && sample_amount($d['tax_amount']) === 0 && $d['tax_status'] === 'not_modelled', 'Document total or tax scope mismatch.');
    }
    $stock = [];
    $stockValue = 0;
    $lotExpiry = [];
    $applyStock = static function (array $s, int $quantity) use (&$stock, &$stockValue, &$lotExpiry, $items, $locations): void {
        sample_check(isset($items[$s['item_id']], $locations[$s['location_id']]), 'Unknown stock item/location.');
        $item = $items[$s['item_id']];
        sample_check($item['kind'] === 'stock' && sample_amount($s['unit_cost']) === sample_amount($item['fixture_unit_cost']), 'Stock cost differs from constant fixture cost.');
        sample_check($item['lot_tracking'] === ($s['lot_id'] !== null), 'Missing or unexpected lot.');
        $key = sample_stock_key($s);
        if ($s['lot_id'] !== null) {
            $lotKey = $s['item_id'] . '|' . $s['lot_id'];
            if (($s['expires_on'] ?? null) !== null) {
                sample_date($s['expires_on']);
                sample_check(!isset($lotExpiry[$lotKey]) || $lotExpiry[$lotKey] === $s['expires_on'], 'Conflicting expiry for lot.');
                $lotExpiry[$lotKey] = $s['expires_on'];
            }
            sample_check(isset($lotExpiry[$lotKey]), 'Unknown lot expiry.');
        }
        $stock[$key] = ($stock[$key] ?? 0) + $quantity;
        sample_check($stock[$key] >= 0, 'Negative stock.');
        $stockValue += sample_product($quantity, sample_amount($s['unit_cost']));
    };
    foreach ($p['opening']['stock'] as $s) {
        sample_check(!isset($stock[sample_stock_key($s)]), 'Duplicate opening stock row.');
        $quantity = sample_amount($s['quantity']);
        sample_check(sample_product($quantity, sample_amount($s['unit_cost'])) === sample_amount($s['value']), 'Opening stock value mismatch.');
        $applyStock($s, $quantity);
    }
    sample_check($stockValue === ($balances[$roles['inventory'] ?? ''] ?? 0), 'Opening inventory does not reconcile.');
    $contracts = sample_index($p['opening']['deferred_contracts']);
    $deferredBalances = [];
    $deferredOpening = 0;
    foreach ($contracts as $id => $c) {
        $value = sample_amount($c['deferred_at_cutover']);
        sample_check(sample_amount($c['original_prepayment']) - sample_amount($c['recognized_before_cutover']) === $value, 'Opening prepayment reconciliation failed.');
        $deferredBalances[$id] = $value;
        $deferredOpening += $value;
    }
    sample_check($deferredOpening === -($balances[$roles['deferred_revenue'] ?? ''] ?? 0), 'Opening deferred income does not reconcile.');
    $events = sample_index($p['events']);
    $seen = $keys = $postedDocuments = $reversed = $recognized = $returned = [];
    $lastDate = $start;
    $turnover = 0;
    foreach ($p['opening']['journal'] as $l) { $turnover += sample_amount($l['debit']); }
    foreach ($events as $id => $e) {
        sample_date($e['date']);
        sample_check($e['date'] >= $lastDate && $e['date'] <= $end, 'Events must be chronological and inside period.');
        $lastDate = $e['date'];
        sample_check(!isset($keys[$e['idempotency_key']]), 'Duplicate idempotency key.');
        $keys[$e['idempotency_key']] = true;
        sample_check($e['runtime_status'] === 'expected_accounting_fixture_only', 'Unexpected runtime claim.');
        $kind = $e['kind'];
        $actual = sample_effect($e['expected_journal'], $accounts, $kind === 'stock_transfer');
        $expected = array_fill_keys(array_keys($accounts), 0);
        $add = static function (string $role, int $value) use (&$expected, $accountFor): void { $expected[$accountFor($role)] += $value; };
        $d = null;
        if (isset($e['document_id'])) {
            sample_check(isset($documents[$e['document_id']]) && !isset($postedDocuments[$e['document_id']]), 'Missing or multiply posted document.');
            $d = $documents[$e['document_id']];
            sample_check(isset($d['total']) && $d['issued_on'] === $e['date'], 'Only current documents post on their issue date.');
            $postedDocuments[$d['id']] = true;
            if (in_array($d['kind'], ['invoice', 'bill'], true)) {
                $openBalances[$d['id']] = sample_amount($d['total']);
                $documentKinds[$d['id']] = $d['kind'];
            }
        }
        $netStock = [];
        $stockDeltaValue = 0;
        foreach ($e['stock_movements'] ?? [] as $s) {
            $q = sample_amount($s['quantity_delta'], true);
            sample_check($q !== 0, 'Empty stock movement.');
            $netStock[$s['item_id']] = ($netStock[$s['item_id']] ?? 0) + $q;
            $stockDeltaValue += sample_product($q, sample_amount($s['unit_cost']));
            $applyStock($s, $q);
        }
        $allocTotal = 0;
        foreach ($e['allocations'] ?? [] as $a) {
            sample_check(isset($openBalances[$a['document_id']]), 'Allocation refers to a document not yet open.');
            $expectedKind = $kind === 'supplier_payment' ? 'bill' : 'invoice';
            sample_check($documentKinds[$a['document_id']] === $expectedKind, 'Allocation targets wrong control account.');
            $v = sample_amount($a['amount']);
            sample_check($v > 0 && $v <= $openBalances[$a['document_id']], 'Allocation overpays an open document.');
            $openBalances[$a['document_id']] -= $v;
            $allocTotal += $v;
        }
        if (in_array($kind, ['cash_sale', 'credit_sale', 'sales_return', 'purchase'], true)) {
            sample_check($d !== null, 'Missing financial document.');
            $expectedKind = ['cash_sale'=>'cash_sale','credit_sale'=>'invoice','sales_return'=>'credit_note','purchase'=>'bill'][$kind];
            sample_check($d['kind'] === $expectedKind, 'Event/document type mismatch.');
            $isReturn = $kind === 'sales_return';
            $isBuy = $kind === 'purchase';
            $total = sample_amount($d['total']);
            if ($isBuy) { $add('inventory', $total); $add('accounts_payable', -$total); }
            else {
                $add($kind === 'cash_sale' ? 'cash_on_hand' : 'accounts_receivable', $isReturn ? -$total : $total);
                foreach ($d['lines'] as $l) { $expected[$items[$l['item_id']]['income_account_key']] += ($isReturn ? 1 : -1) * sample_amount($l['amount']); }
                if ($stockDeltaValue !== 0) { $add('inventory', $stockDeltaValue); $add('cost_of_goods_sold', -$stockDeltaValue); }
            }
            $documentStock = [];
            foreach ($d['lines'] as $l) {
                if ($items[$l['item_id']]['kind'] === 'stock') { $documentStock[$l['item_id']] = ($documentStock[$l['item_id']] ?? 0) + sample_amount($l['quantity']) * ($isBuy || $isReturn ? 1 : -1); }
            }
            ksort($documentStock); ksort($netStock);
            sample_check($documentStock === $netStock, 'Document quantities do not equal stock movements.');
            if ($isBuy) { sample_check($stockDeltaValue === $total, 'Purchase cost and stock value differ.'); }
            if ($isReturn) {
                $original = $documents[$d['related_document_id']] ?? null;
                sample_check($original !== null && $original['kind'] === 'invoice' && $original['contact_id'] === $d['contact_id'], 'Invalid return source/contact.');
                sample_check(count($e['allocations']) === 1 && $e['allocations'][0]['document_id'] === $original['id'] && $allocTotal === $total, 'Credit note must be allocated once to its source invoice.');
                foreach ($d['lines'] as $l) {
                    $sourceQuantity = 0;
                    foreach ($original['lines'] as $sourceLine) {
                        if ($sourceLine['item_id'] === $l['item_id']) { $sourceQuantity += sample_amount($sourceLine['quantity']); sample_check($sourceLine['unit_price'] === $l['unit_price'], 'Return price differs from source.'); }
                    }
                    $returnKey = $original['id'] . '|' . $l['item_id'];
                    $returned[$returnKey] = ($returned[$returnKey] ?? 0) + sample_amount($l['quantity']);
                    sample_check($returned[$returnKey] <= $sourceQuantity, 'Return exceeds sold quantity.');
                }
            }
        } elseif ($kind === 'receipt' || $kind === 'supplier_payment') {
            sample_check($allocTotal === sample_amount($e['total']), 'Payment total does not equal allocations.');
            $add('bank_current', $kind === 'receipt' ? $allocTotal : -$allocTotal);
            $add($kind === 'receipt' ? 'accounts_receivable' : 'accounts_payable', $kind === 'receipt' ? -$allocTotal : $allocTotal);
        } elseif ($kind === 'expense') {
            sample_check(isset($accounts[$e['expense_account_key']]) && $accounts[$e['expense_account_key']]['type'] === 'expense', 'Invalid expense account.');
            $expected[$e['expense_account_key']] += sample_amount($e['total']); $add('bank_current', -sample_amount($e['total']));
        } elseif ($kind === 'expense_bill') {
            sample_check($d !== null && $d['kind'] === 'bill', 'Missing expense bill.');
            foreach ($d['lines'] as $l) { sample_check($accounts[$l['account_key']]['type'] === 'expense', 'Wrong expense bill account.'); $expected[$l['account_key']] += sample_amount($l['amount']); }
            $add('accounts_payable', -sample_amount($d['total']));
        } elseif ($kind === 'cash_transfer') {
            $add('bank_current', sample_amount($e['total'])); $add('cash_on_hand', -sample_amount($e['total']));
        } elseif ($kind === 'reversal') {
            $source = $events[$e['reverses_event_id']] ?? null;
            sample_check($source !== null && isset($seen[$source['id']]) && !isset($reversed[$source['id']]) && $source['kind'] === 'expense', 'Invalid or repeated sample expense reversal.');
            $reversed[$source['id']] = true;
            $expected = array_map(static fn (int $v): int => -$v, sample_effect($source['expected_journal'], $accounts));
        } elseif ($kind === 'prepayment') {
            sample_check($d !== null && $d['kind'] === 'prepayment' && !isset($contracts[$e['contract']['id']]), 'Invalid prepaid contract.');
            $contracts[$e['contract']['id']] = $e['contract'];
            $deferredBalances[$e['contract']['id']] = sample_amount($d['total']);
            $add('bank_current', sample_amount($d['total'])); $add('deferred_revenue', -sample_amount($d['total']));
        } elseif ($kind === 'revenue_recognition') {
            $recognition = 0;
            foreach ($e['contract_recognition'] as $r) {
                $contract = $contracts[$r['contract_id']] ?? null;
                $receiptKey = $r['contract_id'] . '|' . $e['recognition_period'];
                sample_check($contract !== null && !isset($recognized[$receiptKey]) && substr($e['date'], 0, 7) === $e['recognition_period'], 'Invalid or repeated recognition period.');
                sample_check($e['date'] >= $contract['coverage_start'] && $e['date'] <= $contract['coverage_end'], 'Recognition outside service term.');
                $amount = sample_amount($r['amount']);
                sample_check($amount === sample_amount($contract['monthly_recognition']) && $amount <= $deferredBalances[$r['contract_id']], 'Recognition exceeds earned/deferred amount.');
                $deferredBalances[$r['contract_id']] -= $amount; $recognized[$receiptKey] = true; $recognition += $amount;
            }
            $add('deferred_revenue', $recognition); $add('membership_revenue', -$recognition);
        } elseif ($kind === 'stock_transfer') {
            sample_check($e['expected_journal'] === [] && array_sum(array_map('abs', $netStock)) === 0 && $stockDeltaValue === 0, 'Location transfer must conserve each item and financial value.');
        } else { throw new RuntimeException('Unknown event kind: ' . $kind); }
        sample_check($actual === $expected, 'Journal does not match the economic event: ' . $id);
        foreach ($actual as $key => $value) { $balances[$key] += $value; }
        foreach ($e['expected_journal'] as $l) { $turnover += sample_amount($l['debit']); }
        sample_check($stockValue === ($balances[$roles['inventory'] ?? ''] ?? 0), 'Inventory control drift after ' . $id);
        $seen[$id] = true;
    }
    sample_check(count($postedDocuments) === count($p['documents']), 'Unposted current document in expected results.');
    $r = $p['expected_reports'];
    $reportBalance = sample_index($r['trial_balance'], 'account_key');
    sample_check(count($reportBalance) === count($accounts), 'Incomplete trial balance.');
    $debit = $credit = $assets = $liabilities = $equity = $income = $expenses = 0;
    foreach ($balances as $key => $value) {
        sample_check(isset($reportBalance[$key]) && sample_amount($reportBalance[$key]['debit']) === max($value, 0) && sample_amount($reportBalance[$key]['credit']) === max(-$value, 0), 'Trial balance mismatch: ' . $key);
        $debit += max($value, 0); $credit += max(-$value, 0);
        switch ($accounts[$key]['type']) {
            case 'asset': $assets += $value; break;
            case 'liability': $liabilities -= $value; break;
            case 'equity': $equity -= $value; break;
            case 'income': $income -= $value; break;
            case 'expense': $expenses += $value; break;
        }
    }
    sample_check($debit === $credit && $assets === $liabilities + $equity + $income - $expenses, 'Accounting equation failed.');
    $expectedScalars = ['trial_balance_debit'=>$debit,'trial_balance_credit'=>$credit,'journal_turnover_debit_including_opening'=>$turnover,'journal_turnover_credit_including_opening'=>$turnover,'income'=>$income,'expenses'=>$expenses,'net_result'=>$income-$expenses,'assets'=>$assets,'liabilities'=>$liabilities,'opening_equity'=>$equity,'equity_including_current_result'=>$equity+$income-$expenses,'accounts_receivable'=>$balances[$accountFor('accounts_receivable')],'accounts_payable'=>-$balances[$accountFor('accounts_payable')],'inventory_value'=>$stockValue,'cash_on_hand'=>$balances[$accountFor('cash_on_hand')],'bank_current'=>$balances[$accountFor('bank_current')]];
    foreach ($expectedScalars as $key => $value) { sample_check(sample_amount($r[$key], true) === $value, 'Report scalar mismatch: ' . $key); }
    $reportDocuments = sample_index($r['document_balances'], 'document_id');
    sample_check(count($reportDocuments) === count($openBalances), 'Incomplete open-document report.');
    $ar = $ap = 0;
    foreach ($openBalances as $id => $value) {
        sample_check(isset($reportDocuments[$id]) && $reportDocuments[$id]['kind'] === $documentKinds[$id] && sample_amount($reportDocuments[$id]['outstanding']) === $value, 'Document balance mismatch.');
        if ($documentKinds[$id] === 'invoice') { $ar += $value; } else { $ap += $value; }
    }
    sample_check($ar === $balances[$accountFor('accounts_receivable')] && $ap === -$balances[$accountFor('accounts_payable')], 'Ending AR/AP control does not reconcile to open documents.');
    sample_check(array_sum($deferredBalances) === -($balances[$roles['deferred_revenue'] ?? ''] ?? 0), 'Ending prepaid contracts do not reconcile.');
    $reportStock = [];
    foreach ($r['stock_on_hand'] as $s) { $key = sample_stock_key($s); sample_check(!isset($reportStock[$key]), 'Duplicate stock report row.'); $reportStock[$key] = sample_amount($s['quantity']); }
    ksort($reportStock); ksort($stock); sample_check($reportStock === $stock, 'Ending stock quantity report mismatch.');
    foreach ($p['industry_scenarios'] as $scenario) {
        sample_check($scenario['status'] === 'future_module_scenario_not_implemented', 'Future module scenario presented as implemented.');
        $entity = $scenario['fixture_entity'];
        if ($p['pack_id'] === 'restaurant' && $scenario['id'] === 'table-kot') {
            $order = $documents[$entity['order_document_id']] ?? null;
            sample_check($order !== null && (int)$entity['covers'] * 10000 === sample_amount($order['lines'][0]['quantity']), 'Restaurant covers do not match the one-meal-per-person scenario.');
        }
        if ($p['pack_id'] === 'service-workshop' && $scenario['id'] === 'job-card') {
            $asset = $entity['customer_asset'];
            sample_check($entity['asset_ref'] === $asset['id'] && isset($contacts[$asset['owner_contact_id']]), 'Workshop customer asset reference is unresolved.');
            sample_check($asset['ownership'] === 'customer' && $asset['owned_inventory'] === false && $asset['capitalized_as_business_asset'] === false && $asset['expected_stock_movements'] === [] && $asset['expected_journal'] === [], 'Customer property must be excluded from owned assets and financial stock.');
            $quote = $entity['quote'];
            $invoice = $documents[$entity['invoice_document_id']] ?? null;
            sample_check($entity['quote_document_id'] === $quote['id'] && $invoice !== null && $quote['contact_id'] === $invoice['contact_id'] && $quote['lines'] === $invoice['lines'] && $quote['total'] === $invoice['total'] && $quote['expected_journal'] === [], 'Workshop estimate does not resolve to its nonposting source or agreed invoice.');
        }
    }
    return ['events'=>count($events),'documents'=>count($documents),'items'=>count($items)];
}

try {
    sample_check(PHP_INT_SIZE === 8, 'A 64-bit PHP runtime is required.');
    $files = glob(dirname(__DIR__) . '/resources/sample-data/*.json') ?: [];
    sample_check(count($files) === 7, 'Expected seven version-one business packs.');
    $totals = ['packs'=>0,'events'=>0,'documents'=>0,'items'=>0];
    $packs = [];
    foreach ($files as $file) {
        $data = json_decode((string)file_get_contents($file), true, 512, JSON_THROW_ON_ERROR);
        sample_check(is_array($data), 'Pack must be an object.');
        try { $counts = sample_validate($data); } catch (Throwable $error) { throw new RuntimeException(basename($file) . ': ' . $error->getMessage(), 0, $error); }
        sample_check(basename($file, '.json') === $data['pack_id'] && !isset($packs[$data['pack_id']]), 'Pack filename/id mismatch.');
        $packs[$data['pack_id']] = $data;
        ++$totals['packs'];
        foreach ($counts as $key => $value) { $totals[$key] += $value; }
    }
    echo 'Sample data valid: ' . json_encode($totals, JSON_THROW_ON_ERROR) . ".\n";
    if (in_array('--self-test', $argv ?? [], true)) {
        $mutations = [
            'unbalanced journal' => static function (array &$p): void { $p['events'][0]['expected_journal'][0]['debit'] = '1.0000'; },
            'opening AR mismatch' => static function (array &$p): void { $p['opening']['documents'][0]['outstanding_at_cutover'] = '301.0000'; },
            'unknown item' => static function (array &$p): void { $p['documents'][0]['lines'][0]['item_id'] = 'missing'; },
            'duplicate posting key' => static function (array &$p): void { $p['events'][1]['idempotency_key'] = $p['events'][0]['idempotency_key']; },
            'overallocated receipt' => static function (array &$p): void { foreach ($p['events'] as &$e) { if ($e['kind'] === 'receipt') { $e['allocations'][0]['amount'] = '9999.0000'; break; } } },
            'negative stock' => static function (array &$p): void { $p['events'][0]['stock_movements'][0]['quantity_delta'] = '-9999.0000'; },
            'misleading future scope' => static function (array &$p): void { $p['industry_scenarios'][0]['status'] = 'implemented'; },
            'wrong report total' => static function (array &$p): void { $p['expected_reports']['net_result'] = '9999.0000'; },
        ];
        foreach ($mutations as $name => $mutate) {
            $candidate = $packs['retail-shop'];
            $mutate($candidate);
            $rejected = false;
            try { sample_validate($candidate); } catch (Throwable) { $rejected = true; }
            sample_check($rejected, 'Validator failed to reject: ' . $name);
        }
        echo 'Validator self-test: ' . count($mutations) . " invalid in-memory packs rejected.\n";
    }
} catch (Throwable $error) {
    fwrite(STDERR, 'Sample data validation failed: ' . $error->getMessage() . "\n");
    exit(1);
}
