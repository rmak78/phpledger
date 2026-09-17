<?php
declare(strict_types=1);

/** Purchasing owns commitments and matching, while Inventory and AP own their ledgers. */
function pl_purchase_command(int $actorId, int $companyId, int $bookId, string $action, string $key, array $payload, callable $work): array
{
    $key = pl_request_key($key);
    $hash = hash('sha256', json_encode([$action, $payload], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE));
    return pl_ledger_transaction(function () use ($actorId, $companyId, $bookId, $action, $key, $hash, $work): array {
        pl_ledger_book($companyId, $bookId, true);
        pl_require_module($actorId, $companyId, $bookId, 'purchasing'); pl_require_book_ready($companyId);
        $prior = DB::queryFirstRow('SELECT * FROM pl_purchase_commands WHERE book_id = %i AND request_key = %s FOR UPDATE', $bookId, $key);
        if ($prior) {
            if (!hash_equals($prior['payload_hash'], $hash)) { throw new DomainException('This purchasing request key has different content.'); }
            return json_decode($prior['result_json'], true, 512, JSON_THROW_ON_ERROR);
        }
        return pl_demo_with_document_capacity($companyId, $bookId, function () use ($companyId, $bookId, $actorId, $action, $key, $hash, $work): array {
            $result = $work('purchasing:' . hash('sha256', $key));
            DB::insert('pl_purchase_commands', ['company_id' => $companyId, 'book_id' => $bookId, 'request_key' => $key, 'payload_hash' => $hash, 'action' => $action, 'result_json' => json_encode($result, JSON_THROW_ON_ERROR), 'actor_id' => $actorId]);
            return $result;
        });
    });
}

function pl_purchase_positive(mixed $value, string $label): string
{
    $amount = pl_amount(pl_ledger_text($value, $label, 21));
    if (bccomp($amount, '0', 4) <= 0) { throw new DomainException($label . ' must be positive.'); }
    return $amount;
}

function pl_purchase_rows(mixed $lines): array
{
    if (!is_array($lines) || !array_is_list($lines) || $lines === [] || count($lines) > 100) { throw new DomainException('Supply between one and 100 purchasing lines.'); }
    foreach ($lines as $line) { if (!is_array($line)) { throw new DomainException('Purchasing lines must be records.'); } }
    return $lines;
}

function pl_purchase_account(int $companyId, int $bookId, int $id, bool $variance = false): array
{
    $row = DB::queryFirstRow('SELECT * FROM pl_accounts WHERE id = %i AND company_id = %i AND book_id = %i FOR SHARE', $id, $companyId, $bookId);
    if (!$row || !(bool) $row['is_active'] || $row['currency'] !== null || !in_array($row['type'], $variance ? ['expense', 'income'] : ['liability'], true) || in_array($row['role'], ['cash_bank', 'receivables', 'payables'], true)) {
        throw new DomainException($variance ? 'Choose an active currency-neutral purchase variance income/expense account.' : 'Choose an active currency-neutral received-but-unbilled liability account, separate from AP.');
    }
    return $row;
}

function pl_purchase_vendor(int $companyId, int $partyId): array
{
    $row = DB::queryFirstRow('SELECT * FROM pl_parties WHERE id = %i AND company_id = %i FOR SHARE', $partyId, $companyId);
    if (!$row || !(bool) $row['is_vendor']) { throw new DomainException('Choose a supplier in this company.'); }
    return $row;
}

/** History remains readable when a company hides or disables Purchasing. */
function pl_get_purchase_order(int $actorId, int $companyId, int $bookId, int $id): array
{
    pl_require_company_access($actorId, $companyId); pl_ledger_book($companyId, $bookId);
    $order = DB::queryFirstRow('SELECT o.*, p.legal_name AS party_name FROM pl_purchase_orders o JOIN pl_parties p ON p.id = o.party_id WHERE o.id = %i AND o.company_id = %i AND o.book_id = %i FOR SHARE', $id, $companyId, $bookId);
    if (!$order) { throw new DomainException('Purchase order is not available in this book.'); }
    foreach (['id', 'company_id', 'book_id', 'party_id', 'revision'] as $field) { $order[$field] = (int) $order[$field]; }
    $order['number'] = 'PO-' . str_pad((string) $id, 6, '0', STR_PAD_LEFT);
    $order['lines'] = DB::query('SELECT * FROM pl_purchase_order_lines WHERE order_id = %i ORDER BY line_number FOR SHARE', $id);
    $order['total'] = '0.0000';
    foreach ($order['lines'] as &$line) {
        foreach (['id', 'product_id', 'line_number'] as $field) { $line[$field] = (int) $line[$field]; }
        $line['amount'] = pl_fx_convert($line['quantity'], $line['unit_price']);
        $line['received_quantity'] = '0.0000';
        foreach (DB::query('SELECT quantity FROM pl_purchase_receipt_lines WHERE order_line_id = %i FOR SHARE', $line['id']) as $receipt) { $line['received_quantity'] = bcadd($line['received_quantity'], $receipt['quantity'], 4); }
        $line['remaining_quantity'] = bcsub($line['quantity'], $line['received_quantity'], 4);
        $order['total'] = bcadd($order['total'], $line['amount'], 4);
    }
    unset($line);
    return $order;
}

