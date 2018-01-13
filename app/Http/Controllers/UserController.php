<?php

namespace App\Http\Controllers;

use App\models\Grade;
use App\models\State;
use App\models\User;
use App\models\School;
use App\models\City;
use App\models\NamayandeSchool;
use App\models\ControllerLevel;
use App\models\Lesson;
use App\models\RedundantInfo1;
use Auth;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;

class UserController extends Controller {

    public function editSchool() {
        
        if(isset($_POST["firstName"]) && isset($_POST["lastName"]) && isset($_POST["schoolName"]) &&
            isset($_POST["schoolLevel"]) && isset($_POST["kindSchool"]) && isset($_POST["uId"]) && 
            isset($_POST["schoolCity"]) && isset($_POST["phone"]) && isset($_POST["telPhone"]) &&
            isset($_POST["sex"])) {

            $tmp2 = School::whereUId(makeValidInput($_POST["uId"]))->first();

            if($tmp2 == null || count($tmp2) == 0)
                return;

            $user = User::find(makeValidInput($_POST["uId"]));
            $user->firstName = makeValidInput($_POST["firstName"]);
            $user->lastName = makeValidInput($_POST["lastName"]);
            $user->phoneNum = makeValidInput($_POST["phone"]);
            $user->sex = makeValidInput($_POST["sex"]);
            $user->introducer = makeValidInput($_POST["telPhone"]);

            $user->save();

            $tmp2->name = makeValidInput($_POST["schoolName"]);
            $tmp2->level = makeValidInput($_POST["schoolLevel"]);
            $tmp2->kind = makeValidInput($_POST["kindSchool"]);
            $tmp2->cityId = makeValidInput($_POST["schoolCity"]);
            $tmp2->save();

            echo "ok";
            
        }
    }

    public function changeSchoolCode() {
        
        if(isset($_POST["uId"]) && isset($_POST["newCode"])) {

            $uId = makeValidInput($_POST["uId"]);

            if(User::find($uId) != null) {

                $newCode = makeValidInput($_POST["newCode"]);

                $condition = ['invitationCode' => $newCode, 'level' => getValueInfo('schoolLevel')];
                $user = User::where($condition)->first();

                if ($user != null && count($user) > 0) {

                    SchoolStudent::where('uId', '=', $uId)->delete();

                    $tmp = new SchoolStudent();
                    $tmp->sId = $user->id;
                    $tmp->uId = $uId;
                    $tmp->save();
                    echo "ok";
                    return;
                }
            }
            
        }

        echo "nok";
        
    }
    
    public function admins() {
        return "salam";
    }

    public function operators_2() {

        $val = getValueInfo('operator2Level');
        return view('users', array('mode' => $val, 'users' => User::where('level', '=', $val)->get()));
    }

    public function addOperator2() {
        
        return view('addUser', array('url' => route('addOperator2')));
    }

    public function doAddOperator2() {

        $msg = $username = $password = $sex = $firstName = $lastName = $phoneNum = "";

        if (isset($_POST["doAdd"])) {

            $username = makeValidInput($_POST["username"]);
            $password = makeValidInput($_POST["password"]);
            $firstName = makeValidInput($_POST["firstName"]);
            $lastName = makeValidInput($_POST["lastName"]);
            $phoneNum = makeValidInput($_POST["phoneNum"]);
            $sex = makeValidInput($_POST["sex"]);

            if($sex == "none") {
                $msg = "لطفا جنسیت خود را وارد نمایید";
            }

            else if(User::where('username', '=', $username)->count() > 0) {
                $msg = "نام کاربری وارد شده در سامانه موجود است";
            }

            else {
                $user = new User();

                $user->username = $username;
                $user->firstName = $firstName;
                $user->lastName = $lastName;
                $user->password = Hash::make($password);
                $user->phoneNum = $phoneNum;
                $user->sex = $sex;
                $user->status = 1;
                $user->level = getValueInfo('operator2Level');

                $user->save();

                return Redirect::to(route('operators_2'));
            }
        }

        return view('addUser', array('url' => route('addOperator2'), "msg" => $msg, "username" => $username,
            "phoneNum" => $phoneNum, "sex" => $sex, "firstName" => $firstName, "lastName" => $lastName));
    }
    
