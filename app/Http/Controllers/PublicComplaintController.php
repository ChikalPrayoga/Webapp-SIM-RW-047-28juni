<?php

namespace App\Http\Controllers;

use App\Models\LogLaporanAspirasi;
use App\Http\Requests\StoreComplaintRequest;
use App\Http\Requests\TrackComplaintRequest;
use App\Services\ComplaintService;
use Illuminate\Http\Request;

class PublicComplaintController extends Controller
{
    protected $service;

    public function __construct(ComplaintService $service)
    {
        $this->service = $service;
    }

    // View: Form Submit Complaint
    public function create()
    {
        return view('complaints.public.create');
    }

    // Action: Store Complaint
    public function store(StoreComplaintRequest $request)
    {
        $files = $request->file('attachments') ?? [];
        
        $complaint = $this->service->submitComplaint($request->validated(), $files);

        return back()->with('success', [
            'message' => 'Laporan berhasil dikirim!',
            'ticket_id' => $complaint->aspirasi_id,
            'instruction' => 'Harap simpan Nomor Tiket di atas. Anda dapat menggunakannya bersama NIK Anda untuk melacak status laporan di halaman Lacak Laporan.'
        ]);
    }

    // View: Form Track Complaint
    public function trackForm()
    {
        return view('complaints.public.track');
    }

    // Action: Track Complaint
    public function track(TrackComplaintRequest $request)
    {
        // Find the complaint where aspirasi_id and nik matches
        $complaint = LogLaporanAspirasi::with(['statusHistories.actor', 'attachments'])
            ->where('aspirasi_id', $request->aspirasi_id)
            ->where('nik', $request->nik)
            ->first();

        if (!$complaint) {
            return back()->withErrors(['nik' => 'Nomor Tiket dan NIK tidak cocok.'])->withInput();
        }

        return view('complaints.public.show', compact('complaint'));
    }
}
