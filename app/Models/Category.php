<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model {

	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'categories';
	protected $hidden = ['pivot'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = array('category_name', 'parent_id', 'id', 'status');

	public function parentCat()
	{
		return $this->belongsTo('App\Models\Category','parent_id','id');
	}
	public function childCat()
	{
		return $this->hasMany('App\Models\Category','parent_id','id');
	}

	public function catPost()
	{
		return $this->hasMany('App\Models\Post','category_id','id');
	}

	public function subCatPost()
	{
		return $this->hasMany('App\Models\Post','sub_category_id','id');
	}

	public function scopeSearchByName($query, $category_name)
	{
		$category_name = str_replace(' and ', ' & ', $category_name);
		 return $query->whereRaw( 'LOWER(`category_name`) like ?', array( $category_name ) );// blockfor new logic implement for url friendly..
		//return $query->whereRaw( 'LOWER(`category_name`) like ?', array( $category_name ) )->orWhereRaw( 'LOWER(`category_name_slug`) like ?', array( str_slug($category_name )));
	}

	public function scopeSearchByNameWildCards($query, $category_name)
	{
		$category_name = str_replace(' and ', ' & ', strtolower($category_name));
		return $query->whereRaw( "LOWER(`category_name`) REGEXP '[[:<:]]" . $category_name . "[[:>:]]'");// blockfor new logic implement for url friendly..
		//return $query->whereRaw( "LOWER(`category_name`) REGEXP '[[:<:]]" . $category_name . "[[:>:]]'")->orWhereRaw( "LOWER(`category_name`) REGEXP '[[:<:]]" . str_slug($category_name) . "[[:>:]]'");
	}
	
}