    public function operators_1() {

        $val = getValueInfo('operator1Level');
        return view('users', array('mode' => $val, 'users' => User::where('level', '=', $val)->get()));
    }

    public function addOperator1() {
        return view('addUser', array('url' => route('addOperator1')));
    }

    public function doAddOperator1() {

        $msg = $username = $password = $sex = $firstName = $lastName = $phoneNum = "";

        if (isset($_POST["doAdd"])) {

            $username = makeValidInput($_POST["username"]);
            $password = makeValidInput($_POST["password"]);
            $firstName = makeValidInput($_POST["firstName"]);
            $lastName = makeValidInput($_POST["lastName"]);
            $phoneNum = makeValidInput($_POST["phoneNum"]);
            $sex = makeValidInput($_POST["sex"]);

            if($sex == "none") {
                $msg = "لطفا جنسیت خود را وارد نمایید";
            }

            else if(User::where('username', '=', $username)->count() > 0) {
                $msg = "نام کاربری وارد شده در سامانه موجود است";
            }

            else {
                $user = new User();

                $user->username = $username;
                $user->firstName = $firstName;
                $user->lastName = $lastName;
                $user->password = Hash::make($password);
                $user->phoneNum = $phoneNum;
                $user->sex = $sex;
                $user->status = 1;
                $user->level = getValueInfo('operator1Level');

                $user->save();

                return Redirect::to(route('operators_1'));
            }
        }

        return view('addUser', array('url' => route('addOperator1'), "msg" => $msg, "username" => $username,
            "phoneNum" => $phoneNum, "sex" => $sex, "firstName" => $firstName, "lastName" => $lastName));
    }

    public function controllers() {
        $val = getValueInfo('controllerLevel');
        return view('users', array('mode' => $val, 'users' => User::where('level', '=', $val)->get()));
    }

    public function namayandeha() {
        $val = getValueInfo('namayandeLevel');
        $users = User::where('level', '=', $val)->get();
        foreach ($users as $user) {
            $tmp = RedundantInfo1::where('uId', '=', $user->id)->first();
            if($tmp == null || count($tmp) == 0) {
                return Redirect::to('profile');
            }

            $city = City::find($tmp->cityId);
            if($city == null)
                return Redirect::to('profile');
            $user->city = $city->name;

        }
        return view('users', array('mode' => $val, 'users' => $users));
    }

    public function addNamayande() {
        return view('addUser', array('url' => route('addNamayande'), 'states' => State::all()));
    }

    public function doAddNamayande() {

        $msg = $username = $password = $firstName = $lastName = $phoneNum = "";

        if (isset($_POST["doAdd"])) {

            $username = makeValidInput($_POST["username"]);
            $password = makeValidInput($_POST["password"]);
            $firstName = makeValidInput($_POST["firstName"]);
            $lastName = makeValidInput($_POST["lastName"]);
            $phoneNum = makeValidInput($_POST["phoneNum"]);
            $city = makeValidInput($_POST["city"]);

            if(User::where('username', '=', $username)->count() > 0) {
                $msg = "نام کاربری وارد شده در سامانه موجود است";
            }

            else {

                try {
                    DB::transaction(function () use ($username, $firstName, $lastName, $password, $city, $phoneNum) {

                        $user = new User();

                        $user->username = $username;
                        $user->firstName = $firstName;
                        $user->lastName = $lastName;
                        $user->password = Hash::make($password);
                        $user->phoneNum = $phoneNum;
                        $user->sex = 1;
                        $user->status = 1;
                        $user->invitationCode = generateActivationCode();
                        $user->level = getValueInfo('namayandeLevel');

                        $user->save();

                        $tmp = new RedundantInfo1();
                        $tmp->uId = $user->id;
                        $tmp->gradeId = Grade::first()->id;
                        $tmp->cityId = $city;

                        $tmp->save();
                    });
                }
                catch (Exception $x) {
                    $msg = "خطایی در انجام عملیات مورد نظر رخ داده است";
                }

                if(empty($msg))
                    return Redirect::to(route('namayandeha'));
            }
        }

        return view('addUser', array('url' => route('addNamayande'), "msg" => $msg, "username" => $username, 'states' => State::all(),
            "phoneNum" => $phoneNum, "sex" => 1, "firstName" => $firstName, "lastName" => $lastName));
    }

