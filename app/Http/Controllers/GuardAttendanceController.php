<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GuardAttendance;
use App\Models\Guard;
use Carbon\Carbon;

class GuardAttendanceController extends Controller
{
    public function listAttendances(Request $request)
    {
        if (!checkPermission('show-attendance')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $query = GuardAttendance::query();

        if ($request->has('guard_id')) {
            $query->where('guard_id', $request->guard_id);
        }

        if ($request->has('from') && $request->has('to')) {
            $query->whereBetween('created_at', [$request->from, $request->to]);
        }

        $attendances = $query->with(['assignedGuard', 'shift'])->orderBy('created_at', 'desc')->get();

        return response()->json($attendances);
    }

    public function clockIn(Request $request)
    {
        if (!checkPermission('guard-clockin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $this->validate($request, [
            'guard_id' => 'required|exists:guards,id',
            'shift_id' => 'required|exists:shifts,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'notes' => 'nullable|string',
        ]);

        $guard = Guard::with('location')->find($request->guard_id);

        if (!$guard || !$guard->location) {
            return response()->json(['message' => 'No assigned location found for guard.'], 400);
        }

        $assignedLat = $guard->location->latitude;
        $assignedLong = $guard->location->longitude;
        $currentLat = $request->latitude;
        $currentLong = $request->longitude;

        $distance = $this->calculateDistance($assignedLat, $assignedLong, $currentLat, $currentLong);

        if ($distance > 0.2) {
            return response()->json(['message' => 'You are not in range of your assigned location.'], 403);
        }

        $existing = GuardAttendance::where('guard_id', $guard->id)->whereNull('clock_out')->first();
        if ($existing) {
            return response()->json(['message' => 'Already clocked in.'], 400);
        }

        $now = Carbon::now();

        // Fetch assigned shift start time and check late
        $isLate = false;
        $assignedShift = $guard->shift;
        if ($assignedShift && Carbon::parse($assignedShift->start_time)->lt($now)) {
            $isLate = true;
        }

        $attendance = GuardAttendance::create([
            'guard_id' => $guard->id,
            'shift_id' => $request->shift_id,
            'clock_in' => $now,
            'late_arrival' => $isLate,
            'notes' => $request->notes
        ]);

        return response()->json(['message' => 'Clock-in successful', 'data' => $attendance], 201);
    }

    public function clockOut(Request $request)
    {
        if (!checkPermission('guard-clockout')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $this->validate($request, [
            'guard_id' => 'required|exists:guards,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $attendance = GuardAttendance::where('guard_id', $request->guard_id)
            ->whereNull('clock_out')
            ->latest()
            ->first();

        if (!$attendance) {
            return response()->json(['message' => 'No active clock-in found.'], 404);
        }

        // Get guard's assigned location
        $guard = Guard::find($request->guard_id);
        $location = $guard->location;

        if (!$location || !$location->latitude || !$location->longitude) {
            return response()->json(['message' => 'Assigned location is missing for the guard.'], 422);
        }

        $distance = $this->calculateDistance(
            $request->latitude,
            $request->longitude,
            $location->latitude,
            $location->longitude
        );

        if ($distance > 0.2) {
            return response()->json(['message' => 'You are not at your assigned location. Please move closer to your designated area.'], 403);
        }

        $now = Carbon::now();
        $clockIn = Carbon::parse($attendance->clock_in);
        $workedDuration = $clockIn->diff($now);
        $workedHours = $workedDuration->format('%H:%I:%S');

        $shift = $attendance->shift;
        $earlyDeparture = false;

        if ($shift && $shift->end_time) {
            $shiftEnd = Carbon::parse($shift->end_time);
            $earlyDeparture = $now->lt($shiftEnd);
            $attendance->total_assigned_time = $shiftEnd->diff(Carbon::parse($shift->start_time))->format('%H:%I:%S');
        }

        $attendance->clock_out = $now;
        $attendance->early_leave = $earlyDeparture;
        $attendance->total_worked_hours = $workedHours;
        $attendance->save();

        return response()->json(['message' => 'Clock-out successful', 'data' => $attendance]);
    }

    public function todayAttendance($guardId)
    {
        if (!checkPermission('show-attendance')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $today = Carbon::today();
        $attendance = GuardAttendance::where('guard_id', $guardId)
            ->whereDate('created_at', $today)
            ->first();

        if (!$attendance) {
            return response()->json(['message' => 'No attendance found for today.'], 404);
        }

        return response()->json($attendance);
    }

    protected function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $lat1 = deg2rad($lat1);
        $lat2 = deg2rad($lat2);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             sin($dLon / 2) * sin($dLon / 2) * cos($lat1) * cos($lat2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