function pl_list_purchase_orders(int $actorId, int $companyId, int $bookId): array
{
    pl_require_company_access($actorId, $companyId); pl_ledger_book($companyId, $bookId);
    $ids = DB::query('SELECT id FROM pl_purchase_orders WHERE company_id = %i AND book_id = %i ORDER BY id DESC LIMIT 200', $companyId, $bookId);
    return array_map(static fn(array $row): array => pl_get_purchase_order($actorId, $companyId, $bookId, (int) $row['id']), $ids);
}

function pl_save_purchase_order(int $actorId, int $companyId, int $bookId, array $input, ?int $id = null, ?int $expectedRevision = null): array
{
    $data = ['party_id' => pl_oi_id($input, 'party_id'), 'document_date' => pl_ledger_date(pl_ledger_text($input['date'] ?? null, 'Order date', 10)), 'currency' => pl_currency_code(pl_ledger_text($input['currency'] ?? null, 'Currency', 3)), 'reference' => pl_ledger_text($input['reference'] ?? '', 'Reference', 120, false), 'notes' => pl_ledger_text($input['notes'] ?? '', 'Notes', 2000, false)];
    $lines = []; $seen = [];
    foreach (pl_purchase_rows($input['lines'] ?? null) as $line) {
        $productId = pl_oi_id($line, 'product_id');
        if (isset($seen[$productId])) { throw new DomainException('Use each product once per purchase order.'); }
        $seen[$productId] = true;
        $quantity = pl_purchase_positive($line['quantity'] ?? null, 'Quantity'); $price = pl_purchase_positive($line['unit_price'] ?? null, 'Unit price');
        pl_purchase_positive(pl_fx_convert($quantity, $price), 'Line amount');
        $lines[] = ['line_number' => count($lines) + 1, 'product_id' => $productId, 'description' => pl_ledger_text($line['description'] ?? '', 'Description', 300, false), 'quantity' => $quantity, 'unit_price' => $price];
    }
    $key = pl_request_key(pl_ledger_text($input['creation_key'] ?? null, 'Creation key', 128));
    return pl_purchase_command($actorId, $companyId, $bookId, 'save_order', $key, [$id, $expectedRevision, $data, $lines], function () use ($actorId, $companyId, $bookId, $id, $expectedRevision, $data, $lines, $key): array {
        pl_purchase_vendor($companyId, $data['party_id']);
        foreach ($lines as $line) {
            $product = pl_get_inventory_product($actorId, $companyId, $bookId, $line['product_id']);
            if ($product['kind'] !== 'stock' || !(bool) ($product['is_active'] ?? true)) { throw new DomainException('Purchase receipts require active stock products; enter service bills directly in AP.'); }
        }
        $existingLines = [];
        if ($id === null) {
            DB::insert('pl_purchase_orders', $data + ['company_id' => $companyId, 'book_id' => $bookId, 'created_by' => $actorId, 'creation_key' => $key, 'creation_hash' => hash('sha256', json_encode([$data, $lines], JSON_THROW_ON_ERROR))]);
            $id = (int) DB::insertId();
        } else {
            $order = pl_get_purchase_order($actorId, $companyId, $bookId, $id);
            if ($order['status'] !== 'draft' || $expectedRevision !== $order['revision']) { throw new DomainException('Only the current purchase order draft can be edited.'); }
            if (pl_demo_enabled()) {
                $existingLines = $order['lines'];
                if (count($lines) < count($existingLines)) { throw new DomainException('The public sample cannot remove draft lines. Start a new order with the required lines.'); }
            } else { DB::delete('pl_purchase_order_lines', 'order_id = %i', $id); }
            DB::update('pl_purchase_orders', $data + ['revision' => $order['revision'] + 1], 'id = %i', $id);
        }
        foreach ($lines as $index => $line) {
            if (isset($existingLines[$index])) { DB::update('pl_purchase_order_lines', $line, 'id=%i AND order_id=%i AND company_id=%i AND book_id=%i', $existingLines[$index]['id'], $id, $companyId, $bookId); }
            else { DB::insert('pl_purchase_order_lines', $line + ['company_id' => $companyId, 'book_id' => $bookId, 'order_id' => $id]); }
        }
        return pl_get_purchase_order($actorId, $companyId, $bookId, $id);
    });
}

function pl_confirm_purchase_order(int $actorId, int $companyId, int $bookId, int $id, int $revision, string $key): array
{
    return pl_purchase_command($actorId, $companyId, $bookId, 'confirm_order', $key, [$id, $revision], function () use ($actorId, $companyId, $bookId, $id, $revision): array {
        $order = pl_get_purchase_order($actorId, $companyId, $bookId, $id);
        if ($order['status'] !== 'draft' || $order['revision'] !== $revision) { throw new DomainException('Review the current purchase order draft before confirming it.'); }
        pl_purchase_vendor($companyId, $order['party_id']);
        DB::update('pl_purchase_orders', ['status' => 'confirmed', 'revision' => $revision + 1], 'id = %i', $id);
        return pl_get_purchase_order($actorId, $companyId, $bookId, $id);
    });
}

