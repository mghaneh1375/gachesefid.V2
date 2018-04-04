<?php

namespace App\Http\Controllers;

use App\models\LOK;
use App\models\OffCode;
use App\models\QuizStatus;
use App\models\RegularQOQ;
use App\models\RegularQuiz;
use App\models\SoldQuestion;
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
use Illuminate\Support\Facades\Redirect;
use PHPExcel_IOFactory;
use soapclient;

class QuizController extends Controller {

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

        foreach ($users as $user) {
            $tmp = DB::select('select lesson.name, lesson.coherence, taraz.percent, taraz.taraz from taraz, lesson WHERE taraz.qEntryId = ' . $user->id .
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

        return view('ranking', array('users' => $users, 'quizName' => RegularQuiz::whereId($quizId)->name));

    }

    public function rankingSelectQuiz() {

        $today = getToday();
        $date = $today["date"];
        $time = $today["time"];

        $quizes = DB::select('select id, name from regularQuiz WHERE endDate < ' . $date . ' or (endDate = ' . $date . ' and endTime < ' . $time . ')');

        return view('rankingSelectQuiz', array('quizes' => $quizes));
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
                        
                        $quizQuestions = DB::select('select questionId from regularQOQ WHERE mark = 1 and quizId = ' . $key . " order by qNo ASC");

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

        $myQuiz = QuizRegistry::whereUId($uId)->get();
        $regularQuizMode = getValueInfo('regularQuiz');

        foreach ($myQuiz as $itr) {

            if($itr->quizMode == $regularQuizMode) {

                $itr->mode = "regular";
                $itr->quiz = RegularQuiz::whereId($itr->qId);
                $itr->quiz->timeLen = calcTimeLenQuiz($itr->quiz->id, 'regular');

                if(($itr->quiz->startDate < $date && $itr->quiz->endDate > $date) ||
                    ($itr->quiz->startDate < $date && $itr->quiz->endDate >= $date && $itr->quiz->endTime > $time) ||
                    ($itr->quiz->startDate == $date && $itr->quiz->starTime <= $time && (
                            ($itr->quiz->startDate == $itr->quiz->endDate && $itr->quiz->endTime > $time) ||
                            ($itr->quiz->startDate != $itr->quiz->endDate) ||
                            ($itr->quiz->endDate == $date && $itr->quiz->endTime > $time)
                        )
                    )) {

                    $condition = ['qId' => $itr->quiz->id, 'uId' => $uId, 'quizMode' => $regularQuizMode];
                    $quizRegistry = QuizRegistry::where($condition)->first();

                    $timeLen = calcTimeLenQuiz($itr->quiz->id, 'regular');

                    if($quizRegistry->timeEntry == "") {
                        $itr->quizEntry = 1;
                    }
                    else {
                        $timeEntry = $quizRegistry->timeEntry;
                        $reminder = $timeLen * 60 - time() + $timeEntry;
                        if($reminder <= 0)
                            $itr->quizEntry = -2;
                        else
                            $itr->quizEntry = 1;
                    }
                }
                else if($itr->quiz->startDate > $date ||
                    ($itr->quiz->startDate == $date && $itr->quiz->starTime > $time)) {
                    $itr->quizEntry = -1;
                }
                else {
                    $itr->quizEntry = -2;
                }

                $itr->quiz->startDate = convertStringToDate($itr->quiz->startDate);
                $itr->quiz->endDate = convertStringToDate($itr->quiz->endDate);
                $itr->quiz->startTime = convertStringToTime($itr->quiz->startTime);
                $itr->quiz->endTime = convertStringToTime($itr->quiz->endTime);
            }
            
            else {

                $itr->mode = "system";
                $itr->quiz = SystemQuiz::whereId($itr->qId);
                $itr->quiz->timeLen = calcTimeLenQuiz($itr->quiz->id, 'system');

                if($itr->quiz->startDate == $date) {

                    if($itr->quiz->startTime <= $time) {

                        $itr->quiz->reminder = subTimes(sumTimes($itr->quiz->startTime, $itr->quiz->timeLen), $time);

                        if ($itr->quiz->reminder <= 0)
                            $itr->quizEntry = -2;
                        else
                            $itr->quizEntry = 1;
                    }
                    else {
                        $itr->quizEntry = -1;
                    }
                }
                else {
                    if($itr->quiz->startDate > $date)
                        $itr->quizEntry = -1;
                    else
                        $itr->quizEntry = -2;
                }

                $itr->quiz->startDate = convertStringToDate($itr->quiz->startDate);
                $itr->quiz->startTime = convertStringToTime($itr->quiz->startTime);

            }


        }

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
        
        return view('quizEntry', array('quizes' => $myQuiz, 'err' => $err, 'selfQuizes' => $myQuiz2));

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
                $msg = "پاسخ برگ شما به سایت ارسال نشده است";
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

        $quizes = array();
        $conditions = ['uId' => $uId, 'quizMode' => getValueInfo('regularQuiz')];
        $myQuizes = QuizRegistry::where($conditions)->select('qId')->get();
        $quizes = array();
        for($i = 0; $i < count($myQuizes); $i++)
            $quizes[$i] = RegularQuiz::where('id', '=', $myQuizes[$i]->qId)->select('id', 'name')->first();
        return view('karname', array('quizes' => $quizes, 'msg' => $msg, 'selectedQuiz' => $quizId));
    }

    private function showSubjectKarname($uId, $quizId, $kindKarname, $lId) {

        $status = array();
        $avgs = array();

        $cityId = RedundantInfo1::whereUId($uId)->first()->cityId;

        if($kindKarname->subjectStatus)
            $status = QuizStatus::whereLevel(2)->get();

        if($kindKarname->subjectAvg &&  $kindKarname->subjectMaxPercent) {
            if($kindKarname->subjectMinPercent)
                $avgs = DB::select('select SUM(percent) / count(*) as avg, MAX(percent) as maxPercent, MIN(percent) as minPercent FROM subjectsPercent, subject WHERE qId = ' . $quizId . ' and subject.id = sId and subject.lessonId = ' . $lId . ' GROUP by(sId)');
            else
                $avgs = DB::select('select SUM(percent) / count(*) as avg, MAX(percent) as maxPercent FROM subjectsPercent, subject WHERE qId = ' . $quizId . ' and subject.id = sId and subject.lessonId = ' . $lId . ' GROUP by(sId)');
        }
        else if($kindKarname->subjectAvg) {
            if($kindKarname->subjectMinPercent)
                $avgs = DB::select('select SUM(percent) / count(*) as avg, MIN(percent) as minPercent FROM subjectsPercent WHERE qId = ' . $quizId . ' and subject.id = sId and subject.lessonId = ' . $lId . ' GROUP by(sId)');
            else
                $avgs = DB::select('select SUM(percent) / count(*) as avg FROM subjectsPercent WHERE qId = ' . $quizId . ' and subject.id = sId and subject.lessonId = ' . $lId . ' GROUP by(sId)');
        }

        $cityRank = array();
        $stateRank = array();
        $countryRank = array();

        $subjects = $this->getSubjectsQuiz($quizId, $lId);

        if($kindKarname->subjectCityRank) {
            $counter = 0;
            foreach ($subjects as $subject) {
                $tmp = DB::select('SELECT subjectsPercent.uId, subjectsPercent.percent as taraz from redundantInfo1 rd, subjectsPercent WHERE rd.uId = subjectsPercent.uId and rd.cityId = ' . $cityId . ' and subjectsPercent.qId = ' . $quizId . ' and subjectsPercent.sId = ' . $subject->id . ' ORDER by subjectsPercent.percent DESC');
                $cityRank[$counter++] = $this->getRank($tmp, $uId);
            }
        }

        if($kindKarname->subjectStateRank) {
            $counter = 0;
            $stateId = State::whereId(City::whereId($cityId)->stateId)->id;
            foreach ($subjects as $subject) {
                $tmp = DB::select('SELECT subjectsPercent.uId, subjectsPercent.percent as taraz from redundantInfo1 rd, city ci, subjectsPercent WHERE rd.uId = subjectsPercent.uId and rd.cityId = ci.id and ci.stateId = ' . $stateId . ' and subjectsPercent.qId = ' . $quizId . ' and subjectsPercent.sId = ' . $subject->id . ' ORDER by subjectsPercent.percent DESC');
                $stateRank[$counter++] = $this->getRank($tmp, $uId);
            }
        }

        if($kindKarname->subjectCountryRank) {
            $counter = 0;
            foreach ($subjects as $subject) {
                $tmp = DB::select('SELECT subjectsPercent.uId, subjectsPercent.percent as taraz from subjectsPercent WHERE subjectsPercent.qId = ' . $quizId . ' and subjectsPercent.sId = ' . $subject->id . ' ORDER by subjectsPercent.percent DESC');
                $countryRank[$counter++] = $this->getRank($tmp, $uId);
            }
        }

        $regularQuizMode = getValueInfo('regularQuiz');

        $inCorrects =  DB::select('SELECT count(*) as inCorrects, SOQ.sId as target FROM ROQ, question, SOQ, subject WHERE ROQ.quizMode = ' . $regularQuizMode . ' and ROQ.quizId = ' . $quizId . ' and ROQ.questionId = question.id and question.ans <> ROQ.result and ROQ.result <> 0 and ROQ.uId = ' . $uId . ' and question.id = SOQ.qId and subject.id = SOQ.sId and subject.lessonId = ' . $lId . ' group by(subject.id)');
        $corrects =  DB::select('SELECT count(*) as corrects, SOQ.sId as target FROM ROQ, question, SOQ, subject WHERE ROQ.quizMode = ' . $regularQuizMode . ' and ROQ.quizId = ' . $quizId . ' and ROQ.questionId = question.id and question.ans = ROQ.result and ROQ.uId = ' . $uId . ' and question.id = SOQ.qId and subject.id = SOQ.sId and subject.lessonId = ' . $lId . ' group by(subject.id)');
        $total =  DB::select('SELECT count(*) as total, SOQ.sId as target FROM ROQ, question, SOQ, subject WHERE ROQ.quizMode = ' . $regularQuizMode . ' and ROQ.quizId = ' . $quizId . ' and ROQ.questionId = question.id and ROQ.uId = ' . $uId . ' and question.id = SOQ.qId and subject.id = SOQ.sId and subject.lessonId = ' . $lId . ' group by(subject.id)');

        $roq = $this->getResultOfSpecificContainer($total, $corrects, $inCorrects);

        $totalMark = 20;

        if($kindKarname->subjectMark)
            $totalMark = 20;

        $minusMark = 1;

        return view('subjectKarname', array('quizId' => $quizId, 'status' => $status, 'roq' => $roq, 'subjects' => $subjects,
            'kindKarname' => $kindKarname, 'avgs' => $avgs, 'cityRank' => $cityRank, 'stateRank' => $stateRank,
            'countryRank' => $countryRank, 'totalMark' => $totalMark, 'minusMark' => $minusMark));
    }

    private function showQuestionKarname($uId, $quizId) {

        $regularQuizMode = getValueInfo('regularQuiz');

         $qInfos = DB::select("select question.id, question.ans, ROQ.result ".
            "from question, ROQ WHERE ROQ.quizId = " . $quizId . " and " .
            "ROQ.questionId = question.id and ROQ.quizMode = " . $regularQuizMode . " and ROQ.uId = " . $uId .
            " order by ROQ.id ASC");

        if(count($qInfos) == 0)
            return Redirect::to(route('seeResult'));

        $condition = ['questionId' => $qInfos[0]->id, 'quizId' => $quizId, 'quizMode' => $regularQuizMode];
        $total = ROQ::where($condition)->count();

        foreach ($qInfos as $qInfo) {

            $condition = ['questionId' => $qInfo->id, 'quizId' => $quizId, 'quizMode' => $regularQuizMode,
                'result' => 0];
            $qInfo->white = ROQ::where($condition)->count();

            $condition = ['questionId' => $qInfo->id, 'quizId' => $quizId, 'quizMode' => $regularQuizMode,
                'result' => $qInfo->ans];
            $qInfo->correct = ROQ::where($condition)->count();
            
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
        $avgs = [];
        $stateId = -1;
        $rankInLessonCity = array();
        $rankInLessonState = array();

        $cityId = RedundantInfo1::whereUId($uId)->first();

        if(count($cityId) == 0)
            $cityId = City::first()->id;
        else
            $cityId = $cityId->cityId;

        if($kindKarname->lessonCityRank)
            $cityRank = calcRankInCity($quizId, $uId, $cityId);

        if($kindKarname->lessonStateRank) {
            $stateId = State::whereId(City::whereId($cityId)->stateId)->id;
            $stateRank = calcRankInState($quizId, $uId, $stateId);
        }

        if($kindKarname->lessonAvg &&  $kindKarname->lessonMaxPercent) {
            if($kindKarname->lessonMinPercent)
                $avgs = DB::select('select SUM(percent) / count(*) as avg, MAX(percent) as maxPercent, MIN(percent) as minPercent FROM taraz, quizRegistry WHERE quizRegistry.qId = ' . $quizId . ' and quizRegistry.id  = taraz.qEntryId GROUP by(taraz.lId)');
            else
                $avgs = DB::select('select SUM(percent) / count(*) as avg, MAX(percent) as maxPercent FROM taraz, quizRegistry WHERE quizRegistry.qId = ' . $quizId . ' and quizRegistry.id  = taraz.qEntryId GROUP by(taraz.lId)');
        }
        else if($kindKarname->lessonAvg) {
            if($kindKarname->lessonMinPercent)
                $avgs = DB::select('select SUM(percent) / count(*) as avg, MIN(percent) as minPercent FROM taraz, quizRegistry WHERE quizRegistry.qId = ' . $quizId . ' and quizRegistry.id  = taraz.qEntryId GROUP by(taraz.lId)');
            else
                $avgs = DB::select('select SUM(percent) / count(*) as avg FROM taraz, quizRegistry WHERE quizRegistry.qId = ' . $quizId . ' and quizRegistry.id  = taraz.qEntryId GROUP by(taraz.lId)');
        }

        $inCorrects =  DB::select('SELECT count(*) as inCorrects, subject.lessonId as target FROM ROQ, SOQ, question, subject WHERE ROQ.quizId = ' . $quizId . ' and ROQ.questionId = question.id and question.ans <> ROQ.result and ROQ.result <> 0 and ROQ.uId = ' . $uId . ' and subject.id = SOQ.sId and SOQ.qId = question.id group by(subject.lessonId)');
        $corrects =  DB::select('SELECT count(*) as corrects, subject.lessonId as target FROM ROQ, SOQ, question, subject WHERE ROQ.quizId = ' . $quizId . ' and ROQ.questionId = question.id and question.ans = ROQ.result and ROQ.uId = ' . $uId . ' and subject.id = SOQ.sId and SOQ.qId = question.id group by(subject.lessonId)');
        $total = DB::select('SELECT count(*) as total, subject.lessonId as target FROM ROQ, SOQ, question, subject WHERE ROQ.quizId = ' . $quizId . ' and ROQ.questionId = question.id and ROQ.uId = ' . $uId . ' and subject.id = SOQ.sId and SOQ.qId = question.id group by(subject.lessonId)');

        $roq = $this->getResultOfSpecificContainer($total, $corrects, $inCorrects);

        $lessons = getLessonQuiz($quizId);

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

        return view('generalKarname', array('quizId' => $quizId, 'status' => $status, 'kindKarname' => $kindKarname,
            'rank' => $rank, 'rankInLessonCity' => $rankInLessonCity, 'rankInLesson' => $rankInLesson,
            'lessons' => $lessons, 'taraz' => $taraz, 'rankInLessonState' => $rankInLessonState, 'stateRank' => $stateRank,
            'avgs' => $avgs, 'roq' => $roq, 'cityRank' => $cityRank, "totalMark" => $totalMark));
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

            $client = new soapclient('https://bpm.shaparak.ir/pgwchannel/services/pgw?wsdl');
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

                echo json_encode(['status' => 'nok', 'refId' => $res]);
                return;

                if($res != -1)
                    echo json_encode(['status' => 'ok', 'refId' => $res]);

                else {
                    echo json_encode(['status' => 'nok1']);
                }
                return;
            }

            if($mode == "system")
                quizRegistry(getValueInfo('systemQuizTransaction'), getValueInfo('systemQuiz'), $toPay, Auth::user()->id,
                    getValueInfo('money2'), $quizId, $useGift);
            else
                quizRegistry(getValueInfo('regularQuizTransaction'), getValueInfo('regularQuiz'), $toPay, Auth::user()->id,
                    getValueInfo('money2'), $quizId, $useGift);

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

            $client = new soapclient('https://bpm.shaparak.ir/pgwchannel/services/pgw?wsdl');
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
                                getValueInfo('money2'), $quizId, $mellat->gift);
                        }
                        else
                            quizRegistryOnline(getValueInfo('regularQuizTransaction'), getValueInfo('regularQuiz'), $mellat->amount / 10, Auth::user()->id,
                                getValueInfo('money2'), $quizId, $mellat->gift);

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

