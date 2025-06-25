<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index()
    {
        if (!checkPermission('show-location')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json(Location::all());
    }

    public function store(Request $request)
    {
        if (!checkPermission('add-location')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $this->validate($request, [
            'name'      => 'required|string|max:255',
            'address'   => 'nullable|string',
            'latitude'  => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $location = Location::create($request->all());

        return response()->json([
            'message' => 'Location created successfully',
            'data'    => $location
        ], 201);
    }

    public function show($id)
    {
        if (!checkPermission('show-location')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $location = Location::findOrFail($id);
        return response()->json($location);
    }

    public function update(Request $request, $id)
    {
        if (!checkPermission('edit-location')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $location = Location::findOrFail($id);

        $this->validate($request, [
            'name'      => 'required|string|max:255',
            'address'   => 'nullable|string',
            'latitude'  => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $location->update($request->only(['name', 'address', 'latitude', 'longitude']));

        return response()->json([
            'message' => 'Location updated successfully',
            'data'    => $location
        ]);
    }

    public function destroy($id)
    {
        if (!checkPermission('delete-location')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $location = Location::findOrFail($id);
        $location->delete();

        return response()->json(['message' => 'Location deleted successfully']);
    }
}
