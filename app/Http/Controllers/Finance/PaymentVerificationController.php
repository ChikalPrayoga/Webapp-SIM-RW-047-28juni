<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\CatatanIuranWarga;
use App\Http\Requests\ApproveContributionRequest;
use App\Http\Requests\RejectContributionRequest;
use App\Services\ContributionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PaymentVerificationController extends Controller
{
    protected ContributionService $contributionService;

    public function __construct(ContributionService $contributionService)
    {
        $this->contributionService = $contributionService;
    }

    /**
     * Tampilkan antrean audit iuran (Pending Queue).
     */
    public function index(Request $request)
    {
        // ViewAny check
        if (!Gate::allows('viewAny', CatatanIuranWarga::class)) {
            abort(403, 'Unauthorized');
        }

        // Bendahara RW exclusive view for pending verifications
        $user = $request->user();
        if (!empty($user->position?->area_code)) {
            abort(403, 'Hanya Bendahara RW yang dapat mengakses antrean audit iuran.');
        }

        $pendingContributions = CatatanIuranWarga::with(['iuranType', 'kartuKeluarga'])
            ->pending()
            ->orderBy('created_at', 'asc')
            ->paginate(15);

        return view('admin.finance.verification.index', compact('pendingContributions'));
    }

    /**
     * Setujui Pembayaran Iuran (APPROVED).
     */
    public function approve(ApproveContributionRequest $request, $id)
    {
        $contribution = CatatanIuranWarga::findOrFail($id);

        if (!Gate::allows('verify', $contribution)) {
            abort(403, 'Anda tidak memiliki wewenang untuk menyetujui data iuran ini.');
        }

        try {
            $this->contributionService->validateContribution($id, $request->user()->user_id);

            return redirect()->route('finances.verifications.index')
                ->with('success', 'Catatan iuran berhasil disetujui dan kas masuk telah dicatat.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memproses approval: ' . $e->getMessage());
        }
    }

    /**
     * Tolak/Batalkan Pembayaran Iuran (REJECTED).
     */
    public function reject(RejectContributionRequest $request, $id)
    {
        $contribution = CatatanIuranWarga::findOrFail($id);

        if (!Gate::allows('verify', $contribution)) {
            abort(403, 'Anda tidak memiliki wewenang untuk membatalkan data iuran ini.');
        }

        try {
            $reason = $request->input('rejection_notes');
            $this->contributionService->invalidateContribution($id, $reason, $request->user()->user_id);

            return redirect()->route('finances.verifications.index')
                ->with('success', 'Catatan iuran telah dibatalkan dan kas masuk terkait telah disesuaikan.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memproses penolakan: ' . $e->getMessage());
        }
    }
}