function pl_cancel_purchase_order(int $actorId, int $companyId, int $bookId, int $id, int $revision, string $reason, string $key): array
{
    $reason = pl_ledger_text($reason, 'Cancellation reason', 500);
    return pl_purchase_command($actorId, $companyId, $bookId, 'cancel_order', $key, [$id, $revision, $reason], function () use ($actorId, $companyId, $bookId, $id, $revision, $reason): array {
        $order = pl_get_purchase_order($actorId, $companyId, $bookId, $id);
        if ($order['status'] === 'cancelled' || $order['revision'] !== $revision) { throw new DomainException('Review the current order before cancelling unreceived quantities.'); }
        DB::update('pl_purchase_orders', ['status' => 'cancelled', 'revision' => $revision + 1], 'id = %i', $id);
        return pl_get_purchase_order($actorId, $companyId, $bookId, $id) + ['cancellation_reason' => $reason];
    });
}

/** Shared read-only receipt quantities and valuation; callers hold the book lock. */
function pl_purchase_receipt_plan(int $actorId, int $companyId, int $bookId, int $orderId, array $input): array
{
    $date=pl_ledger_date(pl_ledger_text($input['date']??null,'Receipt date',10));
    $grni=pl_oi_id($input,'grni_account_id'); $rows=pl_purchase_rows($input['lines']??null);
    $order=pl_get_purchase_order($actorId,$companyId,$bookId,$orderId);
    if ($order['status']!=='confirmed' || $date<$order['document_date']) { throw new DomainException('Receive a confirmed purchase order on or after its order date.'); }
    pl_purchase_account($companyId,$bookId,$grni);
    $snapshot=pl_oi_rate($actorId,$companyId,$bookId,$order['currency'],$date,$input['rate']??null,null,'spot');
    $byId=[]; foreach ($order['lines'] as $line) { $byId[$line['id']]=$line; }
    $lines=[]; $seen=[]; $total='0.0000';
    foreach ($rows as $row) {
        $lineId=pl_oi_id($row,'order_line_id'); $quantity=pl_purchase_positive($row['quantity']??null,'Received quantity');
        if (!isset($byId[$lineId]) || isset($seen[$lineId])) { throw new DomainException('Select each order line once from this order.'); }
        $seen[$lineId]=true; $line=$byId[$lineId];
        if (bccomp($quantity,$line['remaining_quantity'],4)>0) { throw new DomainException('Received quantity exceeds the unreceived order quantity.'); }
        $fc=pl_fx_convert($quantity,$line['unit_price']); $base=pl_fx_convert($fc,$snapshot['rate']);
        $lines[]=['order_line_id'=>$lineId,'product_id'=>$line['product_id'],'description'=>$line['description'],'quantity'=>$quantity,
            'ordered_quantity'=>$line['quantity'],'received_quantity'=>$line['received_quantity'],'remaining_quantity'=>$line['remaining_quantity'],
            'remaining_after'=>bcsub($line['remaining_quantity'],$quantity,4),'unit_price'=>$line['unit_price'],'amount_fc'=>$fc,'amount_base'=>$base];
        $total=bcadd($total,$base,4);
    }
    return ['order'=>$order,'date'=>$date,'grni_account_id'=>$grni,'rate_snapshot'=>$snapshot,'lines'=>$lines,'total_base'=>$total];
}

function pl_receive_purchase_order(int $actorId, int $companyId, int $bookId, int $orderId, array $input): array
{
    $key=pl_ledger_text($input['idempotency_key']??null,'Receipt key',128);
    return pl_purchase_command($actorId,$companyId,$bookId,'receive',$key,[$orderId,$input],function (string $command) use ($actorId,$companyId,$bookId,$orderId,$input): array {
        $plan=pl_purchase_receipt_plan($actorId,$companyId,$bookId,$orderId,$input);
        $order=$plan['order']; $date=$plan['date']; $grni=$plan['grni_account_id']; $snapshot=$plan['rate_snapshot'];
        DB::insert('pl_purchase_receipts',['company_id'=>$companyId,'book_id'=>$bookId,'order_id'=>$orderId,'document_date'=>$date,'grni_account_id'=>$grni,'currency'=>$order['currency'],'rate'=>$snapshot['rate'],'rate_snapshot'=>json_encode($snapshot,JSON_THROW_ON_ERROR),'actor_id'=>$actorId]);
        $receiptId=(int)DB::insertId(); $receiptLines=[];
        foreach ($plan['lines'] as $line) {
            $lineId=$line['order_line_id']; $quantity=$line['quantity']; $base=$line['amount_base'];
            $movement=pl_inventory_receive($actorId,$companyId,$bookId,['product_id'=>$line['product_id'],'quantity'=>$quantity,'amount_base'=>$base,'date'=>$date,'offset_account_id'=>$grni,'source_type'=>'purchase_receipt','source_reference'=>'purchase-receipt:'.$receiptId.':'.$lineId,'idempotency_key'=>$command.':line:'.$lineId,'reason'=>'Goods received against '.$order['number']]);
            DB::insert('pl_purchase_receipt_lines',['company_id'=>$companyId,'book_id'=>$bookId,'receipt_id'=>$receiptId,'order_line_id'=>$lineId,'movement_id'=>$movement['movement_id'],'journal_id'=>$movement['journal_id'],'quantity'=>$quantity,'amount_fc'=>$line['amount_fc'],'amount_base'=>$base]);
            $receiptLines[]=['id'=>(int)DB::insertId(),'order_line_id'=>$lineId,'quantity'=>$quantity,'amount_base'=>$base,'movement_id'=>$movement['movement_id'],'journal_id'=>$movement['journal_id']];
        }
        return ['receipt_id'=>$receiptId,'order_id'=>$orderId,'lines'=>$receiptLines];
    });
}

