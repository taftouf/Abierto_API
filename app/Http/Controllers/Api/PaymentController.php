<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use DB;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Agent\Agent;
use Stevebauman\Location\Facades\Location;


class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $res = DB::table('payments')->get();;
                return response()->json([
                    "data" => $res
                ], 200);
       } catch (Exception $e) {
            return response()->json([
                "err" => $e
            ], 400);
       }
    }

    public function getPaymentForOwner(Request $request)
    {
        try {
            $owner = $request->header('owner');
            $res = DB::table('payments')
            ->where('owner','LIKE','%'.$owner.'%')
            ->orderBy('created_at', 'desc')
            ->get();
            return response()->json([
                    "data" => $res,
                    "nbr" => $res->count()
                ], 200);
       } catch (Exception $e) {
            return response()->json([
                "err" => $e
            ], 400);
       }
    }

    public function getPaymentForIntegration(Request $request)
    {
        try {
            $ApiKey = $request->header('ApiKey');
            $res = DB::table('payments')
            ->where('ApiKey','LIKE','%'.$ApiKey.'%')
            ->orderBy('created_at', 'desc')
            ->get();
            return response()->json([
                    "data" => $res
                ], 200);
       } catch (Exception $e) {
            return response()->json([
                "err" => $e
            ], 400);
       }
    }

    public function getTokenInForOwner(Request $request)
    {   
        $tokens = [];
        try {
            $owner = $request->header('owner');
            $res = DB::table('payments')->select('tokenIn', 'tokenOut', 'amountIn')->where('owner','LIKE','%'.$owner.'%')->get();
            $t = DB::table('payments')->select('tokenIn')->where('owner','LIKE','%'.$owner.'%')->groupBy('tokenIn')->get();
            foreach ($t as $key => $value) {
                $tokens[$value['tokenIn']] = $res->where('tokenIn', $value['tokenIn'])->count();
            }
            return response()->json([
                    "data" => $res,
                    "nbrTokenIn" => $res->groupBy("tokenIn")->count(),
                    "token" => $tokens
                ], 200);
       } catch (Exception $e) {
            return response()->json([
                "err" => $e
            ], 400);
       }
    }

    public function getTokenInForIntegration(Request $request)
    {
        try {
            $ApiKey = $request->header('ApiKey');
            $res = DB::table('payments')->select('tokenIn', 'tokenOut', 'amountIn')->where('ApiKey','LIKE','%'.$ApiKey.'%')->get();
            return response()->json([
                    "data" => $res
                ], 200);
       } catch (Exception $e) {
            return response()->json([
                "err" => $e
            ], 400);
       }
    }

    public function getSuccessTransactionForOwner(Request $request)
    {
        try {
            $owner = $request->header('owner');
            $res = DB::table('payments')->where('status',1)->get();
            return response()->json([
                    "data" => $res,
                    "nbr" => $res->count()
                ], 200);
       } catch (Exception $e) {
            return response()->json([
                "err" => $e
            ], 400);
       }
    }

    public function getFailedTransactionForOwner(Request $request)
    {
        try {
            $owner = $request->header('owner');
            $res = DB::table('payments')->where('status',0)->get();
            return response()->json([
                    "data" => $res,
                    "nbr" => $res->count()
                ], 200);
       } catch (Exception $e) {
            return response()->json([
                "err" => $e
            ], 400);
       }
    }

    

    // EndPOINTS For Home Route
    public function getPaymentStatic(Request $request){
        $name = [];
        $success = [];
        $failed = [];
        $ApiKey = 0;
        $pay = [];
        $owner = $request->header('owner');
        $res = DB::table('integrations')->select('name','key')->where('owner','LIKE','%'.$owner.'%')->get();
        foreach ($res as $key => $value) {
           foreach ($value as $k => $v) {
            if($k == 'key') {$ApiKey = $v;}
            if($k == 'name'){
                $s = DB::table('payments')->where([['owner','=',$owner],['ApiKey', '=', $ApiKey],['status','=',1]])->count();
                $f = DB::table('payments')->where([['owner','=',$owner],['ApiKey', '=', $ApiKey],['status','=',0]])->count();
                array_push($name, $v);
                array_push($success, $s);
                array_push($failed, $f);

            }
           }
        }

        return response()->json([
            "data" => ["name"=>$name,"success"=>$success,"failed"=>$failed],
            "nbr" => $res->count()
        ], 200);
    }


    public function getTransaction(Request $request){
        try {
            $owner = $request->header('owner');
            $res = DB::table('payments')
            ->select('transactionHash', 'tokenIn', 'ApiKey', 'status')
            ->where('owner','LIKE','%'.$owner.'%')
            ->offset(0)
            ->limit(4)
            ->orderBy('created_at', 'desc')
            ->get();
            return response()->json([
                    "data" => $res
                ], 200);
       } catch (Exception $e) {
            return response()->json([
                "err" => $e
            ], 400);
       }
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
       
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $agent = new Agent();

        $validator = Validator::make($request->all(), [
            'key' => 'required|string|max:255',
            'transactionHash' => 'required|string|max:255',
            'wallet' => 'required|string|max:255'
        ]);
        
        if ($validator->fails()) {
            $errors = $validator->fails();
            return response()->json([
                'errors' => $request->all()
            ], 400);
        }
      
        if ($validator->passes()) {
            try {
                $owner = DB::table('integrations')->where('key',$request->key)->value('owner');
                $payment = Payment::firstOrCreate(
                    [
                        'eventName' => $request->eventName,
                        'owner' => $owner, 
                        'ApiKey' => $request->key,
                        'transactionHash' => $request->transactionHash,
                        'status' => $request->status,
                        'wallet' => $request->wallet,
                        'tokenIn' => $request->tokenIn,
                        'tokenOut' => $request->tokenOut,
                        'amountIn' => $request->amountIn,
                        'position' => $request->position,
                        'device' => $agent->isDesktop()?"Desktop":$agent->device(),
                        'platform' => $agent->platform(),
                        'browser' => $agent->browser(),
                        'languages' => $agent->languages(),
                        'location' => Location::get($request->header('X-Forwarded-For')),
                        'protocol' => $request->protocol,
                        'host' => $request->host,
                        'pathname' => $request->pathname
                    ]
                );
                $res = DB::table('payments')->where('owner',$owner)->orderBy('_id', 'desc')->first();
                return response()->json([
                    "success" => true
                ], 200);

            } catch (Exception $e) {
                return response()->json([
                    'error' => $e
                ], 400);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function show(Payment $payment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function edit(Payment $payment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Payment $payment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        try {
            $id = $request->header('_id');
            $res = DB::table('payments')->where('_id',$id)->delete();
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
