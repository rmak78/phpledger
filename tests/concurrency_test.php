<?php
declare(strict_types=1);

/** Start separate PHP/database connections behind a shared barrier. */
function ledger_race(array $jobs): array
{
    $directory = sys_get_temp_dir() . '/phpledger-race-' . bin2hex(random_bytes(10));
    if (!mkdir($directory, 0700)) {
        throw new RuntimeException('Cannot create the concurrency fixture.');
    }
    $barrier = $directory . '/start';
    $processes = [];
    try {
        foreach ($jobs as $index => $job) {
            $path = $directory . '/' . $index . '.json';
            file_put_contents($path, json_encode($job + ['barrier' => $barrier], JSON_THROW_ON_ERROR));
            $pipes = [];
            $process = proc_open([PHP_BINARY, __DIR__ . '/concurrency_worker.php', $path], [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);
            if (!is_resource($process)) {
                throw new RuntimeException('Cannot start a concurrency worker.');
            }
            fclose($pipes[0]);
            stream_set_blocking($pipes[1], false);
            stream_set_blocking($pipes[2], false);
            $processes[] = ['process' => $process, 'out' => $pipes[1], 'err' => $pipes[2], 'stdout' => '', 'stderr' => '', 'exit' => null];
        }
        touch($barrier);
        $deadline = microtime(true) + 20;
        do {
            $running = false;
            foreach ($processes as &$entry) {
                if ($entry['exit'] !== null) {
                    continue;
                }
                $entry['stdout'] .= stream_get_contents($entry['out']);
                $entry['stderr'] .= stream_get_contents($entry['err']);
                $status = proc_get_status($entry['process']);
                if ($status['running']) {
                    $running = true;
                } else {
                    $entry['exit'] = $status['exitcode'];
                }
            }
            unset($entry);
            if ($running) {
                usleep(10000);
            }
        } while ($running && microtime(true) < $deadline);
        assert_true(!$running, 'Concurrent posting timed out.');
        $results = [];
        foreach ($processes as &$entry) {
            $entry['stdout'] .= stream_get_contents($entry['out']);
            $entry['stderr'] .= stream_get_contents($entry['err']);
            assert_same(0, $entry['exit'], $entry['stderr']);
            $results[] = json_decode($entry['stdout'], true, 512, JSON_THROW_ON_ERROR);
        }
        unset($entry);
        return $results;
    } finally {
        // An assertion can exit the by-reference loop before its normal unset.
        unset($entry);
        foreach ($processes as $entry) {
            if ($entry['exit'] === null && is_resource($entry['process'])) {
                proc_terminate($entry['process']);
            }
            if (is_resource($entry['out'])) {
                fclose($entry['out']);
            }
            if (is_resource($entry['err'])) {
                fclose($entry['err']);
            }
            if (is_resource($entry['process'])) {
                proc_close($entry['process']);
            }
        }
        foreach (glob($directory . '/*') ?: [] as $path) {
            unlink($path);
        }
        rmdir($directory);
    }
}

test('simultaneous retries create one journal across separate database connections', function (): void {
    $fixture = ledger_fixture();
    $payload = ledger_payload($fixture, '125.0000', 'same-concurrent-request');
    $job = ['mode' => 'post', 'fixture' => $fixture, 'payload' => $payload];
    $results = ledger_race([$job, $job]);
    assert_same($results[0]['id'], $results[1]['id']);
    assert_same(1, (int) DB::queryFirstField('SELECT COUNT(*) FROM pl_journals WHERE book_id=%i', $fixture['book_id']));
    $report = pl_trial_balance($fixture['actor_id'], $fixture['company_id'], $fixture['book_id']);
    assert_same('125.0000', $report['total_debit']);
    assert_true($report['balanced']);
});

test('simultaneous distinct postings both commit and reconcile', function (): void {
    $fixture = ledger_fixture();
    $results = ledger_race([
        ['mode' => 'post', 'fixture' => $fixture, 'payload' => ledger_payload($fixture, '0.1000', 'concurrent-a')],
        ['mode' => 'post', 'fixture' => $fixture, 'payload' => ledger_payload($fixture, '0.2000', 'concurrent-b')],
    ]);
    assert_true($results[0]['id'] !== $results[1]['id']);
    $report = pl_trial_balance($fixture['actor_id'], $fixture['company_id'], $fixture['book_id']);
    assert_same('0.3000', $report['total_debit']);
    assert_same('0.3000', $report['total_credit']);
});

test('simultaneous reversal retries create exactly one linked reversal', function (): void {
    $fixture = ledger_fixture();
    $journal = pl_post_journal($fixture['actor_id'], $fixture['company_id'], $fixture['book_id'], ledger_payload($fixture));
    $job = ['mode' => 'reverse', 'fixture' => $fixture, 'journal_id' => $journal['id'], 'key' => 'same-concurrent-reversal'];
    $results = ledger_race([$job, $job]);
    assert_same($results[0]['id'], $results[1]['id']);
    assert_same(1, (int) DB::queryFirstField('SELECT COUNT(*) FROM pl_journals WHERE reversal_of_id=%i', $journal['id']));
    assert_same('0.0000', pl_trial_balance($fixture['actor_id'], $fixture['company_id'], $fixture['book_id'])['total_debit']);
});

test('an older caller snapshot retrieves and retries another connection committed journal', function (): void {
    $fixture = ledger_fixture();
    $payload = ledger_payload($fixture, '11.0000', 'older-snapshot-request');
    DB::startTransaction();
    try {
        // This consistent read intentionally creates a snapshot before the child commits.
        DB::queryFirstField('SELECT COUNT(*) FROM pl_journals');
        $results = ledger_race([['mode' => 'post', 'fixture' => $fixture, 'payload' => $payload]]);
        assert_same($results[0]['id'], pl_get_journal($fixture['actor_id'], $fixture['company_id'], $fixture['book_id'], $results[0]['id'])['id']);
        assert_same($results[0]['id'], pl_post_journal($fixture['actor_id'], $fixture['company_id'], $fixture['book_id'], $payload)['id']);
    } finally {
        if (DB::transactionDepth() > 0) {
            DB::rollback();
        }
    }
    assert_same(1, (int) DB::queryFirstField('SELECT COUNT(*) FROM pl_journals WHERE book_id = %i', $fixture['book_id']));
});

test('four independent companies post concurrently without leaking deadlock or savepoint errors', function (): void {
    $jobs = [];
    for ($index = 0; $index < 4; $index++) {
        $fixture = ledger_fixture();
        $jobs[] = ['mode' => 'post', 'fixture' => $fixture, 'payload' => ledger_payload($fixture, '9.2500')];
    }
    $results = ledger_race($jobs);
    assert_same(4, count(array_unique(array_column($results, 'id'))));
    foreach ($jobs as $job) {
        $fixture = $job['fixture'];
        assert_same(1, (int) DB::queryFirstField('SELECT COUNT(*) FROM pl_journals WHERE book_id = %i', $fixture['book_id']));
        $report = pl_trial_balance($fixture['actor_id'], $fixture['company_id'], $fixture['book_id']);
        assert_true($report['balanced']);
        assert_same('9.2500', $report['total_debit']);
    }
});
