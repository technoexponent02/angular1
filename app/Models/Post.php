<?php

namespace App\Models;

use DB;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Post extends Model {

	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'posts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = [
		'created_by',
		'caption',
		'title',
		'category_id',
		'sub_category_id',
		'post_type',
		'short_description',
		'image',
		'image_url',
		'video',
		'video_poster',
		'embed_code',
		'content',
		'external_link',
		'post_date',
		'location',
		'city',
		'state',
        'country_code',
        'place_url',
		'lat',
		'lon',
		'source',
		'likes',
		'points',
		'upvotes',
		'allow_comment',
		'allow_share',
		'privacy_id',
	];

	/**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['people_here'];

    protected $appends = ['people_here'];

    public function getPeopleHereAttribute()
    {
        return DB::select('SELECT COUNT(*) AS total FROM (SELECT MAX(id),IFNULL(user_id, ip) as unq_user FROM `post_opened` WHERE `post_id` = ? GROUP BY unq_user) AS uTbl', [$this->attributes['id']])[0]->total;
    }

	public function category()
	{
		return $this->belongsTo('App\Models\Category','category_id','id');
	}

	public function subCategory()
	{
		return $this->belongsTo('App\Models\Category','sub_category_id','id');
	}

    public function country()
    {
        return $this->belongsTo('App\Models\Country','country_code','country_code');
    }

	public function tags()
	{
		return $this->belongsToMany('App\Models\Tag');
	}

	
	

	public function collections()
	{
		return $this->belongsToMany('App\Models\Collection');
	}

	public function privacy()
	{
		return $this->belongsTo('App\Models\Privacy');
	}

	public function user()
	{
		return $this->belongsTo('App\Models\User', 'created_by', 'id');
	}

	public function userview() {
        return $this->hasMany('App\Models\Userview','user_id','created_by');
    }

    public function follower() {
        return $this->hasMany('App\Models\Follower','user_id','created_by');
    }

    public function following() {
        return $this->hasMany('App\Models\Follower','follower_id','created_by');
    }

    public function activities()
	{
		return $this->belongsToMany('App\Models\Activity')->withPivot('user_id');
	}

    public function comment() {
        return $this->hasMany('App\Models\Comment','post_id','id');
    }

	public function parentPostUser()
	{
		return $this->belongsTo('App\Models\User', 'parent_post_user_id', 'id');
	}

	public function orginalPost()
	{
		return $this->belongsTo('App\Models\Post','orginal_post_id','id');
	}

    public function featurePhotoDetail() {
        return $this->hasOne('App\Models\FeaturePhotoDetail','post_id','id');
    }

	public function scopeSinceDaysAgo($query, $day)
	{
		return $query->where('created_at', '>=', Carbon::now()->subDays($day));
	}

    public function scopeSearchByAddress($query, $field, $value)
    {
        $v1 = str_replace('-', ' ', strtolower($value));
        $v2 = str_replace(' and ', ' & ', strtolower($value));
        return $query->whereRaw( "(`$field` like ? OR `$field` LIKE ?)", array( $v1,  $v2) );
    }

	/**
	 * Remove reported posts by user.
	 */
	public function scopeRemoveReported($query, $user_id)
	{
		return $query->whereNotIn('id', function($query) use ($user_id) {
			$query->select('post_id')->from('post_report')->where('user_id', $user_id);
		});
	}


	

}
