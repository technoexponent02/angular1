<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model {

	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'comments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = array('id', 'post_id', 'user_id', 'parent_id', 'message', 'upvotes', 'downvotes');

	
	public function post()
	{
		return $this->belongsTo('App\Models\Post','post_id','id');
	}

	public function user()
	{
		return $this->belongsTo('App\Models\User','user_id','id');
	}

	public function parentComment()
	{
		return $this->belongsTo('App\Models\Comment','parent_id','id');
	}
	public function childComment()
	{
		return $this->hasMany('App\Models\Comment','parent_id','id');
	}



	
	
}
