<?php

namespace App\Http\Controllers;

use App\models\AdviserFields;
use App\models\AdviserInfo;
use App\models\ComposeQuiz;
use App\models\Question;
use App\models\QuizRegistry;
use App\models\RegularQOQ;
use App\models\ROQ2;
use App\models\School;
use App\models\StudentAdviser;
use App\models\Transaction;
use App\models\User;
use App\models\SlideBar;
use App\models\RegularQuiz;
use App\models\RedundantInfo1;
use App\models\SchoolStudent;
use App\models\RedundantInfo2;
use App\models\State;
use App\models\City;
use App\models\Grade;
use App\models\SystemQuiz;
use App\models\PointConfig;
use App\models\Activation;
use DOMDocument;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

class HomeController extends Controller {

	public function aboutUs() {
		return View('aboutUs');
	}

    public function showHome() {

//		$sliders = SlideBar::all();
//		foreach ($sliders as $slider) {
//			$slider->pic = URL::asset('images/slideBar/' . $slider->pic);
//		}

//		if(Auth::check() &&
//			(Auth::user()->level == getValueInfo('adminLevel') || Auth::user()->level == getValueInfo('superAdminLevel'))) {
//
//			if (Cache::has("name2"))
//				dd(Cache::get("name"));
//			else
//				Cache::remember("name2", 60 * 60 * 24, function () {
//					return "ali";
//				});
//		}


		return view('home', ['qNos' => Question::accepted()->count(),
			'usersNo' => User::students()->count(), 'schoolsNo' => School::count(), 'composeNo' => ComposeQuiz::count(),
			'quizNo' => RegularQuiz::count(), 'adviserNos' => User::advisers()->whereStatus(1)->count()]);

		
////
//		return view('home', ['qNos' => 2633,
//			'usersNo' => 3903, 'schoolsNo' => 228, 'composeNo' => 12,
//			'quizNo' => 37, 'adviserNos' => 7]);
	}

	public function login() {

        $this->middleware('auth');
//		return view('login');
	}

	public function logout() {
		Auth::logout();
		return Redirect::route("login");
	}

	public function checkAuth() {

		if(isset($_POST["username"]) && isset($_POST["password"])) {

			$username = makeValidInput($_POST['username']);
			$password = makeValidInput($_POST['password']);

			if(User::whereUsername($username)->count() == 0) {
				echo "false1";
				return;
			}

			if(User::whereUsername($username)->first()->status != 1) {
				echo "false3";
				return;
			}

			if(Auth::attempt(array('username' => $username, 'password' => $password))) {
				if(Auth::user()->level == getValueInfo('studentLevel'))
					echo "false2";
				else
					echo "true";
				return;
			}
		}
		echo "false3";
    }

	private function checkRequiresForAuth() {

		if(Auth::user()->status != 1) {
			$msg = "حساب کاربری شما هنوز فعال نشده است";
			Auth::logout();
		}
		else {
			if(Auth::user()->phoneNum == "")
				return Redirect::to('userInfo');

			return redirect::route('profile');
		}

		return view('login', array('msg' => $msg));
	}

	public function doLogin() {

		$username = makeValidInput(Input::get('username'));
		$password = makeValidInput(Input::get('password'));

		if(Auth::attempt(['username' => $username, 'password' => $password], true)) {
			return $this->checkRequiresForAuth();
		}
		elseif(Auth::attempt(['phoneNum' => $username, 'password' => $password], true)) {
			return $this->checkRequiresForAuth();
		}
		elseif(Auth::attempt(['NID' => $username, 'password' => $password], true)) {
			return $this->checkRequiresForAuth();
		}
		else {
			$msg = 'نام کاربری و یا رمزعبور اشتباه است';
		}

		return view('login', array('msg' => $msg));
	}

	public function resetPas($msg = "") {
		return view('resetPas', array('msg' => $msg));
	}

