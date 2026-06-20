<?php

namespace App\Repositories;

use App\Models\LogLaporanAspirasi;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class ComplaintRepository
{
    public function getBaseQuery(): Builder
    {
        return LogLaporanAspirasi::with(['pelapor', 'assignments.assignedTo', 'attachments']);
    }

    public function getAllPaginated(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = $this->getBaseQuery();

        if (isset($filters['status']) && $filters['status']) {
            $query->where('current_status', $filters['status']);
        }

        if (isset($filters['category']) && $filters['category']) {
            $query->where('ai_category', $filters['category']);
        }

        if (isset($filters['search']) && $filters['search']) {
            $query->where('teks_keluhan', 'like', '%' . $filters['search'] . '%');
        }

        // Scope by NIK for residents
        if (isset($filters['nik']) && $filters['nik']) {
            $query->where('nik', $filters['nik']);
        }

        return $query->orderBy('submitted_at', 'desc')->paginate($perPage);
    }

    public function findById($id): ?LogLaporanAspirasi
    {
        return $this->getBaseQuery()->where('aspirasi_id', $id)->first();
    }
}