function pl_preview_purchase_receipt(int $actorId, int $companyId, int $bookId, int $orderId, array $input): array
{
    return pl_ledger_transaction(function () use ($actorId,$companyId,$bookId,$orderId,$input): array {
        pl_require_company_access($actorId,$companyId,true); pl_ledger_book($companyId,$bookId,true);
        pl_require_module($actorId,$companyId,$bookId,'purchasing');
        $plan=pl_purchase_receipt_plan($actorId,$companyId,$bookId,$orderId,$input);
        foreach ($plan['lines'] as &$line) {
            if (bccomp($line['amount_base'],'0',4)<=0) { throw new DomainException('A receipt needs positive quantity and carrying value.'); }
            $data=pl_inventory_movement_input(['product_id'=>$line['product_id'],'date'=>$plan['date'],'offset_account_id'=>$plan['grni_account_id'],
                'source_type'=>'purchase_receipt','source_reference'=>'receipt-preview:'.$orderId.':'.$line['order_line_id'],'reason'=>'Goods received against '.$plan['order']['number']]);
            $line['stock']=pl_inventory_movement_plan($actorId,$companyId,$bookId,$data,$line['quantity'],$line['amount_base']);
        } unset($line);
        return $plan;
    });
}

function pl_confirm_purchase_receipt(int $actorId, int $companyId, int $bookId, int $orderId, array $input, string $expectedHash): array
{
    return pl_ledger_transaction(function () use ($actorId,$companyId,$bookId,$orderId,$input,$expectedHash): array {
        pl_require_company_access($actorId,$companyId,true); pl_ledger_book($companyId,$bookId,true);
        $key=pl_request_key(pl_ledger_text($input['idempotency_key']??null,'Receipt key',128));
        if (!DB::queryFirstField('SELECT request_key FROM pl_purchase_commands WHERE company_id=%i AND book_id=%i AND request_key=%s',$companyId,$bookId,$key)) {
            $plan=pl_preview_purchase_receipt($actorId,$companyId,$bookId,$orderId,$input);
            if (!hash_equals($expectedHash,hash('sha256',json_encode($plan,JSON_THROW_ON_ERROR)))) { throw new DomainException('The receipt or its order/stock balances changed. Update the preview before confirming.'); }
        }
        return pl_receive_purchase_order($actorId,$companyId,$bookId,$orderId,$input);
    });
}

function pl_purchase_receipt_line(int $companyId, int $bookId, int $lineId): array
{
    $line = DB::queryFirstRow('SELECT l.*, r.document_date, r.grni_account_id, r.currency, r.rate, r.order_id, o.party_id, ol.product_id, ol.unit_price, ol.description FROM pl_purchase_receipt_lines l JOIN pl_purchase_receipts r ON r.id = l.receipt_id JOIN pl_purchase_orders o ON o.id = r.order_id JOIN pl_purchase_order_lines ol ON ol.id = l.order_line_id WHERE l.id = %i AND l.company_id = %i AND l.book_id = %i FOR SHARE', $lineId, $companyId, $bookId);
    if (!$line) { throw new DomainException('Receipt line is not available in this book.'); }
    $matches = DB::query('SELECT * FROM pl_purchase_bill_matches WHERE receipt_line_id = %i ORDER BY id FOR SHARE', $lineId);
    $returns = DB::query('SELECT * FROM pl_purchase_returns WHERE receipt_line_id = %i ORDER BY id FOR SHARE', $lineId);
    $used = '0.0000'; $usedBase = '0.0000'; $returned = '0.0000';
    foreach ($matches as $match) { $used = bcadd($used, $match['quantity'], 4); $usedBase = bcadd($usedBase, $match['receipt_basis_base'], 4); }
    foreach ($returns as $return) {
        $returned = bcadd($returned, $return['quantity'], 4);
        if ($return['match_id'] === null) { $used = bcadd($used, $return['quantity'], 4); $usedBase = bcadd($usedBase, $return['receipt_basis_base'], 4); }
    }
    return $line + ['matches' => $matches, 'returns' => $returns, 'used_quantity' => $used, 'used_basis_base' => $usedBase, 'unbilled_quantity' => bcsub($line['quantity'], $used, 4), 'unbilled_base' => bcsub($line['amount_base'], $usedBase, 4), 'returned_quantity' => $returned];
}

