<?php

namespace App\Http\Controllers;

use App\Models\LogLaporanAspirasi;
use App\Repositories\ComplaintRepository;
use App\Repositories\ComplaintHistoryRepository;
use App\Services\ComplaintService;
use App\Services\ComplaintAssignmentService;

use App\Http\Requests\UpdateComplaintStatusRequest;
use App\Enums\ComplaintStatusEnum;
use App\Enums\ComplaintCategoryEnum;
use Illuminate\Http\Request;

class ComplaintController extends Controller
{
    protected $repository;
    protected $historyRepository;
    protected $service;
    protected $assignmentService;

    public function __construct(
        ComplaintRepository $repository,
        ComplaintHistoryRepository $historyRepository,
        ComplaintService $service,
        ComplaintAssignmentService $assignmentService
    ) {
        $this->repository = $repository;
        $this->historyRepository = $historyRepository;
        $this->service = $service;
        $this->assignmentService = $assignmentService;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', LogLaporanAspirasi::class);

        $complaints = $this->repository->getAllPaginated($request->all());
        
        return view('complaints.admin.index', compact('complaints'));
    }



    public function show(LogLaporanAspirasi $complaint)
    {
        $this->authorize('view', $complaint);

        $complaint->load(['pelapor', 'assignments.assignedTo', 'attachments']);
        $histories = $this->historyRepository->getHistoriesByComplaintId($complaint->aspirasi_id);
        
        $usersList = \App\Models\User::where('status', \App\Enums\UserStatusEnum::ACTIVE->value)->get();

        return view('complaints.admin.show', compact('complaint', 'histories', 'usersList'));
    }

    public function updateStatus(UpdateComplaintStatusRequest $request, LogLaporanAspirasi $complaint)
    {
        $status = ComplaintStatusEnum::from($request->status);
        $category = $request->category ? ComplaintCategoryEnum::from($request->category) : null;
        
        $this->service->updateStatus(
            $complaint, 
            $status, 
            $request->user(), 
            $request->notes,
            $category,
            $request->priority
        );

        return back()->with('success', 'Status laporan berhasil diperbarui.');
    }

    public function assign(Request $request, LogLaporanAspirasi $complaint)
    {
        $this->authorize('create', \App\Models\ComplaintAssignment::class);

        $request->validate([
            'assigned_to_user_id' => 'required|exists:users,user_id',
            'notes' => 'nullable|string'
        ]);

        $assignment = $this->assignmentService->assignComplaint(
            $complaint,
            $request->assigned_to_user_id,
            $request->user()->user_id,
            $request->notes
        );

        return back()->with('success', 'Tugas berhasil didelegasikan ke staf yang dipilih.');
    }
}
