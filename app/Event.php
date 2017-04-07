<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
  protected $table = "events";
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
      'name', 'description', 'date'
  ];

  protected $hidden = [
      'pivot'
  ];

  public function services(){
    return $this->belongsToMany('App\Service');
  }

  public function scopeOrder($query, $request){
    $columnName = $request->input('order-by') ?: 'date';
    $direction = $request->input('order') ?: 'asc';

    return $query->orderBy($columnName, $direction);
  }
}