    public function schoolsList() {

        $users = User::schools()->get();
        foreach ($users as $user) {

            $tmp = School::where('uId', '=', $user->id)->first();
            if($tmp == null || count($tmp) == 0)
                return Redirect::to('profile');

            $user->schoolName = $tmp->name;
            switch ($tmp->kind) {
                case getValueInfo('sampadSch'):
                default:
                    $user->schoolKind = 'سمپاد';
                    break;
                case getValueInfo('gheyrSch'):
                    $user->schoolKind = 'غیرانتفاعی';
                    break;
                case getValueInfo('nemoneSch'):
                    $user->schoolKind = 'نمونه دولتی';
                    break;
                case getValueInfo('shahedSch'):
                    $user->schoolKind = 'شاهد';
                    break;
                case getValueInfo('dolatiSch'):
                    $user->schoolKind = 'سایر';
                    break;
                case getValueInfo('HeyatSch'):
                    $user->schoolKind = 'هیئت امنایی';
                    break;
            }

            $user->schoolLevel = ($tmp->level == getValueInfo('motevaseteAval')) ? 'متوسطه اول' : 'متوسطه دوم';

            $user->schoolCity = City::find($tmp->cityId)->name;
            
            $tmp = NamayandeSchool::where('sId', '=', $user->id)->first();
            if($tmp == null || count($tmp) == 0)
                return Redirect::to('profile');

        }
        return view('schoolsList', array('users' => $users));
    }

    public function removeSchool() {
        if(isset($_POST["uId"])) {

            $uId = makeValidInput($_POST["uId"]);
            $level = User::find($uId)->level;
            if($level == getValueInfo('schoolLevel'))
                User::destroy($uId);
        }
        return Redirect::to(route('namayandeSchool'));
    }

    public function schools() {

        $val = getValueInfo('schoolLevel');

        $users = User::whereLevel($val)->get();
        foreach ($users as $user) {

            $tmp = School::whereUId($user->id)->first();
            if($tmp == null || count($tmp) == 0)
                return Redirect::to('profile');

            $user->schoolName = $tmp->name;
            switch ($tmp->kind) {
                case getValueInfo('sampadSch'):
                default:
                    $user->schoolKind = 'سمپاد';
                    break;
                case getValueInfo('gheyrSch'):
                    $user->schoolKind = 'غیرانتفاعی';
                    break;
                case getValueInfo('nemoneSch'):
                    $user->schoolKind = 'نمونه دولتی';
                    break;
                case getValueInfo('shahedSch'):
                    $user->schoolKind = 'شاهد';
                    break;
                case getValueInfo('dolatiSch'):
                    $user->schoolKind = 'دولتی';
                    break;
                case getValueInfo('sayerSch'):
                    $user->schoolKind = 'سایر';
                    break;
                case getValueInfo('HeyatSch'):
                    $user->schoolKind = 'هیئت امنایی';
                    break;
            }
            
            $user->schoolLevel = ($tmp->level == getValueInfo('motevaseteAval')) ? 'متوسطه اول' :
                ($tmp->level == getValueInfo('motevaseteDovom')) ? 'متوسطه دوم' : 'دبستان';

            $user->schoolCity = City::find($tmp->cityId)->name;

            $tmp = NamayandeSchool::whereSId($user->id)->first();
            if($tmp == null || count($tmp) == 0)
                return Redirect::to('profile');

            $user->schoolNamayande = User::find($tmp->nId)->username;
        }
        
        return view('users', array('mode' => $val, 'users' => $users));
    }