	public function doResetPas() {

		if(isset($_POST["username"]) && isset($_POST["val"]) && isset($_POST["mode"])) {

			$username = makeValidInput($_POST["username"]);
			$val = makeValidInput($_POST["val"]);
			$mode = makeValidInput($_POST["mode"]); // 1 : email - 2 : phone
			$user = User::whereUsername($username)->first();
			if ($user == null) {
				$user = User::whereNID($username)->first();
				if ($user == null) {
					echo "نام کاربری یا کد ملی وارد شده معتبر نمی باشد";
					return;
				}
			}

			if ($mode == 1) {
				if (RedundantInfo1::whereUId($user->id)->first()->email != $val) {
					echo "ایمیل وارد شده صحیح نمی باشد";
					return;
				}
				else {
					$newPas = generateActivationCode();
					$user->password = Hash::make($newPas);
					$user->save();

					Mail::send('newPasswordGenerated', array("newPas" => $newPas), function ($message) use($val) {
						$message->to($val, $val)->subject('بازیابی رمزعبور');
					});
				}
			} else {
				if ($user->phoneNum != $val) {
					echo "شماره ی وارد شده صحیح نمی باشد";
					return;
				}
				else {

					$newPas = generateActivationCode();
					$user->password = Hash::make($newPas);
					$user->save();

					sendSMS($val, $newPas, 'resetPas');
				}
			}
			echo "ok";
			return;
		}
		echo "خطایی در انجام عملیات مورد نظر رخ داده است";
	}

	public function showRSSIrysc() {

		$xml = ("http://www.irysc.com/rss");

		$xmlDoc = new DOMDocument();
		$xmlDoc->load($xml);

		$x = $xmlDoc->getElementsByTagName('item');
		$limit = ($x->length > 7) ? 7 : $x->length;

		for ($i = 0; $i < $limit; $i++) {
			$item_title = $x->item($i)->getElementsByTagName('title')
				->item(0)->childNodes->item(0)->nodeValue;
			$item_link=$x->item($i)->getElementsByTagName('link')
				->item(0)->childNodes->item(0)->nodeValue;
			echo ("<li><a href='" . $item_link
				. "'>" . $item_title . "</a>");
			echo ("<br>");
		}
	}

	public function showRSSGach() {

		$xml = ("http://www.news.gachesefid.com/feed/");

		$xmlDoc = new DOMDocument();
		$xmlDoc->load($xml);

		$x = $xmlDoc->getElementsByTagName('item');
		$limit = ($x->length > 7) ? 7 : $x->length;
		for ($i = 0; $i < $limit; $i++) {
			$item_title = $x->item($i)->getElementsByTagName('title')
				->item(0)->childNodes->item(0)->nodeValue;
			$item_link=$x->item($i)->getElementsByTagName('link')
				->item(0)->childNodes->item(0)->nodeValue;
			echo ("<li><a href='" . $item_link
				. "'>" . $item_title . "</a>");
			echo ("<br>");
		}
	}

	public function chargeAccount($status = "nop") {
		include_once 'MoneyController.php';
		return view('chargeAccount', array('status' => $status, 'total' => getTotalMoney()));
	}

	public function chargeWithGiftCard() {

		if(isset($_POST["giftCode"])) {

			include_once 'MoneyController.php';
			$code = makeValidInput($_POST["giftCode"]);
			if(checkOffCodeValidation($code)) {
				echo "ok";
				return;
			}
		}
		echo "nok1";
	}

