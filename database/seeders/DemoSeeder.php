<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\OrganizationalPosition;
use App\Models\KartuKeluarga;
use App\Models\AnggotaKeluarga;
use App\Models\LogLaporanAspirasi;
use App\Models\ComplaintStatusHistory;
use App\Models\PengajuanSurat;
use App\Models\LetterStatusHistory;
use App\Models\ActivityLog;
use App\Models\AuditLog;
use App\Enums\RoleEnum;
use App\Enums\ComplaintStatusEnum;
use App\Enums\ComplaintCategoryEnum;
use App\Enums\LetterStatusEnum;
use App\Enums\LetterTypeEnum;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Faker\Factory as Faker;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // 1. Roles
        $rwRole = Role::where('role_name', RoleEnum::KETUA_RW->value)->first();
        $rtRole = Role::where('role_name', RoleEnum::KETUA_RT->value)->first();
        
        // 2. Users (Pengurus)
        $rwUser = User::firstOrCreate(
            ['email' => 'rw@rw047.com'],
            ['role_id' => $rwRole->role_id, 'username' => 'ketuarw', 'password' => Hash::make('password123'), 'full_name' => 'Bapak Ketua RW', 'status' => 'ACTIVE']
        );
        OrganizationalPosition::firstOrCreate(
            ['user_id' => $rwUser->user_id, 'position_type' => 'KETUA_RW', 'area_code' => '047'],
            ['is_active' => true, 'start_date' => Carbon::now()->subYears(1)]
        );

        $rt01User = User::firstOrCreate(
            ['email' => 'rt01@rw047.com'],
            ['role_id' => $rtRole->role_id, 'username' => 'ketuart01', 'password' => Hash::make('password123'), 'full_name' => 'Bapak Ketua RT 01', 'status' => 'ACTIVE']
        );
        OrganizationalPosition::firstOrCreate(
            ['user_id' => $rt01User->user_id, 'position_type' => 'KETUA_RT', 'area_code' => '001'],
            ['is_active' => true, 'start_date' => Carbon::now()->subYears(1)]
        );

        $rt02User = User::firstOrCreate(
            ['email' => 'rt02@rw047.com'],
            ['role_id' => $rtRole->role_id, 'username' => 'ketuart02', 'password' => Hash::make('password123'), 'full_name' => 'Bapak Ketua RT 02', 'status' => 'ACTIVE']
        );
        OrganizationalPosition::firstOrCreate(
            ['user_id' => $rt02User->user_id, 'position_type' => 'KETUA_RT', 'area_code' => '002'],
            ['is_active' => true, 'start_date' => Carbon::now()->subYears(1)]
        );

        // 3. Warga Data (30-50 warga per RT total ~60)
        $wargaNiks = [];
        foreach (['001', '002'] as $rtCode) {
            for ($i = 0; $i < 10; $i++) {
                $kk = KartuKeluarga::create([
                    'no_kk' => $faker->unique()->numerify('327#############'),
                    'rt_code' => $rtCode,
                    'alamat_lengkap' => $faker->streetAddress,
                ]);

                $numMembers = rand(3, 5);
                for ($j = 0; $j < $numMembers; $j++) {
                    $nik = $faker->unique()->numerify('327#############');
                    $wargaNiks[] = ['nik' => $nik, 'rt' => $rtCode];
                    AnggotaKeluarga::create([
                        'no_kk' => $kk->no_kk,
                        'nik' => $nik,
                        'nama_lengkap' => $faker->name,
                        'jenis_kelamin' => $faker->randomElement(['L', 'P']),
                        'tempat_lahir' => $faker->city,
                        'tanggal_lahir' => $faker->date('Y-m-d', '-20 years'),
                        'pekerjaan' => 'Wiraswasta',
                        'status_hubungan_keluarga' => $j == 0 ? 'Kepala Keluarga' : 'Istri/Anak',
                        'status_warga' => 'AKTIF',
                    ]);
                }
            }
        }

        // 4. Complaints
        for ($i = 0; $i < 30; $i++) {
            $warga = $faker->randomElement($wargaNiks);
            $rtUser = $warga['rt'] == '001' ? $rt01User : $rt02User;
            $submittedAt = Carbon::now()->subDays(rand(1, 90));
            
            $status = $faker->randomElement([ComplaintStatusEnum::SUBMITTED, ComplaintStatusEnum::IN_PROGRESS, ComplaintStatusEnum::RESOLVED, ComplaintStatusEnum::CLOSED]);
            
            $complaint = LogLaporanAspirasi::create([
                'nik' => $warga['nik'],
                'kanal_laporan' => 'WEBSITE',
                'teks_keluhan' => $faker->paragraph,
                'ai_category' => $faker->randomElement([ComplaintCategoryEnum::INFRASTRUCTURE, ComplaintCategoryEnum::ENVIRONMENT, ComplaintCategoryEnum::SECURITY]),
                'current_status' => $status,
                'submitted_at' => $submittedAt,
            ]);

            // History
            ComplaintStatusHistory::create([
                'aspirasi_id' => $complaint->aspirasi_id,
                'actor_user_id' => null,
                'previous_status' => null,
                'new_status' => ComplaintStatusEnum::SUBMITTED,
                'notes' => null,
                'changed_at' => $submittedAt,
            ]);

            ActivityLog::create([
                'user_id' => null,
                'activity_type' => 'SUBMIT_COMPLAINT',
                'description' => 'Laporan diajukan',
                'ip_address' => '127.0.0.1',
                'created_at' => $submittedAt,
            ]);

            if ($status != ComplaintStatusEnum::SUBMITTED) {
                $processAt = clone $submittedAt;
                $processAt->addHours(rand(2, 24));
                
                $finalStatus = $status;
                if ($finalStatus == ComplaintStatusEnum::RESOLVED) {
                    $status = ComplaintStatusEnum::IN_PROGRESS; // pass through
                }

                ComplaintStatusHistory::create([
                    'aspirasi_id' => $complaint->aspirasi_id,
                    'actor_user_id' => $rtUser->user_id,
                    'previous_status' => ComplaintStatusEnum::SUBMITTED,
                    'new_status' => $status,
                    'notes' => 'Sedang kami cek.',
                    'changed_at' => $processAt,
                ]);

                AuditLog::create([
                    'user_id' => $rtUser->user_id,
                    'action' => 'UPDATE_STATUS',
                    'entity_type' => 'App\Models\LogLaporanAspirasi',
                    'entity_id' => $complaint->aspirasi_id,
                    'old_value' => json_encode(['current_status' => 'SUBMITTED']),
                    'new_value' => json_encode(['current_status' => $status->value]),
                    'ip_address' => '127.0.0.1',
                    'user_agent' => 'Seeder',
                    'created_at' => $processAt,
                ]);

                if ($finalStatus == ComplaintStatusEnum::RESOLVED) {
                    $resolveAt = clone $processAt;
                    $resolveAt->addDays(rand(1, 3));
                    $complaint->update(['resolved_at' => $resolveAt, 'current_status' => ComplaintStatusEnum::RESOLVED]);
                    ComplaintStatusHistory::create([
                        'aspirasi_id' => $complaint->aspirasi_id,
                        'actor_user_id' => $rtUser->user_id,
                        'previous_status' => ComplaintStatusEnum::IN_PROGRESS,
                        'new_status' => ComplaintStatusEnum::RESOLVED,
                        'notes' => 'Sudah diperbaiki.',
                        'changed_at' => $resolveAt,
                    ]);
                    AuditLog::create([
                        'user_id' => $rtUser->user_id,
                        'action' => 'UPDATE_STATUS',
                        'entity_type' => 'App\Models\LogLaporanAspirasi',
                        'entity_id' => $complaint->aspirasi_id,
                        'old_value' => json_encode(['current_status' => 'IN_PROGRESS']),
                        'new_value' => json_encode(['current_status' => 'RESOLVED']),
                        'ip_address' => '127.0.0.1',
                        'user_agent' => 'Seeder',
                        'created_at' => $resolveAt,
                    ]);
                }
            }
        }

        // 5. Letters
        for ($i = 0; $i < 30; $i++) {
            $warga = $faker->randomElement($wargaNiks);
            $rtUser = $warga['rt'] == '001' ? $rt01User : $rt02User;
            $submittedAt = Carbon::now()->subDays(rand(1, 90));
            
            $letterType = $faker->randomElement([LetterTypeEnum::SURAT_PENGANTAR, LetterTypeEnum::SURAT_KETERANGAN_DOMISILI]);
            $status = $faker->randomElement([LetterStatusEnum::SUBMITTED, LetterStatusEnum::RT_REVIEW, LetterStatusEnum::RW_REVIEW, LetterStatusEnum::COMPLETED, LetterStatusEnum::REJECTED]);
            
            // Fix invalid states based on letter type
            if ($letterType == LetterTypeEnum::SURAT_KETERANGAN_DOMISILI && $status == LetterStatusEnum::RW_REVIEW) {
                $status = LetterStatusEnum::COMPLETED;
            }

            $letter = PengajuanSurat::create([
                'nik' => $warga['nik'],
                'jenis_surat' => $letterType,
                'keperluan' => $faker->sentence(6),
                'current_status' => $status,
                'tanggal_pengajuan' => $submittedAt,
                'tanggal_selesai' => $status == LetterStatusEnum::COMPLETED ? (clone $submittedAt)->addDays(rand(1,3)) : null,
                'nomor_surat' => $status == LetterStatusEnum::COMPLETED ? '047/' . rand(10,99) . '/' . $warga['rt'] : null,
                'created_at' => $submittedAt,
            ]);

            // History SUBMITTED
            LetterStatusHistory::create([
                'pengajuan_id' => $letter->pengajuan_id,
                'actor_user_id' => null,
                'previous_status' => null,
                'new_status' => LetterStatusEnum::SUBMITTED,
                'notes' => null,
                'changed_at' => $submittedAt,
            ]);

            ActivityLog::create([
                'user_id' => null,
                'activity_type' => 'SUBMIT_LETTER',
                'description' => 'Pengajuan surat',
                'ip_address' => '127.0.0.1',
                'created_at' => $submittedAt,
            ]);

            if ($status != LetterStatusEnum::SUBMITTED) {
                $processAt = clone $submittedAt;
                $processAt->addHours(rand(2, 12));
                
                $finalStatus = $status;
                $status = LetterStatusEnum::RT_REVIEW;

                LetterStatusHistory::create([
                    'pengajuan_id' => $letter->pengajuan_id,
                    'actor_user_id' => $rtUser->user_id,
                    'previous_status' => LetterStatusEnum::SUBMITTED,
                    'new_status' => LetterStatusEnum::RT_REVIEW,
                    'notes' => 'Diperiksa RT.',
                    'changed_at' => $processAt,
                ]);

                if ($finalStatus == LetterStatusEnum::RT_REVIEW) continue;

                if ($letterType == LetterTypeEnum::SURAT_PENGANTAR && in_array($finalStatus, [LetterStatusEnum::RW_REVIEW, LetterStatusEnum::COMPLETED, LetterStatusEnum::REJECTED])) {
                    $rwAt = clone $processAt;
                    $rwAt->addHours(rand(2, 24));
                    LetterStatusHistory::create([
                        'pengajuan_id' => $letter->pengajuan_id,
                        'actor_user_id' => $rtUser->user_id,
                        'previous_status' => LetterStatusEnum::RT_REVIEW,
                        'new_status' => LetterStatusEnum::RW_REVIEW,
                        'notes' => 'Diteruskan ke RW.',
                        'changed_at' => $rwAt,
                    ]);

                    if ($finalStatus == LetterStatusEnum::RW_REVIEW) continue;

                    $completeAt = clone $rwAt;
                    $completeAt->addHours(rand(2, 24));
                    LetterStatusHistory::create([
                        'pengajuan_id' => $letter->pengajuan_id,
                        'actor_user_id' => $finalStatus == LetterStatusEnum::REJECTED ? $rwUser->user_id : $rwUser->user_id,
                        'previous_status' => LetterStatusEnum::RW_REVIEW,
                        'new_status' => $finalStatus,
                        'notes' => $finalStatus == LetterStatusEnum::REJECTED ? 'Ditolak RW' : 'Selesai ditandatangani RW',
                        'changed_at' => $completeAt,
                    ]);
                } else {
                    $completeAt = clone $processAt;
                    $completeAt->addHours(rand(2, 24));
                    LetterStatusHistory::create([
                        'pengajuan_id' => $letter->pengajuan_id,
                        'actor_user_id' => $rtUser->user_id,
                        'previous_status' => LetterStatusEnum::RT_REVIEW,
                        'new_status' => $finalStatus,
                        'notes' => $finalStatus == LetterStatusEnum::REJECTED ? 'Ditolak RT' : 'Selesai ditandatangani RT',
                        'changed_at' => $completeAt,
                    ]);
                }
            }
        }
    }
}
