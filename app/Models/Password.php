<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Password extends Model {

    use SoftDeletes;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];


    /**
     * User associated with a password.
     *
     * @return App\Http\Models\Users
     */
    public function user() {
        return $this->belongsTo('App\Models\User');
    }
}
