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
}
