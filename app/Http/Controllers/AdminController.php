<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller; 
use App\Admin; 
use App\User; 
use Illuminate\Support\Facades\Auth; 
use Validator;
use Carbon\Carbon;
use Laravel\Passport\Passport; 
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware('auth:admin');
    // }

    public function login(){ 
        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){ 
			$admin = Auth::admin();
			$token = $admin->createToken('MyApp')-> accessToken; 
			//Passport::tokensExpireIn(now()->addDays(15));
			//Passport::refreshTokensExpireIn(now()->addDays(30));
			Passport::tokensExpireIn(Carbon::now()->addDays(15));			
			return response()->json(['status' => 'ok', 'token' => $token], $this-> successStatus);
        } 
        else{ 
            return response()->json(['status'=>'error', 'message'=>'Unauthorised'], 401); 
        }
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('admin');
    }
}
