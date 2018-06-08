<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model {

	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'tags';
	protected $hidden = ['pivot'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = array('id', 'tag_name','tag_text','question_tag','question_tag_created_at', 'status');

	public function posts()
	{
		return $this->belongsToMany('App\Models\Post');
	}
	/*******add (20-02-18)start  *******/
	public function postsTag()
    {
        return $this->belongsToMany('App\Models\Post')->selectRaw('count(posts.id) as count');
	}
	/*******add (20-02-18)end *******/


	public function users()
	{
		return $this->belongsToMany('App\Models\User');
	}
}
