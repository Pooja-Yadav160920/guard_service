<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = ['name', 'address', 'latitude', 'longitude'];

    public function guards()
    {
        return $this->hasMany(Guard::class);
    }
}
