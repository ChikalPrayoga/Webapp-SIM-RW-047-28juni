<?php

namespace App\Http\Controllers;

use App\Models\KartuKeluarga;
use App\Repositories\KartuKeluargaRepository;
use Illuminate\Http\Request;

class KartuKeluargaController extends Controller
{
    protected $repository;

    public function __construct(KartuKeluargaRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', KartuKeluarga::class);

        $filters = $request->only(['search', 'rt_code']);
        $kartuKeluargas = $this->repository->getPaginatedList($filters, 10);

        return view('kk.index', compact('kartuKeluargas'));
    }

    public function show($no_kk)
    {
        $kk = $this->repository->getByNoKkWithRelations($no_kk);
        
        $this->authorize('view', $kk);
        
        return view('kk.show', compact('kk'));
    }
}
