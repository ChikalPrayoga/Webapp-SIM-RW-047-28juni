<?php

namespace App\Services;

use App\Models\AnggotaKeluarga;
use Illuminate\Support\Facades\DB;
use App\Models\AuditLog;
use App\Models\ActivityLog;
use App\Models\ResidentChangeRequest;
use App\Events\ResidentChangeRequested;

class WargaService
{
    public function createWarga(array $data)
    {
        return DB::transaction(function () use ($data) {
            $warga = AnggotaKeluarga::create($data);

            $userId = auth()->id();
            
            AuditLog::create([
                'user_id' => $userId,
                'entity_type' => 'AnggotaKeluarga',
                'entity_id' => $warga->nik,
                'action' => 'CREATED',
                'old_value' => null,
                'new_value' => $warga->toArray(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            ActivityLog::create([
                'user_id' => $userId,
                'activity_type' => 'CREATE_RESIDENT',
                'entity_type' => 'AnggotaKeluarga',
                'entity_id' => $warga->nik,
                'description' => "Membuat Data Warga baru: {$warga->nik}",
                'ip_address' => request()->ip(),
            ]);

            return $warga;
        });
    }

    public function requestUpdateWarga(AnggotaKeluarga $warga, array $data)
    {
        // Instead of directly updating, create a ResidentChangeRequest
        return DB::transaction(function () use ($warga, $data) {
            foreach ($data as $field => $newValue) {
                if ($warga->{$field} != $newValue) {
                    $request = ResidentChangeRequest::create([
                        'nik' => $warga->nik,
                        'field_name' => $field,
                        'old_value' => $warga->{$field},
                        'new_value' => $newValue,
                        'current_status' => \App\Enums\ResidentChangeStatusEnum::PENDING->value,
                    ]);

                    event(new ResidentChangeRequested($request));
                }
            }

            return true;
        });
    }

    // Direct update for Ketua RT / Admin
    public function directUpdateWarga(AnggotaKeluarga $warga, array $data)
    {
        return DB::transaction(function () use ($warga, $data) {
            $oldValue = $warga->toArray();
            $warga->update($data);

            $userId = auth()->id();
            
            AuditLog::create([
                'user_id' => $userId,
                'entity_type' => 'AnggotaKeluarga',
                'entity_id' => $warga->nik,
                'action' => 'UPDATED',
                'old_value' => $oldValue,
                'new_value' => $warga->toArray(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            ActivityLog::create([
                'user_id' => $userId,
                'activity_type' => 'UPDATE_RESIDENT',
                'entity_type' => 'AnggotaKeluarga',
                'entity_id' => $warga->nik,
                'description' => "Memperbarui Data Warga (Direct): {$warga->nik}",
                'ip_address' => request()->ip(),
            ]);

            return $warga;
        });
    }
}
