<?php

namespace App\Http\Controllers;

use App\models\AdviserQuestion;
use App\models\AnswerSheetTemplates;
use App\models\AnswerAnswerSheetTemplates;
use App\models\AnswerTemplate;
use App\models\City;
use App\models\RedundantInfo1;
use App\models\RegularQOQ;
use App\models\RegularQuiz;
use App\models\QuizRegistry;
use App\models\RegularQuizQueue;
use App\models\School;
use App\models\SchoolStudent;
use App\models\Transaction;
use App\models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;

class AdminController extends Controller {

    public function getExams() {

        $exams = RegularQuiz::select('id', 'name', 'startDate')->get();

        foreach ($exams as $exam) {

            if ($exam->id < 100) {
                if ($exam->id < 10)
                    $exam->id = "00" . $exam->id;
                else
                    $exam->id = "0" . $exam->id;
            }

            $exam->startDate = convertStringToDate($exam->startDate);
        }

        echo json_encode($exams);
    }

    public function getMyStudents() {

        $users = null;

        if(isset($_POST["qId"]) && isset($_POST["username"]) && isset($_POST["password"])) {

            $user = User::whereUsername(makeValidInput($_POST["username"]))->first();

            if($user == null) {
                echo "loginFailed";
                return;
            }

            if(!Hash::check(makeValidInput($_POST["password"]), $user->password)) {
                echo "loginFailed";
                return;
            }

            if($user->level == getValueInfo('studentLevel')) {
                echo "accessDenied";
                return;
            }

            $qId = makeValidInput($_POST["qId"]);

            if($user->level == getValueInfo('namayandeLevel'))
                $users = DB::select("select u.id, u.firstName, u.lastName, s.name as school, c.name as city from city c, school s, namayandeSchool nS, users u, schoolStudent sS, quizRegistry q WHERE q.quizMode = " . getValueInfo('regularQuiz') . " and c.id = s.cityId and sS.sId = s.uId and u.id = sS.uId and nS.sId = sS.sId and sS.uId = q.uId and q.qId = " . $qId . " and nS.nId = " . $user->id);

            else if($user->level == getValueInfo('schoolLevel'))
                $users = DB::select("select u.id, u.firstName, u.lastName, s.name as school, c.name as city from city c, school s, users u, schoolStudent sS, quizRegistry q WHERE q.quizMode = " . getValueInfo('regularQuiz') . " and c.id = s.cityId and sS.sId = s.uId and u.id = sS.uId and sS.uId = q.uId and q.qId = " . $qId . " and sS.sId = " . $user->id);

            else if($user->level == getValueInfo('adminLevel') || $user->level == getValueInfo('superAdminLevel')) {

                $usersTmp = DB::select("select u.id, u.firstName, u.lastName from users u, quizRegistry q WHERE q.quizMode = " . getValueInfo('regularQuiz') . " and q.uId = u.id and q.qId = " . $qId);

                if($usersTmp != null && count($usersTmp) > 0) {

                    foreach ($usersTmp as $itr) {

                        $r = RedundantInfo1::whereUId($itr->id)->first();

                        if ($r != null)
                            $itr->city = City::whereId($r->cityId)->name;
                        else
                            $itr->city = "نامشخص";

                        $s = SchoolStudent::whereUId($itr->id)->first();
                        if($s != null)
                            $itr->school = School::whereUId($s->sId)->first()->name;
                        else
                            $itr->school = "نامشخص";
                    }
                }

                $users = $usersTmp;
            }

            else if($user->level == getValueInfo('adviserLevel')) {

                $usersTmp = DB::select("select u.id, u.firstName, u.lastName from users u, quizRegistry q, studentsAdviser sA WHERE sA.studentId = u.id and sA.adviserId = " . $user->id . " and q.quizMode = " . getValueInfo('regularQuiz') . " and q.uId = u.id and q.qId = " . $qId);

                if($usersTmp != null && count($usersTmp) > 0) {

                    foreach ($usersTmp as $itr) {

                        $r = RedundantInfo1::whereUId($itr->id)->first();

                        if ($r != null)
                            $itr->city = City::whereId($r->cityId)->name;
                        else
                            $itr->city = "نامشخص";

                        $s = SchoolStudent::whereUId($itr->id)->first();
                        if($s != null)
                            $itr->school = School::whereUId($s->sId)->first()->name;
                        else
                            $itr->school = "نامشخص";
                    }

                    $users = $usersTmp;
                }
            }

            if($users != null) {

                foreach ($users as $user) {
                    if ($user->id < 10) {
                        $user->id = "00000" . $user->id;
                    } else if ($user->id < 100) {
                        $user->id = "0000" . $user->id;
                    } else if ($user->id < 1000) {
                        $user->id = "000" . $user->id;
                    } else if ($user->id < 10000) {
                        $user->id = "00" . $user->id;
                    } else if ($user->id < 100000) {
                        $user->id = "0" . $user->id;
                    }
                }


                echo json_encode($users);
            }

        }
    }

