<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminSettingController extends Controller
{
    public function index()
    {
        // Simple mock array for MVP settings. 
        // Real implementation would pull from a settings table or .env
        $settings = [
            'rw_name' => 'RW 047',
            'rw_address' => 'Jl. Merdeka Blok C No. 12, Kelurahan Harapan Baru',
            'phone' => '+62 812-3456-7890',
            'email' => 'contact@rw047.com',
            'timezone' => 'Asia/Jakarta',
            'maintenance_mode' => false,
        ];

        return view('admin.settings.index', compact('settings'));
    }
}