    public function editStudent() {

        if(isset($_POST["firstName"]) && isset($_POST["lastName"]) && isset($_POST["sex"]) && isset($_POST["uId"])) {

            $user = User::find(makeValidInput($_POST["uId"]));

            if($user != null) {

                $user->firstName = makeValidInput($_POST["firstName"]);
                $user->lastName = makeValidInput($_POST["lastName"]);
                $user->sex = makeValidInput($_POST["sex"]);

                $user->save();
                echo "ok";
                return;
            }
        }

        echo "nok";
    }

    public function addSchool() {
        return view('addUser', array('url' => route('addSchool'), 'states' => State::all()));
    }

    public function doAddSchool() {

        $msg = $username = $password = $firstName = $sex = $schoolPhone =
        $lastName = $namayandeCode = $phoneNum = $schoolName = $kindSchool = $schoolLevel = "";

        if (isset($_POST["doAdd"])) {

            $username = makeValidInput($_POST["username"]);
            $password = makeValidInput($_POST["password"]);
            $firstName = makeValidInput($_POST["firstName"]);
            $lastName = makeValidInput($_POST["lastName"]);

            if(Auth::user()->level == getValueInfo('namayandeLevel'))
                $namayandeCode = Auth::user()->invitationCode;
            else
                $namayandeCode = makeValidInput($_POST["namayandeCode"]);

            $phoneNum = makeValidInput($_POST["phoneNum"]);
            $sex = makeValidInput($_POST["sex"]);
            $schoolName = makeValidInput($_POST["schoolName"]);
            $kindSchool = makeValidInput($_POST["kindSchool"]);
            $schoolLevel = makeValidInput($_POST["schoolLevel"]);
            $schoolPhone = makeValidInput($_POST["schoolPhone"]);
            $city = makeValidInput($_POST["city"]);

            if($sex == "none") {
                $msg = "لطفا جنسیت مدرسه را وارد نمایید";
            }

            else if(User::whereUsername($username)->count() > 0) {
                $msg = "نام کاربری وارد شده در سامانه موجود است";
            }

            else {
                $condition = ['level' => getValueInfo('namayandeLevel'),
                    'invitationCode' => $namayandeCode];

                $namayande = User::where($condition)->first();
                if($namayande == null || count($namayande) == 0)
                    $msg = "کد نمایندگی وارد شده ناصحیح است";

                else {

                    try {
                        DB::transaction(function () use (
                            $username, $firstName, $lastName, $password,
                            $phoneNum, $sex, $namayande, $kindSchool, $schoolLevel, $schoolName, $schoolPhone, $city
                        ) {

                            $user = new User();
                            $user->username = $username;
                            $user->firstName = $firstName;
                            $user->lastName = $lastName;
                            $user->password = Hash::make($password);
                            $user->phoneNum = $phoneNum;
                            $user->sex = $sex;
                            $user->status = 1;
                            $user->invitationCode = generateActivationCode();
                            $user->level = getValueInfo('schoolLevel');
                            $user->introducer = $schoolPhone;

                            $user->save();

                            $tmp = new NamayandeSchool();
                            $tmp->nId = $namayande->id;
                            $tmp->sId = $user->id;
                            $tmp->save();

                            $tmp2 = new School();
                            $tmp2->name = $schoolName;
                            $tmp2->level = $schoolLevel;
                            $tmp2->kind = $kindSchool;
                            $tmp2->uId = $user->id;
                            $tmp2->cityId = $city;
                            $tmp2->save();
                        });
                    }
                    catch (Exception $x) {
                        $msg = "خطایی در انجام عملیات مورد نظر رخ داده است" . $x->getMessage();
                    }

                    if(empty($msg))
                        return Redirect::to(route('schools'));
                }
            }
        }

        return view('addUser', array('url' => route('addSchool'), "msg" => $msg, "username" => $username, 'states' => State::all(),
            "phoneNum" => $phoneNum, "sex" => $sex, "firstName" => $firstName, "lastName" => $lastName));
    }

    public function addControllers() {
        return view('addUser', array('url' => route('addControllers')));
    }

