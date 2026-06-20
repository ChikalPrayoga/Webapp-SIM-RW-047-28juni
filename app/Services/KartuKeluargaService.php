<?php

namespace App\Services;

use App\Models\KartuKeluarga;
use Illuminate\Support\Facades\DB;
use App\Models\AuditLog;
use App\Models\ActivityLog;

class KartuKeluargaService
{
    public function createKartuKeluarga(array $data)
    {
        return DB::transaction(function () use ($data) {
            $kk = KartuKeluarga::create($data);

            $userId = auth()->id();
            
            AuditLog::create([
                'user_id' => $userId,
                'entity_type' => 'KartuKeluarga',
                'entity_id' => $kk->no_kk,
                'action' => 'CREATED',
                'old_value' => null,
                'new_value' => $kk->toArray(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            ActivityLog::create([
                'user_id' => $userId,
                'activity_type' => 'CREATE_RESIDENT',
                'entity_type' => 'KartuKeluarga',
                'entity_id' => $kk->no_kk,
                'description' => "Membuat KK baru: {$kk->no_kk}",
                'ip_address' => request()->ip(),
            ]);

            return $kk;
        });
    }

    public function updateKartuKeluarga(KartuKeluarga $kk, array $data)
    {
        return DB::transaction(function () use ($kk, $data) {
            $oldValue = $kk->toArray();
            $kk->update($data);

            $userId = auth()->id();
            
            AuditLog::create([
                'user_id' => $userId,
                'entity_type' => 'KartuKeluarga',
                'entity_id' => $kk->no_kk,
                'action' => 'UPDATED',
                'old_value' => $oldValue,
                'new_value' => $kk->toArray(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            ActivityLog::create([
                'user_id' => $userId,
                'activity_type' => 'UPDATE_RESIDENT',
                'entity_type' => 'KartuKeluarga',
                'entity_id' => $kk->no_kk,
                'description' => "Memperbarui KK: {$kk->no_kk}",
                'ip_address' => request()->ip(),
            ]);

            return $kk;
        });
    }
}
