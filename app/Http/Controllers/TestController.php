<?php

namespace App\Http\Controllers;

use App\models\Activity;
use App\models\Adab;
use App\models\Amaken;
use App\models\Block;
use App\models\Hotel;
use App\models\LogModel;
use App\models\Majara;
use App\models\Message;
use App\models\Opinion;
use App\models\PicItem;
use App\models\PlaceStyle;
use App\models\Restaurant;
use App\models\User;
use Auth;
use CURLFile;
use Illuminate\Support\Facades\Hash;

class TestController extends Controller {
 
    static $cookie;

    public function start($c) {

        $testClass = new TestController();

        $methods = [];

        foreach (get_class_methods($testClass) as $method) {
            if (strpos($method, "test") === 0)
                $methods[count($methods)] = $method;
        }

        return view('test', array('methods' => $methods, 'cookie' => $c));
    }

    public function methodTest() {

        if(!isset($_POST["method"]) || !isset($_POST["cookie"]))
            return;

        TestController::$cookie = makeValidInput($_POST["cookie"]);

        $testClass = new TestController();
        $requestedMethod = makeValidInput($_POST["method"]);

        foreach (get_class_methods($testClass) as $method) {
            if (strpos($method, "test") === 0 && $method == $requestedMethod) {
                echo call_user_func(array($testClass, $method));
            }
        }
    }

    public function sdtestSendResetPasSMS() {

        $data = [
            'username' => 'mghaneh1375',
            'mode' => 2,
            'val' => '09214915905'
        ];

        $ch = curl_init(route('doResetPas'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, 0);
        curl_setopt($ch, CURLOPT_HEADER, 1);  // DO NOT RETURN HTTP HEADERS
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  // RETURN THE CONTENTS OF THE CALL
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $body = substr($response, curl_getinfo($ch, CURLINFO_HEADER_SIZE));

        if ($httpCode != 200 && $httpCode != 302)
            return 'sendMsg response: ' . $httpCode;

        return $body;
    }

    public function testChangePas() {

        $data = [
            'oldPas' => 'Mg22743823',
            'newPas' => '123456',
            'confirmPas' => '123456'
        ];

        $ch = curl_init(route('doChangePas'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json', 'Cookie: ' . TestController::$cookie]);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, 0);
        curl_setopt($ch, CURLOPT_HEADER, 1);  // DO NOT RETURN HTTP HEADERS
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  // RETURN THE CONTENTS OF THE CALL
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($httpCode != 200 && $httpCode != 302)
            return 'sendMsg response: ' . $httpCode;

        if(Hash::check('123456', Auth::user()->password))
            return "ok";

        return "changing pas failed";
    }
}