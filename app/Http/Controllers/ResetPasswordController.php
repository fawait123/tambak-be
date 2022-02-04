<?php

namespace App\Http\Controllers;

use App\Models\ResetPassword;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ResetPasswordController extends Controller
{
    public function send(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'email'=>'required|email:dns'
        ]);

        if($validator->fails()){
            return response()->json([
                'status'=>false,
                'message'=>$validator->errors()
            ],400);
        }
        $code = rand(10000,100000);
        $cek = User::where('email',$request->email)->first();
        if($cek){
            $push = ResetPassword::create([
                'email'=>$request->email,
                'token'=>$code
            ]);
            if($push){
                                $data = [
                    'code'=>$code
                ];
                Mail::send('resetpassword', $data, function ($message) use ($request) {
                    $message->to($request->email, $request->nama)->subject('Reset Password');
                    $message->from('lokalprogrammer@gmail.com', 'Jala');
                });
                return response()->json([
                    'status'=>true,
                    'message'=>'Please check your email for reset password'
                ],200);
            }
                return response()->json([
                    'status'=>false,
                    'message'=>'server error'
                ],500);
        }

        return response()->json([
            'status'=>false,
            'message'=>'Not found!'
        ],404);
    }


    public function reset(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'email'=>'required|email:dns',
            'token'=>'required',
            'password'=>'required|min:8',
            'confirm_password' => 'required|same:password'
        ]);

        if($validator->fails()){
            return response()->json([
                'status'=>false,
                'message'=>$validator->errors()
            ],400);
        }

        $cek = ResetPassword::where('email',$request->email)->where('token',$request->token)->first();
        if($cek){
            $user = User::where('email',$cek->email)->first();
            if($user){
                $user->update([
                    'password'=>Hash::make($request->password)
                ]);
                $cek->delete();
                return response()->json([
                    'status'=>true,
                    'message'=>'Password has been reset'
                ],200);
            }
            return response()->json([
                'status'=>false,
                'message'=>'server error'
            ],500);
        }
        return response()->json([
            'status'=>false,
            'message'=>'Not Found'
        ],404);

    }
}
