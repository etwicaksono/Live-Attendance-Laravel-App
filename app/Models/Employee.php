<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'job_role',
    ];

  // Define the relationship with the User model
  public function user()
  {
    return $this->belongsTo(User::class, 'user_id');
  }
}
