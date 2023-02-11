<?php

namespace App\Models\PackageManagement;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
  use HasFactory;

  protected $fillable = [
    'slider_imgs',
    'featured_img',
    'plan_type',
    'max_persons',
    'number_of_days',
    'pricing_type',
    'package_price',
    'email',
    'phone',
    'avg_rating',
    'is_featured'
  ];

  public function packageContent()
  {
    return $this->hasMany('App\Models\PackageManagement\PackageContent');
  }

  public function packageLocationList()
  {
    return $this->hasMany('App\Models\PackageManagement\PackageLocation');
  }

  public function packagePlanList()
  {
    return $this->hasMany('App\Models\PackageManagement\PackagePlan');
  }

  public function tourPackageBooking()
  {
    return $this->hasMany('App\Models\PackageManagement\PackageBooking');
  }

  public function packageReview()
  {
    return $this->hasMany('App\Models\PackageManagement\PackageReview');
  }
}
