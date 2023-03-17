<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(Request $request)
    {
    	$validate = \Validator::make( $request->all(),[

    		'name' => 'required',
    		'email' => 'required|unique:users',
    		'password' => 'required'

    	],[

    		'name.required' => 'Name is required',
    		'email.required' => 'Email is required',
    		'password.required' => 'Password is required'
    	]);

    	if ( $validate->fails() ) 
		{
		    return response()->json([ 'msg' => $validate->errors(), 'status' => false ], 422);
		}


    	$res = new \App\Models\User;
    	$res->name = $request->name;
    	$res->email = $request->email;
    	$res->password = \Hash::make($request->password);
    	$res->save();

    	return response()->json(['msg' => 'User register successfully', 'status' => true ], 200);
    }

    public function login(Request $request)
    {
		$validate = \Validator::make( $request->all(),[

    		'email' => 'required',
    		'password' => 'required'

    	],[

    		'email.required' => 'Email is required',
    		'password.required' => 'Password is required'
    	]);

    	if ( $validate->fails() ) 
		{
		    return response()->json([ 'msg' => $validate->errors(), 'status' => false ], 422);
		}

		if ( !\Auth::attempt(['email' => $request->email, 'password' => $request->password]) ) {
			
			return response()->json([ 'msg' => 'Email & Password does not match with our record.', 'status' => false], 401);
		}

		$user = \App\Models\User::where('email', $request->email)->first();

		return response()->json([ 'status' => true, 'message' => 'User Logged In Successfully', 'token' => $user->createToken("Login Token")->plainTextToken ], 200);

    }

    public function getUsers()
    {
    	try{

    		$users = \App\Models\User::all();

    		return response()->json($users, 200);

    	}catch( \Exception $e){

    		return response()->json([ 'msg' => 'Something Went Wrong', 'status' => false], 500);
    	}
    }
}
