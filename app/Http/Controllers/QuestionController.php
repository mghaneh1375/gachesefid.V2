<?php

namespace App\Http\Controllers;

use App\models\ControllerActivity;
use App\models\Discussion;
use App\models\DiscussionRate;
use App\models\Lesson;
use App\models\LOK;
use App\models\OffCode;
use App\models\OrderId;
use App\models\QErr;
use App\models\Question;
use App\models\ROQ;
use App\models\SOQ;
use App\models\Subject;
use App\models\User;
use App\models\UserCreatedQuiz;
use App\models\SoldQuestion;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use App\models\Mellat;
use PHPExcel_IOFactory;
use soapclient;

class QuestionController extends Controller {

    public function getQuestionByOrganizationId() {

        if(isset($_POST["organizationId"])) {

            $q = DB::select('select question.id, question.organizationId, question.choicesCount, users.level as authorLevel, question.questionFile, ' .
                'question.ansFile, question.level, question.neededTime, question.telorance, question.choicesCount, ' .
                'question.kindQ, question.ans from question, users where author = users.id and `organizationId` = "' . makeValidInput($_POST["organizationId"]) . '"');

            if($q != null && count($q) > 0) {

                foreach ($q as $question) {
                    if ($question->authorLevel == getValueInfo('adminLevel') || $question->authorLevel == getValueInfo('superAdminLevel')) {
                        $question->questionFile = URL::asset('images/questions/system/' . $question->questionFile);
                        $question->ansFile = URL::asset('images/answers/system/' . $question->ansFile);
                    } else {
                        $question->questionFile = URL::asset('images/questions/students/' . $question->questionFile);
                        $question->ansFile = URL::asset('images/answers/students/' . $question->ansFile);
                    }
                }

                echo \GuzzleHttp\json_encode(['status' => 'ok', 'question' => $q[0]]);
            }
            else
                echo \GuzzleHttp\json_encode(['status' => 'nok']);
        }
        
    }
    
    public function preTransactionQuestion() {

        if(isset($_POST["toPay"]) && isset($_POST["qIds"])) {

            $qIds = $_POST["qIds"];
            $toPay = makeValidInput($_POST["toPay"]);

            try {
                DB::transaction(function () use ($qIds, $toPay){

                    $tmp = new UserCreatedQuiz();
                    $tmp->uId = Auth::user()->id;
                    $tmp->toPay = $toPay;
                    $tmp->created = getToday()["date"];

                    $tmp->save();

                    $quizId = $tmp->id;

                    foreach ($qIds as $qId) {
                        $tmp = new SoldQuestion();
                        $tmp->quizId = $quizId;
                        $tmp->qId = makeValidInput($qId);
                        $tmp->save();
                    }

                    echo $quizId;
                });
                return;
            }
            catch (Exception $x) {}
        }

        echo "nok";
    }

    public function preTransactionBuyQuestion($quizId, $status = "nop") {

        if(UserCreatedQuiz::whereId($quizId) == null)
            return Redirect::to(route('profile'));

        include_once 'MoneyController.php';

        return view('preTransactionQuestion', array('url' => route('createCustomQuiz'), 'backURL' => route('createCustomQuiz'), 'status' => $status, 'quizId' => $quizId,
            'total' => getTotalMoney(), 'toPay' => UserCreatedQuiz::whereId($quizId)->toPay, 'payURL' => route('doCreateCustomQuizFromAccount'), 'payURL2' => route('doCreateCustomQuizOnline')));
    }

    public function doCreateCustomQuizFromAccount() {

        if(isset($_POST["quizId"]) && isset($_POST["giftCode"])) {

            include_once 'MoneyController.php';

            $total = getTotalMoney();
            $quizId = makeValidInput($_POST["quizId"]);

            $quiz = UserCreatedQuiz::whereId($quizId);

            if($quiz == null) {
                echo json_encode(["status" => "nok1"]);
                return;
            }

            $toPay = $quiz->toPay;
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

            quizRegistryOnline(getValueInfo('questionBuyTransaction'), getValueInfo('questionQuiz'), $toPay, Auth::user()->id,
                    getValueInfo('money2'), $quizId, $useGift, false, false);

            echo json_encode(["status" => "ok"]);
            return;
        }

        echo json_encode(["status" => "nok"]);
    }

    public function doCreateCustomQuizOnline() {

        if(isset($_POST["quizId"]) && isset($_POST["giftCode"])) {

            include_once 'MoneyController.php';

            $quizId = makeValidInput($_POST["quizId"]);

            $quiz = UserCreatedQuiz::whereId($quizId);

            if($quiz == null) {
                echo json_encode(['status' => 'nok1']);
                return;
            }

            $toPay = $quiz->toPay;
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

            if($toPay > 10 && $toPay > getTotalMoney()) {

                $callBackUrl = route('paymentPostSelfQuiz', ['quizId' => $quizId]);

                $res = payment(($toPay - getTotalMoney()) * 10, $callBackUrl, $useGift);

                if($res != -1)
                    echo json_encode(['status' => 'ok', 'refId' => $res]);

                else {
                    echo json_encode(['status' => 'nok1']);
                }
                return;
            }


        }

        echo json_encode(['status' => 'nok1']);
    }