	public function testMyFunc() {

		$start = microtime(true);
//		echo time();
//		$questions = DB::select('select choicesCount, question.id, question.questionFile, question.kindQ, question.neededTime as qoqId from question, regularQOQ WHERE questionId = question.id and quizId = ' . 171 . ' order by regularQOQ.qNo ASC');
//		var_dump($questions);
//		DB::select('call getQuizQuestions("' . 171 . '")');


		$today = getToday();
//		$date = $today["date"];
//		$time = $today["time"];
		$uId = 4512;
		$quizId = 171;
		$date = "13970923";
		$time = "1400";
		$currTime = "123";

		$quiz = DB::select('select rQ.*, qR.id as qRID, qR.timeEntry from quizRegistry qR, regularQuiz rQ WHERE rQ.id = ' . $quizId .
			' and qR.qId = ' . $quizId . ' and qR.uId = ' . $uId .
			' and qR.quizMode = ' .getValueInfo('regularQuiz')
		);

		if($quiz == null || count($quiz) == 0)
			return Redirect::to('profile');

		$quiz = $quiz[0];

		if(!(($quiz->startDate < $date && $quiz->endDate > $date) ||
			($quiz->startDate < $date && $quiz->endDate >= $date && $quiz->endTime > $time) ||
			($quiz->startDate == $date && $quiz->startTime <= $time && (
					($quiz->startDate == $quiz->endDate && $quiz->endTime > $time) ||
					($quiz->startDate != $quiz->endDate) ||
					($quiz->endDate == $date && $quiz->endTime > $time)
				)
			)))
			return Redirect::to(route('showQuizWithOutTime', ['quizId' => $quizId, 'quizMode' => getValueInfo('regularQuiz')]));

		$timeLen = calcTimeLenQuiz($quiz->id, 'regular');

		if($quiz->timeEntry == "") {
			$timeEntry = time();
			$quizRegistry = QuizRegistry::whereId($quiz->qRID);
			$quizRegistry->timeEntry = $timeEntry;
			$quizRegistry->save();
		}
		else {
//            return Redirect::to(route('showQuizWithOutTime', ['quizId' => $quizId, 'quizMode' => getValueInfo('regularQuiz')]));
			$timeEntry = $quiz->timeEntry;
		}

		$reminder = $timeLen * 60 - $currTime + $timeEntry;

		if($reminder <= 0)
			return Redirect::to(route('showQuizWithOutTime', ['quizId' => $quizId, 'quizMode' => getValueInfo('regularQuiz')]));

//        $roqs = DB::select('select ROQ2.result, ROQ2.id from ROQ2, question where quizId = ' . $quizId . " and uId = " . $uId . " and
//                quizMode = " . getValueInfo('regularQuiz') . " and question.id = ROQ2.questionId");

		$roqs = ROQ2::whereUId($uId)->whereQuizId($quizId)->first();

//        if($roqs == null || count($roqs) == 0) {
//            $this->fillRegularROQ($quizId);
//
//            $roqs = DB::select('select ROQ2.result, ROQ2.id from ROQ2, question where quizId = ' . $quizId . " and uId = " . $uId . " and
//                quizMode = " . getValueInfo('regularQuiz') . " and question.id = ROQ2.questionId");
//        }

		if($roqs == null) {

			$quizQuestionsNo = RegularQOQ::whereQuizId($quizId)->count();

			$tmpResult = "";
			$roqs = [];

			for ($i = 0; $i < $quizQuestionsNo; $i++) {
				$tmpResult .= "0";
				$roqs[$i] = 0;
			}

			$tmpROQ2 = new ROQ2();
			$tmpROQ2->uId = $uId;
			$tmpROQ2->quizId = $quizId;
			$tmpROQ2->result = $tmpResult;
			$tmpROQ2->save();
		}
		else {
			$tmpROQ2 = [];
			$tmpResult = $roqs->result;
			for ($i = 0; $i < strlen($tmpResult); $i++) {
				$tmpROQ2[$i] = $tmpResult[$i];
			}

			$roqs = $tmpROQ2;
		}

		$questions = DB::select('select choicesCount, question.id, question.questionFile, question.kindQ, question.neededTime as qoqId from question, regularQOQ WHERE questionId = question.id and quizId = ' . $quizId . ' order by regularQOQ.qNo ASC');

		dd((microtime(true) - $start) * 1000);
		
	}
	
