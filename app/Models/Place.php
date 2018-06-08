<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = [
		'place_level_1',
		'place_level_2',
		'place_level_3',
		'place_url',
		'indexed'
	];
}
