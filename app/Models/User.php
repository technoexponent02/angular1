<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract {

    use SoftDeletes, Authenticatable, CanResetPassword;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at', 'dob'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'first_name',
        'last_name',
        'email_verification_token', 
        'sign_up_via', 'facebook_token', 
        'twitter_token',
        'reset_password_token'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    protected $appends = ['thumb_image_url', 'user_color'];

    public function getUserColorAttribute()
    {
        if (!empty($this->attributes['first_name']))
            return strtolower(substr($this->attributes['first_name'], 0, 1)). ($this->attributes['id']%2 ? 0 : 1);
        else
            return '';
    }

    public function getThumbImageUrlAttribute()
    {
        if (!empty($this->attributes['profile_image']))
            return generate_profile_image_url('profile/thumbs/' . $this->attributes['profile_image']);
        else
            return '';
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function password() 
    {
        return $this->hasOne('App\Models\Password');
    }

    /**
     * Bcrypt the given password.
     * 
     */
    public function setPasswordAttribute($value) 
    {
        $this->attributes['password'] = bcrypt($value);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function country() 
    {
        return $this->hasOne('App\Models\Country');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function state() 
    {
        return $this->belongsTo('App\Models\State', 'state_id', 'id');
    }

    public function post()
    {
        return $this->hasMany('App\Models\Post','created_by','id');
    }

    public function originalPost()
    {
        return $this->hasMany('App\Models\Post','created_by','id')->whereNull('orginal_post_id');
    }

    public function collection() 
    {
        return $this->hasMany('App\Models\Collection','user_id','id');
    }

    public function categories()
    {
        return $this->belongsToMany('App\Models\Category', 'category_follower', 'follower_id', 'category_id');
    }

    public function tags()
    {
        return $this->belongsToMany('App\Models\Tag');
    }

    public function userview() 
    {
        return $this->hasMany('App\Models\Userview','user_id','id');
    }

    public function follower() 
    {
        return $this->hasMany('App\Models\Follower','user_id','id');
    }

    public function following() 
    {
        return $this->hasMany('App\Models\Follower','follower_id','id');
    }

    public function category_follow() 
    {
        return $this->hasMany('App\Models\CategoryFollower','follower_id','id');
    }
    
}
