<?php

namespace App\Repositories;

use App\Models\PengajuanSurat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class LetterRepository
{
    public function getBaseQuery(): Builder
    {
        return PengajuanSurat::with(['pemohon.kartuKeluarga']);
    }

    public function getAllPaginated(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = $this->getBaseQuery();

        if (isset($filters['status']) && $filters['status']) {
            $query->where('current_status', $filters['status']);
        }

        // Scope by RT code for Ketua RT
        if (isset($filters['rt_code']) && $filters['rt_code']) {
            $query->whereHas('pemohon.kartuKeluarga', function ($q) use ($filters) {
                $q->where('rt_code', $filters['rt_code']);
            });
        }

        // Scope by minimum status (for RW, e.g. status != SUBMITTED, but typically handled via Policy/Controller logic)
        if (isset($filters['min_status_not']) && $filters['min_status_not']) {
            $query->where('current_status', '!=', $filters['min_status_not']);
        }

        if (isset($filters['search']) && $filters['search']) {
            $query->where(function($q) use ($filters) {
                $q->where('nomor_surat', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('keperluan', 'like', '%' . $filters['search'] . '%');
            });
        }

        // Scope by NIK for residents
        if (isset($filters['nik']) && $filters['nik']) {
            $query->where('nik', $filters['nik']);
        }

        return $query->orderBy('tanggal_pengajuan', 'desc')->paginate($perPage);
    }

    public function findById($id): ?PengajuanSurat
    {
        return $this->getBaseQuery()->where('pengajuan_id', $id)->first();
    }
}
