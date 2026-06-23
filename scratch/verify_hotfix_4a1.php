<?php
/**
 * HOTFIX 4A.1 - Activity Log Integrity Constraint Verification
 * 
 * Tests:
 * 1. LetterSubmitted event -> ActivityLog created with correct fields
 * 2. LetterStatusUpdated event (COMPLETED) -> ActivityLog created
 * 3. LetterStatusUpdated event (REJECTED) -> ActivityLog created
 * 4. Global field audit
 * 5. Activity type distribution
 */

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ActivityLog;
use App\Models\PengajuanSurat;
use App\Models\AnggotaKeluarga;
use App\Models\LetterStatusHistory;
use App\Enums\LetterStatusEnum;
use Illuminate\Support\Facades\DB;

echo "=== HOTFIX 4A.1 VERIFICATION ===\n\n";

$baselineCount = ActivityLog::count();
echo "Baseline ActivityLog count: {$baselineCount}\n\n";

$allPassed = true;

// ============================================================
// TEST 1: LetterSubmitted Event
// ============================================================
echo "--- TEST 1: LetterSubmitted Event ---\n";

$pemohon = AnggotaKeluarga::first();
if (!$pemohon) {
    echo "FAIL: No AnggotaKeluarga found for testing\n\n";
    $allPassed = false;
} else {
    try {
        $letterService = new \App\Services\LetterRequestService();
        
        // Use SURAT_KETERANGAN_DOMISILI (does NOT require RW approval)
        $letter = $letterService->submitRequest([
            'nik' => $pemohon->nik,
            'jenis_surat' => 'SURAT_KETERANGAN_DOMISILI',
            'keperluan' => 'Test HOTFIX 4A.1 - Letter Submit',
        ]);

        $log = ActivityLog::where('activity_type', 'SUBMIT_LETTER')
            ->where('entity_id', $letter->pengajuan_id)
            ->first();

        if ($log) {
            echo "PASS: ActivityLog created for LetterSubmitted\n";
            echo "  activity_type: {$log->activity_type}\n";
            echo "  entity_type:   {$log->entity_type}\n";
            echo "  entity_id:     {$log->entity_id}\n";
            echo "  description:   {$log->description}\n";
            echo "  ip_address:    {$log->ip_address}\n";
        } else {
            echo "FAIL: ActivityLog NOT created for LetterSubmitted\n";
            $allPassed = false;
        }
    } catch (\Exception $e) {
        echo "FAIL: Exception during LetterSubmitted test\n";
        echo "  " . $e->getMessage() . "\n";
        $allPassed = false;
    }
    echo "\n";

    // ============================================================
    // TEST 2: LetterStatusUpdated Event (COMPLETED path)
    // ============================================================
    echo "--- TEST 2: LetterStatusUpdated (COMPLETED) ---\n";

    $ketuaRT = \App\Models\User::whereHas('role', function($q) {
        $q->where('role_name', 'KETUA_RT');
    })->first();

    if ($ketuaRT) {
        try {
            auth()->login($ketuaRT);

            $approvalService = new \App\Services\LetterApprovalService();
            
            // Process: SUBMITTED -> RT_REVIEW
            $approvalService->process($letter, $ketuaRT, 'Test processing');
            
            // SURAT_KETERANGAN_DOMISILI does NOT need RW: RT_REVIEW -> COMPLETED
            $letter->refresh();
            $approvalService->complete($letter, $ketuaRT, 'TEST-HOTFIX-001', 'Test completion');

            $completedLog = ActivityLog::where('activity_type', 'COMPLETED_LETTER')
                ->where('entity_id', $letter->pengajuan_id)
                ->first();

            if ($completedLog) {
                echo "PASS: ActivityLog created for COMPLETED_LETTER\n";
                echo "  activity_type: {$completedLog->activity_type}\n";
                echo "  entity_type:   {$completedLog->entity_type}\n";
                echo "  entity_id:     {$completedLog->entity_id}\n";
                echo "  description:   {$completedLog->description}\n";
            } else {
                echo "FAIL: ActivityLog NOT created for COMPLETED_LETTER\n";
                $allPassed = false;
            }

            auth()->logout();
        } catch (\Exception $e) {
            echo "FAIL: Exception during COMPLETED test\n";
            echo "  " . $e->getMessage() . "\n";
            $allPassed = false;
        }
    } else {
        echo "SKIP: No Ketua RT user found\n";
    }
    echo "\n";

    // ============================================================
    // TEST 3: LetterStatusUpdated Event (REJECTED path)
    // ============================================================
    echo "--- TEST 3: LetterStatusUpdated (REJECTED) ---\n";

    try {
        // Create another letter
        $letter2 = $letterService->submitRequest([
            'nik' => $pemohon->nik,
            'jenis_surat' => 'SURAT_KETERANGAN_DOMISILI',
            'keperluan' => 'Test HOTFIX 4A.1 - Letter Reject',
        ]);

        if ($ketuaRT) {
            auth()->login($ketuaRT);
            $approvalService = new \App\Services\LetterApprovalService();

            // Reject from SUBMITTED
            $approvalService->reject($letter2, $ketuaRT, 'Test rejection for hotfix');

            $rejectedLog = ActivityLog::where('activity_type', 'REJECTED_LETTER')
                ->where('entity_id', $letter2->pengajuan_id)
                ->first();

            if ($rejectedLog) {
                echo "PASS: ActivityLog created for REJECTED_LETTER\n";
                echo "  activity_type: {$rejectedLog->activity_type}\n";
                echo "  entity_type:   {$rejectedLog->entity_type}\n";
                echo "  entity_id:     {$rejectedLog->entity_id}\n";
                echo "  description:   {$rejectedLog->description}\n";
            } else {
                echo "FAIL: ActivityLog NOT created for REJECTED_LETTER\n";
                $allPassed = false;
            }

            auth()->logout();
        } else {
            echo "SKIP: No Ketua RT user found\n";
        }
    } catch (\Exception $e) {
        echo "FAIL: Exception during REJECTED test\n";
        echo "  " . $e->getMessage() . "\n";
        $allPassed = false;
    }
    echo "\n";
}

// ============================================================
// TEST 4: Global Field Audit
// ============================================================
echo "--- TEST 4: Global ActivityLog Field Audit ---\n";

$newCount = ActivityLog::count();
echo "New ActivityLog count: {$newCount} (added " . ($newCount - $baselineCount) . ")\n";

$nullActivityType = DB::table('activity_logs')->whereNull('activity_type')->count();
echo "Records with NULL activity_type: {$nullActivityType}\n";

if ($nullActivityType > 0) {
    echo "FAIL: Found records with NULL activity_type!\n";
    $allPassed = false;
} else {
    echo "PASS: All activity_type values are non-null\n";
}
echo "\n";

// ============================================================
// TEST 5: Activity Type Distribution
// ============================================================
echo "--- TEST 5: Activity Type Distribution ---\n";
$distribution = DB::table('activity_logs')
    ->select('activity_type', DB::raw('count(*) as count'))
    ->groupBy('activity_type')
    ->get();

foreach ($distribution as $row) {
    echo "  {$row->activity_type}: {$row->count}\n";
}
echo "\n";

// ============================================================
// FINAL STATUS
// ============================================================
echo "==========================================\n";
if ($allPassed) {
    echo "ALL TESTS PASSED - HOTFIX VERIFIED\n";
} else {
    echo "SOME TESTS FAILED - HOTFIX NOT VERIFIED\n";
}
echo "==========================================\n";
