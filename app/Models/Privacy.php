<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Privacy extends Model {

	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'privacies';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = array('id', 'privacy_name', 'status');

	public function parentCat()
	{
		return $this->belongsTo('App\Models\Post','privacy_id','id');
	}
}
