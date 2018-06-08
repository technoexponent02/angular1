<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Postview extends Model {

	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'postviews';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = array('id', 'post_id', 'viewer_id', 'views', 'view_time');

	
	public function post()
	{
		return $this->belongsTo('App\Models\Post', 'post_id', 'id');
	}

	

	
	
}
