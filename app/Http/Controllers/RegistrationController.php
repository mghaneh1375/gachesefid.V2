<?php

namespace App\Http\Controllers;

use App\models\Grade;
use App\models\NamayandeSchool;
use App\models\PointConfig;
use App\models\QuizRegistry;
use App\models\RedundantInfo1;
use App\models\RegularQuizQueue;
use App\models\School;
use App\models\SchoolStudent;
use App\models\Activation;
use App\models\RegularQuiz;
use App\models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Writer_Excel2007;

class RegistrationController extends Controller {

    public function getActivation($err = "") {
        return view('getActivation', array('err' => $err));
    }

    public function deleteStdFromSchool() {

        if(isset($_POST['uId'])) {

            $uId = makeValidInput($_POST["uId"]);

            $user = Auth::user();

            if($user->level == getValueInfo('schoolLevel')) {
                $condition = ['sId' => $user->id, 'uId' => $uId];
                if(SchoolStudent::where($condition)->count() == 0)
                    return;
            }
            else {
                $tmp = DB::select('select count(*) as countNum from namayandeSchool nS, schoolStudent sS WHERE sS.sId = nS.sId and nS.nId = ' . $user->id . ' and sS.uId = ' . $uId);
                if($tmp == null || count($tmp) == 0 || $tmp[0]->countNum == 0)
                    return;
            }

            SchoolStudent::whereUId($uId)->delete();
            echo "ok";
            return;

        }

    }

    public function doGetActivation() {
        
        if(isset($_POST["phoneNum"])) {

            $phoneNum = makeValidInput($_POST["phoneNum"]);

            $activation = Activation::wherePhoneNum( $phoneNum)->first();

            if($activation == null)
                return $this->getActivation('شماره وارد شده در سیستم وجود ندارد');

            $user = User::where('phoneNum', '=', $phoneNum)->first();
            if($user == null)
                return $this->getActivation('شماره وارد شده در سیستم وجود ندارد');

            return view("registration", array("mode" => "pending", "phoneNum" => $phoneNum,
                'uId' => $user->id, 'reminder' => 300 - time() + $activation->sendTime));

        }
        return $this->getActivation('شماره وارد شده در سیستم وجود ندارد');
    }
    
    public function registration() {
        return view('registration', array("mode" => "pass1"));
    }

