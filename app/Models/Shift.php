<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    protected $fillable = [
        'name',
        'start_time',
        'end_time',
        'days',
        'is_active'
    ];

    public function guards()
    {
        return $this->hasMany(Guard::class);
    }

        protected $casts = [
        'days' => 'array',
    ];

    
}

