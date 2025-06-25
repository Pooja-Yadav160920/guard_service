<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Authenticatable;
use Laravel\Sanctum\HasApiTokens;

use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

class Guard extends Model implements AuthenticatableContract
{
    use HasApiTokens, Authenticatable;

    protected $fillable = [
        'name',
        'username',
        'emp_code',
        'email',
        'password',
        'phone',
        'address',
        'emergency_contact',
        'emergency_phone',
        'profile_photo',
        'shift_id',
        'location_id',
        'status'
    ];

    protected $hidden = ['password'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function attendanceLogs()
    {
        return $this->hasMany(GuardAttendance::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}