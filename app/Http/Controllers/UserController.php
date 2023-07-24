<?php

namespace App\Http\Controllers;

use App\Helper\JWToken;
use App\Mail\OTPEmail;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    function UserRegistration(Request $request){
        try {
            User::create([
                'firstName' => $request->input('firstName'),
                'lastName' => $request->input('lastName'),
                'userName' => $request->input('userName'),
                'email' => $request->input('email'),
                'mobile' => $request->input('mobile'),
                'password' => $request->input('password'),
            ]);
            return response()->json([
                'status' => 'success',
                'message' => 'User Registration Successfully..!'
            ],200);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'User Registration Failed..!'
            ],200);

        }
    }

    function UserLogin(Request $request){
        $userCount=User::where('email','=',$request->input('email'))
            ->where('password','=',$request->input('password'))
            ->count();

        if($userCount==1){

            $token = JWToken::CreateToken($request->input('email'));
            return response()->json([
                'status' => 'success',
                'message' => 'User Login Successful',
            ],200)->cookie('token',$token,60*24*30);
        }
        else{
            return response()->json([
                'status' => 'failed',
                'message' => 'unauthorized..!'
            ],200);

        }
    }

    function SendOTP(Request $request){

        $email=$request->input('email');
        $otp=rand(10000, 99999);
        $userCount=User::where('email','=',$email)->count();

        if($userCount == 1){

            Mail::to($email)->send(new OTPEmail($otp));

            User::where('email','=',$email)->update(['otp'=>$otp]);

            return response()->json([
                'status' => 'success',
                'message' => 'OTP Code has been send to your email !'
            ],200);
        }
        else{
            return response()->json([
                'status' => 'failed',
                'message' => 'unauthorized'
            ]);
        }
    }

    function VerifyOTP(Request $request){

        $email=$request->input('email');
        $otp=$request->input('otp');
        $userCount=User::where('email','=',$email)
            ->where('otp','=',$otp)->count();

        if($userCount == 1){

            User::where('email','=',$email)->update(['otp'=>'0']);

            // Pass Reset Token Issue
            $token = JWToken::CreateTokenForSetPassword($request->input('email'));
            return response()->json([
                'status' => 'success',
                'message' => 'OTP Verify Successful',
            ],200)->cookie('token',$token,60*24*30);

        }
        else{
            return response()->json([
                'status' => 'failed',
                'message' => 'unauthorized'
            ],200);
        }
    }

    function ResetPassword(Request $request){
        try{
            $email=$request->header('email');
            $password=$request->input('password');
            User::where('email','=',$email)->update(['password'=>$password]);
            return response()->json([
                'status' => 'success',
                'message' => 'Request Successful',
            ],200);

        }catch (Exception $exception){
            return response()->json([
                'status' => 'fail',
                'message' => 'Something Went Wrong',
            ],200);
        }
    }

}
