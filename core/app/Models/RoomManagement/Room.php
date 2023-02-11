<?php

namespace App\Models\RoomManagement;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
  use HasFactory;

  protected $fillable = [
    'slider_imgs',
    'featured_img',
    'status',
    'max_guests',
    'bed',
    'bath',
    'rent',
    'latitude',
    'longitude',
    'address',
    'email',
    'phone',
    'is_featured',
    'avg_rating',
    'quantity'
  ];

  public function roomContent()
  {
    return $this->hasMany('App\Models\RoomManagement\RoomContent');
  }

  public function roomBooking()
  {
    return $this->hasMany('App\Models\RoomManagement\RoomBooking');
  }

  public function roomReview()
  {
    return $this->hasMany('App\Models\RoomManagement\RoomReview');
  }

  /**
   * scope a query to only those rooms whose status is show.
   *
   * @param  \Illuminate\Database\Eloquent\Builder  $query
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function scopeStatus($query)
  {
    return $query->where('status', 1);
  }
}
