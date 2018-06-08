<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostOpened extends Model
{
    protected $table = 'post_opened';

    public $timestamps = false;

    protected $fillable = [
    	'post_id',
    	'user_id'
    ];
}
