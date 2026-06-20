<?php

namespace App\Repositories;

use App\Models\KartuKeluarga;

class KartuKeluargaRepository
{
    public function getPaginatedList(array $filters = [], int $perPage = 10)
    {
        $query = KartuKeluarga::query();

        if (!empty($filters['rt_code'])) {
            $query->where('rt_code', $filters['rt_code']);
        }
        
        if (!empty($filters['search'])) {
            $query->where('no_kk', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('alamat_lengkap', 'like', '%' . $filters['search'] . '%');
        }

        return $query->paginate($perPage);
    }

    public function getByNoKkWithRelations(string $no_kk)
    {
        return KartuKeluarga::with('anggotaKeluargas')->where('no_kk', $no_kk)->firstOrFail();
    }
}
