<?php

namespace App;

use App\User;
use Zizaco\Entrust\EntrustRole;

class Role extends EntrustRole {
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
    public static function ownerRoleName()
    {
        return 'Owner';   
    }
    
    public static function adminRoleName()
    {
        return 'Admin';       
    }
    
    public static function volunteerRoleName()
    {
        return 'Volunteer';
    }
    public static function teacherRoleName()
    {
        return 'Teacher';
    }
    public static function studentRoleName()
    {
        return 'Student';       
    }

    public static function adminRoleNames()
    {
        return ['Owner','Admin'];

    }
    
}