    public function showQuizWithOutTime($quizId, $quizMode) {

        $uId = Auth::user()->id;

        if($quizMode == getValueInfo('regularQuiz')) {

            $quiz = RegularQuiz::whereId($quizId);

            if ($quiz == null)
                return Redirect::to('profile');


            $today = getToday();
            $date = $today["date"];
            $time = $today["time"];


            if($quiz->startDate > $date || ($quiz->startDate == $date && $quiz->startTime > $time) ||
                $quiz->endDate > $date || ($quiz->endDate == $date && $quiz->endTime > $time)
            )
                return $this->myQuizes('زمان مرور آزمون مورد نظر هنوز نرسیده است');

            $condition = ['qId' => $quizId, 'uId' => $uId, 'quizMode' => getValueInfo('regularQuiz')];
            $quizRegistry = QuizRegistry::where($condition)->first();

            if($quizRegistry == null)
                return Redirect::to('profile');

            $roqs = DB::select('select ROQ.result, question.ans as status from ROQ, question where quizId = ' . $quizId . " and uId = " . $uId . " and 
                quizMode = " . getValueInfo('regularQuiz') . " and question.id = ROQ.questionId");

            if ($roqs == null || count($roqs) == 0) {
                $this->fillRegularROQ($quizId);

                $roqs = DB::select('select ROQ.result, question.ans as status from ROQ, question where quizId = ' . $quizId . " and uId = " . $uId . " and 
                quizMode = " . getValueInfo('regularQuiz') . " and question.id = ROQ.questionId");
            }

            foreach ($roqs as $roq) {
                if ($roq->status == $roq->result)
                    $roq->status = 1;
                else
                    $roq->status = 0;
            }

            $questions = DB::select('select ans, ansFile, choicesCount, question.id, question.questionFile, question.kindQ, question.neededTime as qoqId ' .
                'from question, regularQOQ WHERE questionId = question.id and quizId = ' . $quizId . ' order by regularQOQ.qNo ASC');

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
    
    public function doRegularQuiz($quizId) {

        $today = getToday();
        $date = $today["date"];
        $time = $today["time"];
        $uId = Auth::user()->id;

        $quiz = RegularQuiz::whereId($quizId);
        if($quiz == null)
            return Redirect::to('profile');

        $condition = ['qId' => $quizId, 'uId' => $uId, 'quizMode' => getValueInfo('regularQuiz')];
        $quizRegistry = QuizRegistry::where($condition)->first();

        if($quizRegistry == null)
            return Redirect::to('profile');

        if(!(($quiz->startDate < $date && $quiz->endDate > $date) ||
            ($quiz->startDate < $date && $quiz->endDate >= $date && $quiz->endTime > $time) ||
            ($quiz->startDate == $date && $quiz->starTime <= $time && (
                    ($quiz->startDate == $quiz->endDate && $quiz->endTime > $time) ||
                    ($quiz->startDate != $quiz->endDate) ||
                    ($quiz->endDate == $date && $quiz->endTime > $time)
                )
            )))
            return Redirect::to(route('showQuizWithOutTime', ['quizId' => $quizId, 'quizMode' => getValueInfo('regularQuiz')]));

        $timeLen = calcTimeLenQuiz($quiz->id, 'regular');

        if($quizRegistry->timeEntry == "") {
            $timeEntry = time();
            $quizRegistry->timeEntry = $timeEntry;
            $quizRegistry->save();
        }
        else {
            $timeEntry = $quizRegistry->timeEntry;
        }

        $reminder = $timeLen * 60 - time() + $timeEntry;

        if($reminder <= 0)
            return Redirect::to(route('showQuizWithOutTime', ['quizId' => $quizId, 'quizMode' => getValueInfo('regularQuiz')]));

        $roqs = DB::select('select ROQ.result, question.ans as status from ROQ, question where quizId = ' . $quizId . " and uId = " . $uId . " and 
                quizMode = " . getValueInfo('regularQuiz') . " and question.id = ROQ.questionId");

        if($roqs == null || count($roqs) == 0) {
            $this->fillRegularROQ($quizId);

            $roqs = DB::select('select ROQ.result, question.ans as status from ROQ, question where quizId = ' . $quizId . " and uId = " . $uId . " and 
                quizMode = " . getValueInfo('regularQuiz') . " and question.id = ROQ.questionId");
        }

        foreach ($roqs as $roq) {
            if($roq->status == $roq->result)
                $roq->status = 1;
            else
                $roq->status = 0;
        }

        $questions = DB::select('select choicesCount, question.id, question.questionFile, question.kindQ, question.neededTime as qoqId from question, regularQOQ WHERE questionId = question.id and quizId = ' . $quizId . ' order by regularQOQ.qNo ASC');

        return view('regularQuiz', array('quiz' => $quiz, 'mode' => 'normal', 'questions' => $questions,
            'reminder' => $reminder, 'roqs' => $roqs));

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

    public function submitAnsRegularQuiz() {

        if(isset($_POST["questionId"]) && isset($_POST["quizId"]) && isset($_POST["newVal"])) {

            $questionId = makeValidInput($_POST["questionId"]);
            $question = Question::whereId($questionId);
            $quizId = makeValidInput($_POST["quizId"]);

            if($question == null) {
                echo "nok";
                return;
            }

            $condition = ['questionId' => $questionId, 'uId' => Auth::user()->id,
                'quizId' => $quizId, 'quizMode' => getValueInfo('regularQuiz')];

            $roq = ROQ::where($condition)->first();

            if($roq != null) {

                $newVal = makeValidInput($_POST["newVal"]);

                if($roq->status == 0) {

                    if($question->kindQ == 1 && ($newVal > $question->choicesCount || $newVal < 0)) {
                        echo "nok2";
                        return;
                    }

                    $roq->result = $newVal;
                    $roq->save();
                }
                else
                    echo "noAccess";

                return;
            }
        }

        echo "nok";
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

    public function quizRegistry() {

        $date = getToday()["date"];
        $uId = Auth::user()->id;

        $quizes = SystemQuiz::whereNotExists(function($query) use ($uId) {
            $query->select(DB::raw(1))
                ->from('quizRegistry')
                ->whereRaw('systemQuiz.id = quizRegistry.qId and quizRegistry.quizMode = ' . getValueInfo('systemQuiz') . ' and quizRegistry.uId = ' . $uId);
        })->whereRaw('startReg <= ' . $date . ' and endReg >= ' . $date)->get();

        foreach ($quizes as $quiz) {
            $quiz->startDate = convertStringToDate($quiz->startDate);
            $quiz->startTime = convertStringToTime($quiz->startTime);
            $quiz->startReg = convertStringToDate($quiz->startReg);
            $quiz->endReg = convertStringToDate($quiz->endReg);
        }

        return view('quizRegistry', array('quizes' => $quizes, 'mode' => 'system'));
    }

    public function regularQuizRegistry() {

        $date = getToday()["date"];
        $uId = Auth::user()->id;

        $quizes = RegularQuiz::whereNotExists(function($query) use ($uId) {
            $query->select(DB::raw(1))
                ->from('quizRegistry')
                ->whereRaw('regularQuiz.id = quizRegistry.qId and quizRegistry.quizMode = ' . getValueInfo('regularQuiz') . ' and quizRegistry.uId = ' . $uId);
        })->whereRaw('startReg <= ' . $date . ' and endReg >= ' . $date)->get();

        foreach ($quizes as $quiz) {
            $quiz->startDate = convertStringToDate($quiz->startDate);
            $quiz->endDate = convertStringToDate($quiz->endDate);
            $quiz->startTime = convertStringToTime($quiz->startTime);
            $quiz->endTime = convertStringToTime($quiz->endTime);
            $quiz->startReg = convertStringToDate($quiz->startReg);
            $quiz->endReg = convertStringToDate($quiz->endReg);
        }

        return view('quizRegistry', array('quizes' => $quizes, 'mode' => 'regular'));
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
                echo "nok1";
                return;
            }

            if($quiz == null) {
                echo "nok1";
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
                echo "nok1";
                return;
            }

            if($mode == "system")
                $condition = ['uId' => $uId, 'qId' => $quizId, 'quizMode' => getValueInfo('systemQuiz')];
            else
                $condition = ['uId' => $uId, 'qId' => $quizId, 'quizMode' => getValueInfo('regularQuiz')];

            if(QuizRegistry::where($condition)->count() > 0) {
                echo "nok2";
                return;
            }

            if($mode == "system")
                quizRegistry(getValueInfo('systemQuizTransaction'), getValueInfo('systemQuiz'), $toPay, Auth::user()->id,
                    getValueInfo('money2'), $quizId, $useGift);
            else
                quizRegistry(getValueInfo('regularQuizTransaction'), getValueInfo('regularQuiz'), $toPay, Auth::user()->id,
                    getValueInfo('money2'), $quizId, $useGift);

            echo "ok";
            return;
        }

        echo "nok3";

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

                        if (count($cols) < 'A') {
                            unlink($path);
                            $err = "تعداد ستون های فایل شما معتبر نمی باشد";
                        } else {
                            for ($row = 1; $row <= $lastRow; $row++) {
                                $questions[$row - 1] = $workSheet->getCell('A' . $row)->getValue();
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

        foreach ($questions as $question) {

            $tmp = Question::where('organizationId', '=', $question)->first();
            if($tmp == null) {
                $errs .= $question . ', ';
                continue;
            }

            $qoq = new RegularQOQ();
            $qoq->quizId = $quizId;
            $qoq->questionId = $tmp->id;

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
                $errs .= $question . ', ';
            }
        }

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
            $quiz = SystemQuiz::whereId($quizId);
            $today = getToday();

            if($quiz->startDate < $today["date"] || ($quiz->startDate == $today["date"] && $quiz->startTime < $today["time"])) {
                echo "timeOut";
                return;
            }

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
            $quiz = RegularQuiz::whereId($quizId);
            $today = getToday();

            /*if($quiz->startDate < $today["date"] || ($quiz->startDate == $today["date"] && $quiz->startTime < $today["time"])) {
                echo "timeOut";
                return;
            }*/

            $questions = DB::select('select regularQOQ.qNo as qNo, organizationId, questionFile, ans, users.level as authorLevel, ansFile, question.level,
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
                SystemQuiz::destroy($quizId);
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

            if($quiz->startDate < $today["date"] || ($quiz->startDate == $today["date"] && $quiz->startTime < $today["time"])) {
                echo "timeOut";
                return;
            }

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
            $quiz = RegularQuiz::whereId($quizId);
            $today = getToday();
            $questionId = makeValidInput($_POST["questionId"]);

            if($quiz->startDate < $today["date"] || ($quiz->startDate == $today["date"] && $quiz->startTime < $today["time"])) {
                echo "timeOut";
                return;
            }

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
            $today = getToday();

            if($quiz->startDate < $today["date"] || ($quiz->startDate == $today["date"] && $quiz->startTime < $today["time"])) {
                echo "timeOut";
                return;
            }

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
            $today = getToday();


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
}