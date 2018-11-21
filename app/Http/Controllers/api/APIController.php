<?php

namespace App\Http\Controllers\api;

use App\models\ClassModel;
use App\models\Exam;
use App\models\ExamAssist;
use App\models\Financial;
use App\models\Lesson;
use App\models\Oauth_access_tokens;
use App\models\Shahrestan;
use App\models\State;
use App\models\SubSupervision;
use App\models\Supervision;
use App\models\User;
use Carbon\Carbon;
use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Console\Parser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class APIController extends Controller {

    private $content;

    public function __construct(){
        $this->content = array();
    }

    public function login() {

        if(isset($_POST["username"]) && isset($_POST["password"])) {

            $username = makeValidInput($_POST["username"]);
            $password = makeValidInput($_POST["password"]);

            if(Auth::attempt(['username' => $username, 'password' => $password], true)){
                $user = Auth::user();

                $token = Oauth_access_tokens::where('user_id', '=', $user->id)->first();

                if($token == null || Carbon::now() > $token->expires_at) {
                    Oauth_access_tokens::where('user_id', '=', $user->id)->delete();
                    $this->content['token'] =  $user->createToken('BogenDesign1982')->accessToken;
                    $user->api_token = $this->content['token'];
                    try {
                        $user->save();
                    }
                    catch (\Exception $x) {
                        dd($x->getMessage());
                    }
                    echo $this->content['token'];
                }
                else
                    echo $user->api_token;
            }
            else {
                echo "nok";
            }
        }
    }

    public function logout() {

        $user = Auth::user();
        Oauth_access_tokens::where('user_id', '=', $user->id)->delete();
        
        $user->api_token = "";
        $user->save();
        Session::flush();

        echo \GuzzleHttp\json_encode(['msg' => 'ok', 'status' => empty($user->api_token)]);
    }
}