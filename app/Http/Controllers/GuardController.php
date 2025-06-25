<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\Guard;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GuardController extends Controller
{
    public function index()
    {
        if (!checkPermission('show-guard')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $guards = Guard::with(['user', 'shift'])->get();

        return response()->json([
            'message' => 'All guards fetched successfully',
            'data' => $guards
        ], 200);
    }

    public function store(Request $request)
    {
        if (!checkPermission('add-guard')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'username'          => 'required|string|unique:guards,username',
            'emp_code'          => 'nullable|string|unique:guards,emp_code',
            'email'             => 'nullable|email',
            'phone'             => 'nullable|string|max:15',
            'address'           => 'nullable|string|max:255',
            'emergency_contact' => 'nullable|string',
            'emergency_phone'   => 'nullable|string|max:15',
            'profile_photo'     => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'shift_id'          => 'nullable|integer|exists:shifts,id',
            'location_id'       => 'nullable|integer|exists:locations,id',
            'status'            => 'required|string|in:active,inactive',
            'name'              => 'required|string',
            'password'          => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $guard = new Guard();
        $guard->shift_id = $request->input('shift_id');
        $guard->location_id = $request->input('location_id');
        $guard->name = $request->input('name');
        $guard->username = $request->input('username');
        $guard->emp_code = $request->input('emp_code');
        $guard->email = $request->input('email');
        $guard->phone = $request->input('phone');
        $guard->address = $request->input('address');
        $guard->emergency_contact = $request->input('emergency_contact');
        $guard->emergency_phone = $request->input('emergency_phone');
        $guard->status = $request->input('status');
        $guard->password = Hash::make($request->input('password'));

        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profile_photos', 'public');
            $guard->profile_photo = $path;
        }

        $guard->save();

        $response = $guard->toArray();
        $response['profile_photo_url'] = $guard->profile_photo
            ? asset('storage/' . $guard->profile_photo)
            : null;

        return response()->json([
            'message' => 'Guard created successfully',
            'data'    => $response,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        if (!checkPermission('edit-guard')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $guard = Guard::find($id);
        Log::info('Request input:', $request->all());

        if (!$guard) {
            return response()->json(['message' => 'Guard not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name'              => 'sometimes|required|string',
            'username'          => 'sometimes|required|string|unique:guards,username,' . intval($id),
            'emp_code'          => 'sometimes|required|string|unique:guards,emp_code,' . intval($id),
            'password'          => 'nullable|string|min:6',
            'email'             => 'nullable|email',
            'phone'             => 'nullable|string|max:15',
            'address'           => 'nullable|string|max:255',
            'emergency_contact' => 'nullable|string',
            'emergency_phone'   => 'nullable|string|max:15',
            'profile_photo'     => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'shift_id'          => 'nullable|integer|exists:shifts,id',
            'location_id'       => 'nullable|integer|exists:locations,id',
            'status'            => 'sometimes|required|string|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($request->filled('shift_id'))          $guard->shift_id = $request->input('shift_id');
        if ($request->filled('location_id'))       $guard->location_id = $request->input('location_id');
        if ($request->filled('name'))              $guard->name = $request->input('name');
        if ($request->filled('username'))          $guard->username = $request->input('username');
        if ($request->filled('emp_code'))          $guard->emp_code = $request->input('emp_code');
        if ($request->filled('email'))             $guard->email = $request->input('email');
        if ($request->filled('phone'))             $guard->phone = $request->input('phone');
        if ($request->filled('address'))           $guard->address = $request->input('address');
        if ($request->filled('emergency_contact')) $guard->emergency_contact = $request->input('emergency_contact');
        if ($request->filled('emergency_phone'))   $guard->emergency_phone = $request->input('emergency_phone');
        if ($request->filled('status'))            $guard->status = $request->input('status');

        if ($request->filled('password')) {
            $guard->password = Hash::make($request->input('password'));
        }

        if ($request->hasFile('profile_photo')) {
            try {
                $path = $request->file('profile_photo')->store('profile_photos', 'public');
                $guard->profile_photo = $path;
            } catch (\Exception $e) {
                Log::error('Profile photo upload failed: ' . $e->getMessage());
                return response()->json(['message' => 'Photo upload failed'], 500);
            }
        }

        Log::info('Guard to be updated:', $guard->toArray());
        $guard->save();
        $guard = $guard->fresh();

        return response()->json([
            'message' => 'Guard updated successfully',
            'data'    => $guard,
        ]);
    }

    public function show($id)
    {
        if (!checkPermission('show-guard')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $guard = Guard::with(['shift'])->find($id);

        if (!$guard) {
            return response()->json(['message' => 'Guard not found'], 404);
        }

        return response()->json([
            'message' => 'Guard fetched successfully',
            'data' => $guard
        ], 200);
    }

    public function destroy($id)
    {
        if (!checkPermission('delete-guard')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $guard = Guard::find($id);

        if (!$guard) {
            return response()->json(['message' => 'Guard not found'], 404);
        }

        $guard->delete();

        return response()->json(['message' => 'Guard deleted successfully'], 200);
    }
}
