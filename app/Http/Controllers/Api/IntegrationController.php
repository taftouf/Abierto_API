<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Integration;
use DB;
use Illuminate\Support\Facades\Auth;


class IntegrationController extends Controller
{
    public function getIntegration(Request $request){
        try {
            $owner = $request->header('owner');
            $res = DB::table('integrations')->where('owner','LIKE','%'.$owner.'%')->paginate(5);
            return response()->json([
                "integration" => $res
            ]);
        } catch (Exception $e) {
            return response()->json([
                "error" => $e
            ]);
        }
    }

    public function addIntegration(Request $request){
        $validator = Validator::make($request->all(), [
            'owner' => 'required|string|max:255',
            'name' => 'required|string|max:255'
        ]);
        
        if ($validator->fails()) {
            $errors = $validator->fails();
            return response()->json([
                'errors' => $request->all()
            ], 400);
        }

        // $token = [
        //             ['token_address' => 99, 'token_symbol' => 1],
        //             ['token_address' => 87, 'token_symbol' => 10]
        //         ];
        
        if ($validator->passes()) {
            try {
                $integration = Integration::firstOrCreate(
                    [
                        'owner' => $request->owner, 
                        'name' => $request->name,
                        'receiver' => $request->owner,
                    ]
                );
                $res = DB::table('integrations')->where('owner','LIKE','%'.$request->owner.'%')->paginate(5);
                return response()->json([
                    "integration" => $res
                ], 200);

            } catch (Exception $e) {
                return response()->json([
                    'error' => $e
                ], 400);
            }
        }
    }

    public function update(Request $request){
        

        try {
            $receiver = $request->header('receiver');
            $name = $request->header('name');
            $_id = $request->header('_id');

            $integration = Integration::find($_id);
            $integration->receiver = $receiver;
            $integration->name = $name;
            $integration->save();

            return response()->json([
                'msg' => 'success',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e
            ], 400);
        }
    
    }
}