/** Dr variance / Cr GRNI for a higher supplier bill; negative differences reverse those sides. */
function pl_purchase_variance(int $actorId, int $companyId, int $bookId, string $amount, string $date, int $grni, int $varianceAccount, string $key, string $description): ?int
{
    if (bccomp($amount, '0', 4) === 0) { return null; }
    pl_purchase_account($companyId, $bookId, $varianceAccount, true);
    $positive = bccomp($amount, '0', 4) > 0;
    $value = $positive ? $amount : bcsub('0', $amount, 4);
    $book = pl_ledger_book($companyId, $bookId);
    $journal = pl_post_journal($actorId, $companyId, $bookId, ['date' => $date, 'currency' => $book['currency'], 'source_type' => 'purchase_variance', 'source_reference' => $key, 'idempotency_key' => $key, 'description' => $description, 'lines' => [
        ['account_id' => $varianceAccount, 'debit' => $positive ? $value : '0', 'credit' => $positive ? '0' : $value, 'description' => $description],
        ['account_id' => $grni, 'debit' => $positive ? '0' : $value, 'credit' => $positive ? $value : '0', 'description' => $description],
    ]]);
    return (int) $journal['id'];
}

/** Non-posting browser review; confirmation rechecks quantities and the same rate/tax snapshot. */
function pl_preview_purchase_bill(int $actorId, int $companyId, int $bookId, array $input): array
{
    return pl_ledger_transaction(function () use ($actorId, $companyId, $bookId, $input): array {
        pl_ledger_book($companyId, $bookId, true); pl_require_module($actorId, $companyId, $bookId, 'purchasing');
        $partyId = pl_oi_id($input, 'party_id'); $grni = pl_oi_id($input, 'grni_account_id');
        $party = pl_purchase_vendor($companyId, $partyId); pl_purchase_account($companyId, $bookId, $grni);
        $date = pl_ledger_date(pl_ledger_text($input['date'] ?? null, 'Bill date', 10));
        $due = pl_ledger_date(pl_ledger_text($input['due_date'] ?? null, 'Due date', 10));
        if ($due < $date) { throw new DomainException('Due date cannot precede the bill date.'); }
        $currency = pl_currency_code(pl_ledger_text($input['currency'] ?? null, 'Currency', 3));
        $snapshot = pl_oi_rate($actorId, $companyId, $bookId, $currency, $date, $input['rate'] ?? null, null, 'spot');
        $priceMode = $input['price_mode'] ?? pl_tax_price_mode($actorId, $companyId, $bookId);
        if (!in_array($priceMode, ['exclusive', 'inclusive'], true)) { throw new DomainException('Choose tax-exclusive or tax-inclusive bill prices.'); }
        $lines = []; $seen = []; $net = '0.0000'; $tax = '0.0000'; $variance = '0.0000'; $varianceRequired = false;
        foreach (pl_purchase_rows($input['lines'] ?? null) as $row) {
            $id = pl_oi_id($row, 'receipt_line_id');
            if (isset($seen[$id])) { throw new DomainException('Match each receipt line once per bill.'); }
            $seen[$id] = true; $receipt = pl_purchase_receipt_line($companyId, $bookId, $id);
            $quantity = pl_purchase_positive($row['quantity'] ?? null, 'Billed quantity'); $price = pl_purchase_positive($row['unit_price'] ?? null, 'Billed unit price');
            if ((int) $receipt['party_id'] !== $partyId || (int) $receipt['grni_account_id'] !== $grni || $receipt['currency'] !== $currency || $date < $receipt['document_date']) { throw new DomainException('Select receipts for this supplier, currency and clearing account on or after receipt.'); }
            if (bccomp($quantity, $receipt['unbilled_quantity'], 4) > 0) { throw new DomainException('Billed quantity exceeds the received, unbilled quantity.'); }
            $basis = pl_inventory_cost($receipt['unbilled_base'], $receipt['unbilled_quantity'], $quantity);
            $entered = pl_fx_convert($quantity, $price);
            $taxCodeId = isset($row['tax_code_id']) ? pl_oi_id($row, 'tax_code_id') : null;
            $calculated = pl_tax_calculate($actorId, $companyId, $bookId, $taxCodeId, $date, $entered, 'purchase');
            $split = pl_tax_split($entered, $calculated['tax_rate'], $priceMode);
            $fc = $split['net']; $calculated['tax_amount'] = $split['tax']; $base = pl_fx_convert($fc, $snapshot['rate']);
            $difference = bcsub($base, $basis, 4); $varianceRequired = $varianceRequired || bccomp($difference, '0', 4) !== 0;
            $net = bcadd($net, $fc, 4); $tax = bcadd($tax, $calculated['tax_amount'], 4); $variance = bcadd($variance, $difference, 4);
            $lines[] = ['receipt_line_id' => $id, 'description' => $receipt['description'], 'quantity' => $quantity, 'unit_price' => $price, 'net_fc' => $fc, 'net_base' => $base, 'receipt_basis_base' => $basis, 'available_quantity' => $receipt['unbilled_quantity'], 'variance_base' => $difference, 'tax' => $calculated];
        }
        if ($varianceRequired) {
            if (($input['variance_confirmed'] ?? false) !== true) { throw new DomainException('Review price/rate differences and select a purchase variance account.'); }
            pl_purchase_account($companyId, $bookId, pl_oi_id($input, 'variance_account_id'), true);
        }
        unset($input['expected_hash']);
        $payload = ['input' => $input, 'price_mode' => $priceMode, 'supplier' => $party['legal_name'], 'rate_snapshot' => $snapshot, 'lines' => $lines, 'net_fc' => $net, 'tax_fc' => $tax, 'total_fc' => bcadd($net, $tax, 4), 'variance_base' => $variance];
        return $payload + ['payload_hash' => hash('sha256', json_encode($payload, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE))];
    });
}

