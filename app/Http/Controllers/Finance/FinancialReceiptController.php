<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\CatatanIuranWarga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class FinancialReceiptController extends Controller
{
    /**
     * Mengalirkan file bukti transfer secara aman dari private storage.
     */
    public function download(Request $request, $id)
    {
        $contribution = CatatanIuranWarga::findOrFail($id);

        // Otorisasi via Policy
        if (!Gate::allows('downloadReceipt', $contribution)) {
            abort(403, 'Anda tidak memiliki hak untuk mengakses berkas bukti transfer ini.');
        }

        if (empty($contribution->payment_proof_path)) {
            abort(404, 'Berkas bukti transfer tidak ditemukan.');
        }

        $path = storage_path('app/private/receipts/' . $contribution->payment_proof_path);

        if (!file_exists($path)) {
            abort(404, 'Berkas fisik bukti transfer tidak ditemukan di server.');
        }

        return response()->file($path);
    }
}
