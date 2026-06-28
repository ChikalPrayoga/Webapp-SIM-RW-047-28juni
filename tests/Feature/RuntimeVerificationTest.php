<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\KartuKeluarga;
use App\Models\AnggotaKeluarga;
use App\Models\OrganizationalPosition;
use App\Models\PengajuanSurat;
use App\Models\LogLaporanAspirasi;
use App\Enums\RoleEnum;
use App\Enums\LetterTypeEnum;
use App\Enums\LetterStatusEnum;
use App\Enums\ComplaintStatusEnum;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class RuntimeVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Run seeders for roles and permissions
        $this->seed([\Database\Seeders\RoleSeeder::class, \Database\Seeders\PermissionSeeder::class, \Database\Seeders\RolePermissionSeeder::class]);
    }

    public function test_modul_surat_end_to_end()
    {
        // 1. Setup Data Warga & Pengurus
        $kk = KartuKeluarga::create([
            'no_kk' => '3201012345678901',
            'rt_code' => 'RT_01',
            'alamat_lengkap' => 'Jl. Merdeka No. 1',
            'blok' => 'A',
            'nomor_rumah' => '1',
            'status_kepemilikan_rumah' => 'MILIK_SENDIRI',
        ]);

        $warga = AnggotaKeluarga::create([
            'nik' => '3201011122334455',
            'no_kk' => $kk->no_kk,
            'nama_lengkap' => 'Warga Test',
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => '1990-01-01',
            'pekerjaan' => 'Karyawan',
            'nomor_hp' => '081234567890',
            'status_hubungan_keluarga' => 'KEPALA_KELUARGA',
            'status_sosio_ekonomi' => 'MENENGAH',
            'status_warga' => 'WARGA_TETAP',
        ]);

        $rtRole = Role::where('role_name', RoleEnum::KETUA_RT->value)->first();
        $rtUser = User::factory()->create(['role_id' => $rtRole->role_id, 'status' => 'ACTIVE']);
        OrganizationalPosition::create([
            'user_id' => $rtUser->user_id,
            'position_type' => 'KETUA_RT',
            'area_code' => 'RT_01',
            'start_date' => now(),
        ]);

        $rwRole = Role::where('role_name', RoleEnum::KETUA_RW->value)->first();
        $rwUser = User::factory()->create(['role_id' => $rwRole->role_id, 'status' => 'ACTIVE']);

        // 2. Warga Mengajukan Surat
        $response = $this->post(route('public.letters.store'), [
            'nik' => $warga->nik,
            'jenis_surat' => LetterTypeEnum::SURAT_PENGANTAR->value,
            'keperluan' => 'Pembuatan KTP Baru'
        ]);
        
        $response->assertRedirect(route('public.letters.track'));
        $response->assertSessionHas('success');

        $surat = PengajuanSurat::where('nik', $warga->nik)->first();
        $this->assertNotNull($surat);
        $this->assertEquals(LetterStatusEnum::SUBMITTED, $surat->current_status);

        // 3. Warga Track Surat
        $response = $this->post(route('public.letters.show'), [
            'pengajuan_id' => $surat->pengajuan_id,
            'nik' => $warga->nik,
        ]);
        $response->assertStatus(200);

        // 4. RT Memproses Surat
        $this->actingAs($rtUser);
        $response = $this->post(route('letters.process', $surat->pengajuan_id), [
            'notes' => 'Berkas lengkap, memproses surat'
        ]);
        $response->assertRedirect();
        
        $surat->refresh();
        $this->assertEquals(LetterStatusEnum::RT_REVIEW, $surat->current_status);

        // 5. RT Forward Surat ke RW
        $response = $this->post(route('letters.forward', $surat->pengajuan_id), [
            'notes' => 'Silakan di ACC Pak RW'
        ]);
        $response->assertRedirect();
        
        $surat->refresh();
        $this->assertEquals(LetterStatusEnum::RW_REVIEW, $surat->current_status);

        // 6. RW Menyelesaikan Surat
        $this->actingAs($rwUser);
        $response = $this->post(route('letters.complete', $surat->pengajuan_id), [
            'nomor_surat' => '001/RW047/2026',
            'notes' => 'Selesai ditandatangani'
        ]);
        $response->assertRedirect();

        $surat->refresh();
        $this->assertEquals(LetterStatusEnum::COMPLETED, $surat->current_status);
        $this->assertEquals('001/RW047/2026', $surat->nomor_surat);
        $this->assertCount(4, $surat->statusHistories); // SUBMITTED, RT_REVIEW, RW_REVIEW, COMPLETED
    }

    public function test_modul_laporan_end_to_end()
    {
        Storage::fake('local');

        // 1. Setup Data
        $kk = KartuKeluarga::create([
            'no_kk' => '3201012345678902',
            'rt_code' => 'RT_02',
            'alamat_lengkap' => 'Jl. Merdeka No. 2',
            'blok' => 'B',
            'nomor_rumah' => '2',
            'status_kepemilikan_rumah' => 'MILIK_SENDIRI',
        ]);

        $warga = AnggotaKeluarga::create([
            'nik' => '3201011122334466',
            'no_kk' => $kk->no_kk,
            'nama_lengkap' => 'Warga Pelapor',
            'jenis_kelamin' => 'P',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => '1992-02-02',
            'pekerjaan' => 'Ibu Rumah Tangga',
            'nomor_hp' => '081234567891',
            'status_hubungan_keluarga' => 'ISTRI',
            'status_sosio_ekonomi' => 'MENENGAH',
            'status_warga' => 'WARGA_TETAP',
        ]);

        $adminRole = Role::where('role_name', RoleEnum::KETUA_RW->value)->first();
        $adminUser = User::factory()->create(['role_id' => $adminRole->role_id, 'status' => 'ACTIVE']);
        $staffUser = User::factory()->create(['role_id' => $adminRole->role_id, 'status' => 'ACTIVE']);

        // 2. Warga Mengajukan Laporan
        $file = UploadedFile::fake()->image('bukti.jpg');
        $response = $this->post(route('public.complaints.store'), [
            'nik' => $warga->nik,
            'teks_keluhan' => 'Lampu jalan mati di depan rumah.',
            'attachments' => [$file]
        ]);
        
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $laporan = LogLaporanAspirasi::where('nik', $warga->nik)->first();
        $this->assertNotNull($laporan);
        $this->assertEquals(ComplaintStatusEnum::SUBMITTED, $laporan->current_status);
        $this->assertCount(1, $laporan->attachments);

        // 3. Admin Update Status
        $this->actingAs($adminUser);
        $response = $this->put(route('complaints.updateStatus', $laporan->aspirasi_id), [
            'status' => ComplaintStatusEnum::IN_PROGRESS->value,
            'category' => 'INFRASTRUCTURE',
            'priority' => 'MEDIUM',
            'notes' => 'Sedang dikoordinasikan dengan PLN'
        ]);
        $response->assertRedirect();
        
        $laporan->refresh();
        $this->assertEquals(ComplaintStatusEnum::IN_PROGRESS, $laporan->current_status);
        $this->assertEquals('INFRASTRUCTURE', $laporan->ai_category->value);

        // 4. Admin Assign ke Staff
        $response = $this->post(route('complaints.assign', $laporan->aspirasi_id), [
            'assigned_to_user_id' => $staffUser->user_id,
            'notes' => 'Tolong dicek ke lokasi ya'
        ]);
        $response->assertRedirect();

        $this->assertCount(1, $laporan->assignments);
        $this->assertEquals($staffUser->user_id, $laporan->assignments->first()->assigned_to_user_id);

        // 5. Download Attachment test
        $attachment = $laporan->attachments->first();
        $response = $this->get(route('complaints.attachments.download', $attachment->attachment_id));
        $response->assertStatus(200);
    }
}
