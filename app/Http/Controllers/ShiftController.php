<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function index()
    {
        if (!checkPermission('show-shift')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $shifts = Shift::all();
        return response()->json(['data' => $shifts], 200);
    }

    public function show($id)
    {
        if (!checkPermission('show-shift')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $shift = Shift::find($id);

        if (!$shift) {
            return response()->json(['message' => 'Shift data not found'], 404);
        }

        return response()->json(['data' => $shift], 200);
    }

    public function store(Request $request)
    {
        if (!checkPermission('add-shift')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $this->validate($request, [
            'name'       => 'required|string',
            'start_time' => 'required|date_format:H:i',
            'end_time'   => 'required|date_format:H:i',
            'days'       => 'nullable|array',
            'days.*'     => 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'is_active'  => 'required|boolean',
        ]);

        $shift = new Shift();
        $shift->name = $request->input('name');
        $shift->start_time = $request->input('start_time');
        $shift->end_time = $request->input('end_time');
        $shift->days = $request->input('days', []);
        $shift->is_active = $request->boolean('is_active');
        $shift->save();

        return response()->json(['message' => 'Shift created successfully', 'data' => $shift], 201);
    }

    public function update(Request $request, $id)
    {
        if (!checkPermission('edit-shift')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $shift = Shift::find($id);

        if (!$shift) {
            return response()->json(['message' => 'Shift not found'], 404);
        }

        $this->validate($request, [
            'name'       => 'sometimes|string|unique:shifts,name,' . $shift->id,
            'start_time' => 'required|date_format:H:i',
            'end_time'   => 'required|date_format:H:i',
            'days'       => 'nullable|array',
            'days.*'     => 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'is_active'  => 'required|boolean',
        ]);

        $shift->update($request->only(['name', 'start_time', 'end_time', 'days', 'is_active']));

        return response()->json(['message' => 'Shift updated successfully', 'data' => $shift], 200);
    }

    public function destroy($id)
    {
        if (!checkPermission('delete-shift')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $shift = Shift::find($id);

        if (!$shift) {
            return response()->json(['message' => 'Shift not found'], 404);
        }

        $shift->delete();

        return response()->json(['message' => 'Shift deleted successfully'], 200);
    }
}
