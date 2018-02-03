<?php

namespace App\Http\Controllers;

use App\models\Enheraf;
use App\models\QuizRegistry;
use App\models\ROQ;
use App\models\Question;
use App\models\Taraz;
use App\models\SubjectsPercent;
use App\models\RegularQuiz;
use App\models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class TarazController extends Controller {

    private function getAverageLesson($lId, $qId, $qEntryIds) {

        $questionIds = DB::select('SELECT question.id, question.ans, question.choicesCount, question.kindQ, question.level FROM regularQOQ, question, SOQ WHERE regularQOQ.mark <> 0 and SOQ.qId = question.id and regularQOQ.quizId = ' . $qId . ' and question.id = regularQOQ.questionId and ' . $lId . ' IN (SELECT subject.lessonId FROM subject WHERE subject.id = SOQ.sId)');

        $regularQuizMode = getValueInfo('regularQuiz');

        $totalPercent = 0;
        for($i = 0; $i < count($qEntryIds); $i++) {
            $percent = 0;
            for($j = 0; $j < count($questionIds); $j++) {

                if($i == 0) {
                    $condition = ['questionId' => $questionIds[$j]->id, 'status' => 1];
                    $count = ROQ::where($condition)->count();

                    if($count > 10) {
                        $condition = ['questionId' => $questionIds[$j]->id, 'result' => $questionIds[$j]->ans, 'status' => 1];
                        $correct = ROQ::where($condition)->count();

                        $level = 3; // hard
                        if (($correct * 1.0) / $count > 0.6)
                            $level = 1; // easy
                        if (($correct * 1.0) / $count > 0.3)
                            $level = 2; // average

                        $q = Question::find($questionIds[$j]->id);
                        $q->level = $level;
                        $q->save();
                    }
                }

                $conditions = ['uId' => $qEntryIds[$i]->uId, 'questionId' => $questionIds[$j]->id,
                    'quizId' => $qId, 'quizMode' => $regularQuizMode];

                $stdAns = ROQ::where($conditions)->first();
                if($stdAns != null && count($stdAns) > 0) {

                    if($questionIds[$j]->kindQ == 1 && ($stdAns->result > $questionIds[$j]->choicesCount || $stdAns->result < 0)) {
                        $percent -= (1 / ($questionIds[$j]->choicesCount - 1));
                        $stdAns->result = -1;
                        $stdAns->save();
                    }

                    else if($questionIds[$j]->ans == $stdAns->result)
                        $percent++;
                    else if($stdAns->result != 0)
                        $percent -= (1 / ($questionIds[$j]->choicesCount - 1));
                }
            }

            $conditions = ["qEntryId" => $qEntryIds[$i]->id, 'lId' => $lId];
            $taraz = Taraz::where($conditions)->first();
            if($taraz != null) {
                $taraz->percent = round($percent / count($questionIds) * 100, 4);
                $taraz->save();
            }
            else {
                $taraz = new Taraz();
                $taraz->qEntryId = $qEntryIds[$i]->id;
                $taraz->lId = $lId;
                $taraz->percent = round($percent / count($questionIds) * 100, 4);
                $taraz->save();
            }
            $totalPercent += $taraz->percent;
        }

        return round(($totalPercent / count($qEntryIds)), 4);
    }

    private function getAverageLessons($qId, $qEntryIds) {

        $lIds = getLessonQuiz($qId);
        $avgs = array();
        for($i = 0; $i < count($lIds); $i++) {
            $avgs[$i][0] = $lIds[$i]->id;
            $avgs[$i][1] = $this->getAverageLesson($lIds[$i]->id, $qId, $qEntryIds);
        }
        return $avgs;
    }

    public function getEnherafMeyar($lId, $lessonAvg, $quizId) {

        $percents = DB::select('select percent from taraz, quizRegistry WHERE taraz.lId = ' . $lId .' and taraz.qEntryId = quizRegistry.id AND quizRegistry.qId = '. $quizId);
        $sum = 0.0;
        for($i = 0; $i < count($percents); $i++)
            $sum += pow($percents[$i]->percent - $lessonAvg, 2);
        $sum /= count($percents);
        $sum = sqrt($sum);

        $tmp = new Enheraf();
        $tmp->lId = $lId;
        $tmp->lessonAVG = $lessonAvg;
        $tmp->qId = $quizId;
        $tmp->val = $sum;
        $tmp->save();
    }

    public function fillSubjectsPercentTable($quizId) {

        SubjectsPercent::where('qId', '=', $quizId)->delete();

        $uIds = DB::select('select qR.id, qR.uId from quizRegistry qR WHERE qR.qId = ' . $quizId . ' and qR.quizMode = ' . getValueInfo('regularQuiz') .
            ' and (select count(*) from ROQ r where r.uId = qR.uId and r.quizMode = qR.quizMode and r.quizId = qR.qId) > 0'
        );

        $sIds = DB::select('select DISTINCT SOQ.sId as sId FROM regularQOQ, SOQ WHERE regularQOQ.quizId = ' . $quizId . ' AND regularQOQ.questionId = SOQ.qId');

        $percentInSubjects = array();

        foreach($uIds as $uId) {
            foreach($sIds as $sId) {
                $percentInSubjects[$uId->uId][$sId->sId] = 0;;
            }
        }

        $qoqs = DB::select('select question.id, question.ans as ans, question.choicesCount, SOQ.sId as sId from regularQOQ, question, SOQ WHERE regularQOQ.quizId = ' . $quizId . ' AND regularQOQ.questionId = question.id and question.id = SOQ.qId');
        $totals = array();

        foreach ($sIds as $sId)
            $totals[$sId->sId] = 0;

        $regularQuizMode = getValueInfo('regularQuiz');

        foreach ($qoqs as $qoq) {

            $condition = ['quizMode' => $regularQuizMode, 'quizId' => $quizId, 'questionId' => $qoq->id];
            $roqs = ROQ::where($condition)->select('result', 'uId')->get();
            $totals[$qoq->sId]++;

            foreach ($roqs as $roq) {
                if($qoq->ans == $roq->result)
                    $percentInSubjects[$roq->uId][$qoq->sId]++;
                else if($roq->result != 0)
                    $percentInSubjects[$roq->uId][$qoq->sId] -= (1 / ($qoq->choicesCount - 1));
            }
        }

        foreach ($uIds as $uId) {
            foreach ($sIds as $sId) {
                $subjectsPercent = new SubjectsPercent();
                $subjectsPercent->qId = $quizId;
                $subjectsPercent->sId = $sId->sId;
                $subjectsPercent->uId = $uId->uId;

                $subjectsPercent->percent =
                    round(($percentInSubjects[$uId->uId][$sId->sId] / $totals[$sId->sId] * 100), 4);

                $subjectsPercent->save();
            }
        }


        echo "ok";
    }

    public function createTarazTable() {

        if(isset($_POST["submitQID"])) {

            $final = (isset($_POST["final"])) ? true : false;

            $quizId = makeValidInput($_POST["quizId"]);
            
            $quiz = RegularQuiz::find($quizId);
            $date = getToday();
            $regularQuizMode = getValueInfo('regularQuiz');

            if($quiz == null || $quiz->endDate > $date["date"] || ($quiz->endDate == $date["date"] &&
                $quiz->endTime > $date["time"]))
                return Redirect::to('createTarazTable');

            $qEntryIds = DB::select('select qR.id, qR.uId from quizRegistry qR WHERE qR.qId = ' . $quizId . ' and qR.quizMode = ' . $regularQuizMode .
                        ' and (select count(*) from ROQ r where r.uId = qR.uId and r.quizMode = qR.quizMode and r.quizId = qR.qId) > 0'
            );

            if(count($qEntryIds) > 0) {
                if(Taraz::where('qEntryId', '=', $qEntryIds[0]->id)->count() > 0)
                    return Redirect::to(route('createTarazTable2', ['mode' => 'err']));
            }

            $deleteItems = DB::select('select qR1.id from quizRegistry qR1, quizRegistry qR2 WHERE qR1.qId = qR2.qId and qR1.qId = ' . $quizId . ' and qR1.quizMode = qR2.quizMode and qR1.quizMode = ' . $regularQuizMode . ' and qR1.id < qR2.id and qR1.uId = qR2.uId');

            foreach ($deleteItems as $itr)
                QuizRegistry::destroy($itr->id);

//            foreach ($qEntryIds as $itr) {
//                $tmp = User::whereId($itr->uId);
//                $tmp = $tmp->firstName . $tmp->lastName;
//                if(DB::select("select count(*) as countNum from quizRegistry qR, users u WHERE concat(u.firstName, u.lastName) LIKE '" . $tmp . "' and u.id = qR.uId and qR.quizMode = " . $regularQuizMode)[0]->countNum > 1) {
//                    QuizRegistry::destroy($itr->id);
//                    $condition = ['quizId' => $quizId, 'uId' => $itr->uId, 'quizMode' => $regularQuizMode];
//                    ROQ::where($condition)->delete();
//                }
//            }
//
            $qEntryIds = DB::select('select qR.id, qR.uId from quizRegistry qR WHERE qR.qId = ' . $quizId . ' and qR.quizMode = ' . getValueInfo('regularQuiz') .
                ' and (select count(*) from ROQ r where r.uId = qR.uId and r.quizMode = qR.quizMode and r.quizId = qR.qId) > 0'
            );

            Enheraf::where('qId', '=', $quizId)->delete();

            $avgs = $this->getAverageLessons($quizId, $qEntryIds);

            for($i = 0; $i < count($avgs); $i++) {
                $this->getEnherafMeyar($avgs[$i][0], $avgs[$i][1], $quizId);
            }

            $tmp = array();
            for ($i = 0; $i < count($qEntryIds); $i++) {
                $tmp[$i] = $qEntryIds[$i]->id;
            }

            $this->fillSubjectsPercentTable($quizId);

            return view('createTaraz', array('quizId' => $quizId, 'qEntryIds' => $tmp, 'final' => $final));
        }

        return Redirect::to('createTarazTable');
    }

    public function getRanksMoneyOfQuiz() {

        if(isset($_POST["quizId"])) {

            $quizId = makeValidInput($_POST["quizId"]);

            if(RegularQuiz::find($quizId) == null) {
                echo "nok";
                return;
            }

            $count = ConfigModel::first()->rankInQuiz;

            $users = DB::select("select q.uId, SUM(t.taraz) as tarazSum from taraz t, quizRegistry q WHERE q.qId = " . $quizId . " and quizMode = " . getValueInfo('regularQuiz') .
                " and t.qEntryId = q.id group by(t.qEntryId) order by sum(t.taraz) DESC limit 0, $count");

            include_once 'MoneyController.php';

            $amount = PointConfig::first()->rankInQuizPoint;

            foreach ($users as $user) {
                charge($amount, $user->uId, getValueInfo('quizRankTransaction'), getValueInfo('money2'));
            }

            echo "ok";
            return;

        }

        echo "nok2";

    }

    public function deleteTarazTable() {

        $msg = "";

        if(isset($_POST["deleteTaraz"])) {

            $quizId = makeValidInput($_POST["quizId"]);
            $regularQuizMode = getValueInfo('regularQuiz');

            DB::delete('delete from taraz where qEntryId IN (SELECT id from quizRegistry where quizMode = ' . $regularQuizMode . ' and qId = ' . $quizId . ')');
            SubjectsPercent::where('qId', '=', $quizId)->delete();

            $msg = "جدول تراز آزمون مورد نظر با موفقیت حذف گردید";

        }
        return $this->seeQuizes2("select", $msg);
    }

    public function seeQuizes($mode = "select") {

        $date = getToday();
        $quizes = DB::select('select id, name from regularQuiz WHERE endDate < ' . $date["date"] . " or " .
        "(endDate = " . $date["date"] . " and endTime < " . $date["time"] . ")");

        return view('createTarazTable', array('quizes' => $quizes, 'mode' => $mode));

    }

    public function seeQuizes2($mode = "select", $msg = '') {

        $date = getToday();
        $quizes = DB::select('select id, name from regularQuiz WHERE endDate < ' . $date["date"] . " or " .
            "(endDate = " . $date["date"] . " and endTime < " . $date["time"] . ")");

        return view('deleteTarazTable', array('quizes' => $quizes, 'mode' => $mode, 'msg' => $msg));

    }

}