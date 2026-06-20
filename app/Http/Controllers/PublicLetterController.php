<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\StoreLetterRequest;
use App\Models\PengajuanSurat;
use App\Services\LetterRequestService;
use App\Enums\LetterTypeEnum;

class PublicLetterController extends Controller
{
    public function create()
    {
        $types = LetterTypeEnum::cases();
        return view('letters.public.create', compact('types'));
    }

    public function store(StoreLetterRequest $request, LetterRequestService $service)
    {
        try {
            $letter = $service->submitRequest($request->validated());
            return redirect()->route('public.letters.track')
                ->with('success', 'Permohonan surat berhasil diajukan.')
                ->with('pengajuan_id', $letter->pengajuan_id)
                ->with('nik', $letter->nik);
        } catch (\DomainException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function track()
    {
        return view('letters.public.track');
    }

    public function show(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'pengajuan_id' => 'required|integer',
            'nik' => 'required|string',
        ]);

        $letter = PengajuanSurat::with(['statusHistories' => function($q) {
            $q->orderBy('changed_at', 'desc');
        }, 'pemohon'])
        ->where('pengajuan_id', $request->pengajuan_id)
        ->where('nik', $request->nik)
        ->first();

        if (!$letter) {
            return back()->withInput()->with('error', 'Surat tidak ditemukan. Pastikan NIK dan Nomor Pengajuan benar.');
        }

        return view('letters.public.show', compact('letter'));
    }
}
