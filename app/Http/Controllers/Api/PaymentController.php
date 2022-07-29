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
        //
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
      
        $ip = $request->ip();
        if ($validator->passes()) {
            try {
                $owner = DB::table('integrations')->where('key',$request->key)->value('owner');
                $payment = Payment::firstOrCreate(
                    [
                        'eventName' => $request->eventName,
                        'owner' => $owner, 
                        'ApiKey' => $request->key,
                        'transactionHash' => $request->transactionHash,
                        'wallet' => $request->wallet,
                        'position' => $request->position,
                        'device' => $agent->isDesktop()?"Desktop":$agent->device(),
                        'platform' => $agent->platform(),
                        'browser' => $agent->browser(),
                        'languages' => $agent->languages(),
                        'ip' => $request->ip()
                    ]
                );
                $res = DB::table('payments')->where('owner',$owner)->orderBy('_id', 'desc')->get();;
                return response()->json([
                    "payments" => $res
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
    public function destroy(Payment $payment)
    {
        //
    }

    
}
