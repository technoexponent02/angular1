<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class S3MoveFail extends Model
{
    /**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 's3_move_fails';

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = [
		'local_path',
		'reason',
		's3_dirname'
	];
}
