<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Guard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function register(Request $request)
    {
        if (!checkPermission('add-user')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name'     => 'required|string',
            'email'    => 'required|email|unique:users',
            'password' => 'required|string',
            'role'     => 'required|string',
        ]);

        $role = Role::where('name', $request->role)->first();
        if (!$role) {
            return response()->json(['error' => 'Invalid role'], 400);
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role_id'  => $role->id,
        ]);

        return response()->json(['message' => 'User registered successfully'], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => $user,
        ]);
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    }

    public function profile(Request $request)
    {
        return response()->json([
            'id'        => $request->user()->id,
            'name'      => $request->user()->name,
            'email'     => $request->user()->email,
            'role'      => $request->user()->role->name ?? null,
            'status'    => $request->user()->status ?? 'active',
            'createdAt' => $request->user()->created_at,
            'updatedAt' => $request->user()->updated_at,
        ]);
    }

    public function updateProfile(Request $request, $id)
    {
        if (!checkPermission('edit-user')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $user = User::findOrFail($id);

        $this->validate($request, [
            'name'    => 'required|string',
            'email'   => 'required|email|unique:users,email,' . $id,
            'role_id' => 'required|exists:roles,id',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role_id = $request->role_id;

        $user->save();

        return response()->json([
            'message' => 'User Updated Successfully!',
            'user'    => $user
        ], 200);
    }

    public function destroyProfile(Request $request, $id)
    {
        if (!checkPermission('delete-user')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'message' => 'User Deleted Successfully!!'
        ], 200);
    }

    public function getAllUser(Request $request)
    {
        if (!checkPermission('show-user')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $users = User::all();
        return response()->json([
            'message' => 'Get All User Successfully!',
            'count'   => count($users),
            'user'    => $users
        ], 200);
    }


    public function guardLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login'    => 'required|string', 
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $loginField = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $guard = Guard::where($loginField, $request->login)->first();

        if (!$guard || !Hash::check($request->password, $guard->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $guard->createToken('guard_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token'   => $token,
            'guard'   => $guard,
        ]);
    }
}