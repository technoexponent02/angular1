<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommentNotification extends Model {

	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'comment_notifications';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = array('id', 'activity_id', 'comment_id', 'notified_user_id', 'user_id', 'notified');

	
	public function notifiedUser()
	{
		return $this->belongsTo('App\Models\User', 'notified_user_id', 'id');
	}

	public function comment()
    {
        return $this->belongsTo('App\Models\Comment', 'comment_id', 'id');
    }

	public function user()
	{
		return $this->belongsTo('App\Models\User', 'user_id', 'id');
	}
	
}
