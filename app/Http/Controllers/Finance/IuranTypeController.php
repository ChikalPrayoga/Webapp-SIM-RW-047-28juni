<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\IuranType;
use App\Http\Requests\StoreIuranTypeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class IuranTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (!Gate::allows('viewAny', IuranType::class)) {
            abort(403, 'Unauthorized');
        }

        $search = $request->input('search');
        $query = IuranType::query();

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $iuranTypes = $query->orderBy('is_active', 'desc')
            ->orderBy('name')
            ->paginate(10);

        return view('admin.finance.iuran-types.index', compact('iuranTypes', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!Gate::allows('create', IuranType::class)) {
            abort(403, 'Unauthorized');
        }

        return view('admin.finance.iuran-types.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreIuranTypeRequest $request)
    {
        $data = $request->validated();
        IuranType::create($data);

        return redirect()->route('finances.iuran-types.index')
            ->with('success', 'Jenis Iuran baru berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(IuranType $iuranType)
    {
        if (!Gate::allows('view', $iuranType)) {
            abort(403, 'Unauthorized');
        }

        return view('admin.finance.iuran-types.show', compact('iuranType'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(IuranType $iuranType)
    {
        if (!Gate::allows('update', $iuranType)) {
            abort(403, 'Unauthorized');
        }

        return view('admin.finance.iuran-types.edit', compact('iuranType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreIuranTypeRequest $request, IuranType $iuranType)
    {
        if (!Gate::allows('update', $iuranType)) {
            abort(403, 'Unauthorized');
        }

        $data = $request->validated();
        $iuranType->update($data);

        return redirect()->route('finances.iuran-types.index')
            ->with('success', 'Jenis Iuran berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(IuranType $iuranType)
    {
        if (!Gate::allows('delete', $iuranType)) {
            abort(403, 'Unauthorized');
        }

        // Cek jika sudah direferensikan oleh pembayaran warga
        if ($iuranType->payments()->exists()) {
            // Menonaktifkan saja (is_active = false)
            $iuranType->update(['is_active' => false]);
            return redirect()->route('finances.iuran-types.index')
                ->with('success', 'Jenis Iuran sudah memiliki riwayat pembayaran warga. Status telah dinonaktifkan.');
        }

        $iuranType->delete();

        return redirect()->route('finances.iuran-types.index')
            ->with('success', 'Jenis Iuran berhasil dihapus.');
    }
}
