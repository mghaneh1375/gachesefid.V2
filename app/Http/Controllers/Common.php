<?php

use App\models\Question;
use App\models\RedundantInfo1;
use App\models\City;
use App\models\State;
use App\models\SchoolStudent;
use App\models\School;
use App\models\OrderId;
use App\models\Mellat;
use App\models\User;
use Illuminate\Support\Facades\Auth;


function makeValidInput($input) {
    $input = addslashes($input);
    $input = trim($input);
    if(get_magic_quotes_gpc())
        $input = stripslashes($input);
    $input = htmlspecialchars($input);
    return $input;
}

function getStdCityAndState($uId) {

    $tmp = RedundantInfo1::whereUId($uId)->first();

    if($tmp == null) {
        $cityTmp = City::first();
        return ["city" => $cityTmp->name, "state" => State::whereId($cityTmp->stateId)->name,
            'cityId' => $cityTmp->id, 'stateId' => State::whereId($cityTmp->stateId)->id];
    }

    $cityId = $tmp->cityId;

    $city = City::whereId($cityId);
    if($city == null) {
        $cityTmp = City::first();
        return ["city" => $cityTmp->name, "state" => State::whereId($cityTmp->stateId)->name,
            'cityId' => $cityTmp->id, 'stateId' => State::whereId($cityTmp->stateId)->id];
    }

    return ["city" => $city->name, "state" => State::whereId(City::whereId($cityId)->stateId)->name,
        'cityId' => $cityId, 'stateId' => State::whereId(City::whereId($cityId)->stateId)->id];

}

function getStdSchoolName($uId) {

    $tmp = SchoolStudent::whereUId($uId)->first();
    if($tmp == null) {
        $tmp = RedundantInfo1::whereUId($uId)->first();

        if($tmp == null) {
            return "";
        }

        return $tmp->schoolName;
    }
    else {
        $tmp = School::whereUId($tmp->sId)->first();
        if($tmp == null) {
            return "";
        }
        return $tmp->name;
    }

}

function getQuestionLevel($qId) { // strategies should be taken

    $question = Question::whereId($qId);
    if($question == null)
        return "متوسط";

    switch ($question->level) {
        case 1:
            return "ساده"; // easy
            break;
        case 2:
        default:
            return "متوسط"; // average
            break;
        case 3:
            return "دشوار"; // hard
            break;
    }
}

function checkUserAndNamayandeRelation($nId, $uId) {

    $tmp = DB::select('select count(*) as countNum from namayandeSchool nS, schoolStudent sS WHERE nS.sId = sS.sId and nS.nId ' . $nId
        . ' and sS.uId = ' . $uId);

    if($tmp == null || count($tmp) == 0 || $tmp[0]->countNum == 0)
        return false;
    return true;
}

function calcRank($quizId, $uId) {
    $regularQuizMode = getValueInfo('regularQuiz');

    $ranks = DB::select('SELECT quizRegistry.uId, sum(taraz.taraz * (SELECT lesson.coherence FROM lesson WHERE lesson.id = taraz.lId)) as weighted_avg from quizRegistry, taraz WHERE quizMode = ' . $regularQuizMode . ' and quizRegistry.id = taraz.qEntryId and quizRegistry.qId = ' . $quizId . ' GROUP by (quizRegistry.uId) ORDER by weighted_avg DESC');

    $tmp = DB::select('SELECT DISTINCT L.id, L.name, L.coherence from lesson L, SOQ SO, subject S, regularQOQ QO WHERE QO.quizId = ' . $quizId . ' and QO.questionId = SO.qId and SO.sId = S.id and S.lessonId = L.id order by L.id ASC');
    $sum = 0;

    if($tmp == null || count($tmp) == 0)
        $sum = 1;

    else {
        foreach ($tmp as $itr) {
            $sum += $itr->coherence;
        }
    }

    for($i = 0; $i < count($ranks); $i++) {

        if($ranks[$i]->uId == $uId) {

            $r = $i + 1;

            $currTaraz = $ranks[$i]->weighted_avg;

            $currTaraz = round($currTaraz / $sum, 0);
            $k = $i - 1;
            while ($k >= 0 && round($ranks[$k]->weighted_avg / $sum, 0) == $currTaraz) {
                $k--;
                $r--;
            }

            return $r;
        }
    }
    return count($ranks);
}

