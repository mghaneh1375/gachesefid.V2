<?php

namespace App\Http\Controllers;

use App\models\NamayandeSchool;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\models\RegularQuiz;
use App\models\ReportsAccess;
use App\models\RedundantInfo1;
use App\models\StudentAdviser;
use App\models\SchoolStudent;
use App\models\School;
use App\models\Lesson;
use App\models\Grade;
use App\models\State;
use App\models\City;
use App\models\QuizStatus;
use App\models\Taraz;
use App\models\Transaction;
use App\models\User;
use App\models\ROQ;
use App\models\QuizRegistry;
use App\models\SystemQuiz;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use PHPExcel;
use PHPExcel_Cell_DataType;
use PHPExcel_Writer_Excel2007;

class ReportController extends Controller {

    public function namayandeSchool() {

        $uId = Auth::user()->id;
        $schools = DB::select('select (SELECT count(*) FROM schoolStudent sS WHERE sS.sId = nS.sId) as students, firstName, lastName, username, phoneNum, sex, invitationCode, users.id, introducer, school.name as schoolName, school.level as schoolLevel, school.kind as schoolKind, school.cityId from namayandeSchool nS, users, school WHERE school.uId = nS.sId and nId = ' . $uId . ' and nS.sId = users.id');

        foreach ($schools as $school) {

            $school->schoolCity = City::whereId($school->cityId)->name;

            $school->schoolKindId = $school->schoolKind;

            switch ($school->schoolKind) {
                case getValueInfo('sampadSch'):
                default:
                    $school->schoolKind = 'سمپاد';
                    break;
                case getValueInfo('gheyrSch'):
                    $school->schoolKind = 'غیرانتفاعی';
                    break;
                case getValueInfo('nemoneSch'):
                    $school->schoolKind = 'نمونه دولتی';
                    break;
                case getValueInfo('shahedSch'):
                    $school->schoolKind = 'شاهد';
                    break;
                case getValueInfo('dolatiSch'):
                    $school->schoolKind = 'دولتی';
                    break;
                case getValueInfo('sayerSch'):
                    $school->schoolKind = 'سایر';
                    break;
                case getValueInfo('HeyatSch'):
                    $school->schoolKind = 'هیئت امنایی';
                    break;
            }

            $school->schoolLevelId = $school->schoolLevel;

            $school->schoolLevel = ($school->schoolLevel == getValueInfo('motevaseteAval')) ? 'متوسطه اول' :
                ($school->schoolLevel == getValueInfo('motevaseteDovom')) ? 'متوسطه دوم' : 'دبستان';
        }

        return view('Reports.namayandeSchool', array('schools' => $schools, 'states' => State::all()));
    }

    public function advisersList() {

        $advisers = DB::select('select u.id, u.firstName, u.lastName, u.invitationCode, (select avg(aR.rate) from adviserRate aR WHERE aR.adviserId = u.id group by(aR.adviserId)) as rate, '.
            '(select count(*) from studentsAdviser sA WHERE sA.status = 1 and sA.adviserId = u.id) as studentsNo from ' .
            'users u where u.status = 1 and u.level = ' . getValueInfo('adviserLevel') .
            ' order by rate DESC');

        foreach ($advisers as $adviser) {
            if(empty($adviser->rate))
                $adviser->rate = 'بدون امتیاز';
        }

        $myAdviser = null;

        if(Auth::check())
            $myAdviser = StudentAdviser::whereStudentId(Auth::user()->id)->first();

        return view('Reports.advisers', array('advisers' => $advisers, 'myAdviser' => $myAdviser));

    }

    public function studentsRanking($page = 1) {

        $users = DB::select('select users.id, firstName, lastName, sum(q.level) * 5 as totalSum from users, roq, question q'.
            ' where users.id = uId and q.id = questionId and q.ans = result and users.level = ' . getValueInfo('studentLevel') .
            ' group by(uId) order by totalSum DESC limit ' . (($page - 1) * 10) . ', 10');

        $k = 0;

        if(count($users) > 0) {
            $k = count(DB::select('select sum(q.level) * 5 as totalSum from users, roq, question q'.
                ' where users.id = uId and q.id = questionId and q.ans = result and users.level = ' . getValueInfo('studentLevel') .
                ' group by(uId) having totalSum > ' . $users[0]->totalSum));
        }

        foreach ($users as $user) {
            $user->cityName = getStdCityAndState($user->id)['city'];
            $user->schoolName = getStdSchoolName($user->id);
            $tmp = RedundantInfo1::whereUId($user->id)->first();
            if($tmp != null)
                $user->grade = Grade::whereId($tmp->gradeId)->name;
            else
                $user->grade = "نامشخص";
        }

        $myRank = -2;

        if(Auth::check()) {
            $uId = Auth::user()->id;

            $amount = DB::select('select sum(q.level) * 5 as totalSum from roq, question q'.
                ' where uId = ' . $uId . ' and q.id = questionId and q.ans = result');

            if($amount == null || count($amount) == 0 || $amount[0]->totalSum == 0)
                $myRank = -1;

            else
                $myRank = count(DB::select('select sum(q.level) * 5 as totalSum from users, roq, question q'.
                    ' where users.id = uId and q.id = questionId and q.ans = result and users.level = ' . getValueInfo('studentLevel') .
                    ' group by(uId) having totalSum > ' . $amount[0]->totalSum));

        }

        return view('Reports.studentRanking', array('users' => $users, 'myRank' => $myRank, 'page' => $page, 'k' => $k,
            'total' => DB::select('select count(*) as countNum from users, transaction where users.id = userId and kindMoney = ' . getValueInfo('money1') . ' and level = ' . getValueInfo('studentLevel'))[0]->countNum));

    }

    public function myActivities() {

        $transactions = Transaction::whereUserId(Auth::user()->id)->orderBy('date', 'DESC')->get();
        $redundant1Transaction = getValueInfo('redundant1Transaction');
        $redundant2Transaction = getValueInfo('redundant2Transaction');
        $initTransaction = getValueInfo('initTransaction');
        $invitationTransaction = getValueInfo('invitationTransaction');
        $chargeTransaction = getValueInfo('chargeTransaction');
        $quizRankTransaction = getValueInfo('quizRankTransaction');
        $systemQuizTransaction = getValueInfo('systemQuizTransaction');
        $regularQuizTransaction = getValueInfo('regularQuizTransaction');
        $questionBuyTransaction = getValueInfo('questionBuyTransaction');

        foreach ($transactions as $transaction) {
            $transaction->date = convertStringToDate($transaction->date);
            switch ($transaction->kindTransactionId) {
                case $redundant1Transaction:
                    $transaction->kindTransactionId = "تکمیل اطلاعات اختیاری فاز 1";
                    break;
                case $redundant2Transaction:
                    $transaction->kindTransactionId = "تکمیل اطلاعات اختیاری فاز 2";
                    break;
                case $initTransaction:
                    $transaction->kindTransactionId = "اعتبار اولیه";
                    break;
                case $invitationTransaction:
                    $transaction->kindTransactionId = "دعوت از دوستان";
                    break;
                case $chargeTransaction:
                    $transaction->kindTransactionId = "شارژ حساب";
                    break;
                case $quizRankTransaction:
                    $transaction->kindTransactionId = "رتبه برتر در آزمون";
                    break;
                case $systemQuizTransaction:
                    $transaction->kindTransactionId = "ثبت نام در آزمون پای تخته";
                    break;
                case $regularQuizTransaction:
                    $transaction->kindTransactionId = "ثبت نام در آزمون پشت میز";
                    break;
                case $questionBuyTransaction:
                    $transaction->kindTransactionId = "ساخت آزمون دست ساز";
                    break;
            }
        }

        return view('Reports.myActivities', ['transactions' => $transactions]);

    }
    
    public function namayandeStudent() {

        $user = Auth::user();

        $backURL = route('namayandeStudent');
        $students = DB::select('select users.id, firstName, lastName, username, phoneNum, sex, invitationCode from namayandeSchool nS, schoolStudent sS, users WHERE nS.nId = ' . $user->id . ' and nS.sId = sS.sId and sS.uId = users.id');
        return view('Reports.schoolStudent', array('students' => $students, 'backURL' => $backURL));

    }
    
    public function schoolStudent($sId) {

        if(User::whereId($sId) == null)
            return Redirect::to('profile');

        $user = Auth::user();

        if($user->level == getValueInfo('schoolLevel') && $user->id != $sId)
            return Redirect::to('profile');

        $condition = ['nId' => $user->id, 'sId' => $sId];
        if($user->level == getValueInfo('namayandeLevel') && NamayandeSchool::where($condition)->count() == 0)
            return Redirect::to('profile');

        $backURL = route('schoolStudent', ['sId' => $sId]);
        $students = DB::select('select users.id, firstName, lastName, username, phoneNum, sex, invitationCode from schoolStudent sS, users WHERE sId = ' . $sId . ' and sS.uId = users.id');
        return view('Reports.schoolStudent', array('students' => $students, 'sId' => $sId, 'backURL' => $backURL));
    }

    public function gradeReport() {

        $grades = Grade::all();

        foreach ($grades as $grade) {
            $tmp = DB::select('select count(*) as countNum from SOQ, subject, lesson WHERE lessonId = lesson.id and SOQ.sId = subject.id and gradeId = ' . $grade->id);
            if($tmp == null || count($tmp) == 0 || empty($tmp[0]->countNum))
                $grade->qNo = 0;
            else
                $grade->qNo = $tmp[0]->countNum;

            $grade->studentNo = RedundantInfo1::where('gradeId', '=', $grade->id)->count();
        }

        return view('Reports.gradeReport', array('grades' => $grades));

    }