function pl_bill_purchase_receipts(int $actorId, int $companyId, int $bookId, array $input): array
{
    $partyId = pl_oi_id($input, 'party_id'); $grni = pl_oi_id($input, 'grni_account_id');
    $date = pl_ledger_date(pl_ledger_text($input['date'] ?? null, 'Bill date', 10));
    $currency = pl_currency_code(pl_ledger_text($input['currency'] ?? null, 'Currency', 3));
    $rows = pl_purchase_rows($input['lines'] ?? null);
    return pl_purchase_command($actorId, $companyId, $bookId, 'bill_receipts', pl_ledger_text($input['idempotency_key'] ?? null, 'Bill key', 128), $input, function (string $command) use ($actorId, $companyId, $bookId, $input, $partyId, $grni, $date, $currency, $rows): array {
        if (isset($input['expected_hash'])) {
            $review = pl_preview_purchase_bill($actorId, $companyId, $bookId, $input);
            if (!hash_equals($review['payload_hash'], pl_ledger_text($input['expected_hash'], 'Review identity', 64))) { throw new DomainException('The bill review changed. Preview it again before posting.'); }
        }
        pl_purchase_vendor($companyId, $partyId); pl_purchase_account($companyId, $bookId, $grni);
        $snapshot = pl_oi_rate($actorId, $companyId, $bookId, $currency, $date, $input['rate'] ?? null, null, 'spot');
        $priceMode = $input['price_mode'] ?? pl_tax_price_mode($actorId, $companyId, $bookId);
        if (!in_array($priceMode, ['exclusive', 'inclusive'], true)) { throw new DomainException('Choose tax-exclusive or tax-inclusive bill prices.'); }
        $billLines = []; $matches = []; $seen = []; $varianceRequired = false;
        foreach ($rows as $row) {
            $id = pl_oi_id($row, 'receipt_line_id');
            if (isset($seen[$id])) { throw new DomainException('Match each receipt line once per bill.'); }
            $seen[$id] = true; $receipt = pl_purchase_receipt_line($companyId, $bookId, $id);
            $quantity = pl_purchase_positive($row['quantity'] ?? null, 'Billed quantity'); $price = pl_purchase_positive($row['unit_price'] ?? null, 'Billed unit price');
            if ((int) $receipt['party_id'] !== $partyId || (int) $receipt['grni_account_id'] !== $grni || $receipt['currency'] !== $currency || $date < $receipt['document_date']) { throw new DomainException('Match receipts for this supplier, currency and clearing account on or after receipt.'); }
            if (bccomp($quantity, $receipt['unbilled_quantity'], 4) > 0) { throw new DomainException('Billed quantity exceeds the received, unbilled quantity.'); }
            $basis = pl_inventory_cost($receipt['unbilled_base'], $receipt['unbilled_quantity'], $quantity);
            $entered = pl_fx_convert($quantity, $price);
            $calculated = pl_tax_calculate($actorId, $companyId, $bookId, isset($row['tax_code_id']) ? pl_oi_id($row, 'tax_code_id') : null, $date, $entered, 'purchase');
            $net = pl_tax_split($entered, $calculated['tax_rate'], $priceMode)['net'];
            $actual = pl_fx_convert($net, $snapshot['rate']);
            if (bccomp($actual, $basis, 4) !== 0) { $varianceRequired = true; }
            $billLines[] = ['description' => $receipt['description'] ?: 'Received goods', 'quantity' => $quantity, 'unit_price' => $price, 'account_id' => $grni, 'tax_code_id' => isset($row['tax_code_id']) ? pl_oi_id($row, 'tax_code_id') : null];
            $matches[] = ['receipt_line_id' => $id, 'bill_line_number' => count($billLines), 'quantity' => $quantity, 'unit_price' => $price, 'receipt_basis_base' => $basis, 'bill_amount_base' => $actual];
        }
        $varianceAccount = null;
        if ($varianceRequired) {
            if (($input['variance_confirmed'] ?? false) !== true) { throw new DomainException('Explicitly review and confirm price/rate differences before posting this bill.'); }
            $varianceAccount = pl_oi_id($input, 'variance_account_id'); pl_purchase_account($companyId, $bookId, $varianceAccount, true);
        }
        $bill = pl_save_ar_document($actorId, $companyId, $bookId, ['kind' => 'bill', 'date' => $date, 'due_date' => $input['due_date'] ?? null, 'currency' => $currency, 'party_id' => $partyId, 'creation_key' => $command . ':bill', 'reference' => $input['reference'] ?? '', 'lines' => $billLines, 'price_mode' => $priceMode, 'rounding_account_id' => $input['rounding_account_id'] ?? null]);
        $posted = pl_post_ar_document($actorId, $companyId, $bookId, (int) $bill['id'], (int) $bill['revision'], $grni, $snapshot['rate']);
        $matchIds = [];
        foreach ($matches as $match) {
            $variance = bcsub($match['bill_amount_base'], $match['receipt_basis_base'], 4);
            $journalId = bccomp($variance, '0', 4) === 0 ? null : pl_purchase_variance($actorId, $companyId, $bookId, $variance, $date, $grni, (int) $varianceAccount, $command . ':variance:' . $match['bill_line_number'], 'Reviewed purchase price/rate variance');
            DB::insert('pl_purchase_bill_matches', $match + ['company_id' => $companyId, 'book_id' => $bookId, 'bill_document_id' => $bill['id'], 'variance_journal_id' => $journalId, 'variance_account_id' => $varianceAccount, 'actor_id' => $actorId]);
            $matchIds[] = (int) DB::insertId();
        }
        return ['bill_document_id' => (int) $bill['id'], 'posted' => $posted, 'match_ids' => $matchIds];
    });
}