function calcRankInCity($quizId, $uId, $cityId) {
    $ranks = DB::select('SELECT quizRegistry.uId, sum(taraz.taraz * (SELECT lesson.coherence FROM lesson WHERE lesson.id = taraz.lId)) as weighted_avg from quizRegistry, taraz, redundantInfo1 rd WHERE quizRegistry.id = taraz.qEntryId and quizRegistry.uId = rd.uId AND rd.cityId = ' . $cityId . ' and quizRegistry.qId = ' . $quizId . ' GROUP by (quizRegistry.uId) ORDER by weighted_avg DESC');
    for($i = 0; $i < count($ranks); $i++) {
        if($ranks[$i]->uId == $uId) {
            $r = $i + 1;
            $currTaraz = $ranks[$i]->weighted_avg;
            $k = $i - 1;
            while ($k >= 0 && $ranks[$k]->weighted_avg == $currTaraz) {
                $k--;
                $r--;
            }
            return $r;
        }
    }
    return count($ranks);
}

function calcRankInState($quizId, $uId, $stateId) {
    $ranks = DB::select('SELECT quizRegistry.uId, sum(taraz.taraz * (SELECT lesson.coherence FROM lesson WHERE lesson.id = taraz.lId)) as weighted_avg from quizRegistry, taraz, redundantInfo1 rd, city ci WHERE quizRegistry.id = taraz.qEntryId and quizRegistry.uId = rd.uId AND rd.cityId = ci.id and ci.stateId = ' . $stateId . ' and quizRegistry.qId = ' . $quizId . ' GROUP by (quizRegistry.uId) ORDER by weighted_avg DESC');
    for($i = 0; $i < count($ranks); $i++) {
        if($ranks[$i]->uId == $uId) {
            $r = $i + 1;
            $currTaraz = $ranks[$i]->weighted_avg;
            $k = $i - 1;
            while ($k >= 0 && $ranks[$k]->weighted_avg == $currTaraz) {
                $k--;
                $r--;
            }
            return $r;
        }
    }
    return count($ranks);
}

function sendSMS($destNum, $text, $templateId, $text2 = "", $text3 = "") {

    require __DIR__ . '/../../../vendor/autoload.php';

    try{
        $api = new \Kavenegar\KavenegarApi("34766E574B6B4F7A306F3167544556473164387749673D3D");
//        $sender = "10000008008080";
//        $result = $api->Send("30006703323323","09214915905","خدمات پیام کوتاه کاوه نگار");
        $result = $api->VerifyLookup($destNum, $text, $text2, $text3, $templateId);

        if($result){
            foreach($result as $r){
                return $r->messageid;
            }
        }
    }
    catch(\Kavenegar\Exceptions\ApiException $e){
        // در صورتی که خروجی وب سرویس 200 نباشد این خطا رخ می دهد
        echo $e->errorMessage();
        return -1;
    }
    catch(\Kavenegar\Exceptions\HttpException $e){
        // در زمانی که مشکلی در برقرای ارتباط با وب سرویس وجود داشته باشد این خطا رخ می دهد
        echo $e->errorMessage();
        return -1;
    }
    return -1;
}

