<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\FinancialTransaction;
use App\Http\Requests\StoreTransactionRequest;
use App\Services\LedgerService;
use App\Enums\TransactionType;
use App\Enums\TransactionCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class FinancialTransactionController extends Controller
{
    protected LedgerService $ledgerService;

    public function __construct(LedgerService $ledgerService)
    {
        $this->ledgerService = $ledgerService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (!Gate::allows('viewAny', FinancialTransaction::class)) {
            abort(403, 'Unauthorized');
        }

        $user = $request->user();
        $userArea = $user->position?->area_code; // Null or RT code

        $query = FinancialTransaction::query();

        // 1. Scoping
        if (!empty($userArea)) {
            // Ketua RT: Hanya transaksi RT sendiri atau Kas RW (rt_code === null)
            $query->where(function($q) use ($userArea) {
                $q->where('rt_code', $userArea)
                  ->orWhereNull('rt_code');
            });
        }

        // 2. Filters
        if ($request->has('category') && $request->category !== '') {
            $query->where('category', $request->category);
        }
        if ($request->has('type') && $request->type !== '') {
            $query->where('transaction_type', $request->type);
        }
        if ($request->has('search') && $request->search !== '') {
            $query->where(function($q) use ($request) {
                $q->where('transaction_number', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%");
            });
        }

        $transactions = $query->orderBy('transaction_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $categories = TransactionCategory::cases();
        $types = TransactionType::cases();

        return view('admin.finance.transactions.index', compact(
            'transactions',
            'categories',
            'types',
            'userArea'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        if (!Gate::allows('create', FinancialTransaction::class)) {
            abort(403, 'Unauthorized');
        }

        $type = $request->input('type', 'INCOME');
        if (!in_array($type, ['INCOME', 'EXPENSE'])) {
            $type = 'INCOME';
        }

        $categories = collect(TransactionCategory::cases())->reject(function($cat) {
            return $cat === TransactionCategory::ADJUSTMENT; // Reversal is automated
        });

        return view('admin.finance.transactions.create', compact('type', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTransactionRequest $request)
    {
        $data = $request->validated();
        $user = $request->user();
        $userArea = $user->position?->area_code;

        // Scoping rt_code berdasarkan jabatan:
        // Bendahara RW -> Kas RW (rt_code = null)
        // Ketua RT -> Kas RT (rt_code = area_code)
        $data['rt_code'] = !empty($userArea) ? $userArea : null;
        $data['created_by_user_id'] = $user->user_id;

        try {
            if ($data['transaction_type'] === TransactionType::INCOME->value) {
                $this->ledgerService->createIncome($data);
            } else {
                $this->ledgerService->createExpense($data);
            }

            return redirect()->route('finances.transactions.index')
                ->with('success', 'Transaksi Kas berhasil dicatat.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal mencatat transaksi: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(FinancialTransaction $transaction)
    {
        if (!Gate::allows('view', $transaction)) {
            abort(403, 'Unauthorized');
        }

        return view('admin.finance.transactions.show', compact('transaction'));
    }

    /**
     * Melakukan reversal (koreksi) atas transaksi kas.
     */
    public function reverse(Request $request, $id)
    {
        $transaction = FinancialTransaction::findOrFail($id);

        if (!Gate::allows('reverse', $transaction)) {
            abort(403, 'Anda tidak memiliki wewenang untuk mengoreksi transaksi ini.');
        }

        $request->validate([
            'reason' => 'required|string|min:5|max:255',
        ]);

        try {
            $this->ledgerService->createAdjustment(
                $transaction->transaction_id,
                $request->input('reason'),
                $request->user()->user_id
            );

            return redirect()->route('finances.transactions.index')
                ->with('success', 'Transaksi penyesuaian (reversal) berhasil diposting.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memproses koreksi: ' . $e->getMessage());
        }
    }
}
