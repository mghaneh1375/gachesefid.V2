<?php

namespace App\Http\Controllers;

use App\models\AdviserQuestion;
use App\models\AdviserRate;
use App\models\Grade;
use App\models\SchoolStudent;
use App\models\State;
use App\models\StudentAdviser;
use App\models\User;
use App\models\School;
use App\models\City;
use App\models\NamayandeSchool;
use App\models\ControllerLevel;
use App\models\Lesson;
use App\models\RedundantInfo1;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use PHPExcel;
use PHPExcel_Writer_Excel2007;

class UserController extends Controller {

    public function editSchool() {
        
        if(isset($_POST["firstName"]) && isset($_POST["lastName"]) && isset($_POST["schoolName"]) &&
            isset($_POST["schoolLevel"]) && isset($_POST["kindSchool"]) && isset($_POST["uId"]) && 
            isset($_POST["schoolCity"]) && isset($_POST["phone"]) && isset($_POST["telPhone"]) &&
            isset($_POST["sex"]) && isset($_POST["username"])) {

            $tmp2 = School::whereUId(makeValidInput($_POST["uId"]))->first();

            if($tmp2 == null)
                return;

            $user = User::whereId(makeValidInput($_POST["uId"]));
            $user->firstName = makeValidInput($_POST["firstName"]);
            $user->lastName = makeValidInput($_POST["lastName"]);
            $user->phoneNum = makeValidInput($_POST["phone"]);
            $user->sex = makeValidInput($_POST["sex"]);
            $user->introducer = makeValidInput($_POST["telPhone"]);

            if($user->username != makeValidInput($_POST["username"]))
                $user->username = makeValidInput($_POST["username"]);

            if(isset($_POST["confirm"]) && isset($_POST["password"])) {
                $pas = makeValidInput($_POST["password"]);
                $confirm = makeValidInput($_POST["confirm"]);
                if($pas == $confirm)
                    $user->password = Hash::make($pas);
                else {
                    echo "nok2";
                    return;
                }
            }

            try {
                $user->save();
                $tmp2->name = makeValidInput($_POST["schoolName"]);
                $tmp2->level = makeValidInput($_POST["schoolLevel"]);
                $tmp2->kind = makeValidInput($_POST["kindSchool"]);
                $tmp2->cityId = makeValidInput($_POST["schoolCity"]);
                $tmp2->save();
                echo "ok";
            }
            catch (Exception $x) {
                echo "nok1";
            }
            return;
        }
        echo "nok2";
    }

