<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Token;
use DB;

class TokenController extends Controller
{
    public function index(Request $request){
        // return all Tokens
    }

    public function insert(Request $request){
       
        try {
            $token = Token::firstOrCreate(
                [
                    'symbol' => 'USDTt', 
                    'decimal' => '18',
                    'address' => '0xV1V1D3FV15T1VD2FBV2FGB1F2GB1'
                ]
            );
            return response()->json([
                "integration" => "success"
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'error' => $e
            ], 400);
        }
    }
}
