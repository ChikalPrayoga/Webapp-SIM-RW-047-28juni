<?php

namespace App\Http\Controllers;

use App\Models\ComplaintAttachment;
use Illuminate\Support\Facades\Storage;

class ComplaintAttachmentController extends Controller
{
    public function download(ComplaintAttachment $attachment)
    {
        $this->authorize('view', $attachment->complaint);

        if (!Storage::disk('local')->exists($attachment->file_path)) {
            abort(404, 'File not found');
        }

        return Storage::disk('local')->download($attachment->file_path, $attachment->file_name);
    }
}
