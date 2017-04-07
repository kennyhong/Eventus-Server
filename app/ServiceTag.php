<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServiceTag extends Model
{
  protected $table = "service_tags";
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
      'name'
  ];

  protected $hidden = [
      'pivot'
  ];

  public function services(){
    return $this->belongsToMany('App\Service');
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

  public function scopeOrder($query, $request){
    $columnName = $request->input('order-by') ?: 'name';
    $direction = $request->input('order') ?: 'asc';

    return $query->orderBy($columnName, $direction);
  }
}
