<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserIdSyncLog extends Model
{
    protected $fillable = [
        'chunk_size',
        'success',
        'inserted_count',
        'errors_count',
        'duplicate_existing_before',
        'duplicate_from_race_condition',
        'sent_payload',
        'api_raw_response',
        'response_status',
    ];

    protected $casts = [
        'duplicate_existing_before' => 'array',
        'duplicate_from_race_condition' => 'array',
        'sent_payload' => 'array',
        'api_raw_response' => 'array',
    ];
}
