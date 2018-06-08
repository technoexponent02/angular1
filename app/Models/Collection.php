<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Collection extends Model {

	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'collections';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = array('id', 'user_id', 'collection_name', 'collection_text', 'status');

	
	public function user()
	{
		return $this->belongsTo('App\Models\User', 'user_id', 'id');
	}

	public function post()
    {
        return $this->belongsToMany('App\Models\Post');
    }
	
}