	public function profile() {

		if(Auth::user()->level == getValueInfo('studentLevel')) {

			$user = Auth::user();
			$today = getToday();

			$tmp1 = DB::select('select count(*) as countNum from systemQuiz sQ WHERE endReg >= '. $today["date"] .
					' and not exists(select * from quizRegistry qR where qR.uId = ' . $user->id . ' and qR.qId = sQ.id and qR.quizMode = ' . getValueInfo('systemQuiz') . ')');

			if($tmp1 == null || count($tmp1) == 0 || $tmp1[0]->countNum == 0)
				$tmp1 = 0;
			else
				$tmp1 = $tmp1[0]->countNum;

			$tmp2 = DB::select('select count(*) as countNum from regularQuiz rQ WHERE endReg >= '. $today["date"] .
				' and not exists(select * from quizRegistry qR where qR.uId = ' . $user->id . ' and qR.qId = rQ.id and qR.quizMode = ' . getValueInfo('regularQuiz') . ')');

			if($tmp2 == null || count($tmp2) == 0 || $tmp2[0]->countNum == 0)
				$tmp2 = 0;
			else
				$tmp2 = $tmp2[0]->countNum;

			$totalQuizes = $tmp1 + $tmp2;

			$regularCondition = ['uId' => $user->id, 'quizMode' => getValueInfo('regularQuiz')];
			$systemCondition = ['uId' => $user->id, 'quizMode' => getValueInfo('systemQuiz')];

			$tmp = QuizRegistry::where($regularCondition)->count() + QuizRegistry::where($systemCondition)->count();

			$amount = DB::select('select sum(q.level) * 5 as totalSum from ROQ, question q'.
				' where uId = ' . $user->id . ' and q.id = questionId and q.ans = result');

			if($amount == null || count($amount) == 0 || $amount[0]->totalSum == 0) {
				$rate = -1;
				$amount = -1;
			}

			else {
				$amount = $amount[0]->totalSum;
				$rate = count(DB::select('select sum(q.level) * 5 as totalSum from users, ROQ, question q' .
					' where users.id = uId and q.id = questionId and q.ans = result and users.level = ' . getValueInfo('studentLevel') .
					' group by(uId) having totalSum > ' . $amount));
			}

			$name =  $user->firstName . " " . $user->lastName;
			$school = SchoolStudent::whereUId($user->id)->first();
			if($school == null)
				$school = "نامشخص";
			else
				$school = School::whereUId($school->sId)->first()->name;

			return view('profile', array('money' => Auth::user()->money,
				'myQuizNo' => $tmp, 'name' => $name,
				'nextQuizNo' => $totalQuizes, 'school' => $school,
				'rate' => $amount, 'rank' => $rate,
				'questionNo' => 0));

//			UserCreatedQuiz->totalQuestions($uId)
		}

		return view('profile');
	}

	public function doEditAdviserInfo() {

		if (isset($_POST["editInfo"])) {

			$username = makeValidInput($_POST["username"]);
			$firstName = makeValidInput($_POST["firstName"]);
			$lastName = makeValidInput($_POST["lastName"]);
			$phoneNum = makeValidInput($_POST["phoneNum"]);
			$honors = makeValidInput($_POST["honors"]);
			$essay = makeValidInput($_POST["essay"]);
			$schools = makeValidInput($_POST["schools"]);
			$workYears = makeValidInput($_POST["workYears"]);
			$lastCertificate = makeValidInput($_POST["lastCertificate"]);
			$grades = $_POST["grades"];
			$field = makeValidInput($_POST["field"]);
			$cityId = makeValidInput($_POST["cityId"]);
			$birthDay = makeValidInput($_POST["birthDay"]);

			if($username != Auth::user()->username) {
				if (User::whereUsername($username)->count() > 0 ||
					User::wherePhoneNum($username)->count() > 0 ||
					User::whereNID($username)->count() > 0
				) {
					return Redirect::route('editAdviserInfo', ['msg' => "err"]);
				}
			}

			else {

				$user = Auth::user();

				$user->username = $username;
				$user->firstName = $firstName;
				$user->lastName = $lastName;
				$user->phoneNum = $phoneNum;

				$user->save();

				$adviserInfo = AdviserInfo::whereUID($user->id)->first();
				$adviserInfo->cityId = $cityId;
				$adviserInfo->field = $field;
				$adviserInfo->lastCertificate = $lastCertificate;
				$adviserInfo->honors = $honors;
				$adviserInfo->essay = $essay;
				$adviserInfo->schools = $schools;
				$adviserInfo->workYears = $workYears;
				$adviserInfo->birthDay = $birthDay;

				try {
					$adviserInfo->save();
					AdviserFields::whereUID($user->id)->delete();

					foreach ($grades as $grade) {
						$adviserFields = new AdviserFields();
						$adviserFields->uId = $user->id;
						$adviserFields->gradeId = makeValidInput($grade);
						$adviserFields->save();
					}
				}
				catch (Exception $x) {}
			}

		}

		return Redirect::route('editAdviserInfo');
	}

