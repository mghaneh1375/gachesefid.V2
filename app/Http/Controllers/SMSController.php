<?php

namespace App\Http\Controllers;

use App\models\RegularQuiz;
use App\models\SMSQueue;
use App\models\SMSTemplate;
use App\models\SystemQuiz;
use App\models\User;
use Illuminate\Support\Facades\DB;
use App\models\State;
use App\models\Grade;

class SMSController extends Controller {

    public function smsPanel() {

        $out = [];
        $counter = 0;

        $tmp = DB::select('select regularQuiz.id, name from regularQuiz');

        foreach ($tmp as $itr)
            $out[$counter++] = ['name' => $itr->name, 'id' => $itr->id, 'quizMode' => getValueInfo('regularQuiz')];

        $quizes = DB::select('select systemQuiz.id, name from systemQuiz');

        foreach ($quizes as $quiz) {
            $out[$counter++] = ['name' => $quiz->name, 'id' => $quiz->id, 'quizMode' => getValueInfo('systemQuiz')];
        }


        return view('smsPanel', array('states' => State::all(), 'quizes' => $out, 'grades' => Grade::all()));

    }

    private function queryBuilder($uIds) {

        $first = true;
        $query = "";
        foreach ($uIds as $itr) {
            if($first) {
                $query .= "(" . $itr->id;
                $first = false;
            }
            else {
                $query .= ', ' . $itr->id;
            }
        }
        if(!$first)
            $query .= ")";

        return $query;
    }

    public function sendSMS() {

        if(isset($_POST["stateId"]) && isset($_POST["cityId"]) && isset($_POST["quiz"])
            && isset($_POST["level"]) && isset($_POST["sex"]) && isset($_POST["grade"])
            && isset($_POST["text"]) && isset($_POST["templateId"]) && isset($_POST["sendToAll"])) {

            $quiz = makeValidInput($_POST["quiz"]);
            $sendToAll = makeValidInput($_POST["sendToAll"]);
            $quiz = explode('_', $quiz);

            if(count($quiz) != 2) {
                echo "nok";
                return;
            }

            $quizMode = $quiz[1];
            $quiz = $quiz[0];
            $sex = makeValidInput($_POST["sex"]);

            $level = makeValidInput($_POST["level"]);
            if($sex == -1)
                $uIds = User::where('level', '=', $level)->select('id')->get();
            else {
                $condition = ['sex' => $sex, 'level' => $level];
                $uIds = User::where($condition)->select('id')->get();
            }

            $query = $this->queryBuilder($uIds);

            $stateId = makeValidInput($_POST["stateId"]);
            $cityId = makeValidInput($_POST["cityId"]);

            if($stateId != -1) {
                if($cityId == -1)
                    $uIds = DB::select('select users.id from users, redundantInfo1, city WHERE users.id IN ' . $query . ' and users.id = uId and cityId = city.id and stateId = ' . $stateId);
                else
                    $uIds = DB::select('select users.id from users, redundantInfo1 WHERE users.id IN ' . $query . ' and users.id = uId and cityId = ' . $cityId);
            }

            $query = $this->queryBuilder($uIds);

            if(($level == 1 || $level == 2) && $quiz != -1) {
                if($level == 1) {
                    if($sendToAll == "false")
                        $uIds = DB::select('select users.id from users, quizRegistry WHERE users.id IN ' . $query . ' and users.id = uId and quizMode = ' . $quizMode . ' and qId = ' . $quiz);
                }
                else
                    $uIds = DB::select('select DISTINCT users.id from users, quizRegistry, studentsAdviser WHERE users.id IN ' . $query . ' and studentId = uId and users.id = adviserId and quizMode = ' . $quizMode . ' and qId = ' . $quiz);
            }

            $grade = makeValidInput($_POST["grade"]);

            if($level == 1 && $grade != -1) {
                $query = $this->queryBuilder($uIds);
                $uIds = DB::select('select users.id from users, redundantInfo1 WHERE users.id IN ' . $query . ' and users.id = uId and gradeId = ' . $grade);
            }

            if(count($uIds) == 0) {
                echo "nok3";
                return;
            }

            $text = makeValidInput($_POST["text"]);
            $templateId = makeValidInput($_POST["templateId"]);

            $template = new SMSTemplate();

            if($templateId == -1)
                $template->text = $text;
            else
                $template->text = $templateId;

            $template->save();

            if($templateId == -1) {
                foreach ($uIds as $itr) {

                    if(empty($itr->phoneNum))
                        continue;

                    $tmp = new SMSQueue();
                    $tmp->phoneNum = User::find($itr->id)->phoneNum;
                    $tmp->templateId = $template->id;
                    $tmp->save();
                }
            }
            else{
                if($quizMode == getValueInfo('regularQuiz'))
                    $quiz = RegularQuiz::find($quiz);
                else
                    $quiz = SystemQuiz::find($quiz);

                foreach ($uIds as $itr) {

                    $user = User::find($itr->id);

                    if(empty($user->phoneNum))
                        continue;

                    $tmp = new SMSQueue();
                    $tmp->phoneNum = $user->phoneNum;
                    $tmp->templateId = $template->id;
                    $tmp->extra1 = $user->firstName . '-' . $user->lastName;
                    $name = explode(' ', $quiz->name);
                    $quizName = "";
                    foreach ($name as $itr2)
                        $quizName .= $itr2 . '-';
                    $tmp->extra2 = $quizName;
                    if($templateId == 3)
                        $tmp->extra3 = $quiz->startDate;
                    $tmp->save();
                }
            }

            echo $template->id;
            return;
        }
        echo "nok2";
    }

    public function sendSMSStatus() {

        if(isset($_POST["templateId"])) {

            $tmp = SMSTemplate::find(makeValidInput($_POST["templateId"]));

            if($tmp == null)
                return "nok";

            $sms = SMSQueue::where('templateId', '=', $tmp->id)->first();

            if($sms == null || count($sms) == 0)
                return "nok";

            if(strlen($tmp->text) == 1) {
                if($tmp->text == 2)
                    sendSMS($sms->phoneNum, $sms->extra1, "endQuiz", $sms->extra2);
                else if($tmp->text == 3)
                    sendSMS($sms->phoneNum, $sms->extra1, "newQuiz", $sms->extra2, $sms->extra3);
                else if($tmp->text == 4)
                    sendSMS($sms->phoneNum, $sms->extra1, "quizRemember", $sms->extra2);
            }
            else
                SendREST($sms->phoneNum, $tmp->text, null);
            
            $sms->delete();
            $totalCount = SMSQueue::where('templateId', '=', makeValidInput($_POST["templateId"]))->count();
            if($totalCount == 0) {
                echo "finish";
                SMSTemplate::destroy(makeValidInput($_POST["templateId"]));
            }
            else
                echo "تعداد پیام های باقی مانده: " . $totalCount;
        }

    }

}