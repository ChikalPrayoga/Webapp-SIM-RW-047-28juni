<?php

namespace App\Http\Controllers;

use App\Enums\LetterTypeEnum;

class PublicPortalController extends Controller
{
    /**
     * Tampilkan halaman Portal Layanan Warga terpadu.
     * Hanya bertindak sebagai gateway UI, tanpa business logic.
     */
    public function index()
    {
        $letterTypes = LetterTypeEnum::cases();

        return view('portal.index', compact('letterTypes'));
    }
}
