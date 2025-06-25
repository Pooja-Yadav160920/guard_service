<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class RoleController extends Controller
{
    public function index()
    {
         if (!checkPermission('show-role')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $roles = Role::all();
        return response()->json($roles);
    }

    
    public function store(Request $request)
    {


        if (!checkPermission('add-role')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

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

         if (!checkPermission('edit-role')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
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

        public function destroy(Role $role)
        {

             if (!checkPermission('delete-role')) {
            return response()->json(['error' => 'Unauthorized'], 403);
            }
            $role->delete();

            return response()->json([
                "success" => true,
                "message" => "Role deleted successfully.",
                "role" => $role,
            ]);
        }

}
