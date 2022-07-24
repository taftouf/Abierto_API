<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Integration;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;


class IntegrationController extends Controller
{
    public function index(Request $request){
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

    public function getIntegration(Request $request){
        try {
            $id = $request->header('_id');
            
            $res = DB::table('integrations')->where('_id',$id)->get();
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
                        'key' => $this->getKey(),
                    ]
                );
                $res = DB::table('integrations')->where('owner','LIKE','%'.$request->owner.'%')->get();
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

    public function delete(Request $request){
        try {
            $id = $request->header('_id');
            $res = DB::table('integrations')->where('_id',$id)->delete();
            return response()->json([
                'msg' => 'success',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e
            ], 400);
        }
    }

    private function getKey(){
        $key = Str::random(4)."-".Str::random(4)."-".Str::random(4)."-".Str::random(4);
        $res = DB::table('integrations')->where('key','LIKE','%'.$key.'%')->get();
        $i = 0;
        if(count($res) != 0 && $i++<10){
            $this->getKey();
        }else{
            return $key;
        }
        return "";
    }
}