    public function doAddControllers() {

        $msg = $username = $password = $sex = $firstName = $lastName = $phoneNum = "";

        if (isset($_POST["doAdd"])) {

            $username = makeValidInput($_POST["username"]);
            $password = makeValidInput($_POST["password"]);
            $firstName = makeValidInput($_POST["firstName"]);
            $lastName = makeValidInput($_POST["lastName"]);
            $phoneNum = makeValidInput($_POST["phoneNum"]);
            $sex = makeValidInput($_POST["sex"]);

            if($sex == "none") {
                $msg = "لطفا جنسیت خود را وارد نمایید";
            }

            else if(User::whereUsername($username)->count() > 0) {
                $msg = "نام کاربری وارد شده در سامانه موجود است";
            }

            else {
                $user = new User();

                $user->username = $username;
                $user->firstName = $firstName;
                $user->lastName = $lastName;
                $user->password = Hash::make($password);
                $user->phoneNum = $phoneNum;
                $user->sex = $sex;
                $user->status = 1;
                $user->level = getValueInfo('controllerLevel');

                $user->save();

                return Redirect::to(route('controllers'));
            }
        }

        return view('addUser', array('url' => route('addControllers'), "msg" => $msg, "username" => $username,
            "phoneNum" => $phoneNum, "sex" => $sex, "firstName" => $firstName, "lastName" => $lastName));
    }

    public function removeUser($mode) {

        if(isset($_POST["uId"])) {

            $uId = makeValidInput($_POST["uId"]);
            $level = User::find($uId)->level;

            if(!($level == getValueInfo('superAdminLevel') ||
            ($level == getValueInfo('adminLevel') && Auth::user()->level != getValueInfo('superAdminLevel')))) {
                User::destroy($uId);
            }
        }
        if($mode == getValueInfo('operator2Level'))
            return Redirect::to(route('operators_2'));
        else if($mode == getValueInfo('operator1Level'))
            return Redirect::to(route('operators_1'));
        else if($mode == getValueInfo('controllerLevel'))
            return Redirect::to(route('controllers'));
        else if($mode == getValueInfo('adviserLevel'))
            return Redirect::to(route('advisers'));
        else if($mode == getValueInfo('namayandeLevel'))
            return Redirect::to(route('namayandeha'));
        else if($mode == getValueInfo('schoolLevel'))
            return Redirect::to(route('schools'));
        
        return Redirect::to(route('profile'));
    }

    public function assignControllers() {
        
        return view('assignControllers', array('controllers' => User::where('level', '=', getValueInfo('controllerLevel'))->get(),
            'grades' => Grade::all()));
    }

    public function doAssignToController() {

        if(isset($_POST["lessons"]) && isset($_POST["controllerId"])) {

            $controllerId = makeValidInput($_POST["controllerId"]);
            $lessons = $_POST["lessons"];

            foreach ($lessons as $lesson) {
                $controllerLevel = new ControllerLevel();
                $controllerLevel->controllerId = $controllerId;
                $controllerLevel->lessonId = makeValidInput($lesson);
                try {
                    $controllerLevel->save();
                }
                catch (Exception $x) {}
            }

            echo "ok";

        }
    }

    public function getControllerLevelsDir() {

        if(isset($_POST["controllerId"])) {

            $levels = ControllerLevel::where('controllerId', '=', makeValidInput($_POST["controllerId"]))->get();
            foreach ($levels as $level)
                $level->lessonName = Lesson::find($level->lessonId)->name;
            echo json_encode($levels);

        }
    }

    public function confirmAdviser() {
        
        if(isset($_POST["uId"])) {

            $user = User::find(makeValidInput($_POST["uId"]));

            if($user == null || $user->status != 2 || $user->level != getValueInfo('adviserLevel'))
                return Redirect::to('advisers');

            $user->status = 1;
            $user->save();

        }
        return Redirect::to('advisers');
    }

    public function advisers() {

        $val = getValueInfo('adviserLevel');

        return view('users', array('users' => DB::select('select * from users WHERE level = ' . getValueInfo('adviserLevel') . ' and status <> 0'),
            'mode' => $val));
    }
}