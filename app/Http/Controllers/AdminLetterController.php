<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\RejectLetterRequest;
use App\Http\Requests\CompleteLetterRequest;
use App\Models\PengajuanSurat;
use App\Services\LetterApprovalService;
use App\Repositories\LetterRepository;

class AdminLetterController extends Controller
{
    protected LetterApprovalService $service;

    public function __construct(LetterApprovalService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request, LetterRepository $repository)
    {
        $this->authorize('viewAny', PengajuanSurat::class);

        $filters = $request->only(['status', 'search']);
        
        $user = auth()->user();
        if ($user->role->role_name === \App\Enums\RoleEnum::KETUA_RT->value) {
            $filters['rt_code'] = $user->position->area_code ?? null;
        }
        
        $letters = $repository->getAllPaginated($filters);
        
        return view('letters.admin.index', compact('letters'));
    }

    public function show($id)
    {
        $letter = PengajuanSurat::with(['pemohon.kartuKeluarga', 'statusHistories.actor'])->findOrFail($id);
        $this->authorize('view', $letter);

        return view('letters.admin.show', compact('letter'));
    }

    public function processRt(Request $request, $id)
    {
        $letter = PengajuanSurat::findOrFail($id);
        $this->authorize('process', $letter);

        try {
            $this->service->process($letter, auth()->user(), $request->input('notes'));
            return back()->with('success', 'Surat sedang diproses.');
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function forwardRw(Request $request, $id)
    {
        $letter = PengajuanSurat::findOrFail($id);
        $this->authorize('forwardToRw', $letter);

        try {
            $this->service->forwardToRw($letter, auth()->user(), $request->input('notes'));
            return back()->with('success', 'Surat berhasil diteruskan ke Ketua RW.');
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function complete(CompleteLetterRequest $request, $id)
    {
        $letter = PengajuanSurat::findOrFail($id);
        $this->authorize('complete', $letter);

        try {
            $this->service->complete($letter, auth()->user(), $request->nomor_surat, $request->notes);
            return back()->with('success', 'Surat berhasil diselesaikan.');
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function reject(RejectLetterRequest $request, $id)
    {
        $letter = PengajuanSurat::findOrFail($id);
        $this->authorize('reject', $letter);

        try {
            $this->service->reject($letter, auth()->user(), $request->reason);
            return back()->with('success', 'Surat berhasil ditolak.');
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
