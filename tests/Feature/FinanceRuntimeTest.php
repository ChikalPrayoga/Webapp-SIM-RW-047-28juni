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
use App\Models\IuranType;
use App\Models\CatatanIuranWarga;
use App\Models\FinancialTransaction;
use App\Enums\RoleEnum;
use App\Enums\PaymentStatus;
use App\Enums\TransactionType;
use App\Enums\TransactionCategory;

class FinanceRuntimeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed roles & permissions
        $this->seed([\Database\Seeders\RoleSeeder::class, \Database\Seeders\PermissionSeeder::class, \Database\Seeders\RolePermissionSeeder::class]);
    }

    public function test_finance_module_end_to_end()
    {
        // 1. Setup Data Warga & Pengurus
        $kk = KartuKeluarga::create([
            'no_kk' => '3201012345678911',
            'rt_code' => 'RT_03',
            'alamat_lengkap' => 'Jl. Merdeka No. 3',
            'blok' => 'C',
            'nomor_rumah' => '3',
            'status_kepemilikan_rumah' => 'MILIK_SENDIRI',
        ]);

        $warga = AnggotaKeluarga::create([
            'nik' => '3201011122334477',
            'no_kk' => $kk->no_kk,
            'nama_lengkap' => 'Warga Keuangan',
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
            'area_code' => 'RT_03',
            'start_date' => now(),
        ]);

        $bendaharaRole = Role::where('role_name', RoleEnum::BENDAHARA_RW->value)->first();
        $bendaharaUser = User::factory()->create(['role_id' => $bendaharaRole->role_id, 'status' => 'ACTIVE']);
        OrganizationalPosition::create([
            'user_id' => $bendaharaUser->user_id,
            'position_type' => 'BENDAHARA_RW',
            'area_code' => null,
            'start_date' => now(),
        ]);

        // 2. Setup IuranType as Bendahara
        $this->actingAs($bendaharaUser);
        $response = $this->post(route('finances.iuran-types.store'), [
            'name' => 'Iuran Kebersihan',
            'description' => 'Iuran bulanan untuk sampah',
            'default_nominal' => 25000,
            'type' => 'WAJIB',
            'is_active' => true,
        ]);
        $response->assertRedirect();
        
        $iuranType = IuranType::where('name', 'Iuran Kebersihan')->first();
        $this->assertNotNull($iuranType);

        // 3. RT Mencatat Iuran Warga (Manual)
        $this->actingAs($rtUser);
        $response = $this->post(route('finances.contributions.store'), [
            'no_kk' => $kk->no_kk,
            'iuran_type_id' => $iuranType->id,
            'nominal' => 25000,
            'periode_bulan' => 1,
            'periode_tahun' => 2026,
            'tanggal_pembayaran' => now()->toDateString(),
        ]);
        $response->assertRedirect();
        
        $catatan = CatatanIuranWarga::where('no_kk', $kk->no_kk)->first();
        $this->assertNotNull($catatan);
        $this->assertEquals(PaymentStatus::PENDING, $catatan->status);

        // Pastikan ledger entry tercatat
        $ledgerEntry = FinancialTransaction::where('reference_id', $catatan->iuran_id)->first();
        $this->assertNotNull($ledgerEntry);
        $this->assertEquals(25000, $ledgerEntry->amount);

        // 4. Bendahara RW Approve Iuran
        $this->actingAs($bendaharaUser);
        $response = $this->post(route('finances.verifications.approve', $catatan->iuran_id));
        $response->assertRedirect();
        
        $catatan->refresh();
        $this->assertEquals(PaymentStatus::APPROVED, $catatan->status);

        // 5. Bendahara RW Reject/Cancel Iuran (Untuk test reversal)
        // Kita catat iuran baru untuk direject
        $this->actingAs($rtUser);
        $this->post(route('finances.contributions.store'), [
            'no_kk' => $kk->no_kk,
            'iuran_type_id' => $iuranType->id,
            'nominal' => 30000,
            'periode_bulan' => 2,
            'periode_tahun' => 2026,
            'tanggal_pembayaran' => now()->toDateString(),
        ]);
        
        $catatanReject = CatatanIuranWarga::where('periode_bulan', 2)->first();
        
        $this->actingAs($bendaharaUser);
        $response = $this->post(route('finances.verifications.reject', $catatanReject->iuran_id), [
            'rejection_notes' => 'Nominal tidak sesuai'
        ]);
        $response->assertRedirect();
        
        $catatanReject->refresh();
        $this->assertEquals(PaymentStatus::REJECTED, $catatanReject->status);
        
        // Pastikan ada reversal di ledger
        $ledgerReject = FinancialTransaction::where('reference_id', $catatanReject->iuran_id)->first();
        $this->assertNotNull($ledgerReject->adjusted_transaction_id);

        $reversal = FinancialTransaction::find($ledgerReject->adjusted_transaction_id);
        $this->assertNotNull($reversal);
        $this->assertEquals(TransactionCategory::ADJUSTMENT, $reversal->category);
        $this->assertEquals(TransactionType::EXPENSE, $reversal->transaction_type);

        // 6. Test Transaksi Eksternal (Manual Income/Expense)
        $response = $this->post(route('finances.transactions.store'), [
            'transaction_type' => TransactionType::EXPENSE->value,
            'category' => TransactionCategory::OPERASIONAL->value,
            'amount' => 10000,
            'description' => 'Beli sapu untuk RW',
            'transaction_date' => now()->toDateString(),
        ]);
        $response->assertRedirect();

        // Saldo Test (Income 25k, Expense 10k => 15k)
        $ledgerService = app(\App\Services\LedgerService::class);
        $saldoRw = $ledgerService->getBalance(null);
        $this->assertEquals(-10000, $saldoRw);
        
        $saldoRt = $ledgerService->getBalance('RT_03');
        $this->assertEquals(25000, $saldoRt);
        
        // 7. Portal Warga (Lihat Transparansi & Verifikasi KK)
        $response = $this->post(route('portal.finance.verify'), [
            'no_kk' => $kk->no_kk,
            'nik' => $warga->nik,
        ]);
        $response->assertRedirect(route('portal.finance.history'));
        $response->assertSessionHas('verified_no_kk', $kk->no_kk);
    }
}
