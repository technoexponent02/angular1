<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Follower extends Model {

	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'followers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = ['id', 'user_id', 'follower_id', 'status'];

	/**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['type'];

	
	public function following_by() {
        return $this->belongsTo('App\Models\User','user_id','id');
    }

    public function followed_by() {
        return $this->belongsTo('App\Models\User','follower_id','id');
    }

	/**
     * Get the type for notification.
     *
     * @return bool
     */
    public function getTypeAttribute()
    {
        return 'follow';
    }

	
	
}
