<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SystemSetting;

class SystemSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['setting_key' => 'app_name', 'setting_value' => 'SIM RW 047', 'description' => 'Application Name'],
            ['setting_key' => 'mandatory_contribution_amount', 'setting_value' => '50000', 'description' => 'Mandatory monthly contribution per KK'],
        ];

        foreach ($settings as $setting) {
            SystemSetting::firstOrCreate(['setting_key' => $setting['setting_key']], $setting);
        }
    }
}
