<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\User;
use Validator;

class UserController extends Controller
{
    public function register(Request $request){
        $registrationData = $request->all();
        $validate = Validator::make($registrationData,[
            'name'=>'required|max:60',
            'email'=>'required|email:rfc,dns|unique:users',
            'no_telp'=>'required|numeric|digits_between:8,13',
            'password'=>'required'
        ]);

        if($validate->fails())
            return response(['message'=>$validate->errors()],400); //return error invalid

        $registrationData['password'] = bcrypt($request->password); //enkripsi password
        $user = User::create($registrationData);//membuat user

        return response([
            'status'=>'S',
            'user'=>$user,
        ],200);
    }

    public function login(Request $request){
        $loginData = $request->all();
        $validate = Validator::make($loginData,[
            'email'=>'required|email:rfc,dns',
            'password'=>'required'
        ]);

        if($validate->fails())
            return response(['message'=>$validate->errors()],400);

        if(!Auth::attempt($loginData))
            return response(['message'=>'Invalid Credentials'],401);

        $user = Auth::user();
        $token = $user->createToken('Authenticaton Token')->accessToken;

        return response([
            'status' =>'S',
            'message'=>'Authenticated',
            'user'=>$user,
            'token_type'=>'Bearer',
            'access_token'=>$token
        ]);
    }

    public function logout(Request $request){
        $request->user()->token()->revoke();

        return response()->json([
            'status'=>'S'
        ]);
    }

    public function show($id){
        $user = User::find($id);

        if(!is_null($user)){
            return response([
                'message'=>'S',
                'data'=>$user
            ],200);
        }

        return response([
            'status'=>'F',
            'data'=>null
        ],404);
    }

    public function update(Request $request,$id){
        $user = User::find($id);

        if(is_null($user)){
            return response([
                'message'=>'F',
                'data'=>null
            ],404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData,[
            'email'=>'required|email:rfc,dns',
            'name'=>'required',
            'no_telp'=>'required|numeric|digits_between:8,13'
        ]);

        if($validate->fails())
            return response(['message'=>$validate->errors()],404);//return error invalid input

        $user->email = $updateData['email'];
        $user->name = $updateData['name'];
        $user->no_telp = $updateData['no_telp'];

        if($user->save()){
            return response([
                'message'=>'S',
                'data'=>$user,
            ],200);
        }//return user yg telah diedit

        return response([
            'message'=>'F',
            'data'=>null,
        ],404);//return message saat user gagal diedit
    }

    public function updatePassword(Request $request,$id){
        $user = User::find($id);

        if(is_null($user)){
            return response([
                'message'=>'F',
                'data'=>null
            ],404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData,[
            'password'=>'required',
            'newPassword'=>'required',
            'confirmPassword'=>'required'
        ]);

        if($validate->fails()){
            return response(['message'=>$validate->errors()],404);//return error invalid input
        }else{
                if((Hash::check(request('password'), Auth::user()->password))==false){
                    return response([
                        'message'=>'Old Password not same like before',
                        'data'=>null,
                    ],404);//return message saat user gagal diedit
                }else if($updateData['newPassword'] != $updateData['confirmPassword']){
                    return response([
                        'message'=>'new password not match',
                        'data'=>null,
                    ],404);//return message saat user gagal diedit
                }else{
                    $user->password = bcrypt($updateData['newPassword']);
                }
        }


        if($user->save()){
            return response([
                'message'=>'S',
                'data'=>$user,
            ],200);
        }//return user yg telah diedit

        return response([
            'message'=>'F',
            'data'=>null,
        ],404);//return message saat user gagal diedit

    }

}
