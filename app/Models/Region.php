<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = [
		'id',
		'name',
		'slug_name'
	];

	public $timestamps = false;

	public function country() {
        return $this->hasMany('App\Models\Country', 'region_id', 'id');
    }
}