    public function answer_sheet_templates() {

        $answer_sheet_templates = DB::select('select A.id, A.name, (SELECT count(*) from answer_answer_sheet_template AA WHERE AA.answer_sheet_template_id = A.id) as countNum FROM answer_sheet_templates A');

        return view('admin.answer_sheet_templates', [
            'answerSheetTemplates' => $answer_sheet_templates
        ]);
    }

    public function delete_answer_sheet_template($id) {
        AnswerSheetTemplates::destroy($id);
        return Redirect::to(route('answer_sheet_templates'));
    }

    public function add_answer_sheet_template_form() {
        return view('admin.add_answer_sheet_template', array('aId' => -1,
            'name' => '', 'rowCount' => '', 'colCount' => ''));
    }

    public function add_answer_sheet_template() {

        if(isset($_POST["submitForm"])) {
            AnswerSheetTemplates::create([
                'name' => makeValidInput($_POST["name"]),
                'row_count' => makeValidInput($_POST["row_count"]),
                'column_count' => makeValidInput($_POST["col_count"])
            ]);
        }
        return Redirect::to(route('answer_sheet_templates'));
    }

    public function edit_answer_sheet_template($aId) {

        $answerSheet = AnswerSheetTemplates::find($aId);

        return view('admin.add_answer_sheet_template', array('aId' => $aId,
            'name' => $answerSheet->name, 'rowCount' => $answerSheet->row_count, 'colCount' => $answerSheet->column_count));
    }

    public function update_answer_sheet_template($aId) {

        $answer_sheet_template = AnswerSheetTemplates::find($aId);

        $answer_sheet_template->name = makeValidInput($_POST["name"]);
        $answer_sheet_template->row_count = makeValidInput($_POST["row_count"]);
        $answer_sheet_template->column_count = makeValidInput($_POST["col_count"]);
        $answer_sheet_template->save();
        return Redirect::to(route('answer_sheet_templates'));
    }

    public function answer_sheet_template_answers($answer_sheet_template, $err = "") {

        $answer = AnswerSheetTemplates::find($answer_sheet_template);

        if($answer == null)
            return Redirect::to('answer_sheet_template');

        $answer_templates = DB::select("select A.id, A.row, A.column from answer_answer_sheet_template AA, answer_templates A WHERE AA.answer_template_id = A.id and AA.answer_sheet_template_id = " . $answer_sheet_template . " order by A.id ASC");

        return view('admin.manage_answer_sheet_template', [
            'answer_templates' => $answer_templates, 'answer_sheet_template' => $answer, 'err' => $err
        ]);
    }

    public function add_answer_template($answer_sheet_template) {

        if(isset($_POST["row"]) && isset($_POST["col"])) {

            $answer = AnswerSheetTemplates::find($answer_sheet_template);
            $row = makeValidInput($_POST["row"]);
            $col = makeValidInput($_POST["col"]);

            if ($answer == null)
                return Redirect::to(route('answer_sheet_templates'));

            if ($answer->row_count < $row)
                return $this->answer_sheet_template_answers($answer_sheet_template, 'تعداد ردیف ها باید کمتر از ' . $answer->row_count . ' باشد');

            if($answer->column_count < $col)
                return $this->answer_sheet_template_answers($answer_sheet_template, 'تعداد ستون ها باید کمتر از ' . $answer->col_count . ' باشد');

            $answerTemplate = new AnswerTemplate();
            $answerTemplate->row = $row;
            $answerTemplate->column = $col;
            $answerTemplate->save();

            $tmp = new AnswerAnswerSheetTemplates();
            $tmp->answer_sheet_template_id = $answer->id;
            $tmp->answer_template_id = $answerTemplate->id;
            $tmp->save();
        }
        return Redirect::to(route('answer_answer_sheet_template', ['answer_sheet_template' => $answer_sheet_template]));
    }