    public function changeSchoolCode() {
        
        if(isset($_POST["uId"]) && isset($_POST["newCode"])) {

            $uId = makeValidInput($_POST["uId"]);

            if(User::whereId($uId) != null) {

                $newCode = makeValidInput($_POST["newCode"]);

                $condition = ['invitationCode' => $newCode, 'level' => getValueInfo('schoolLevel')];
                $user = User::where($condition)->first();

                if ($user != null) {

                    SchoolStudent::whereUId($uId)->delete();

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
        return view('users', array('mode' => $val, 'users' => User::whereLevel($val)->get()));
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
        return view('users', array('mode' => $val, 'users' => User::where($val)->get()));
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
        return view('users', array('mode' => $val, 'users' => User::whereLevel($val)->get()));
    }

    public function namayandeha() {
        $val = getValueInfo('namayandeLevel');
        $users = User::whereLevel($val)->get();
        foreach ($users as $user) {
            $tmp = RedundantInfo1::whereUId($user->id)->first();
            if($tmp == null) {
                return Redirect::to('profile');
            }

            $city = City::whereId($tmp->cityId);
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

            if(User::whereUsername($username)->count() > 0) {
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

            $tmp = School::whereUId($user->id)->first();
            if($tmp == null)
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

            $tmpCity = City::whereId($tmp->cityId);
            $user->schoolCity = $tmpCity->name;
            $user->schoolState = State::whereId($tmpCity->stateId)->name;
            
            $tmp = NamayandeSchool::where('sId', '=', $user->id)->first();
            if($tmp == null)
                return Redirect::to('profile');

        }
        return view('schoolsList', array('users' => $users));
    }

    public function removeSchool() {
        if(isset($_POST["uId"])) {

            $uId = makeValidInput($_POST["uId"]);
            $level = User::whereId($uId)->level;
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
            if($tmp == null)
                return Redirect::to('profile');

            $user->schoolName = $tmp->name;
            $user->cityId = $tmp->cityId;
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

            $user->schoolKindId = $tmp->kind;
            if($tmp->level == getValueInfo('motevaseteAval'))
                $user->schoolLevel = 'متوسطه اول';
            else if($tmp->level == getValueInfo('motevaseteDovom'))
                $user->schoolLevel = 'متوسطه دوم';
            else
                $user->schoolLevel = 'دبستان';
            
            $user->schoolLevelId = $tmp->level;

            $tmpCity = City::whereId($tmp->cityId);
            $user->schoolCity = $tmpCity->name;
            $user->schoolState = State::whereId($tmpCity->stateId)->name;

            $tmp = NamayandeSchool::whereSId($user->id)->first();
            if($tmp == null)
                return Redirect::to('profile');

            $user->schoolNamayande = User::whereId($tmp->nId)->username;
        }
        
        return view('users', array('mode' => $val, 'users' => $users, 'states' => State::all()));
    }

    public function schoolsExcel() {

        $val = getValueInfo('schoolLevel');

        $users = User::whereLevel($val)->get();
        foreach ($users as $user) {

            $tmp = School::whereUId($user->id)->first();
            if($tmp == null)
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

            $tmpCity = City::whereId($tmp->cityId);
            $user->schoolCity = $tmpCity->name;
            $user->schoolState = State::whereId($tmpCity->stateId)->name;

            $tmp = NamayandeSchool::whereSId($user->id)->first();
            if($tmp == null)
                return Redirect::to('profile');

            $user->schoolNamayande = User::whereId($tmp->nId)->username;
        }


        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("Gachesefid");
        $objPHPExcel->getProperties()->setLastModifiedBy("Gachesefid");
        $objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.");

        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->getActiveSheet()->setCellValue('L1', 'کد مدرسه');
        $objPHPExcel->getActiveSheet()->setCellValue('K1', 'شماره ثابت');
        $objPHPExcel->getActiveSheet()->setCellValue('J1', 'شماره همراه');
        $objPHPExcel->getActiveSheet()->setCellValue('I1', 'نام کاربری مسئول مدرسه');
        $objPHPExcel->getActiveSheet()->setCellValue('H1', 'نام مسئول مدرسه');
        $objPHPExcel->getActiveSheet()->setCellValue('G1', 'نمایندگی');
        $objPHPExcel->getActiveSheet()->setCellValue('F1', 'جنسیت');
        $objPHPExcel->getActiveSheet()->setCellValue('E1', 'مقطع');
        $objPHPExcel->getActiveSheet()->setCellValue('D1', 'نوع مدرسه');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'استان');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'شهر');
        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'نام مدرسه');

        $counter = 2;

        foreach ($users as $user) {
            $objPHPExcel->getActiveSheet()->setCellValue('L' . $counter, $user->invitationCode);
            $objPHPExcel->getActiveSheet()->setCellValue('K' . $counter, $user->introducer);
            $objPHPExcel->getActiveSheet()->setCellValue('J' . $counter, $user->phoneNum);
            $objPHPExcel->getActiveSheet()->setCellValue('I' . $counter, $user->username);
            $objPHPExcel->getActiveSheet()->setCellValue('H' . $counter, $user->firstName . " " . $user->lastName);
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $counter, $user->schoolNamayande);
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $counter, ($user->sex == 0) ? 'دخترانه' : 'پسرانه');
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $counter, $user->schoolLevel);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $counter, $user->schoolKind);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $counter, $user->schoolState);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $counter, $user->schoolCity);
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $counter++, $user->schoolName);
        }


        $fileName = __DIR__ . "/../../../public/registrations/schools.xlsx";

        $objPHPExcel->getActiveSheet()->setTitle('گزارش گیری آزمون ها');

        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save($fileName);


        if (file_exists($fileName)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($fileName).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($fileName));
            readfile($fileName);
            unlink($fileName);
        }

        return view('users', array('mode' => $val, 'users' => $users));
    }

    public function editStudent() {

        if(isset($_POST["firstName"]) && isset($_POST["lastName"]) && isset($_POST["sex"]) && isset($_POST["uId"]) && isset($_POST["NID"])) {

            $user = User::whereId(makeValidInput($_POST["uId"]));

            if($user != null) {

                $user->firstName = makeValidInput($_POST["firstName"]);
                $user->lastName = makeValidInput($_POST["lastName"]);
                $user->sex = makeValidInput($_POST["sex"]);

                $NID = makeValidInput($_POST["NID"]);

                if(!_custom_check_national_code($NID)) {
                    echo "کد ملی وارد شده معتبر نمی باشد";
                    return;
                }

                if(User::whereNID($NID)->count() > 0 && $user->NID != $NID) {
                    echo "کد ملی وارد شده در سامانه موجود است";
                    return;
                }

                $user->NID = $NID;

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
                if($namayande == null)
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
            $level = User::whereId($uId)->level;

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
        
        return view('assignControllers', array('controllers' => User::whereLevel(getValueInfo('controllerLevel'))->get(),
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
                $level->lessonName = Lesson::whereId($level->lessonId)->name;
            echo json_encode($levels);

        }
    }

    public function confirmAdviser() {
        
        if(isset($_POST["uId"])) {

            $user = User::whereId(makeValidInput($_POST["uId"]));

            if($user == null || $user->status != 2 || $user->level != getValueInfo('adviserLevel'))
                return Redirect::to('advisers');

            $user->status = 1;
            $user->save();

        }
        return Redirect::to('advisers');
    }

    public function disableUser() {

        if(isset($_POST["uId"])) {

            $user = User::whereId(makeValidInput($_POST["uId"]));

            if($user != null) {
                $user->status = 2;
                $user->save();
            }
        }
    }

    public function advisers() {

        $val = getValueInfo('adviserLevel');

        return view('users', array('users' => DB::select('select * from users WHERE level = ' . getValueInfo('adviserLevel') . ' and status <> 0'),
            'mode' => $val));
    }

    public function myAdviser() {

        $uId = Auth::user()->id;

        $myAdvisers = StudentAdviser::whereStudentId($uId)->get();

        $users = [];

        if($myAdvisers != null && count($myAdvisers) != 0) {

            foreach ($myAdvisers as $myAdviser) {

                $user = User::whereId($myAdviser->adviserId);
                if ($user == null)
                    return Redirect::route('profile');

                $questions = AdviserQuestion::all();

                $avg = 0;
                foreach ($questions as $question) {
                    $tmp = AdviserRate::whereUId($uId)->whereAdviserId($myAdviser->adviserId)->whereQuestionId($question->id)->first();
                    if($tmp == null) {
                        $question->rate = -1;
                    }
                    else {
                        $question->rate = $tmp->rate;
                        $avg += $question->rate;
                    }
                }

                $user->totalStudents = StudentAdviser::whereAdviserId($myAdviser->adviserId)->whereStatus(true)->count();
                $user->questions = $questions;
                $user->status = $myAdviser->status;

                $users[count($users)] = $user;
            }

            return view('myAdviser', array('myAdvisers' => $users));
        }

        return view('myAdviser', array('myAdvisers' => $users));
    }

    public function cancelAdviser($adviserId) {

        $uId = Auth::user()->id;
        
        $myAdviser = StudentAdviser::whereStudentId($uId)->whereAdviserId($adviserId)->first();

        if($myAdviser != null)
            $myAdviser->delete();
        
        return Redirect::route('advisersList');
    }

    public function submitRate() {

        if(isset($_POST["rate"])) {

            $rate = makeValidInput($_POST["rate"]);
            $adviserId = explode('_', makeValidInput($_POST["adviserId"]))[0];
            $studentId = makeValidInput($_POST["studentId"]);
            $questionId = makeValidInput($_POST["questionId"]);

            $condition = ['adviserId' => $adviserId, 'studentId' => $studentId, 'status' => 1];

            if(StudentAdviser::where($condition)->count() > 0) {

                $condition = ['adviserId' => $adviserId, 'uId' => $studentId, 'questionId' => $questionId];

                AdviserRate::where($condition)->delete();

                $tmp = new AdviserRate();
                $tmp->adviserId = $adviserId;
                $tmp->questionId = $questionId;
                $tmp->uId = $studentId;
                $tmp->rate = $rate;

                $tmp->save();

                echo "ok";
                return;
            }

            echo "nok2";
            return;
        }

        echo "nok";
    }

    public function setAsMyAdviser() {

        if(isset($_POST["adviserId"])) {

            $uId = Auth::user()->id;
            $adviserId = makeValidInput($_POST["adviserId"]);

            if(User::whereId($adviserId) == null) {
                echo "nok2";
                return;
            }

            $tmp = new StudentAdviser();
            $tmp->studentId = $uId;
            $tmp->adviserId = $adviserId;
            $tmp->status = false;

            try {
                $tmp->save();
                echo "ok";
            }
            catch (Exception $x) {
                echo "nok3" . $x->getMessage();
            }
            return;
        }

        echo "nok1";
    }
}