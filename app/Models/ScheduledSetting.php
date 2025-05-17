<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduledSetting extends Model
{
    protected $table = 'scheduled_settings';

    protected $fillable = [
        'key',
        'value',
        'db_location'
    ];

    public $timestamps = true;

 
}