    public function gradeReportExcel() {

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("Gachesefid");
        $objPHPExcel->getProperties()->setLastModifiedBy("Gachesefid");
        $objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.");

        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'تعداد دانش آموزان');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'تعداد سوالات');
        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'نام پایه');

        $counter = 2;

        $grades = Grade::all();

        foreach ($grades as $grade) {
            $tmp = DB::select('select count(*) as countNum from SOQ, subject, lesson WHERE lessonId = lesson.id and SOQ.sId = subject.id and gradeId = ' . $grade->id);
            if($tmp == null || count($tmp) == 0 || empty($tmp[0]->countNum))
                $grade->qNo = 0;
            else
                $grade->qNo = $tmp[0]->countNum;

            $grade->studentNo = RedundantInfo1::where('gradeId', '=', $grade->id)->count();

            $objPHPExcel->getActiveSheet()->setCellValue('C' . ($counter), $grade->studentNo);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . ($counter), $grade->qNo);
            $objPHPExcel->getActiveSheet()->setCellValue('A' . ($counter), $grade->name);

            $counter++;
        }

        $fileName = __DIR__ . "/../../../public/registrations/subjectReport.xlsx";

        $objPHPExcel->getActiveSheet()->setTitle('گزارش گیری پایه تحصیلی');

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

        return Redirect::to('gradeReport');
    }

    public function quizReport() {

        $regularQuizes = RegularQuiz::all();
        $regularQuizMode = getValueInfo('regularQuiz');
        $systemQuizMode = getValueInfo('systemQuiz');

        foreach ($regularQuizes as $itr) {

            $itr->startDate = convertStringToDate($itr->startDate);
            $itr->endDate = convertStringToDate($itr->endDate);
            $itr->startTime = convertStringToTime($itr->startTime);
            $itr->endTime = convertStringToTime($itr->endTime);

            $condition = ['qId' => $itr->id, 'quizMode' => $regularQuizMode, 'online' => 1];
            $itr->onlineRegistered = QuizRegistry::where($condition)->count();
            $condition = ['qId' => $itr->id, 'quizMode' => $regularQuizMode, 'online' => 0];
            $itr->nonOnlineRegistered = QuizRegistry::where($condition)->count();
        }

        $systemQuizes = SystemQuiz::all();
        foreach ($systemQuizes as $itr) {
            $itr->startDate = convertStringToDate($itr->startDate);
            $itr->startTime = convertStringToTime($itr->startTime);
            $condition = ['qId' => $itr->id, 'quizMode' => $systemQuizMode];
            $itr->registered = QuizRegistry::where($condition)->count();
        }

        return view('Reports.quizReport', array('regularQuizes' => $regularQuizes,
            'systemQuizes' => $systemQuizes));

    }

    public function moneyReport() {

        $transactions = Transaction::where('amount', '<', 0)->orderBy('date', 'DESC')->paginate(300);

        foreach ($transactions as $transaction) {
            switch ($transaction->kindTransactionId) {
                case getValueInfo('regularQuizTransaction'):
                    $transaction->kindTransaction = "ثبت نام انفرادی در سنجش پشت میز";
                    break;
                case getValueInfo('regularQuizGroupTransaction'):
                    $transaction->kindTransaction = "ثبت نام گروهی در سنجش پشت میز";
                    break;
                case getValueInfo('systemQuizTransaction'):
                    $transaction->kindTransaction = "ثبت نام انفرادی در سنجش پای تخته";
                    break;
                case getValueInfo('chargeTransaction'):
                    $transaction->kindTransaction = "شارژ حساب";
                    break;
                default:
                    $transaction->kindTransaction = "نامشخص";
            }
            $user = User::whereId($transaction->userId);
            $transaction->userId = $user->firstName . " " . $user->lastName;
            $transaction->date = convertStringToDate($transaction->date);
        }

        return view('Reports.moneyReport', array('transactions' => $transactions));
    }

    public function quizReportExcel() {

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("Gachesefid");
        $objPHPExcel->getProperties()->setLastModifiedBy("Gachesefid");
        $objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.");

        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->getActiveSheet()->setCellValue('F1', 'ساعت برگزاری');
        $objPHPExcel->getActiveSheet()->setCellValue('E1', 'تاریخ برگزاری');
        $objPHPExcel->getActiveSheet()->setCellValue('D1', 'تعداد ثبت نام');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'نوع آزمون');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'آی دی آزمون');
        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'نام آزمون');

        $counter = 2;

        $regularQuizes = RegularQuiz::all();
        $regularQuizMode = getValueInfo('regularQuiz');
        $systemQuizMode = getValueInfo('systemQuiz');

        foreach ($regularQuizes as $itr) {

            $itr->startDate = convertStringToDate($itr->startDate);
            $itr->endDate = convertStringToDate($itr->endDate);
            $itr->startTime = convertStringToTime($itr->startTime);
            $itr->endTime = convertStringToTime($itr->endTime);

            $condition = ['qId' => $itr->id, 'quizMode' => $regularQuizMode, 'online' => 1];
            $itr->onlineRegistered = QuizRegistry::where($condition)->count();
            $condition = ['qId' => $itr->id, 'quizMode' => $regularQuizMode, 'online' => 0];
            $itr->nonOnlineRegistered = QuizRegistry::where($condition)->count();

            $objPHPExcel->getActiveSheet()->setCellValue('F' . ($counter), 'شروع : ' . $itr->startTime . ' - اتمام : ' . $itr->endTime);
            $objPHPExcel->getActiveSheet()->setCellValue('E' . ($counter), 'شروع : ' . $itr->startDate . ' - اتمام : ' . $itr->endDate);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . ($counter), 'حضوری: ' . $itr->onlineRegistered . ' - غیر حضوری : ' . $itr->nonOnlineRegistered);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . ($counter), 'پشت میز');
            $objPHPExcel->getActiveSheet()->setCellValue('B' . ($counter), $itr->id);
            $objPHPExcel->getActiveSheet()->setCellValue('A' . ($counter), $itr->name);

            $counter++;
        }

        $systemQuizes = RegularQuiz::all();
        foreach ($systemQuizes as $itr) {
            $itr->startDate = convertStringToDate($itr->startDate);
            $itr->startTime = convertStringToTime($itr->startTime);
            $condition = ['qId' => $itr->id, 'quizMode' => $systemQuizMode];
            $itr->registered = QuizRegistry::where($condition)->count();

            $objPHPExcel->getActiveSheet()->setCellValue('F' . ($counter), 'شروع : ' . $itr->startTime);
            $objPHPExcel->getActiveSheet()->setCellValue('E' . ($counter), 'شروع : ' . $itr->startDate);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . ($counter), $itr->registered);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . ($counter), 'پای تخته');
            $objPHPExcel->getActiveSheet()->setCellValue('B' . ($counter), $itr->id);
            $objPHPExcel->getActiveSheet()->setCellValue('A' . ($counter), $itr->name);

            $counter++;
        }

        $fileName = __DIR__ . "/../../../public/registrations/subjectReport.xlsx";

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

        return Redirect::to('quizReport');
    }

    public function studentReport($mode = "", $key = "", $page = 1) {

        $start = ($page - 1) * 20;
        $name = $username = "";

        if($mode == "name") {
            $name = makeValidInput($key);
            $users = DB::select("select * from users where concat(firstName, ' ', lastName) LIKE '%$name%' limit $start, 20");
            $total = count(DB::select("select * from users where concat(firstName, ' ', lastName) LIKE '%$name%' "));
        }
        else if($mode == "username") {
            $username = makeValidInput($key);
            $users = DB::select("select * from users where username LIKE '%$username%' limit $start, 20");
            $total = count(DB::select("select * from users where username LIKE '%$username%'"));
        }

        else {
            $users = User::whereLevel(getValueInfo('studentLevel'))->skip($start)->take(20)->get();
            $total = User::whereLevel(getValueInfo('studentLevel'))->count();
        }

        foreach ($users as $user) {

            $tmp = RedundantInfo1::whereUId($user->id)->first();
            if($tmp == null)
                $user->grade = "تعریف نشده";
            else
                $user->grade = Grade::whereId($tmp->gradeId)->name;

            $tmp = StudentAdviser::whereStudentId($user->id)->first();
            if($tmp == null)
                $user->adviser = "تعریف نشده";
            else {
                $tmp = User::whereId($tmp->adviserId);
                $user->adviser = $tmp->firstName . " " . $tmp->lastName;
            }

            $user->city = getStdCityAndState($user->id)["city"];
            $user->school = getStdSchoolName($user->id);
        }

        return view('Reports.studentReport', array('users' => $users, 'username' => $username,
            'total' => $total, 'page' => $page, 'name' => $name));
    }

    public function doEditUser() {
        
        if(isset($_POST["firstName"]) && isset($_POST["lastName"]) && isset($_POST["phone"]) &&
            isset($_POST["username"]) && isset($_POST["uId"])
        ) {

            $user = User::whereId(makeValidInput($_POST["uId"]));

            if($user == null) {
                echo "nok";
                return;
            }

            $user->firstName = makeValidInput($_POST["firstName"]);
            $user->lastName = makeValidInput($_POST["lastName"]);
            $user->phoneNum = makeValidInput($_POST["phone"]);

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
                echo "ok";
            }
            catch (Exception $x) {
                echo "nok1";
            }
            return;
        }
        echo "nok";
    }
    
    public function studentReportExcel() {

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("Gachesefid");
        $objPHPExcel->getProperties()->setLastModifiedBy("Gachesefid");
        $objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.");

        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->getActiveSheet()->setCellValue('H1', 'مدرسه');
        $objPHPExcel->getActiveSheet()->setCellValue('G1', 'شهر');
        $objPHPExcel->getActiveSheet()->setCellValue('F1', 'مشاور');
        $objPHPExcel->getActiveSheet()->setCellValue('E1', 'پایه تحصیلی');
        $objPHPExcel->getActiveSheet()->setCellValue('D1', 'شماره تماس');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'نام کاربری');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'نام خانوادگی');
        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'نام');

        $users = User::whereLevel(getValueInfo('studentLevel'))->get();
        $counter = 2;

        foreach ($users as $user) {

            $tmp = RedundantInfo1::whereUId($user->id)->first();
            if($tmp == null)
                $user->grade = "تعریف نشده";
            else
                $user->grade = Grade::whereId($tmp->gradeId)->name;

            $tmp = StudentAdviser::whereStudentId($user->id)->first();
            if($tmp == null)
                $user->adviser = "تعریف نشده";
            else {
                $tmp = User::whereId($tmp->adviserId);
                $user->adviser = $tmp->firstName . " " . $tmp->lastName;
            }

            $user->city = getStdCityAndState($user->id)["city"];
            $user->school = getStdSchoolName($user->id);

            $objPHPExcel->getActiveSheet()->setCellValue('A' . ($counter), $user->firstName);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . ($counter), $user->lastName);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . ($counter), $user->username);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . ($counter), $user->phoneNum);
            $objPHPExcel->getActiveSheet()->setCellValue('E' . ($counter), $user->grade);
            $objPHPExcel->getActiveSheet()->setCellValue('F' . ($counter), $user->adviser);
            $objPHPExcel->getActiveSheet()->setCellValue('G' . ($counter), $user->city);
            $objPHPExcel->getActiveSheet()->setCellValue('H' . ($counter), $user->school);

            $counter++;
        }

        $fileName = __DIR__ . "/../../../public/registrations/subjectReport.xlsx";

        $objPHPExcel->getActiveSheet()->setTitle('گزارش گیری بارکد از آزمون');

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

        return Redirect::to(route('studentReport'));
    }

    public function barcodeReport() {
        return view('chooseQuizForBarcodeReport', array('quizes' => RegularQuiz::all()));
    }

    public function getBarcodeReport($quizId) {

        $objPHPExcel = new PHPExcel();;
        $objPHPExcel->getProperties()->setCreator("Gachesefid");
        $objPHPExcel->getProperties()->setLastModifiedBy("Gachesefid");
        $objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.");

        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->getActiveSheet()->setCellValue('K1', 'استان');
        $objPHPExcel->getActiveSheet()->setCellValue('J1', 'شهر');
        $objPHPExcel->getActiveSheet()->setCellValue('I1', 'بارکد');
        $objPHPExcel->getActiveSheet()->setCellValue('H1', 'کد نمایندگی');
        $objPHPExcel->getActiveSheet()->setCellValue('G1', 'نام نمایندگی');
        $objPHPExcel->getActiveSheet()->setCellValue('F1', 'کد مدرسه');
        $objPHPExcel->getActiveSheet()->setCellValue('E1', 'نام مدرسه');
        $objPHPExcel->getActiveSheet()->setCellValue('D1', 'نام آزمون');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'پایه');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'نام خانوادگی');
        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'نام');


        $quizName = RegularQuiz::whereId($quizId)->name;

        $users = DB::select('select users.id, firstName, lastName, g.name as gradeId, ci.name as schoolCity, sa.name as stateName, s.name as schoolName, (SELECT u5.invitationCode from users u5 WHERE u5.id = sS.sId) as schoolCode, (SELECT concat(u3.firstName, " ", u3.lastName) from users u3 WHERE u3.id = nS.nId) as namayandeName, (SELECT u4.invitationCode from users u4 WHERE u4.id = nS.nId) as namayandeCode from schoolStudent sS, namayandeSchool nS, quizRegistry qR, users, redundantInfo1 rd, grade g, city ci, state sa, school s WHERE s.uId = sS.sId and ci.id = s.cityId and ci.stateId = sa.id and rd.gradeId = g.id and nS.sId = sS.sId and sS.uId = users.id and rd.uId = users.id and quizMode = ' . getValueInfo('regularQuiz') . ' and online = 0 and qR.qId = ' . $quizId . ' and users.id = qR.uId');
        $counter = 2;

        if($quizId < 100) {
            if($quizId < 10)
                $quizId = "00" . $quizId;
            else
                $quizId = "0" . $quizId;
        }

        foreach ($users as $user) {

            if($user->id < 10) {
                $user->id = "00000" . $user->id;
            }
            else if($user->id < 100) {
                $user->id = "0000" . $user->id;
            }
            else if($user->id < 1000) {
                $user->id = "000" . $user->id;
            }
            else if($user->id < 10000) {
                $user->id = "00" . $user->id;
            }
            else if($user->id < 100000) {
                $user->id = "0" . $user->id;
            }

            $objPHPExcel->getActiveSheet()->setCellValueExplicit('K' . ($counter), $user->stateName);
            $objPHPExcel->getActiveSheet()->setCellValueExplicit('J' . ($counter), $user->schoolCity);
            $objPHPExcel->getActiveSheet()->setCellValueExplicit('I' . ($counter), $quizId . $user->id, PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->getActiveSheet()->setCellValueExplicit('H' . ($counter), $user->namayandeCode);
            $objPHPExcel->getActiveSheet()->setCellValueExplicit('G' . ($counter), $user->namayandeName);
            $objPHPExcel->getActiveSheet()->setCellValueExplicit('F' . ($counter), $user->schoolCode);
            $objPHPExcel->getActiveSheet()->setCellValueExplicit('E' . ($counter), $user->schoolName);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . ($counter), $quizName);
            $objPHPExcel->getActiveSheet()->setCellValueExplicit('C' . ($counter), $user->gradeId, PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->getActiveSheet()->setCellValueExplicit('B' . ($counter), $user->lastName, PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->getActiveSheet()->setCellValueExplicit('A' . ($counter), $user->firstName, PHPExcel_Cell_DataType::TYPE_STRING);

            $counter++;
        }

        $fileName = __DIR__ . "/../../../public/registrations/subjectReport.xlsx";

        $objPHPExcel->getActiveSheet()->setTitle('گزارش گیری بارکد از آزمون');

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

        return Redirect::to(route('barcodeReport'));

    }

    public function subjectReport() {

        $subjects = DB::select('select subject.id, subject.name, lesson.name as lessonName, grade.name as gradeName from subject, lesson, grade WHERE grade.id = lesson.gradeId and lessonId = lesson.id order by subject.id ASC');

        return view('subjectReport', array('subjects' => $subjects));
    }

    public function subjectReportExcel() {

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("Gachesefid");
        $objPHPExcel->getProperties()->setLastModifiedBy("Gachesefid");
        $objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.");

        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->getActiveSheet()->setCellValue('D1', 'نام مقطع تحصیلی');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'نام درس');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'نام مبحث');
        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'آی دی مبحث');

        $subjects = DB::select('select subject.id, subject.name, lesson.name as lessonName, grade.name as gradeName from subject, lesson, grade WHERE grade.id = lesson.gradeId and lessonId = lesson.id order by subject.id ASC');
        $counter = 2;

        foreach ($subjects as $subject) {

            $objPHPExcel->getActiveSheet()->setCellValue('D' . ($counter), $subject->gradeName);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . ($counter), $subject->lessonName);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . ($counter), $subject->name);
            $objPHPExcel->getActiveSheet()->setCellValue('A' . ($counter), $subject->id);

            $counter++;
        }

        $fileName = __DIR__ . "/../../../public/registrations/subjectReport.xlsx";

        $objPHPExcel->getActiveSheet()->setTitle('گزارش گیری مباحث');

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

        return Redirect::to(route('subjectReport'));
    }

    public function reportsAccess() {
        return view('reportsAccess', array('reportsAccess' => ReportsAccess::orderBy('reportNo', 'ASC')->get()));
    }

    public function changeReportStatus() {

        if(isset($_POST["id"])) {

            $reportAccess = ReportsAccess::find(makeValidInput($_POST["id"]));

            if($reportAccess == null)
                return;

            $reportAccess->status = !$reportAccess->status;
            $reportAccess->save();

        }
    }

    public function A5($quizId, $msg = "") {

        $user = Auth::user();
        $regularQuizMode = getValueInfo('regularQuiz');

        if($user->level == getValueInfo('adviserLevel')) {
            $users = DB::select('SELECT qR.id, qR.uId, sum(taraz.taraz * (SELECT lesson.coherence FROM lesson WHERE lesson.id = taraz.lId)) as weighted_avg from quizRegistry qR, studentsAdviser sA, taraz WHERE sA.adviserId = ' . $user->id . ' and sA.studentId = qR.uId and quizMode = ' . $regularQuizMode . ' and qR.id = taraz.qEntryId and qR.qId = ' . $quizId . ' and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0 GROUP by (qR.id) ORDER by weighted_avg DESC');
        }
        else if($user->level == getValueInfo('namayandeLevel')) {
            $users = DB::select('SELECT qR.id, qR.uId, sum(taraz.taraz * (SELECT lesson.coherence FROM lesson WHERE lesson.id = taraz.lId)) as weighted_avg from namayandeSchool nS, quizRegistry qR, schoolStudent sS, taraz WHERE nS.sId = sS.sId and nS.nId = ' . $user->id . ' and sS.uId = qR.uId and quizMode = ' . $regularQuizMode . ' and qR.id = taraz.qEntryId and qR.qId = ' . $quizId . ' and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0 GROUP by (qR.id) ORDER by weighted_avg DESC');
        }
        else if($user->level == getValueInfo('schoolLevel')) {
            $users = DB::select('SELECT qR.id, qR.uId, sum(taraz.taraz * (SELECT lesson.coherence FROM lesson WHERE lesson.id = taraz.lId)) as weighted_avg from quizRegistry qR, schoolStudent sS, taraz WHERE sS.sId = ' . $user->id . ' and sS.uId = qR.uId and quizMode = ' . $regularQuizMode . ' and qR.id = taraz.qEntryId and qR.qId = ' . $quizId . ' and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0 GROUP by (qR.id) ORDER by weighted_avg DESC');
        }
        else {
            $users = DB::select('SELECT qR.id, qR.uId, sum(taraz.taraz * (SELECT lesson.coherence FROM lesson WHERE lesson.id = taraz.lId)) as weighted_avg from quizRegistry qR, taraz WHERE quizMode = ' . $regularQuizMode . ' and qR.id = taraz.qEntryId and qR.qId = ' . $quizId . ' and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0 GROUP by (qR.id) ORDER by weighted_avg DESC');

        }

        $tmp = DB::select('SELECT DISTINCT L.id, L.name, L.coherence from lesson L, SOQ SO, subject S, regularQOQ QO WHERE QO.quizId = ' . $quizId . ' and QO.questionId = SO.qId and SO.sId = S.id and S.lessonId = L.id order by L.id ASC');
        $sum = 0;

        if($tmp == null || count($tmp) == 0)
            $sum = 1;

        else {
            foreach ($tmp as $itr) {
                $sum += $itr->coherence;
            }
        }

        for($i = 0; $i < count($users); $i++)
            $users[$i]->rank = ($i + 1);


        $preTaraz = (count($users) > 0) ? round($users[0]->weighted_avg / $sum, 0) : 0;

        for ($i = 1; $i < count($users); $i++) {

            if ($preTaraz == round($users[$i]->weighted_avg / $sum, 0))
                $users[$i]->rank = $users[$i - 1]->rank;
            else
                $preTaraz = $users[$i - 1]->rank;
        }

        foreach ($users as $user) {

            $tmp = DB::select('select lesson.name, lesson.coherence, taraz.percent, taraz.taraz from taraz, lesson WHERE taraz.qEntryId = ' . $user->id .
                ' and lesson.id = taraz.lId');

            $user->lessons = $tmp;

            $target = User::whereId($user->uId);
            $user->name = $target->firstName . " " . $target->lastName;
            $user->uId = $target->id;

            $user->schoolName = "نامشخص";
            $schTmp = SchoolStudent::whereUId($target->id)->first();
            if($schTmp != null) {
                $schTmp = School::whereUId($schTmp->sId)->first();
                if($schTmp != null)
                    $user->schoolName = $schTmp->name;
            }

            $cityAndState = getStdCityAndState($target->id);
            $user->city = $cityAndState['city'];

            $user->state = $cityAndState['state'];

            $user->cityRank = calcRankInCity($quizId, $user->uId, $cityAndState['cityId']);
            $user->stateRank = calcRankInState($quizId, $user->uId, $cityAndState['stateId']);

        }

        usort($users, function ($a, $b) {
            return $a->rank - $b->rank;
        });

        return view('reportA5', array('users' => $users, 'quizId' => $quizId, 'msg' => $msg));

    }

    public function A5Excel($quizId) {

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("Gachesefid");
        $objPHPExcel->getProperties()->setLastModifiedBy("Gachesefid");
        $objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.");

        $objPHPExcel->setActiveSheetIndex(0);

        $user = Auth::user();
        $regularQuizMode = getValueInfo('regularQuiz');

        if($user->level == getValueInfo('adviserLevel')) {
            $users = DB::select('SELECT qR.id, qR.uId, sum(taraz.taraz * (SELECT lesson.coherence FROM lesson WHERE lesson.id = taraz.lId)) as weighted_avg from quizRegistry qR, studentsAdviser sA, taraz WHERE sA.adviserId = ' . $user->id . ' and sA.studentId = qR.uId and quizMode = ' . $regularQuizMode . ' and qR.id = taraz.qEntryId and qR.qId = ' . $quizId . ' and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0 GROUP by (qR.id) ORDER by weighted_avg DESC');
        }
        else if($user->level == getValueInfo('namayandeLevel')) {
            $users = DB::select('SELECT qR.id, qR.uId, sum(taraz.taraz * (SELECT lesson.coherence FROM lesson WHERE lesson.id = taraz.lId)) as weighted_avg from namayandeSchool nS, quizRegistry qR, schoolStudent sS, taraz WHERE nS.sId = sS.sId and nS.nId = ' . $user->id . ' and sS.uId = qR.uId and quizMode = ' . $regularQuizMode . ' and qR.id = taraz.qEntryId and qR.qId = ' . $quizId . ' and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0 GROUP by (qR.id) ORDER by weighted_avg DESC');
        }
        else if($user->level == getValueInfo('schoolLevel')) {
            $users = DB::select('SELECT qR.id, qR.uId, sum(taraz.taraz * (SELECT lesson.coherence FROM lesson WHERE lesson.id = taraz.lId)) as weighted_avg from quizRegistry qR, schoolStudent sS, taraz WHERE sS.sId = ' . $user->id . ' and sS.uId = qR.uId and quizMode = ' . $regularQuizMode . ' and qR.id = taraz.qEntryId and qR.qId = ' . $quizId . ' and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0 GROUP by (qR.id) ORDER by weighted_avg DESC');
        }
        else {
            $users = DB::select('SELECT qR.id, qR.uId, sum(taraz.taraz * (SELECT lesson.coherence FROM lesson WHERE lesson.id = taraz.lId)) as weighted_avg from quizRegistry qR, taraz WHERE quizMode = ' . $regularQuizMode . ' and qR.id = taraz.qEntryId and qR.qId = ' . $quizId . ' and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0 GROUP by (qR.id) ORDER by weighted_avg DESC');

        }

        $tmp = DB::select('SELECT DISTINCT L.id, L.name, L.coherence from lesson L, SOQ SO, subject S, regularQOQ QO WHERE QO.quizId = ' . $quizId . ' and QO.questionId = SO.qId and SO.sId = S.id and S.lessonId = L.id order by L.id ASC');
        $sum = 0;

        if($tmp == null || count($tmp) == 0)
            $sum = 1;

        else {
            foreach ($tmp as $itr) {
                $sum += $itr->coherence;
            }
        }

        for($i = 0; $i < count($users); $i++)
            $users[$i]->rank = ($i + 1);


        $preTaraz = (count($users) > 0) ? round($users[0]->weighted_avg / $sum, 0) : 0;

        for ($i = 1; $i < count($users); $i++) {

            if ($preTaraz == round($users[$i]->weighted_avg / $sum, 0))
                $users[$i]->rank = $users[$i - 1]->rank;
            else
                $preTaraz = $users[$i - 1]->rank;
        }

        foreach ($users as $user) {

            $tmp = DB::select('select lesson.name, lesson.coherence, taraz.percent, taraz.taraz from taraz, lesson WHERE taraz.qEntryId = ' . $user->id .
                ' and lesson.id = taraz.lId');

            $user->lessons = $tmp;

            $target = User::whereId($user->uId);
            $user->name = $target->firstName . " " . $target->lastName;
            $user->uId = $target->id;

            $user->schoolName = "نامشخص";
            $schTmp = SchoolStudent::whereUId($target->id)->first();
            if($schTmp != null) {
                $schTmp = School::whereUId($schTmp->sId)->first();
                if($schTmp != null)
                    $user->schoolName = $schTmp->name;
            }

            $cityAndState = getStdCityAndState($target->id);
            $user->city = $cityAndState['city'];

            $user->state = $cityAndState['state'];

            $user->cityRank = calcRankInCity($quizId, $user->uId, $cityAndState['cityId']);
            $user->stateRank = calcRankInState($quizId, $user->uId, $cityAndState['stateId']);

        }

        usort($users, function ($a, $b) {
            return $a->rank - $b->rank;
        });

        $objPHPExcel->getActiveSheet()->setCellValue('D1', 'مدرسه');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'استان');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'شهر');
        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'نام و نام خانوادگی');

        $j = 'E';

        if(count($users) > 0) {
            foreach($users[0]->lessons as $itr) {
                $objPHPExcel->getActiveSheet()->setCellValue(($j++) . '1', $itr->name);

            }
        }


        $objPHPExcel->getActiveSheet()->setCellValue(($j++) . '1', 'میانگین');
        $objPHPExcel->getActiveSheet()->setCellValue(($j++) . '1', 'تراز کل');
        $objPHPExcel->getActiveSheet()->setCellValue(($j++) . '1', 'رتبه در شهر');
        $objPHPExcel->getActiveSheet()->setCellValue(($j++) . '1', 'رتبه در استان');
        $objPHPExcel->getActiveSheet()->setCellValue(($j) . '1', 'رتبه در کشور');

        $i = 2;

        foreach($users as $user) {

            $sumTaraz = 0;
            $sumLesson = 0;
            $sumCoherence = 0;

            $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $user->name);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $user->city);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $user->state);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $user->schoolName);

            $j = 'E';

            foreach($user->lessons as $itr) {
                if($itr->coherence == 0) {
                    $sumTaraz += $itr->taraz;
                    $sumLesson += $itr->percent;
                    $sumCoherence += 1;
                }
                else {
                    $sumTaraz += $itr->taraz * $itr->coherence;
                    $sumLesson += $itr->percent * $itr->coherence;
                    $sumCoherence += $itr->coherence;
                }
                $objPHPExcel->getActiveSheet()->setCellValue($j++ . $i, $itr->percent);
            }
            if($sumCoherence != 0) {
                $objPHPExcel->getActiveSheet()->setCellValue($j++ . $i, round(($sumLesson / $sumCoherence), 0));
                $objPHPExcel->getActiveSheet()->setCellValue($j++ . $i, round(($sumTaraz / $sumCoherence), 0));
            }
            else {
                $objPHPExcel->getActiveSheet()->setCellValue($j++ . $i, round(($sumLesson), 0));
                $objPHPExcel->getActiveSheet()->setCellValue($j++ . $i, round(($sumTaraz), 0));
            }
            $objPHPExcel->getActiveSheet()->setCellValue($j++ . $i, $user->cityRank);
            $objPHPExcel->getActiveSheet()->setCellValue($j++ . $i, $user->stateRank);
            $objPHPExcel->getActiveSheet()->setCellValue($j . $i, $user->rank);
            $i++;
        }

        $fileName = __DIR__ . "/../../../public/registrations/A5.xlsx";

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

        return view('reportA5', array('users' => $users, 'quizId' => $quizId));

    }

    public function A1($quizId) {

        $regularQuizMode = getValueInfo('regularQuiz');
        $user = Auth::user();

        $qInfos = DB::select("select question.id, question.ans " .
            "from question, regularQOQ WHERE regularQOQ.quizId = " . $quizId . " and " .
            "regularQOQ.questionId = question.id order by regularQOQ.id ASC");

        if(count($qInfos) == 0)
            return Redirect::to(route('getQuizReport', ['quizId' => $quizId]));

        if($user->level == getValueInfo('adviserLevel')) {

            $total = DB::select('select count(*) as countNum from ROQ, studentsAdviser WHERE quizId = ' . $quizId . " and quizMode = " . $regularQuizMode .
                " and studentsAdviser.adviserId = " . $user->id . " and studentsAdviser.studentId = ROQ.uId and ROQ.questionId = " . $qInfos[0]->id
            )[0]->countNum;

            foreach ($qInfos as $qInfo) {

                $qInfo->white = DB::select('select count(*) as countNum from ROQ, studentsAdviser WHERE quizId = ' . $quizId . " and quizMode = " . $regularQuizMode .
                    " and adviserId = " . $user->id . " and studentId = ROQ.uId and ROQ.result = 0 and ROQ.questionId = " . $qInfo->id
                )[0]->countNum;

                $qInfo->correct = DB::select('select count(*) as countNum from ROQ, studentsAdviser WHERE quizId = ' . $quizId . " and quizMode = " . $regularQuizMode .
                    " and studentsAdviser.adviserId = " . $user->id . " and studentsAdviser.studentId = ROQ.uId and ROQ.result = " . $qInfo->ans . " and ROQ.questionId = " . $qInfo->id
                )[0]->countNum;
            }
        }
        else if($user->level == getValueInfo('namayandeLevel')) {

            $total = DB::select('select count(*) as countNum from ROQ, namayandeSchool nS, schoolStudent sS WHERE nS.sId = sS.sId and quizId = ' . $quizId . " and quizMode = " . $regularQuizMode .
                " and nS.nId = " . $user->id . " and sS.uId = ROQ.uId and ROQ.questionId = " . $qInfos[0]->id
            )[0]->countNum;

            foreach ($qInfos as $qInfo) {

                $qInfo->white = DB::select('select count(*) as countNum from ROQ, namayandeSchool nS, schoolStudent sS WHERE nS.sId = sS.sId and quizId = ' . $quizId . " and quizMode = " . $regularQuizMode .
                    " and nS.nId = " . $user->id . " and sS.uId = ROQ.uId and ROQ.result = 0 and ROQ.questionId = " . $qInfo->id
                )[0]->countNum;

                $qInfo->correct = DB::select('select count(*) as countNum from ROQ, namayandeSchool nS, schoolStudent sS WHERE nS.sId = sS.sId and quizId = ' . $quizId . " and quizMode = " . $regularQuizMode .
                    " and nS.nId = " . $user->id . " and sS.uId = ROQ.uId and ROQ.result = " . $qInfo->ans . " and ROQ.questionId = " . $qInfo->id
                )[0]->countNum;
            }
        }
        else if($user->level == getValueInfo('schoolLevel')) {

            $total = DB::select('select count(*) as countNum from ROQ, schoolStudent sS WHERE quizId = ' . $quizId . " and quizMode = " . $regularQuizMode .
                " and sS.sId = " . $user->id . " and sS.uId = ROQ.uId and ROQ.questionId = " . $qInfos[0]->id
            )[0]->countNum;

            foreach ($qInfos as $qInfo) {

                $qInfo->white = DB::select('select count(*) as countNum from ROQ, schoolStudent sS WHERE quizId = ' . $quizId . " and quizMode = " . $regularQuizMode .
                    " and sS.sId = " . $user->id . " and sS.uId = ROQ.uId and ROQ.result = 0 and ROQ.questionId = " . $qInfo->id
                )[0]->countNum;

                $qInfo->correct = DB::select('select count(*) as countNum from ROQ, schoolStudent sS WHERE quizId = ' . $quizId . " and quizMode = " . $regularQuizMode .
                    " and sS.sId = " . $user->id . " and sS.uId = ROQ.uId and ROQ.result = " . $qInfo->ans . " and ROQ.questionId = " . $qInfo->id
                )[0]->countNum;
            }
        }
        else {
            $condition = ['questionId' => $qInfos[0]->id, 'quizId' => $quizId, 'quizMode' => $regularQuizMode];
            $total = ROQ::where($condition)->count();

            foreach ($qInfos as $qInfo) {
                $condition = ['questionId' => $qInfo->id, 'quizId' => $quizId, 'quizMode' => $regularQuizMode,
                    'result' => 0];
                $qInfo->white = ROQ::where($condition)->count();

                $condition = ['questionId' => $qInfo->id, 'quizId' => $quizId, 'quizMode' => $regularQuizMode,
                    'result' => $qInfo->ans];
                $qInfo->correct = ROQ::where($condition)->count();
            }
        }

        foreach ($qInfos as $qInfo) {
            $contents = DB::select('select subject.name as subjectName, lesson.name as lessonName from SOQ, subject, lesson WHERE SOQ.qId = ' . $qInfo->id . ' and SOQ.sId = subject.id and subject.lessonId = lesson.id');
            $subjects = [];
            $lessons = [];
            $i = 0;
            foreach ($contents as $content) {
                $subjects[$i] = $content->subjectName;
                if (!in_array($content->lessonName, $lessons))
                    $lessons[count($lessons)] = $content->lessonName;
                $i++;
            }
            $qInfo->subjects = $subjects;
            $qInfo->lessons = $lessons;
            $qInfo->level = getQuestionLevel($qInfo->id);
        }

        return view('A1', array('qInfos' => $qInfos, 'quizId' => $quizId, 'total' => $total));
    }

    public function A1Excel($quizId) {

        $regularQuizMode = getValueInfo('regularQuiz');
        $user = Auth::user();

        $qInfos = DB::select("select question.id, question.ans " .
            "from question, regularQOQ WHERE regularQOQ.quizId = " . $quizId . " and " .
            "regularQOQ.questionId = question.id order by regularQOQ.id ASC");

        if(count($qInfos) == 0)
            return Redirect::to(route('getQuizReport', ['quizId' => $quizId]));

        if($user->level == getValueInfo('adviserLevel')) {

            $total = DB::select('select count(*) as countNum from ROQ, studentsAdviser WHERE quizId = ' . $quizId . " and quizMode = " . $regularQuizMode .
                " and studentsAdviser.adviserId = " . $user->id . " and studentsAdviser.studentId = ROQ.uId and ROQ.questionId = " . $qInfos[0]->id
            )[0]->countNum;

            foreach ($qInfos as $qInfo) {

                $qInfo->white = DB::select('select count(*) as countNum from ROQ, studentsAdviser WHERE quizId = ' . $quizId . " and quizMode = " . $regularQuizMode .
                    " and adviserId = " . $user->id . " and studentId = ROQ.uId and ROQ.result = 0 and ROQ.questionId = " . $qInfo->id
                )[0]->countNum;

                $qInfo->correct = DB::select('select count(*) as countNum from ROQ, studentsAdviser WHERE quizId = ' . $quizId . " and quizMode = " . $regularQuizMode .
                    " and studentsAdviser.adviserId = " . $user->id . " and studentsAdviser.studentId = ROQ.uId and ROQ.result = " . $qInfo->ans . " and ROQ.questionId = " . $qInfo->id
                )[0]->countNum;
            }
        }
        else if($user->level == getValueInfo('namayandeLevel')) {

            $total = DB::select('select count(*) as countNum from ROQ, namayandeSchool nS, schoolStudent sS WHERE nS.sId = sS.sId and quizId = ' . $quizId . " and quizMode = " . $regularQuizMode .
                " and nS.nId = " . $user->id . " and sS.uId = ROQ.uId and ROQ.questionId = " . $qInfos[0]->id
            )[0]->countNum;

            foreach ($qInfos as $qInfo) {

                $qInfo->white = DB::select('select count(*) as countNum from ROQ, namayandeSchool nS, schoolStudent sS WHERE nS.sId = sS.sId and quizId = ' . $quizId . " and quizMode = " . $regularQuizMode .
                    " and nS.nId = " . $user->id . " and sS.uId = ROQ.uId and ROQ.result = 0 and ROQ.questionId = " . $qInfo->id
                )[0]->countNum;

                $qInfo->correct = DB::select('select count(*) as countNum from ROQ, namayandeSchool nS, schoolStudent sS WHERE nS.sId = sS.sId and quizId = ' . $quizId . " and quizMode = " . $regularQuizMode .
                    " and nS.nId = " . $user->id . " and sS.uId = ROQ.uId and ROQ.result = " . $qInfo->ans . " and ROQ.questionId = " . $qInfo->id
                )[0]->countNum;
            }
        }
        else if($user->level == getValueInfo('schoolLevel')) {

            $total = DB::select('select count(*) as countNum from ROQ, schoolStudent sS WHERE quizId = ' . $quizId . " and quizMode = " . $regularQuizMode .
                " and sS.sId = " . $user->id . " and sS.uId = ROQ.uId and ROQ.questionId = " . $qInfos[0]->id
            )[0]->countNum;

            foreach ($qInfos as $qInfo) {

                $qInfo->white = DB::select('select count(*) as countNum from ROQ, schoolStudent sS WHERE quizId = ' . $quizId . " and quizMode = " . $regularQuizMode .
                    " and sS.sId = " . $user->id . " and sS.uId = ROQ.uId and ROQ.result = 0 and ROQ.questionId = " . $qInfo->id
                )[0]->countNum;

                $qInfo->correct = DB::select('select count(*) as countNum from ROQ, schoolStudent sS WHERE quizId = ' . $quizId . " and quizMode = " . $regularQuizMode .
                    " and sS.sId = " . $user->id . " and sS.uId = ROQ.uId and ROQ.result = " . $qInfo->ans . " and ROQ.questionId = " . $qInfo->id
                )[0]->countNum;
            }
        }
        else {
            $condition = ['questionId' => $qInfos[0]->id, 'quizId' => $quizId, 'quizMode' => $regularQuizMode];
            $total = ROQ::where($condition)->count();

            foreach ($qInfos as $qInfo) {
                $condition = ['questionId' => $qInfo->id, 'quizId' => $quizId, 'quizMode' => $regularQuizMode,
                    'result' => 0];
                $qInfo->white = ROQ::where($condition)->count();

                $condition = ['questionId' => $qInfo->id, 'quizId' => $quizId, 'quizMode' => $regularQuizMode,
                    'result' => $qInfo->ans];
                $qInfo->correct = ROQ::where($condition)->count();
            }
        }

        foreach ($qInfos as $qInfo) {
            $contents = DB::select('select subject.name as subjectName, lesson.name as lessonName from SOQ, subject, lesson WHERE SOQ.qId = ' . $qInfo->id . ' and SOQ.sId = subject.id and subject.lessonId = lesson.id');
            $subjects = [];
            $lessons = [];
            $i = 0;
            foreach ($contents as $content) {
                $subjects[$i] = $content->subjectName;
                if (!in_array($content->lessonName, $lessons))
                    $lessons[count($lessons)] = $content->lessonName;
                $i++;
            }
            $qInfo->subjects = $subjects;
            $qInfo->lessons = $lessons;
            $qInfo->level = getQuestionLevel($qInfo->id);
        }

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("Gachesefid");
        $objPHPExcel->getProperties()->setLastModifiedBy("Gachesefid");
        $objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.");

        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->getActiveSheet()->setCellValue('H1', 'وضعیت دشواری');
        $objPHPExcel->getActiveSheet()->setCellValue('G1', 'درصد بدون پاسخ');
        $objPHPExcel->getActiveSheet()->setCellValue('F1', 'درصد پاسخ نادرست');
        $objPHPExcel->getActiveSheet()->setCellValue('E1', 'درصد پاسخ درست');
        $objPHPExcel->getActiveSheet()->setCellValue('D1', 'درس');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'مبحث');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'گزینه صحیح');
        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'شماره سوال');

        $i = 1;
        foreach($qInfos as $qInfo) {
            $objPHPExcel->getActiveSheet()->setCellValue('A' . ($i + 1), $i);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . ($i + 1), $qInfo->ans);
            $j = 'C';
            foreach($qInfo->subjects as $itr)
                $objPHPExcel->getActiveSheet()->setCellValue($j++ . ($i + 1), $itr);

            foreach($qInfo->lessons as $itr)
                $objPHPExcel->getActiveSheet()->setCellValue($j++ . ($i + 1), $itr);

            if($total != 0) {
                $objPHPExcel->getActiveSheet()->setCellValue($j++ . ($i + 1), round($qInfo->correct * 100 / $total, 0));
                $objPHPExcel->getActiveSheet()->setCellValue($j++ . ($i + 1), round((($total - $qInfo->correct - $qInfo->white) * 100 / $total), 0));
                $objPHPExcel->getActiveSheet()->setCellValue($j++ . ($i + 1), round(($qInfo->white * 100 / $total), 0));
            }
            else {
                $objPHPExcel->getActiveSheet()->setCellValue($j++ . ($i + 1), 0);
                $objPHPExcel->getActiveSheet()->setCellValue($j++ . ($i + 1), 0);
                $objPHPExcel->getActiveSheet()->setCellValue($j++ . ($i + 1), 0);
            }
            $objPHPExcel->getActiveSheet()->setCellValue($j . ($i + 1), $qInfo->level);
            $i++;
        }

        $fileName = __DIR__ . "/../../../public/registrations/A1.xlsx";

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

        return Redirect::to(route('A1', ['quizId' => $quizId]));
    }

    public function A7($quizId) {

        $floor = [-34, 10, 30, 50, 75];
        $ceil = [11, 31, 51, 76, 101];

        $regularQuizMode = getValueInfo('regularQuiz');
        $user = Auth::user();

        $lessons = getLessonQuiz($quizId);

        if($user->level == getValueInfo('adviserLevel')) {

            $total = DB::select('SELECT count(*) as total FROM quizRegistry qR, studentsAdviser WHERE qId = ' . $quizId . ' and quizMode = ' . $regularQuizMode .
                ' and adviserId = ' . $user->id . ' and studentId = qR.uId' .
                " and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0");

            if($total == null || count($total) == 0 || empty($total->total))
                $total = 0;
            else
                $total = $total[0]->total;

            foreach ($lessons as $lesson) {

                $tmp = DB::select('select count(*) as countNum from quizRegistry qR, taraz, studentsAdviser WHERE qId = ' . $quizId .
                    ' and adviserId = ' . $user->id . " and studentId = qR.uId and qEntryId = qR.id and " .
                    'percent < ' . $ceil[0] . ' and percent > ' . $floor[0] . ' and lId = ' . $lesson->id .
                    " and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0");

                if($tmp != null && count($tmp) > 0)
                    $lesson->group_0 = $tmp[0]->countNum;
                else
                    $lesson->group_0 = 0;

                $tmp = DB::select('select count(*) as countNum from quizRegistry qR, taraz, studentsAdviser WHERE qId = ' . $quizId .
                    ' and adviserId = ' . $user->id . " and studentId = qR.uId and qEntryId = qR.id and " .
                    'percent < ' . $ceil[1] . ' and percent > ' . $floor[1] . ' and lId = ' . $lesson->id .
                    " and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0");

                if($tmp != null && count($tmp) > 0)
                    $lesson->group_1 = $tmp[0]->countNum;
                else
                    $lesson->group_1 = 0;

                $tmp = DB::select('select count(*) as countNum from quizRegistry qR, taraz, studentsAdviser WHERE qId = ' . $quizId .
                    ' and adviserId = ' . $user->id . " and studentId = qR.uId and qEntryId = qR.id and " .
                    'percent < ' . $ceil[2] . ' and percent > ' . $floor[2] . ' and lId = ' . $lesson->id .
                    " and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0");

                if($tmp != null && count($tmp) > 0)
                    $lesson->group_2 = $tmp[0]->countNum;
                else
                    $lesson->group_2 = 0;

                $tmp = DB::select('select count(*) as countNum from quizRegistry qR, taraz, studentsAdviser WHERE qId = ' . $quizId .
                    ' and adviserId = ' . $user->id . " and studentId = qR.uId and qEntryId = qR.id and " .
                    'percent < ' . $ceil[3] . ' and percent > ' . $floor[3] . ' and lId = ' . $lesson->id .
                    " and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0");

                if($tmp != null && count($tmp) > 0)
                    $lesson->group_3 = $tmp[0]->countNum;
                else
                    $lesson->group_3 = 0;

                $tmp = DB::select('select count(*) as countNum from quizRegistry qR, taraz, studentsAdviser WHERE qId = ' . $quizId .
                    ' and adviserId = ' . $user->id . " and studentId = qR.uId and qEntryId = qR.id and " .
                    'percent < ' . $ceil[4] . ' and percent > ' . $floor[4] . ' and lId = ' . $lesson->id .
                    " and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0");

                if($tmp != null && count($tmp) > 0)
                    $lesson->group_4 = $tmp[0]->countNum;
                else
                    $lesson->group_4 = 0;
            }
        }

        else if($user->level == getValueInfo('namayandeLevel')) {

            $total = DB::select('SELECT count(*) as total FROM quizRegistry qR, namayandeSchool nS, schoolStudent sS WHERE nS.sId = sS.sId and qId = ' . $quizId . ' and quizMode = ' . $regularQuizMode .
                ' and nS.nId = ' . $user->id . ' and sS.uId = qR.uId' .
                " and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0"
            );

            if($total == null || count($total) == 0 || empty($total->total))
                $total = 0;
            else
                $total = $total[0]->total;

            foreach ($lessons as $lesson) {

                $tmp = DB::select('select count(*) as countNum from quizRegistry qR, taraz, namayandeSchool nS, schoolStudent sS WHERE nS.sId = sS.sId and qId = ' . $quizId .
                    ' and nS.nId = ' . $user->id . " and sS.uId = qR.uId and qEntryId = qR.id and " .
                    'percent < ' . $ceil[0] . ' and percent > ' . $floor[0] . ' and lId = ' . $lesson->id .
                    " and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0"
                );

                if($tmp != null && count($tmp) > 0)
                    $lesson->group_0 = $tmp[0]->countNum;
                else
                    $lesson->group_0 = 0;

                $tmp = DB::select('select count(*) as countNum from quizRegistry qR, taraz, namayandeSchool nS, schoolStudent sS WHERE nS.sId = sS.sId and qId = ' . $quizId .
                    ' and nS.nId = ' . $user->id . " and sS.uId = qR.uId and qEntryId = qR.id and " .
                    'percent < ' . $ceil[1] . ' and percent > ' . $floor[1] . ' and lId = ' . $lesson->id .
                    " and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0"
                );

                if($tmp != null && count($tmp) > 0)
                    $lesson->group_1 = $tmp[0]->countNum;
                else
                    $lesson->group_1 = 0;

                $tmp = DB::select('select count(*) as countNum from quizRegistry qR, taraz, namayandeSchool nS, schoolStudent sS WHERE nS.sId = sS.sId and qId = ' . $quizId .
                    ' and nS.nId = ' . $user->id . " and sS.uId = qR.uId and qEntryId = qR.id and " .
                    'percent < ' . $ceil[2] . ' and percent > ' . $floor[2] . ' and lId = ' . $lesson->id .
                    " and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0"
                );

                if($tmp != null && count($tmp) > 0)
                    $lesson->group_2 = $tmp[0]->countNum;
                else
                    $lesson->group_2 = 0;

                $tmp = DB::select('select count(*) as countNum from quizRegistry qR, taraz, namayandeSchool nS, schoolStudent sS WHERE nS.sId = sS.sId and qId = ' . $quizId .
                    ' and nS.nId = ' . $user->id . " and sS.uId = qR.uId and qEntryId = qR.id and " .
                    'percent < ' . $ceil[3] . ' and percent > ' . $floor[3] . ' and lId = ' . $lesson->id .
                    " and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0"
                );

                if($tmp != null && count($tmp) > 0)
                    $lesson->group_3 = $tmp[0]->countNum;
                else
                    $lesson->group_3 = 0;

                $tmp = DB::select('select count(*) as countNum from quizRegistry qR, taraz, namayandeSchool nS, schoolStudent sS WHERE nS.sId = sS.sId and qId = ' . $quizId .
                    ' and nS.nId = ' . $user->id . " and sS.uId = qR.uId and qEntryId = qR.id and " .
                    'percent < ' . $ceil[4] . ' and percent > ' . $floor[4] . ' and lId = ' . $lesson->id .
                    " and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0"
                );

                if($tmp != null && count($tmp) > 0)
                    $lesson->group_4 = $tmp[0]->countNum;
                else
                    $lesson->group_4 = 0;
            }
        }
        else if($user->level == getValueInfo('schoolLevel')) {

            $total = DB::select('SELECT count(*) as total FROM quizRegistry qR, schoolStudent sS WHERE qId = ' . $quizId . ' and quizMode = ' . $regularQuizMode .
                ' and sS.sId = ' . $user->id . ' and sS.uId = qR.uId' .
                " and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0"
            );

            if($total == null || count($total) == 0 || empty($total->total))
                $total = 0;
            else
                $total = $total[0]->total;

            foreach ($lessons as $lesson) {

                $tmp = DB::select('select count(*) as countNum from quizRegistry qR, taraz, schoolStudent sS WHERE qId = ' . $quizId .
                    ' and sS.sId = ' . $user->id . " and sS.uId = qR.uId and qEntryId = qR.id and " .
                    'percent < ' . $ceil[0] . ' and percent > ' . $floor[0] . ' and lId = ' . $lesson->id .
                    " and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0"
                );

                if($tmp != null && count($tmp) > 0)
                    $lesson->group_0 = $tmp[0]->countNum;
                else
                    $lesson->group_0 = 0;

                $tmp = DB::select('select count(*) as countNum from quizRegistry qR, taraz, schoolStudent sS WHERE qId = ' . $quizId .
                    ' and sS.sId = ' . $user->id . " and sS.uId = qR.uId and qEntryId = qR.id and " .
                    'percent < ' . $ceil[1] . ' and percent > ' . $floor[1] . ' and lId = ' . $lesson->id .
                    " and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0"
                );

                if($tmp != null && count($tmp) > 0)
                    $lesson->group_1 = $tmp[0]->countNum;
                else
                    $lesson->group_1 = 0;

                $tmp = DB::select('select count(*) as countNum from quizRegistry qR, taraz, schoolStudent sS WHERE qId = ' . $quizId .
                    ' and sS.sId = ' . $user->id . " and sS.uId = qR.uId and qEntryId = qR.id and " .
                    'percent < ' . $ceil[2] . ' and percent > ' . $floor[2] . ' and lId = ' . $lesson->id .
                    " and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0"
                );

                if($tmp != null && count($tmp) > 0)
                    $lesson->group_2 = $tmp[0]->countNum;
                else
                    $lesson->group_2 = 0;

                $tmp = DB::select('select count(*) as countNum from quizRegistry qR, taraz, schoolStudent sS WHERE qId = ' . $quizId .
                    ' and sS.sId = ' . $user->id . " and sS.uId = qR.uId and qEntryId = qR.id and " .
                    'percent < ' . $ceil[3] . ' and percent > ' . $floor[3] . ' and lId = ' . $lesson->id .
                    " and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0"
                );

                if($tmp != null && count($tmp) > 0)
                    $lesson->group_3 = $tmp[0]->countNum;
                else
                    $lesson->group_3 = 0;

                $tmp = DB::select('select count(*) as countNum from quizRegistry qR, taraz, schoolStudent sS WHERE qId = ' . $quizId .
                    ' and sS.sId = ' . $user->id . " and sS.uId = qR.uId and qEntryId = qR.id and " .
                    'percent < ' . $ceil[4] . ' and percent > ' . $floor[4] . ' and lId = ' . $lesson->id .
                    " and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0"
                );

                if($tmp != null && count($tmp) > 0)
                    $lesson->group_4 = $tmp[0]->countNum;
                else
                    $lesson->group_4 = 0;
            }
        }
        else {

            $total = DB::select('SELECT count(*) as total FROM quizRegistry qR WHERE qId = ' . $quizId . ' and quizMode = ' . $regularQuizMode .
                " and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0");

            if($total == null || count($total) == 0 || empty($total->total))
                $total = 0;
            else
                $total = $total[0]->total;

            foreach ($lessons as $lesson) {

                $tmp = DB::select('select count(*) as countNum from quizRegistry qR, taraz WHERE qId = ' . $quizId .
                    " and qEntryId = qR.id and " .
                    'percent < ' . $ceil[0] . ' and percent > ' . $floor[0] . ' and lId = ' . $lesson->id .
                    " and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0");

                if($tmp != null && count($tmp) > 0)
                    $lesson->group_0 = $tmp[0]->countNum;
                else
                    $lesson->group_0 = 0;

                $tmp = DB::select('select count(*) as countNum from quizRegistry qR, taraz WHERE qId = ' . $quizId .
                    " and qEntryId = qR.id and " .
                    'percent < ' . $ceil[1] . ' and percent > ' . $floor[1] . ' and lId = ' . $lesson->id .
                    " and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0");

                if($tmp != null && count($tmp) > 0)
                    $lesson->group_1 = $tmp[0]->countNum;
                else
                    $lesson->group_1 = 0;

                $tmp = DB::select('select count(*) as countNum from quizRegistry qR, taraz WHERE qId = ' . $quizId .
                    " and qEntryId = qR.id and " .
                    'percent < ' . $ceil[2] . ' and percent > ' . $floor[2] . ' and lId = ' . $lesson->id .
                    " and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0");

                if($tmp != null && count($tmp) > 0)
                    $lesson->group_2 = $tmp[0]->countNum;
                else
                    $lesson->group_2 = 0;

                $tmp = DB::select('select count(*) as countNum from quizRegistry qR, taraz WHERE qId = ' . $quizId .
                    " and qEntryId = qR.id and " .
                    'percent < ' . $ceil[3] . ' and percent > ' . $floor[3] . ' and lId = ' . $lesson->id .
                    " and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0");

                if($tmp != null && count($tmp) > 0)
                    $lesson->group_3 = $tmp[0]->countNum;
                else
                    $lesson->group_3 = 0;

                $tmp = DB::select('select count(*) as countNum from quizRegistry qR, taraz WHERE qId = ' . $quizId .
                    " and qEntryId = qR.id and " .
                    'percent < ' . $ceil[4] . ' and percent > ' . $floor[4] . ' and lId = ' . $lesson->id .
                    " and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0");

                if($tmp != null && count($tmp) > 0)
                    $lesson->group_4 = $tmp[0]->countNum;
                else
                    $lesson->group_4 = 0;
            }
        }

        return view('A7', array('lessons' => $lessons, 'total' => $total, 'quizId' => $quizId));
    }

    public function A7Excel($quizId) {

        $floor = [-34, 10, 30, 50, 75];
        $ceil = [11, 31, 51, 76, 101];

        $regularQuizMode = getValueInfo('regularQuiz');
        $user = Auth::user();

        $lessons = getLessonQuiz($quizId);

        if($user->level == getValueInfo('adviserLevel')) {

            $total = DB::select('SELECT count(*) as total FROM quizRegistry qR, studentsAdviser WHERE qId = ' . $quizId . ' and quizMode = ' . $regularQuizMode .
                ' and adviserId = ' . $user->id . ' and studentId = qR.uId' .
                " and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0");

            if($total == null || count($total) == 0 || empty($total->total))
                $total = 0;
            else
                $total = $total[0]->total;

            foreach ($lessons as $lesson) {

                $tmp = DB::select('select count(*) as countNum from quizRegistry qR, taraz, studentsAdviser WHERE qId = ' . $quizId .
                    ' and adviserId = ' . $user->id . " and studentId = qR.uId and qEntryId = qR.id and " .
                    'percent < ' . $ceil[0] . ' and percent > ' . $floor[0] . ' and lId = ' . $lesson->id .
                    " and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0");

                if($tmp != null && count($tmp) > 0)
                    $lesson->group_0 = $tmp[0]->countNum;
                else
                    $lesson->group_0 = 0;

                $tmp = DB::select('select count(*) as countNum from quizRegistry qR, taraz, studentsAdviser WHERE qId = ' . $quizId .
                    ' and adviserId = ' . $user->id . " and studentId = qR.uId and qEntryId = qR.id and " .
                    'percent < ' . $ceil[1] . ' and percent > ' . $floor[1] . ' and lId = ' . $lesson->id .
                    " and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0");

                if($tmp != null && count($tmp) > 0)
                    $lesson->group_1 = $tmp[0]->countNum;
                else
                    $lesson->group_1 = 0;

                $tmp = DB::select('select count(*) as countNum from quizRegistry qR, taraz, studentsAdviser WHERE qId = ' . $quizId .
                    ' and adviserId = ' . $user->id . " and studentId = qR.uId and qEntryId = qR.id and " .
                    'percent < ' . $ceil[2] . ' and percent > ' . $floor[2] . ' and lId = ' . $lesson->id .
                    " and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0");

                if($tmp != null && count($tmp) > 0)
                    $lesson->group_2 = $tmp[0]->countNum;
                else
                    $lesson->group_2 = 0;

                $tmp = DB::select('select count(*) as countNum from quizRegistry qR, taraz, studentsAdviser WHERE qId = ' . $quizId .
                    ' and adviserId = ' . $user->id . " and studentId = qR.uId and qEntryId = qR.id and " .
                    'percent < ' . $ceil[3] . ' and percent > ' . $floor[3] . ' and lId = ' . $lesson->id .
                    " and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0");

                if($tmp != null && count($tmp) > 0)
                    $lesson->group_3 = $tmp[0]->countNum;
                else
                    $lesson->group_3 = 0;

                $tmp = DB::select('select count(*) as countNum from quizRegistry qR, taraz, studentsAdviser WHERE qId = ' . $quizId .
                    ' and adviserId = ' . $user->id . " and studentId = qR.uId and qEntryId = qR.id and " .
                    'percent < ' . $ceil[4] . ' and percent > ' . $floor[4] . ' and lId = ' . $lesson->id .
                    " and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0");

                if($tmp != null && count($tmp) > 0)
                    $lesson->group_4 = $tmp[0]->countNum;
                else
                    $lesson->group_4 = 0;
            }
        }

        else if($user->level == getValueInfo('namayandeLevel')) {

            $total = DB::select('SELECT count(*) as total FROM quizRegistry qR, namayandeSchool nS, schoolStudent sS WHERE nS.sId = sS.sId and qId = ' . $quizId . ' and quizMode = ' . $regularQuizMode .
                ' and nS.nId = ' . $user->id . ' and sS.uId = qR.uId' .
                " and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0"
            );

            if($total == null || count($total) == 0 || empty($total->total))
                $total = 0;
            else
                $total = $total[0]->total;

            foreach ($lessons as $lesson) {

                $tmp = DB::select('select count(*) as countNum from quizRegistry qR, taraz, namayandeSchool nS, schoolStudent sS WHERE nS.sId = sS.sId and qId = ' . $quizId .
                    ' and nS.nId = ' . $user->id . " and sS.uId = qR.uId and qEntryId = qR.id and " .
                    'percent < ' . $ceil[0] . ' and percent > ' . $floor[0] . ' and lId = ' . $lesson->id .
                    " and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0"
                );

                if($tmp != null && count($tmp) > 0)
                    $lesson->group_0 = $tmp[0]->countNum;
                else
                    $lesson->group_0 = 0;

                $tmp = DB::select('select count(*) as countNum from quizRegistry qR, taraz, namayandeSchool nS, schoolStudent sS WHERE nS.sId = sS.sId and qId = ' . $quizId .
                    ' and nS.nId = ' . $user->id . " and sS.uId = qR.uId and qEntryId = qR.id and " .
                    'percent < ' . $ceil[1] . ' and percent > ' . $floor[1] . ' and lId = ' . $lesson->id .
                    " and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0"
                );

                if($tmp != null && count($tmp) > 0)
                    $lesson->group_1 = $tmp[0]->countNum;
                else
                    $lesson->group_1 = 0;

                $tmp = DB::select('select count(*) as countNum from quizRegistry qR, taraz, namayandeSchool nS, schoolStudent sS WHERE nS.sId = sS.sId and qId = ' . $quizId .
                    ' and nS.nId = ' . $user->id . " and sS.uId = qR.uId and qEntryId = qR.id and " .
                    'percent < ' . $ceil[2] . ' and percent > ' . $floor[2] . ' and lId = ' . $lesson->id .
                    " and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0"
                );

                if($tmp != null && count($tmp) > 0)
                    $lesson->group_2 = $tmp[0]->countNum;
                else
                    $lesson->group_2 = 0;

                $tmp = DB::select('select count(*) as countNum from quizRegistry qR, taraz, namayandeSchool nS, schoolStudent sS WHERE nS.sId = sS.sId and qId = ' . $quizId .
                    ' and nS.nId = ' . $user->id . " and sS.uId = qR.uId and qEntryId = qR.id and " .
                    'percent < ' . $ceil[3] . ' and percent > ' . $floor[3] . ' and lId = ' . $lesson->id .
                    " and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0"
                );

                if($tmp != null && count($tmp) > 0)
                    $lesson->group_3 = $tmp[0]->countNum;
                else
                    $lesson->group_3 = 0;

                $tmp = DB::select('select count(*) as countNum from quizRegistry qR, taraz, namayandeSchool nS, schoolStudent sS WHERE nS.sId = sS.sId and qId = ' . $quizId .
                    ' and nS.nId = ' . $user->id . " and sS.uId = qR.uId and qEntryId = qR.id and " .
                    'percent < ' . $ceil[4] . ' and percent > ' . $floor[4] . ' and lId = ' . $lesson->id .
                    " and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0"
                );

                if($tmp != null && count($tmp) > 0)
                    $lesson->group_4 = $tmp[0]->countNum;
                else
                    $lesson->group_4 = 0;
            }
        }
        else if($user->level == getValueInfo('schoolLevel')) {

            $total = DB::select('SELECT count(*) as total FROM quizRegistry qR, schoolStudent sS WHERE qId = ' . $quizId . ' and quizMode = ' . $regularQuizMode .
                ' and sS.sId = ' . $user->id . ' and sS.uId = qR.uId' .
                " and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0"
            );

            if($total == null || count($total) == 0 || empty($total->total))
                $total = 0;
            else
                $total = $total[0]->total;

            foreach ($lessons as $lesson) {

                $tmp = DB::select('select count(*) as countNum from quizRegistry qR, taraz, schoolStudent sS WHERE qId = ' . $quizId .
                    ' and sS.sId = ' . $user->id . " and sS.uId = qR.uId and qEntryId = qR.id and " .
                    'percent < ' . $ceil[0] . ' and percent > ' . $floor[0] . ' and lId = ' . $lesson->id .
                    " and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0"
                );

                if($tmp != null && count($tmp) > 0)
                    $lesson->group_0 = $tmp[0]->countNum;
                else
                    $lesson->group_0 = 0;

                $tmp = DB::select('select count(*) as countNum from quizRegistry qR, taraz, schoolStudent sS WHERE qId = ' . $quizId .
                    ' and sS.sId = ' . $user->id . " and sS.uId = qR.uId and qEntryId = qR.id and " .
                    'percent < ' . $ceil[1] . ' and percent > ' . $floor[1] . ' and lId = ' . $lesson->id .
                    " and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0"
                );

                if($tmp != null && count($tmp) > 0)
                    $lesson->group_1 = $tmp[0]->countNum;
                else
                    $lesson->group_1 = 0;

                $tmp = DB::select('select count(*) as countNum from quizRegistry qR, taraz, schoolStudent sS WHERE qId = ' . $quizId .
                    ' and sS.sId = ' . $user->id . " and sS.uId = qR.uId and qEntryId = qR.id and " .
                    'percent < ' . $ceil[2] . ' and percent > ' . $floor[2] . ' and lId = ' . $lesson->id .
                    " and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0"
                );

                if($tmp != null && count($tmp) > 0)
                    $lesson->group_2 = $tmp[0]->countNum;
                else
                    $lesson->group_2 = 0;

                $tmp = DB::select('select count(*) as countNum from quizRegistry qR, taraz, schoolStudent sS WHERE qId = ' . $quizId .
                    ' and sS.sId = ' . $user->id . " and sS.uId = qR.uId and qEntryId = qR.id and " .
                    'percent < ' . $ceil[3] . ' and percent > ' . $floor[3] . ' and lId = ' . $lesson->id .
                    " and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0"
                );

                if($tmp != null && count($tmp) > 0)
                    $lesson->group_3 = $tmp[0]->countNum;
                else
                    $lesson->group_3 = 0;

                $tmp = DB::select('select count(*) as countNum from quizRegistry qR, taraz, schoolStudent sS WHERE qId = ' . $quizId .
                    ' and sS.sId = ' . $user->id . " and sS.uId = qR.uId and qEntryId = qR.id and " .
                    'percent < ' . $ceil[4] . ' and percent > ' . $floor[4] . ' and lId = ' . $lesson->id .
                    " and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0"
                );

                if($tmp != null && count($tmp) > 0)
                    $lesson->group_4 = $tmp[0]->countNum;
                else
                    $lesson->group_4 = 0;
            }
        }
        else {

            $total = DB::select('SELECT count(*) as total FROM quizRegistry qR WHERE qId = ' . $quizId . ' and quizMode = ' . $regularQuizMode .
                " and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0");

            if($total == null || count($total) == 0 || empty($total->total))
                $total = 0;
            else
                $total = $total[0]->total;

            foreach ($lessons as $lesson) {

                $tmp = DB::select('select count(*) as countNum from quizRegistry qR, taraz WHERE qId = ' . $quizId .
                    " and qEntryId = qR.id and " .
                    'percent < ' . $ceil[0] . ' and percent > ' . $floor[0] . ' and lId = ' . $lesson->id .
                    " and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0");

                if($tmp != null && count($tmp) > 0)
                    $lesson->group_0 = $tmp[0]->countNum;
                else
                    $lesson->group_0 = 0;

                $tmp = DB::select('select count(*) as countNum from quizRegistry qR, taraz WHERE qId = ' . $quizId .
                    " and qEntryId = qR.id and " .
                    'percent < ' . $ceil[1] . ' and percent > ' . $floor[1] . ' and lId = ' . $lesson->id .
                    " and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0");

                if($tmp != null && count($tmp) > 0)
                    $lesson->group_1 = $tmp[0]->countNum;
                else
                    $lesson->group_1 = 0;

                $tmp = DB::select('select count(*) as countNum from quizRegistry qR, taraz WHERE qId = ' . $quizId .
                    " and qEntryId = qR.id and " .
                    'percent < ' . $ceil[2] . ' and percent > ' . $floor[2] . ' and lId = ' . $lesson->id .
                    " and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0");

                if($tmp != null && count($tmp) > 0)
                    $lesson->group_2 = $tmp[0]->countNum;
                else
                    $lesson->group_2 = 0;

                $tmp = DB::select('select count(*) as countNum from quizRegistry qR, taraz WHERE qId = ' . $quizId .
                    " and qEntryId = qR.id and " .
                    'percent < ' . $ceil[3] . ' and percent > ' . $floor[3] . ' and lId = ' . $lesson->id .
                    " and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0");

                if($tmp != null && count($tmp) > 0)
                    $lesson->group_3 = $tmp[0]->countNum;
                else
                    $lesson->group_3 = 0;

                $tmp = DB::select('select count(*) as countNum from quizRegistry qR, taraz WHERE qId = ' . $quizId .
                    " and qEntryId = qR.id and " .
                    'percent < ' . $ceil[4] . ' and percent > ' . $floor[4] . ' and lId = ' . $lesson->id .
                    " and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0");

                if($tmp != null && count($tmp) > 0)
                    $lesson->group_4 = $tmp[0]->countNum;
                else
                    $lesson->group_4 = 0;
            }
        }

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("Gachesefid");
        $objPHPExcel->getProperties()->setLastModifiedBy("Gachesefid");
        $objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.");

        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->getActiveSheet()->setCellValue('F1', 'بین 76 تا 100');
        $objPHPExcel->getActiveSheet()->setCellValue('E1', 'بین 51 تا 75');
        $objPHPExcel->getActiveSheet()->setCellValue('D1', 'بین 31 تا 50');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'بین 11 تا 30');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'بین -33 تا 10');
        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'نام درس');

        $i = 0;

        foreach($lessons as $lesson) {
            $objPHPExcel->getActiveSheet()->setCellValue('A' . ($i + 2), $lesson->name);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . ($i + 2), $lesson->group_0);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . ($i + 2), $lesson->group_1);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . ($i + 2), $lesson->group_2);
            $objPHPExcel->getActiveSheet()->setCellValue('E' . ($i + 2), $lesson->group_3);
            $objPHPExcel->getActiveSheet()->setCellValue('F' . ($i + 2), $lesson->group_4);

            $i++;
        }

        $fileName = __DIR__ . "/../../../public/registrations/A7.xlsx";

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

        return view('A7', array('lessons' => $lessons, 'total' => $total, 'quizId' => $quizId));
    }

    public function A6($quizId) {

        $subjects = getSubjectQuiz($quizId);
        $regularQuizMode = getValueInfo('regularQuiz');
        $user = Auth::user();

        if($user->level == getValueInfo('adviserLevel')) {
            foreach ($subjects as $sId) {
                $tmp = DB::select('select count(*) as countNum from ROQ, SOQ, question, studentsAdviser WHERE ROQ.quizId = ' . $quizId . " and ROQ.quizMode = " . $regularQuizMode
                    . " and question.id = SOQ.qId and adviserId = " . $user->id . " and studentId = ROQ.uId and ans = result and ROQ.questionId = SOQ.qId and SOQ.sId = " . $sId->id);
                if ($tmp == null || count($tmp) == 0)
                    $sId->correct = 0;
                else
                    $sId->correct = $tmp[0]->countNum;

                $tmp = DB::select('select count(*) as countNum from ROQ, SOQ, question, studentsAdviser WHERE ROQ.quizId = ' . $quizId . " and ROQ.quizMode = " . $regularQuizMode
                    . " and question.id = SOQ.qId and adviserId = " . $user->id . " and studentId = ROQ.uId and result <> 0 and result <> ans and ROQ.questionId = SOQ.qId and SOQ.sId = " . $sId->id);
                if ($tmp == null || count($tmp) == 0)
                    $sId->inCorrect = 0;
                else
                    $sId->inCorrect = $tmp[0]->countNum;

                $tmp = DB::select('select count(*) as countNum from ROQ, SOQ, question, studentsAdviser WHERE ROQ.quizId = ' . $quizId . " and ROQ.quizMode = " . $regularQuizMode
                    . " and question.id = SOQ.qId and adviserId = " . $user->id . " and studentId = ROQ.uId and result = 0 and ROQ.questionId = SOQ.qId and SOQ.sId = " . $sId->id);
                if ($tmp == null || count($tmp) == 0)
                    $sId->white = 0;
                else
                    $sId->white = $tmp[0]->countNum;

                $sId->lessonName = Lesson::whereId($sId->lessonId)->name;
            }
        }
        else if($user->level == getValueInfo('namayandeLevel')) {
            foreach ($subjects as $sId) {
                $tmp = DB::select('select count(*) as countNum from ROQ, SOQ, question, namayandeSchool nS, schoolStudent sS WHERE nS.sId = sS.sId and ROQ.quizId = ' . $quizId . " and ROQ.quizMode = " . $regularQuizMode
                    . " and question.id = SOQ.qId and nS.nId = " . $user->id . " and sS.uId = ROQ.uId and ans = result and ROQ.questionId = SOQ.qId and SOQ.sId = " . $sId->id);
                if ($tmp == null || count($tmp) == 0)
                    $sId->correct = 0;
                else
                    $sId->correct = $tmp[0]->countNum;

                $tmp = DB::select('select count(*) as countNum from ROQ, SOQ, question, namayandeSchool nS, schoolStudent sS WHERE nS.sId = sS.sId and ROQ.quizId = ' . $quizId . " and ROQ.quizMode = " . $regularQuizMode
                    . " and question.id = SOQ.qId and nS.nId = " . $user->id . " and sS.uId = ROQ.uId and ans <> result and result <> 0 and ROQ.questionId = SOQ.qId and SOQ.sId = " . $sId->id);
                if ($tmp == null || count($tmp) == 0)
                    $sId->inCorrect = 0;
                else
                    $sId->inCorrect = $tmp[0]->countNum;

                $tmp = DB::select('select count(*) as countNum from ROQ, SOQ, question, namayandeSchool nS, schoolStudent sS WHERE nS.sId = sS.sId and ROQ.quizId = ' . $quizId . " and ROQ.quizMode = " . $regularQuizMode
                    . " and question.id = SOQ.qId and nS.nId = " . $user->id . " and sS.uId = ROQ.uId and result = 0 and ROQ.questionId = SOQ.qId and SOQ.sId = " . $sId->id);
                if ($tmp == null || count($tmp) == 0)
                    $sId->white = 0;
                else
                    $sId->white = $tmp[0]->countNum;

                $sId->lessonName = Lesson::whereId($sId->lessonId)->name;
            }
        }
        else if($user->level == getValueInfo('schoolLevel')) {
            foreach ($subjects as $sId) {
                $tmp = DB::select('select count(*) as countNum from ROQ, SOQ, question, schoolStudent sS WHERE ROQ.quizId = ' . $quizId . " and ROQ.quizMode = " . $regularQuizMode
                    . " and question.id = SOQ.qId and sS.sId = " . $user->id . " and sS.uId = ROQ.uId and ans = result and ROQ.questionId = SOQ.qId and SOQ.sId = " . $sId->id);
                if ($tmp == null || count($tmp) == 0)
                    $sId->correct = 0;
                else
                    $sId->correct = $tmp[0]->countNum;

                $tmp = DB::select('select count(*) as countNum from ROQ, SOQ, question, schoolStudent sS WHERE ROQ.quizId = ' . $quizId . " and ROQ.quizMode = " . $regularQuizMode
                    . " and question.id = SOQ.qId and sS.sId = " . $user->id . " and sS.uId = ROQ.uId and ans <> result and result <> 0 and ROQ.questionId = SOQ.qId and SOQ.sId = " . $sId->id);
                if ($tmp == null || count($tmp) == 0)
                    $sId->inCorrect = 0;
                else
                    $sId->inCorrect = $tmp[0]->countNum;

                $tmp = DB::select('select count(*) as countNum from ROQ, SOQ, question, schoolStudent sS WHERE ROQ.quizId = ' . $quizId . " and ROQ.quizMode = " . $regularQuizMode
                    . " and question.id = SOQ.qId and sS.sId = " . $user->id . " and sS.uId = ROQ.uId and result = 0 and ROQ.questionId = SOQ.qId and SOQ.sId = " . $sId->id);
                if ($tmp == null || count($tmp) == 0)
                    $sId->white = 0;
                else
                    $sId->white = $tmp[0]->countNum;

                $sId->lessonName = Lesson::whereId($sId->lessonId)->name;
            }
        }
        else {
            foreach ($subjects as $sId) {
                $tmp = DB::select('select count(*) as countNum from ROQ, SOQ, question WHERE ROQ.quizId = ' . $quizId . " and ROQ.quizMode = " . $regularQuizMode
                    . " and question.id = SOQ.qId and ans = result and ROQ.questionId = SOQ.qId and SOQ.sId = " . $sId->id);

                if ($tmp == null || count($tmp) == 0)
                    $sId->correct = 0;
                else
                    $sId->correct = $tmp[0]->countNum;

                $tmp = DB::select('select count(*) as countNum from ROQ, SOQ, question WHERE ROQ.quizId = ' . $quizId . " and ROQ.quizMode = " . $regularQuizMode
                    . " and question.id = SOQ.qId and ans <> result and result <> 0 and ROQ.questionId = SOQ.qId and SOQ.sId = " . $sId->id);
                if ($tmp == null || count($tmp) == 0)
                    $sId->inCorrect = 0;
                else
                    $sId->inCorrect = $tmp[0]->countNum;

                $tmp = DB::select('select count(*) as countNum from ROQ, SOQ, question WHERE ROQ.quizId = ' . $quizId . " and ROQ.quizMode = " . $regularQuizMode
                    . " and question.id = SOQ.qId and result = 0 and ROQ.questionId = SOQ.qId and SOQ.sId = " . $sId->id);
                if ($tmp == null || count($tmp) == 0)
                    $sId->white = 0;
                else
                    $sId->white = $tmp[0]->countNum;

                $sId->lessonName = Lesson::whereId($sId->lessonId)->name;
            }
        }

        return view('A6', array('subjects' => $subjects, 'quizId' => $quizId));

    }

    public function A6Excel($quizId) {

        $subjects = getSubjectQuiz($quizId);
        $regularQuizMode = getValueInfo('regularQuiz');
        $user = Auth::user();

        if($user->level == getValueInfo('adviserLevel')) {
            foreach ($subjects as $sId) {
                $tmp = DB::select('select count(*) as countNum from ROQ, SOQ, question, studentsAdviser WHERE ROQ.quizId = ' . $quizId . " and ROQ.quizMode = " . $regularQuizMode
                    . " and question.id = SOQ.qId and adviserId = " . $user->id . " and studentId = ROQ.uId and ans = result and ROQ.questionId = SOQ.qId and SOQ.sId = " . $sId->id);
                if ($tmp == null || count($tmp) == 0)
                    $sId->correct = 0;
                else
                    $sId->correct = $tmp[0]->countNum;

                $tmp = DB::select('select count(*) as countNum from ROQ, SOQ, question, studentsAdviser WHERE ROQ.quizId = ' . $quizId . " and ROQ.quizMode = " . $regularQuizMode
                    . " and question.id = SOQ.qId and adviserId = " . $user->id . " and studentId = ROQ.uId and result <> 0 and result <> ans and ROQ.questionId = SOQ.qId and SOQ.sId = " . $sId->id);
                if ($tmp == null || count($tmp) == 0)
                    $sId->inCorrect = 0;
                else
                    $sId->inCorrect = $tmp[0]->countNum;

                $tmp = DB::select('select count(*) as countNum from ROQ, SOQ, question, studentsAdviser WHERE ROQ.quizId = ' . $quizId . " and ROQ.quizMode = " . $regularQuizMode
                    . " and question.id = SOQ.qId and adviserId = " . $user->id . " and studentId = ROQ.uId and result = 0 and ROQ.questionId = SOQ.qId and SOQ.sId = " . $sId->id);
                if ($tmp == null || count($tmp) == 0)
                    $sId->white = 0;
                else
                    $sId->white = $tmp[0]->countNum;

                $sId->lessonName = Lesson::whereId($sId->lessonId)->name;
            }
        }
        else if($user->level == getValueInfo('namayandeLevel')) {
            foreach ($subjects as $sId) {
                $tmp = DB::select('select count(*) as countNum from ROQ, SOQ, question, namayandeSchool nS, schoolStudent sS WHERE nS.sId = sS.sId and ROQ.quizId = ' . $quizId . " and ROQ.quizMode = " . $regularQuizMode
                    . " and question.id = SOQ.qId and nS.nId = " . $user->id . " and sS.uId = ROQ.uId and ans = result and ROQ.questionId = SOQ.qId and SOQ.sId = " . $sId->id);
                if ($tmp == null || count($tmp) == 0)
                    $sId->correct = 0;
                else
                    $sId->correct = $tmp[0]->countNum;

                $tmp = DB::select('select count(*) as countNum from ROQ, SOQ, question, namayandeSchool nS, schoolStudent sS WHERE nS.sId = sS.sId and ROQ.quizId = ' . $quizId . " and ROQ.quizMode = " . $regularQuizMode
                    . " and question.id = SOQ.qId and nS.nId = " . $user->id . " and sS.uId = ROQ.uId and ans <> result and result <> 0 and ROQ.questionId = SOQ.qId and SOQ.sId = " . $sId->id);
                if ($tmp == null || count($tmp) == 0)
                    $sId->inCorrect = 0;
                else
                    $sId->inCorrect = $tmp[0]->countNum;

                $tmp = DB::select('select count(*) as countNum from ROQ, SOQ, question, namayandeSchool nS, schoolStudent sS WHERE nS.sId = sS.sId and ROQ.quizId = ' . $quizId . " and ROQ.quizMode = " . $regularQuizMode
                    . " and question.id = SOQ.qId and nS.nId = " . $user->id . " and sS.uId = ROQ.uId and result = 0 and ROQ.questionId = SOQ.qId and SOQ.sId = " . $sId->id);
                if ($tmp == null || count($tmp) == 0)
                    $sId->white = 0;
                else
                    $sId->white = $tmp[0]->countNum;

                $sId->lessonName = Lesson::whereId($sId->lessonId)->name;
            }
        }
        else if($user->level == getValueInfo('schoolLevel')) {
            foreach ($subjects as $sId) {
                $tmp = DB::select('select count(*) as countNum from ROQ, SOQ, question, schoolStudent sS WHERE ROQ.quizId = ' . $quizId . " and ROQ.quizMode = " . $regularQuizMode
                    . " and question.id = SOQ.qId and sS.sId = " . $user->id . " and sS.uId = ROQ.uId and ans = result and ROQ.questionId = SOQ.qId and SOQ.sId = " . $sId->id);
                if ($tmp == null || count($tmp) == 0)
                    $sId->correct = 0;
                else
                    $sId->correct = $tmp[0]->countNum;

                $tmp = DB::select('select count(*) as countNum from ROQ, SOQ, question, schoolStudent sS WHERE ROQ.quizId = ' . $quizId . " and ROQ.quizMode = " . $regularQuizMode
                    . " and question.id = SOQ.qId and sS.sId = " . $user->id . " and sS.uId = ROQ.uId and ans <> result and result <> 0 and ROQ.questionId = SOQ.qId and SOQ.sId = " . $sId->id);
                if ($tmp == null || count($tmp) == 0)
                    $sId->inCorrect = 0;
                else
                    $sId->inCorrect = $tmp[0]->countNum;

                $tmp = DB::select('select count(*) as countNum from ROQ, SOQ, question, schoolStudent sS WHERE ROQ.quizId = ' . $quizId . " and ROQ.quizMode = " . $regularQuizMode
                    . " and question.id = SOQ.qId and sS.sId = " . $user->id . " and sS.uId = ROQ.uId and result = 0 and ROQ.questionId = SOQ.qId and SOQ.sId = " . $sId->id);
                if ($tmp == null || count($tmp) == 0)
                    $sId->white = 0;
                else
                    $sId->white = $tmp[0]->countNum;

                $sId->lessonName = Lesson::whereId($sId->lessonId)->name;
            }
        }
        else {
            foreach ($subjects as $sId) {
                $tmp = DB::select('select count(*) as countNum from ROQ, SOQ, question WHERE ROQ.quizId = ' . $quizId . " and ROQ.quizMode = " . $regularQuizMode
                    . " and question.id = SOQ.qId and ans = result and ROQ.questionId = SOQ.qId and SOQ.sId = " . $sId->id);

                if ($tmp == null || count($tmp) == 0)
                    $sId->correct = 0;
                else
                    $sId->correct = $tmp[0]->countNum;

                $tmp = DB::select('select count(*) as countNum from ROQ, SOQ, question WHERE ROQ.quizId = ' . $quizId . " and ROQ.quizMode = " . $regularQuizMode
                    . " and question.id = SOQ.qId and ans <> result and result <> 0 and ROQ.questionId = SOQ.qId and SOQ.sId = " . $sId->id);
                if ($tmp == null || count($tmp) == 0)
                    $sId->inCorrect = 0;
                else
                    $sId->inCorrect = $tmp[0]->countNum;

                $tmp = DB::select('select count(*) as countNum from ROQ, SOQ, question WHERE ROQ.quizId = ' . $quizId . " and ROQ.quizMode = " . $regularQuizMode
                    . " and question.id = SOQ.qId and result = 0 and ROQ.questionId = SOQ.qId and SOQ.sId = " . $sId->id);
                if ($tmp == null || count($tmp) == 0)
                    $sId->white = 0;
                else
                    $sId->white = $tmp[0]->countNum;

                $sId->lessonName = Lesson::whereId($sId->lessonId)->name;
            }
        }

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("Gachesefid");
        $objPHPExcel->getProperties()->setLastModifiedBy("Gachesefid");
        $objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.");

        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->getActiveSheet()->setCellValue('F1', 'درصد');
        $objPHPExcel->getActiveSheet()->setCellValue('E1', 'بدون پاسخ');
        $objPHPExcel->getActiveSheet()->setCellValue('D1', 'نادرست');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'درست');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'نام مبحث');
        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'نام درس');

        $i = 0;
        foreach($subjects as $subject) {
            $objPHPExcel->getActiveSheet()->setCellValue('A' . ($i + 2), $subject->lessonName);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . ($i + 2), $subject->name);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . ($i + 2), $subject->correct);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . ($i + 2), $subject->inCorrect);
            $objPHPExcel->getActiveSheet()->setCellValue('E' . ($i + 2), $subject->white);
            if($subject->correct + $subject->inCorrect + $subject->white != 0)
                $objPHPExcel->getActiveSheet()->setCellValue('F' . ($i + 2), round($subject->correct * 100 / ($subject->correct + $subject->inCorrect + $subject->white), 0));
            else
                $objPHPExcel->getActiveSheet()->setCellValue('F' . 0);
            $i++;
        }

        $fileName = __DIR__ . "/../../../public/registrations/A6.xlsx";

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

        return view('A6', array('subjects' => $subjects, 'quizId' => $quizId));

    }

    public function A4($quizId) {

        $user = Auth::user();

        $lessonsNo = count(getLessonQuiz($quizId));

        if($user->level == getValueInfo('adviserLevel')) {
            $cities = $this->getCitiesInQuiz($quizId, $user->id, $user->level);

            foreach ($cities as $city) {

                $lessons = DB::select('select coherence, percent from redundantInfo1 rd, taraz, quizRegistry qR, lesson, studentsAdviser WHERE lesson.id = lId and rd.cityId = ' . $city->id .
                    ' and qR.qId = ' . $quizId . ' and qR.id = qEntryId and rd.uId = qR.uId and studentId = qR.uId and adviserId = ' . $user->id .
                    " and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0");

                $count = 0;
                $sum = 0;
                $sumCoherence = 0;

                $city->group_0 = 0;
                $city->group_1 = 0;
                $city->group_2 = 0;
                $city->group_3 = 0;
                $city->group_4 = 0;

                foreach ($lessons as $lesson) {

                    if($lesson->coherence != 0) {
                        $sum += $lesson->coherence * $lesson->percent;
                        $sumCoherence += $lesson->coherence;
                    }
                    else {
                        $sum += $lesson->percent;
                        $sumCoherence += 1;
                    }
                    $count++;
                    if ($count % $lessonsNo == 0) {
                        $sum /= $sumCoherence;

                        if ($sum < 11)
                            $city->group_0 = $city->group_0 + 1;
                        else if ($sum < 31)
                            $city->group_1 = $city->group_1 + 1;
                        else if ($sum < 51)
                            $city->group_2 = $city->group_2 + 1;
                        else if ($sum < 76)
                            $city->group_3 = $city->group_3 + 1;
                        else
                            $city->group_4 = $city->group_4 + 1;

                        $sum = 0;
                        $sumCoherence = 0;
                    }
                }
            }
        }
        else if($user->level == getValueInfo('namayandeLevel')) {
            $cities = $this->getCitiesInQuiz($quizId, $user->id, $user->level);

            foreach ($cities as $city) {

                $lessons = DB::select('select coherence, percent from redundantInfo1 rd, taraz, quizRegistry qR, lesson, namayandeSchool nS, schoolStudent sS WHERE nS.sId = sS.sId and lesson.id = lId and rd.cityId = ' . $city->id .
                    ' and qR.qId = ' . $quizId . ' and qR.id = qEntryId and rd.uId = qR.uId and sS.uId = qR.uId and nS.nId = ' . $user->id .
                    " and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0"
                );

                $count = 0;
                $sum = 0;
                $sumCoherence = 0;

                $city->group_0 = 0;
                $city->group_1 = 0;
                $city->group_2 = 0;
                $city->group_3 = 0;
                $city->group_4 = 0;

                foreach ($lessons as $lesson) {

                    if($lesson->coherence != 0) {
                        $sum += $lesson->coherence * $lesson->percent;
                        $sumCoherence += $lesson->coherence;
                    }
                    else {
                        $sum += $lesson->percent;
                        $sumCoherence += 1;
                    }
                    $count++;
                    if ($count % $lessonsNo == 0) {
                        $sum /= $sumCoherence;

                        if ($sum < 11)
                            $city->group_0 = $city->group_0 + 1;
                        else if ($sum < 31)
                            $city->group_1 = $city->group_1 + 1;
                        else if ($sum < 51)
                            $city->group_2 = $city->group_2 + 1;
                        else if ($sum < 76)
                            $city->group_3 = $city->group_3 + 1;
                        else
                            $city->group_4 = $city->group_4 + 1;

                        $sum = 0;
                        $sumCoherence = 0;
                    }
                }
            }
        }
        else if($user->level == getValueInfo('schoolLevel')) {
            $cities = $this->getCitiesInQuiz($quizId, $user->id, $user->level);

            foreach ($cities as $city) {

                $lessons = DB::select('select coherence, percent from redundantInfo1 rd, taraz, quizRegistry qR, lesson, schoolStudent sS WHERE lesson.id = lId and rd.cityId = ' . $city->id .
                    ' and qR.qId = ' . $quizId . ' and qR.id = qEntryId and rd.uId = qR.uId and sS.uId = qR.uId and sS.sId = ' . $user->id .
                    " and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0"
                );

                $count = 0;
                $sum = 0;
                $sumCoherence = 0;

                $city->group_0 = 0;
                $city->group_1 = 0;
                $city->group_2 = 0;
                $city->group_3 = 0;
                $city->group_4 = 0;

                foreach ($lessons as $lesson) {

                    if($lesson->coherence != 0) {
                        $sum += $lesson->coherence * $lesson->percent;
                        $sumCoherence += $lesson->coherence;
                    }
                    else {
                        $sum += $lesson->percent;
                        $sumCoherence += 1;
                    }
                    $count++;
                    if ($count % $lessonsNo == 0) {
                        $sum /= $sumCoherence;

                        if ($sum < 11)
                            $city->group_0 = $city->group_0 + 1;
                        else if ($sum < 31)
                            $city->group_1 = $city->group_1 + 1;
                        else if ($sum < 51)
                            $city->group_2 = $city->group_2 + 1;
                        else if ($sum < 76)
                            $city->group_3 = $city->group_3 + 1;
                        else
                            $city->group_4 = $city->group_4 + 1;

                        $sum = 0;
                        $sumCoherence = 0;
                    }
                }
            }
        }
        else {
            $cities = $this->getCitiesInQuiz($quizId, -1);

            foreach ($cities as $city) {

                $lessons = DB::select('select coherence, percent from redundantInfo1 rd, taraz, quizRegistry qR, lesson WHERE lesson.id = lId and rd.cityId = ' . $city->id .
                    ' and qR.qId = ' . $quizId . ' and qR.id = qEntryId and rd.uId = qR.uId' .
                    " and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0");

                $count = 0;
                $sum = 0;
                $sumCoherence = 0;

                $city->group_0 = 0;
                $city->group_1 = 0;
                $city->group_2 = 0;
                $city->group_3 = 0;
                $city->group_4 = 0;

                foreach ($lessons as $lesson) {

                    if($lesson->coherence != 0) {
                        $sum += $lesson->coherence * $lesson->percent;
                        $sumCoherence += $lesson->coherence;
                    }
                    else {
                        $sum += $lesson->percent;
                        $sumCoherence += 1;
                    }
                    $count++;

                    if($count % $lessonsNo == 0) {
                        $sum /= $sumCoherence;

                        if($sum < 11)
                            $city->group_0 = $city->group_0 + 1;
                        else if($sum < 31)
                            $city->group_1 = $city->group_1 + 1;
                        else if($sum < 51)
                            $city->group_2 = $city->group_2 + 1;
                        else if($sum < 76)
                            $city->group_3 = $city->group_3 + 1;
                        else
                            $city->group_4 = $city->group_4 + 1;

                        $sum = 0;
                        $sumCoherence = 0;
                    }
                }
            }
        }
        return view('A4', array('cities' => $cities, 'quizId' => $quizId));
    }

    public function A4Excel($quizId) {

        $user = Auth::user();

        $lessonsNo = count(getLessonQuiz($quizId));

        if($user->level == getValueInfo('adviserLevel')) {
            $cities = $this->getCitiesInQuiz($quizId, $user->id, $user->level);

            foreach ($cities as $city) {

                $lessons = DB::select('select coherence, percent from redundantInfo1 rd, taraz, quizRegistry qR, lesson, studentsAdviser WHERE lesson.id = lId and rd.cityId = ' . $city->id .
                    ' and qR.qId = ' . $quizId . ' and qR.id = qEntryId and rd.uId = qR.uId and studentId = qR.uId and adviserId = ' . $user->id .
                    " and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0");

                $count = 0;
                $sum = 0;
                $sumCoherence = 0;

                $city->group_0 = 0;
                $city->group_1 = 0;
                $city->group_2 = 0;
                $city->group_3 = 0;
                $city->group_4 = 0;

                foreach ($lessons as $lesson) {

                    if($lesson->coherence != 0) {
                        $sum += $lesson->coherence * $lesson->percent;
                        $sumCoherence += $lesson->coherence;
                    }
                    else {
                        $sum += $lesson->percent;
                        $sumCoherence += 1;
                    }
                    $count++;
                    if ($count % $lessonsNo == 0) {
                        $sum /= $sumCoherence;

                        if ($sum < 11)
                            $city->group_0 = $city->group_0 + 1;
                        else if ($sum < 31)
                            $city->group_1 = $city->group_1 + 1;
                        else if ($sum < 51)
                            $city->group_2 = $city->group_2 + 1;
                        else if ($sum < 76)
                            $city->group_3 = $city->group_3 + 1;
                        else
                            $city->group_4 = $city->group_4 + 1;

                        $sum = 0;
                        $sumCoherence = 0;
                    }
                }
            }
        }
        else if($user->level == getValueInfo('namayandeLevel')) {
            $cities = $this->getCitiesInQuiz($quizId, $user->id, $user->level);

            foreach ($cities as $city) {

                $lessons = DB::select('select coherence, percent from redundantInfo1 rd, taraz, quizRegistry qR, lesson, namayandeSchool nS, schoolStudent sS WHERE nS.sId = sS.sId and lesson.id = lId and rd.cityId = ' . $city->id .
                    ' and qR.qId = ' . $quizId . ' and qR.id = qEntryId and rd.uId = qR.uId and sS.uId = qR.uId and nS.nId = ' . $user->id .
                    " and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0"
                );

                $count = 0;
                $sum = 0;
                $sumCoherence = 0;

                $city->group_0 = 0;
                $city->group_1 = 0;
                $city->group_2 = 0;
                $city->group_3 = 0;
                $city->group_4 = 0;

                foreach ($lessons as $lesson) {

                    if($lesson->coherence != 0) {
                        $sum += $lesson->coherence * $lesson->percent;
                        $sumCoherence += $lesson->coherence;
                    }
                    else {
                        $sum += $lesson->percent;
                        $sumCoherence += 1;
                    }
                    $count++;
                    if ($count % $lessonsNo == 0) {
                        $sum /= $sumCoherence;

                        if ($sum < 11)
                            $city->group_0 = $city->group_0 + 1;
                        else if ($sum < 31)
                            $city->group_1 = $city->group_1 + 1;
                        else if ($sum < 51)
                            $city->group_2 = $city->group_2 + 1;
                        else if ($sum < 76)
                            $city->group_3 = $city->group_3 + 1;
                        else
                            $city->group_4 = $city->group_4 + 1;

                        $sum = 0;
                        $sumCoherence = 0;
                    }
                }
            }
        }
        else if($user->level == getValueInfo('schoolLevel')) {
            $cities = $this->getCitiesInQuiz($quizId, $user->id, $user->level);

            foreach ($cities as $city) {

                $lessons = DB::select('select coherence, percent from redundantInfo1 rd, taraz, quizRegistry qR, lesson, schoolStudent sS WHERE lesson.id = lId and rd.cityId = ' . $city->id .
                    ' and qR.qId = ' . $quizId . ' and qR.id = qEntryId and rd.uId = qR.uId and sS.uId = qR.uId and sS.sId = ' . $user->id .
                    " and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0"
                );

                $count = 0;
                $sum = 0;
                $sumCoherence = 0;

                $city->group_0 = 0;
                $city->group_1 = 0;
                $city->group_2 = 0;
                $city->group_3 = 0;
                $city->group_4 = 0;

                foreach ($lessons as $lesson) {

                    if($lesson->coherence != 0) {
                        $sum += $lesson->coherence * $lesson->percent;
                        $sumCoherence += $lesson->coherence;
                    }
                    else {
                        $sum += $lesson->percent;
                        $sumCoherence += 1;
                    }
                    $count++;
                    if ($count % $lessonsNo == 0) {
                        $sum /= $sumCoherence;

                        if ($sum < 11)
                            $city->group_0 = $city->group_0 + 1;
                        else if ($sum < 31)
                            $city->group_1 = $city->group_1 + 1;
                        else if ($sum < 51)
                            $city->group_2 = $city->group_2 + 1;
                        else if ($sum < 76)
                            $city->group_3 = $city->group_3 + 1;
                        else
                            $city->group_4 = $city->group_4 + 1;

                        $sum = 0;
                        $sumCoherence = 0;
                    }
                }
            }
        }
        else {
            $cities = $this->getCitiesInQuiz($quizId, -1);

            foreach ($cities as $city) {

                $lessons = DB::select('select coherence, percent from redundantInfo1 rd, taraz, quizRegistry qR, lesson WHERE lesson.id = lId and rd.cityId = ' . $city->id .
                    ' and qR.qId = ' . $quizId . ' and qR.id = qEntryId and rd.uId = qR.uId' .
                    " and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0");

                $count = 0;
                $sum = 0;
                $sumCoherence = 0;

                $city->group_0 = 0;
                $city->group_1 = 0;
                $city->group_2 = 0;
                $city->group_3 = 0;
                $city->group_4 = 0;

                foreach ($lessons as $lesson) {

                    if($lesson->coherence != 0) {
                        $sum += $lesson->coherence * $lesson->percent;
                        $sumCoherence += $lesson->coherence;
                    }
                    else {
                        $sum += $lesson->percent;
                        $sumCoherence += 1;
                    }
                    $count++;

                    if($count % $lessonsNo == 0) {
                        $sum /= $sumCoherence;

                        if($sum < 11)
                            $city->group_0 = $city->group_0 + 1;
                        else if($sum < 31)
                            $city->group_1 = $city->group_1 + 1;
                        else if($sum < 51)
                            $city->group_2 = $city->group_2 + 1;
                        else if($sum < 76)
                            $city->group_3 = $city->group_3 + 1;
                        else
                            $city->group_4 = $city->group_4 + 1;

                        $sum = 0;
                        $sumCoherence = 0;
                    }
                }
            }
        }

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("Gachesefid");
        $objPHPExcel->getProperties()->setLastModifiedBy("Gachesefid");
        $objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.");

        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->getActiveSheet()->setCellValue('F1', 'بین 76 تا 100');
        $objPHPExcel->getActiveSheet()->setCellValue('E1', 'بین 51 تا 75');
        $objPHPExcel->getActiveSheet()->setCellValue('D1', 'بین 31 تا 50');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'بین 11 تا 30');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'بین -33 تا 10');
        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'شهر');


        $i = 2;
        foreach($cities as $city) {
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $city->name);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $city->group_0);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $city->group_1);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $city->group_2);
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $city->group_3);
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $city->group_4);
            $i++;
        }

        $fileName = __DIR__ . "/../../../public/registrations/A4.xlsx";

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

        return view('A4', array('cities' => $cities, 'quizId' => $quizId));
    }

    public function preA3($quizId, $err = "") {

        $user = Auth::user();

        if($user->level == getValueInfo('adviserLevel'))
            $uIds = DB::select('select users.id, users.firstName, users.lastName from studentsAdviser, quizRegistry qR, users WHERE adviserId = ' . $user->id .
                ' and qR.uId = studentId and qId = ' . $quizId . ' and quizMode = ' . getValueInfo('regularQuiz') .
                ' and users.id = qR.uId' .
                " and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0"
            );
        else if($user->level == getValueInfo('namayandeLevel'))
            $uIds = DB::select('select users.id, users.firstName, users.lastName from namayandeSchool nS, schoolStudent sS, quizRegistry qR, users WHERE nS.sId = sS.sId and nS.nId = ' . $user->id .
                ' and qR.uId = sS.uId and qId = ' . $quizId . ' and quizMode = ' . getValueInfo('regularQuiz') .
                ' and users.id = qR.uId' .
                " and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0"
            );
        else if($user->level == getValueInfo('schoolLevel'))
            $uIds = DB::select('select users.id, users.firstName, users.lastName from schoolStudent sS, quizRegistry qR, users WHERE sS.sId = ' . $user->id .
                ' and qR.uId = sS.uId and qId = ' . $quizId . ' and quizMode = ' . getValueInfo('regularQuiz') .
                ' and users.id = qR.uId' .
                " and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0"
            );
        else
            $uIds = DB::select('select users.id, users.firstName, users.lastName from quizRegistry qR, users WHERE ' .
                'qId = ' . $quizId . ' and quizMode = ' . getValueInfo('regularQuiz') .
                ' and users.id = qR.uId' .
                " and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0"
            );

        return view('chooseStudent', array('uIds' => $uIds, 'quizId' => $quizId, 'err' => $err));

    }

    public function printKarname($quizId, $uId = "") {

        if(empty($uId))
            $uId = Auth::user()->id;
        else {
            $level = Auth::user()->level;
            $currId = Auth::user()->id;

            $condition1 = ["sId" => $currId, 'uId' => $uId];

            if($level == getValueInfo('schoolLevel') && SchoolStudent::where($condition1)->count() == 0)
                return Redirect::to(route('profile'));

            if($level == getValueInfo('namayandeLevel')) {
                $tmp = DB::select('select count(*) as countNum from namayandeSchool nS, schoolStudent sS where nS.sId = sS.sId and nS.nId = ' . $currId .
                    ' and sS.uId = ' . $uId);
                if($tmp == null || count($tmp) == 0 || $tmp->countNum == 0) {
                    return Redirect::to(route('profile'));
                }
            }
            if($level != getValueInfo('adminLevel') && $level != getValueInfo('superAdminLevel') && $level == getValueInfo('namayandeLevel')
                && $level == getValueInfo('schoolLevel'))
                return Redirect::to(route('profile'));
        }

        $status = QuizStatus::whereLevel(1)->get();
        $rank = calcRank($quizId, $uId);

        $condition = ['qId' => $quizId, 'quizMode' => getValueInfo('regularQuiz'), 'uId' => $uId];
        $qEntryId = QuizRegistry::where($condition)->first();

        $rankInLesson = array();
        $rankInLessonCity = array();
        $rankInLessonState = array();

        $cityId = RedundantInfo1::whereUId($uId)->first();

        if($cityId == null)
            $cityId = City::first()->id;
        else
            $cityId = $cityId->cityId;

        $cityRank = calcRankInCity($quizId, $uId, $cityId);

        $stateId = State::whereId(City::whereId($cityId)->stateId)->id;
        $stateRank = calcRankInState($quizId, $uId, $stateId);

        $avgs = DB::select('select SUM(percent) / count(*) as avg, MAX(percent) as maxPercent, MIN(percent) as minPercent FROM taraz, quizRegistry WHERE quizRegistry.qId = ' . $quizId . ' and quizRegistry.id  = taraz.qEntryId GROUP by(taraz.lId)');


        $inCorrects =  DB::select('SELECT count(*) as inCorrects, subject.lessonId as target FROM ROQ, SOQ, question, subject WHERE ROQ.quizId = ' . $quizId . ' and ROQ.questionId = question.id and question.ans <> ROQ.result and ROQ.result <> 0 and ROQ.uId = ' . $uId . ' and subject.id = SOQ.sId and SOQ.qId = question.id group by(subject.lessonId)');
        $corrects =  DB::select('SELECT count(*) as corrects, subject.lessonId as target FROM ROQ, SOQ, question, subject WHERE ROQ.quizId = ' . $quizId . ' and ROQ.questionId = question.id and question.ans = ROQ.result and ROQ.uId = ' . $uId . ' and subject.id = SOQ.sId and SOQ.qId = question.id group by(subject.lessonId)');
        $total = DB::select('SELECT count(*) as total, subject.lessonId as target FROM ROQ, SOQ, question, subject WHERE ROQ.quizId = ' . $quizId . ' and ROQ.questionId = question.id and ROQ.uId = ' . $uId . ' and subject.id = SOQ.sId and SOQ.qId = question.id group by(subject.lessonId)');

        $roq = $this->getResultOfSpecificContainer($total, $corrects, $inCorrects);

        $lessons = getLessonQuiz($quizId);

        $taraz = Taraz::whereQEntryId($qEntryId->id)->get();

        $counter = 0;
        foreach ($lessons as $lesson) {
            $tmp = DB::select('SELECT quizRegistry.uId, taraz.taraz from quizRegistry, taraz WHERE quizRegistry.id = taraz.qEntryId and quizRegistry.qId = ' . $quizId . ' and taraz.lId = ' . $lesson->id . ' ORDER by taraz.taraz DESC');
            $rankInLesson[$counter++] = $this->getRank($tmp, $uId);
        }

        $counter = 0;
        foreach ($lessons as $lesson) {
            $tmp = DB::select('SELECT quizRegistry.uId, taraz.taraz from redundantInfo1 rd, city ci, quizRegistry, taraz WHERE rd.uId = quizRegistry.uId and rd.cityId = ci.id and ci.stateId = ' . $stateId . ' and quizRegistry.id = taraz.qEntryId and quizRegistry.qId = ' . $quizId . ' and taraz.lId = ' . $lesson->id . ' ORDER by taraz.taraz DESC');
            $rankInLessonState[$counter++] = $this->getRank($tmp, $uId);
        }

        $counter = 0;
        foreach ($lessons as $lesson) {
            $tmp = DB::select('SELECT quizRegistry.uId, taraz.taraz from redundantInfo1 rd, quizRegistry, taraz WHERE rd.uId = quizRegistry.uId and rd.cityId = ' . $cityId . ' and quizRegistry.id = taraz.qEntryId and quizRegistry.qId = ' . $quizId . ' and taraz.lId = ' . $lesson->id . ' ORDER by taraz.taraz DESC');
            $rankInLessonCity[$counter++] = $this->getRank($tmp, $uId);
        }

        $totalMark = 20;
        $user = User::whereId($uId);

        $regularQuizMode = getValueInfo('regularQuiz');

        $qInfos = DB::select("select question.id, question.ans, ROQ.result ".
            "from question, ROQ WHERE ROQ.quizId = " . $quizId . " and " .
            "ROQ.questionId = question.id and ROQ.quizMode = " . $regularQuizMode . " and ROQ.uId = " . $uId .
            " order by ROQ.id ASC");

        if(count($qInfos) == 0)
            return Redirect::to(route('seeResult'));

        $condition = ['questionId' => $qInfos[0]->id, 'quizId' => $quizId, 'quizMode' => $regularQuizMode];
        $totalQKarname = ROQ::where($condition)->count();

        foreach ($qInfos as $qInfo) {

            $contents = DB::select('select subject.name as subjectName from SOQ, subject WHERE SOQ.qId = ' . $qInfo->id . ' and SOQ.sId = subject.id');
            $subjectsTmp = [];
            $i = 0;
            foreach ($contents as $content) {
                $subjectsTmp[$i] = $content->subjectName;
                $i++;
            }
            $qInfo->subjects = $subjectsTmp;
            $qInfo->level = getQuestionLevel($qInfo->id);
        }

        return view('printKarname', array('quizId' => $quizId, 'status' => $status, 'name' => $user->firstName . ' ' . $user->lastName,
            'rank' => $rank, 'rankInLessonCity' => $rankInLessonCity, 'rankInLesson' => $rankInLesson, 'uId' => $uId, 'qInfos' => $qInfos,
            'lessons' => $lessons, 'taraz' => $taraz, 'rankInLessonState' => $rankInLessonState, 'stateRank' => $stateRank, 'totalQKarname' => $totalQKarname,
            'avgs' => $avgs, 'roq' => $roq, 'cityRank' => $cityRank, "totalMark" => $totalMark));
        
    }

    public function printA5($quizId) {

        $user = Auth::user();
        $regularQuizMode = getValueInfo('regularQuiz');

        if($user->level == getValueInfo('adviserLevel')) {
            $users = DB::select('SELECT qR.id, qR.uId, sum(taraz.taraz * (SELECT lesson.coherence FROM lesson WHERE lesson.id = taraz.lId)) as weighted_avg from quizRegistry qR, studentsAdviser sA, taraz WHERE sA.adviserId = ' . $user->id . ' and sA.studentId = qR.uId and quizMode = ' . $regularQuizMode . ' and qR.id = taraz.qEntryId and qR.qId = ' . $quizId . ' and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0 GROUP by (qR.id) ORDER by weighted_avg DESC');
        }
        else if($user->level == getValueInfo('namayandeLevel')) {
            $users = DB::select('SELECT qR.id, qR.uId, sum(taraz.taraz * (SELECT lesson.coherence FROM lesson WHERE lesson.id = taraz.lId)) as weighted_avg from namayandeSchool nS, quizRegistry qR, schoolStudent sS, taraz WHERE nS.sId = sS.sId and nS.nId = ' . $user->id . ' and sS.uId = qR.uId and quizMode = ' . $regularQuizMode . ' and qR.id = taraz.qEntryId and qR.qId = ' . $quizId . ' and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0 GROUP by (qR.id) ORDER by weighted_avg DESC');
        }
        else if($user->level == getValueInfo('schoolLevel')) {
            $users = DB::select('SELECT qR.id, qR.uId, sum(taraz.taraz * (SELECT lesson.coherence FROM lesson WHERE lesson.id = taraz.lId)) as weighted_avg from quizRegistry qR, schoolStudent sS, taraz WHERE sS.sId = ' . $user->id . ' and sS.uId = qR.uId and quizMode = ' . $regularQuizMode . ' and qR.id = taraz.qEntryId and qR.qId = ' . $quizId . ' and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0 GROUP by (qR.id) ORDER by weighted_avg DESC');
        }
        else {
            $users = DB::select('SELECT qR.id, qR.uId, sum(taraz.taraz * (SELECT lesson.coherence FROM lesson WHERE lesson.id = taraz.lId)) as weighted_avg from quizRegistry qR, taraz WHERE quizMode = ' . $regularQuizMode . ' and qR.id = taraz.qEntryId and qR.qId = ' . $quizId . ' and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0 GROUP by (qR.id) ORDER by weighted_avg DESC');

        }

        $tmp = DB::select('SELECT DISTINCT L.id, L.name, L.coherence from lesson L, SOQ SO, subject S, regularQOQ QO WHERE QO.quizId = ' . $quizId . ' and QO.questionId = SO.qId and SO.sId = S.id and S.lessonId = L.id order by L.id ASC');
        $sum = 0;

        if($tmp == null || count($tmp) == 0)
            $sum = 1;

        else {
            foreach ($tmp as $itr) {
                $sum += $itr->coherence;
            }
        }

        for($i = 0; $i < count($users); $i++)
            $users[$i]->rank = ($i + 1);


        $preTaraz = (count($users) > 0) ? round($users[0]->weighted_avg / $sum, 0) : 0;

        for ($i = 1; $i < count($users); $i++) {

            if ($preTaraz == round($users[$i]->weighted_avg / $sum, 0))
                $users[$i]->rank = $users[$i - 1]->rank;
            else
                $preTaraz = $users[$i - 1]->rank;
        }

        foreach ($users as $user) {

            $tmp = DB::select('select lesson.name, lesson.coherence, taraz.percent, taraz.taraz from taraz, lesson WHERE taraz.qEntryId = ' . $user->id .
                ' and lesson.id = taraz.lId');

            $user->lessons = $tmp;

            $target = User::whereId($user->uId);
            $user->name = $target->firstName . " " . $target->lastName;
            $user->uId = $target->id;

            $user->schoolName = "نامشخص";
            $schTmp = SchoolStudent::whereUId($target->id)->first();
            if($schTmp != null) {
                $schTmp = School::whereUId($schTmp->sId)->first();
                if($schTmp != null)
                    $user->schoolName = $schTmp->name;
            }

            $cityAndState = getStdCityAndState($target->id);
            $user->city = $cityAndState['city'];

            $user->state = $cityAndState['state'];

            $user->cityRank = calcRankInCity($quizId, $user->uId, $cityAndState['cityId']);
            $user->stateRank = calcRankInState($quizId, $user->uId, $cityAndState['stateId']);

        }

        usort($users, function ($a, $b) {
            return $a->rank - $b->rank;
        });

        return view('printA5', array('users' => $users, 'quizId' => $quizId));
    }

    public function A3($quizId, $uId, $backURL = "") {

        $condition = ['qId' => $quizId, 'quizMode' => getValueInfo('regularQuiz'), 'uId' => $uId];
        $qEntryId = QuizRegistry::where($condition)->first();
        
        if($qEntryId == null || ($qEntryId->online == 1 && empty($qEntryId->timeEntry))) {

            if(empty($backURL))
                return $this->preA3($quizId, 'فرد مورد نظر در آزمون شرکت نکرده است');
            else
                return $this->A5($quizId, 'فرد مورد نظر در آزمون شرکت نکرده است');
        }

        $condition = ['quizId' => $quizId, 'quizMode' => getValueInfo('regularQuiz'), 'uId' => $uId];
        if(ROQ::where($condition)->count() == 0) {
            if(empty($backURL))
                return $this->preA3($quizId, 'فرد مورد نظر در آزمون شرکت نکرده است');
            else
                return $this->A5($quizId, 'فرد مورد نظر در آزمون شرکت نکرده است');
        }

        return $this->showGeneralKarname($uId, $quizId, $qEntryId, $backURL);
    }

    private function getRank($tmp, $uId) {
        for($j = 0; $j < count($tmp); $j++) {
            if($tmp[$j]->uId == $uId) {
                $r = $j + 1;
                $currTaraz = $tmp[$j]->taraz;
                $k = $j - 1;
                while ($k >= 0 && $tmp[$k]->taraz == $currTaraz) {
                    $k--;
                    $r--;
                }
                return $r;
            }
        }
        return count($tmp);
    }

    public static function partialQuizReport($quizId) {

        $quiz = RegularQuiz::whereId($quizId);

        if($quiz == null)
            return Redirect::to('quizReport');

        $online = DB::select('select s.id, s.name, count(*) as countNum, ci.name as cityName, sa.name as stateName from quizRegistry qR, users u, schoolStudent sS, school s, city ci, state sa WHERE qR.qId = ' . $quizId . ' and qR.quizMode = ' .
            getValueInfo('regularQuiz') . ' and qR.online = 1 and u.id = qR.uId and sS.uId = u.id and s.uId = sS.sId and ci.id = s.cityId and sa.id = ci.stateId group by(sS.sId)');

        $nonOnline = DB::select('select s.id, s.name, count(*) as countNum, ci.name as cityName, sa.name as stateName from quizRegistry qR, users u, schoolStudent sS, school s, city ci, state sa WHERE qR.qId = ' . $quizId . ' and qR.quizMode = ' .
            getValueInfo('regularQuiz') . ' and qR.online = 0 and u.id = qR.uId and sS.uId = u.id and s.uId = sS.sId and ci.id = s.cityId and sa.id = ci.stateId group by(sS.sId)');

        $totalOnline = DB::select('select count(*) as countNum from quizRegistry qR WHERE qR.qId = ' . $quizId . ' and qR.quizMode = ' .
            getValueInfo('regularQuiz') . ' and qR.online = 1');

        if($totalOnline == null || count($totalOnline) == 0 || empty($totalOnline[0]->countNum))
            $totalOnline = 0;
        else
            $totalOnline = $totalOnline[0]->countNum;

        $totalNonOnline = DB::select('select count(*) as countNum from quizRegistry qR WHERE qR.qId = ' . $quizId . ' and qR.quizMode = ' .
            getValueInfo('regularQuiz') . ' and qR.online = 0');

        if($totalNonOnline == null || count($totalNonOnline) == 0 || empty($totalNonOnline[0]->countNum))
            $totalNonOnline = 0;
        else
            $totalNonOnline = $totalNonOnline[0]->countNum;

        return view('Reports.partialQuizReport', array('online' => $online, 'nonOnline' => $nonOnline, 'totalOnline' => $totalOnline,
            'totalNonOnline' => $totalNonOnline, 'quiz' => $quiz));
    }

    public function doublePartialQuizReport($quizId, $sId, $online) {

        if(RegularQuiz::whereId($quizId) == null || ($sId != -1 && School::find($sId) == null))
            return Redirect::to(route('profile'));

        $schoolName = "نامشخص";
        $cityName = "نامشخص";
        $quizName = RegularQuiz::whereId($quizId)->name;

        if($sId != -1) {
            $school = School::find($sId);
            $schoolName = $school->name;
            $cityName = City::whereId($school->cityId)->name;

            $users = DB::select('select u.firstName, u.lastName, u.phoneNum, u.username from quizRegistry qR, users u, schoolStudent sS, school s WHERE qR.qId = ' . $quizId . ' and qR.quizMode = ' .
                getValueInfo('regularQuiz') . ' and qR.online = ' . $online . ' and u.id = qR.uId and sS.uId = u.id and s.uId = sS.sId and s.id = ' . $sId);
        }

        else {
            $users = DB::select('select u.firstName, u.lastName, u.phoneNum, u.username from quizRegistry qR, users u WHERE qR.qId = ' . $quizId . ' and qR.quizMode = ' .
                getValueInfo('regularQuiz') . ' and qR.online = ' . $online . ' and u.id = qR.uId and u.id not IN (select sS.uId from schoolStudent sS)');
        }


        return view('Reports.doublePartialQuizReport', array('online' => $online, 'schoolName' => $schoolName, 'users' => $users,
            'quizName' => $quizName, 'cityName' => $cityName, 'quizId' => $quizId, 'sId' => $sId));
    }

    public function doRemoveUser() {

        if(isset($_POST["uId"])) {
            User::destroy(makeValidInput($_POST["uId"]));
            echo "ok";
        }

    }

    public function quizDoublePartialReportExcel($quizId, $sId, $online) {

        if(RegularQuiz::whereId($quizId) == null || ($sId != -1 && School::find($sId) == null))
            return Redirect::to(route('profile'));

        if($sId != -1) {
            $users = DB::select('select u.firstName, u.lastName, u.phoneNum, u.username from quizRegistry qR, users u, schoolStudent sS, school s WHERE qR.qId = ' . $quizId . ' and qR.quizMode = ' .
                getValueInfo('regularQuiz') . ' and qR.online = ' . $online . ' and u.id = qR.uId and sS.uId = u.id and s.uId = sS.sId and s.id = ' . $sId);
        }

        else {
            $users = DB::select('select u.firstName, u.lastName, u.phoneNum, u.username from quizRegistry qR, users u WHERE qR.qId = ' . $quizId . ' and qR.quizMode = ' .
                getValueInfo('regularQuiz') . ' and qR.online = ' . $online . ' and u.id = qR.uId and u.id not IN (select sS.uId from schoolStudent sS)');
        }

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("Gachesefid");
        $objPHPExcel->getProperties()->setLastModifiedBy("Gachesefid");
        $objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.");

        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->getActiveSheet()->setCellValue('D1', 'شماره همراه');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'نام کاربری');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'نام خانوادگی');
        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'نام');

        $counter = 2;

        foreach ($users as $itr) {

            $objPHPExcel->getActiveSheet()->setCellValue('D' . ($counter), $itr->phoneNum);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . ($counter), $itr->username);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . ($counter), $itr->lastName);
            $objPHPExcel->getActiveSheet()->setCellValue('A' . ($counter), $itr->firstName);
            $counter++;
        }

        $fileName = __DIR__ . "/../../../public/registrations/doublePartialQuizReport.xlsx";

        $objPHPExcel->getActiveSheet()->setTitle('گزارش گیری جزئی آزمون');

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

        return Redirect::to(route('quizPartialReportExcel', ['quizId' => $quizId, 'sId' => $sId, 'online' => $online]));
    }

    public function quizPartialReportExcel ($quizId) {

        $quiz = RegularQuiz::whereId($quizId);

        if($quiz == null)
            return Redirect::to('quizReport');


        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("Gachesefid");
        $objPHPExcel->getProperties()->setLastModifiedBy("Gachesefid");
        $objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.");

        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'نوع ثبت نام');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'تعداد دانش آموزان');
        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'نام مدرسه');

        $online = DB::select('select s.name, count(*) as countNum, ci.name as cityName, sa.name as stateName from quizRegistry qR, users u, schoolStudent sS, school s, city ci, state sa WHERE qR.qId = ' . $quizId . ' and qR.quizMode = ' .
            getValueInfo('regularQuiz') . ' and qR.online = 1 and u.id = qR.uId and sS.uId = u.id and s.uId = sS.sId and ci.id = s.cityId and sa.id = ci.stateId group by(sS.sId)');
        $nonOnline = DB::select('select s.name, count(*) as countNum, ci.name as cityName, sa.name as stateName from quizRegistry qR, users u, schoolStudent sS, school s, city ci, state sa WHERE qR.qId = ' . $quizId . ' and qR.quizMode = ' .
            getValueInfo('regularQuiz') . ' and qR.online = 0 and u.id = qR.uId and sS.uId = u.id and s.uId = sS.sId and ci.id = s.cityId and sa.id = ci.stateId group by(sS.sId)');

        $totalOnline = DB::select('select count(*) as countNum from quizRegistry qR WHERE qR.qId = ' . $quizId . ' and qR.quizMode = ' .
            getValueInfo('regularQuiz') . ' and qR.online = 1');

        if($totalOnline == null || count($totalOnline) == 0 || empty($totalOnline[0]->countNum))
            $totalOnline = 0;
        else
            $totalOnline = $totalOnline[0]->countNum;

        $totalNonOnline = DB::select('select count(*) as countNum from quizRegistry qR WHERE qR.qId = ' . $quizId . ' and qR.quizMode = ' .
            getValueInfo('regularQuiz') . ' and qR.online = 0');

        if($totalNonOnline == null || count($totalNonOnline) == 0 || empty($totalNonOnline[0]->countNum))
            $totalNonOnline = 0;
        else
            $totalNonOnline = $totalNonOnline[0]->countNum;

        $counter = 2;

        foreach ($nonOnline as $itr) {

            $objPHPExcel->getActiveSheet()->setCellValue('E' . ($counter), $itr->stateName);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . ($counter), $itr->cityName);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . ($counter), 'حضوری');
            $objPHPExcel->getActiveSheet()->setCellValue('B' . ($counter), $itr->countNum);
            $objPHPExcel->getActiveSheet()->setCellValue('A' . ($counter), $itr->name);
            $counter++;
        }

        $objPHPExcel->getActiveSheet()->setCellValue('C' . ($counter), 'حضوری');
        $objPHPExcel->getActiveSheet()->setCellValue('B' . ($counter), $totalNonOnline);
        $objPHPExcel->getActiveSheet()->setCellValue('A' . ($counter++), 'نامشخص');

        foreach ($online as $itr) {

            $objPHPExcel->getActiveSheet()->setCellValue('E' . ($counter), $itr->stateName);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . ($counter), $itr->cityName);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . ($counter), 'آنلاین');
            $objPHPExcel->getActiveSheet()->setCellValue('B' . ($counter), $itr->countNum);
            $objPHPExcel->getActiveSheet()->setCellValue('A' . ($counter), $itr->name);
            $counter++;
        }

        $objPHPExcel->getActiveSheet()->setCellValue('C' . ($counter), 'آنلاین');
        $objPHPExcel->getActiveSheet()->setCellValue('B' . ($counter), $totalOnline);
        $objPHPExcel->getActiveSheet()->setCellValue('A' . ($counter), 'نامشخص');

        $fileName = __DIR__ . "/../../../public/registrations/subjectReport.xlsx";

        $objPHPExcel->getActiveSheet()->setTitle('گزارش گیری جزئی آزمون');

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

        return Redirect::to(route('partialQuizReport', ['quizId' => $quizId]));
    }

    private function getResultOfSpecificContainer($total, $corrects, $inCorrects) {

        $j = $k = 0;
        $correctsArr = $inCorrectsArr = $totalArr = array();

        for($i = 0; $i < count($total); $i++) {

            $totalArr[$i] = $total[$i]->total;

            if($j < count($corrects) && $total[$i]->target == $corrects[$j]->target)
                $correctsArr[$i] = $corrects[$j++]->corrects;
            else
                $correctsArr[$i] = 0;

            if($k < count($inCorrects) && $total[$i]->target == $inCorrects[$k]->target)
                $inCorrectsArr[$i] = $inCorrects[$k++]->inCorrects;
            else
                $inCorrectsArr[$i] = 0;
        }

        return [$inCorrectsArr, $correctsArr, $totalArr];
    }

    private function showGeneralKarname($uId, $quizId, $qEntryId, $backURL = "") {

        $status = QuizStatus::whereLevel(1)->get();
        $rank = calcRank($quizId, $uId);

        $user = User::whereId($uId);

        $rankInLesson = array();
        $rankInLessonCity = array();
        $rankInLessonState = array();

        $cityId = RedundantInfo1::whereUId($uId)->first();

        if($cityId == null)
            $cityId = City::first()->id;
        else
            $cityId = $cityId->cityId;

        $cityRank = calcRankInCity($quizId, $uId, $cityId);

        $stateId = State::whereId(City::whereId($cityId)->stateId)->id;
        $stateRank = calcRankInState($quizId, $uId, $stateId);

        $avgs = DB::select('select SUM(percent) / count(*) as avg, MAX(percent) as maxPercent, MIN(percent) as minPercent FROM taraz, quizRegistry WHERE quizRegistry.qId = ' . $quizId . ' and quizRegistry.id  = taraz.qEntryId GROUP by(taraz.lId)');


        $inCorrects =  DB::select('SELECT count(*) as inCorrects, subject.lessonId as target FROM ROQ, SOQ, question, subject WHERE ROQ.quizId = ' . $quizId . ' and ROQ.questionId = question.id and question.ans <> ROQ.result and ROQ.result <> 0 and ROQ.uId = ' . $uId . ' and subject.id = SOQ.sId and SOQ.qId = question.id group by(subject.lessonId)');
        $corrects =  DB::select('SELECT count(*) as corrects, subject.lessonId as target FROM ROQ, SOQ, question, subject WHERE ROQ.quizId = ' . $quizId . ' and ROQ.questionId = question.id and question.ans = ROQ.result and ROQ.uId = ' . $uId . ' and subject.id = SOQ.sId and SOQ.qId = question.id group by(subject.lessonId)');
        $total = DB::select('SELECT count(*) as total, subject.lessonId as target FROM ROQ, SOQ, question, subject WHERE ROQ.quizId = ' . $quizId . ' and ROQ.questionId = question.id and ROQ.uId = ' . $uId . ' and subject.id = SOQ.sId and SOQ.qId = question.id group by(subject.lessonId)');

        $roq = $this->getResultOfSpecificContainer($total, $corrects, $inCorrects);

        $lessons = getLessonQuiz($quizId);

        $taraz = Taraz::whereQEntryId($qEntryId->id)->get();

        if($taraz == null || count($taraz) == 0) {

            if(empty($backURL))
                return view('A3', array('quizId' => $quizId, 'msg' => 'err'));
            else
                return view('A5', array('quizId' => $quizId, 'msg' => 'err'));
        }

        $counter = 0;
        foreach ($lessons as $lesson) {
            $tmp = DB::select('SELECT quizRegistry.uId, taraz.taraz from quizRegistry, taraz WHERE quizRegistry.id = taraz.qEntryId and quizRegistry.qId = ' . $quizId . ' and taraz.lId = ' . $lesson->id . ' ORDER by taraz.taraz DESC');
            $rankInLesson[$counter++] = $this->getRank($tmp, $uId);
        }

        $counter = 0;
        foreach ($lessons as $lesson) {
            $tmp = DB::select('SELECT quizRegistry.uId, taraz.taraz from redundantInfo1 rd, city ci, quizRegistry, taraz WHERE rd.uId = quizRegistry.uId and rd.cityId = ci.id and ci.stateId = ' . $stateId . ' and quizRegistry.id = taraz.qEntryId and quizRegistry.qId = ' . $quizId . ' and taraz.lId = ' . $lesson->id . ' ORDER by taraz.taraz DESC');
            $rankInLessonState[$counter++] = $this->getRank($tmp, $uId);
        }

        $counter = 0;
        foreach ($lessons as $lesson) {
            $tmp = DB::select('SELECT quizRegistry.uId, taraz.taraz from redundantInfo1 rd, quizRegistry, taraz WHERE rd.uId = quizRegistry.uId and rd.cityId = ' . $cityId . ' and quizRegistry.id = taraz.qEntryId and quizRegistry.qId = ' . $quizId . ' and taraz.lId = ' . $lesson->id . ' ORDER by taraz.taraz DESC');
            $rankInLessonCity[$counter++] = $this->getRank($tmp, $uId);
        }

        $totalMark = 20;
        
        return view('A3', array('quizId' => $quizId, 'status' => $status, 'backURL' => $backURL, 'name' => $user->firstName . ' ' . $user->lastName,
            'rank' => $rank, 'rankInLessonCity' => $rankInLessonCity, 'rankInLesson' => $rankInLesson, 'uId' => $uId,
            'lessons' => $lessons, 'taraz' => $taraz, 'rankInLessonState' => $rankInLessonState, 'stateRank' => $stateRank,
            'avgs' => $avgs, 'roq' => $roq, 'cityRank' => $cityRank, "totalMark" => $totalMark));
    }

    public function chooseQuiz() {

        $user = Auth::user();

        if($user->level == getValueInfo('adviserLevel'))
            $quizes = DB::select('select DISTINCT(regularQuiz.id), regularQuiz.name  from quizRegistry, studentsAdviser, regularQuiz WHERE studentsAdviser.adviserId = ' . $user->id .
                ' and quizRegistry.uId = studentsAdviser.studentId and quizRegistry.quizMode = ' . getValueInfo('regularQuiz') .
                ' and regularQuiz.id = quizRegistry.qId');

        else if($user->level == getValueInfo('namayandeLevel'))
            $quizes = DB::select('select DISTINCT(regularQuiz.id), regularQuiz.name  from quizRegistry, namayandeSchool nS, schoolStudent sS, regularQuiz WHERE nS.sId = sS.sId and nS.nId = ' . $user->id .
                ' and quizRegistry.uId = sS.uId and quizRegistry.quizMode = ' . getValueInfo('regularQuiz') .
                ' and regularQuiz.id = quizRegistry.qId');

        else if($user->level == getValueInfo('schoolLevel'))
            $quizes = DB::select('select DISTINCT(regularQuiz.id), regularQuiz.name  from quizRegistry, schoolStudent sS, regularQuiz WHERE sS.sId = ' . $user->id .
                ' and quizRegistry.uId = sS.uId and quizRegistry.quizMode = ' . getValueInfo('regularQuiz') .
                ' and regularQuiz.id = quizRegistry.qId');
        else {
            $quizes = RegularQuiz::all();
        }

        return view('chooseQuiz', array('quizes' => $quizes));

    }

    private function checkLessonCoherence() {
        DB::raw('update lesson set coherence = 1 WHERE coherence <= 0');
    }

    public function getQuizReport($quizId) {

        $this->checkLessonCoherence();

        $user = Auth::user();

        if($user->level == getValueInfo('adviserLevel'))
            $quizes = DB::select('select count(*) as countNum  from quizRegistry, studentsAdviser WHERE studentsAdviser.adviserId = ' . $user->id .
                ' and quizRegistry.uId = studentsAdviser.studentId and quizRegistry.quizMode = ' . getValueInfo('regularQuiz') .
                ' and quizRegistry.qId = ' . $quizId);

        else if($user->level == getValueInfo('namayandeLevel'))
            $quizes = DB::select('select count(*) as countNum from namayandeSchool nS, quizRegistry, schoolStudent sS WHERE nS.nId = ' . $user->id . ' and nS.sId = sS.sId' .
                ' and quizRegistry.uId = sS.uId and quizRegistry.quizMode = ' . getValueInfo('regularQuiz') .
                ' and quizRegistry.qId = ' . $quizId);

        else if($user->level == getValueInfo('schoolLevel'))
            $quizes = DB::select('select count(*) as countNum  from quizRegistry, schoolStudent sS WHERE sS.sId = ' . $user->id .
                ' and quizRegistry.uId = sS.uId and quizRegistry.quizMode = ' . getValueInfo('regularQuiz') .
                ' and quizRegistry.qId = ' . $quizId);

        else {
            $quizes = DB::select('select count(*) as countNum  from quizRegistry WHERE ' .
                ' quizRegistry.quizMode = ' . getValueInfo('regularQuiz') .
                ' and quizRegistry.qId = ' . $quizId);
        }

        if($quizes == null || count($quizes) == 0 || empty($quizes[0]->countNum) || $quizes[0]->countNum == 0)
            return Redirect::to(route('quizReports'));

        return view('chooseReport', array('reports' => ReportsAccess::where('status', '=', 1)->get(),
            'quizId' => $quizId));

    }

    public function A2($quizId) {

        $user = Auth::user();
        $regularQuizMode = getValueInfo('regularQuiz');

        if($user->level == getValueInfo('adviserLevel')) {
            $cities = $this->getCitiesInQuiz($quizId, $user->id, $user->level);

            foreach ($cities as $city) {
                $city->lessons = DB::select('select AVG(percent) as avgPercent, lesson.name from quizRegistry qR, redundantInfo1, taraz, lesson, studentsAdviser WHERE lesson.id = lId and qR.uId = redundantInfo1.uId and ' .
                    'qId = ' . $quizId . ' and quizMode = ' . $regularQuizMode . ' and adviserId = ' . $user->id . ' and studentId = quizRegistry.uId and cityId = ' . $city->id . ' and qEntryId = qR.id ' .
                    " and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0" .
                    ' group by(lId)');
            }
        }
        else if($user->level == getValueInfo('namayandeLevel')) {
            $cities = $this->getCitiesInQuiz($quizId, $user->id, $user->level);

            foreach ($cities as $city) {
                $city->lessons = DB::select('select AVG(percent) as avgPercent, lesson.name from quizRegistry qR, redundantInfo1, taraz, lesson, namayandeSchool nS, schoolStudent sS WHERE lesson.id = lId and qR.uId = redundantInfo1.uId and ' .
                    'qId = ' . $quizId . ' and quizMode = ' . $regularQuizMode . ' and nS.sId = sS.sId and nS.nId = ' . $user->id . ' and sS.uId = qR.uId and cityId = ' . $city->id . ' and qEntryId = qR.id ' .
                    " and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0" .
                    ' group by(lId)');
            }
        }
        else if($user->level == getValueInfo('schoolLevel')) {
            $cities = $this->getCitiesInQuiz($quizId, $user->id, $user->level);

            foreach ($cities as $city) {
                $city->lessons = DB::select('select AVG(percent) as avgPercent, lesson.name from quizRegistry qR, redundantInfo1, taraz, lesson, schoolStudent sS WHERE lesson.id = lId and qR.uId = redundantInfo1.uId and ' .
                    'qId = ' . $quizId . ' and quizMode = ' . $regularQuizMode . ' and sS.sId = ' . $user->id . ' and sS.uId = qR.uId and cityId = ' . $city->id . ' and qEntryId = qR.id ' .
                    " and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0" .
                    ' group by(lId)');
            }
        }
        else {
            $cities = $this->getCitiesInQuiz($quizId, -1);

            foreach ($cities as $city) {
                $city->lessons = DB::select('select AVG(percent) as avgPercent, lesson.name from quizRegistry qR, redundantInfo1, taraz, lesson WHERE lesson.id = lId and qR.uId = redundantInfo1.uId and ' .
                    'qId = ' . $quizId . ' and quizMode = ' . $regularQuizMode . ' and cityId = ' . $city->id . ' and qEntryId = qR.id ' .
                    " and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0" .
                    ' group by(lId)');
            }
        }

        return view('A2', array('cities' => $cities, 'quizId' => $quizId));
    }
    
    public function A2Excel($quizId) {

        $user = Auth::user();
        $regularQuizMode = getValueInfo('regularQuiz');

        if($user->level == getValueInfo('adviserLevel')) {
            $cities = $this->getCitiesInQuiz($quizId, $user->id, $user->level);

            foreach ($cities as $city) {
                $city->lessons = DB::select('select AVG(percent) as avgPercent, lesson.name from quizRegistry qR, redundantInfo1, taraz, lesson, studentsAdviser WHERE lesson.id = lId and qR.uId = redundantInfo1.uId and ' .
                    'qId = ' . $quizId . ' and quizMode = ' . $regularQuizMode . ' and adviserId = ' . $user->id . ' and studentId = quizRegistry.uId and cityId = ' . $city->id . ' and qEntryId = qR.id ' .
                    " and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0" .
                    ' group by(lId)');
            }
        }
        else if($user->level == getValueInfo('namayandeLevel')) {
            $cities = $this->getCitiesInQuiz($quizId, $user->id, $user->level);

            foreach ($cities as $city) {
                $city->lessons = DB::select('select AVG(percent) as avgPercent, lesson.name from quizRegistry qR, redundantInfo1, taraz, lesson, namayandeSchool nS, schoolStudent sS WHERE lesson.id = lId and qR.uId = redundantInfo1.uId and ' .
                    'qId = ' . $quizId . ' and quizMode = ' . $regularQuizMode . ' and nS.sId = sS.sId and nS.nId = ' . $user->id . ' and sS.uId = qR.uId and cityId = ' . $city->id . ' and qEntryId = qR.id ' .
                    " and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0" .
                    ' group by(lId)');
            }
        }
        else if($user->level == getValueInfo('schoolLevel')) {
            $cities = $this->getCitiesInQuiz($quizId, $user->id, $user->level);

            foreach ($cities as $city) {
                $city->lessons = DB::select('select AVG(percent) as avgPercent, lesson.name from quizRegistry qR, redundantInfo1, taraz, lesson, schoolStudent sS WHERE lesson.id = lId and qR.uId = redundantInfo1.uId and ' .
                    'qId = ' . $quizId . ' and quizMode = ' . $regularQuizMode . ' and sS.sId = ' . $user->id . ' and sS.uId = qR.uId and cityId = ' . $city->id . ' and qEntryId = qR.id ' .
                    " and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0" .
                    ' group by(lId)');
            }
        }
        else {
            $cities = $this->getCitiesInQuiz($quizId, -1);

            foreach ($cities as $city) {
                $city->lessons = DB::select('select AVG(percent) as avgPercent, lesson.name from quizRegistry qR, redundantInfo1, taraz, lesson WHERE lesson.id = lId and qR.uId = redundantInfo1.uId and ' .
                    'qId = ' . $quizId . ' and quizMode = ' . $regularQuizMode . ' and cityId = ' . $city->id . ' and qEntryId = qR.id ' .
                    " and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0" .
                    ' group by(lId)');
            }
        }


        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("Gachesefid");
        $objPHPExcel->getProperties()->setLastModifiedBy("Gachesefid");
        $objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.");

        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'تعداد حاضرین');
        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'شهر');


        if(count($cities) > 0) {

            $j = 'C';
            foreach ($cities[0]->lessons as $itr)
                $objPHPExcel->getActiveSheet()->setCellValue(($j++) . '1', $itr->name);
        }

        $i = 0;

        foreach($cities as $city) {
            $objPHPExcel->getActiveSheet()->setCellValue('A' . ($i + 2), $city->name);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . ($i + 2), $city->total);
            $j = 'C';
            foreach($cities[$i]->lessons as $itr) {
                $objPHPExcel->getActiveSheet()->setCellValue(($j++) . ($i + 2), round($itr->avgPercent, 0));
            }
            $i++;
        }

        $fileName = __DIR__ . "/../../../public/registrations/A2.xlsx";

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

        return view('A2', array('cities' => $cities, 'quizId' => $quizId));
    }

    public function getCitiesInQuiz($quizId, $uId, $level = -1) {

        $regularQuizMode = getValueInfo('regularQuiz');

        if($uId == -1)
            return DB::select('select cityId as id, city.name, count(*) as total from quizRegistry qR, redundantInfo1, city WHERE qR.uId = redundantInfo1.uId and ' .
              'qR.qId = ' . $quizId . ' and qR.quizMode = ' . $regularQuizMode . ' and city.id = cityId and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0 group by(cityId)');

        if($level == getValueInfo('adviserLevel'))
            return DB::select('select cityId as id, city.name, count(*) as total from quizRegistry qR, redundantInfo1, studentsAdviser, city WHERE qR.uId = redundantInfo1.uId and ' .
                'qR.qId = ' . $quizId . ' and qR.quizMode = ' . $regularQuizMode . ' and adviserId = ' . $uId . ' and studentId = qR.uId and city.id = cityId and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0 group by(cityId)');

        else if($level == getValueInfo('namayandeLevel'))
            return DB::select('select cityId as id, city.name, count(*) as total from quizRegistry qR, redundantInfo1, namayandeSchool nS, schoolStudent sS, city WHERE nS.sId = sS.sId and qR.uId = redundantInfo1.uId and ' .
                'qId = ' . $quizId . ' and quizMode = ' . $regularQuizMode . ' and nS.nId = ' . $uId . ' and sS.uId = qR.uId and city.id = cityId ' .
                " and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0" .
                ' group by(cityId)');

        return DB::select('select cityId as id, city.name, count(*) as total from quizRegistry qR, redundantInfo1, schoolStudent sS, city WHERE qR.uId = redundantInfo1.uId and ' .
            'qR.qId = ' . $quizId . ' and qR.quizMode = ' . $regularQuizMode . ' and sS.sId = ' . $uId . ' and sS.uId = qR.uId and city.id = cityId and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0 group by(cityId)');

    }

}