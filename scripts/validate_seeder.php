<?php
$metrics = [];
$metrics['counts'] = [
    'users' => App\Models\User::count(),
    'positions' => App\Models\OrganizationalPosition::count(),
    'kk' => App\Models\KartuKeluarga::count(),
    'anggota' => App\Models\AnggotaKeluarga::count(),
    'complaints' => App\Models\LogLaporanAspirasi::count(),
    'complaint_histories' => App\Models\ComplaintStatusHistory::count(),
    'letters' => App\Models\PengajuanSurat::count(),
    'letter_histories' => App\Models\LetterStatusHistory::count(),
    'activity_logs' => App\Models\ActivityLog::count(),
    'audit_logs' => App\Models\AuditLog::count(),
];
$metrics['complaint_status'] = App\Models\LogLaporanAspirasi::select('current_status', DB::raw('count(*) as total'))->groupBy('current_status')->pluck('total', 'current_status');
$metrics['letter_status'] = App\Models\PengajuanSurat::select('current_status', DB::raw('count(*) as total'))->groupBy('current_status')->pluck('total', 'current_status');

$metrics['complaint_rt'] = DB::table('log_laporan_aspirasis')
    ->join('anggota_keluargas', 'log_laporan_aspirasis.nik', '=', 'anggota_keluargas.nik')
    ->join('kartu_keluargas', 'anggota_keluargas.no_kk', '=', 'kartu_keluargas.no_kk')
    ->select('kartu_keluargas.rt_code', DB::raw('count(log_laporan_aspirasis.aspirasi_id) as total'))
    ->groupBy('kartu_keluargas.rt_code')->pluck('total', 'rt_code');

$metrics['letter_rt'] = DB::table('pengajuan_surats')
    ->join('anggota_keluargas', 'pengajuan_surats.nik', '=', 'anggota_keluargas.nik')
    ->join('kartu_keluargas', 'anggota_keluargas.no_kk', '=', 'kartu_keluargas.no_kk')
    ->select('kartu_keluargas.rt_code', DB::raw('count(pengajuan_surats.pengajuan_id) as total'))
    ->groupBy('kartu_keluargas.rt_code')->pluck('total', 'rt_code');

$metrics['orphans'] = [
    'letter_without_warga' => DB::table('pengajuan_surats')->whereNotIn('nik', DB::table('anggota_keluargas')->pluck('nik'))->count(),
    'complaint_without_warga' => DB::table('log_laporan_aspirasis')->whereNotIn('nik', DB::table('anggota_keluargas')->pluck('nik'))->count(),
    'letter_history_orphan' => DB::table('letter_status_histories')->whereNotIn('pengajuan_id', DB::table('pengajuan_surats')->pluck('pengajuan_id'))->count(),
    'complaint_history_orphan' => DB::table('complaint_status_histories')->whereNotIn('aspirasi_id', DB::table('log_laporan_aspirasis')->pluck('aspirasi_id'))->count(),
];

echo json_encode($metrics, JSON_PRETTY_PRINT);
