<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller; 
use App\User; 
use Illuminate\Support\Facades\Auth; 
use Validator;
use Carbon\Carbon;
use Laravel\Passport\Passport; 
use Illuminate\Support\Facades\Hash;
//use App\Http\Controllers\Auth\LoginProxy;
class APIUserController extends Controller
{
    public $successStatus = 200;
		
	public function login(){ 
        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){ 
			$user = Auth::user();
			$token = $user->createToken('MyApp')-> accessToken; 
			//Passport::tokensExpireIn(now()->addDays(15));
			//Passport::refreshTokensExpireIn(now()->addDays(30));
			Passport::tokensExpireIn(Carbon::now()->addDays(15));			
			return response()->json(['status' => 'ok', 'token' => $token], $this-> successStatus);
        } 
        else{ 
            return response()->json(['status'=>'error', 'message'=>'Unauthorised'], 401); 
        }
	}
	
	public function adminLogin(){ 
        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){ 
			$admin = Auth::user();
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

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }
	
	public function register(){
		$err_message = '';
		$user = new User;
		$name = request('name');
		if ($name == null || $name == ''){
			$err_message = 'Name cannot be empty';
		} else {
			$email = request('email');
			if ($email == null || $email == ''){
				$user->email = $email;
				$err_message = 'Email cannot be empty';
			} else {
				$password = request('password');
				if ($password == null || $password == ''){
					$user->password = $password;
					$err_message = 'Password cannot be empty';
				} else {
					$user->name = request('name');
                    $user->email = request('email');
					$user->address = request('address');
					$user->age = request('age');
					$user->password = Hash::make(request('password'));
					$user->save();
					return response()->json(['status' => 'ok'], $this-> successStatus);
				}
			}
		}
		return response()->json(['status'=>'error', 'message'=>$err_message], 401); 
	}

	public function updateUser(Request $request, $id){
		$myCredentials = User::findOrfail($id);
		$myCredentials->update($request->all());

		return $myCredentials;
	}

	public function deleteUser(Request $request, $id){
        $myCredentials = User::findOrFail($id);
        $myCredentials->delete();

        return response()->json(null, 204);
    }
	
	public function myCredentials($id){
		return User::find($id);
	}
	
	public function details() 
    { 
        $user = Auth::user(); 
        return response()->json(['success' => $user], $this-> successStatus); 
	} 	
	
	public function showAllUser(){
		return User::all();
	}

	//GOOGLE
	public function googleLogin(Request $request)  {
        $google_redirect_url = route('glogin');
        $gClient = new \Google_Client();
        $gClient->setApplicationName(config('services.google.app_name'));
        $gClient->setClientId(config('services.google.client_id'));
        $gClient->setClientSecret(config('services.google.client_secret'));
        $gClient->setRedirectUri($google_redirect_url);
        $gClient->setDeveloperKey(config('services.google.api_key'));
        $gClient->setScopes(array(
            'https://www.googleapis.com/auth/plus.me',
            'https://www.googleapis.com/auth/userinfo.email',
            'https://www.googleapis.com/auth/userinfo.profile',
        ));
        $google_oauthV2 = new \Google_Service_Oauth2($gClient);
        if ($request->get('code')){
            $gClient->authenticate($request->get('code'));
            $request->session()->put('token', $gClient->getAccessToken());
        }
        if ($request->session()->get('token'))
        {
            $gClient->setAccessToken($request->session()->get('token'));
        }
        if ($gClient->getAccessToken())
        {
            //For logged in user, get details from google using access token
            $guser = $google_oauthV2->userinfo->get();  
               
                $request->session()->put('name', $guser['name']);
                if ($user =User::where('email',$guser['email'])->first())
                {
                    //logged your user via auth login
                }else{
                    //register your user with response data
                }               
         return redirect()->route('user.glist');          
        } else
        {
            //For Guest user, get google login url
            $authUrl = $gClient->createAuthUrl();
            return redirect()->to($authUrl);
        }
    }
    public function listGoogleUser(Request $request){
      $users = User::orderBy('id','DESC')->paginate(5);
     return view('users.list',compact('users'))->with('i', ($request->input('page', 1) - 1) * 5);;
    }

}