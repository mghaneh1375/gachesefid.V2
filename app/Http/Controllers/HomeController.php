<?php

namespace App\Http\Controllers;

use App\models\Question;
use App\models\QuizRegistry;
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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

class HomeController extends Controller {

	public function salam() {
		require_once 'getUpdatesCLI.php';
	}

	public function aboutUs() {
		return View('aboutUs');
	}

    public function showHome() {
		
		$sliders = SlideBar::all();
		foreach ($sliders as $slider) {
			$slider->pic = URL::asset('images/slideBar/' . $slider->pic);
		}

		return view('home', ['sliders' => $sliders, 'qNos' => Question::accepted()->count(),
			'usersNo' => User::students()->count(),
			'quizNo' => RegularQuiz::count(), 'adviserNos' => User::advisers()->count()]);
	}

	public function login() {

        $this->middleware('auth');
//		return view('login');
	}

	public function logout() {
		Auth::logout();
		return Redirect::To("login");
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

	public function doLogin() {

		$username = makeValidInput(Input::get('username'));
		$phone = makeValidInput(Input::get('phone'));
		$email = makeValidInput(Input::get('email'));
		$password = makeValidInput(Input::get('password'));

		if(Auth::attempt(['username' => $username, 'password' => $password], true) ||
			Auth::attempt(['phoneNum' => $phone, 'password' => $password], true)) {
			if(Auth::user()->status != 1) {
				$msg = "حساب کاربری شما هنوز فعال نشده است";
				Auth::logout();
			}
			else {
				if(Auth::user()->phoneNum == "")
					return Redirect::to('userInfo');

				return Redirect::to('profile');
			}
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
			if ($user == null || empty($user)) {
				echo "نام کاربری وارد شده معتبر نمی باشد";
				return;
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

					Mail::send('newPasswordGenerated', array("newPas" => $newPas), function ($message) {
						$message->to("mghaneh1375@yahoo.com", "بازیابی پسورد")->subject('Gachesefid@new password');
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

	public function profile() {

		if(Auth::user()->level == getValueInfo('studentLevel')) {

			$uId = Auth::user()->id;
			$today = getToday();

			$tmp1 = DB::select('select count(*) as countNum from systemQuiz sQ WHERE endReg >= '. $today["date"] .
					' and not exists(select * from quizRegistry qR where qR.uId = ' . $uId . ' and qR.qId = sQ.id and qR.quizMode = ' . getValueInfo('systemQuiz') . ')');

			if($tmp1 == null || count($tmp1) == 0 || $tmp1[0]->countNum == 0)
				$tmp1 = 0;
			else
				$tmp1 = $tmp1[0]->countNum;

			$tmp2 = DB::select('select count(*) as countNum from regularQuiz rQ WHERE endReg >= '. $today["date"] .
				' and not exists(select * from quizRegistry qR where qR.uId = ' . $uId . ' and qR.qId = rQ.id and qR.quizMode = ' . getValueInfo('regularQuiz') . ')');

			if($tmp2 == null || count($tmp2) == 0 || $tmp2[0]->countNum == 0)
				$tmp2 = 0;
			else
				$tmp2 = $tmp2[0]->countNum;

			$totalQuizes = $tmp1 + $tmp2;

			$regularCondition = ['uId' => $uId, 'quizMode' => getValueInfo('regularQuiz')];
			$systemCondition = ['uId' => $uId, 'quizMode' => getValueInfo('systemQuiz')];

			$tmp = QuizRegistry::where($regularCondition)->count() + QuizRegistry::where($systemCondition)->count();

			$amount = DB::select('select sum(q.level) * 5 as totalSum from roq, question q'.
				' where uId = ' . $uId . ' and q.id = questionId and q.ans = result');

			if($amount == null || count($amount) == 0 || $amount[0]->totalSum == 0) {
				$rate = -1;
				$amount = -1;
			}

			else {
				$amount = $amount[0]->totalSum;
				$rate = count(DB::select('select sum(q.level) * 5 as totalSum from users, roq, question q' .
					' where users.id = uId and q.id = questionId and q.ans = result and users.level = ' . getValueInfo('studentLevel') .
					' group by(uId) having totalSum > ' . $amount));
			}


			return view('profile', array('money' => Auth::user()->money,
				'myQuizNo' => $tmp,
				'nextQuizNo' => $totalQuizes,
				'rate' => $amount, 'rank' => $rate,
				'questionNo' => 0));

//			UserCreatedQuiz->totalQuestions($uId)
		}

		return view('profile');
	}

	public function userInfo($msg = "", $mode = "", $reminder = "", $phoneNum = "") {

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
			isset($_POST['phoneNum'])) {

			$user = Auth::user();

			$username = makeValidInput($_POST["username"]);

			if($user->username != $username && User::whereUsername($username)->count() > 0) {
				$msg = "نام کاربری وارد شده در سیستم موجود است";
			}
			else {

				$user->firstName = makeValidInput($_POST["firstName"]);
				$user->lastName = makeValidInput($_POST["lastName"]);
				$namayandeCode = "";
				if(isset($_POST["namayandeCode"]))
					$namayandeCode = makeValidInput($_POST["namayandeCode"]);

				if(!empty($namayandeCode)) {
					$namayande = User::where('invitationCode', '=', $namayandeCode)->first();
					if($namayande != null) {
						SchoolStudent::whereSId($user->id)->delete();
						$tmp = new SchoolStudent();
						$tmp->sId = $user->id;
						$tmp->uId = $namayande->id;
						$tmp->save();
					}
				}

				$user->username = $username;
				$user->save();

				$phoneNum = makeValidInput($_POST["phoneNum"]);

				if($phoneNum != $user->phoneNum) {

//					$user->phoneNum = $phoneNum;
//					$user->save();
//					return Redirect::to('profile');

					if(User::where('phoneNum', '=', $phoneNum)->count() > 0) {
						$msg = "شماره همراه وارد شده در سیستم موجود است";
					}
					else {

						$activation = Activation::wherePhoneNum( $phoneNum)->first();

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
		else {
			$msg = "خطایی در انجام عملیات مورد نظر رخ داده است";
		}

		if(empty($msg))
			return Redirect::to('userInfo');

		return $this->userInfo($msg, 'editInfo');
	}

	public function editRedundantInfo1() {

		if(isset($_POST['fatherName']) && isset($_POST['cityId']) &&
			isset($_POST['schoolName']) && isset($_POST["gradeId"]) && isset($_POST["email"])) {

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
			$redundant1->schoolName = makeValidInput($_POST["schoolName"]);
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
			isset($_POST['motherPhone']) && isset($_POST["homePostCode"]) && isset($_POST["kindSchool"])) {

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
			$redundant2->kindSchool = makeValidInput($_POST["kindSchool"]);

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
}
