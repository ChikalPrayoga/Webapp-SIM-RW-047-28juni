<?php

namespace App\Repositories;

use App\Models\ComplaintStatusHistory;
use Illuminate\Support\Collection;

class ComplaintHistoryRepository
{
    public function getHistoriesByComplaintId($aspirasiId): Collection
    {
        return ComplaintStatusHistory::with('actor')
            ->where('aspirasi_id', $aspirasiId)
            ->orderBy('changed_at', 'asc')
            ->get();
    }
}