    public function delete_answer_template($answer_template) {

        $tmp = AnswerAnswerSheetTemplates::where('answer_template_id', '=', $answer_template)->first();
        if($tmp == null)
            return Redirect::to(route('answer_sheet_templates'));
        
        AnswerTemplate::destroy($answer_template);
        return Redirect::to(route('answer_answer_sheet_template', ['answer_sheet_template' => $tmp->answer_sheet_template_id]));
    }

    public function edit_answer_template() {

        if(isset($_POST["templateId"]) && isset($_POST["row"]) && isset($_POST["col"]) && 
            isset($_POST["answer_sheet_template_id"])) {

            $answerSheet = AnswerSheetTemplates::find(makeValidInput($_POST["answer_sheet_template_id"]));
            if($answerSheet == null)
                return;

            $answer = AnswerTemplate::find(makeValidInput($_POST["templateId"]));
            if($answer == null)
                return;

            $row = makeValidInput($_POST["row"]);
            $col = makeValidInput($_POST["col"]);

            if($row > $answerSheet->row_count) {
                echo json_encode(['status' => 'nok', 'row' => $answer->row, 'col' => $answer->column,
                    'err' => 'ردیف مورد نظر باید کمتر از ' . $answerSheet->row_count . " باشد"]);
                return;
            }

            if($col > $answerSheet->column_count) {
                echo json_encode(['status' => 'nok', 'row' => $answer->row, 'col' => $answer->column,
                    'err' => 'ستون مورد نظر باید کمتر از ' . $answerSheet->column_count . " باشد"]);
                return;
            }

            $answer->row = $row;
            $answer->column = $col;
            $answer->save();
            echo json_encode(['status' => 'ok']);
        }
    }

    public function get_exam_answer_sheet_template($examId) {

        if(isset($_POST["username"]) && isset($_POST["password"])) {

            $user = User::whereUsername(makeValidInput($_POST["username"]))->first();
            if($user == null) {
                echo "loginFailed";
                return;
            }

            if(!Hash::check(makeValidInput($_POST["password"]), $user->password)) {
                echo "loginFailed";
                return;
            }

            if($user->level == getValueInfo('studentLevel')) {
                echo "accessDenied";
                return;
            }

            $condition = ['quizId' => $examId, 'mark' => 1];
            $regularQOQCount = RegularQOQ::where($condition)->count();

            $tmp = DB::select('select AA.row_count, AA.column_count, AA.id from answer_sheet_templates AA, regularQuiz rQ' .
                ' where rQ.answerSheetId = AA.id and rQ.id = ' . $examId
            );

            if ($tmp == null || count($tmp) == 0) {
                echo "nok1";
                return;
            }

            $tmp2 = DB::select('select A.row, A.column from answer_answer_sheet_template AAA, answer_templates A' .
                ' where AAA.answer_sheet_template_id = ' . $tmp[0]->id . ' and AAA.answer_template_id = A.id order by A.id ASC'
            );

            $i = 0;
            $tmp3 = [];
            foreach ($tmp2 as $itr) {

                $condition = ['quizId' => $examId, 'mark' => 0, 'qNo' => ($i + 1)];
                if(RegularQOQ::where($condition)->count() > 0) {
                    $i++;
                    continue;
                }

                if(count($tmp3) == $regularQOQCount)
                    break;

                $tmp3[count($tmp3)] = ['number' => ($i + 1), 'row' => $itr->row, 'column' => $itr->column];
                $i++;
            }

            echo json_encode([$tmp[0]->row_count, $tmp[0]->column_count, $tmp3]);
            return;
        }
        echo "argumentErr";
    }

