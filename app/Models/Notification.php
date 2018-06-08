<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model {

	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'notifications';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = array('id', 'activity_id', 'post_id', 'post_user_id', 'user_id', 'notified');

	
	public function postUser()
	{
		return $this->belongsTo('App\Models\User', 'post_user_id', 'id');
	}

	public function post()
    {
        return $this->belongsTo('App\Models\Post', 'post_id', 'id');
    }

	public function user()
	{
		return $this->belongsTo('App\Models\User', 'user_id', 'id');
	}
	
}
