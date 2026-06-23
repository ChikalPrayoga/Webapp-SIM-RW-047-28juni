<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Services\LedgerService;
use App\Models\FinancialTransaction;
use App\Models\CatatanIuranWarga;
use App\Enums\PermissionEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class FinanceDashboardController extends Controller
{
    protected LedgerService $ledgerService;

    public function __construct(LedgerService $ledgerService)
    {
        $this->ledgerService = $ledgerService;
    }

    /**
     * Tampilan Dashboard Keuangan.
     */
    public function index(Request $request)
    {
        // 1. Authorize
        if (!Gate::allows('viewAny', FinancialTransaction::class)) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat data keuangan.');
        }

        // 2. Scoping RT / RW
        $user = $request->user();
        $userArea = $user->position?->area_code; // Null for RW, 'RT001' etc. for RT

        // 3. Ambil data balance & statistik
        $saldoKas = $this->ledgerService->getBalance($userArea);
        
        $query = FinancialTransaction::active();
        $iuranQuery = CatatanIuranWarga::query();

        if (!empty($userArea)) {
            $query->rt($userArea);
            $iuranQuery->byRt($userArea);
            $saldoKasRW = $this->ledgerService->getBalance(null); // Kas RW read-only for RT
        } else {
            $query->rw();
            $saldoKasRW = $saldoKas;
        }

        $totalIncome = (float) (clone $query)->income()->sum('amount');
        $totalExpense = (float) (clone $query)->expense()->sum('amount');
        
        // Antrean validasi pending (hanya relevan bagi Bendahara RW secara global)
        $pendingCount = CatatanIuranWarga::pending()->count();
        
        // Aktivitas terbaru
        $recentTransactions = $query->latest('transaction_date')
            ->latest('created_at')
            ->limit(5)
            ->get();

        return view('admin.finance.dashboard', compact(
            'saldoKas',
            'saldoKasRW',
            'totalIncome',
            'totalExpense',
            'pendingCount',
            'recentTransactions',
            'userArea'
        ));
    }
}