    public function groupQuizRegistrationController($qId) {

        $quiz = RegularQuiz::whereId($qId);

        if($quiz == null)
            return Redirect::to(route('profile'));

        return view('groupQuizRegistrationController', array('quiz' => $quiz, 'advisers' => DB::select("select DISTINCT u.id, u.firstName, u.lastName, u.phoneNum from regularQuizQueue rQ, namayandeSchool nS, users u, schoolStudent sS WHERE u.id = nS.nId and nS.sId = sS.sId and sS.uId = rQ.studentId and rQ.qId = " . $qId)));
    }

    public function adviserQuestions() {

        return view('adviserQuestions', ['adviserQuestions' => AdviserQuestion::all()]);

    }

    public function addAdviserQuestion() {

        if(isset($_POST["question"])) {

            try {
                $tmp = new AdviserQuestion();
                $tmp->name = makeValidInput($_POST["question"]);
                $tmp->save();
                echo "ok";
                return;
            }
            catch (Exception $x) {}
        }

        echo "nok";
    }

    public function deleteAdviserQuestion() {

        if(isset($_POST["deleteId"])) {

            $id = makeValidInput($_POST["deleteId"]);

            try {
                AdviserQuestion::destroy($id);
            }
            catch (Exception $x) {}
        }

        return Redirect::route('adviserQuestions');
    }

    public function editAdviserQuestion() {
        
        if(isset($_POST["question"]) && isset($_POST["qId"])) {

            $question = AdviserQuestion::whereId(makeValidInput($_POST["qId"]));

            if($question == null)
                return "nok1";

            try {
                $question->name = makeValidInput($_POST["question"]);
                $question->save();
                echo "ok";
                return;
            }
            catch (Exception $x) {}
        }

        echo "nok";

    }

    public function studentsOfAdviserInQuiz() {
        if(isset($_POST["qId"]) && isset($_POST["adviserId"])) {
            echo json_encode(DB::select("select u.firstName, u.lastName, u.phoneNum, rQ.online from regularQuizQueue rQ, namayandeSchool nS, users u, schoolStudent sS WHERE u.id = sS.uId and nS.sId = sS.sId and sS.uId = rQ.studentId and rQ.qId = " . makeValidInput($_POST["qId"]) . " and nS.nId = " . makeValidInput($_POST['adviserId'])));
        }
    }

    public function totalRegister() {

        if(isset($_POST["adviserId"]) && isset($_POST["qId"]) && isset($_POST["totalPrice"])) {

            $adviserId = makeValidInput($_POST["adviserId"]);
            $qId = makeValidInput($_POST["qId"]);
            $totalPrice = makeValidInput($_POST["totalPrice"]);

            try{
                DB::transaction(function () use ($adviserId, $qId, $totalPrice){

                    $students = DB::select('select rQ.id, rQ.studentId as stdId, rQ.online from regularQuizQueue rQ, namayandeSchool nS, users u, schoolStudent sS WHERE u.id = sS.uId and nS.sId = sS.sId and sS.uId = rQ.studentId and rQ.qId = ' . makeValidInput($_POST["qId"]) . " and nS.nId = " . $adviserId);
                    $regularQuizMode = getValueInfo('regularQuiz');

                    foreach ($students as $student) {
                        $tmp = new QuizRegistry();
                        $tmp->uId = $student->stdId;
                        $tmp->qId = $qId;
                        $tmp->quizMode = $regularQuizMode;
                        $tmp->online = $student->online;
                        $tmp->save();
                        RegularQuizQueue::destroy($student->id);
                    }

                    $tmp = new Transaction();
                    $tmp->kindMoney = getValueInfo('money2');
                    $tmp->userId = $adviserId;
                    $tmp->amount = -$totalPrice;
                    $tmp->kindTransactionId = getValueInfo('regularQuizGroupTransaction');
                    $tmp->date = getToday()["date"];
                    $tmp->save();

                    echo "ok";
                });
            }
            catch (Exception $x) {
                echo "nok";
            }

        }
    }
}