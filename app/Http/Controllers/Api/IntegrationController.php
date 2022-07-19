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
            $res = DB::table('integrations')->where('owner','LIKE','%'.$owner.'%')->get();
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
                'errors' => $errors
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

    public function updateIntegration(Request $request){
        $validator = Validator::make($request->all(), [
            '_id' => 'required|integer',
            'owner' => 'required|string|max:255',
            'list' => 'required'
        ]);
        die($request);
        if ($validator->fails()) {
            $errors = $validator->fails();
            return response()->json([
                'errors' => $errors,
                'msg' => $request
            ], 400);
        }

        if ($validator->passes()) {
            try {
                $integration = Integration::find($request['_id']);
                $integration->owner = $request['owner'];
                $integration->list = $request['list'];
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
}
