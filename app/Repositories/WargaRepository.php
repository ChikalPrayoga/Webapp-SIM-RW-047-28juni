<?php

namespace App\Repositories;

use App\Models\AnggotaKeluarga;

class WargaRepository
{
    public function getPaginatedList(array $filters = [], int $perPage = 10)
    {
        $query = AnggotaKeluarga::query()->with('kartuKeluarga');

        if (!empty($filters['search'])) {
            $query->where('nik', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('nama_lengkap', 'like', '%' . $filters['search'] . '%');
        }

        if (!empty($filters['rt_code'])) {
            $query->whereHas('kartuKeluarga', function ($q) use ($filters) {
                $q->where('rt_code', $filters['rt_code']);
            });
        }

        return $query->paginate($perPage);
    }

    public function getByNikWithRelations(string $nik)
    {
        return AnggotaKeluarga::with(['kartuKeluarga', 'changeRequests'])->where('nik', $nik)->firstOrFail();
    }
}
