<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;


use  App\Models\User;
use Auth;

class AuthController extends Controller
{
    public function signup(Request $req){
        $validation = Validator::make($req->all(),[
            'name'=> 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:6',
        ]);
        if($validation->fails()){
            $response = [
                'succes' =>false,
                'message' => $validation->errors()
            ];
            return response()->json($response, 400);
        }
        $input = $req->all();
        $input ['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] = $user->createToken('MyApp')->plainTextToken;
        $success['name'] = $user->name;
        $success['role'] = $user->role;
        $response = [
            'success' => true,
            'data' => $success,
            'message' => 'User register succesfully'
        ];
        return response()->json($response,200);

    }
    // login
    public function login(Request $req){
        $validate = Validator::make($req->all(),[
            'email' => 'required|email',
            'password' => 'required'
        ]);
        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validate->errors()
            ], 422);
        }
        if(Auth::attempt(['email'=>$req->email,'password'=>$req->password])){
            $user = Auth::user();
            $success['token'] = $user->createToken('MyApp')->plainTextToken;
            $success['name'] = $user->name;
            $success['role'] = $user->role;
            $response = [
                'success' => true,
                'data' => $success,
            ];
            return response()->json($response,200);
        }else{
            $response = [
                'success' => false,
                'message' => 'email or password in correct'
            ];
        }
        return response()->json($response);
    }
    // vender signup
     public function Vender_register(Request $req){
        $validation = Validator::make($req->all(),[
            'name'=> 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:6',
            'role' => 'in:vender'

        ]);
        if($validation->fails()){
            $response = [
                'succes' =>false,
                'message' => $validation->errors()
            ];
            return response()->json($response, 400);
        }
        $user = User::create([
            'name' => $req->name,
            'email' => $req->email,
            'password' => Hash::make($req->password),
            'role' => 'vender',
        ]);
        $success['token'] = $user->createToken('MyApp')->plainTextToken;
        $success['name'] = $user->name;
        $success['role'] = $user->role;
        $response = [
            'success' => true,
            'data' => $success,
            'message' => 'User register succesfully'
        ];
        return response()->json($response,200);

    }
}

