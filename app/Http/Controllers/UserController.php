<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    //GET api/users
    public function getAllUsers(){
        try{
            $users = User::all();
            return response()->json($users);
        }
        catch(\Exception $e){
            return response()->json(['error' => 'Failed to retrieve users']);
        }
    }

    //GET api/user/{id}
    public function getUserById($id){
        try{
            $user = User::find($id);
            if($user){
                return response()->json($user);
            } else {
                return response()->json(['message' => 'User not found']);
            }
        }
        catch(\Exception $e){
            return response()->json(['error' => 'Failed to retrieve user']);
        }
    }
}
