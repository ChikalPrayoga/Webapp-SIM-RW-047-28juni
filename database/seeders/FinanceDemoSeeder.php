<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KartuKeluarga;
use App\Models\User;
use App\Models\IuranType;
use App\Enums\RoleEnum;
use App\Enums\TransactionCategory;
use App\Services\ContributionService;
use App\Services\LedgerService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use App\Enums\PaymentStatus;

class FinanceDemoSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $contributionService = app(ContributionService::class);
        $ledgerService = app(LedgerService::class);

        // Get key users
        $rwUser = User::whereHas('role', function($q) {
            $q->where('role_name', RoleEnum::KETUA_RW->value);
        })->first();

        $bendaharaRw = User::whereHas('role', function($q) {
            $q->where('role_name', RoleEnum::BENDAHARA_RW->value);
        })->first();

        if (!$bendaharaRw) {
            $bendaharaRw = $rwUser;
        }

        $rt01User = User::where('username', 'ketuart01')->first();
        $rt02User = User::where('username', 'ketuart02')->first();

        // Get Iuran Types
        $iuranWajib = IuranType::where('name', 'Iuran Bulanan Wajib')->first();
        $iuranKematian = IuranType::where('name', 'Iuran Kematian')->first();
        $iuranSosial = IuranType::where('name', 'Iuran Sosial')->first();

        if (!$iuranWajib) {
            $this->command->warn('Iuran types not found. Please ensure IuranTypeSeeder has been run.');
            return;
        }

        $allKk = KartuKeluarga::all();

        if ($allKk->isEmpty()) {
            $this->command->warn('No KartuKeluarga found. Please ensure DemoSeeder has been run.');
            return;
        }

        DB::beginTransaction();

        try {
            // 1. Seed Contributions (Iuran)
            $monthsToSeed = [
                Carbon::now()->subMonths(2),
                Carbon::now()->subMonths(1),
                Carbon::now(),
            ];

            foreach ($allKk as $kk) {
                // Determine RT User
                $rtUser = $kk->rt_code === '001' ? $rt01User : ($kk->rt_code === '002' ? $rt02User : $rwUser);
                if (!$rtUser) continue;

                foreach ($monthsToSeed as $monthDate) {
                    // Not everyone pays every month (80% payment rate)
                    if (rand(1, 100) > 80) continue;

                    $nominal = $iuranWajib->default_nominal;
                    $tanggalPembayaran = (clone $monthDate)->startOfMonth()->addDays(rand(1, 20))->toDateString();

                    $data = [
                        'no_kk' => $kk->no_kk,
                        'iuran_type_id' => $iuranWajib->id,
                        'nominal' => $nominal,
                        'periode_bulan' => $monthDate->month,
                        'periode_tahun' => $monthDate->year,
                        'tanggal_pembayaran' => $tanggalPembayaran,
                    ];

                    try {
                        // Create PENDING contribution and INCOME ledger
                        $catatan = $contributionService->recordContribution($data, $rtUser->user_id);

                        // Randomly approve, reject, or leave pending
                        $rand = rand(1, 100);
                        if ($rand <= 70) {
                            // 70% APPROVED
                            $contributionService->validateContribution($catatan->iuran_id, $bendaharaRw->user_id);
                        } elseif ($rand > 70 && $rand <= 80) {
                            // 10% REJECTED
                            $contributionService->invalidateContribution($catatan->iuran_id, "Nominal tidak sesuai atau bukti buram", $bendaharaRw->user_id);
                        }
                        // 20% left PENDING
                    } catch (\Exception $e) {
                        $this->command->warn('Failed to seed contribution for KK ' . $kk->no_kk . ': ' . $e->getMessage());
                    }
                }
            }

            // 2. Seed Non-Iuran Transactions (Kas RW & Kas RT)
            
            // Pengeluaran Kas RW
            if ($bendaharaRw) {
                for ($i=0; $i<5; $i++) {
                    $ledgerService->createExpense([
                        'rt_code' => null,
                        'category' => TransactionCategory::OPERASIONAL->value,
                        'amount' => rand(5, 50) * 10000,
                        'description' => $faker->randomElement(['Biaya rapat pengurus', 'Perbaikan gerbang', 'Konsumsi kerja bakti', 'Beli ATK sekretariat']),
                        'transaction_date' => Carbon::now()->subDays(rand(1, 60))->toDateString(),
                        'created_by_user_id' => $bendaharaRw->user_id,
                    ]);
                }
                
                // Donasi Kas RW
                for ($i=0; $i<2; $i++) {
                    $ledgerService->createIncome([
                        'rt_code' => null,
                        'category' => TransactionCategory::DONASI->value,
                        'amount' => rand(10, 100) * 10000,
                        'description' => $faker->randomElement(['Donasi dari donatur anonim', 'Sumbangan acara agustusan', 'Bantuan operasional RW']),
                        'transaction_date' => Carbon::now()->subDays(rand(1, 60))->toDateString(),
                        'created_by_user_id' => $bendaharaRw->user_id,
                    ]);
                }
            }

            // Pengeluaran Kas RT 01
            if ($rt01User) {
                for ($i=0; $i<3; $i++) {
                    $ledgerService->createExpense([
                        'rt_code' => '001',
                        'category' => TransactionCategory::OPERASIONAL->value,
                        'amount' => rand(5, 20) * 10000,
                        'description' => $faker->randomElement(['Konsumsi ronda malam', 'Beli lampu jalan', 'Alat kebersihan pos']),
                        'transaction_date' => Carbon::now()->subDays(rand(1, 60))->toDateString(),
                        'created_by_user_id' => $rt01User->user_id,
                    ]);
                }
            }

            // Pengeluaran Kas RT 02
            if ($rt02User) {
                for ($i=0; $i<3; $i++) {
                    $ledgerService->createExpense([
                        'rt_code' => '002',
                        'category' => TransactionCategory::OPERASIONAL->value,
                        'amount' => rand(5, 20) * 10000,
                        'description' => $faker->randomElement(['Konsumsi ronda malam', 'Beli sapu jalan', 'Uang kopi keamanan']),
                        'transaction_date' => Carbon::now()->subDays(rand(1, 60))->toDateString(),
                        'created_by_user_id' => $rt02User->user_id,
                    ]);
                }
            }

            DB::commit();
            $this->command->info('Financial Demo Data seeded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Error seeding data: ' . $e->getMessage());
        }
    }
}
