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
}