	public function adviserQueue() {
		$uId = Auth::user()->id;
		$students = StudentAdviser::whereAdviserId($uId)->whereStatus(false)->get();
		foreach ($students as $student) {
			$tmp = RedundantInfo1::whereUId($student->studentId)->first();

			if($tmp == null) {
				$student->city = 'نامشخص';
				$student->grade = 'نامشخص';
			}
			else {
				$student->city = City::whereId($tmp->cityId)->name;
				$student->grade = Grade::whereId($tmp->gradeId)->name;
			}

			$student->user = User::whereId($student->studentId);
		}
		return view('adviserQueue', ['students' => $students]);
	}

	public function rejectStudent() {

		if(isset($_POST["uId"])) {

			$userId = makeValidInput($_POST["uId"]);
			$uId = Auth::user()->id;

			$user = StudentAdviser::whereAdviserId($uId)->whereStudentId($userId)->first();
			if($user != null) {
				$user->delete();
				echo "ok";
			}
		}
	}

	public function acceptStudent() {

		if(isset($_POST["uId"])) {

			$userId = makeValidInput($_POST["uId"]);
			$uId = Auth::user()->id;

			$user = StudentAdviser::whereAdviserId($uId)->whereStudentId($userId)->first();
			if($user != null) {
				$user->status = true;
				$user->save();
				echo "ok";
			}
		}

	}

	public function editAdviserInfo($msg = "") {

		$user = Auth::user();
		$adviserFields = AdviserFields::whereUID($user->id)->select('gradeId')->get();

		$adviserInfo = AdviserInfo::whereUID($user->id)->first();

		if($adviserInfo == null)
			return Redirect::route('home');

		$city = City::whereId($adviserInfo->cityId);

		return view('editAdviserInfo', ['adviserFields' => $adviserFields, 'adviserInfo' => $adviserInfo, 'user' => $user,
			'city' => $city, 'msg' => $msg]);
	}

	public function userInfo($msg = "", $mode = "", $reminder = "", $phoneNum = "") {

		if(Auth::user()->level == getValueInfo('adviserLevel'))
			return $this->editAdviserInfo();
		
		$uId = Auth::user()->id;
		$stateId = -1;

		if(RedundantInfo1::whereUId($uId)->count() > 0)
			$stateId = City::whereId(RedundantInfo1::whereUId($uId)->first()->cityId)->stateId;

		$namayande = SchoolStudent::whereSId($uId)->first();
		if($namayande == null)
			$namayande = "";
		else
			$namayande = User::whereId($namayande->uId)->invitationCode;

		return view('userInfo', array('user' => User::whereId($uId),
			'redundant1' => RedundantInfo1::whereUId($uId)->first(),
			'redundant2' => RedundantInfo2::whereUId($uId)->first(),
			'states' => State::all(), 'stateId' => $stateId, 'reminder' => $reminder,
			'selectedPart' => 'necessary', 'namayande' => $namayande, 'phoneNum' => $phoneNum,
			'grades' => Grade::orderBy('id', 'ASC')->get(), 'msg' => $msg, 'mode' => $mode));
	}

	public function userInfo2($selectedPart = "necessary") {

		$uId = Auth::user()->id;
		$stateId = -1;

		if(RedundantInfo1::whereUId($uId)->count() > 0) {
			$stateId = City::whereId(RedundantInfo1::whereUId($uId)->first()->cityId)->stateId;
		}

		$namayande = SchoolStudent::whereSId($uId)->first();
		if($namayande == null)
			$namayande = "";
		else {
			$namayande = User::whereId($namayande->uId)->invitationCode;
		}

		return view('userInfo', array('user' => User::whereId($uId),
			'redundant1' => RedundantInfo1::whereUId($uId)->first(),
			'redundant2' => RedundantInfo2::whereUId($uId)->first(),
			'states' => State::all(), 'stateId' => $stateId, 'reminder' => '',
			'selectedPart' => $selectedPart, 'namayande' => $namayande,
			'grades' => Grade::all(), 'msg' => 'برای ورود به آزمون باید این قسمت را تکمیل نمایید', 'mode' => 'editRedundant1'));
	}

