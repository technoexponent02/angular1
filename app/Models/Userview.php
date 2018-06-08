<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Userview extends Model {

	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'userviews';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = array('id', 'user_id', 'viewer_id', 'views', 'view_time','ip_address');

	
	public function user()
	{
		return $this->belongsTo('App\Models\User', 'user_id', 'id');
	}

	

	
	
}
