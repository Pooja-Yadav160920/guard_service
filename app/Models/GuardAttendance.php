<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuardAttendance extends Model
{
    protected $table = 'guard_attendances';

    protected $fillable = [
        'guard_id',
        'shift_id',
        'clock_in',
        'clock_out',
        'notes',
        'late_arrival',
        'early_leave',
        'total_assigned_time',
        'total_worked_hours',
    ];

    /**
     * Relationship to the Guard
     */
    public function assignedGuard()
    {
        return $this->belongsTo(Guard::class, 'guard_id');
    }

    /**
     * Relationship to the Shift
     */
    public function shift()
    {
        return $this->belongsTo(Shift::class, 'shift_id');
    }
}