	public function editInfo() {

		if (isset($_POST["activeProfile"])) {

			$activationCode = makeValidInput($_POST["activationCode"]);

			$user = Auth::user();
			$phoneNum = makeValidInput($_POST["phoneNum"]);
			$activation = Activation::wherePhoneNum( $phoneNum)->first();
			if($activation == null || $activation->code != $activationCode)
				return $this->userInfo('pendingErr', 'editInfo', 300 - time() + $activation->sendTime, $phoneNum);

			$user->phoneNum = $phoneNum;

			try {
				$user->save();
				$activation->delete();
				return Redirect::to('userInfo');
			}
			catch (Exception $x) {
				return $this->userInfo('pendingErr', 'editInfo', 300 - time() + $activation->sendTime, $phoneNum);
			}
		}

		else if(isset($_POST["resendActivation"])) {

			$phoneNum = makeValidInput($_POST["phoneNum"]);

			$activation = Activation::wherePhoneNum( $phoneNum)->first();
			if($activation == null)
				return $this->userInfo('pendingErrTime', 'editInfo', 300, $phoneNum);

			if(time() - $activation->startTime < 300)
				return $this->userInfo('pendingErrTime', 'editInfo', 300 - time() + $activation->startTime, $phoneNum);

			$activation->code = generateActivationCode();
			$activation->startTime = time();
			$activation->save();

			return $this->userInfo('pending', 'editInfo', 300);

		}

		else if(isset($_POST['firstName']) && isset($_POST['lastName']) && isset($_POST['username']) &&
			isset($_POST['phoneNum']) && isset($_POST["NID"])) {

			$user = Auth::user();

			$username = makeValidInput($_POST["username"]);
			$NID = makeValidInput($_POST["NID"]);

			if($user->username != $username && User::whereUsername($username)->count() > 0)
				$msg = "نام کاربری وارد شده در سیستم موجود است";
			else {

				$user->firstName = makeValidInput($_POST["firstName"]);
				$user->lastName = makeValidInput($_POST["lastName"]);

				$allow = true;

				if($user->NID != $NID) {
					if(User::whereNID($NID)->count() > 0) {
						$msg = "کد ملی وارد شده در سامانه موجود است";
						$allow = false;
					}
					if (!_custom_check_national_code($NID)) {
						$msg = "کد ملی وارد شده معتبر نمی باشد";
						$allow = false;
					}
				}

				if($allow) {
					$user->NID = $NID;
					$user->username = $username;
					$user->save();

					$phoneNum = makeValidInput($_POST["phoneNum"]);

					if ($phoneNum != $user->phoneNum) {

//					$user->phoneNum = $phoneNum;
//					$user->save();
//					return Redirect::to('profile');

						if (User::wherePhoneNum($phoneNum)->count() > 0) {
							$msg = "شماره همراه وارد شده در سیستم موجود است";
						} else {

							$activation = Activation::wherePhoneNum($phoneNum)->first();

							if ($activation == null) {
								$activation = new Activation();
								$activation->phoneNum = $phoneNum;
							} else {
								if (time() - $activation->startTime < 300) {
									$msg = "pendingErrTime";
									return $this->userInfo($msg, 'editInfo', 300 - time() + $activation->sendTime, $phoneNum);
								}
							}

							$activationCode = generateActivationCode();
							$activation->code = $activationCode;
							$activation->sendTime = time();

							$activation->save();
							sendSMS($phoneNum, $activationCode, "activationCode");

							$msg = "pending";
							return $this->userInfo($msg, 'editInfo', '300', $phoneNum);
						}
					}
				}
			}
		}
		else
			$msg = "خطایی در انجام عملیات مورد نظر رخ داده است";

		if(empty($msg))
			return Redirect::to('userInfo');

		return $this->userInfo($msg, 'editInfo');
	}

