<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\CatatanIuranWarga;
use App\Models\IuranType;
use App\Models\KartuKeluarga;
use App\Http\Requests\StoreManualContributionRequest;
use App\Services\ContributionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ContributionController extends Controller
{
    protected ContributionService $contributionService;

    public function __construct(ContributionService $contributionService)
    {
        $this->contributionService = $contributionService;
    }

    /**
     * Display a listing of the contributions.
     */
    public function index(Request $request)
    {
        if (!Gate::allows('viewAny', CatatanIuranWarga::class)) {
            abort(403, 'Unauthorized');
        }

        $user = $request->user();
        $userArea = $user->position?->area_code;

        $query = CatatanIuranWarga::with(['iuranType', 'kartuKeluarga']);

        // Scoping RT
        if (!empty($userArea)) {
            $query->byRt($userArea);
        }

        // Filters
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }
        if ($request->has('iuran_type_id') && $request->iuran_type_id !== '') {
            $query->where('iuran_type_id', $request->iuran_type_id);
        }
        if ($request->has('search') && $request->search !== '') {
            $query->where('no_kk', 'like', "%{$request->search}%");
        }

        $contributions = $query->orderBy('created_at', 'desc')->paginate(15);
        $iuranTypes = IuranType::active()->get();

        return view('admin.finance.contributions.index', compact('contributions', 'iuranTypes', 'userArea'));
    }

    /**
     * Show the form for recording a manual contribution.
     */
    public function create(Request $request)
    {
        if (!Gate::allows('create', CatatanIuranWarga::class)) {
            abort(403, 'Unauthorized');
        }

        $user = $request->user();
        $userArea = $user->position?->area_code;

        $iuranTypes = IuranType::active()->get();
        
        // Scope families listed to their RT only if Ketua RT
        $kkQuery = KartuKeluarga::query();
        if (!empty($userArea)) {
            $kkQuery->where('rt_code', $userArea);
        }
        $families = $kkQuery->orderBy('no_kk')->get();

        return view('admin.finance.contributions.create', compact('iuranTypes', 'families', 'userArea'));
    }

    /**
     * Store a manual contribution.
     */
    public function store(StoreManualContributionRequest $request)
    {
        $data = $request->validated();
        $userId = $request->user()->user_id;

        try {
            $this->contributionService->recordContribution($data, $userId);

            return redirect()->route('finances.contributions.index')
                ->with('success', 'Catatan iuran tunai warga berhasil disimpan.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal mencatat iuran: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified contribution details.
     */
    public function show($id)
    {
        $contribution = CatatanIuranWarga::with(['iuranType', 'kartuKeluarga', 'recorder', 'approver'])
            ->findOrFail($id);

        if (!Gate::allows('view', $contribution)) {
            abort(403, 'Unauthorized');
        }

        return view('admin.finance.contributions.show', compact('contribution'));
    }
}