function pl_return_purchase_receipt(int $actorId, int $companyId, int $bookId, array $input): array
{
    $lineId = pl_oi_id($input, 'receipt_line_id'); $quantity = pl_purchase_positive($input['quantity'] ?? null, 'Return quantity');
    $date = pl_ledger_date(pl_ledger_text($input['date'] ?? null, 'Return date', 10)); $reason = pl_ledger_text($input['reason'] ?? null, 'Return reason', 500);
    $matchId = isset($input['match_id']) ? pl_oi_id($input, 'match_id') : null;
    return pl_purchase_command($actorId, $companyId, $bookId, 'return_receipt', pl_ledger_text($input['idempotency_key'] ?? null, 'Return key', 128), $input, function (string $command) use ($actorId, $companyId, $bookId, $lineId, $quantity, $date, $reason, $matchId, $input): array {
        $receipt = pl_purchase_receipt_line($companyId, $bookId, $lineId);
        if ($date < $receipt['document_date']) { throw new DomainException('A return cannot precede its receipt.'); }
        $grni = (int) $receipt['grni_account_id']; $creditId = null; $varianceJournal = null; $creditBase = null; $varianceAccount = null; $unbilledBasis = null;
        if (($input['variance_confirmed'] ?? false) === true) { $varianceAccount = pl_oi_id($input, 'variance_account_id'); }
        if ($matchId === null) {
            if (bccomp($quantity, $receipt['unbilled_quantity'], 4) > 0) { throw new DomainException('Unbilled return exceeds unbilled stock; select a matched bill for a supplier credit.'); }
            $unbilledBasis = pl_inventory_cost($receipt['unbilled_base'], $receipt['unbilled_quantity'], $quantity);
        } else {
            $match = null;
            foreach ($receipt['matches'] as $candidate) { if ((int) $candidate['id'] === $matchId) { $match = $candidate; break; } }
            if (!$match) { throw new DomainException('Select a bill match belonging to this receipt line.'); }
            $returned = '0.0000'; foreach ($receipt['returns'] as $return) { if ((int) ($return['match_id'] ?? 0) === $matchId) { $returned = bcadd($returned, $return['quantity'], 4); } }
            if (bccomp(bcadd($returned, $quantity, 4), $match['quantity'], 4) > 0) { throw new DomainException('Return exceeds the remaining matched quantity.'); }
            $bill = pl_get_ar_document($actorId, $companyId, $bookId, (int) $match['bill_document_id']);
            $credit = pl_save_ar_document($actorId, $companyId, $bookId, ['kind' => 'supplier_credit', 'date' => $date, 'due_date' => $date, 'currency' => $receipt['currency'], 'party_id' => (int) $receipt['party_id'], 'original_document_id' => (int) $bill['id'], 'creation_key' => $command . ':credit', 'reference' => 'Return of receipt line ' . $lineId, 'notes' => $reason, 'rounding_account_id' => $input['rounding_account_id'] ?? null, 'lines' => [['description' => 'Returned goods', 'quantity' => $quantity, 'unit_price' => $match['unit_price'], 'account_id' => $grni, 'original_line_number' => (int) $match['bill_line_number']]]]);
            pl_post_ar_document($actorId, $companyId, $bookId, (int) $credit['id'], (int) $credit['revision'], $grni);
            $creditId = (int) $credit['id'];
            $creditPosted = pl_get_ar_document($actorId, $companyId, $bookId, $creditId);
            $creditBase = (string) DB::queryFirstField('SELECT COALESCE(SUM(credit),0) FROM pl_journal_lines WHERE journal_id = %i AND account_id = %i', $creditPosted['journal_id'], $grni);
            $varianceAccount = $match['variance_account_id'] === null ? $varianceAccount : (int) $match['variance_account_id'];
        }
        $movement = pl_inventory_return($actorId, $companyId, $bookId, ['original_movement_id' => (int) $receipt['movement_id'], 'quantity' => $quantity, 'date' => $date, 'offset_account_id' => $grni, 'source_type' => 'purchase_return', 'source_reference' => $command, 'idempotency_key' => $command . ':stock', 'reason' => $reason]);
        $basis = (string) $movement['value_base'];
        $variance = bcsub($basis, $matchId === null ? $unbilledBasis : $creditBase, 4);
        if (bccomp($variance, '0', 4) !== 0) {
            if ($varianceAccount === null) { throw new DomainException('This return has a rounding/carrying difference; explicitly review a variance account before returning it.'); }
            $varianceJournal = pl_purchase_variance($actorId, $companyId, $bookId, $variance, $date, $grni, $varianceAccount, $command . ':variance', 'Reviewed purchase return carrying difference');
        }
        if ($matchId === null) { $basis = $unbilledBasis; }
        DB::insert('pl_purchase_returns', ['company_id' => $companyId, 'book_id' => $bookId, 'receipt_line_id' => $lineId, 'match_id' => $matchId, 'credit_document_id' => $creditId, 'movement_id' => $movement['movement_id'], 'journal_id' => $movement['journal_id'], 'variance_journal_id' => $varianceJournal, 'document_date' => $date, 'quantity' => $quantity, 'receipt_basis_base' => $basis, 'reason' => $reason, 'actor_id' => $actorId]);
        return ['return_id' => (int) DB::insertId(), 'movement_id' => $movement['movement_id'], 'journal_id' => $movement['journal_id'], 'credit_document_id' => $creditId, 'variance_journal_id' => $varianceJournal];
    });
}

