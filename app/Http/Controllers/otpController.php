<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Twilio\Rest\Client;
use App\Models\otp;
use Carbon\Carbon;

class otpController extends Controller
{
    public function sendotp(Request $request)
    {
        $input = $request->all();
        $validation = Validator::make($input, [
            'phonenumber' => 'required',
        ]);

        if ($validation->fails()) {
            return response()->json(['error' => $validation->errors(), 'status_code' => 400], 400);
        } else {

            $phoneNumber = $request->input('phonenumber');

            $otp = mt_rand(10000, 99999);

            $twilioSid = config('services.twilio.sid');
            $twilioToken = config('services.twilio.token');
            $twilioPhoneNumber = config('services.twilio.from');
            $client = new Client($twilioSid, $twilioToken);

            try {
                $message = $client->messages->create(
                    $phoneNumber,
                    [
                        'from' => $twilioPhoneNumber,
                        'body' => 'Your OTP is: ' . $otp,
                    ]
                );

                $otpEntry = otp::Create(
                    ['phonenumber' => $phoneNumber],
                    ['otp' => $otp]
                );

                return response()->json([
                    'message' => 'OTP sent successfully',
                    'data' => $otpEntry,
                    'message_sid' => $message->sid,
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'error' => 'Failed to send OTP',
                    'error_message' => $e->getMessage(),
                ], 500);
            }
        }
    }


    public function verifyotp(Request $request)
    {
        $input = $request->all();
        $validation = Validator::make($input, [
            'phonenumber' => 'required',
            'otp' => 'required',
        ]);

        if ($validation->fails()) {
            return response()->json(['error' => $validation->errors(), 'status_code' => 400], 400);
        } else {
            $phonenumber = $request->input('phonenumber');
            $otp = $request->input('otp');

            $phonenumdetails = otp::where('phonenumber', $phonenumber)->first();

            if ($otp === strval($phonenumdetails->otp)) {
                $createdexpiryTime = Carbon::parse($phonenumdetails->created_at)->addMinutes(5);
                
              
                if (Carbon::now()->greaterThan($createdexpiryTime)) {
                    return response()->json([
                        'error' => 'OTP has expired',
                    ], 422);
                } else {
                    return response()->json([
                        'message' => 'OTP verified successfully',
                    ]);
                }
            } else {
                return response()->json([
                    'error' => 'Invalid OTP',
                ], 422);
            }
        }
    }



}