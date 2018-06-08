<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryFollower extends Model {

	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'category_follower';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = array('id', 'category_id', 'follower_id');

	
	public function category_following() {
        return $this->belongsTo('App\Models\Category','category_id','id');
    }
    
}
