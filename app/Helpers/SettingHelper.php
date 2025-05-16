<?php

namespace App\Helpers;

use App\Models\Setting;

class SettingHelper
{
    public static function companyName(): string
    {
        return Setting::first()->company_name ?? 'App';
    }
}
