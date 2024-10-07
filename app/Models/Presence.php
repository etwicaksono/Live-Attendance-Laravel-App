<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presence extends Model
{
  use HasFactory;

  // Define the fields that are mass assignable
  protected $fillable = [
    'user_id',
    'check_in',
    'check_out',
    'photo_check_in',
    'photo_check_out',
    'latitude_check_in',
    'longitude_check_in',
    'latitude_check_out',
    'longitude_check_out',
  ];

  // Define any fields that should be cast to native types
  protected $casts = [
    'check_in' => 'datetime',
    'check_out' => 'datetime',
    'latitude_check_in' => 'float',
    'longitude_check_in' => 'float',
    'latitude_check_out' => 'float',
    'longitude_check_out' => 'float',
  ];

  // Define relationships

  // A presence belongs to a user
  public function user()
  {
    return $this->belongsTo(User::class);
  }
}