    public function doRegistration() {

        $msg = $NID = $username = $password = $sex = $level = $firstName = $lastName = $phoneNum = $invitationCode = "";

        if (isset($_POST["doRegistration"])) {

            $allow = 0;

            $username = makeValidInput($_POST["username"]);
            $password = makeValidInput($_POST["password"]);
            $firstName = makeValidInput($_POST["firstName"]);
            $lastName = makeValidInput($_POST["lastName"]);
            $phoneNum = makeValidInput($_POST["phoneNum"]);
            $level = makeValidInput($_POST["level"]);
            $NID = makeValidInput($_POST["NID"]);
            $sex = makeValidInput($_POST["sex"]);

            if($sex == "none") {
                $msg = "لطفا جنسیت خود را وارد نمایید";
            }

            else if($level == "none") {
                $msg = "لطفا عنوان ثبت نام خود را وارد نمایید";
            }

            else if(User::whereUsername($username)->count() > 0) {
                $msg = "نام کاربری وارد شده در سامانه موجود است";
            }

            else if(User::whereNID($NID)->count() > 0) {
                $msg = "کد ملی وارد شده در سامانه موجود است";
            }

            else if(!_custom_check_national_code($NID)) {
                $msg = "کد ملی وارد شده معتبر نمی باشد";
            }

            else {

                if (isset($_POST["invitationCode"]) && !empty($_POST["invitationCode"])) {

                    $invitationCode = makeValidInput($_POST["invitationCode"]);

                    $user = User::whereInvitationCode($invitationCode)->select("id", "level")->first();

                    if ($user == null) {
                        $msg = "کد معرف اشتباه است";
                        $allow = -1;
                    }
                    else if ($user->level == 1 && $level == 2) {
                        $msg = "شما اجازه ی استفاده از این کد را ندارید";
                        $allow = -1;
                    } 
                    else
                        $allow = 1;
                }

                if ($allow != -1) {

                    $user = new User();

                    $user->username = $username;
                    $user->firstName = $firstName;
                    $user->lastName = $lastName;
                    $user->NID = $NID;
                    $user->password = Hash::make($password);
                    $user->phoneNum = $phoneNum;
                    
                    if($level == 1)
                        $user->level = getValueInfo("studentLevel");
                    else
                        $user->level = getValueInfo("adviserLevel");
                    
                    $user->sex = $sex;
                    $user->invitationCode = generateInvitationCode();

                    if($allow == 1)
                        $user->introducer = $invitationCode;

                    $user->save();

                    $activation = new Activation();
                    $activationCode = generateActivationCode();
                    $activation->code = $activationCode;
                    $activation->phoneNum = $phoneNum;
                    $activation->sendTime = time();
                    $activation->save();

                    sendSMS($phoneNum, $activationCode, "activationCode");

                    return view("registration", array("mode" => "pending", "phoneNum" => $phoneNum,
                        'uId' => $user->id, 'reminder' => 300));
                }
            }

        }

        else if(isset($_POST["activeProfile"]) && isset($_POST["phoneNum"])) {

            $activationCode = makeValidInput($_POST["activationCode"]);
            $phoneNum = makeValidInput($_POST["phoneNum"]);
            $uId = makeValidInput($_POST["uId"]);

            if(strlen($phoneNum) == 10)
                $phoneNum = '0' . $phoneNum;

            $activation = Activation::wherePhoneNum( $phoneNum)->first();

            if($activation != null && $activationCode == $activation->code) {
                $user = User::whereId($uId);
                include_once 'MoneyController.php';
                if($user != null &&
                    $user->introducer != null && !empty($user->introducer)) {

                    $invitationAmount = PointConfig::first()->invitationPoint;

                    charge($invitationAmount, User::whereInvitationCode($user->introducer)->first()->id, getValueInfo("invitationTransaction"), getValueInfo("money2"));
                    charge($invitationAmount, $uId, getValueInfo("invitationTransaction"), getValueInfo("money2"));
                }

                if($user->level == getValueInfo('studentLevel'))
                    $user->status = 1;
                else
                    $user->status = 2;
                $user->save();
                charge(PointConfig::first()->init, $user->id, getValueInfo('initTransaction'), getValueInfo('money2'));

                Activation::wherePhoneNum( $user->phoneNum)->delete();

                return Redirect::to('login');
            }

            if($activation != null)
                $reminder = 300 - time() + $activation->sendTime;
            else
                $reminder = 0;

            $msg = "کد فعال سازی وارد شده معتبر نمی باشد";
            return view("registration", array("mode" => "pending",
                'uId' => $uId, "msg" => $msg, 'phoneNum' => $phoneNum, 'reminder' => $reminder));

        }
        
        else if(isset($_POST["resendActivation"])) {

            $phoneNum = makeValidInput($_POST["phoneNum"]);
            $activation = Activation::wherePhoneNum( $phoneNum)->first();

            if($activation->sendTime >= time() - 300)
                return view("registration", array("mode" => "pending", "phoneNum" => $phoneNum, 
                    'uId' => makeValidInput($_POST["uId"]), 'reminder' => 300 - time() + $activation->sendTime));

            $uId = makeValidInput($_POST["uId"]);

            sendSMS($phoneNum, $activation->code, "activationCode");

            $activation->sendTime = time();
            $activation->save();
            return view("registration", array("mode" => "pending", "phoneNum" => $phoneNum,
                'uId' => $uId, 'reminder' => 300 - time() + $activation->sendTime));
        }

        return view('registration', array("mode" => "pass1", "msg" => $msg, "username" => $username, 'NID' => $NID,
            "phoneNum" => $phoneNum, "sex" => $sex, "firstName" => $firstName, "lastName" => $lastName,
            'invitationCode' => $invitationCode, 'level' => $level));

    }

    public function groupRegistration($err = "") {
        return view('groupRegistration', array('err' => $err));
    }

