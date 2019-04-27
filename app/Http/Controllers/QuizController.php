<?php

namespace App\Http\Controllers;

use App\models\ComposeQuiz;
use App\models\ComposeQuizItem;
use App\models\ConfigModel;
use App\models\LOK;
use App\models\OffCode;
use App\models\QuizStatus;
use App\models\RegularQOQ;
use App\models\RegularQuiz;
use App\models\ROQ2;
use App\models\SoldQuestion;
use App\models\SubjectsPercent;
use App\models\SystemQOQ;
use App\models\User;
use App\models\Mellat;
use App\models\OrderId;
use App\models\Question;
use App\models\ROQ;
use App\models\ControllerActivity;
use App\models\Taraz;
use App\models\KindKarname;
use App\models\RedundantInfo1;
use App\models\State;
use App\models\City;
use App\models\Grade;
use App\models\QuizRegistry;
use App\models\UserCreatedQuiz;
use App\models\SystemQuiz;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use PHPExcel_IOFactory;
use soapclient;

class QuizController extends Controller {

    public function addToRegularQuiz() {

        if(isset($_POST["online"]) && isset($_POST["nid"]) && isset($_POST["quizId"])) {

            $uId = User::whereNID(makeValidInput($_POST["nid"]))->first();

            if($uId != null) {

                $uId = $uId->id;
                $quizId = makeValidInput($_POST["quizId"]);

                if(QuizRegistry::whereQId($quizId)->whereUId($uId)->whereQuizMode(getValueInfo('regularQuiz'))->first() == null) {

                    $tmp = new QuizRegistry();
                    $tmp->quizMode = getValueInfo('regularQuiz');
                    $tmp->qId = $quizId;
                    $tmp->uId = $uId;
                    $tmp->online = (makeValidInput($_POST["online"]) == 1);
                    $tmp->save();

                    echo "ok";
                }

            }
        }

    }

    public function elseQuiz() {

        if(isset($_POST["quizId"])) {

            $quizId = makeValidInput($_POST["quizId"]);
            $regularQuiz = RegularQuiz::whereId($quizId);

            if($regularQuiz == null) {
                echo "nok";
                return;
            }

            $condition = ['quizId' => $quizId, 'mark' => 0];
            $regularQOQ = RegularQOQ::where($condition)->select('qNo')->get();

            echo json_encode(['ranking' => $regularQuiz->ranking, 'showRanking' => $regularQuiz->ranking,
                'deleteQ' => json_encode($regularQOQ)]);

            return;
        }

        echo "nok";

    }

    public function elseSystemQuiz() {

        if(isset($_POST["quizId"])) {

            $quizId = makeValidInput($_POST["quizId"]);
            $systemQuiz = SystemQuiz::whereId($quizId);

            if($systemQuiz == null) {
                echo "nok";
                return;
            }

            $condition = ['quizId' => $quizId, 'mark' => 0];
            $systemQOQ = SystemQOQ::where($condition)->select('qNo')->get();

            echo json_encode(['deleteQ' => json_encode($systemQOQ)]);

            return;
        }

        echo "nok";

    }

    public function changeRankingCount() {
        
        if(isset($_POST["val"]) && isset($_POST["quizId"])) {
            $quizId = makeValidInput($_POST["quizId"]);
            $regularQuiz = RegularQuiz::whereId($quizId);

            $regularQuiz->ranking = makeValidInput($_POST["val"]);
            $regularQuiz->save();
        }
        
    }

    public function deleteQFromQ() {

        if(isset($_POST["quizId"]) && isset($_POST["questionId"]) && isset($_POST["quizMode"])) {


            if (makeValidInput($_POST["quizMode"]) == "regularQuiz") {

                $condition = ['quizId' => makeValidInput($_POST["quizId"]),
                    'qNo' => makeValidInput($_POST["questionId"])];
                $tmp = RegularQOQ::where($condition)->first();
                if ($tmp == null) {
                    echo "nok";
                    return;
                }

                $tmp->mark = 0;
                $tmp->save();
                echo "ok";
                return;
            }

            else if(makeValidInput($_POST["quizMode"]) == "systemQuiz") {

                $condition = ['quizId' => makeValidInput($_POST["quizId"]),
                    'qNo' => makeValidInput($_POST["questionId"])];

                $tmp = SystemQOQ::where($condition)->first();
                if ($tmp == null) {
                    echo "nok";
                    return;
                }

                $tmp->mark = 0;
                $tmp->save();
                echo "ok";
                return;
            }

            echo "nok";
        }
    }

    public function deleteDeletedQFromQ() {

        if(isset($_POST["quizId"]) && isset($_POST["questionId"])) {

            $condition = ['quizId' => makeValidInput($_POST["quizId"]),
                'qNo' => makeValidInput($_POST["questionId"])];
            $tmp = RegularQOQ::where($condition)->first();
            if($tmp == null) {
                echo "nok";
                return;
            }

            $tmp->mark = 1;
            $tmp->save();
            echo "ok";
            return;
        }
        echo "nok";

    }

    public function deleteDeletedQFromSystemQ() {

        if(isset($_POST["quizId"]) && isset($_POST["questionId"])) {

            $condition = ['quizId' => makeValidInput($_POST["quizId"]),
                'qNo' => makeValidInput($_POST["questionId"])];
            $tmp = SystemQOQ::where($condition)->first();

            if($tmp == null) {
                echo "nok";
                return;
            }

            $tmp->mark = 10;
            $tmp->save();
            echo "ok";
            return;
        }
        echo "nok";

    }

    public function ranking($quizId) {

        $const = RegularQuiz::whereId($quizId);

        if($const == null || $const->ranking == 0)
            return Redirect::to('profile');

        $users = DB::select("select DISTINCT qR.uId, qR.id from quizRegistry qR WHERE qR.qId = " . $quizId .
                " and qR.quizMode = " . getValueInfo('regularQuiz') .
            " and (select count(*) from ROQ r where r.uId = qR.uId and r.quizId = qR.qId and r.quizMode = qR.quizMode) > 0");

        $const = $const->ranking;

        $totalMark = DB::select("select sum(r.mark) as sumTotal from regularQOQ r WHERE r.quizId = " . $quizId)[0]->sumTotal;

        foreach ($users as $user) {

            $tmp = DB::select('select name, coherence, (percent + percent2 + percent3) as percent, taraz from taraz, lesson WHERE taraz.qEntryId = ' . $user->id .
                ' and lesson.id = taraz.lId');

            $user->lessons = $tmp;

            $target = User::whereId($user->uId);
            $user->name = $target->firstName . " " . $target->lastName;

            $cityAndState = getStdCityAndState($user->uId);

            $user->city = $cityAndState["city"];
            $user->state = $cityAndState["state"];
            $user->schoolName = getStdSchoolName($user->uId);

            $user->rank = calcRank($quizId, $user->uId);
            $user->cityRank = calcRankInCity($quizId, $user->uId, $cityAndState["cityId"]);
            $user->stateRank = calcRankInState($quizId, $user->uId, $cityAndState["stateId"]);

        }

        usort($users, function ($a, $b) {
            return $a->rank - $b->rank;
        });

        $limit = (count($users) > $const) ? $const : count($users);
        $users = array_splice($users, 0, $limit);

        return view('ranking', array('users' => $users, 'totalMark' => $totalMark, 'quizName' => RegularQuiz::whereId($quizId)->name));

    }

    public function rankingSelectQuiz() {

        $today = getToday();
        $date = $today["date"];
        $time = $today["time"];

        $quizes = DB::select('select id, name from regularQuiz WHERE endDate < ' . $date . ' or (endDate = ' . $date . ' and endTime < ' . $time . ')');

        return view('rankingSelectQuiz', array('quizes' => $quizes));
    }

    public function toggleStatusOnline() {

        if(isset($_POST["id"])) {

            $tmp = QuizRegistry::whereId(makeValidInput($_POST["id"]));
            if($tmp != null) {
                $tmp->online = !$tmp->online;
                $tmp->save();
                echo "ok";
            }
        }

    }

    public function getROQ() {

        if(isset($_POST["username"]) && isset($_POST["password"]) &&
            isset($_POST["testAnswerSheets"])) {

            $username = makeValidInput($_POST['username']);
            $password = makeValidInput($_POST['password']);

            if(User::whereUsername($username)->count() == 0 ||
                User::whereUsername($username)->first()->status != 1) {
                return -1;
            }

            if(Auth::attempt(array('username' => $username, 'password' => $password))) {
                if(Auth::user()->level != getValueInfo('studentLevel')) {

                    $regularQuizMode = getValueInfo('regularQuiz');
                    $decoded = json_decode($_POST["testAnswerSheets"], true);
                    $qErrs = [];
                    $stdErrs = [];
                    foreach ($decoded as $key=>$value){
                        if(RegularQuiz::whereId($key) == null) {
                            $qErrs[count($qErrs)] = $key;
                            continue;
                        }
                        
                        $quizQuestions = DB::select('select questionId, mark from regularQOQ WHERE mark = 1 and quizId = ' . $key . " order by qNo ASC");

                        foreach ($value as $k=>$v){

                            if(count($quizQuestions) != strlen($v)) {
                                $qErrs[count($qErrs)] = "-404";
                                $stdErrs[count($stdErrs)] = $k;
                                continue;
                            }

                            $condition = ['uId' => $k,
                                'quizMode' => $regularQuizMode,
                                'qId' => $key
                            ];
                            if(QuizRegistry::where($condition)->count() == 0) {
                                $qErrs[count($qErrs)] = "-405 " . $key . " " . $k . " " . $regularQuizMode;
                                $stdErrs[count($stdErrs)] = $k;
                                continue;
                            }
                            $condition = ["quizId" => $key,
                                'uId' => $k,
                                'quizMode' => $regularQuizMode];
                            if(ROQ::where($condition)->count() > 0) {
                                ROQ::where($condition)->delete();
                            }

                            for($i = 0; $i < strlen($v); $i++) {

                                if($quizQuestions[$i]->mark == 0)
                                    continue;

                                $roq = new ROQ();
                                $roq->uId = $k;
                                $roq->questionId = $quizQuestions[$i]->questionId;
                                $roq->quizId = $key;
                                $roq->status = 1;
                                $roq->quizMode = $regularQuizMode;
                                $roq->result = $v[$i];
                                $roq->save();
                            }
                        }
                    }

                    if(empty($qErrs) && empty($stdErrs))
                        return "true";
                    return json_encode(['quizErr' => $qErrs, 'stdErr' => $stdErrs]);
                }
                else
                    return -1;
            }
            return -1;
        }
        return "false";
    }

