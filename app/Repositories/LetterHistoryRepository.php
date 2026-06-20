<?php

namespace App\Repositories;

use App\Models\LetterStatusHistory;
use Illuminate\Database\Eloquent\Collection;

class LetterHistoryRepository
{
    public function getHistoriesByLetterId(int $pengajuanId): Collection
    {
        return LetterStatusHistory::with('actor')
            ->where('pengajuan_id', $pengajuanId)
            ->orderBy('changed_at', 'desc')
            ->get();
    }
}