    public function groupQuizRegistration() {

        $date = getToday()["date"];

        $quizes = DB::select('select * from regularQuiz WHERE startReg <= ' . $date . ' and endReg >= ' . $date);
        foreach ($quizes as $quiz) {
            $quiz->startDate = convertStringToDate($quiz->startDate);
            $quiz->endDate = convertStringToDate($quiz->endDate);
            $quiz->startTime = convertStringToTime($quiz->startTime);
            $quiz->endTime = convertStringToTime($quiz->endTime);
            $quiz->startReg = convertStringToDate($quiz->startReg);
            $quiz->endReg = convertStringToDate($quiz->endReg);
        }

        $user = Auth::user();
        if($user->level == getValueInfo('schoolLevel'))
            return view('groupQuizRegistration', array('quizes' => $quizes, 'students' =>
                DB::select('select users.id, firstName, lastName from schoolStudent, users WHERE users.id = uId and sId = ' . $user->id)));

        return view('groupQuizRegistration', array('quizes' => $quizes, 'students' =>
            DB::select('select users.id, firstName, lastName from schoolStudent sS, users, namayandeSchool nS WHERE nS.nId = ' . $user->id . ' and nS.sId = sS.sId and users.id = sS.uId')));

    }

    public function getStdOfQuiz() {

        if(isset($_POST["qId"])) {

            $qId = makeValidInput($_POST["qId"]);

            $user = Auth::user();

            if ($user->level == getValueInfo('schoolLevel'))
                $stds = DB::select('select users.id from schoolStudent, users WHERE users.id = uId and sId = ' . $user->id);
            else
                $stds = DB::select('select users.id from schoolStudent sS, users, namayandeSchool nS WHERE nS.nId = ' . $user->id . ' and nS.sId = sS.sId and users.id = sS.uId');

            $regularQuizMode = getValueInfo('regularQuiz');

            foreach ($stds as $std) {
                $condition = ['uId' => $std->id, 'qId' => $qId, 'quizMode' => $regularQuizMode];
                $tmp = QuizRegistry::where($condition)->first();
                if($tmp != null) {
                    $std->status = 3;
                    $std->online = ($tmp->online == 1) ? 'آنلاین' : 'حضوری';
                }
                else {
                    $condition = ['studentId' => $std->id, 'qId' => $qId];
                    $tmp = RegularQuizQueue::where($condition)->first();
                    if($tmp != null) {
                        $std->status = 2;
                        $std->online = ($tmp->online == 1) ? 'آنلاین' : 'حضوری';
                    }
                    else {
                        $std->status = 1;
                    }
                }
            }
            echo json_encode($stds);
        }

    }

    public function submitRegistry() {

        if(isset($_POST["qId"]) && isset($_POST["stds"]) && isset($_POST["mode"])) {

            $qId = makeValidInput($_POST["qId"]);
            if(RegularQuiz::whereId($qId) == null)
                return;

            $mode = makeValidInput($_POST["mode"]);
            $mode = ($mode == "online") ? 1 : 0;
            $stds = $_POST["stds"];

            $level = Auth::user()->level;
            $uId = Auth::user()->id;

            $namayandeLevel = getValueInfo('namayandeLevel');
            $schoolLevel = getValueInfo('schoolLevel');

            foreach ($stds as $std) {

                $std = makeValidInput($std);

                if($level == $schoolLevel) {
                    $condition = ['sId' => $uId, 'uId' => $std];
                    if(SchoolStudent::where($condition)->count() == 0)
                        continue;
                }

                else if($level == $namayandeLevel){

                    $tmp = DB::select('select * from namayandeSchool nS, schoolStudent sS WHERE sS.uId = ' . $std . ' and sS.sId = nS.sId and nS.nId = ' . $uId);

                    if($tmp == null || count($tmp) == 0)
                        continue;
                }

                $condition = ['online' => $mode, 'qId' => $qId, 'studentId' => $std];
                if(RegularQuizQueue::where($condition)->count() > 0)
                    continue;

                $tmp = new RegularQuizQueue();
                $tmp->studentId = $std;
                $tmp->qId = $qId;
                $tmp->online = $mode;
                $tmp->save();
            }
            echo "ok";
        }

    }

