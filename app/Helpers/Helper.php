<?php

namespace App\Helpers;

use App\Models\User;

class Helper
{
  public static function hasRole(User $user,string $roles)
  {
    return in_array($user->role, explode("|", $roles));
  }

}