function pl_purchasing_receipts(int $actorId, int $companyId, int $bookId): array
{
    pl_require_company_access($actorId, $companyId); pl_ledger_book($companyId, $bookId);
    $ids = DB::query('SELECT id FROM pl_purchase_receipt_lines WHERE company_id = %i AND book_id = %i ORDER BY id DESC LIMIT 300', $companyId, $bookId);
    return array_map(static fn(array $row): array => pl_purchase_receipt_line($companyId, $bookId, (int) $row['id']), $ids);
}

/** A standalone reversal must not detach accounting from immutable receipt/bill matching. */
function pl_purchasing_assert_reversal_allowed(int $companyId, int $bookId, int $journalId): void
{
    $matched = DB::queryFirstField('SELECT m.id FROM pl_purchase_bill_matches m JOIN pl_ar_documents d ON d.id = m.bill_document_id WHERE m.company_id = %i AND m.book_id = %i AND (d.journal_id = %i OR m.variance_journal_id = %i) LIMIT 1 FOR SHARE', $companyId, $bookId, $journalId, $journalId);
    $returned = DB::queryFirstField('SELECT r.id FROM pl_purchase_returns r LEFT JOIN pl_ar_documents d ON d.id = r.credit_document_id WHERE r.company_id = %i AND r.book_id = %i AND (d.journal_id = %i OR r.variance_journal_id = %i) LIMIT 1 FOR SHARE', $companyId, $bookId, $journalId, $journalId);
    if ($matched || $returned) { throw new DomainException('Purchasing matched history requires a coordinated return and supplier credit; standalone reversal is unavailable.'); }
}

function pl_purchase_received_unbilled(int $actorId, int $companyId, int $bookId, ?string $asOf = null): array
{
    pl_require_company_access($actorId, $companyId); pl_ledger_book($companyId, $bookId);
    $asOf = pl_ledger_date($asOf ?? gmdate('Y-m-d')); $accounts = []; $details = [];
    $rows = DB::query('SELECT l.id, r.grni_account_id FROM pl_purchase_receipt_lines l JOIN pl_purchase_receipts r ON r.id = l.receipt_id WHERE l.company_id = %i AND l.book_id = %i AND r.document_date <= %s ORDER BY l.id', $companyId, $bookId, $asOf);
    foreach ($rows as $row) {
        $line = pl_purchase_receipt_line($companyId, $bookId, (int) $row['id']); $quantity = $line['quantity']; $basis = $line['amount_base'];
        foreach ($line['matches'] as $match) {
            $billDate = DB::queryFirstField('SELECT document_date FROM pl_ar_documents WHERE id = %i', $match['bill_document_id']);
            if ($billDate <= $asOf) { $quantity = bcsub($quantity, $match['quantity'], 4); $basis = bcsub($basis, $match['receipt_basis_base'], 4); }
        }
        foreach ($line['returns'] as $return) {
            if ($return['match_id'] === null && $return['document_date'] <= $asOf) { $quantity = bcsub($quantity, $return['quantity'], 4); $basis = bcsub($basis, $return['receipt_basis_base'], 4); }
        }
        $accountId = (int) $row['grni_account_id']; $accounts[$accountId] = bcadd($accounts[$accountId] ?? '0', $basis, 4);
        $details[] = ['receipt_line_id' => (int) $row['id'], 'account_id' => $accountId, 'quantity' => $quantity, 'amount_base' => $basis];
    }
    $controls = [];
    foreach ($accounts as $id => $basis) {
        $gl = (string) DB::queryFirstField('SELECT COALESCE(SUM(l.credit-l.debit),0) FROM pl_journal_lines l JOIN pl_journals j ON j.id = l.journal_id WHERE l.company_id = %i AND l.book_id = %i AND l.account_id = %i AND j.journal_date <= %s', $companyId, $bookId, $id, $asOf);
        $controls[] = ['account_id' => $id, 'unbilled_base' => $basis, 'ledger_base' => $gl, 'difference' => bcsub($gl, $basis, 4)];
    }
    return ['as_of' => $asOf, 'lines' => $details, 'accounts' => $controls];
}
