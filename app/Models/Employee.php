<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Employee extends Authenticatable
{
    use HasFactory, Notifiable;

    public $table = 'employee';

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
        if ($this->isAdmin())
            return "Admin";
        else if ($this->isStaff())
            return "Staff";
        else if ($this->isHousekeeper())
            return "Housekeeper";
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
}
