<?php

namespace App\Http\Controllers;

use App\Models\AnggotaKeluarga;
use App\Repositories\WargaRepository;
use Illuminate\Http\Request;

class WargaController extends Controller
{
    protected $repository;

    public function __construct(WargaRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', AnggotaKeluarga::class);

        $filters = $request->only(['search', 'rt_code']);
        $wargas = $this->repository->getPaginatedList($filters, 10);

        return view('warga.index', compact('wargas'));
    }

    public function show($nik)
    {
        $warga = $this->repository->getByNikWithRelations($nik);

        $this->authorize('view', $warga);
        
        return view('warga.show', compact('warga'));
    }
}
