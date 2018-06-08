<?php

namespace App\Models;

use Carbon\Carbon;

use Illuminate\Database\Eloquent\Model;

class SearchKeyword extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'keyword',
        'user_id',
        'post_count',
        'channel_count',
        'tag_count',
        'location_count'
    ];

    public function scopeSinceHoursAgo($query, $hour)
    {
        return $query->where('created_at', '>=', Carbon::now()->subHours($hour));
    }
}