    public function deleteFromQueue() {

        if(isset($_POST["qId"]) && isset($_POST["stds"])) {

            $qId = makeValidInput($_POST["qId"]);
            if(RegularQuiz::whereId($qId) == null)
                return;

            $level = Auth::user()->level;
            $uId = Auth::user()->id;

            $namayandeLevel = getValueInfo('namayandeLevel');
            $schoolLevel = getValueInfo('schoolLevel');

            $stds = $_POST["stds"];

            foreach ($stds as $std) {

                if($level == $schoolLevel) {
                    $condition = ['sId' => $uId, 'uId' => $std];
                    if(SchoolStudent::where($condition)->count() == 0)
                        continue;
                }

                else if($level == $namayandeLevel){

                    $tmp = DB::select('select * from namayandeSchool nS, schoolStudent sS WHERE sS.uId = ' . $std . ' and sS.sId = nS.sId and nS.nId = ' . $uId);

                    if($tmp == null || count($tmp) == 0)
                        continue;
                }

                $std = makeValidInput($std);
                $condition = ['qId' => $qId, 'studentId' => $std];
                RegularQuizQueue::where($condition)->delete();
            }
            echo "ok";
        }
    }

    public function getRegularQuizesOfStd() {
        if(isset($_POST["uId"])) {
            echo json_encode(DB::select('select rQ.name from regularQuiz rQ, quizRegistry WHERE quizMode = ' . getValueInfo('regularQuiz') . ' and qId = rQ.id and uId = ' . makeValidInput($_POST["uId"])));
        }
    }

    public function getQueuedQuizes() {
        if(isset($_POST["uId"])) {
            echo json_encode(DB::select('select rQ.name, regularQuizQueue.online from regularQuiz rQ, regularQuizQueue WHERE qId = rQ.id and studentId = ' . makeValidInput($_POST["uId"])));
        }
    }

    public function registerableList() {

        if(isset($_POST["uId"])) {

            $uId = makeValidInput($_POST["uId"]);
            $date = getToday()["date"];

            $quizes = RegularQuiz::whereNotExists(function($query) use ($uId) {
                $query->select(DB::raw(1))
                    ->from('quizRegistry')
                    ->whereRaw('(quizRegistry.quizMode = ' . getValueInfo('regularQuiz') . ' and regularQuiz.id = quizRegistry.qId and quizRegistry.uId = ' . $uId . ") or (select regularQuizQueue.id from regularQuizQueue where regularQuizQueue.qId = regularQuiz.id and regularQuizQueue.studentId = " . $uId . ")");
            })->whereRaw('startReg <= ' . $date . ' and endReg >= ' . $date)->get();

            foreach ($quizes as $quiz) {
                $quiz->startDate = convertStringToDate($quiz->startDate);
                $quiz->endDate = convertStringToDate($quiz->endDate);
                $quiz->startTime = convertStringToTime($quiz->startTime);
                $quiz->endTime = convertStringToTime($quiz->endTime);
                $quiz->startReg = convertStringToDate($quiz->startReg);
                $quiz->endReg = convertStringToDate($quiz->endReg);
            }

            echo json_encode($quizes);
        }

    }

    private function generateUserName() {

        $rand = rand(1, 10000);
        $username = "g" . $rand;

        while (User::whereUsername($username)->count() > 0) {
            $rand = rand(1, 10000);
            $username = "g_" . $rand;
        }

        return $username;
    }

