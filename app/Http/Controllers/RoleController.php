<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        return response()->json($roles);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [

            "name"=> "required|string",

        ]);

        if($validator->fails()){
            return response()->json([

                "error"=> $validator->errors()->first(),
            
            ], 422);
        }

        $role = Role::create($request->all());
        return response()->json([
            "success"=> true,
            "role"=> $role
            ]);
    }

   
    public function update(Request $request, Role $role)
    {
        $validator = Validator::make($request->all(), [
              "name"=> "required|string",
              ]);
              if($validator->fails()){
                return response()->json([
                    "error"=> $validator->errors()->first(),
                    ],422);
                }

            $role->name = $request->name;
            $role->save();
             return response()->json([
                "success"=> true,
                "role"=> $role
                ]);

    }

    /**
     * Remove the specified resource from storage.
     */public function destroy(Role $role)
        {
            $role->delete();

            return response()->json([
                "success" => true,
                "message" => "Role deleted successfully.",
                "role" => $role,
            ]);
        }

}
