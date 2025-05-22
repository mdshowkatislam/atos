<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShiftSetting extends Model
{
    protected $table = "shift_settings";
    protected $fillable = [
        'in_time',
        'out_time',
    ];  
}
