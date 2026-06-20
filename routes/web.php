<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('public.portal');
});

Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/under-construction', function() {
    return view('placeholder');
})->middleware(['auth', 'verified'])->name('placeholder');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Resident Management Routes
    Route::resource('kk', \App\Http\Controllers\KartuKeluargaController::class)->only(['index', 'show']);
    Route::resource('warga', \App\Http\Controllers\WargaController::class)->only(['index', 'show']);

    // Modul Manajemen Laporan (Pengurus)
    Route::prefix('complaints')->name('complaints.')->group(function () {
        Route::get('/', [App\Http\Controllers\ComplaintController::class, 'index'])->name('index');
        Route::get('/{complaint}', [App\Http\Controllers\ComplaintController::class, 'show'])->name('show');
        Route::put('/{complaint}/status', [App\Http\Controllers\ComplaintController::class, 'updateStatus'])->name('updateStatus');
        Route::post('/{complaint}/assign', [App\Http\Controllers\ComplaintController::class, 'assign'])->name('assign');
    });

    // Modul Persuratan (Pengurus)
    Route::prefix('letters')->name('letters.')->group(function () {
        Route::get('/', [App\Http\Controllers\AdminLetterController::class, 'index'])->name('index');
        Route::get('/{letter}', [App\Http\Controllers\AdminLetterController::class, 'show'])->name('show');
        Route::post('/{letter}/process', [App\Http\Controllers\AdminLetterController::class, 'processRt'])->name('process');
        Route::post('/{letter}/forward', [App\Http\Controllers\AdminLetterController::class, 'forwardRw'])->name('forward');
        Route::post('/{letter}/complete', [App\Http\Controllers\AdminLetterController::class, 'complete'])->name('complete');
        Route::post('/{letter}/reject', [App\Http\Controllers\AdminLetterController::class, 'reject'])->name('reject');
    });

    Route::get('/complaints/attachments/{attachment}', [\App\Http\Controllers\ComplaintAttachmentController::class, 'download'])->name('complaints.attachments.download');
});

/*
|--------------------------------------------------------------------------
| Admin Workspace (SUPER_ADMIN)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'can:manage_system'])->prefix('admin')->name('admin.')->group(function () {
    // User Management
    Route::resource('users', \App\Http\Controllers\Admin\AdminUserController::class);
    Route::post('users/{user}/toggle-status', [\App\Http\Controllers\Admin\AdminUserController::class, 'toggleStatus'])->name('users.toggle-status');
    
    // Role & Permission Matrix (Read Only)
    Route::get('roles', [\App\Http\Controllers\Admin\AdminRoleController::class, 'index'])->name('roles.index');
    Route::get('permissions', [\App\Http\Controllers\Admin\AdminPermissionController::class, 'index'])->name('permissions.index');
    
    // System Settings & Audit Log
    Route::get('settings', [\App\Http\Controllers\Admin\AdminSettingController::class, 'index'])->name('settings.index');
    Route::get('audit-log', [\App\Http\Controllers\Admin\AdminAuditLogController::class, 'index'])->name('audit-log.index');
});


/*
|--------------------------------------------------------------------------
| Portal Warga — Gateway Terpadu
|--------------------------------------------------------------------------
*/
Route::get('/layanan', [App\Http\Controllers\PublicPortalController::class, 'index'])->name('public.portal');

/*
|--------------------------------------------------------------------------
| Portal Warga (Modul Laporan / Pengaduan)
|--------------------------------------------------------------------------
*/
Route::prefix('layanan/laporan')->name('public.complaints.')->group(function () {
    Route::get('/', [App\Http\Controllers\PublicComplaintController::class, 'create'])->name('create');
    Route::post('/', [App\Http\Controllers\PublicComplaintController::class, 'store'])->name('store');
    Route::get('/track', [App\Http\Controllers\PublicComplaintController::class, 'trackForm'])->name('track');
    Route::post('/track', [App\Http\Controllers\PublicComplaintController::class, 'track'])->name('track.post');
});

/*
|--------------------------------------------------------------------------
| Portal Warga (Modul Persuratan)
|--------------------------------------------------------------------------
*/
Route::prefix('layanan/surat')->name('public.letters.')->group(function () {
    Route::get('/', [App\Http\Controllers\PublicLetterController::class, 'create'])->name('create');
    Route::post('/', [App\Http\Controllers\PublicLetterController::class, 'store'])->name('store');
    Route::get('/track', [App\Http\Controllers\PublicLetterController::class, 'track'])->name('track');
    Route::post('/track', [App\Http\Controllers\PublicLetterController::class, 'show'])->name('show');
});

require __DIR__.'/auth.php';
