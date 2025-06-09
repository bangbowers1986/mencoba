<?php

namespace App\Services;

use App\Models\Transaction;

class SyncService
{
    public function processSync(array $transactions)
    {
        return Transaction::upsert(
            $transactions,
            ['local_id'],
            ['total', 'items', 'user_id']
        );
    }
}