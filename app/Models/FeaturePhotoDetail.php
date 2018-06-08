<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeaturePhotoDetail extends Model
{
    protected $table = 'feature_photo_details';

    public $timestamps = false;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */

    protected $fillable = [
        'post_id',
        'thumb_width',
        'thumb_height',
        'width',
        'height',
        'data_url'
    ];


}