    public function myQuizes($err = "") {

        $today = getToday();
        $date = $today["date"];
        $time = $today["time"];
        $uId = Auth::user()->id;

        $myQuiz = DB::select('select r.*, qR.quizMode, qR.timeEntry from quizRegistry qR, regularQuiz r WHERE qR.uId = ' . $uId . ' and r.id = qR.qId');
        $regularQuizMode = getValueInfo('regularQuiz');

        foreach ($myQuiz as $itr) {

            if($itr->quizMode == $regularQuizMode) {

                $itr->mode = "regular";

                $tmpTimeLen = calcTimeLenQuiz($itr->id, 'regular');

                if($tmpTimeLen < 10)
                    $itr->timeLen = " - ";
                else
                    $itr->timeLen = $tmpTimeLen;

                if(ROQ::whereQuizId($itr->id)->whereUId($uId)->count() > 0) {
                    $itr->quizEntry = -2;
                }

                else {
                    if (($itr->startDate < $date && $itr->endDate > $date) ||
                        ($itr->startDate < $date && $itr->endDate >= $date && $itr->endTime > $time) ||
                        ($itr->startDate == $date && $itr->startTime <= $time && (
                                ($itr->startDate == $itr->endDate && $itr->endTime > $time) ||
                                ($itr->startDate != $itr->endDate) ||
                                ($itr->endDate == $date && $itr->endTime > $time)
                            )
                        )
//                    || ($itr->id == 203 && $uId == 50)
//                    || ($itr->id == 203 && $uId == 4783)
//                    || ($itr->id == 203 && $uId == 4707)
//                    || ($itr->id == 203 && $uId == 4738)
                    ) {

                        $timeLen = calcTimeLenQuiz($itr->id, 'regular');

                        if ($itr->timeEntry == "") {
                            $itr->quizEntry = 1;
                        } else {
                            $timeEntry = $itr->timeEntry;
                            $reminder = $timeLen * 60 - time() + $timeEntry;
                            if ($reminder <= 0)
                                $itr->quizEntry = -2;
                            else
                                $itr->quizEntry = 1;
                        }
                    } else if ($itr->startDate > $date ||
                        ($itr->startDate == $date && $itr->startTime > $time)
                    ) {
                        $itr->quizEntry = -1;
                    } else {
                        $itr->quizEntry = -2;
                    }
                }

                $itr->startDate = convertStringToDate($itr->startDate);
                $itr->endDate = convertStringToDate($itr->endDate);
                $itr->startTime = convertStringToTime($itr->startTime);
                $itr->endTime = convertStringToTime($itr->endTime);
            }

            else {

                $itr->mode = "system";
                $tmpTimeLen = calcTimeLenQuiz($itr->quiz->id, 'system');

                if($tmpTimeLen < 10)
                    $itr->timeLen = " - ";
                else
                    $itr->timeLen = $tmpTimeLen;

                if($itr->startDate == $date) {

                    if($itr->startTime <= $time) {

                        $itr->reminder = subTimes(sumTimes($itr->startTime, $itr->timeLen), $time);

                        if ($itr->reminder <= 0)
                            $itr->quizEntry = -2;
                        else
                            $itr->quizEntry = 1;
                    }
                    else {
                        $itr->quizEntry = -1;
                    }
                }
                else {
                    if($itr->startDate > $date)
                        $itr->quizEntry = -1;
                    else
                        $itr->quizEntry = -2;
                }

                $itr->startDate = convertStringToDate($itr->startDate);
                $itr->startTime = convertStringToTime($itr->startTime);

            }
        }

//        $myQuiz = QuizRegistry::whereUId($uId)->get();
//        $regularQuizMode = getValueInfo('regularQuiz');
//
//        foreach ($myQuiz as $itr) {
//
//            if($itr->quizMode == $regularQuizMode) {
//
//                $itr->mode = "regular";
//                $itr->quiz = RegularQuiz::whereId($itr->qId);
//
//                $tmpTimeLen = calcTimeLenQuiz($itr->quiz->id, 'regular');
//
//                if($tmpTimeLen < 10)
//                    $itr->quiz->timeLen = " - ";
//                else
//                    $itr->quiz->timeLen = $tmpTimeLen;
//
//                if(($itr->quiz->startDate < $date && $itr->quiz->endDate > $date) ||
//                    ($itr->quiz->startDate < $date && $itr->quiz->endDate >= $date && $itr->quiz->endTime > $time) ||
//                    ($itr->quiz->startDate == $date && $itr->quiz->startTime <= $time && (
//                            ($itr->quiz->startDate == $itr->quiz->endDate && $itr->quiz->endTime > $time) ||
//                            ($itr->quiz->startDate != $itr->quiz->endDate) ||
//                            ($itr->quiz->endDate == $date && $itr->quiz->endTime > $time)
//                        )
//                    )) {
//
//                    $condition = ['qId' => $itr->quiz->id, 'uId' => $uId, 'quizMode' => $regularQuizMode];
//                    $quizRegistry = QuizRegistry::where($condition)->first();
//
//                    $timeLen = calcTimeLenQuiz($itr->quiz->id, 'regular');
//
//                    if($quizRegistry->timeEntry == "") {
//                        $itr->quizEntry = 1;
//                    }
//                    else {
//                        $timeEntry = $quizRegistry->timeEntry;
//                        $reminder = $timeLen * 60 - time() + $timeEntry;
//                        if($reminder <= 0)
//                            $itr->quizEntry = -2;
//                        else
//                            $itr->quizEntry = 1;
//                    }
//                }
//                else if($itr->quiz->startDate > $date ||
//                    ($itr->quiz->startDate == $date && $itr->quiz->startTime > $time)) {
//                    $itr->quizEntry = -1;
//                }
//                else {
//                    $itr->quizEntry = -2;
//                }
//
//                $itr->quiz->startDate = convertStringToDate($itr->quiz->startDate);
//                $itr->quiz->endDate = convertStringToDate($itr->quiz->endDate);
//                $itr->quiz->startTime = convertStringToTime($itr->quiz->startTime);
//                $itr->quiz->endTime = convertStringToTime($itr->quiz->endTime);
//            }
//
//            else {
//
//                $itr->mode = "system";
//                $itr->quiz = SystemQuiz::whereId($itr->qId);
//                $tmpTimeLen = calcTimeLenQuiz($itr->quiz->id, 'system');
//
//                if($tmpTimeLen < 10)
//                    $itr->quiz->timeLen = " - ";
//                else
//                    $itr->quiz->timeLen = $tmpTimeLen;
//
//                if($itr->quiz->startDate == $date) {
//
//                    if($itr->quiz->startTime <= $time) {
//
//                        $itr->quiz->reminder = subTimes(sumTimes($itr->quiz->startTime, $itr->quiz->timeLen), $time);
//
//                        if ($itr->quiz->reminder <= 0)
//                            $itr->quizEntry = -2;
//                        else
//                            $itr->quizEntry = 1;
//                    }
//                    else {
//                        $itr->quizEntry = -1;
//                    }
//                }
//                else {
//                    if($itr->quiz->startDate > $date)
//                        $itr->quizEntry = -1;
//                    else
//                        $itr->quizEntry = -2;
//                }
//
//                $itr->quiz->startDate = convertStringToDate($itr->quiz->startDate);
//                $itr->quiz->startTime = convertStringToTime($itr->quiz->startTime);
//
//            }
//        }

        $condition = ['uId' => $uId, 'status' => 1];
        $myQuiz2 = UserCreatedQuiz::where($condition)->get();
        foreach ($myQuiz2 as $itr) {

            $itr->quizMode = getValueInfo('questionQuiz');
            $itr->timeLen = calcTimeLenQuiz($itr->id, 'self');

            if(!empty($itr->timeEntry))
                $reminder = ceil($itr->timeLen * 60 - (time() - $itr->timeEntry));
            else
                $reminder = $itr->timeLen;

            if($reminder > 0)
                $itr->quizEntry = 1;
            else
                $itr->quizEntry = -2;

            if(!empty($itr->created))
                $itr->created = convertStringToDate($itr->created);
        }
        
        return view('quizEntry2', array('quizes' => $myQuiz, 'err' => $err, 'selfQuizes' => $myQuiz2));

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

    public function seeResult($quizId = "") {

        $uId = Auth::user()->id;
        $msg = "";

        if (isset($_POST["getKarname"])) {
            
            if(empty($quizId))
                $quizId = makeValidInput($_POST["quizId"]);

            $tmp = DB::select('select count(*) as countNum from quizRegistry qR, taraz t where qR.uId = ' . $uId . ' and qR.quizMode = ' . getValueInfo('regularQuiz') . ' and qR.qId = ' . $quizId . " and qR.id = t.qEntryId");

            if($tmp == null || count($tmp) == 0 || empty($tmp[0]->countNum) || $tmp[0]->countNum = 0) {
                $msg = "پاسخ برگ شما به سایت ارسال نشده یا در حال بررسی است";
            }
            else {

                $karname = makeValidInput($_POST["kindKarname"]);
                $conditions = ['uId' => $uId, 'qId' => $quizId, 'quizMode' => getValueInfo('regularQuiz')];
                $qEntryId = QuizRegistry::where($conditions)->select('id')->first();

                $tmp = Taraz::whereQEntryId($qEntryId->id)->count();

                if ($tmp == null || $tmp == 0)
                    $msg = "صفحه ی نمایش کارنامه برای این آزمون هنوز باز نشده است";

                else {
                    $kindKarname = KindKarname::first();
                    if ($kindKarname == null)
                        $msg = "مشکلی در نمایش کارنامه به وجود آمده است";
                    else {
                        switch ($karname) {
                            case 1:
                            default:
                                return $this->showGeneralKarname($uId, $quizId, $qEntryId, $kindKarname);
                            case 2:
                                return $this->showSubjectKarname($uId, $quizId, $kindKarname, makeValidInput($_POST["lId"]));
                            case 3:
                                return $this->showQuestionKarname($uId, $quizId);
                        }
                    }
                }
            }
        }

        $conditions = ['uId' => $uId, 'quizMode' => getValueInfo('regularQuiz')];
        $myQuizes = QuizRegistry::where($conditions)->select('qId')->get();
        $quizes = array();
        for($i = 0; $i < count($myQuizes); $i++)
            $quizes[$i] = RegularQuiz::where('id', '=', $myQuizes[$i]->qId)->select('id', 'name')->first();
        return view('karname', array('quizes' => $quizes, 'msg' => $msg, 'selectedQuiz' => $quizId));
    }

    private function showSubjectKarname($uId, $quizId, $kindKarname, $lId) {

        $status = array();

        $cityId = RedundantInfo1::whereUId($uId)->first()->cityId;

        if($kindKarname->subjectStatus)
            $status = QuizStatus::whereLevel(2)->get();

        $avgs = DB::select('select SUM(percent3) / count(*) as avg3, MAX(percent3) as maxPercent3, MIN(percent3) as minPercent3, SUM(percent2) / count(*) as avg2, MAX(percent2) as maxPercent2, MIN(percent2) as minPercent2, SUM(percent) / count(*) as avg, MAX(percent) as maxPercent, MIN(percent) as minPercent FROM subject, subjectsPercent WHERE qId = ' . $quizId . ' and subject.id = sId and subject.lessonId = ' . $lId . ' GROUP by(sId)');

        $cityRank = array();
        $stateRank = array();
        $countryRank = array();

        $subjects = $this->getSubjectsQuiz($quizId, $lId);

        if($kindKarname->subjectCityRank) {
            $counter = 0;
            foreach ($subjects as $subject) {
                $tmp = DB::select('SELECT subjectsPercent.uId, (percent + percent2 + percent3) as taraz from redundantInfo1 rd, subjectsPercent WHERE rd.uId = subjectsPercent.uId and rd.cityId = ' . $cityId . ' and subjectsPercent.qId = ' . $quizId . ' and subjectsPercent.sId = ' . $subject->id . ' ORDER by taraz DESC');
                $cityRank[$counter++] = $this->getRank($tmp, $uId);
            }
        }

        if($kindKarname->subjectStateRank) {
            $counter = 0;
            $stateId = State::whereId(City::whereId($cityId)->stateId)->id;
            foreach ($subjects as $subject) {
                $tmp = DB::select('SELECT subjectsPercent.uId, (percent + percent2 + percent3) as taraz from redundantInfo1 rd, city ci, subjectsPercent WHERE rd.uId = subjectsPercent.uId and rd.cityId = ci.id and ci.stateId = ' . $stateId . ' and subjectsPercent.qId = ' . $quizId . ' and subjectsPercent.sId = ' . $subject->id . ' ORDER by taraz DESC');
                $stateRank[$counter++] = $this->getRank($tmp, $uId);
            }
        }

        if($kindKarname->subjectCountryRank) {
            $counter = 0;
            foreach ($subjects as $subject) {
                $tmp = DB::select('SELECT subjectsPercent.uId, (percent + percent2 + percent3) as taraz from subjectsPercent WHERE subjectsPercent.qId = ' . $quizId . ' and subjectsPercent.sId = ' . $subject->id . ' ORDER by taraz DESC');
                $countryRank[$counter++] = $this->getRank($tmp, $uId);
            }
        }

        $roq = [];
        $roq2 = [];
        $roq3 = [];
        $counterTmp = 0;
        $subjectPercents = [];

        foreach ($subjects as $subject) {

            $totalQInSpecificSubject = DB::select('select result, ans, kindQ, telorance from ROQ, SOQ, question where uId = ' . $uId . ' and qId = question.id and quizId = ' . $quizId . ' and question.id = questionId and sId = ' . $subject->id);

            if($totalQInSpecificSubject != null) {

                $corrects = $inCorrects = 0;
                $corrects2 = $inCorrects2 = 0;
                $corrects3 = $inCorrects3 = 0;
                $totalQ = 0;
                $totalQ2 = 0;
                $totalQ3 = 0;

                foreach ($totalQInSpecificSubject as $itr) {

                    if($itr->kindQ == 1) {
                        if($itr->ans == $itr->result)
                            $corrects++;
                        else if($itr->result != 0)
                            $inCorrects++;

                        $totalQ++;
                    }

                    else if($itr->kindQ == 0) {
                        if($itr->ans - $itr->telorance <= $itr->result &&
                            $itr->ans + $itr->telorance >= $itr->result)
                            $corrects2++;
                        else if($itr->result != 0)
                            $inCorrects2++;
                        $totalQ2++;
                    }

                    else {
                        $itr->result = (string)$itr->result;
                        $totalQ3 += strlen($itr->result);
                        for ($k = 0; $k < strlen($itr->result); $k++) {
                            if ($itr->result[$k] == $itr->ans[$k])
                                $corrects3++;
                            else if ($itr->result[$k] != 0)
                                $inCorrects3++;
                        }
                    }
                }

                $subjectPercents[$counterTmp] = SubjectsPercent::whereUId($uId)->whereQId($quizId)->whereSId($subject->id)->first();
                $roq[$counterTmp] = [$inCorrects, $corrects, $totalQ];
                $roq2[$counterTmp] = [$inCorrects2, $corrects2, $totalQ2];
                $roq3[$counterTmp++] = [$inCorrects3, $corrects3, $totalQ3];
            }
        }

        $totalMark = 20;

        if($kindKarname->subjectMark)
            $totalMark = 20;

        $minusMark = 1;

        return view('subjectKarname', array('quizId' => $quizId, 'status' => $status, 'roq' => $roq, 'subjects' => $subjects,
            'kindKarname' => $kindKarname, 'avgs' => $avgs, 'cityRank' => $cityRank, 'stateRank' => $stateRank,
            'countryRank' => $countryRank, 'totalMark' => $totalMark, 'minusMark' => $minusMark,
            'roq2' => $roq2, 'roq3' => $roq3, 'subjectPercents' => $subjectPercents));
    }

    private function showQuestionKarname($uId, $quizId) {

        $regularQuizMode = getValueInfo('regularQuiz');

         $qInfos = DB::select("select telorance, kindQ, question.id, question.ans, ROQ.result ".
            "from question, ROQ WHERE ROQ.quizId = " . $quizId . " and " .
            "ROQ.questionId = question.id and ROQ.quizMode = " . $regularQuizMode . " and ROQ.uId = " . $uId .
            " order by ROQ.id ASC");

        if(count($qInfos) == 0)
            return Redirect::to(route('seeResult'));

        $condition = ['questionId' => $qInfos[0]->id, 'quizId' => $quizId, 'quizMode' => $regularQuizMode];
        $total = ROQ::where($condition)->count();

        foreach ($qInfos as $qInfo) {

            $qInfo->result = (string)$qInfo->result;

            $condition = ['questionId' => $qInfo->id, 'quizId' => $quizId, 'quizMode' => $regularQuizMode,
                'result' => 0];
            $qInfo->white = ROQ::where($condition)->count();

            if($qInfo->kindQ == 1) {
                $condition = ['questionId' => $qInfo->id, 'quizId' => $quizId, 'quizMode' => $regularQuizMode,
                    'result' => $qInfo->ans];
                $qInfo->correct = ROQ::where($condition)->count();
            }
            elseif($qInfo->kindQ == 0) {
                $qInfo->correct = DB::select('select count(*) as countNum from ROQ WHERE questionId = ' . $qInfo->id . ' and quizId = ' . $quizId .
                    ' and quizMode = ' . $regularQuizMode . ' and result >= ' . ($qInfo->ans - $qInfo->telorance) .
                    ' and result <= ' . ($qInfo->ans + $qInfo->telorance))[0]->countNum;
            }
            else {

                $roqsTmp = ROQ::whereQuestionId($qInfo->id)->whereQuizId($quizId)->select('result')->get();
                $corrects = $inCorrects = $whites = [];
                $first = true;

                foreach ($roqsTmp as $itr) {

                    $itr->result = (string)$itr->result;

                    if($first) {
                        for ($k = 0; $k < strlen($itr->result); $k++) {
                            $corrects[$k] = 0;
                            $inCorrects[$k] = 0;
                            $whites[$k] = 0;
                        }
                        $first = false;
                    }

                    for ($k = 0; $k < strlen($itr->result); $k++) {
                        if ($itr->result[$k] == $qInfo->ans[$k])
                            $corrects[$k] = $corrects[$k] + 1;
                        else if ($itr->result[$k] != 0)
                            $inCorrects[$k] = $inCorrects[$k] + 1;
                        else
                            $whites[$k] = $whites[$k] + 1;
                    }
                }

                $qInfo->corrects = $corrects;
                $qInfo->inCorrects = $inCorrects;
                $qInfo->whites = $whites;
            }
            
            $contents = DB::select('select subject.name as subjectName, lesson.name as lessonName from SOQ, subject, lesson WHERE SOQ.qId = ' . $qInfo->id . ' and SOQ.sId = subject.id and subject.lessonId = lesson.id');
            $subjects = [];
            $lessons = [];
            $i = 0;
            foreach ($contents as $content) {
                $subjects[$i] = $content->subjectName;
                if(!in_array($content->lessonName, $lessons))
                    $lessons[count($lessons)] = $content->lessonName;
                $i++;
            }
            $qInfo->subjects = $subjects;
            $qInfo->lessons = $lessons;
            $qInfo->level = getQuestionLevel($qInfo->id);
        }

        return view('questionKarname', array('qInfos' => $qInfos, 'quizId' => $quizId, 'total' => $total));
    }

    private function getSubjectsQuiz($quizId, $lId) {
        $sIds = DB::select('SELECT DISTINCT S.name, S.id as id from subject S, question q, regularQOQ QO, SOQ SO WHERE QO.quizId = ' . $quizId . ' and QO.questionId = q.id and SO.sId = S.id and SO.qId = q.id and S.lessonId = ' . $lId);
        if(count($sIds) > 0)
            return $sIds;
        return null;
    }

    private function showGeneralKarname($uId, $quizId, $qEntryId, $kindKarname) {

        $status = array();
        if($kindKarname->lessonStatus)
            $status = QuizStatus::whereLevel(1)->get();

        $rank = calcRank($quizId, $uId);

        $rankInLesson = array();
        $cityRank = -1;
        $stateRank = -1;
        $stateId = -1;
        $rankInLessonCity = array();
        $rankInLessonState = array();

        $cityId = RedundantInfo1::whereUId($uId)->first();

        if($cityId == null)
            $cityId = City::first()->id;
        else
            $cityId = $cityId->cityId;

        if($kindKarname->lessonCityRank)
            $cityRank = calcRankInCity($quizId, $uId, $cityId);

        if($kindKarname->lessonStateRank) {
            $stateId = State::whereId(City::whereId($cityId)->stateId)->id;
            $stateRank = calcRankInState($quizId, $uId, $stateId);
        }

        $lessons = getLessonQuiz($quizId);
        $roq = [];
        $roq2 = [];
        $roq3 = [];
        $counterTmp = 0;

        $avgs = DB::select('select SUM(percent3) / count(*) as avg3, MAX(percent3) as maxPercent3, MIN(percent3) as minPercent3, SUM(percent2) / count(*) as avg2, MAX(percent2) as maxPercent2, MIN(percent2) as minPercent2, SUM(percent) / count(*) as avg, MAX(percent) as maxPercent, MIN(percent) as minPercent FROM taraz, quizRegistry WHERE quizRegistry.qId = ' . $quizId . ' and quizRegistry.id  = taraz.qEntryId GROUP by(taraz.lId)');

        foreach ($lessons as $lesson) {

            $kindQ2 = DB::select('select result, ans, kindQ, telorance, mark from regularQOQ qoq, ROQ roq, SOQ, question, subject where qoq.mark <> 0 and qoq.questionId = question.id and qoq.quizId = ' . $quizId . ' and uId = ' . $uId . ' and subject.id = sId and qId = question.id and roq.quizId = ' . $quizId . ' and question.id = roq.questionId and lessonId = ' . $lesson->id);

            if($kindQ2 != null) {

                $corrects = $inCorrects = 0;
                $corrects2 = $inCorrects2 = 0;
                $corrects3 = $inCorrects3 = 0;
                $totalQ1 = 0;
                $totalMark = 0;
                $totalQ2 = 0;
                $totalQ3 = 0;

                foreach ($kindQ2 as $itrKindQ2) {

                    if($itrKindQ2->kindQ == 1) {
                        if($itrKindQ2->ans == $itrKindQ2->result)
                            $corrects++;
                        else if($itrKindQ2->result != 0)
                            $inCorrects++;
                        $totalQ1++;
                        $totalMark += $itrKindQ2->mark;
                    }

                    else if($itrKindQ2->kindQ == 0) {
                        if($itrKindQ2->ans - $itrKindQ2->telorance <= $itrKindQ2->result &&
                            $itrKindQ2->ans + $itrKindQ2->telorance >= $itrKindQ2->result)
                            $corrects2++;
                        else if($itrKindQ2->result != 0)
                            $inCorrects2++;
                        $totalQ2++;
                    }

                    else {
                        $itrKindQ2->result = (string)$itrKindQ2->result;
                        $totalQ3 += strlen($itrKindQ2->result);
                        for ($k = 0; $k < strlen($itrKindQ2->result); $k++) {
                            if ($itrKindQ2->result[$k] == $itrKindQ2->ans[$k])
                                $corrects3++;
                            else if ($itrKindQ2->result[$k] != 0)
                                $inCorrects3++;
                        }
                    }
                }

                $roq[$counterTmp] = [$inCorrects, $corrects, $totalQ1, $totalMark];
                $roq2[$counterTmp] = [$inCorrects2, $corrects2, $totalQ2];
                $roq3[$counterTmp++] = [$inCorrects3, $corrects3, $totalQ3];
            }
        }

        $taraz = Taraz::whereQEntryId($qEntryId->id)->get();

        if($kindKarname->lessonCountryRank) {
            $counter = 0;
            foreach ($lessons as $lesson) {
                $tmp = DB::select('SELECT quizRegistry.uId, taraz.taraz from quizRegistry, taraz WHERE quizRegistry.id = taraz.qEntryId and quizRegistry.qId = ' . $quizId . ' and taraz.lId = ' . $lesson->id . ' ORDER by taraz.taraz DESC');
                $rankInLesson[$counter++] = $this->getRank($tmp, $uId);
            }
        }

        if($kindKarname->lessonStateRank) {
            $counter = 0;
            foreach ($lessons as $lesson) {
                $tmp = DB::select('SELECT quizRegistry.uId, taraz.taraz from redundantInfo1 rd, city ci, quizRegistry, taraz WHERE rd.uId = quizRegistry.uId and rd.cityId = ci.id and ci.stateId = ' . $stateId . ' and quizRegistry.id = taraz.qEntryId and quizRegistry.qId = ' . $quizId . ' and taraz.lId = ' . $lesson->id . ' ORDER by taraz.taraz DESC');
                $rankInLessonState[$counter++] = $this->getRank($tmp, $uId);
            }
        }

        if($kindKarname->lessonCityRank) {
            $counter = 0;
            foreach ($lessons as $lesson) {
                $tmp = DB::select('SELECT quizRegistry.uId, taraz.taraz from redundantInfo1 rd, quizRegistry, taraz WHERE rd.uId = quizRegistry.uId and rd.cityId = ' . $cityId . ' and quizRegistry.id = taraz.qEntryId and quizRegistry.qId = ' . $quizId . ' and taraz.lId = ' . $lesson->id . ' ORDER by taraz.taraz DESC');
                $rankInLessonCity[$counter++] = $this->getRank($tmp, $uId);
            }
        }

        $totalMark = 20;
        if($kindKarname->lessonMark)
            $totalMark = 20;

        $avgTaraz = -1;
        $avgRate = -1;
        $sumTaraz = -1;
        $sumRate = -1;
        $pack = false;

        $composeId = DB::select('select c.id from composeQuiz c, composeQuizItem ci WHERE c.id = ci.composeId and ci.quizId = ' . $quizId .' and ci.quizMode = ' . getValueInfo('regularQuiz'));
        if($composeId != null && count($composeId) > 0) {
            $composeId = $composeId[0]->id;

            $avgTarazes = DB::select('SELECT AVG(t.taraz) as avgTaraz, qR.uId from taraz t, quizRegistry qR, 
            composeQuizItem ci WHERE t.qEntryId = qR.id 
and qR.qId = ci.quizId and qR.quizMode = ci.quizMode and ' . $composeId . ' = ci.composeId GROUP BY(qR.uId) order BY avgTaraz DESC');

            for($i = 0; $i < count($avgTarazes); $i++) {

                if($avgTarazes[$i]->uId == $uId) {

                    $r = $i + 1;
                    $avgTaraz = $avgTarazes[$i]->avgTaraz;
                    $k = $i - 1;

                    while ($k >= 0 && $avgTarazes[$k]->avgTaraz == $avgTaraz) {
                        $k--;
                        $r--;
                    }
                    $avgRate = $r;
                    $avgTaraz = round($avgTaraz, 2);
                    break;
                }
            }

            $sumTarazes = DB::select('SELECT SUM(t.taraz) as sumTaraz, qR.uId from taraz t, quizRegistry qR, composeQuizItem ci WHERE t.qEntryId = qR.id and 
qR.qId = ci.quizId and qR.quizMode = ci.quizMode and ' . $composeId . ' = ci.composeId GROUP BY(qR.uId) order BY 
sumTaraz DESC');

            for($i = 0; $i < count($sumTarazes); $i++) {

                if($sumTarazes[$i]->uId == $uId) {

                    $r = $i + 1;
                    $sumTaraz = $sumTarazes[$i]->sumTaraz;
                    $k = $i - 1;

                    while ($k >= 0 && $sumTarazes[$k]->sumTaraz == $sumTaraz) {
                        $k--;
                        $r--;
                    }
                    $sumRate = $r;
                    break;
                }
            }

            $pack = true;
        }

        return view('generalKarname', array('quizId' => $quizId, 'status' => $status, 'kindKarname' => $kindKarname,
            'rank' => $rank, 'rankInLessonCity' => $rankInLessonCity, 'rankInLesson' => $rankInLesson, 'pack' => $pack,
            'lessons' => $lessons, 'taraz' => $taraz, 'rankInLessonState' => $rankInLessonState, 'stateRank' => $stateRank,
            'avgs' => $avgs, 'roq' => $roq, 'roq2' => $roq2, 'roq3' => $roq3, 'cityRank' => $cityRank, "totalMark" => $totalMark,
            'avgRate' => $avgRate, 'avgTaraz' => $avgTaraz, 'sumRate' => $sumRate, 'sumTaraz' => $sumTaraz));
    }

    public function quizStatus() {

        $mode = 'show';
        $msg = "";

        if(isset($_POST["addNewStatus"]))
            $mode = 'addNewStatus';

        else if(isset($_POST["doAddStatus"])) {

            $isPicSet = makeValidInput($_POST["isPicSet"]);

            if($isPicSet == "1" && !isset($_FILES["pic"]))
                $msg = "لطفا فایلی را به عنوان تصویر وضعیت انتخاب کنید";

            else if($isPicSet == "0" && !isset($_POST["statusName"]))
                $msg = "لطفا متنی را به عنوان متن وضعیت انتخاب کنید";

            else {

                $quizStatus = new QuizStatus();
                if($isPicSet == "0")
                    $quizStatus->status = makeValidInput($_POST["statusName"]);
                else
                    $quizStatus->status = $_FILES["pic"]["name"];

                $quizStatus->level = makeValidInput($_POST["level"]);

                if (empty($quizStatus->level))
                    $msg = "لطفا تمامی فیلد های لازم را پر نمایید";

                else {

                    $quizStatus->type = makeValidInput($_POST["type"]);
                    $quizStatus->floor = makeValidInput($_POST["floorStatus"]);
                    $quizStatus->ceil = makeValidInput($_POST["ceilStatus"]);
                    $quizStatus->color = makeValidInput($_POST["color"]);
                    $quizStatus->pic = $isPicSet;

                    $file = $_FILES["pic"];

                    $targetFile = __DIR__ .  "/../../../public/status/" . $file["name"];

                    if($isPicSet) {
                        if (!file_exists($targetFile)) {
                            $msg = uploadCheck($targetFile, "pic", "ایجاد وضعیت جدید", 300000, "jpg");
                            if (empty($msg)) {
                                $msg = upload($targetFile, "pic", "ایجاد وضعیت جدید");
                                if (empty($msg)) {
                                    $quizStatus->save();
                                    return Redirect::to('quizStatus');
                                }
                            }
                        }
                    }
                    else {
                        $quizStatus->save();
                        return Redirect::to('quizStatus');
                    }
                }
            }
        }

        else if(isset($_POST["removeStatus"])) {

            $quizStatusId = makeValidInput($_POST["removeStatus"]);
            $quizStatus = QuizStatus::whereId($quizStatusId);

            if($quizStatus != null) {

                if($quizStatus->pic && file_exists(__DIR__ .  "/../../../public/status/" . $quizStatus->status)) {
                    $targetFile = __DIR__ .  "/../../../public/status/" . $quizStatus->status;
                    unlink($targetFile);
                }
                $quizStatus->delete();
            }
        }

        $quizStatus = QuizStatus::all();
        return view('quizStatus', array('quizStatus' => $quizStatus, 'mode' => $mode, 'msg' => $msg));
    }
    
    public function onlineQuizes() {

        $quizes = SystemQuiz::all();

        foreach ($quizes as $quiz) {
            $quiz->startDate = convertStringToDate($quiz->startDate);
            $quiz->startReg = convertStringToDate($quiz->startReg);
            $quiz->endReg = convertStringToDate($quiz->endReg);
            $quiz->startTime = convertStringToTime($quiz->startTime);
        }

        return view('onlineQuizes', array('quizes' => $quizes));

    }

    public function regularQuizes($err = "", $selectedQuiz = "") {

        $quizes = RegularQuiz::all();

        foreach ($quizes as $quiz) {
            $quiz->startDate = convertStringToDate($quiz->startDate);
            $quiz->endDate = convertStringToDate($quiz->endDate);
            $quiz->startReg = convertStringToDate($quiz->startReg);
            $quiz->endReg = convertStringToDate($quiz->endReg);
            $quiz->startTime = convertStringToTime($quiz->startTime);
            $quiz->endTime = convertStringToTime($quiz->endTime);
        }

        return view('createRegularQuiz', array('quizes' => $quizes, "err" => $err, "selectedQuiz" => $selectedQuiz));

    }

    public function doQuizRegistry($quizId, $mode, $status = "nop") {

        if($mode == "system")
            $quiz = SystemQuiz::whereId($quizId);
        else if($mode == "regular")
            $quiz = RegularQuiz::whereId($quizId);
        else
            return Redirect::to('profile');

        $today = getToday();

        if($quiz == null || $quiz->startReg > $today["date"] ||
            $quiz->endReg < $today["date"])
            return Redirect::to('profile');

        include_once 'MoneyController.php';

        if($mode == "regular")
            return view('preTransaction', array('quizId' => $quizId, 'url' => route('regularQuizRegistry'), 'backURL' => route('regularQuizRegistry'), 'status' => $status,
                'total' => getTotalMoney(), 'toPay' => $quiz->price, 'payURL' => route('doQuizRegistryFromAccount', ['mode' => $mode]), 'payURL2' => route('paymentQuiz', ['mode' => $mode])));

        return view('preTransaction', array('quizId' => $quizId, 'url' => route('quizRegistry'), 'backURL' => route('quizRegistry'), 'status' => $status,
            'total' => getTotalMoney(), 'toPay' => $quiz->price, 'payURL' => route('doQuizRegistryFromAccount', ['mode' => $mode]), 'payURL2' => route('paymentQuiz', ['mode' => $mode])));
    }

    public function doChargeAccount() {

        if(isset($_POST["amount"]) && isset($_POST["giftCode"])) {

            include_once 'MoneyController.php';

            $amount = makeValidInput($_POST["amount"]);
            $toPay = $amount;
            $useGift = -1;

            $giftCode = makeValidInput($_POST["giftCode"]);
            if (checkOffCodeValidation($giftCode)) {
                $code = OffCode::whereCode($giftCode)->first();

                if ($code->type == getValueInfo('staticOffCode'))
                    $toPay -= $code->amount;
                else
                    $toPay -= ceil($code->amount * $toPay / 100);
                if ($toPay < 0)
                    $toPay = 0;

                $useGift = $giftCode;
            }

            if ($toPay > 10) {

                $callBackUrl = route('chargeAccountPost', ['additional' => $amount]);

                $res = payment($toPay * 10, $callBackUrl, $useGift);

                if ($res != -1)
                    echo json_encode(['status' => 'ok', 'refId' => $res]);

                else {
                    echo json_encode(['status' => 'nok2']);
                }
                return;
            }

            
            charge($amount, Auth::user()->id, getValueInfo('chargeTransaction'), getValueInfo('money2'));

            if($useGift != -1)
                OffCode::whereCode($useGift)->delete();
            
            echo json_encode(['status' => 'ok2']);
            return;
        }

        echo json_encode(['status' => 'nok1']);
    }

    public function chargeAccountPost($additional) {

        if (isset($_POST["RefId"]) && isset($_POST["ResCode"]) && isset($_POST["SaleOrderId"]) && isset($_POST["SaleReferenceId"]))  {

            if(makeValidInput($_POST["ResCode"]) != 0) {
                return Redirect::to(route('chargeAccountWithStatus', ['status' => 'err']));
            }

            $condition = ['refId' => makeValidInput($_POST["RefId"])];
            $mellat = Mellat::where($condition)->first();

            $mellat->saleReferenceId = makeValidInput($_POST["SaleReferenceId"]);
            $mellat->saleOrderId = makeValidInput($_POST["SaleOrderId"]);
            $mellat->status = 2;
            $mellat->save();

            require_once("lib/nusoap.php");

            $client = new \nusoap_client('https://bpm.shaparak.ir/pgwchannel/services/pgw?wsdl');
            $namespace = 'http://interfaces.core.sw.bps.com/';

            $terminalId = 909350;
            $userName = "irysc";
            $userPassword = "ir99ys";

            $tmp = new OrderId();
            $orderId = rand(1, 1000000000);

            while (OrderId::where('code', '=', $orderId)->count() > 0)
                $orderId = rand(1, 1000000000);

            $tmp->code = $orderId;
            $tmp->save();

            $verifySaleOrderId = $mellat->saleOrderId;
            $verifySaleReferenceId = $mellat->saleReferenceId;

            // Check for an error
            $err = $client->getError();
            if ($err) {
                return Redirect::to(route('chargeAccountWithStatus', ['status' => 'err']));
            }

            $parameters = array(
                'terminalId' => $terminalId,
                'userName' => $userName,
                'userPassword' => $userPassword,
                'orderId' => $orderId,
                'saleOrderId' => $verifySaleOrderId,
                'saleReferenceId' => $verifySaleReferenceId);

            // Call the SOAP method
            $result = $client->call('bpVerifyRequest', $parameters, $namespace);

            // Check for a fault
            if ($client->fault) {
                return Redirect::to(route('chargeAccountWithStatus', ['status' => 'err']));
            }
            else {
                $resultStr = $result;

                $err = $client->getError();
                if ($err) {
                    return Redirect::to(route('chargeAccountWithStatus', ['status' => 'err']));
                }
                else {
                    // Display the result
                    // Update Table, Save Verify Status
                    // Note: Successful Verify means complete successful sale was done.
//					echo "<script>alert('Verify Response is : " . $resultStr . "');</script>";
//					echo "Verify Response is : " . $resultStr;

                    if($resultStr == 0) {

                        include_once 'MoneyController.php';

                        chargeOnline($mellat->amount / 10, Auth::user()->id, getValueInfo('chargeTransaction'),
                            getValueInfo('money2'), $mellat->gift, $additional);

                        $mellat->status = 3;
                        $mellat->save();

                        $tmp = new OrderId();
                        $orderId = rand(1, 1000000000);

                        while (OrderId::where('code', '=', $orderId)->count() > 0)
                            $orderId = rand(1, 1000000000);

                        $tmp->code = $orderId;
                        $tmp->save();

                        $settleSaleOrderId = $mellat->saleOrderId;
                        $settleSaleReferenceId = $mellat->saleReferenceId;

                        // Check for an error
                        $err = $client->getError();
                        if (empty($err)) {

                            $parameters = array(
                                'terminalId' => $terminalId,
                                'userName' => $userName,
                                'userPassword' => $userPassword,
                                'orderId' => $orderId,
                                'saleOrderId' => $settleSaleOrderId,
                                'saleReferenceId' => $settleSaleReferenceId);

                            // Call the SOAP method
                            $result = $client->call('bpSettleRequest', $parameters, $namespace);

                            if(empty($client->fault)) {
                                $resultStr = $result;
                                $err = $client->getError();
                                if (empty($err)) {
                                    if ($resultStr == 0) {
                                        $mellat->status = 4;
                                        $mellat->save();
                                    }
                                }
                            }// end Display the result
                        }

                        return Redirect::to(route('chargeAccountWithStatus', ['status' => 'finish']));

                    }
                }// end Display the result
            }// end Check for errors
        }

        return Redirect::to(route('chargeAccountWithStatus', ['status' => 'err']));
    }

    public function doMultiQuizRegistryFromAccount($mode) {

        if(isset($_POST["quizId"]) && isset($_POST["giftCode"]) && isset($_POST["pack"])) {

            include_once 'MoneyController.php';

            $total = getTotalMoney();
            $qIds = $_POST["quizId"];
            $uId = Auth::user()->id;
            $toPay = 0;
            $pack = makeValidInput($_POST["pack"]);

            if($mode == getValueInfo('systemQuiz')) {
                foreach ($qIds as $qId) {
                    $quiz = SystemQuiz::whereId($qId);
                    if($quiz == null) {
                        echo json_encode(['status' => 'nok5']);
                        return;
                    }

                    $condition = ['uId' => $uId, 'qId' => $qId, 'quizMode' => getValueInfo('systemQuiz')];

                    if(QuizRegistry::where($condition)->count() > 0) {
                        echo json_encode(['status' => 'nok2']);
                        return;
                    }

                    $toPay += $quiz->price;
                }
            }
            elseif($mode == getValueInfo('regularQuiz')) {
                foreach ($qIds as $qId) {

                    $quiz = RegularQuiz::whereId($qId);
                    if($quiz == null) {
                        echo json_encode(['status' => 'nok5']);
                        return;
                    }

                    $condition = ['uId' => $uId, 'qId' => $qId, 'quizMode' => getValueInfo('regularQuiz')];
                    if(QuizRegistry::where($condition)->count() > 0) {
                        echo json_encode(['status' => 'nok2']);
                        return;
                    }

                    $toPay += $quiz->price;
                }
            }
            else {
                echo json_encode(['status' => 'nok6']);
                return;
            }

            $config = ConfigModel::first();

            if($pack == "true" || $pack)
                $toPay = floor($toPay * (100 - $config->percentOfPackage) / 100);
            else
                $toPay = floor($toPay * (100 - $config->percentOfQuizes) / 100);

            $useGift = -1;

            $giftCode = makeValidInput($_POST["giftCode"]);
            if(checkOffCodeValidation($giftCode)) {
                $code = OffCode::whereCode($giftCode)->first();

                if($code->type == getValueInfo('staticOffCode'))
                    $toPay -= $code->amount;
                else
                    $toPay -= ceil($code->amount * $toPay / 100);
                if($toPay < 0)
                    $toPay = 0;

                $useGift = $giftCode;
            }

            if($toPay > $total) {
                echo json_encode(['status' => 'nok1']);
                return;
            }

            if($mode == getValueInfo('systemQuiz')) {
                for ($i = 0; $i < count($qIds); $i++) {
                    quizRegistryOnline(getValueInfo('systemQuizTransaction'), getValueInfo('systemQuiz'), $toPay, Auth::user()->id, getValueInfo('money2'),
                        $qIds[$i], $useGift, false, true, ($i == 0));
                }
            }
            else {
                for ($i = 0; $i < count($qIds); $i++) {
                    quizRegistryOnline(getValueInfo('regularQuizTransaction'), getValueInfo('regularQuiz'), $toPay, Auth::user()->id, getValueInfo('money2'),
                        $qIds[$i], $useGift, false, true, ($i == 0));
                }
            }

            echo json_encode(['status' => 'ok']);
            return;
        }

        echo json_encode(['status' => 'nok3']);
    }

    public function doQuizRegistryFromAccount($mode) {

        if(isset($_POST["quizId"]) && isset($_POST["giftCode"])) {

            include_once 'MoneyController.php';

            $total = getTotalMoney();
            $quizId = makeValidInput($_POST["quizId"]);
            $uId = Auth::user()->id;

            if($mode == "system")
                $quiz = SystemQuiz::whereId($quizId);
            else if($mode == "regular")
                $quiz = RegularQuiz::whereId($quizId);
            else {
                echo json_encode(["status" => "nok5"]);
                return;
            }

            if($quiz == null) {
                echo json_encode(["status" => "nok5"]);
                return;
            }

            $toPay = $quiz->price;
            $useGift = -1;

            $giftCode = makeValidInput($_POST["giftCode"]);
            if(checkOffCodeValidation($giftCode)) {
                $code = OffCode::whereCode($giftCode)->first();

                if($code->type == getValueInfo('staticOffCode'))
                    $toPay -= $code->amount;
                else
                    $toPay -= ceil($code->amount * $toPay / 100);
                if($toPay < 0)
                    $toPay = 0;

                $useGift = $giftCode;
            }

            if($toPay > $total) {
                echo json_encode(["status" => "nok1"]);
                return;
            }

            if($mode == "system")
                $condition = ['uId' => $uId, 'qId' => $quizId, 'quizMode' => getValueInfo('systemQuiz')];
            else
                $condition = ['uId' => $uId, 'qId' => $quizId, 'quizMode' => getValueInfo('regularQuiz')];

            if(QuizRegistry::where($condition)->count() > 0) {
                echo json_encode(["status" => "nok2"]);
                return;
            }

            if($mode == "system")
                quizRegistryOnline(getValueInfo('systemQuizTransaction'), getValueInfo('systemQuiz'), $toPay, Auth::user()->id,
                    getValueInfo('money2'), $quizId, $useGift, false);
            else
                quizRegistryOnline(getValueInfo('regularQuizTransaction'), getValueInfo('regularQuiz'), $toPay, Auth::user()->id,
                    getValueInfo('money2'), $quizId, $useGift, false);

            echo json_encode(["status" => "ok"]);
            return;
        }

        echo json_encode(["status" => "nok3"]);

    }
    
    public function paymentQuiz($mode) {

        if(isset($_POST["quizId"]) && isset($_POST["giftCode"])) {

            include_once 'MoneyController.php';

            $quizId = makeValidInput($_POST["quizId"]);
            $uId = Auth::user()->id;

            if($mode == "system")
                $quiz = SystemQuiz::whereId($quizId);
            else if($mode == "regular")
                $quiz = RegularQuiz::whereId($quizId);
            else {
                echo json_encode(['status' => 'nok1']);
                return;
            }

            if($quiz == null) {
                echo json_encode(['status' => 'nok1']);
                return;
            }

            $toPay = $quiz->price;
            $useGift = -1;

            $giftCode = makeValidInput($_POST["giftCode"]);
            if(checkOffCodeValidation($giftCode)) {
                $code = OffCode::whereCode($giftCode)->first();

                if($code->type == getValueInfo('staticOffCode'))
                    $toPay -= $code->amount;
                else
                    $toPay -= ceil($code->amount * $toPay / 100);
                if($toPay < 0)
                    $toPay = 0;

                $useGift = $giftCode;
            }

            if($mode == "system")
                $condition = ['uId' => $uId, 'qId' => $quizId, 'quizMode' => getValueInfo('systemQuiz')];
            else
                $condition = ['uId' => $uId, 'qId' => $quizId, 'quizMode' => getValueInfo('regularQuiz')];

            if(QuizRegistry::where($condition)->count() > 0) {
                echo json_encode(['status' => 'nok2']);
                return;
            }


            if($toPay > 10 && $toPay > getTotalMoney()) {

                $callBackUrl = route('paymentPostQuiz', ['quizId' => $quizId, 'mode' => $mode]);

                $res = payment(($toPay - getTotalMoney()) * 10, $callBackUrl, $useGift);

                if($res != -1)
                    echo json_encode(['status' => 'ok', 'refId' => $res]);

                else {
                    echo json_encode(['status' => 'nok1']);
                }
                return;
            }

            if($mode == "system")
                quizRegistryOnline(getValueInfo('systemQuizTransaction'), getValueInfo('systemQuiz'), $toPay, Auth::user()->id,
                    getValueInfo('money2'), $quizId, $useGift, false);
            else
                quizRegistryOnline(getValueInfo('regularQuizTransaction'), getValueInfo('regularQuiz'), $toPay, Auth::user()->id,
                    getValueInfo('money2'), $quizId, $useGift, false);

            echo json_encode(['status' => 'ok2']);
            return;
        }

        echo json_encode(['status' => 'nok1']);
    }

    public function multiPaymentQuiz($mode) {

        if(isset($_POST["quizId"]) && isset($_POST["giftCode"]) && isset($_POST["pack"])) {

            include_once 'MoneyController.php';

            $qIds = $_POST["quizId"];
            $uId = Auth::user()->id;
            $toPay = 0;
            $pack = makeValidInput($_POST["pack"]);

            if($mode == getValueInfo('systemQuiz')) {
                foreach ($qIds as $qId) {
                    $quiz = SystemQuiz::whereId($qId);
                    if($quiz == null) {
                        echo json_encode(['status' => 'nok1']);
                        return;
                    }

                    $condition = ['uId' => $uId, 'qId' => $qId, 'quizMode' => getValueInfo('systemQuiz')];

                    if(QuizRegistry::where($condition)->count() > 0) {
                        echo json_encode(['status' => 'nok2']);
                        return;
                    }

                    $toPay += $quiz->price;
                }
            }
            elseif($mode == getValueInfo('regularQuiz')) {
                foreach ($qIds as $qId) {

                    $quiz = RegularQuiz::whereId($qId);
                    if($quiz == null) {
                        echo json_encode(['status' => 'nok1']);
                        return;
                    }

                    $condition = ['uId' => $uId, 'qId' => $qId, 'quizMode' => getValueInfo('regularQuiz')];
                    if(QuizRegistry::where($condition)->count() > 0) {
                        echo json_encode(['status' => 'nok2']);
                        return;
                    }

                    $toPay += $quiz->price;
                }
            }
            else {
                echo json_encode(['status' => 'nok1']);
                return;
            }

            $config = ConfigModel::first();

            if($pack == 1)
                $toPay = floor($toPay * (100 - $config->percentOfPackage) / 100);
            else
                $toPay = floor($toPay * (100 - $config->percentOfQuizes) / 100);

            $useGift = -1;

            $giftCode = makeValidInput($_POST["giftCode"]);
            if(checkOffCodeValidation($giftCode)) {
                $code = OffCode::whereCode($giftCode)->first();

                if($code->type == getValueInfo('staticOffCode'))
                    $toPay -= $code->amount;
                else
                    $toPay -= ceil($code->amount * $toPay / 100);
                if($toPay < 0)
                    $toPay = 0;

                $useGift = $giftCode;
            }

            if($toPay > 100 && $toPay > getTotalMoney() && ($toPay - getTotalMoney()) > 100) {

                $arrTmp['mode'] = $mode;
                $arrTmp['pack'] = $pack;
                for ($i = 0; $i < count($qIds); $i++) {
                    $arrTmp["qId" . ($i + 1)] = $qIds[$i];
                }

                $callBackUrl = route('multiPaymentPostQuiz', $arrTmp);

                $res = payment(($toPay - getTotalMoney()) * 10, $callBackUrl, $useGift);

                if($res == -1)
                    echo json_encode(['status' => 'nok1']);
                else
                    echo json_encode(['status' => 'ok', 'refId' => $res]);
                return;
            }


            if($mode == getValueInfo('systemQuiz')) {
                for ($i = 0; $i < count($qIds); $i++) {
                    quizRegistryOnline(getValueInfo('systemQuizTransaction'), getValueInfo('systemQuiz'), $toPay, Auth::user()->id, getValueInfo('money2'),
                        $qIds[$i], $useGift, false, true, ($i == 0));
                }
            }
            else {
                for ($i = 0; $i < count($qIds); $i++) {
                    quizRegistryOnline(getValueInfo('regularQuizTransaction'), getValueInfo('regularQuiz'), $toPay, Auth::user()->id, getValueInfo('money2'),
                        $qIds[$i], $useGift, false, true, ($i == 0));
                }
            }

            echo json_encode(['status' => 'ok2']);
            return;
        }

        echo json_encode(['status' => 'nok1']);
    }

    public function paymentPostQuiz($quizId, $mode) {

        if (isset($_POST["RefId"]) && isset($_POST["ResCode"]) && isset($_POST["SaleOrderId"]) && isset($_POST["SaleReferenceId"]))  {

            if(makeValidInput($_POST["ResCode"]) != 0) {
                return Redirect::to(route('doQuizRegistryWithStatus', ['quizId' => $quizId, 
                    'mode' => $mode, 'status' => 'err']));
            }

            $condition = ['refId' => makeValidInput($_POST["RefId"])];
            $mellat = Mellat::where($condition)->first();

            $mellat->saleReferenceId = makeValidInput($_POST["SaleReferenceId"]);
            $mellat->saleOrderId = makeValidInput($_POST["SaleOrderId"]);
            $mellat->status = 2;
            $mellat->save();

            require_once("lib/nusoap.php");

            $client = new \nusoap_client('https://bpm.shaparak.ir/pgwchannel/services/pgw?wsdl');
            $namespace = 'http://interfaces.core.sw.bps.com/';

            $terminalId = 909350;
            $userName = "irysc";
            $userPassword = "ir99ys";

            $tmp = new OrderId();
            $orderId = rand(1, 1000000000);

            while (OrderId::where('code', '=', $orderId)->count() > 0)
                $orderId = rand(1, 1000000000);

            $tmp->code = $orderId;
            $tmp->save();

            $verifySaleOrderId = $mellat->saleOrderId;
            $verifySaleReferenceId = $mellat->saleReferenceId;

            // Check for an error
            $err = $client->getError();
            if ($err) {
                return Redirect::to(route('doQuizRegistryWithStatus', ['quizId' => $quizId,
                    'mode' => $mode, 'status' => 'err']));
            }

            $parameters = array(
                'terminalId' => $terminalId,
                'userName' => $userName,
                'userPassword' => $userPassword,
                'orderId' => $orderId,
                'saleOrderId' => $verifySaleOrderId,
                'saleReferenceId' => $verifySaleReferenceId);

            // Call the SOAP method
            $result = $client->call('bpVerifyRequest', $parameters, $namespace);

            // Check for a fault
            if ($client->fault) {
                return Redirect::to(route('doQuizRegistryWithStatus', ['quizId' => $quizId,
                    'mode' => $mode, 'status' => 'err']));
            }
            else {
                $resultStr = $result;

                $err = $client->getError();
                if ($err) {
                    return Redirect::to(route('doQuizRegistryWithStatus', ['quizId' => $quizId,
                        'mode' => $mode, 'status' => 'err']));
                }
                else {
                    // Display the result
                    // Update Table, Save Verify Status
                    // Note: Successful Verify means complete successful sale was done.
//					echo "<script>alert('Verify Response is : " . $resultStr . "');</script>";
//					echo "Verify Response is : " . $resultStr;

                    if($resultStr == 0) {

                        include_once 'MoneyController.php';

                        if($mode == "system") {
                            quizRegistryOnline(getValueInfo('systemQuizTransaction'), getValueInfo('systemQuiz'), $mellat->amount / 10, Auth::user()->id,
                                getValueInfo('money2'), $quizId, $mellat->gift, true);
                        }
                        else
                            quizRegistryOnline(getValueInfo('regularQuizTransaction'), getValueInfo('regularQuiz'), $mellat->amount / 10, Auth::user()->id,
                                getValueInfo('money2'), $quizId, $mellat->gift, true);

                        $mellat->status = 3;
                        $mellat->save();

                        $tmp = new OrderId();
                        $orderId = rand(1, 1000000000);

                        while (OrderId::where('code', '=', $orderId)->count() > 0)
                            $orderId = rand(1, 1000000000);

                        $tmp->code = $orderId;
                        $tmp->save();

                        $settleSaleOrderId = $mellat->saleOrderId;
                        $settleSaleReferenceId = $mellat->saleReferenceId;

                        // Check for an error
                        $err = $client->getError();
                        if (empty($err)) {

                            $parameters = array(
                                'terminalId' => $terminalId,
                                'userName' => $userName,
                                'userPassword' => $userPassword,
                                'orderId' => $orderId,
                                'saleOrderId' => $settleSaleOrderId,
                                'saleReferenceId' => $settleSaleReferenceId);

                            // Call the SOAP method
                            $result = $client->call('bpSettleRequest', $parameters, $namespace);

                            if(empty($client->fault)) {
                                $resultStr = $result;
                                $err = $client->getError();
                                if (empty($err)) {
                                    if ($resultStr == 0) {
                                        $mellat->status = 4;
                                        $mellat->save();
                                    }
                                }
                            }// end Display the result
                        }

                        return Redirect::to(route('doQuizRegistryWithStatus', ['quizId' => $quizId,
                            'mode' => $mode, 'status' => 'finish']));

                    }
                }// end Display the result
            }// end Check for errors
        }

        return Redirect::to(route('doQuizRegistryWithStatus', ['quizId' => $quizId,
            'mode' => $mode, 'status' => 'err']));
    }

    public function multiPaymentPostQuiz($mode, $pack, ... $qIds) {

        if(count($qIds) == 0)
            $qIds = $qIds[0];

        if (isset($_POST["RefId"]) && isset($_POST["ResCode"]) && isset($_POST["SaleOrderId"]) && isset($_POST["SaleReferenceId"]))  {

            $arg = ['mode' => $mode, 'pack' => $pack, 'status' => 'err'];
            $counter = 1;
            foreach ($qIds as $qId) {
                $arg['qId' . ($counter++)] = $qId;
            }

            if(makeValidInput($_POST["ResCode"]) != 0)
                return Redirect::route('doMultiQuizRegistry', $arg);

            $condition = ['refId' => makeValidInput($_POST["RefId"])];
            $mellat = Mellat::where($condition)->first();

            $mellat->saleReferenceId = makeValidInput($_POST["SaleReferenceId"]);
            $mellat->saleOrderId = makeValidInput($_POST["SaleOrderId"]);
            $mellat->status = 2;
            $mellat->save();

            require_once("lib/nusoap.php");

            $client = new \nusoap_client('https://bpm.shaparak.ir/pgwchannel/services/pgw?wsdl');
            $namespace = 'http://interfaces.core.sw.bps.com/';

            $terminalId = 909350;
            $userName = "irysc";
            $userPassword = "ir99ys";

            $tmp = new OrderId();
            $orderId = rand(1, 1000000000);

            while (OrderId::whereCode($orderId)->count() > 0)
                $orderId = rand(1, 1000000000);

            $tmp->code = $orderId;
            $tmp->save();

            $verifySaleOrderId = $mellat->saleOrderId;
            $verifySaleReferenceId = $mellat->saleReferenceId;

            // Check for an error
            $err = $client->getError();
            if ($err)
                return Redirect::route('doMultiQuizRegistry', $arg);


            $parameters = array(
                'terminalId' => $terminalId,
                'userName' => $userName,
                'userPassword' => $userPassword,
                'orderId' => $orderId,
                'saleOrderId' => $verifySaleOrderId,
                'saleReferenceId' => $verifySaleReferenceId);

            // Call the SOAP method
            $result = $client->call('bpVerifyRequest', $parameters, $namespace);

            // Check for a fault
            if ($client->fault)
                return Redirect::route('doMultiQuizRegistry', $arg);

            else {
                $resultStr = $result;

                $err = $client->getError();
                if ($err)
                    return Redirect::route('doMultiQuizRegistry', $arg);
                else {

                    if($resultStr == 0) {

                        include_once 'MoneyController.php';

                        if($mode == getValueInfo('systemQuiz')) {
                            for ($i = 0; $i < count($qIds); $i++) {
                                quizRegistryOnline(getValueInfo('systemQuizTransaction'), getValueInfo('systemQuiz'), $mellat->amount / 10, Auth::user()->id,
                                    getValueInfo('money2'), $qIds[$i], $mellat->gift, true, true, ($i == 0));
                            }
                        }
                        else {
                            for ($i = 0; $i < count($qIds); $i++) {
                                quizRegistryOnline(getValueInfo('regularQuizTransaction'), getValueInfo('regularQuiz'), $mellat->amount / 10, Auth::user()->id,
                                    getValueInfo('money2'), $qIds[$i], $mellat->gift, true, true, ($i == 0));
                            }
                        }

                        $mellat->status = 3;
                        $mellat->save();

                        $tmp = new OrderId();
                        $orderId = rand(1, 1000000000);

                        while (OrderId::whereCode($orderId)->count() > 0)
                            $orderId = rand(1, 1000000000);

                        $tmp->code = $orderId;
                        $tmp->save();

                        $settleSaleOrderId = $mellat->saleOrderId;
                        $settleSaleReferenceId = $mellat->saleReferenceId;

                        // Check for an error
                        $err = $client->getError();
                        if (empty($err)) {

                            $parameters = array(
                                'terminalId' => $terminalId,
                                'userName' => $userName,
                                'userPassword' => $userPassword,
                                'orderId' => $orderId,
                                'saleOrderId' => $settleSaleOrderId,
                                'saleReferenceId' => $settleSaleReferenceId);

                            // Call the SOAP method
                            $result = $client->call('bpSettleRequest', $parameters, $namespace);

                            if(empty($client->fault)) {
                                $resultStr = $result;
                                $err = $client->getError();
                                if (empty($err)) {
                                    if ($resultStr == 0) {
                                        $mellat->status = 4;
                                        $mellat->save();
                                    }
                                }
                            }// end Display the result
                        }

                        $arg["status"] = "finish";
                        return Redirect::route('doMultiQuizRegistry', $arg);

                    }
                }// end Display the result
            }// end Check for errors
        }

        return Redirect::route('profile');
    }

    public function quizEntry() {

        $today = getToday();
        $date = $today["date"];
        $time = $today["time"];
        $uId = Auth::user()->id;

        $quizes = DB::select('select systemQuiz.id, name, startDate, startTime, startReg, endReg from systemQuiz, quizRegistry WHERE systemQuiz.id = qId and uId = ' . $uId .
            ' and startDate = ' . $date . ' and startTime <= ' . $time);

        $out = [];
        $counter = 0;
        foreach ($quizes as $quiz) {
            $timeLen = calcTimeLenQuiz($quiz->id, 'system');
            $reminder = subTimes(sumTimes($quiz->startTime, $timeLen), $time);
            if($reminder <= 0)
                continue;
            $quiz->startDate = convertStringToDate($quiz->startDate);
            $quiz->startTime = convertStringToTime($quiz->startTime);
            $quiz->timeLen = $timeLen;
            $quiz->reminder = $reminder;
            $out[$counter++] = $quiz;
        }

        return view('quizEntry', array('quizes' => $out, 'mode' => 'system'));
    }

    public function doQuiz($quizId) {

        $today = getToday();
        $date = $today["date"];
        $time = $today["time"];
        $uId = Auth::user()->id;

        $quiz = SystemQuiz::whereId($quizId);
        if($quiz == null)
            return Redirect::to('profile');
        
        $condition = ['qId' => $quizId, 'uId' => $uId, 'quizMode' => getValueInfo('systemQuiz')];
        $quizRegistry = QuizRegistry::where($condition)->first();
        if($quizRegistry == null)
            return Redirect::to('profile');

        if($quiz->startDate != $date)
            return Redirect::to(route('showQuizWithOutTime', ['quizId' => $quizId, 'quizMode' => getValueInfo('regularQuiz')]));

        $timeLen = calcTimeLenQuiz($quiz->id, 'system');

        $reminder = subTimes(sumTimes($quiz->startTime, $timeLen), $time);

        if($reminder <= 0)
            return Redirect::to(route('showQuizWithOutTime', ['quizId' => $quizId, 'quizMode' => getValueInfo('regularQuiz')]));

        $roqs = DB::select('select ROQ.result, question.ans as status from ROQ, question where quizId = ' . $quizId . " and uId = " . $uId . " and 
                quizMode = " . getValueInfo('systemQuiz') . " and question.id = ROQ.questionId");

        if($roqs == null || count($roqs) == 0) {
            $this->fillSystemROQ($quizId);
            $roqs = DB::select('select ROQ.result, question.ans as status from ROQ, question where quizId = ' . $quizId . " and uId = " . $uId . " and 
                quizMode = " . getValueInfo('systemQuiz') . " and question.id = ROQ.questionId");
        }

        foreach ($roqs as $roq) {
            if($roq->status == $roq->result)
                $roq->status = 1;
            else
                $roq->status = 0;
        }

        $questions = DB::select('select choicesCount, systemQOQ.mark, question.id, question.questionFile, question.kindQ, question.neededTime as qoqId from question, systemQOQ WHERE questionId = question.id and quizId = ' . $quizId . ' order by systemQOQ.qNo ASC');

        return view('systemQuiz', array('quiz' => $quiz, 'mode' => 'normal', 'questions' => $questions,
            'reminder' => $reminder, 'roqs' => $roqs));

    }

    public function doSelfQuiz($quizId) {

        $quiz = UserCreatedQuiz::whereId($quizId);
        if($quiz == null)
            return Redirect::to('profile');

        $timeLen = calcTimeLenQuiz($quiz->id, 'self') * 60;

        if(empty($quiz->timeEntry)) {
            $reminder = $timeLen;
            $quiz->timeEntry = time();
            $quiz->save();
        }
        else {
            $reminder = $timeLen - (time() - $quiz->timeEntry);
        }

        if($reminder <= 0)
            return Redirect::to(route('showQuizWithOutTime', ['quizId' => $quizId, 'quizMode' => getValueInfo('questionQuiz')]));

        $roqs = DB::select('select s.result, question.ans as status, choicesCount, question.id, question.questionFile, question.kindQ from soldQuestion s, question where quizId = ' . $quizId . " and question.id = s.qId order by s.id ASC");

        if($roqs == null || count($roqs) == 0)
            return Redirect::to('profile');

        foreach ($roqs as $roq) {
            if($roq->status == $roq->result)
                $roq->status = 1;
            else
                $roq->status = 0;
        }

        return view('selfQuiz', array('quiz' => $quiz, 'mode' => 'normal',
            'reminder' => $reminder, 'roqs' => $roqs));

    }

    public function composeQuizes() {

        $composes = ComposeQuiz::all();

        foreach ($composes as $compose) {

            $quizes = ComposeQuizItem::whereComposeId($compose->id)->get();

            if($quizes != null && count($quizes) > 0) {
                foreach ($quizes as $tmp) {
                    if ($tmp->quizMode == getValueInfo('regularQuiz')) {
                        $tmp->quizName = RegularQuiz::whereId($tmp->quizId)->name;
                    } else if ($tmp->quizMode == getValueInfo('systemQuiz')) {
                        $tmp->quizName = SystemQuiz::whereId($tmp->quizId)->name;
                    }
                }
            }

            $compose->items = $quizes;
        }

        return view('composeQuizes', ['composeQuizes' => $composes, 'regulars' => RegularQuiz::all(), 'systems' => SystemQuiz::all()]);
    }

    public function removeCompose() {
        
        if(isset($_POST["composeId"])) {
            ComposeQuiz::destroy(makeValidInput($_POST["composeId"]));
        }

        return Redirect::route('composeQuizes');
    }

    public function deleteFromPackage() {

        if (isset($_POST["qId"]) && isset($_POST["quizMode"])) {

            $quizId = makeValidInput($_POST["qId"]);
            $quizMode = makeValidInput($_POST["quizMode"]);

            $condition = ['quizId' => $quizId, 'quizMode' => $quizMode];
            if (ComposeQuizItem::where($condition)->count() == 0) {
                echo "nok";
                return;
            }

            ComposeQuizItem::where($condition)->delete();

            echo "ok";
        }
    }

    public function addQuizToCompose() {
        
        if(isset($_POST["quizId"]) && isset($_POST["quizMode"]) && isset($_POST["composeId"])) {

            $quizId = makeValidInput($_POST["quizId"]);
            $quizMode = makeValidInput($_POST["quizMode"]);
            $composeId = makeValidInput($_POST['composeId']);

            if(ComposeQuiz::whereId($composeId)->kindQuiz != $quizMode) {
                echo "nok2";
                return;
            }

            $condition = ['quizId' => $quizId, 'quizMode' => $quizMode];
            if(ComposeQuizItem::where($condition)->count() > 0) {
                echo "nok";
                return;
            }

            $tmp = new ComposeQuizItem();
            $tmp->quizId = $quizId;
            $tmp->quizMode = $quizMode;
            $tmp->composeId = $composeId;

            try {
                $tmp->save();
            }
            catch (Exception $x) {}
        }

        echo "ok";
        
    }

    public function addCompose() {

        if(isset($_POST["name"]) && isset($_POST["kindQuiz"])) {

            $name = makeValidInput($_POST["name"]);
            $kindQuiz = makeValidInput($_POST["kindQuiz"]);

            if(ComposeQuiz::whereName($name)->count() == 0) {

                $tmp = new ComposeQuiz();
                $tmp->name = $name;
                $tmp->kindQuiz = $kindQuiz;

                try {
                    $tmp->save();
                }
                catch (Exception $x) {}
            }
        }

        return Redirect::route('composeQuizes');
    }

    public function getComposeListOfQuiz() {

        if(isset($_POST["qId"]) && isset($_POST["quizMode"])) {

            $qId = makeValidInput($_POST["qId"]);
            $quizMode = makeValidInput($_POST["quizMode"]);

            echo json_encode(ComposeQuizItem::whereQuizId($qId)->whereQuizMode($quizMode)->select('composeId')->get());

        }

    }

    public function showQuizWithOutTime($quizId, $quizMode) {

        $uId = Auth::user()->id;

        if($quizMode == getValueInfo('regularQuiz')) {

            $quiz = RegularQuiz::whereId($quizId);

            if ($quiz == null)
                return Redirect::to('profile');


            $today = getToday();
            $date = $today["date"];
            $time = $today["time"];

            if(ROQ::whereQuizId($quizId)->whereUId($uId)->count() == 0) {

                if ($quiz->startDate > $date || ($quiz->startDate == $date && $quiz->startTime > $time) ||
                    $quiz->endDate > $date || ($quiz->endDate == $date && $quiz->endTime > $time)
                )
                    return $this->myQuizes('زمان مرور آزمون مورد نظر هنوز نرسیده است');

            }

            $condition = ['qId' => $quizId, 'uId' => $uId, 'quizMode' => getValueInfo('regularQuiz')];
            $quizRegistry = QuizRegistry::where($condition)->first();

            if($quizRegistry == null)
                return Redirect::to('profile');

            $roqs = DB::select('select r.result, telorance, kindQ, q.ans as status from ROQ r, question q, regularQOQ rq where r.quizId = rq.quizId and r.quizId = ' . $quizId . " and uId = " . $uId . " and 
                rq.questionId = q.id and rq.mark <> 0 and quizMode = " . getValueInfo('regularQuiz') . " and q.id = r.questionId");

            if ($roqs == null || count($roqs) == 0) {
                $this->fillRegularROQ($quizId);

                $roqs = DB::select('select r.result, telorance, kindQ, q.ans as status from ROQ r, question q, regularQOQ rq where r.quizId = rq.quizId and r.quizId = ' . $quizId . " and uId = " . $uId . " and 
                rq.questionId = q.id and rq.mark <> 0 and quizMode = " . getValueInfo('regularQuiz') . " and q.id = r.questionId");
            }

            foreach ($roqs as $roq) {

                if ($roq->kindQ == 1 && $roq->status == $roq->result)
                    $roq->status = 1;
                else if ($roq->kindQ == 0 && $roq->status - $roq->telorance <= $roq->result &&
                    $roq->status + $roq->telorance >= $roq->result
                )
                    $roq->status = 1;
                else
                    $roq->status = 0;
            }

            $questions = DB::select('select telorance, ans, ansFile, choicesCount, question.id, question.questionFile, question.kindQ, question.neededTime as qoqId ' .
                'from question, regularQOQ WHERE mark <> 0 and questionId = question.id and quizId = ' . $quizId . ' order by regularQOQ.qNo ASC');

            foreach ($questions as $question) {

                if($question->kindQ == 1) {
                    $condition = ['questionId' => $question->id, 'result' => $question->ans];
                    $question->correct = ROQ::where($condition)->count();
                    $question->incorrect = DB::select('select count(*) as countNum from ROQ WHERE questionId = ' . $question->id . ' and result <> ' . $question->ans
                        . " and result <> 0")[0]->countNum;

                    $condition = ['questionId' => $question->id, 'result' => 0];
                    $question->white = ROQ::where($condition)->count();
                }
                else if($question->kindQ == 0){
                    $question->correct = DB::select('select count(*) as countNum from ROQ WHERE questionId = ' . $question->id . ' and result >= ' . ($question->ans - $question->telorance) .
                        ' and result <= ' . ($question->ans + $question->telorance))[0]->countNum;
                    $question->incorrect = DB::select('select count(*) as countNum from ROQ WHERE questionId = ' . $question->id . ' and result <> 0 and (result < ' . ($question->ans - $question->telorance) .
                        ' or result > ' . ($question->ans + $question->telorance) . ')')[0]->countNum;

                    $condition = ['questionId' => $question->id, 'result' => 0];
                    $question->white = ROQ::where($condition)->count();
                }
                else {

                    $roqsTmp = ROQ::whereQuestionId($question->id)->select('result')->get();
                    $corrects = $inCorrects = $whites = [];
                    $first = true;

                    foreach ($roqsTmp as $itr) {

                        $itr->result = (string)$itr->result;

                        if($first) {
                            for ($k = 0; $k < strlen($itr->result); $k++) {
                                $corrects[$k] = 0;
                                $inCorrects[$k] = 0;
                                $whites[$k] = 0;
                            }
                            $first = false;
                        }

                        for ($k = 0; $k < strlen($itr->result); $k++) {
                            if ($itr->result[$k] == $question->ans[$k])
                                $corrects[$k] = $corrects[$k] + 1;
                            else if ($itr->result[$k] != 0)
                                $inCorrects[$k] = $inCorrects[$k] + 1;
                            else
                                $whites[$k] = $whites[$k] + 1;
                        }
                    }

                    $question->corrects = $corrects;
                    $question->inCorrects = $inCorrects;
                    $question->whites = $whites;
                }

                $condition = ['uId' => $uId, 'questionId' => $question->id];
                $question->hasLike = (LOK::where($condition)->count() == 1) ? true : false;
                $question->level = getQuestionLevel($question->id);

                $question->likeNo = LOK::whereQuestionId($question->id)->count();

                $question->discussion = route('discussion', ['qId' => $question->id]);
            }

            return view('showQuizWithOutTime', array('quiz' => $quiz, 'questions' => $questions,
                'roqs' => $roqs, 'quizMode' => $quizMode));
        }

        else if($quizMode == getValueInfo('questionQuiz')) {

            $quiz = UserCreatedQuiz::whereId($quizId);

            if ($quiz == null)
                return Redirect::to('profile');

            $roqs = DB::select('select ansFile, s.result, question.ans as status, choicesCount, question.id, question.questionFile, question.kindQ from soldQuestion s, question where quizId = ' . $quizId . " and question.id = s.qId order by s.id ASC");

            if ($roqs == null || count($roqs) == 0)
                return Redirect::to('profile');

            foreach ($roqs as $roq) {
                if ($roq->status == $roq->result)
                    $roq->status = 1;
                else
                    $roq->status = 0;

                $condition = ['questionId' => $roq->id, 'result' => $roq->status];
                $roq->correct = ROQ::where($condition)->count();
                $roq->incorrect = DB::select('select count(*) as countNum from ROQ WHERE questionId = ' . $roq->id . ' and result <> ' . $roq->status
                    . " and result <> 0")[0]->countNum;
                $condition = ['questionId' => $roq->id, 'result' => 0];
                $roq->white = ROQ::where($condition)->count();

                $condition = ['uId' => $uId, 'questionId' => $roq->id];
                $roq->hasLike = (LOK::where($condition)->count() == 1) ? true : false;
                $roq->level = getQuestionLevel($roq->id);

                $roq->likeNo = LOK::whereQuestionId($roq->id)->count();

                $roq->discussion = route('discussion', ['qId' => $roq->id]);
            }

            return view('selfQuiz', array('quiz' => $quiz, 'mode' => 'special',
                'roqs' => $roqs));
        }

        $quiz = SystemQuiz::whereId($quizId);
        if($quiz == null)
            return Redirect::to('profile');

        $condition = ['qId' => $quizId, 'uId' => $uId, 'quizMode' => getValueInfo('systemQuiz')];
        $quizRegistry = QuizRegistry::where($condition)->first();
        if($quizRegistry == null)
            return Redirect::to('profile');


        $roqs = DB::select('select ROQ.result, question.ans as status from ROQ, question where quizId = ' . $quizId . " and uId = " . $uId . " and 
                quizMode = " . getValueInfo('systemQuiz') . " and question.id = ROQ.questionId");

        if($roqs == null || count($roqs) == 0) {
            $this->fillSystemROQ($quizId);
            $roqs = DB::select('select ROQ.result, question.ans as status from ROQ, question where quizId = ' . $quizId . " and uId = " . $uId . " and 
                quizMode = " . getValueInfo('systemQuiz') . " and question.id = ROQ.questionId");
        }

        foreach ($roqs as $roq) {
            if($roq->status == $roq->result)
                $roq->status = 1;
            else
                $roq->status = 0;
        }

        $questions = DB::select('select ans, ansFile, choicesCount, systemQOQ.mark, question.id, question.questionFile, question.kindQ, question.neededTime as qoqId from question, systemQOQ WHERE questionId = question.id and quizId = ' . $quizId . ' order by systemQOQ.qNo ASC');

        foreach ($questions as $question) {

            $condition = ['questionId' => $question->id, 'result' => $question->ans];
            $question->correct = ROQ::where($condition)->count();
            $question->incorrect = DB::select('select count(*) as countNum from ROQ WHERE questionId = ' . $question->id . ' and result <> ' . $question->ans
                . " and result <> 0")[0]->countNum;
            $condition = ['questionId' => $question->id, 'result' => 0];
            $question->white = ROQ::where($condition)->count();

            $condition = ['uId' => $uId, 'questionId' => $question->id];
            $question->hasLike = (LOK::where($condition)->count() == 1) ? true : false;
            $question->level = getQuestionLevel($question->id);

            $question->likeNo = LOK::whereQuestionId($question->id)->count();

            $question->discussion = route('discussion', ['qId' => $question->id]);
        }

        return view('showQuizWithOutTime', array('quiz' => $quiz, 'questions' => $questions, 'quizMode' => $quizMode,
            'roqs' => $roqs));
    }

    public function deleteFromQuiz() {

        if(isset($_POST["id"])) {

            try {
                QuizRegistry::destroy(makeValidInput($_POST["id"]));
                echo "ok";
            }
            catch (\Exception $x) {}
        }

    }

    public function doRegularQuiz($quizId) {

        $today = getToday();
        $date = $today["date"];
        $time = $today["time"];
        $uId = Auth::user()->id;

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
            ))) {

//            if($uId != 50 && $uId != 4783 && $uId != 4738 && $uId != 4707)
                return Redirect::to(route('showQuizWithOutTime', ['quizId' => $quizId, 'quizMode' => getValueInfo('regularQuiz')]));
        }

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

        $reminder = $timeLen * 60 - time() + $timeEntry;

        if($reminder <= 0)
            return Redirect::to(route('showQuizWithOutTime', ['quizId' => $quizId, 'quizMode' => getValueInfo('regularQuiz')]));

        $roqs = ROQ2::whereUId($uId)->whereQuizId($quizId)->first();

        $questions = DB::select('select choicesCount, question.id, question.questionFile, question.kindQ, question.neededTime as qoqId from question, regularQOQ WHERE questionId = question.id and quizId = ' . $quizId . ' order by regularQOQ.qNo ASC');

        if($roqs == null) {

            $tmpResult = "";
            $roqs = [];

            for ($i = 0; $i < count($questions) - 1; $i++) {

                $str = "";

                if($questions[$i]->kindQ == 2) {
                    for($j = 0; $j < $questions[$i]->choicesCount; $j++)
                        $str .= "0";
                }
                else
                    $str .= "0";

                $roqs[$i] = $str;
                $tmpResult .= $str . "-";
            }

            if(count($questions) > 0) {

                $str = "";

                if($questions[count($questions) - 1]->kindQ == 2) {
                    for($j = 0; $j < $questions[count($questions) - 1]->choicesCount; $j++)
                        $str .= "0";
                }
                else
                    $str .= "0";

                $tmpResult .= $str;
                $roqs[count($questions) - 1] = $str;
            }

            $tmpROQ2 = new ROQ2();
            $tmpROQ2->uId = $uId;
            $tmpROQ2->quizId = $quizId;
            $tmpROQ2->result = $tmpResult;
            $tmpROQ2->save();
            $verify = Hash::make($tmpROQ2->id);
        }
        else {
            $tmpROQ2 = [];
            $verify = Hash::make($roqs->id);
            $tmpResult = explode('-', $roqs->result);
            for ($i = 0; $i < count($tmpResult); $i++) {
                $tmpROQ2[$i] = $tmpResult[$i];
            }

            $roqs = $tmpROQ2;
        }

        return view('regularQuiz', array('quiz' => $quiz, 'mode' => 'normal', 'questions' => $questions, 'uId' => $uId,
            'reminder' => $reminder, 'roqs' => $roqs, 'verify' => $verify));

    }

    public function getOnlineStanding() {

        if(isset($_POST["quizId"])) {

            $quizId = makeValidInput($_POST["quizId"]);

            $uIds = DB::select('SELECT uId as name, SUM(point) as rate FROM `ROQ` WHERE quizId = ' . $quizId . ' GROUP BY(uId) ORDER by rate DESC');

            foreach ($uIds as $uId) {
                $condition = ['quizId' => $quizId, 'uId' => $uId->name, 'quizMode' => '1'];
                $roq = ROQ::where($condition)->select('point')->get();
                $counter = 0;
                $tmp = [];
                foreach ($roq as $itr) {
                    $tmp[$counter++] = $itr->point;
                }
                $uId->roq = $tmp;
                $uId->name = User::whereId($uId->name)->username;
            }

            echo json_encode($uIds);
        }
    }

    public function submitAnsSystemQuiz() {

        if(isset($_POST["questionId"]) && isset($_POST["quizId"]) && isset($_POST["newVal"])) {

            $questionId = makeValidInput($_POST["questionId"]);
            $question = Question::whereId($questionId);
            $quizId = makeValidInput($_POST["quizId"]);

            if($question == null) {
                echo "nok";
                return;
            }

            $condition = ['questionId' => $questionId, 'uId' => Auth::user()->id,
                'quizId' => $quizId, 'quizMode' => getValueInfo('systemQuiz')];

            $roq = ROQ::where($condition)->first();

            if($roq != null) {

                $newVal = makeValidInput($_POST["newVal"]);

                if($roq->status == 0 && $roq->result == 0) {
                    $roq->result = $newVal;
                    $quiz = SystemQuiz::whereId($quizId);
                    $condition = ['questionId' => $question->id, 'quizId' => $quizId];
                    $mark = SystemQOQ::where($condition)->first()->mark;
                    $timeLen = calcTimeLenQuiz($quizId, 'system');
                    $time = getToday()["time"];
                    $reminder = subTimes(sumTimes($quiz->startTime, $timeLen), $time);

                    if($question->kindQ == 1) {
                        if ($newVal == $question->ans) {
                            $roq->point = $mark * $reminder / 60;
                            echo "correct";
                        } else {
                            $roq->point = -1 * $mark / ($question->choicesCount + 1);
                            echo "incorrect";
                        }
                    }
                    else {
                        if($newVal >= $question->ans - $question->telorance && $newVal <= $question->ans + $question->telorance) {
                            $roq->point = $mark * $reminder / 60;
                            echo "correct";
                        }
                        else {
                            $roq->point = -1 * $mark / 3;
                            echo "incorrect";
                        }
                    }

                    $roq->save();
                }
                else
                    echo "noAccess";

                return;
            }
        }
        echo "nok";
    }
    
    public function submitAllAnsRegularQuiz() {

        if(isset($_POST["newVals"]) && isset($_POST["quizId"]) && isset($_POST["uId"]) &&
            isset($_POST["verify"])) {

            $roq = ROQ2::whereUId(makeValidInput($_POST["uId"]))->whereQuizId(makeValidInput($_POST["quizId"]))->first();

            if($roq != null) {

                if(!Hash::check($roq->id, $_POST["verify"])) {
                    echo "nok3";
                    return;
                }

                $roq->result = makeValidInput($_POST["newVals"]);
                try {
                    $roq->save();
                    echo "ok";
                }
                catch (\Exception $x) {
                    echo $x->getMessage();
                }
                return;
            }
        }

        echo "nok2";
    }

    public function transfer() {

        $roq2 = ROQ2::all();

        foreach ($roq2 as $itr) {

            $str = $itr->result;
            $counter = 1;
            for($i = 0; $i < strlen($str); $i++) {
                $tmp = new ROQ;
                $tmp->uId = $itr->uId;
                $tmp->result = $str[$i];
                $tmp->questionId = RegularQOQ::whereQuizId($itr->quizId)->whereQNo($counter++)->first()->questionId;
                $tmp->quizId = $itr->quizId;
                $tmp->status = 1;
                $tmp->quizMode = 2;
                $tmp->save();
            }

        }

    }

    public function submitAnsRegularQuiz() {

        if(isset($_POST["roqId"]) && isset($_POST["newVal"])) {

            $roqId = makeValidInput($_POST["roqId"]);

            $roq = ROQ2::whereId($roqId);

            if($roq != null) {

                $newVal = makeValidInput($_POST["newVal"]);
                
                if($roq->status == 0) {

//                    if($question->kindQ == 1 && ($newVal > $question->choicesCount || $newVal < 0)) {
//                        echo "nok3";
//                        return;
//                    }

                    $roq->result = $newVal;
                    $roq->save();
                    echo "ok";
                }
                else
                    echo "noAccess";

                return;
            }
        }

        echo "nok2";
    }

    public function submitAnsSelfQuiz() {

        if(isset($_POST["questionId"]) && isset($_POST["quizId"]) && isset($_POST["newVal"])) {

            $questionId = makeValidInput($_POST["questionId"]);
            $question = Question::whereId($questionId);
            $quizId = makeValidInput($_POST["quizId"]);

            if($question == null) {
                echo "nok";
                return;
            }

            $condition = ['qId' => $questionId, 'quizId' => $quizId];

            $roq = SoldQuestion::where($condition)->first();

            if($roq != null) {

                $newVal = makeValidInput($_POST["newVal"]);

                if($question->kindQ == 1 && ($newVal > $question->choicesCount || $newVal < 0)) {
                    echo "nok2";
                    return;
                }

                $roq->result = $newVal;
                $roq->save();
                return;
            }
        }

        echo "nok";
    }

    public function finishQuiz() {

        $today = getToday();
        $date = $today["date"];
        $time = $today["time"];

        $quizes = DB::select('select systemQuiz.id, name, startDate, startTime, startReg, endReg from systemQuiz 
          WHERE startDate < ' . $date . ' or (startDate = ' . $date . ' and startTime < ' . $time . ')');

        $out = [];
        $counter = 0;
        $systemQuiz = getValueInfo('systemQuiz');

        foreach ($quizes as $quiz) {
            $timeLen = calcTimeLenQuiz($quiz->id, 'system');
            $reminder = subTimes(sumTimes($quiz->startTime, $timeLen), $time);
            if($reminder > 0 && $quiz->startDate == $date)
                continue;

            $condition = ['quizId' => $quiz->id, 'quizMode' => $systemQuiz];
            $roqTmp = ROQ::where($condition)->first();
            if($roqTmp == null || count($roqTmp) == 0 || $roqTmp->status == 1)
                continue;

            $quiz->startDate = convertStringToDate($quiz->startDate);
            $quiz->startTime = convertStringToTime($quiz->startTime);
            $quiz->timeLen = $timeLen;
            $quiz->reminder = $reminder;
            $out[$counter++] = $quiz;
        }

        return view('finishQuiz', array('quizes' => $out));

    }

    public function doFinishQuiz() {

        if(isset($_POST["quizId"])) {

            $quiz = SystemQuiz::whereId(makeValidInput($_POST["quizId"]));

            if($quiz == null)
                return;

            $today = getToday();
            $date = $today["date"];
            $time = $today["time"];

            $timeLen = calcTimeLenQuiz($quiz->id, 'system');
            $reminder = subTimes(sumTimes($quiz->startTime, $timeLen), $time);
            if($reminder > 0 && $quiz->startDate == $date)
                return;

            $condition = ['quizId' => $quiz->id, 'quizMode' => getValueInfo('systemQuiz')];
            try {
                ROQ::where($condition)->update(array('status' => 1));
                echo "ok";
            }
            catch (Exception $x) {}
        }

    }

    private function fillSystemROQ($quizId) {

        $qoqs = SystemQOQ::where('quizId', '=', $quizId)->orderBy('qNo', 'ASC')->get();
        $uId = Auth::user()->id;
        $quizMode = getValueInfo('systemQuiz');

        foreach ($qoqs as $qoq) {

            $questionTmp = Question::whereId($qoq->questionId);

            $tmp = new ROQ();
            $tmp->questionId = $qoq->questionId;

            if($questionTmp->kindQ == 1)
                $tmp->result = 0;
            else
                $tmp->result = "";

            $tmp->uId = $uId;
            $tmp->quizId = $quizId;
            $tmp->status = 0;
            $tmp->quizMode = $quizMode;
            $tmp->save();
        }
    }

    private function fillRegularROQ($quizId) {

        $qoqs = RegularQOQ::where('quizId', '=', $quizId)->orderBy('qNo', 'ASC')->get();
        $uId = Auth::user()->id;
        $quizMode = getValueInfo('regularQuiz');

        foreach ($qoqs as $qoq) {

            $questionTmp = Question::whereId($qoq->questionId);

            $tmp = new ROQ();
            $tmp->questionId = $qoq->questionId;

            if($questionTmp->kindQ == 1)
                $tmp->result = 0;
            else
                $tmp->result = "";

            $tmp->uId = $uId;
            $tmp->quizId = $quizId;
            $tmp->status = 0;
            $tmp->quizMode = $quizMode;
            $tmp->save();
        }
    }

    public function checkGiftCard() {

        if(isset($_POST["giftCode"]) && isset($_POST["total"])) {

            $giftCode = makeValidInput($_POST["giftCode"]);
            include_once 'MoneyController.php';
            if(checkOffCodeValidation($giftCode)) {

                $total = makeValidInput($_POST["total"]);
                $code = OffCode::whereCode($giftCode)->first();

                if($code->type == getValueInfo('staticOffCode'))
                    $total -= $code->amount;
                else
                    $total -= ceil($code->amount * $total / 100);
                if($total < 0)
                    $total = 0;
                echo json_encode(['status' => 'ok', 'total' => $total]);
                return;
            }
        }
        echo json_encode(["status" => 'nok']);
    }

    public function showQuizRegistry() {

        $date = getToday()["date"];
        $uId = Auth::user()->id;

        $composes = DB::select('select DISTINCT c.id, c.name from ' .
            ' composeQuiz c, systemQuiz s, composeQuizItem qI WHERE qI.composeId = c.id and c.kindQuiz = ' .
            getValueInfo('systemQuiz') . ' and s.id = qI.quizId and qI.quizMode = c.kindQuiz and s.startReg <= ' . $date .
            ' and s.endReg >= ' . $date . ' and not exists (select * from quizRegistry qR where s.id = qR.qId and qR.quizMode = ' .
            getValueInfo('systemQuiz') . ' and qR.uId = ' . $uId . ')'
        );

        $config = ConfigModel::first();

        foreach ($composes as $compose) {

            $compose->registerable = DB::select('select s.id, s.name, s.startDate, s.startTime, s.startReg, s.endReg, s.price from ' .
                ' systemQuiz s, composeQuizItem qI WHERE qI.composeId = ' . $compose->id . ' and s.id = qI.quizId and ' .
                ' qI.quizMode = ' . getValueInfo('systemQuiz') . ' and s.startReg <= ' . $date .
                ' and s.endReg >= ' . $date . ' and not exists (select * from quizRegistry qR where s.id = qR.qId and qR.quizMode = ' .
                getValueInfo('systemQuiz') . ' and qR.uId = ' . $uId . ')'
            );

            $totalPrice = 0;
            
            foreach ($compose->registerable as $quiz) {
                $quiz->startDate = convertStringToDate($quiz->startDate);
                $quiz->startTime = convertStringToTime($quiz->startTime);
                $quiz->startReg = convertStringToDate($quiz->startReg);
                $quiz->endReg = convertStringToDate($quiz->endReg);
                $totalPrice += $quiz->price;
            }

            $compose->totalPrice = floor($totalPrice * (100 - $config->percentOfPackage) / 100);
        }

        return view('quizRegistry', array('composes' => $composes, 'mode' => 'system',
            'percentOfQuizes' => $config->percentOfQuizes,
            'percentOfCompose' => $config->percentOfPackage));
    }

    public function regularQuizRegistry() {

        $date = getToday()["date"];
        $uId = Auth::user()->id;

        $config = ConfigModel::first();

        $composes = DB::select('select DISTINCT c.id, c.name from ' .
            ' composeQuiz c, regularQuiz r, composeQuizItem qI WHERE qI.composeId = c.id and c
.kindQuiz = ' . getValueInfo('regularQuiz') . ' and r.id = qI.quizId and qI.quizMode = c.kindQuiz and r.startReg <= ' . $date . ' and r.endReg >= ' . $date . ' and not exists (select * from quizRegistry qR where r.id = qR.qId and qR.quizMode = ' .
            getValueInfo('regularQuiz') . ' and qR.uId = ' . $uId . ')'
        );

        foreach ($composes as $compose) {

            $compose->registerable = DB::select('select s.id, s.name, s.startDate, s.startTime, s.endTime, s.startReg, s.endReg, s.price, s.endDate from ' . ' regularQuiz s, composeQuizItem qI
 WHERE qI.composeId = ' . $compose->id . ' and s.id = qI.quizId and ' .
                ' qI.quizMode = ' . getValueInfo('regularQuiz') . ' and s.startReg <= ' . $date .
                ' and s.endReg >= ' . $date . ' and not exists (select * from quizRegistry qR where s.id = qR.qId and qR.quizMode = ' . getValueInfo('regularQuiz') . ' and qR.uId = ' . $uId . ')'
            );

            $totalPrice = 0;

            foreach ($compose->registerable as $quiz) {
                $quiz->startDate = convertStringToDate($quiz->startDate);
                $quiz->startTime = convertStringToTime($quiz->startTime);
                $quiz->endDate = convertStringToDate($quiz->endDate);
                $quiz->startReg = convertStringToDate($quiz->startReg);
                $quiz->endReg = convertStringToDate($quiz->endReg);
                $quiz->endTime = convertStringToTime($quiz->endTime);
                $totalPrice += $quiz->price;
            }

            $compose->totalPrice = floor($totalPrice * (100 - $config->percentOfPackage) / 100);
        }

        return view('quizRegistry', array('composes' => $composes, 'mode' => 'regular',
            'percentOfQuizes' => $config->percentOfQuizes,
            'percentOfCompose' => $config->percentOfPackage));
    }

    public function selectiveQuizRegistry() {

        if(isset($_POST["qIds"]) && isset($_POST["mode"])) {
            
            $mode = makeValidInput($_POST["mode"]);
            $qIdsFinal = [];
            $qIds = $_POST["qIds"];
            $counter = 0;
            $uId = Auth::user()->id;
            if($mode == getValueInfo('regularQuiz')) {
                foreach ($qIds as $qId) {
                    $qId = makeValidInput($qId);
                    if(RegularQuiz::whereId($qId) != null && QuizRegistry::whereQId($qId)->whereUId($uId)->whereQuizMode
                        ($mode)->count() == 0)
                        $qIdsFinal[$counter++] = $qId;
                }
            }

            else {
                foreach ($qIds as $qId) {
                    $qId = makeValidInput($qId);
                    if(SystemQuiz::whereId($qId) != null && QuizRegistry::whereQId($qId)->whereUId($uId)->whereQuizMode
                        ($mode)->count() == 0)
                        $qIdsFinal[$counter++] = $qId;
                }
            }

            if(count($qIdsFinal) > 0) {

                $arr = ['mode' => $mode, 'pack' => 0, 'status' => 'nop'];
                for ($i = 1; $i <= count($qIdsFinal); $i++)
                    $arr['qId' . $i] = $qIdsFinal[($i - 1)];

                echo json_encode(['status' => 'ok', 'url' => route('doMultiQuizRegistry', $arr)]);
                return;
            }

            echo json_encode(['status' => 'nok1']);
            return;
        }

        echo json_encode(['status' => 'nok']);
    }

    public function doComposeQuizRegistry($composeId) {

        $compose = ComposeQuiz::whereId($composeId);
        if($compose == null)
            return Redirect::route('profile');

        $date = getToday()["date"];
        $uId = Auth::user()->id;

        $quizes = DB::select('select s.id from ' . ' regularQuiz s, composeQuizItem qI
 WHERE qI.composeId = ' . $compose->id . ' and s.id = qI.quizId and ' .
            ' qI.quizMode = ' . getValueInfo('regularQuiz') . ' and s.startReg <= ' . $date .
            ' and s.endReg >= ' . $date . ' and not exists (select * from quizRegistry qR where s.id = qR.qId and qR.quizMode = ' . getValueInfo('regularQuiz') . ' and qR.uId = ' . $uId . ')'
        );

        if($quizes == null || count($quizes) == 0)
            return Redirect::route('profile');

        $counter = 0;
        $qIds = [];

        foreach ($quizes as $quiz)
             $qIds[$counter++] = $quiz->id;

        return $this->doMultiQuizRegistry($compose->kindQuiz, true, 'nop', $qIds);

    }

    public function doMultiQuizRegistry($mode, $pack, $status, ... $qIds) {

        if(count($qIds) == 1)
            $qIds = $qIds[0];

        $today = getToday();
        $toPay = 0;

        if($mode == getValueInfo('systemQuiz')) {
            foreach ($qIds as $qId) {
                $quiz = SystemQuiz::whereId($qId);
                if($quiz == null || $quiz->startReg > $today["date"] ||
                    $quiz->endReg < $today["date"])
                    return Redirect::route('profile');
                $toPay += $quiz->price;
            }
        }
        else {
            foreach ($qIds as $qId) {
                $quiz = RegularQuiz::whereId($qId);
                if($quiz == null || $quiz->startReg > $today["date"] ||
                    $quiz->endReg < $today["date"])
                    return Redirect::route('profile');
                $toPay += $quiz->price;
            }
        }

        $config = ConfigModel::first();

        include_once 'MoneyController.php';

        if($pack)
            $toPay = floor($toPay * (100 - $config->percentOfPackage) / 100);
        else
            $toPay = floor($toPay * (100 - $config->percentOfQuizes) / 100);

        if($mode == getValueInfo('regularQuiz'))
            return view('preTransaction', array('quizId' => $qIds, 'url' => route('regularQuizRegistry'), 'backURL' => route('regularQuizRegistry'), 'status' => $status, 'multi' => true, 'pack' => $pack,
                'total' => getTotalMoney(), 'toPay' => $toPay, 'payURL' => route('doMultiQuizRegistryFromAccount', ['mode' => $mode]), 'payURL2' => route('multiPaymentQuiz', ['mode' => $mode])));

        return view('preTransaction', array('quizId' => $qIds, 'url' => route('quizRegistry'), 'backURL' => route('quizRegistry'), 'status' => $status, 'multi' => true, 'pack' => $pack,
            'total' => getTotalMoney(), 'toPay' => $toPay, 'payURL' => route('doMultiQuizRegistryFromAccount', ['mode' => $mode]), 'payURL2' => route('multiPaymentQuiz', ['mode' => $mode])));

    }

    public function useGiftCard() {

    }

    public function editQuiz() {

        if(isset($_POST["sTime"]) && isset($_POST["sDate"]) &&
            isset($_POST["name"]) && isset($_POST["price"]) && isset($_POST["sDateReg"]) &&
            isset($_POST["eDateReg"]) && isset($_POST["quizId"])) {
            
            $sDate = composeDate(makeValidInput($_POST["sDate"]));

            $sDateReg = composeDate(makeValidInput($_POST["sDateReg"]));
            $eDateReg = composeDate(makeValidInput($_POST["eDateReg"]));

            if($sDateReg > $eDateReg) {
                echo "زمان آغاز ثبت نام آزمون باید قبل از آن باشد";
                return;
            }

            if($sDate <= $sDateReg) {
                echo "زمان آغاز ثبت نام آزمون باید قبل از شروع آن باشد";
                return;
            }

            if($sDate <= $eDateReg) {
                echo "زمان اتمام ثبت نام آزمون باید قبل از شروع آن باشد";
                return;
            }

            $quiz = SystemQuiz::whereId(makeValidInput($_POST["quizId"]));
            $quiz->name = makeValidInput($_POST["name"]);
            $quiz->startDate = $sDate;
            $quiz->startReg = $sDateReg;
            $quiz->endReg = $eDateReg;
            $quiz->startTime = composeTime(makeValidInput($_POST["sTime"]));
            $quiz->price = makeValidInput($_POST["price"]);

            try {
                $quiz->save();
                echo "ok";
                return;
            }
            catch (Exception $x) {}
        }

        echo "مشکلی در انجام عملیات مورد نظر رخ داده است";
        
    }

    public function editQuizRegular() {

        if(isset($_POST["sTime"]) && isset($_POST["sDate"]) && isset($_POST["eDate"]) && isset($_POST["eTime"]) &&
            isset($_POST["name"]) && isset($_POST["price"]) && isset($_POST["sDateReg"]) &&
            isset($_POST["eDateReg"]) && isset($_POST["quizId"])) {

            $sDate = composeDate(makeValidInput($_POST["sDate"]));
            $eDate = composeDate(makeValidInput($_POST["eDate"]));

            if($sDate > $eDate) {
                echo "زمان شروع آزمون باید قبل از آن باشد";
                return;
            }

            $sTime = composeTime(makeValidInput($_POST["sTime"]));
            $eTime = composeTime(makeValidInput($_POST["eTime"]));

            if($sDate == $eDate) {
                if($sTime >= $eTime) {
                    echo "ساعت شروع آزمون باید قبل از آن باشد";
                    return;
                }
            }

            $sDateReg = composeDate(makeValidInput($_POST["sDateReg"]));
            $eDateReg = composeDate(makeValidInput($_POST["eDateReg"]));

            if($sDateReg > $eDateReg) {
                echo "زمان آغاز ثبت نام آزمون باید قبل از آن باشد";
                return;
            }

            if($sDate <= $sDateReg) {
                echo "زمان آغاز ثبت نام آزمون باید قبل از شروع آن باشد";
                return;
            }

            if($sDate <= $eDateReg) {
                echo "زمان اتمام ثبت نام آزمون باید قبل از شروع آن باشد";
                return;
            }

            $quiz = RegularQuiz::whereId(makeValidInput($_POST["quizId"]));
            $quiz->name = makeValidInput($_POST["name"]);
            $quiz->startDate = $sDate;
            $quiz->endDate = $eDate;
            $quiz->startReg = $sDateReg;
            $quiz->endReg = $eDateReg;
            $quiz->startTime = $sTime;
            $quiz->endTime = $eTime;
            $quiz->price = makeValidInput($_POST["price"]);

            try {
                $quiz->save();
                echo "ok";
                return;
            }
            catch (Exception $x) {}
        }

        echo "مشکلی در انجام عملیات مورد نظر رخ داده است";

    }
    
    public function addQuiz() {

        if(isset($_POST["sTime"]) && isset($_POST["sDate"]) &&
            isset($_POST["name"]) && isset($_POST["price"]) && isset($_POST["sDateReg"]) &&
            isset($_POST["eDateReg"])) {

            $sDate = composeDate(makeValidInput($_POST["sDate"]));
            $sDateReg = composeDate(makeValidInput($_POST["sDateReg"]));
            $eDateReg = composeDate(makeValidInput($_POST["eDateReg"]));

            if($sDateReg > $eDateReg) {
                echo "زمان آغاز ثبت نام آزمون باید قبل از اتمام آن باشد";
                return;
            }

            if($sDate <= $sDateReg) {
                echo "زمان آغاز ثبت نام آزمون باید قبل از شروع آن باشد";
                return;
            }

            if($sDate <= $eDateReg) {
                echo "زمان اتمام ثبت نام آزمون باید قبل از شروع آن باشد";
                return;
            }

            $quiz = new SystemQuiz();
            $quiz->name = makeValidInput($_POST["name"]);
            $quiz->startDate = $sDate;
            $quiz->startReg = $sDateReg;
            $quiz->endReg = $eDateReg;
            $quiz->startTime = composeTime(makeValidInput($_POST["sTime"]));
            $quiz->price = makeValidInput($_POST["price"]);

            try {
                $quiz->save();
                echo "ok";
                return;
            }
            catch (Exception $x) {}
        }

        echo "مشکلی در انجام عملیات مورد نظر رخ داده است";

    }

    public function addQuizRegular() {

        if(isset($_POST["sTime"]) && isset($_POST["eTime"]) && isset($_POST["sDate"]) && isset($_POST["eDate"]) &&
            isset($_POST["name"]) && isset($_POST["price"]) && isset($_POST["sDateReg"]) &&
            isset($_POST["eDateReg"])) {

            $sDate = composeDate(makeValidInput($_POST["sDate"]));
            $eDate = composeDate(makeValidInput($_POST["eDate"]));

            if($sDate > $eDate) {
                echo "زمان آغاز آزمون باید قبل از اتمام آن باشد";
                return;
            }

            $sTime = composeTime(makeValidInput($_POST["sTime"]));
            $eTime = composeTime(makeValidInput($_POST["eTime"]));

            if($sDate == $eDate) {
                if($sTime >= $eTime) {
                    echo "ساعت آغاز آزمون باید قبل از اتمام آن باشد";
                    return;
                }
            }

            $sDateReg = composeDate(makeValidInput($_POST["sDateReg"]));
            $eDateReg = composeDate(makeValidInput($_POST["eDateReg"]));

            if($sDateReg > $eDateReg) {
                echo "زمان آغاز ثبت نام آزمون باید قبل از اتمام آن باشد";
                return;
            }

            if($sDate <= $sDateReg) {
                echo "زمان آغاز ثبت نام آزمون باید قبل از شروع آن باشد";
                return;
            }

            if($sDate <= $eDateReg) {
                echo "زمان اتمام ثبت نام آزمون باید قبل از شروع آن باشد";
                return;
            }

            $quiz = new RegularQuiz();
            $quiz->name = makeValidInput($_POST["name"]);
            $quiz->startDate = $sDate;
            $quiz->endDate = $eDate;
            $quiz->startReg = $sDateReg;
            $quiz->endReg = $eDateReg;
            $quiz->startTime = $sTime;
            $quiz->endTime = $eTime;
            $quiz->price = makeValidInput($_POST["price"]);

            try {
                $quiz->save();
                echo "ok";
                return;
            }
            catch (Exception $x) {}
        }

        echo "مشکلی در انجام عملیات مورد نظر رخ داده است";

    }

    public function removeQFromSystemQ() {

        if(isset($_POST["quizId"]) && isset($_POST["questionId"])) {

            $quizId = makeValidInput($_POST["quizId"]);
            $condition = ['quizId' => $quizId, 'questionId' => makeValidInput($_POST["questionId"])];

            $qoq = SystemQOQ::where($condition)->first();
            if($qoq != null) {

                try {
                    DB::select('update systemQOQ set qNo = qNo - 1 WHERE quizId = ' . $quizId . ' and qNo > ' . $qoq->qNo);
                    $qoq->delete();
                    echo "ok";
                    return;
                }
                catch (Exception $x) {}
            }
        }
        echo "nok";
    }

    public function addBatchQToQ() {

        if(isset($_POST["quizId"]) && isset($_FILES["batchQ"])) {

            $quizId = makeValidInput($_POST["quizId"]);

            if(RegularQuiz::whereId($quizId) == null)
                return Redirect::to(route('profile'));

            if(isset($_FILES["batchQ"])) {

                $file = $_FILES["batchQ"]["name"];

                if(!empty($file)) {

                    $path = __DIR__ . '/../../../public/tmp/' . $file;

                    $err = uploadCheck($path, "batchQ", "اکسل افزودن دسته ای سوالات به آزمون", 20000000, "xlsx");

                    if (empty($err)) {
                        upload($path, "batchQ", "اکسل افزودن دسته ای سوالات به آزمون");
                        $excelReader = PHPExcel_IOFactory::createReaderForFile($path);
                        $excelObj = $excelReader->load($path);
                        $workSheet = $excelObj->getSheet(0);
                        $questions = array();
                        $lastRow = $workSheet->getHighestRow();
                        $cols = $workSheet->getHighestColumn();

                        if ($cols < 'B') {
                            unlink($path);
                            $err = "تعداد ستون های فایل شما معتبر نمی باشد";
                        } else {
                            for ($row = 1; $row <= $lastRow; $row++) {
                                $questions[$row - 1][0] = $workSheet->getCell('A' . $row)->getValue();
                                $questions[$row - 1][1] = $workSheet->getCell('B' . $row)->getValue();
                            }
                            unlink($path);
                            $err = $this->doAddBatchQToQ($questions, $quizId);
                        }
                    }
                }
            }

            if(empty($err))
                $err = "لطفا فایل اکسل مورد نیاز را آپلود نمایید";

            return $this->regularQuizes($err, $quizId);
        }
        return Redirect::to(route('profile'));
    }

    public function doAddBatchQToQ($questions, $quizId) {

        $errs = "بجز سوالات زیر بقیه به درستی به آزمون افزوده شدند" . "<br/>";
        $emptyErr = true;

        foreach ($questions as $question) {

            $tmp = Question::whereOrganizationId($question[0])->first();
            if($tmp == null) {
                $errs .= $question[0] . ', ';
                $emptyErr = false;
                continue;
            }

            $qoq = new RegularQOQ();
            $qoq->quizId = $quizId;
            $qoq->questionId = $tmp->id;
            $qoq->mark = $question[1];

            $lastQ = RegularQOQ::where('quizId', '=', $quizId)->orderBy('qNo', 'DESC')->first();

            if($lastQ != null)
                $qNo = $lastQ->qNo + 1;
            else
                $qNo = 1;

            $qoq->qNo = $qNo;

            try {
                $qoq->save();
            }
            catch (Exception $x) {
                $errs .= $question[0] . ', ';
                $emptyErr = false;
            }
        }

        if($emptyErr)
            $errs = "کلیه سوالات به درستی به آزمون افزوده شدند";
        return $errs;

    }

    public function removeQFromRegularQ() {

        if(isset($_POST["quizId"]) && isset($_POST["questionId"])) {

            $quizId = makeValidInput($_POST["quizId"]);
            $condition = ['quizId' => $quizId, 'questionId' => makeValidInput($_POST["questionId"])];
            $qoq = RegularQOQ::where($condition)->first();

            if($qoq != null) {

                try {
                    DB::update('update regularQOQ set qNo = qNo - 1 WHERE quizId = ' . $quizId . ' and qNo > ' . $qoq->qNo);
                    $qoq->delete();
                    echo "ok";
                    return;
                }
                catch (Exception $x) {
                    echo $x->getMessage();
                }
            }
        }
        echo "nok";
    }

    public function getQuizQuestions() {

        if(isset($_POST["quizId"])) {

            $quizId = makeValidInput($_POST["quizId"]);
//            $quiz = SystemQuiz::whereId($quizId);
//            $today = getToday();

//            if($quiz->startDate < $today["date"] || ($quiz->startDate == $today["date"] && $quiz->startTime < $today["time"])) {
//                echo "timeOut";
//                return;
//            }

            $questions = DB::select('select systemQOQ.mark as mark, systemQOQ.qNo as qNo, questionFile, ans, users.level as authorLevel, ansFile, question.level,
                neededTime, question.id from question, systemQOQ, users WHERE users.id = author and
                quizId = ' .$quizId . ' and questionId = question.id order By systemQOQ.qNo ASC');

            foreach ($questions as $question) {
                if($question->authorLevel == getValueInfo('adminLevel') ||
                    $question->authorLevel == getValueInfo('superAdminLevel')) {

                    $question->questionFile = 'images/questions/system/' . $question->questionFile;
                    $question->ansFile = 'images/answers/system/' . $question->ansFile;
                }
                else {
                    $question->questionFile = 'images/questions/student/' . $question->questionFile;
                    $question->ansFile = 'images/answers/system/' . $question->ansFile;
                }
            }

            echo json_encode($questions);

        }

    }

    public function getRegularQuizQuestions() {

        if(isset($_POST["quizId"])) {

            $quizId = makeValidInput($_POST["quizId"]);

            $questions = DB::select('select regularQOQ.qNo as qNo, mark, organizationId, questionFile, ans, users.level as authorLevel, ansFile, question.level,
                neededTime, question.id from question, regularQOQ, users WHERE users.id = author and
                quizId = ' .$quizId . ' and questionId = question.id order By regularQOQ.qNo ASC');

            foreach ($questions as $question) {
                if($question->authorLevel == getValueInfo('adminLevel') ||
                    $question->authorLevel == getValueInfo('superAdminLevel')) {

                    $question->questionFile = 'images/questions/system/' . $question->questionFile;
                    $question->ansFile = 'images/answers/system/' . $question->ansFile;
                }
                else {
                    $question->questionFile = 'images/questions/student/' . $question->questionFile;
                    $question->ansFile = 'images/answers/system/' . $question->ansFile;
                }
            }

            echo json_encode($questions);

        }

    }

    public function changeMarkQ() {
        
        if(isset($_POST["quizId"]) && isset($_POST["questionId"]) && isset($_POST["val"])) {

            $condition = ['quizId' => makeValidInput($_POST["quizId"]),
                'questionId' => makeValidInput($_POST["questionId"])];
            
            $qoq = SystemQOQ::where($condition)->first();

            if($qoq != null) {
                $qoq->mark = makeValidInput($_POST["val"]);
                $qoq->save();
                echo "ok";
            }

        }

    }

    public function changeQMarkRegular() {

        if(isset($_POST["quizId"]) && isset($_POST["questionId"]) && isset($_POST["val"])) {

            $condition = ['quizId' => makeValidInput($_POST["quizId"]),
                'questionId' => makeValidInput($_POST["questionId"])];

            $qoq = RegularQOQ::where($condition)->first();

            if($qoq != null) {
                $qoq->mark = makeValidInput($_POST["val"]);
                $qoq->save();
                echo "ok";
            }

        }
    }
    
    public function changeQNoRegularQuiz() {
        if(isset($_POST["quizId"]) && isset($_POST["questionId"]) && isset($_POST["val"])) {

            $quizId = makeValidInput($_POST["quizId"]);
            $limit = RegularQOQ::where('quizId', '=', $quizId)->count();
            $val = makeValidInput($_POST["val"]);

            if($val > $limit)
                return;

            $condition = ['quizId' => $quizId,
                'questionId' => makeValidInput($_POST["questionId"])];

            $question = RegularQOQ::where($condition)->first();
            if($question == null)
                return;

            $currQNo = $question->qNo;

            if($val > $currQNo) {
                DB::select('update regularQOQ set qNo = qNo - 1 WHERE quizId = ' . $quizId . " and 
                    qNo > " . $currQNo . " and qNo <= " . $val);
                $question->qNo = $val;
                $question->save();
                echo "ok";
            }
            else if($val < $currQNo) {
                DB::select('update regularQOQ set qNo = qNo + 1 WHERE quizId = ' . $quizId . " and 
                    qNo >= " . $val . " and qNo < " . $currQNo);
                $question->qNo = $val;
                $question->save();
                echo "ok";
            }
        }
    }

    public function changeQNo() {

        if(isset($_POST["quizId"]) && isset($_POST["questionId"]) && isset($_POST["val"])) {

            $quizId = makeValidInput($_POST["quizId"]);
            $limit = SystemQOQ::where('quizId', '=', $quizId)->count();
            $val = makeValidInput($_POST["val"]);

            if($val > $limit)
                return;

            $condition = ['quizId' => $quizId,
                'questionId' => makeValidInput($_POST["questionId"])];

            $question = SystemQOQ::where($condition)->first();
            if($question == null)
                return;

            $currQNo = $question->qNo;

            if($val > $currQNo) {
                DB::select('update systemQOQ set qNo = qNo - 1 WHERE quizId = ' . $quizId . " and 
                    qNo > " . $currQNo . " and qNo <= " . $val);
                $question->qNo = $val;
                $question->save();
                echo "ok";
            }
            else if($val < $currQNo) {
                DB::select('update systemQOQ set qNo = qNo + 1 WHERE quizId = ' . $quizId . " and 
                    qNo >= " . $val . " and qNo < " . $currQNo);
                $question->qNo = $val;
                $question->save();
                echo "ok";
            }
        }
    }

    public function deleteQuiz() {
        
        if(isset($_POST["quizId"])) {

            $quizId = makeValidInput($_POST["quizId"]);
            $quiz = SystemQuiz::whereId($quizId);
            $today = getToday();

            if($quiz->startReg <= $today["date"]) {
                echo "timeOut";
                return;
            }

            try{
                SystemQuiz::destroy($quizId);
                echo "ok";
                return;
            }
            catch (Exception $x) {}
        }
        
    }

    public function deleteRegularQuiz() {

        if(isset($_POST["quizId"])) {

            $quizId = makeValidInput($_POST["quizId"]);
            $quiz = RegularQuiz::whereId($quizId);
            $today = getToday();

            if($quiz->startReg <= $today["date"]) {
                echo "timeOut";
                return;
            }

            try{
                RegularQuiz::destroy($quizId);
                echo "ok";
                return;
            }
            catch (Exception $x) {}
        }

    }
    
    public function getSubjectQuestions() {
        if(isset($_POST["sId"])) {

            $questions = DB::select('select questionFile, users.level as authorLevel, ansFile, question.level, neededTime, question.id, ans from 
                question, SOQ, users, controllerActivity WHERE sId = ' . makeValidInput($_POST["sId"]) . ' and users.id = author and  
                SOQ.qId = question.id and controllerActivity.qId = SOQ.qId');

            foreach ($questions as $question) {

                if($question->authorLevel == getValueInfo('adminLevel') ||
                    $question->authorLevel == getValueInfo('superAdminLevel')) {

                    $question->questionFile = 'images/questions/system/' . $question->questionFile;
                    $question->ansFile = 'images/answers/system/' . $question->ansFile;
                }
                else {
                    $question->questionFile = 'images/questions/student/' . $question->questionFile;
                    $question->ansFile = 'images/answers/system/' . $question->ansFile;
                }
            }

            echo json_encode($questions);

        }
    }

    public function fetchQuestionByOrganizationId() {

        if(isset($_POST["organizationId"])) {

            $questions = DB::select("select questionFile, users.level as authorLevel, ansFile, question.level, neededTime, question.id, ans from 
                question, users, controllerActivity WHERE controllerActivity.qId = question.id and organizationId = '" . makeValidInput($_POST["organizationId"]) . "' and users.id = author");

            foreach ($questions as $question) {

                if($question->authorLevel == getValueInfo('adminLevel') ||
                    $question->authorLevel == getValueInfo('superAdminLevel')) {

                    $question->questionFile = 'images/questions/system/' . $question->questionFile;
                    $question->ansFile = 'images/answers/system/' . $question->ansFile;
                }
                else {
                    $question->questionFile = 'images/questions/student/' . $question->questionFile;
                    $question->ansFile = 'images/answers/system/' . $question->ansFile;
                }
            }

            echo json_encode($questions);

        }
    }

    public function doAddQuestionToQuiz() {

        if(isset($_POST["quizId"]) && isset($_POST["questionId"])) {

            $quizId = makeValidInput($_POST["quizId"]);
            $quiz = SystemQuiz::whereId($quizId);
            $today = getToday();
            $questionId = makeValidInput($_POST["questionId"]);

//            if($quiz->startDate < $today["date"] || ($quiz->startDate == $today["date"] && $quiz->startTime < $today["time"])) {
//                echo json_encode(['status' => "nok"]);
//                return;
//            }

            if(ControllerActivity::where('qId', '=', $questionId)->count() == 0) {
                echo json_encode(['status' => "nok"]);
                return;
            }


            $systemQOQ = new SystemQOQ();
            $systemQOQ->quizId = $quizId;
            $systemQOQ->questionId = $questionId;
            $lastQ = SystemQOQ::where('quizId', '=', $quizId)->orderBy('qNo', 'DESC')->first();
            
            if($lastQ != null)
                $qNo = $lastQ->qNo + 1;
            else
                $qNo = 1;

            $systemQOQ->qNo = $qNo;

            try {
                $systemQOQ->save();
                echo json_encode(['status' => "ok", 'qNo' => $qNo]);
                return;
            }
            catch (Exception $x) {}
        }

        echo json_encode(['status' => "nok"]);
    }

    public function doAddQuestionToRegularQuiz() {

        if(isset($_POST["quizId"]) && isset($_POST["questionId"])) {

            $quizId = makeValidInput($_POST["quizId"]);
//            $quiz = RegularQuiz::whereId($quizId);
//            $today = getToday();
            $questionId = makeValidInput($_POST["questionId"]);

//            if($quiz->startDate < $today["date"] || ($quiz->startDate == $today["date"] && $quiz->startTime < $today["time"])) {
//                echo json_encode(['status' => "nok"]);
//                return;
//            }

            if(ControllerActivity::where('qId', '=', $questionId)->count() == 0) {
                echo json_encode(['status' => "nok"]);
                return;
            }

            $qoq = new RegularQOQ();
            $qoq->quizId = $quizId;
            $qoq->questionId = $questionId;
            $lastQ = RegularQOQ::where('quizId', '=', $quizId)->orderBy('qNo', 'DESC')->first();

            if($lastQ != null)
                $qNo = $lastQ->qNo + 1;
            else
                $qNo = 1;

            $qoq->qNo = $qNo;

            try {
                $qoq->save();
                echo json_encode(['status' => "ok", 'qNo' => $qNo]);
                return;
            }
            catch (Exception $x) {}
        }

        echo json_encode(['status' => "nok"]);
    }

    public function getSystemQuizDetails() {

        if(isset($_POST["quizId"])) {

            $quizId = makeValidInput($_POST["quizId"]);
            $quiz = SystemQuiz::whereId($quizId);
//            $today = getToday();

//            if($quiz->startDate < $today["date"] || ($quiz->startDate == $today["date"] && $quiz->startTime < $today["time"])) {
//                echo "timeOut";
//                return;
//            }

            $quiz->startDate = convertStringToDate($quiz->startDate);
            $quiz->startReg = convertStringToDate($quiz->startReg);
            $quiz->endReg = convertStringToDate($quiz->endReg);
            $quiz->startTime = convertStringToTime($quiz->startTime);
            
            echo json_encode($quiz);
        }
    }

    public function getRegularQuizDetails() {

        if(isset($_POST["quizId"])) {

            $quizId = makeValidInput($_POST["quizId"]);
            $quiz = RegularQuiz::whereId($quizId);
//            $today = getToday();


//            if($quiz->startDate < $today["date"] ||
//                ($quiz->startDate == $today["date"] && $quiz->startTime < $today["time"])) {
//
//                echo "timeOut";
//                return;
//            }

            $quiz->startDate = convertStringToDate($quiz->startDate);
            $quiz->endDate = convertStringToDate($quiz->endDate);
            $quiz->startReg = convertStringToDate($quiz->startReg);
            $quiz->endReg = convertStringToDate($quiz->endReg);
            $quiz->startTime = convertStringToTime($quiz->startTime);
            $quiz->endTime = convertStringToTime($quiz->endTime);

            echo json_encode($quiz);
        }
    }

    public function getSuggestionQuestionsCount() {

        if(isset($_POST["filter"]) && isset($_POST["needed"]) && isset($_POST["id"])) {

            $filter = makeValidInput($_POST["filter"]);
            $id = makeValidInput($_POST["id"]);

            $gradeId = -1;
            if($filter == "grade")
                $gradeId = $id;

            $sId = -1;
            if($filter == "subject")
                $sId = $id;

            $lId = -1;
            if($filter == "lesson")
                $lId = $id;

            $level = -1;
            if(isset($_POST["level"]))
                $level = makeValidInput($_POST["level"]);
            
            $needed = makeValidInput($_POST["needed"]);

//            $like = makeValidInput($_POST["like"]);
            $like = false;

            echo json_encode(suggestionQuestionsCount($gradeId, $lId, $sId, Auth::user()->id, $level, $like, $needed));
            return;
        }

        echo 0;

    }

    public function createCustomQuiz() {
        return view('createCustomQuiz', array('grades' => Grade::all()));
    }

    public function transferFromROQ2ToROQ() {

        $roq2 = ROQ2::all();

        foreach ($roq2 as $itr) {

            $str = explode('-', $itr->result);

            for($i = 0; $i < count($str); $i++) {

                if(count(explode('?', $str[$i])) > 1) {
                    $tmpStr = "";
                    $tmpArr = explode('?', $str[$i]);

                    for($j = 0; $j < count($tmpArr); $j++)
                        $tmpStr .= $tmpArr[$j];

                    $str[$i] = $tmpStr;
                }

                $tmp = new ROQ();
                $tmp->uId = $itr->uId;
                $tmp->quizId = $itr->quizId;
                $tmp->result = $str[$i];

                if($itr->quizId == 206 && $i >= 107)
                    continue;

                $qoq = RegularQOQ::whereQuizId($itr->quizId)->whereQNo($i + 1)->select('questionId')->first();
                if($qoq == null)
                    dd($i . ' ' . $itr->id);
                else
                    $tmp->questionId = $qoq->questionId;
                $tmp->quizMode = 2;
                $tmp->status = true;
                try {
                    $tmp->save();
                }
                catch (\Exception $x) {
                    dd($x->getMessage());
                }
            }

        }

        DB::delete('DELETE t1 FROM ROQ t1
        INNER JOIN
    ROQ t2 
WHERE
    t1.id < t2.id AND t1.uId = t2.uId and t1.quizId = t2.quizId and t1.questionId = t2.questionId;');


    }
}