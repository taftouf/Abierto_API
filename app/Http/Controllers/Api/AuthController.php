<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use DB;


class AuthController extends Controller
{
    // Login
    public function login(Request $request){
        if(DB::table('users')->where('address', $request->address)->exists())
        {
            $user = User::where('address', $request['address'])->firstOrFail();
            $token = $user->createToken('auth_token')->plainTextToken;
        
            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer'
            ]);
        }

        return $this->register($request);
    }

    // Register
    private function register(Request $request){
       
        $validator = Validator::make($request->all(), [
            "address" => 'required|max:255|string|unique:users,address'
        ]);

         if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json([
                'error' => $errors
            ], 400);
        }

        if ($validator->passes()) {
            try {

                $user = User::create([
                    'address' => $request->address,
                ]);


                $token = $user->createToken('auth_token')->plainTextToken;
                
                return response()->json([
                    'access_token' => $token,
                    'token_type' => 'Bearer'
                ]);
            } catch (Exception $e) {
                return response()->json([
                    'error' => $e
                ], 400);
            }
        }
    }
}
