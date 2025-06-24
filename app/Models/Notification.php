<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'guard_id',
        'guard_name',
        'type',
        'message',
        'sent',
        'sent_at',
        'responded',
        'responded_at',
    ];

    protected $casts = [
        'sent'         => 'boolean',
        'responded'    => 'boolean',
        'sent_at'      => 'datetime',
        'responded_at' => 'datetime',
    ];

    public function guardRelation()
    {
        return $this->belongsTo(Guard::class);
    }
    
}
