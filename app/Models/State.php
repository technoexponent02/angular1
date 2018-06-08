<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class State extends Model {


	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = array('country_id');

	/**
	 * States belongs to the country
	 * 
	 * @return App\Models\Country
	 */
	public function country() {
		return $this->belongsTo('App\Models\Country');
	}
}
