<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller; 
use App\posts; 
use Illuminate\Support\Facades\Auth; 
use Validator;
use Carbon\Carbon;
use Laravel\Passport\Passport; 
use Illuminate\Support\Facades\Hash;
//use App\Http\Controllers\Auth\LoginProxy;

class PostController extends Controller
{
    public $successStatus = 200;
		
	public function show(){
		return posts::all();
	}

	public function myPost($id){
		return posts::find($id);
	}

	public function createPost(Request $request){
		return posts::create($request->all());
    }
    
    public function updatePost(Request $request, $id){
		$myPost = posts::findOrfail($id);
		$myPost->update($request->all());

		return $myPost;
    }
    
    public function deletePost(Request $request, $id){
        $posts = posts::findOrFail($id);
        $posts->delete();

        return response()->json(null, 204);
    }
	
}