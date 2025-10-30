<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    //POST api/login
    public function login(Request $request){
        $request->validate([
            'email' => 'required|email',
            'password'=> 'required',
        ]);

        try{
            $user = User::where('email', $request->email)->first();

            if(!$user || !Hash::check($request->password, $user->password)){
                return response()->json(['message' => 'Invalid credentials'], 401);
            }

            return response()->json([
                'user'=> $user,
            ],200);
        }
        catch(\Exception $e){
            return response()->json(['message'=> $e->getMessage()],500);
        }
    }

    //POST api/logout
    public function logout(){
        return response()->json(['message' => 'Logout successful']);
    }
}
