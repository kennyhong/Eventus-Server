<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
  protected $table = "services";
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
      'name', 'cost', 'tags'
  ];

  protected $hidden = [
      'pivot'
  ];

  public function events(){
    return $this->belongsToMany('App\Event');
  }

  public function serviceTags(){
    return $this->belongsToMany('App\ServiceTag');
  }

  public function scopeFilterByIds($query, $request){
    if(! $ids = $request->input('filter-ids')) return $query;

    $ids = explode(",", $ids);

    return $query->whereIn('id', $ids);
  }

  public function scopeFilterExceptIds($query, $request){
     if(! $ids = $request->input('filter-except-ids')) return $query;

     $ids = explode(",", $ids);

     return $query->whereNotIn('id', $ids);
  }

  public function scopeFilterByTagIds($query, $request){
    if(! $ids = $request->input('filter-tag-ids')) return $query;

    $ids = explode(",", $ids);

    return $query->whereHas('serviceTags', function($query) use ($ids){
        $query->whereIn('id', $ids);
    });
  }

  public function scopeOrder($query, $request){
    $columnName = $request->input('order-by') ?: 'name';
    $direction = $request->input('order') ?: 'asc';

    return $query->orderBy($columnName, $direction);
  }
}