	public function editRedundantInfo1() {

		if(isset($_POST['fatherName']) && isset($_POST['cityId']) &&
			isset($_POST["gradeId"]) && isset($_POST["email"])) {

			$uId = Auth::user()->id;
			$redundant1 = RedundantInfo1::whereUId($uId)->first();

			if($redundant1 == null) {
				$redundant1 = new RedundantInfo1();
				include_once 'MoneyController.php';
				$redundant1->uId = $uId;
				charge(PointConfig::first()->infoPass2Point, $uId, getValueInfo('redundant1Transaction'), 2);
			}
			
			$redundant1->fatherName = makeValidInput($_POST["fatherName"]);
			$redundant1->cityId = makeValidInput($_POST["cityId"]);
			$redundant1->gradeId = makeValidInput($_POST["gradeId"]);
//			$redundant1->schoolName = makeValidInput($_POST["schoolName"]);
			$redundant1->email = makeValidInput($_POST["email"]);

			$redundant1->save();
		}
		else {
			$msg = "خطایی در انجام عملیات مورد نظر رخ داده است";
		}

		if(empty($msg))
			return Redirect::to('userInfo');

		return $this->userInfo($msg, 'editRedundant1');
	}

	public function editRedundantInfo2() {

		if(isset($_POST['address']) && isset($_POST['homePhone']) && isset($_POST['fatherPhone']) &&
			isset($_POST['motherPhone']) && isset($_POST["homePostCode"])) {

			$uId = Auth::user()->id;
			$redundant2 = RedundantInfo2::whereUId($uId)->first();

			if($redundant2 == null) {
				$redundant2 = new RedundantInfo2();
				include_once 'MoneyController.php';
				$redundant2->uId = $uId;
				charge(PointConfig::first()->infoPass3Point, $uId, getValueInfo('redundant2Transaction'), 2);
			}

			$redundant2->address = makeValidInput($_POST["address"]);
			$redundant2->homePhone = makeValidInput($_POST["homePhone"]);
			$redundant2->fatherPhone = makeValidInput($_POST["fatherPhone"]);
			$redundant2->motherPhone = makeValidInput($_POST["motherPhone"]);
			$redundant2->homePostCode = makeValidInput($_POST["homePostCode"]);
//			$redundant2->kindSchool = makeValidInput($_POST["kindSchool"]);

			$redundant2->save();
		}
		else {
			$msg = "خطایی در انجام عملیات مورد نظر رخ داده است";
		}

		if(empty($msg))
			return Redirect::to('userInfo');

		return $this->userInfo($msg, 'editRedundant2');
	}

	public function changePas($msg = "") {
		return view('changePas', array('msg' => $msg));
	}

	public function doChangePas() {

		if(isset($_POST["oldPas"]) && isset($_POST["newPas"]) && isset($_POST["confirmPas"])) {

			$user = Auth::user();

			if(Hash::check(makeValidInput($_POST["oldPas"]), $user->password)) {

				$newPas = makeValidInput($_POST["newPas"]);
				$confirmPas = makeValidInput($_POST["confirmPas"]);

				if($newPas == $confirmPas) {
					$user->password = Hash::make($newPas);
					$user->save();
					return $this->changePas("عملیات مورد نظر با موفقیت انجام پذیرفت");
				}
				else
					return $this->changePas("رمز جدید و تکرار آن با هم یکی نیستند");
			}
			else
				return $this->changePas("رمز وارد شده نامعتبر است");
		}
		return $this->changePas();
	}

	public function setAsMySchool() {

		if (isset($_POST["sId"])) {

			$sId = makeValidInput($_POST["sId"]);
			$uId = Auth::user()->id;
			SchoolStudent::whereUId($uId)->delete();
			$tmp = new SchoolStudent();
			$tmp->sId = $sId;
			$tmp->uId = $uId;
			try {
				$tmp->save();
				echo "ok";
			}
			catch (\Exception $x) {}
		}
	}
}
