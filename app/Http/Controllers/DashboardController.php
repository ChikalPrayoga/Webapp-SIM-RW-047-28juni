<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AnggotaKeluarga;
use App\Models\KartuKeluarga;
use App\Models\PengajuanSurat;
use App\Models\LogLaporanAspirasi;
use App\Enums\RoleEnum;
use App\Enums\LetterStatusEnum;
use App\Enums\ComplaintStatusEnum;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $roleName = $user->role->role_name ?? null;
        $isRT = $roleName === RoleEnum::KETUA_RT->value;
        $rtCode = $isRT && $user->position ? $user->position->area_code : null;

        // Base Queries
        $wargaQuery = AnggotaKeluarga::query();
        $kkQuery = KartuKeluarga::query();
        $suratQuery = PengajuanSurat::query();
        $laporanQuery = LogLaporanAspirasi::query();

        // Scope by RT if applicable
        if ($isRT && $rtCode) {
            $wargaQuery->whereHas('kartuKeluarga', function($q) use ($rtCode) {
                $q->where('rt_code', $rtCode);
            });
            $kkQuery->where('rt_code', $rtCode);
            $suratQuery->whereHas('pemohon.kartuKeluarga', function($q) use ($rtCode) {
                $q->where('rt_code', $rtCode);
            });
            $laporanQuery->whereHas('pelapor.kartuKeluarga', function($q) use ($rtCode) {
                $q->where('rt_code', $rtCode);
            });
        }

        // Standard Metrics
        $metrics = [
            'total_warga' => $wargaQuery->count(),
            'total_kk' => $kkQuery->count(),
            
            'total_surat' => (clone $suratQuery)->count(),
            'surat_pending' => (clone $suratQuery)->whereIn('current_status', [
                LetterStatusEnum::SUBMITTED->value, 
                LetterStatusEnum::RT_REVIEW->value, 
                LetterStatusEnum::RW_REVIEW->value
            ])->count(),
            'surat_selesai' => (clone $suratQuery)->where('current_status', LetterStatusEnum::COMPLETED->value)->count(),
            
            'total_laporan' => (clone $laporanQuery)->count(),
            'laporan_pending' => (clone $laporanQuery)->whereIn('current_status', [
                ComplaintStatusEnum::SUBMITTED->value,
                ComplaintStatusEnum::CLASSIFIED->value,
                ComplaintStatusEnum::IN_PROGRESS->value
            ])->count(),
            'laporan_selesai' => (clone $laporanQuery)->whereIn('current_status', [
                ComplaintStatusEnum::RESOLVED->value,
                ComplaintStatusEnum::CLOSED->value
            ])->count(),
        ];

        // Custom Metrics per Role
        if ($roleName === RoleEnum::KETUA_RW->value) {
            $metrics['approval_surat_rw'] = (clone $suratQuery)->where('current_status', LetterStatusEnum::RW_REVIEW->value)->count();
        }

        if ($roleName === RoleEnum::SEKRETARIS_RW->value) {
            $metrics['surat_baru'] = (clone $suratQuery)->where('current_status', LetterStatusEnum::SUBMITTED->value)->count();
            $metrics['surat_diproses'] = (clone $suratQuery)->whereIn('current_status', [
                LetterStatusEnum::RT_REVIEW->value,
                LetterStatusEnum::RW_REVIEW->value
            ])->count();
            $metrics['laporan_baru'] = (clone $laporanQuery)->where('current_status', ComplaintStatusEnum::SUBMITTED->value)->count();
        }
        
        if ($isRT) {
            $metrics['approval_surat_rt'] = (clone $suratQuery)->where('current_status', LetterStatusEnum::RT_REVIEW->value)->count();
        }

        if ($roleName === RoleEnum::SUPER_ADMIN->value) {
            $metrics['total_users'] = \App\Models\User::count();
            $metrics['total_roles'] = \App\Models\Role::count();
        }

        // Recent Lists
        $recentSurat = (clone $suratQuery)->with('pemohon')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        $recentLaporan = (clone $laporanQuery)->with('pelapor')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $viewName = match($roleName) {
            RoleEnum::SUPER_ADMIN->value => 'dashboard-admin',
            RoleEnum::KETUA_RW->value => 'dashboard-rw',
            RoleEnum::SEKRETARIS_RW->value => 'dashboard-sekretaris',
            RoleEnum::BENDAHARA_RW->value => 'dashboard-bendahara',
            RoleEnum::KETUA_RT->value => 'dashboard-rt',
            default => 'dashboard'
        };

        return view($viewName, compact('metrics', 'recentSurat', 'recentLaporan'));
    }
}
