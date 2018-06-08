<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model {

	/**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;


	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'activities';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = array('id', 'activity_name');

	public function posts()
	{
		return $this->belongsToMany('App\Models\Post')->withPivot('user_id');
	}
	
}