    public function paymentPostSelfQuiz($quizId) {

        if (isset($_POST["RefId"]) && isset($_POST["ResCode"]) && isset($_POST["SaleOrderId"]) && isset($_POST["SaleReferenceId"]))  {

            if(makeValidInput($_POST["ResCode"]) != 0) {
                return Redirect::to(route('doCreateCustomQuizWithStatus', ['quizId' => $quizId,
                    'status' => 'err']));
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

            while (OrderId::whereCode($orderId)->count() > 0)
                $orderId = rand(1, 1000000000);

            $tmp->code = $orderId;
            $tmp->save();

            $verifySaleOrderId = $mellat->saleOrderId;
            $verifySaleReferenceId = $mellat->saleReferenceId;

            // Check for an error
            $err = $client->getError();
            if ($err) {
                return Redirect::to(route('doCreateCustomQuizWithStatus', ['quizId' => $quizId,
                    'status' => 'err']));
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
                return Redirect::to(route('doCreateCustomQuizWithStatus', ['quizId' => $quizId,
                    'status' => 'err']));
            }
            else {
                $resultStr = $result;

                $err = $client->getError();
                if ($err) {
                    return Redirect::to(route('doCreateCustomQuizWithStatus', ['quizId' => $quizId,
                        'status' => 'err']));
                }
                else {
                    // Display the result
                    // Update Table, Save Verify Status
                    // Note: Successful Verify means complete successful sale was done.
//					echo "<script>alert('Verify Response is : " . $resultStr . "');</script>";
//					echo "Verify Response is : " . $resultStr;

                    if($resultStr == 0) {

                        include_once 'MoneyController.php';

                        quizRegistryOnline(getValueInfo('questionBuyTransaction'), getValueInfo('questionQuiz'), $mellat->amount / 10, Auth::user()->id,
                            getValueInfo('money2'), $quizId, $mellat->gift, true, false);

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

                        return Redirect::to(route('doCreateCustomQuizWithStatus', ['quizId' => $quizId,
                            'status' => 'finish']));

                    }
                }// end Display the result
            }// end Check for errors
        }

        return Redirect::to(route('doCreateCustomQuizWithStatus', ['quizId' => $quizId,
            'status' => 'err']));
    }

    public function getSubjectQuestionNumsUser() {

        if(isset($_POST["sId"])) {

            $sId = makeValidInput($_POST["sId"]);

            $uId = Auth::user()->id;
            $tmp = DB::select('select count(*) as countNum from ROQ, SOQ WHERE uId = ' . $uId . ' and questionId = SOQ.qId and sId = ' . $sId);
            if ($tmp != null && count($tmp) != 0 && !empty($tmp[0]->countNum) && $tmp[0]->countNum > 0)
                return "ok";
        }

        return "nok";
    }

    public function getQuestions() {

        if(isset($_POST["qId"]) && isset($_POST["page"])) {

            $qId = makeValidInput($_POST['qId']);
            $page = (makeValidInput($_POST['page']) - 1) * 5;

            $questions = DB::select('select * from discussion WHERE id = relatedTo and qId = ' . $qId . ' and status = 1 limit ' . $page . ', 5');

            foreach ($questions as $question) {
                $user = User::whereId($question->uId);
                if($user != null) {
                    $question->uId = $user->username;
                }
                else {
                    $question->uId = "نامشخص";
                }
                $question->date = convertStringToDate($question->date);
                $condition = ['relatedTo' => $question->id, 'status' => 1];
                $question->ansNum = Discussion::where($condition)->count() - 1;
                $rate = DB::select('select sum(point) as rate from discussionRate WHERE qId = ' . $question->id);

                if($rate == null || count($rate) == 0 || $rate[0]->rate == null) {
                    $question->rate = 0;
                }
                else {
                    $question->rate = $rate[0]->rate;
                }
            }

            echo json_encode($questions);

        }

    }

    public function sendAns() {

        if(isset($_POST["qId"]) && isset($_POST["text"])) {

            $qId = makeValidInput($_POST["qId"]);
            $discussion = Discussion::whereId($qId);

            if($discussion == null) {
                echo "nok1";
                return;
            }

            $question = Question::whereId($discussion->qId);
            if($question == null) {
                echo "nok2";
                return;
            }

            $uId = Auth::user()->id;
            $condition = ['uId' => $uId, 'questionId' => $question->id, 'status' => 1];

            if(ROQ::where($condition)->count() > 0) {

                $discussion = new Discussion();
                $discussion->qId = $question->id;
                $discussion->relatedTo = $qId;
                $discussion->date = getToday()["date"];
                $discussion->description = makeValidInput($_POST["text"]);
                $discussion->uId = $uId;

                try {
                    $discussion->save();
                    echo "ok";
                    return;
                }
                catch (Exception $x) {
                    echo "nok4";
                    return;
                }
            }
        }
        echo "nok3";
    }

    public function showAllAns() {
        if(isset($_POST["logId"])) {

            $questions = DB::select('select * from discussion WHERE id <> relatedTo and relatedTo = ' . makeValidInput($_POST["logId"]) . ' and status = 1');

            foreach ($questions as $question) {
                $user = User::whereId($question->uId);
                if($user != null) {
                    $question->uId = $user->username;
                }
                else {
                    $question->uId = "نامشخص";
                }
                $question->date = convertStringToDate($question->date);
                $condition = ['relatedTo' => $question->id, 'status' => 1];
                $question->ansNum = Discussion::where($condition)->count() - 1;
                $rate = DB::select('select sum(point) as rate from discussionRate WHERE qId = ' . $question->id);

                if($rate == null || count($rate) == 0 || $rate[0]->rate == null) {
                    $question->rate = 0;
                }
                else {
                    $question->rate = $rate[0]->rate;
                }
            }

            echo json_encode($questions);
        }
    }

    public function showQuestionListSubject($sId) {

        $questions = DB::select('select distinct(questionId) as questionId from ROQ WHERE uId = ' . Auth::user()->id . " and ROQ.status = 1
            and EXISTS(select * from SOQ where qId = ROQ.questionId and sId = " . $sId . ")");
        return view('questionInfo', array('mode' => 'subject', 'questions' => $questions));
    }

    public function questionInfo() {

        if(isset($_POST["qId"])) {

            $qId = makeValidInput($_POST["qId"]);

            $uId = Auth::user()->id;
            $condition = ['questionId' => $qId, 'uId' => $uId, 'status' => 1];
            $roq = ROQ::where($condition)->orderBy('id', 'DESC')->first();

            if($roq == null) {
                echo "nok";
                return;
            }

            $question = Question::whereId($qId);
            if($question == null) {
                echo "nok";
                return;
            }

            if($question->author == User::whereLevel(getValueInfo('adminLevel'))->first()->id ||
                $question->author == User::whereLevel(getValueInfo('superAdminLevel'))->first()->id)
                $question->questionFile = URL::asset('images/questions/system/' . $question->questionFile);
            else
                $question->questionFile = URL::asset('images/questions/student/' . $question->questionFile);

            $condition = ['questionId' => $qId, 'result' => $question->ans, 'status' => 1];
            $question->correct = ROQ::where($condition)->count();
            $question->incorrect = DB::select('select count(*) as countNum from ROQ WHERE questionId = ' . $question->id. ' and status = 1 and result <> ' . $question->ans
                . " and result <> 0")[0]->countNum;
            $condition = ['questionId' => $qId, 'result' => 0, 'status' => 1];
            $question->white = ROQ::where($condition)->count();

            $condition = ['uId' => $uId, 'questionId' => $qId];
            $question->hasLike = (LOK::where($condition)->count() == 1) ? true : false;
            $question->level = getQuestionLevel($qId);

            $question->likeNo = LOK::where('questionId', '=', $qId)->count();
            $question->yourAns = $roq->result;
            $question->discussion = route('discussion', ['qId' => $qId]);

            $question->controller = User::whereId(ControllerActivity::where('qId', '=', $qId)->first()->uId)->username;
            $question->author = User::whereId($question->author)->username;

            echo json_encode($question);
            return;
        }

        echo "nok";
    }

    public function opOnQuestion() {

        if (isset($_POST["logId"]) && isset($_POST["mode"])) {

            $uId = Auth::user()->id;
            $logId = makeValidInput($_POST["logId"]);
            $mode = makeValidInput($_POST["mode"]);

            if($mode == "like")
                echo $this->likeComment($uId, $logId);
            else if($mode == "dislike")
                echo $this->dislikeComment($uId, $logId);

        }
    }

    private function likeComment($uId, $logId) {

        $out = 1;
        $condition = ['qId' => $logId, 'uId' => $uId, 'point' => 1];

        if (DiscussionRate::where($condition)->count() > 0) {
            echo 0;
            return;
        }

        $condition = ['qId' => $logId, 'uId' => $uId, 'point' => -1];

        $rate = DiscussionRate::where($condition)->first();
        if ($rate != null) {
            $out = 2;
        }
        else {
            $rate = new DiscussionRate();
            $rate->uId = $uId;
            $rate->qId = $logId;
        }

        $rate->point = 1;
        $rate->save();
        echo $out;
    }

    private function dislikeComment($uId, $logId) {

        $out = 1;
        $condition = ['qId' => $logId, 'uId' => $uId, 'point' => -1];

        if (DiscussionRate::where($condition)->count() > 0) {
            echo 0;
            return;
        }

        $condition = ['qId' => $logId, 'uId' => $uId, 'point' => 1];

        $rate = DiscussionRate::where($condition)->first();
        if ($rate != null) {
            $out = 2;
        }
        else {
            $rate = new DiscussionRate();
            $rate->uId = $uId;
            $rate->qId = $logId;
        }

        $rate->point = -1;
        $rate->save();
        echo $out;
    }

    public function discussion($qId) {

        $question = Question::whereId($qId);
        if($question == null)
            return Redirect::to('profile');

        $uId = Auth::user()->id;
        $condition = ['uId' => $uId, 'questionId' => $qId];

        $tmp = DB::select('select count(*) as countNum from userCreatedQuiz u, soldQuestion s WHERE u.uId = ' . $uId . ' and u.id = s.quizId and s.qId = ' . $qId);
        if($tmp == null || count($tmp) == 0 || $tmp[0]->countNum == 0)
            $tmp = 0;
        else
            $tmp = 1;

        if(ROQ::where($condition)->count() > 0 || $tmp == 1) {
            return view('discussion', array('qId' => $qId));
        }
        return Redirect::to('profile');
    }

    public function askQuestion() {

        if(isset($_POST["qId"]) && isset($_POST["text"])) {

            $qId = makeValidInput($_POST["qId"]);
            $question = Question::whereId($qId);
            if($question == null) {
                echo "nok2";
                return;
            }

            $uId = Auth::user()->id;
            $condition = ['uId' => $uId, 'questionId' => $qId];

            $tmp = DB::select('select count(*) as countNum from userCreatedQuiz u, soldQuestion s WHERE u.uId = ' . $uId . ' and u.id = s.quizId and s.qId = ' . $qId);
            if($tmp == null || count($tmp) == 0 || $tmp[0]->countNum == 0)
                $tmp = 0;
            else
                $tmp = 1;

            if(ROQ::where($condition)->count() > 0 || $tmp == 1) {
                $discussion = new Discussion();
                $discussion->qId = $qId;
                $discussion->uId = $uId;
                $discussion->date = getToday()["date"];
                $discussion->description = makeValidInput($_POST["text"]);

                try {
                    $discussion->save();
                    $discussion->relatedTo = $discussion->id;
                    $discussion->save();
                    echo "ok";
                    return;
                }
                catch (Exception $x) {
                    echo "nok1";
                    return;
                }
            }
        }
        echo "nok3";
    }

    public function likeQuestion() {

        if(isset($_POST["qId"])) {

            $qId = makeValidInput($_POST["qId"]);
            $uId = Auth::user()->id;
            $condition = ['uId' => $uId, 'questionId' => $qId];
            if(ROQ::where($condition)->count() > 0) {
                $lok = LOK::where($condition)->first();
                if($lok == null) {
                    $lok = new LOK();
                    $lok->uId = $uId;
                    $lok->questionId = $qId;
                    $lok->save();
                    echo "select";
                }else {
                    $lok->delete();
                    echo "unselected";
                }
                return;
            }
        }

        echo "nok";
    }

    public function addQuestion($err = "") {
        return view('addQuestion', array('err' => $err));
    }

    public function doAddQuestionPic() {

        $err = "";
        $uId = Auth::user()->id;

        if(count($_FILES) > 0) {

            $file = $_FILES[0];
            $fileName = explode('.', $file["name"])[0];
            $hashFileName = Hash::make($fileName);
            $hashFileName = str_replace('/', '$', $hashFileName);

            $path = __DIR__ . '/../../../public/images/questions/system/' . $hashFileName . '.jpg';

            $count = 2;
            while(file_exists($path)) {
                $hashFileName = Hash::make($fileName . $count++);
                $hashFileName = str_replace('/', '$', $hashFileName);
                $path = __DIR__ . '/../../../public/images/questions/system/' . $hashFileName . '.jpg';
            }

            $err = uploadCheck($path, 0, "صورت سوال", 300000, "jpg");
            if(empty($err)) {
                $err = upload($path, 0, "صورت سوال");
                if(empty($err)) {
                    $question = new Question();
                    $question->questionFile = $hashFileName . '.jpg';
                    $question->author = $uId;
                    $question->status = 1;
                    $question->save();
                    echo json_encode(['status' => "ok", 'msg' => $question->id]);
                    return;
                }
            }
        }
        if(empty($err))
            $err = "مشکلی در انجام عملیات مورد نظر رخ داده است (خطای 101)";

        echo json_encode(['status' => 'nok', 'msg' => $err]);
    }

    public function doChangeQuestionPic($qId) {

        if(count($_FILES) > 0) {

            $question = Question::whereId(makeValidInput($qId));

            if($question == null) {
                echo json_encode(['status' => "nok", 'msg' => 'مشکلی در انجام عملیات مورد نظر رخ داده است. (خطای 301)']);
                return;
            }

            $file = $_FILES[0];
            $path = __DIR__ . '/../../../public/images/questions/system/' . $file["name"];

            $err = uploadCheck($path, 0, "تغییر تصویر صورت سوال", 300000, "jpg");
            if(empty($err)) {
                $err = upload($path, 0, "تغییر تصویر صورت سوال");
                if(empty($err)) {
                    $newPath = __DIR__ . '/../../../public/images/questions/system/' . $question->questionFile;
                    if(file_exists($newPath))
                        unlink($newPath);
                    rename($path, $newPath);
                    echo json_encode(['status' => "ok"]);
                    return;
                }
            }
        }
        if(empty($err))
            $err = "مشکلی در انجام عملیات مورد نظر رخ داده است (خطای 101)";

        echo json_encode(['status' => 'nok', 'msg' => $err]);
    }

    public function addAnsToQuestion($qId) {

        $question = Question::whereId($qId);
        $err = "";

        if($question == null)
            $err = "مشکلی در انجام عملیات مورد نظر رخ داده است (خطای 102)";

        if(empty($err) && count($_FILES) > 0) {

            $file = $_FILES[0];
            $fileName = explode('.', $file["name"])[0];

            $hashFileName = Hash::make($fileName);
            $hashFileName = str_replace('/', '$', $hashFileName);
            $path = __DIR__ . '/../../../public/images/answers/system/' . $hashFileName . '.jpg';

            $count = 2;
            while(file_exists($path)) {
                $hashFileName = Hash::make($fileName . $count++);
                $hashFileName = str_replace('/', '$', $hashFileName);
                $path = __DIR__ . '/../../../public/images/answers/system/' . $hashFileName . '.jpg';
            }

            $err = uploadCheck($path, 0, "پاسخ تشریحی", 300000, "jpg");
            if(empty($err)) {
                $err = upload($path, 0, "پاسخ تشریحی");

                if(empty($err)) {
                    $question->ansFile = $hashFileName . '.jpg';
                    $question->save();

                    echo json_encode(['status' => "ok", 'msg' => $qId]);
                    return;
                }
            }
        }
        if(empty($err))
            $err = "مشکلی در انجام عملیات مورد نظر رخ داده است (خطای 103)";

        if($question != null) {

            $path = __DIR__ . '/../../../public/images/questions/system/' . $question->questionFile;

            if(file_exists($path))
                unlink($path);

            $question->delete();
        }

        echo json_encode(['status' => 'nok', 'msg' => $err]);

    }

    public function doChangeAnsPic($qId) {

        if(count($_FILES) > 0) {

            $question = Question::whereId(makeValidInput($qId));

            if($question == null) {
                echo json_encode(['status' => "nok", 'msg' => 'مشکلی در انجام عملیات مورد نظر رخ داده است. (خطای 301)']);
                return;
            }

            $file = $_FILES[0];
            $path = __DIR__ . '/../../../public/images/answers/system/' . $file["name"];

            $err = uploadCheck($path, 0, "تغییر تصویر پاسخ سوال", 300000, "jpg");
            if(empty($err)) {
                $err = upload($path, 0, "تغییر تصویر پاسخ سوال");
                if(empty($err)) {
                    $newPath = __DIR__ . '/../../../public/images/answers/system/' . $question->ansFile;
                    if(file_exists($newPath))
                        unlink($newPath);
                    rename($path, $newPath);
                    echo json_encode(['status' => "ok"]);
                    return;
                }
            }
        }
        if(empty($err))
            $err = "مشکلی در انجام عملیات مورد نظر رخ داده است (خطای 101)";

        echo json_encode(['status' => 'nok', 'msg' => $err]);
    }

    public function addDetailToQuestion($qId) {

        $question = Question::whereId($qId);

        if(isset($_POST["level"]) && isset($_POST["ans"]) && isset($_POST["neededTime"]) && isset($_POST["organizationId"]) &&
            isset($_POST["kindQuestion"]) && isset($_POST["additional"]) && isset($_POST["subjects"])) {

            if($question == null) {
                echo "مشکلی در انجام عملیات مورد نظر رخ داده است (خطای 104)";
            }
            else {

                $kindQ = makeValidInput($_POST["kindQuestion"]);

                $question->level = makeValidInput($_POST["level"]);
                $question->ans = makeValidInput($_POST["ans"]);
                $question->neededTime = makeValidInput($_POST["neededTime"]);
                $question->kindQ = $kindQ;
                $question->organizationId = makeValidInput($_POST["organizationId"]);
                $subjects = $_POST["subjects"];

                if($kindQ == 1) {
                    $question->choicesCount = makeValidInput($_POST["additional"]);
                }
                else {
                    $question->telorance = makeValidInput($_POST["additional"]);
                }

                try{
                    $question->save();

                    foreach ($subjects as $subject) {
                        $subject = makeValidInput($subject);
                        $soq = new SOQ();
                        $soq->sId = $subject;
                        $soq->qId = $qId;
                        $soq->save();
                    }
                    echo "ok";
                    return;
                }
                catch (Exception $x) {
                    echo "مشکلی در انجام عملیات مورد نظر رخ داده است (خطای 105)";
                }
            }
            return;
        }

        if($question != null) {

            $path = __DIR__ . '/../../../public/images/questions/system/' . $question->questionFile;
            if(file_exists($path))
                unlink($path);

            $path = __DIR__ . '/../../../public/images/answers/system/' . $question->ansFile;
            if(file_exists($path))
                unlink($path);

            $question->delete();

            SOQ::where('qId', '=', $qId)->delete();
        }

        echo "مشکلی در انجام عملیات مورد نظر رخ داده است (خطای 106)";
    }

    private function addQuestions($questions) {

        $errors = [];
        $uId = Auth::user()->id;

        foreach ($questions as $question) {

            if($question[4] == 1 && $question[5] > $question[6]) {
                $path = __DIR__ . '/../../../public/images/questions/system/' . $question[0];
                if(file_exists($path))
                    unlink($path);
                $path = __DIR__ . '/../../../public/images/answers/system/' . $question[1];
                if(file_exists($path))
                    unlink($path);
                $errors[count($errors)] = "err1: " . $question[0];
                continue;
            }

            $path = __DIR__ . '/../../../public/images/questions/system/' . $question[0];
            if(file_exists($path)) {
                $fileName = explode('.', $question[0])[0];
                $hashName = Hash::make($fileName);
                $hashName = str_replace('/', '$', $hashName);
                $newPath = __DIR__ . '/../../../public/images/questions/system/' . $hashName . '.jpg';
                $count = 2;
                while (file_exists($newPath)) {
                    $hashName = Hash::make($fileName . $count++);
                    $hashName = str_replace('/', '$', $hashName);
                    $newPath = __DIR__ . '/../../../public/images/questions/system/' . $hashName . '.jpg';
                }
                if(rename($path, $newPath)) {
                    $path = __DIR__ . '/../../../public/images/answers/system/' . $question[1];
                    if(file_exists($path)) {
                        $fileName = explode('.', $question[1])[0];
                        $hashNameAns = Hash::make($fileName);
                        $hashNameAns = str_replace('/', '$', $hashNameAns);
                        $newPathAns = __DIR__ . '/../../../public/images/answers/system/' . $hashNameAns . '.jpg';
                        $count = 2;
                        while (file_exists($newPathAns)) {
                            $hashNameAns = Hash::make($fileName . $count++);
                            $hashNameAns = str_replace('/', '$', $hashNameAns);
                            $newPathAns = __DIR__ . '/../../../public/images/answers/system/' . $hashNameAns . '.jpg';
                        }
                        if(rename($path, $newPathAns)) {
                            $newQuestion = new Question();
                            $newQuestion->questionFile = $hashName . '.jpg';
                            $newQuestion->ansFile = $hashNameAns . '.jpg';
                            $newQuestion->level = $question[2];
                            $newQuestion->neededTime = $question[3];
                            $newQuestion->kindQ = $question[4];
                            $newQuestion->ans = $question[5];
                            $newQuestion->author = $uId;
                            $newQuestion->organizationId = $question[7];
                            $newQuestion->status = 1;

                            if($question[4] == 1)
                                $newQuestion->choicesCount = $question[6];
                            else
                                $newQuestion->telorance = $question[6];

                            $newQuestion->save();

                            $controllerActivity = new ControllerActivity();
                            $controllerActivity->uId = $uId;
                            $controllerActivity->qId = $newQuestion->id;
                            $controllerActivity->save();

                            $tmp = 8;
                            while ($tmp < count($question)) {
                                $subject = Subject::find($question[$tmp++]);
                                if($subject == null) {
                                    $errors[count($errors)] = $question[0];
                                    SOQ::where('qId', '=', $newQuestion->id)->delete();
                                    $newQuestion->delete();
                                    unlink($newPath);
                                    unlink($newPathAns);
                                    break;
                                }
                                else {
                                    $soq = new SOQ();
                                    $soq->sId = $subject->id;
                                    $soq->qId = $newQuestion->id;
                                    $soq->save();
                                }
                            }
                        }
                        else {
                            unlink($path);
                            unlink($newPath);
                            $errors[count($errors)] = "err2: " . $question[0];
                        }
                    }
                    else {
                        unlink($newPath);
                        $errors[count($errors)] = "err3: " . $question[0];
                    }
                }
                else {
                    unlink($path);
                    $path = __DIR__ . '/../../../public/images/answers/system/' . $question[1];
                    if(file_exists($path))
                        unlink($path);

                    $errors[count($errors)] = "err4: " . $question[0];
                }
            }
            else
                $errors[count($errors)] = "err5: " . $question[0];
        }
        return $errors;
    }

    public function addQuestionBatch() {

        if(!Auth::check())
            return Redirect::to(route('addQuestion'));

        if (isset($_FILES["questions"])) {

            $path = __DIR__ . '/../../../public/tmp/' . $_FILES["questions"]["name"];

            $err = uploadCheck($path, "questions", "اکسل سوالات", 20000000, "xlsx");

            if (empty($err)) {
                upload($path, "questions", "اکسل سوالات");
                $excelReader = PHPExcel_IOFactory::createReaderForFile($path);
                $excelObj = $excelReader->load($path);
                $workSheet = $excelObj->getSheet(0);
                $questions = array();
                $lastRow = $workSheet->getHighestRow();
                $cols = $workSheet->getHighestColumn();

                if ($cols < 'I') {
                    unlink($path);
                    $err = "تعداد ستون های فایل شما معتبر نمی باشد";
                } else {
                    for ($row = 1; $row <= $lastRow; $row++) {

                        if($workSheet->getCell('A' . $row)->getValue() == "")
                            break;

                        $questions[$row - 1][0] = $workSheet->getCell('A' . $row)->getValue();
                        $questions[$row - 1][1] = $workSheet->getCell('B' . $row)->getValue();
                        $questions[$row - 1][2] = $workSheet->getCell('C' . $row)->getValue();
                        $questions[$row - 1][3] = $workSheet->getCell('D' . $row)->getValue();
                        $questions[$row - 1][4] = $workSheet->getCell('E' . $row)->getValue();
                        $questions[$row - 1][5] = $workSheet->getCell('F' . $row)->getValue();
                        $questions[$row - 1][6] = $workSheet->getCell('G' . $row)->getValue();
                        $questions[$row - 1][7] = $workSheet->getCell('H' . $row)->getValue();
                        $questions[$row - 1][8] = $workSheet->getCell('I' . $row)->getValue();
                        $char = 'J';
                        $count = 9;
                        while ($workSheet->getCell($char . $row)->getValue() != "")
                            $questions[$row - 1][$count++] = $workSheet->getCell($char++ . $row)->getValue();
                    }
                    unlink($path);
                    $errors = $this->addQuestions($questions);
                    if (count($errors) == 0)
                        $err = "کلیه سوالات به درستی به سامانه افزوده شدند";
                    else {
                        $err = "بجز سوالات زیر که در سامانه موجود است بقیه به درستی اضافه شدند" . '<br/>';
                        $size = count($errors);
                        for ($i = 0; $i < $size; $i++)
                            $err .= $errors[$i] . '<br/>';
                    }
                }
            }
        }
        else
            $err = "خطایی در انجام عملیات مورد نظر رخ داده است";

        return $this->addQuestion($err);
    }

    public function getTotalQuestions() {

        if(isset($_POST["lessonId"])) {

            $lessonId = makeValidInput($_POST["lessonId"]);
            if(empty($lessonId)) {
                echo json_encode([]);
                return;
            }

            $questions = DB::select('select question.id, question.organizationId, question.choicesCount, users.level as authorLevel, question.questionFile, ' .
                'question.ansFile, question.level, question.neededTime, question.telorance, question.choicesCount, ' .
                'question.kindQ, question.ans from question, SOQ, subject, users where author = users.id and sId = subject.id and ' .
                'lessonId = ' . $lessonId . ' and qId = question.id');

            foreach ($questions as $question) {
                if($question->authorLevel == getValueInfo('adminLevel') || $question->authorLevel == getValueInfo('superAdminLevel')) {
                    $question->questionFile = URL::asset('images/questions/system/' . $question->questionFile);
                    $question->ansFile = URL::asset('images/answers/system/' . $question->ansFile);
                }
                else {
                    $question->questionFile = URL::asset('images/questions/students/' . $question->questionFile);
                    $question->ansFile = URL::asset('images/answers/students/' . $question->ansFile);
                }
            }
            echo json_encode($questions);
        }
    }

    public function unConfirmedQuestions() {

        $user = Auth::user();
        if($user->level == getValueInfo('controllerLevel'))
            $grades = DB::Select('select DISTINCT(grade.id), grade.name from grade, lesson, controllerLevel WHERE controllerId = ' . $user->id . ' and lessonId = lesson.id and grade.id = lesson.gradeId');
        else
            $grades = DB::Select('select DISTINCT(grade.id), grade.name from grade');

        return view('unConfirmedQuestions', array('grades' => $grades, 'err' => ""));
    }

    public function totalQuestions($qId = "") {

        $grades = DB::Select('select DISTINCT(grade.id), grade.name from grade');

        $lId = -1;
        $gradeId = -1;

        if(!empty($qId)) {
            if(Question::whereId($qId) == null)
                $qId = "";
            else {
                $lId = DB::select("select subject.lessonId from SOQ, subject WHERE SOQ.qId = " . $qId . " and SOQ.sId = subject.id");
                if($lId == null || count($lId) == 0) {
                    $lId = -1;
                }
                else {
                    $lId = $lId[0]->lessonId;
                    $gradeId = Lesson::whereId($lId)->gradeId;
                }
            }
        }

        return view('totalQuestions', array('grades' => $grades, 'err' => "", 'qId' => $qId, 'lId' => $lId, 'gradeId' => $gradeId));
    }

    public function getLessonsController() {

        if(isset($_POST["gradeId"])) {

            $gradeId = makeValidInput($_POST["gradeId"]);
            $user = Auth::user();
            if($user->level == getValueInfo('controllerLevel'))
                echo json_encode(DB::Select('select DISTINCT(lesson.id), lesson.name from grade, lesson, controllerLevel WHERE controllerId = ' . $user->id . ' and lessonId = lesson.id and gradeId = grade.id and  grade.id = ' . $gradeId));
            else
                echo json_encode(DB::Select('select DISTINCT(lesson.id), lesson.name from grade, lesson WHERE gradeId = grade.id and  grade.id = ' . $gradeId));
        }

    }

    public function getControllerQuestions() {

        if(isset($_POST["lessonId"])) {

            $lessonId = makeValidInput($_POST["lessonId"]);
            if(empty($lessonId)) {
                echo json_encode([]);
                return;
            }

            $questions = DB::select('select question.id, question.choicesCount, users.level as authorLevel, question.questionFile, ' .
    'question.ansFile, question.level, question.neededTime, question.telorance, question.choicesCount, ' .
    'question.kindQ, question.ans from question, SOQ, subject, users where author = users.id and sId = subject.id and ' .
    'lessonId = ' . $lessonId . ' and qId = question.id and question.status = 0');

            foreach ($questions as $question) {
                if($question->authorLevel == getValueInfo('adminLevel') || $question->authorLevel == getValueInfo('superAdminLevel')) {
                    $question->questionFile = URL::asset('images/questions/system/' . $question->questionFile);
                    $question->ansFile = URL::asset('images/answers/system/' . $question->ansFile);
                }
                else {
                    $question->questionFile = URL::asset('images/questions/students/' . $question->questionFile);
                    $question->ansFile = URL::asset('images/answers/students/' . $question->ansFile);
                }
            }
            echo json_encode($questions);
        }

    }

    public function getQuestionSubjects() {

        if (isset($_POST["questionId"])) {

            echo json_encode(DB::select('select subject.id, subject.name from subject, SOQ WHERE 
              sId = subject.id and qId = ' . makeValidInput($_POST["questionId"])));

        }

    }

    public function editDetailQuestion($qId) {

        $question = Question::whereId($qId);

        if(isset($_POST["level"]) && isset($_POST["ans"]) && isset($_POST["neededTime"]) &&
            isset($_POST["kindQuestion"]) && isset($_POST["additional"]) && isset($_POST["subjects"])) {

            if($question == null) {
                return "مشکلی در انجام عملیات مورد نظر رخ داده است (خطای 104)";
            }

            $kindQ = makeValidInput($_POST["kindQuestion"]);

            $question->level = makeValidInput($_POST["level"]);
            $question->ans = makeValidInput($_POST["ans"]);
            $question->neededTime = makeValidInput($_POST["neededTime"]);
            $question->kindQ = $kindQ;
            $question->status = 1;
            $subjects = $_POST["subjects"];

            if($kindQ == 1) {
                $question->choicesCount = makeValidInput($_POST["additional"]);
                $question->telorance = 0;
            }
            else {
                $question->choicesCount = 0;
                $question->telorance = makeValidInput($_POST["additional"]);
            }

            try{
                $question->save();
                SOQ::where('qId', '=', $qId)->delete();

                foreach ($subjects as $subject) {
                    $subject = makeValidInput($subject);
                    $soq = new SOQ();
                    $soq->sId = $subject;
                    $soq->qId = $qId;
                    $soq->save();
                }

                $controllerActivity = new ControllerActivity();
                $controllerActivity->uId = Auth::user()->id;
                $controllerActivity->qId = $question->id;
                $controllerActivity->save();
                return "ok";
            }
            catch (Exception $x) {
                return "مشکلی در انجام عملیات مورد نظر رخ داده است (خطای 105)" . $x->getMessage();
            }
        }
        return "مشکلی در انجام عملیات مورد نظر رخ داده است (خطای 106)";
    }

    public function rejectQuestion() {

        if(isset($_POST["qId"]) && isset($_POST['desc'])) {

            $qId = makeValidInput($_POST["qId"]);
            $tmp = Question::whereId($qId);

            if($tmp == null) {
                echo "nok";
                return;
            }

            if($tmp->status == 0) {

                $qErr = new QErr();
                $qErr->uId = Auth::user()->id;
                $qErr->qId = $qId;
                $qErr->description = makeValidInput($_POST["desc"]);

                try {
                    $qErr->save();
                    $tmp->status = -1;
                    $tmp->save();
                    SOQ::where('qId', '=', $qId)->delete();
                    echo "ok";
                    return;
                }
                catch (Exception $x) {
                    echo $x->getMessage();
                    return;
                }
            }

        }

        echo "nok";

    }

    public function unConfirmedDiscussionQ() {
        return view('unConfirmedDiscussionQ');
    }

    public function getUnConfirmedQuestions() {

        if(isset($_POST["page"])) {
            $page = (makeValidInput($_POST['page']) - 1) * 5;

            $questions = DB::select('select * from discussion WHERE id = relatedTo and status = 0 order by date DESC limit ' . $page . ', 5');
            $allow = true;

            foreach ($questions as $question) {
                if($allow) {
                    $question->totalCount = DB::select('select count(*) as countNum from discussion WHERE id = relatedTo and status = 0')[0]->countNum;
                    $allow = false;
                }
                $user = User::whereId($question->uId);
                if($user != null) {
                    $question->uId = $user->username;
                }
                else {
                    $question->uId = "نامشخص";
                }
                $question->date = convertStringToDate($question->date);

                $tmp = Question::whereId($question->qId);
                if($tmp == null)
                    continue;
                $level = User::where('id', '=', $tmp->author)->select('level')->first()->level;
                if($level == getValueInfo('adminLevel') || $level == getValueInfo('superAdminLevel'))
                    $question->fileName =  URL::asset('images/questions/system/' . $tmp->questionFile);
                else
                    $question->fileName = URL::asset('images/questions/student/' . $tmp->questionFile);
            }

            echo json_encode($questions);
        }
    }

    public function getConfirmedQuestions() {
        if(isset($_POST["page"])) {
            $page = (makeValidInput($_POST['page']) - 1) * 5;

            $questions = DB::select('select * from discussion WHERE id = relatedTo and status = 1 order by date DESC limit ' . $page . ', 5');
            $allow = true;

            foreach ($questions as $question) {

                if($allow) {
                    $question->totalCount = DB::select('select count(*) as countNum from discussion WHERE id = relatedTo and status = 1')[0]->countNum;
                    $allow = false;
                }

                $user = User::whereId($question->uId);
                if($user != null) {
                    $question->uId = $user->username;
                }
                else {
                    $question->uId = "نامشخص";
                }
                $question->date = convertStringToDate($question->date);

                $tmp = Question::whereId($question->qId);
                if($tmp == null)
                    continue;
                $level = User::where('id', '=', $tmp->author)->select('level')->first()->level;
                if($level == getValueInfo('adminLevel') || $level == getValueInfo('superAdminLevel'))
                    $question->fileName =  URL::asset('images/questions/system/' . $tmp->questionFile);
                else
                    $question->fileName = URL::asset('images/questions/student/' . $tmp->questionFile);
            }

            echo json_encode($questions);
        }
    }

    public function getConfirmedAndUnConfirmedQuestions() {
        if(isset($_POST["page"])) {
            $page = (makeValidInput($_POST['page']) - 1) * 5;

            $questions = DB::select('select * from discussion WHERE id = relatedTo order by date DESC limit ' . $page . ', 5');

            $allow = true;

            foreach ($questions as $question) {

                if($allow) {
                    $question->totalCount = DB::select('select count(*) as countNum from discussion WHERE id = relatedTo')[0]->countNum;
                    $allow = false;
                }
                $user = User::whereId($question->uId);
                if ($user != null) {
                    $question->uId = $user->username;
                } else {
                    $question->uId = "نامشخص";
                }
                $question->date = convertStringToDate($question->date);

                $tmp = Question::whereId($question->qId);
                if($tmp == null)
                    continue;
                $level = User::where('id', '=', $tmp->author)->select('level')->first()->level;
                if($level == getValueInfo('adminLevel') || $level == getValueInfo('superAdminLevel'))
                    $question->fileName =  URL::asset('images/questions/system/' . $tmp->questionFile);
                else
                    $question->fileName = URL::asset('images/questions/student/' . $tmp->questionFile);
            }
            echo json_encode($questions);
        }
    }

    public function changeQuestionStatus() {

        if(isset($_POST["qId"]) && isset($_POST["status"])) {

            $qId = makeValidInput($_POST["qId"]);
            $question = Discussion::whereId($qId);

            if($question == null) {
                echo "nok";
                return;
            }

            $question->status = makeValidInput($_POST["status"]);
            try {
                $question->save();
                echo "ok";
                return;
            }
            catch (Exception $x) {}
        }
        echo "nok";
    }

    public function unConfirmedDiscussionAns() {
        return view('unConfirmedDiscussionAns');
    }

    public function getUnConfirmedAnses() {

        if(isset($_POST["page"])) {
            $page = (makeValidInput($_POST['page']) - 1) * 5;

            $questions = DB::select('select * from discussion WHERE id <> relatedTo and status = 0 order by date DESC limit ' . $page . ', 5');
            $allow = true;

            foreach ($questions as $question) {
                if($allow) {
                    $question->totalCount = DB::select('select count(*) as countNum from discussion WHERE id <> relatedTo and status = 0')[0]->countNum;
                    $allow = false;
                }
                $user = User::whereId($question->uId);
                if($user != null) {
                    $question->uId = $user->username;
                }
                else {
                    $question->uId = "نامشخص";
                }
                $question->date = convertStringToDate($question->date);
                $question->question = Discussion::whereId($question->relatedTo)->description;

                $tmp = Question::whereId($question->qId);
                if($tmp == null)
                    continue;
                $level = User::where('id', '=', $tmp->author)->select('level')->first()->level;
                if($level == getValueInfo('adminLevel') || $level == getValueInfo('superAdminLevel'))
                    $question->fileName =  URL::asset('images/questions/system/' . $tmp->questionFile);
                else
                    $question->fileName = URL::asset('images/questions/student/' . $tmp->questionFile);
            }

            echo json_encode($questions);
        }
    }

    public function getConfirmedAnses() {

        if(isset($_POST["page"])) {
            $page = (makeValidInput($_POST['page']) - 1) * 5;

            $questions = DB::select('select * from discussion WHERE id <> relatedTo and status = 1 order by date DESC limit ' . $page . ', 5');
            $allow = true;

            foreach ($questions as $question) {
                if($allow) {
                    $question->totalCount = DB::select('select count(*) as countNum from discussion WHERE id <> relatedTo and status = 1')[0]->countNum;
                    $allow = false;
                }
                $user = User::whereId($question->uId);
                if($user != null) {
                    $question->uId = $user->username;
                }
                else {
                    $question->uId = "نامشخص";
                }
                $question->date = convertStringToDate($question->date);
                $question->question = Discussion::whereId($question->relatedTo)->description;

                $tmp = Question::whereId($question->qId);
                if($tmp == null)
                    continue;
                $level = User::where('id', '=', $tmp->author)->select('level')->first()->level;
                if($level == getValueInfo('adminLevel') || $level == getValueInfo('superAdminLevel'))
                    $question->fileName =  URL::asset('images/questions/system/' . $tmp->questionFile);
                else
                    $question->fileName = URL::asset('images/questions/student/' . $tmp->questionFile);
            }

            echo json_encode($questions);
        }
    }

    public function getConfirmedAndUnConfirmedAnses() {

        if(isset($_POST["page"])) {
            $page = (makeValidInput($_POST['page']) - 1) * 5;

            $questions = DB::select('select * from discussion WHERE id <> relatedTo order by date DESC limit ' . $page . ', 5');
            $allow = true;

            foreach ($questions as $question) {
                if($allow) {
                    $question->totalCount = DB::select('select count(*) as countNum from discussion WHERE id <> relatedTo')[0]->countNum;
                    $allow = false;
                }
                $user = User::whereId($question->uId);
                if($user != null) {
                    $question->uId = $user->username;
                }
                else {
                    $question->uId = "نامشخص";
                }
                $question->date = convertStringToDate($question->date);
                $question->question = Discussion::whereId($question->relatedTo)->description;

                $tmp = Question::whereId($question->qId);
                if($tmp == null)
                    continue;
                $level = User::where('id', '=', $tmp->author)->select('level')->first()->level;
                if($level == getValueInfo('adminLevel') || $level == getValueInfo('superAdminLevel'))
                    $question->fileName =  URL::asset('images/questions/system/' . $tmp->questionFile);
                else
                    $question->fileName = URL::asset('images/questions/student/' . $tmp->questionFile);
            }

            echo json_encode($questions);
        }
    }

}