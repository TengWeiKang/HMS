<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Employee extends Authenticatable
{
    use HasFactory, Notifiable;

    public $table = 'employee';
    protected $guard = "employee";

    const ROLES = [
        0 => "Admin",
        1 => "Staff",
        2 => "Housekeeper"
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'email',
        'phone',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function role() {
        return self::ROLES[$this->role];
    }

    public function isAdmin() {
        return ($this->role == 0) ? true : false;
    }

    public function isStaff() {
        return ($this->role == 1) ? true : false;
    }

    public function isHousekeeper() {
        return ($this->role == 2) ? true : false;
    }

    public function isAccessible(...$roles) {
        return in_array(Str::lower(self::ROLES[$this->role]), $roles);
    }
}