function SendREST($Destination, $MsgBody, $Encoding) {

    $username = "gachesefid";
    $password = "irysc1361@";
    $Source = "02166591203";

    $URL = "http://panel.asanak.ir/webservice/v1rest/sendsms";
    $msg = urlencode(trim($MsgBody));
    $url = $URL.'?username='.$username.'&password='.$password.'&source='.$Source.'&destination='.$Destination.'&message='. $msg;
    $headers[] = 'Accept: text/html';
    $headers[] = 'Connection: Keep-Alive';
    $headers[] = 'Content-type: application/x-www-form-urlencoded;charset=UTF-8';
    $process = curl_init($url);
    curl_setopt($process, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($process, CURLOPT_HEADER, 0);
    curl_setopt($process, CURLOPT_TIMEOUT, 30);
    curl_setopt($process, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);
    try
    {
        if(($return = curl_exec($process)))
        {
            return 'ans is ' . $return;
        }
    } catch (Exception $ex)
    {
        return $ex->errorMessage();
    }
}

function msgStatus($username, $password, $msgId) {

    $URL = "http://panel.asanak.ir/webservice/v1rest/msgstatus";
    $url = $URL.'?username='.$username.'&password='.$password.'&msgid='.$msgId;
    $headers[] = 'Accept: text/html';
    $headers[] = 'Connection: Keep-Alive';
    $headers[] = 'Content-type: application/x-www-form-urlencoded;charset=UTF-8';
    $process = curl_init($url);
    curl_setopt($process, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($process, CURLOPT_HEADER, 0);
    curl_setopt($process, CURLOPT_TIMEOUT, 30);
    curl_setopt($process, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);
    try
    {
        if(($return = curl_exec($process)))
        {
            return 'ans is ' . $return;
        }
    } catch (Exception $ex)
    {
        return $ex->errorMessage();
    }
}

function _custom_check_national_code($code) {

    if(!preg_match('/^[0-9]{10}$/',$code))
        return false;

    for($i=0;$i<10;$i++)
        if(preg_match('/^'.$i.'{10}$/',$code))
            return false;
    for($i=0,$sum=0;$i<9;$i++)
        $sum+=((10-$i)*intval(substr($code, $i,1)));
    $ret=$sum%11;
    $parity=intval(substr($code, 9,1));
    if(($ret<2 && $ret==$parity) || ($ret>=2 && $ret==11-$parity))
        return true;
    return false;
}

function getValueInfo($key) {

    $values = ["money1" => 1, "money2" => 2, "invitationTransaction" => 1, 'redundant1Transaction' => 2, 'initTransaction' => 8,
        'redundant2Transaction' => 3, "studentLevel" => 1, "adviserLevel" => 2, 'operator1Level' => 3, 'schoolLevel' => 9,
        'operator2Level' => 4, 'adminLevel' => 5, "superAdminLevel" => 6, 'controllerLevel' => 7, 'namayandeLevel' => 8,
        'sampadSch' => 1, 'gheyrSch' => 2, 'nemoneSch' => 3, 'shahedSch' => 4, 'sayerSch' => 5, 'HeyatSch' => 6, 'dolatiSch' => 7,
        'staticOffCode' => 1, 'dynamicOffCode' => 2, 'chargeTransaction' => 4, 'systemQuiz' => 1, 'motevaseteAval' => 0, 'motevaseteDovom' => 1, 'dabestan' => 2, 'quizRankTransaction' => 9,
        'regularQuiz' => 2, 'questionQuiz' => 3, 'systemQuizTransaction' => 5, 'regularQuizTransaction' => 6, 'regularQuizGroupTransaction' => 7, 'questionBuyTransaction' => 10,
        'konkurAdvise' => 1, 'olympiadAdvise' => 2, 'doore1Advice' => 3, 'doore2Advice' => 4, 'baliniAdvice' => 5, 'unknownAdvice' => 6,
        'diplom' => 1, 'foghDiplom' => 2, 'lisans' => 3, 'foghLisans' => 4, 'phd' => 5, 'unknown' => 6
    ];

    return $values[$key];

}

function calcTimeLenQuiz($quizId, $mode) {

    if($mode == "self")
        $timeLen = DB::select("select SUM(question.neededTime) as timeLen from question, soldQuestion where quizId = " . $quizId . " and qId = question.id");
    else if($mode == 'system')
        $timeLen = DB::select("select SUM(question.neededTime) as timeLen from question, systemQOQ where quizId = " . $quizId . " and questionId = question.id");
    else
        $timeLen = DB::select("select SUM(question.neededTime) as timeLen from question, regularQOQ where quizId = " . $quizId . " and questionId = question.id");

    if($timeLen == null || count($timeLen) == 0 || empty($timeLen[0]->timeLen))
        return 1;

    $timeLen = 1.1 * $timeLen[0]->timeLen;
    return ceil($timeLen / 60);
}

function uploadCheck($target_file, $name, $section, $limitSize, $ext) {

    $err = "";
    $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);

    $uploadOk = 1;
    $check = true;

    if($ext != "xlsx")
        $check = getimagesize($_FILES[$name]["tmp_name"]);

    if($check === false) {
        $err .= "فایل ارسالی در قسمت " . $section . " معتبر نمی باشد" .  "<br />";
        $uploadOk = 0;
    }

    if ($uploadOk == 1 && $_FILES[$name]["size"] > $limitSize)
        $err .= "حداکثر حجم مجاز برای آپلود تصویر $limitSize کیلو بایت می باشد" . "<br />";

    $imageFileType = strtolower($imageFileType);

    if($ext != -1 && $imageFileType != $ext)
        $err .= "شما تنها فایل های $ext. را می توانید در این قسمت آپلود نمایید" . "<br />";
    return $err;
}

function upload($target_file, $name, $section) {
    $err = "";
    try {
        move_uploaded_file($_FILES[$name]["tmp_name"], $target_file);
    }
    catch (Exception $x) {
        return "اشکالی در آپلود تصویر در قسمت " . $section . " به وجود آمده است" . "<br />";
    }
    return "";
//        $err .= ;
//    return $err;
}

function generateActivationCode() {
    return rand(10000, 99999);
}

function generateInvitationCode() {

    return rand(10000, 99999);
    
    $init1 = 65;
    $init2 = 97;

    while (true) {
        $code = "";

        for ($i = 0; $i < 10; $i++) {
            if (rand(0, 1) == 0)
                $code .= chr(rand(0, 25) + $init1);
            else
                $code .= chr(rand(0, 25) + $init2);
        }

        if (User::whereInvitationCode($code)->count() == 0)
            return $code;
    }
    return null;
}

function composeDate($date) {
    $date = explode('/', $date);
    return $date[0] . $date[1] . $date[2];
}

function composeTime($time) {
    $time = explode(':', $time);
    return $time[0] . $time[1];
}

function getToday() {

    include_once 'jdate.php';

    $jalali_date = jdate("c");

    $date_time = explode('-', $jalali_date);

    $subStr = explode('/', $date_time[0]);

    $day = $subStr[0] . $subStr[1] . $subStr[2];

    $time = explode(':', $date_time[1]);

    $time = $time[0] . $time[1];

    return ["date" => $day, "time" => $time];
}

function getPast($past) {

    include_once 'jdate.php';
    
    $jalali_date = jdate("c", $past);

    $date_time = explode('-', $jalali_date);

    $subStr = explode('/', $date_time[0]);

    $day = $subStr[0] . $subStr[1] . $subStr[2];

    $time = explode(':', $date_time[1]);

    $time = $time[0] . $time[1];

    return ["date" => $day, "time" => $time];
}

function convertStringToTime($time) {
    return $time[0] . $time[1] . ":" . $time[2] . $time[3];
}

function convertStringToDate($date) {
    return $date[0] . $date[1] . $date[2] . $date[3] . '/' . $date[4] . $date[5] . '/' . $date[6] . $date[7];
}

function convertDateToString($date) {
    $subStrD = explode('/', $date);
    return $subStrD[0] . $subStrD[1] . $subStrD[2];
}

function convertTimeToString($time) {
    $subStrT = explode(':', $time);
    return $subStrT[0] . $subStrT[1];
}

function sumTimes($time1, $time2) {

    if(strlen($time1) != 4)
        return -1;

    $limit = 4 - strlen($time2);
    $tmp = "";

    for ($i = 0; $i < $limit; $i++)
        $tmp .= "0";

    $time2 = $tmp . $time2;

    $digit3 = $time2[3] + $time1[3];
    $reminder = ($digit3 > 10) ? floor($digit3 / 10) : 0;
    $digit3 = ($digit3 > 10) ? $digit3 - 10 : $digit3;

    $digit2 = $time1[2] + $time2[2] + $reminder;
    $reminder = ($digit2 > 6) ? floor($digit2 / 6) : 0;
    $digit2 = ($digit2 > 6) ? $digit2 - 6 : $digit2;

    $digit1 = $time2[1] + $time1[1] + $reminder;
    $reminder = ($digit1 > 10) ? floor($digit1 / 10) : 0;
    $digit1 = ($digit1 > 10) ? $digit1 - 10 : $digit1;

    $digit0 = $time2[0] + $time1[0] + $reminder;
    $digit0 = ($digit0 > 10) ? $digit0 - 10 : $digit0;

    return $digit0 . $digit1 . $digit2 . $digit3;

}

function subTimes($time1, $time2) {

    if(strlen($time1) != 4)
        return -1;

    $time1 = $time1 . "00";

    $limit = 4 - strlen($time2);
    $tmp = "";

    for ($i = 0; $i < $limit; $i++)
        $tmp .= "0";

    $time2 = $tmp . $time2 . date('s');


    $digit5 = $time1[5] - $time2[5];
    $reminder = ($digit5 < 0) ? -1 : 0;
    $digit5 = ($digit5 < 0) ? $digit5 + 10 : $digit5;

    $digit4 = $time1[4] - $time2[4] + $reminder;
    $reminder = ($digit4 < 0) ? -1 : 0;
    $digit4 = ($digit4 < 0) ? $digit4 + 6 : $digit4;

    $digit3 = $time1[3] - $time2[3] + $reminder;
    $reminder = ($digit3 < 0) ? -1 : 0;
    $digit3 = ($digit3 < 0) ? $digit3 + 10 : $digit3;

    $digit2 = $time1[2] - $time2[2] + $reminder;
    $reminder = ($digit2 < 0) ? -1 : 0;
    $digit2 = ($digit2 < 0) ? $digit2 + 6 : $digit2;

    $digit1 = $time1[1] - $time2[1] + $reminder;
    $reminder = ($digit1 < 0) ? -1 : 0;
    $digit1 = ($digit1 < 0) ? $digit1 + 10 : $digit1;

    $digit0 = $time1[0] - $time2[0] + $reminder;

    $out = "";
    if($digit0 != "0")
        $out .= $digit0;
    if($digit1 != "0" || $out != "")
        $out .= $digit1;
    if($digit2 != "0" || $out != "")
        $out .= $digit2;
    if($digit3 != "0" || $out != "")
        $out .= $digit3;

    if($out != "")
        $out *= 60;

    if($digit4 != "0" || $out != "")
        $out += $digit4 * 10;
    if($digit5 != "0" || $out != "")
        $out += $digit5;

    return $out;

}

function suggestionQuestionsCount($gradeId, $lId, $sId, $uId, $level, $like, $needed) {

    $query = 'select question.level, question.id as qId, subject.name as subjectName, lesson.name as lessonName, subject.price1, subject.price2, subject.price3 from question, SOQ, subject, lesson where SOQ.sId = subject.id and SOQ.qId = question.id AND ' .
        'subject.lessonId = lesson.id and question.id = SOQ.qId ';

    if($lId != -1) {
        if($sId != -1){
            if($level != -1)
                $query .= 'and question.level = ' . $level . ' ';
        }
        else {
            if($level != -1)
                $query .= 'and subject.lessonId = ' . $lId . ' and question.level = ' . $level . ' ';

            else
                $query .= 'and subject.lessonId = ' . $lId . ' ';
        }
    }

    else {
        if($level != -1)
            $query .= 'and lesson.gradeId = ' . $gradeId . ' and question.level = ' . $level . ' ';
        else

            $query .= 'and lesson.gradeId = ' . $gradeId . ' ';
    }

    $query .= 'and (select count(*) from soldQuestion sQ, userCreatedQuiz UCQ where UCQ.uId = ' . $uId . ' and UCQ.id = sQ.quizId and sQ.qId = question.id) = 0 and ' .
        '(select count(*) from quizRegistry qR, regularQOQ rQ where qR.uId = ' . $uId . ' and rQ.questionId = question.id and ' .
        'qR.qId = rQ.quizId and qR.quizMode =  ' . getValueInfo('regularQuiz') . ') = 0 and ' .
        '(select count(*) from quizRegistry qR, systemQOQ sQ where qR.uId = ' . $uId . ' and sQ.questionId = question.id and ' .
        'qR.qId = sQ.quizId and qR.quizMode =  ' . getValueInfo('systemQuiz') . ') = 0 and ' .
        'question.author <> ' . $uId . " limit 0, $needed";

    $result = DB::select($query);


    if($result == null || count($result) == 0 || count($result) < $needed)
        return [];


    foreach ($result as $itr) {
        switch($itr->level) {
            case 1:
                $itr->price = $itr->price1;
                $itr->level = "ساده";
                break;
            case 2:
                $itr->price = $itr->price2;
                $itr->level = "متوسط";
                break;
            case 3:
                $itr->price = $itr->price3;
                $itr->level = "دشوار";
                break;
        }
    }

    return $result;
}
//
//function suggestQuestions($gradeId, $lId, $sId, $level, $uId, $like, $needed) {
//
//    if($result != null && count($result) > 0) {
//        foreach ($result as $itr) {
//
//            if($itr->level == 1)
//                $tmp = DB::select('select subject.name as subjectName, subject.price1 as price, lesson.name as lessonName from subject, SOQ, lesson WHERE SOQ.qId = ' . $itr->id . ' and
//                    SOQ.sId = subject.id and subject.lessonId = lesson.id');
//            else if($itr->level == 2)
//                $tmp = DB::select('select subject.name as subjectName, subject.price2 as price, lesson.name as lessonName from subject, SOQ, lesson WHERE SOQ.qId = ' . $itr->id . ' and
//                    SOQ.sId = subject.id and subject.lessonId = lesson.id');
//            else
//                $tmp = DB::select('select subject.name as subjectName, subject.price3 as price, lesson.name as lessonName from subject, SOQ, lesson WHERE SOQ.qId = ' . $itr->id . ' and
//                    SOQ.sId = subject.id and subject.lessonId = lesson.id');
//
//            if($tmp != null && count($tmp) > 0) {
//                $itr->subject = $tmp[0]->subjectName;
//                $itr->lesson = $tmp[0]->lessonName;
//                $itr->price = $tmp[0]->price;
//            }
//            else {
//                $itr->subject = "نامشخص";
//                $itr->lesson = 'نامشخص';
//                $itr->price = "نامشخص";
//            }
//
//            switch ($itr->level) {
//                case 1:
//                default:
//                    $itr->level = "ساده";
//                    break;
//                case 2:
//                    $itr->level = "متوسط";
//                    break;
//                case 3:
//                    $itr->level = "دشوار";
//                    break;
//            }
//        }
//    }
//
//    return $result;
//}

function getLessonQuiz($quizId) {

    $lIds = DB::select('SELECT DISTINCT L.id, L.name, L.coherence from lesson L, SOQ SO, subject S, regularQOQ QO WHERE QO.quizId = ' . $quizId . ' and QO.questionId = SO.qId and SO.sId = S.id and S.lessonId = L.id order by L.id ASC');
    if(count($lIds) > 0)
        return $lIds;
    return [];
}

function getSubjectQuiz($quizId) {

    $sIds = DB::select('SELECT DISTINCT S.id, S.name, S.lessonId from SOQ SO, subject S, regularQOQ QO WHERE QO.quizId = ' . $quizId . ' and QO.questionId = SO.qId and SO.sId = S.id order by S.id ASC');
    if(count($sIds) > 0)
        return $sIds;
    return [];
}

function payment($amount, $callBackUrl, $useGift) {

//    require_once("lib/nusoap.php");

    $client = new nusoap_client('https://bpm.shaparak.ir/pgwchannel/services/pgw?wsdl');
    $namespace = 'http://interfaces.core.sw.bps.com/';

    $terminalId = 909350;
    $userName = "irysc";
    $userPassword = "ir99ys";

    $localDate = str_split(date("YYMMDD"), 8)[0];
    $localTime = str_split(date("HHIISS"), 6)[0];

    $additionalData = "";

    $uId = Auth::user()->id;

    $tmp = new OrderId();
    $orderId = rand(1, 1000000000);

    while (OrderId::whereCode($orderId)->count() > 0)
        $orderId = rand(1, 1000000000);

    $tmp->code = $orderId;
    $tmp->save();

    $err = $client->getError();
    if ($err)
        return -1;

    $parameters = array(
        'terminalId' => $terminalId,
        'userName' => $userName,
        'userPassword' => $userPassword,
        'orderId' => $orderId,
        'amount' => $amount,
        'localDate' => $localDate,
        'localTime' => $localTime,
        'additionalData' => $additionalData,
        'callBackUrl' => $callBackUrl,
        'payerId' => 0);


    // Call the SOAP method
    $result = $client->call('bpPayRequest', $parameters, $namespace);

    // Check for a fault

    if ($client->fault)
        return -1;

    // Check for errors

    $resultStr  = $result;

    $err = $client->getError();
    if ($err)
        return -1;
    // Display the result

    $res = explode (',', $resultStr);

//    echo "<script>alert('Pay Response is : " . $resultStr . "');</script>";
//    echo "Pay Response is : " . $resultStr;

    $ResCode = $res[0];

    if ($ResCode == "0") {
        // Update table, Save RefId
        $mellat = new Mellat();
        $mellat->uId = $uId;
        $mellat->amount = $amount;
        $mellat->date = $localDate;
        $mellat->time = $localTime;
        $mellat->refId = $res[1];
        $mellat->saleOrderId = $orderId;
        $mellat->saleReferenceId = 11;
        $mellat->status = 1;
        $mellat->gift = $useGift;

        $mellat->save();

        return $res[1];
    }
    return $ResCode;
}