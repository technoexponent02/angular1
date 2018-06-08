<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $fillable = [
        'original_name',
        'save_name',
        'is_draft',
        'schedule_remove'
    ];
}
