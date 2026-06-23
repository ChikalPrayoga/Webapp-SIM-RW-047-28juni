<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\FinancialTransaction;
use App\Models\CatatanIuranWarga;
use App\Models\AnggotaKeluarga;
use App\Models\IuranType;
use App\Services\LedgerService;
use Illuminate\Http\Request;

class PortalFinanceController extends Controller
{
    protected LedgerService $ledgerService;

    public function __construct(LedgerService $ledgerService)
    {
        $this->ledgerService = $ledgerService;
    }

    /**
     * Transparansi Kas RW & RT (Halaman Utama Portal Keuangan).
     */
    public function index(Request $request)
    {
        // Total Saldo RW
        $saldoRW = $this->ledgerService->getBalance(null);

        // Mutasi Kas RW terbaru
        $mutasiRW = FinancialTransaction::active()
            ->rw()
            ->orderBy('transaction_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('portal.finance.transparency', compact('saldoRW', 'mutasiRW'));
    }

    /**
     * Tampilan Riwayat Iuran Warga (berdasarkan validasi KK/NIK).
     */
    public function history(Request $request)
    {
        $noKk = session('verified_no_kk');

        if (!$noKk) {
            return view('portal.finance.verify');
        }

        // Tampilkan data iuran KK tersebut
        $contributions = CatatanIuranWarga::with('iuranType')
            ->where('no_kk', $noKk)
            ->orderBy('periode_tahun', 'desc')
            ->orderBy('periode_bulan', 'desc')
            ->get();

        return view('portal.finance.history', compact('contributions', 'noKk'));
    }

    /**
     * Memproses verifikasi KK & NIK Warga.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'no_kk' => 'required|string|exists:kartu_keluargas,no_kk',
            'nik' => 'required|string|exists:anggota_keluargas,nik',
        ]);

        $noKk = $request->input('no_kk');
        $nik = $request->input('nik');

        // Pastikan NIK tersebut merupakan bagian dari KK yang diinput
        $belongsToKk = AnggotaKeluarga::where('no_kk', $noKk)
            ->where('nik', $nik)
            ->exists();

        if (!$belongsToKk) {
            return back()->withInput()->with('error', 'Kombinasi Nomor KK dan NIK tidak cocok.');
        }

        // Simpan sesi terverifikasi
        session(['verified_no_kk' => $noKk]);

        return redirect()->route('portal.finance.history');
    }

    /**
     * Form untuk melacak/mensubmit konfirmasi pencatatan iuran (luring/offline).
     */
    public function submitForm(Request $request)
    {
        $noKk = session('verified_no_kk');
        if (!$noKk) {
            return redirect()->route('portal.finance.history');
        }

        $iuranTypes = IuranType::active()->get();

        return view('portal.finance.submit', compact('noKk', 'iuranTypes'));
    }

    /**
     * Keluar dari sesi verifikasi KK.
     */
    public function logout()
    {
        session()->forget('verified_no_kk');
        return redirect()->route('portal.finance.history');
    }
}