    private function addUsers($users) {

        $currUser = Auth::user();
        $counter = 2;

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("Gachesefid");
        $objPHPExcel->getProperties()->setLastModifiedBy("Gachesefid");
        $objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.");

        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->getActiveSheet()->setCellValue('D1', 'رمز عبور');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'نام کاربری');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'نام خانوادگی');
        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'نام');
        include_once 'MoneyController.php';

        if($currUser->level == getValueInfo('namayandeLevel') || $currUser->level == getValueInfo('adminLevel') ||
            $currUser->level == getValueInfo('superAdminLevel')) {

            foreach ($users as $user) {

                if(count($user) != 5)
                    continue;

                $condition = ['level' => getValueInfo('schoolLevel'),
                    'invitationCode' => $user[3]];

                $school = User::where($condition)->first();
                if($school == null)
                    continue;

                $schoolCity = School::whereUId($school->id)->first()->cityId;

                if($currUser->level == getValueInfo('namayandeLevel')) {
                    $condition = ['nId' => $currUser->id, 'sId' => $school->id];
                    if (NamayandeSchool::where($condition)->count() == 0)
                        continue;
                }

                if(User::whereNID($user[4])->count() > 0 || !_custom_check_national_code($user[4]))
                    continue;

                switch ($user[2]) {
                    case 7:
                        $target = "هفتم";
                        break;
                    case 8:
                        $target = "هشتم";
                        break;
                    case 9:
                        $target = "نهم";
                        break;
                    case 10:
                        $target = 'دهم ریاضی';
                        break;
                    case 11:
                        $target = 'یازدهم ریاضی';
                        break;
                    default:
                        $target = "اشتباه";
                        break;
                }

                $gradeTmp = Grade::where('name', '=', $target)->first();
                if($gradeTmp == null)
                    continue;

                $tmp = new User();
                $tmp->firstName = $user[0];
                $tmp->lastName = $user[1];
                $tmp->level = getValueInfo("studentLevel");
                $pas = generateActivationCode();
                $username = $this->generateUserName();
                $tmp->invitationCode = $pas;
                $tmp->username = $username;
                $tmp->password = Hash::make($pas);
                $tmp->status = 1;
                $redundantInfo = new RedundantInfo1();

                try {
                    $tmp->save();

                    charge(PointConfig::first()->init, $tmp->id, getValueInfo('initTransaction'), getValueInfo('money2'));
                    $redundantInfo->NID = "";

                    $redundantInfo->gradeId = $gradeTmp->id;
                    $redundantInfo->cityId = $schoolCity;
                    $redundantInfo->email = "";
                    $redundantInfo->uId = $tmp->id;
                    $redundantInfo->save();

                    $namayande = new SchoolStudent();
                    $namayande->uId = $tmp->id;
                    $namayande->sId = $school->id;
                    $namayande->save();

                    $objPHPExcel->getActiveSheet()->setCellValue('A' . ($counter), $user[0]);
                    $objPHPExcel->getActiveSheet()->setCellValue('B' . ($counter), $user[1]);
                    $objPHPExcel->getActiveSheet()->setCellValue('C' . ($counter), $username);
                    $objPHPExcel->getActiveSheet()->setCellValue('D' . ($counter), $pas);
                    $counter++;
                }
                catch (Exception $x) {
                    $tmp->delete();
                    $redundantInfo->delete();
                }
            }
        }

        else {

            $uId = Auth::user()->id;
            $masterSex = Auth::user()->sex;

            $schoolCity = School::whereUId($uId)->first()->cityId;

            foreach ($users as $user) {

                if(count($user) != 4)
                    continue;

                if(User::whereNID($user[3])->count() > 0 || !_custom_check_national_code($user[3]))
                    continue;

                switch ($user[2]) {
                    case 7:
                        $target = "هفتم";
                        break;
                    case 8:
                        $target = "هشتم";
                        break;
                    case 9:
                        $target = "نهم";
                        break;
                    case 10:
                        $target = 'دهم ریاضی';
                        break;
                    case 11:
                        $target = 'یازدهم ریاضی';
                        break;
                    default:
                        $target = "اشتباه";
                        break;
                }

                $gradeTmp = Grade::where('name', '=', $target)->first();
                if($gradeTmp == null)
                    continue;

                $tmp = new User();
                $tmp->firstName = $user[0];
                $tmp->lastName = $user[1];
                $tmp->sex = $masterSex;
                $tmp->level = getValueInfo("studentLevel");
                $pas = generateActivationCode();
                $username = $this->generateUserName();
                $tmp->invitationCode = $pas;
                $tmp->username = $username;
                $tmp->password = Hash::make($pas);
                $tmp->status = 1;
                $redundantInfo = new RedundantInfo1();

                try {
                    $tmp->save();

                    charge(PointConfig::first()->init, $tmp->id, getValueInfo('initTransaction'), getValueInfo('money2'));
                    $redundantInfo->NID = "";
                    $redundantInfo->gradeId = $gradeTmp->id;
                    $redundantInfo->cityId = $schoolCity;
                    $redundantInfo->email = "";
                    $redundantInfo->uId = $tmp->id;
                    $redundantInfo->save();

                    $namayande = new SchoolStudent();
                    $namayande->uId = $tmp->id;
                    $namayande->sId = $uId;
                    $namayande->save();

                    $objPHPExcel->getActiveSheet()->setCellValue('A' . ($counter), $user[0]);
                    $objPHPExcel->getActiveSheet()->setCellValue('B' . ($counter), $user[1]);
                    $objPHPExcel->getActiveSheet()->setCellValue('C' . ($counter), $username);
                    $objPHPExcel->getActiveSheet()->setCellValue('D' . ($counter), $pas);
                    $counter++;
                }
                catch (Exception $x) {
                    $tmp->delete();
                    $redundantInfo->delete();
                }
            }
        }



        $fileName = __DIR__ . "/../../../public/registrations/report_" . $currUser->id . ".xlsx";

        $objPHPExcel->getActiveSheet()->setTitle('اطلاعات ثبت نام');

        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save($fileName);
    }

    public function doGroupRegistry() {

        $err = "";
        $level = Auth::user()->level;

        if(isset($_FILES["group"])) {

            $file = $_FILES["group"]["name"];

            if(!empty($file)) {

                $path = __DIR__ . '/../../../public/tmp/' . $file;

                $err = uploadCheck($path, "group", "اکسل ثبت نام گروهی", 20000000, "xlsx");

                if (empty($err)) {
                    upload($path, "group", "اکسل ثبت نام گروهی");
                    $excelReader = PHPExcel_IOFactory::createReaderForFile($path);
                    $excelObj = $excelReader->load($path);
                    $workSheet = $excelObj->getSheet(0);
                    $users = array();
                    $lastRow = $workSheet->getHighestRow();
                    $cols = $workSheet->getHighestColumn();


                    if($level == getValueInfo('namayandeLevel') || $level == getValueInfo('adminLevel') ||
                        $level == getValueInfo('superAdminLevel')) {
                        if (count($cols) < 'F') {
                            unlink($path);
                            $err = "تعداد ستون های فایل شما معتبر نمی باشد";
                        } else {
                            for ($row = 2; $row <= $lastRow; $row++) {

                                if($workSheet->getCell('B' . $row)->getValue() == "")
                                    break;

                                $users[$row - 2][0] = $workSheet->getCell('B' . $row)->getValue();
                                $users[$row - 2][1] = $workSheet->getCell('C' . $row)->getValue();
                                $users[$row - 2][2] = $workSheet->getCell('D' . $row)->getValue();
                                $users[$row - 2][3] = $workSheet->getCell('E' . $row)->getValue();
                                $users[$row - 2][4] = $workSheet->getCell('F' . $row)->getValue();
                            }
                            unlink($path);
                            $this->addUsers($users);
                            $err = "getFile";
                        }
                    }
                    else {
                        if (count($cols) < 'E') {
                            unlink($path);
                            $err = "تعداد ستون های فایل شما معتبر نمی باشد";
                        } else {
                            for ($row = 2; $row <= $lastRow; $row++) {

                                if($workSheet->getCell('B' . $row)->getValue() == "")
                                    break;

                                $users[$row - 2][0] = $workSheet->getCell('B' . $row)->getValue();
                                $users[$row - 2][1] = $workSheet->getCell('C' . $row)->getValue();
                                $users[$row - 2][2] = $workSheet->getCell('D' . $row)->getValue();
                                $users[$row - 2][3] = $workSheet->getCell('E' . $row)->getValue();
                            }
                            unlink($path);
                            $this->addUsers($users);
                            $err = "getFile";
                        }
                    }

                }
            }
        }

        if(empty($err))
            $err = "لطفا فایل اکسل مورد نیاز را آپلود نمایید";

        return $this->groupRegistration($err);
        
    }
}