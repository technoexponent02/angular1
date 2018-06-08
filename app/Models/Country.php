<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model {

	protected $fillable = [
        'country_name',
		'country_name_slug',
		'country_code',
        'region_code',
        'continent'
	];

    public $timestamps = false;

    public function region()
    {
        return $this->belongsTo('App\Models\Region');
    }
}
