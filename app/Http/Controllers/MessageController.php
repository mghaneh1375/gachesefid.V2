<?php

namespace App\Http\Controllers;

use App\models\Message;
use App\models\NamayandeSchool;
use App\models\SchoolStudent;
use App\models\StudentAdviser;
use App\models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class MessageController extends Controller {

    private function formatDate($msg) {
        return $msg->date[0] . $msg->date[1] . $msg->date[2] . $msg->date[3] . '/'
        . $msg->date[4] . $msg->date[5] . '/' . $msg->date[6] . $msg->date[7];
    }

    public function showMessages($err = "", $currMsg = "", $subject = "", $dest = "") {

        $user = Auth::user();

        return view('message', array('user' => $user, "err" => $err, "currMsg" => $currMsg, 'dest' => $dest,
            'subject' => $subject, 'inMsgCount' => Message::whereReceiverId($user->id)->count(),
            'outMsgCount' => Message::whereSenderId($user->id)->count()));

    }

    public function sendMessage($dest) {

        $user = User::whereId($dest);

        if($user == null)
            return Redirect::route('profile');

        return $this->showMessages('پیام خود را وارد نمایید', '', '', $user->username);
    }

    public function getMessage() {

        $mId = makeValidInput($_POST["mId"]);

        $msg = Message::whereId($mId);

        $msg->senderId = User::whereId($msg->senderId)->username;
        $msg->receiverId = User::whereId($msg->receiverId)->username;

        $msg->date = $this->formatDate($msg);

        echo json_encode($msg);

    }

    public function opOnMsgs() {

        $uId =Auth::user()->id;

        if(isset($_POST["selectedMsg"]))
            $this->deleteMsg($_POST["selectedMsg"], $uId);

        echo 'ok';
    }

    public function deleteMsg($msgs, $uId) {

        for($i = 0; $i < count($msgs); $i++) {
            if(Message::whereId($msgs[$i])->senderId == $uId ||
                Message::whereId($msgs[$i])->receiverId == $uId)
            Message::destroy(makeValidInput($msgs[$i]));
        }
    }

    public function sendMsg() {

        $uId = Auth::user()->id;

        $destUser = makeValidInput($_POST["destUser"]);

        $destUser = User::whereUsername($destUser)->first();
        $currMsg = makeValidInput($_POST["msg"]);
        $subject = makeValidInput($_POST["subject"]);

        if($destUser == null)
            return $this->showMessages("نام کاربری وارد شده نامعتبر است", $currMsg, $subject);

        if($destUser->id == $uId)
            return $this->showMessages("نمی توانید پیامی را به خودتان ارسال کنید", $currMsg, $subject);

        if(Auth::user()->level == getValueInfo('adminLevel') || Auth::user()->level == getValueInfo('superAdminLevel')) {

            $msg = new Message();
            $msg->senderId = $uId;
            $msg->receiverId = $destUser->id;
            $msg->subject = $subject;
            $msg->message = $currMsg;
            $msg->date = getToday()["date"];
            $msg->status = 1;

            $msg->save();

            return Redirect::to(route('message'));
        }

        if(Auth::user()->level == getValueInfo('studentLevel')) {
            $condition = ['studentId' => $uId, 'adviserId' => $destUser->id];
            $condition2 = ['uId' => $uId, 'sId' => $destUser->id];
            if ($destUser->level == getValueInfo('adminLevel') || $destUser->level == getValueInfo('superAdminLevel') ||
                ($destUser->level == getValueInfo('adviserLevel') && StudentAdviser::where($condition)->count() > 0) ||
                ($destUser->level == getValueInfo('schoolLevel') && SchoolStudent::where($condition2)->count() > 0) ||
                ($destUser->level == getValueInfo('namayandeLevel') && checkUserAndNamayandeRelation($destUser->id, $uId))
            ) {

                $msg = new Message();
                $msg->senderId = $uId;
                $msg->receiverId = $destUser->id;
                $msg->subject = $subject;
                $msg->message = $currMsg;
                $msg->status = 0;
                $msg->date = getToday()["date"];

                $msg->save();

                return Redirect::to(route('message'));
            }

            return $this->showMessages("شما تنها می توانید به مشاور خود و یا ادمین سایت پیام ارسال نمایید", $currMsg, $subject);
        }

        elseif (Auth::user()->level == getValueInfo('adviserLevel')) {
            $condition = ['studentId' => $destUser->id, 'adviserId' => $uId];
            if ($destUser->level == getValueInfo('adminLevel') || $destUser->level == getValueInfo('superAdminLevel') ||
                ($destUser->level == getValueInfo('studentLevel') && StudentAdviser::where($condition)->count() > 0)
            ) {

                $msg = new Message();
                $msg->senderId = $uId;
                $msg->receiverId = $destUser->id;
                $msg->subject = $subject;
                $msg->message = $currMsg;
                $msg->status = 0;
                $msg->date = getToday()["date"];

                $msg->save();

                return Redirect::to(route('message'));
            }

            return $this->showMessages("شما تنها می توانید به دانش آموزان خود و یا ادمین سایت پیام ارسال نمایید", $currMsg, $subject);
        }

        elseif (Auth::user()->level == getValueInfo('schoolLevel')) {

            $condition = ['uId' => $destUser->id, 'sId' => $uId];

            if ($destUser->level == getValueInfo('adminLevel') || $destUser->level == getValueInfo('superAdminLevel') ||
                ($destUser->level == getValueInfo('studentLevel') && SchoolStudent::where($condition)->count() > 0)
            ) {

                $msg = new Message();
                $msg->senderId = $uId;
                $msg->receiverId = $destUser->id;
                $msg->subject = $subject;
                $msg->message = $currMsg;
                $msg->status = 0;
                $msg->date = getToday()["date"];

                $msg->save();

                return Redirect::to(route('message'));
            }

            return $this->showMessages("شما تنها می توانید به دانش آموزان خود و یا ادمین سایت پیام ارسال نمایید", $currMsg, $subject);
        }

        elseif (Auth::user()->level == getValueInfo('namyandeLevel')) {

            $condition = ['nId' => $uId, 'sId' => $destUser->id];

            if ($destUser->level == getValueInfo('adminLevel') || $destUser->level == getValueInfo('superAdminLevel') ||
                ($destUser->level == getValueInfo('studentLevel') && checkUserAndNamayandeRelation($uId, $destUser->id)) ||
                ($destUser->level == getValueInfo('schoolLevel') && NamayandeSchool::where($condition)->count() > 0)
            ) {

                $msg = new Message();
                $msg->senderId = $uId;
                $msg->receiverId = $destUser->id;
                $msg->subject = $subject;
                $msg->message = $currMsg;
                $msg->status = 0;
                $msg->date = getToday()["date"];

                $msg->save();

                return Redirect::to(route('message'));
            }

            return $this->showMessages("شما تنها می توانید به دانش آموزان خود و یا ادمین سایت پیام ارسال نمایید", $currMsg, $subject);
        }

        return $this->showMessages("خطایی در ارسال پیام رخ داده است", $currMsg, $subject);
    }

    public function showInboxSpecificMsgs($selectedUser) {

        $user = User::whereId(Auth::user()->id);

        return view('message', array('user' => $user, 'inMsgCount' => Message::whereReceiverId($user->id)->whereSenderId($selectedUser)->count(),
            'outMsgCount' => Message::whereSenderId($user->id)->whereReceiverId($selectedUser)->count(), 'selectedUser' => $selectedUser));

    }

    public function showOutboxSpecificMsgs($selectedUser) {

        $user = User::whereId(Auth::user()->id);

        return view('message', array('user' => $user, 'err' => 'outbox', 'inMsgCount' => Message::whereReceiverId($user->id)->whereSenderId($selectedUser)->count(),
            'outMsgCount' => Message::whereSenderId($user->id)->whereReceiverId($selectedUser)->count(), 'selectedUser' => $selectedUser));

    }

    public function getListOfMsgs() {

        $uId = Auth::user()->id;

        $mode = makeValidInput($_POST["mode"]);
        $sortMode = makeValidInput($_POST["sortMode"]);
        $selectedUser = makeValidInput($_POST["selectedUser"]);

        if($mode == "true") {

            if($selectedUser == -1)
                $inMsgs = Message::whereReceiverId($uId)->whereStatus(1)->orderBy('date', $sortMode)->get();
            else
                $inMsgs = Message::whereReceiverId($uId)->whereSenderId($selectedUser)->whereStatus(1)->orderBy('date', $sortMode)->get();

            foreach ($inMsgs as $inMsg) {
                $inMsg->target = User::whereId($inMsg->senderId)->username;
                $inMsg->date = $this->formatDate($inMsg);
            }
            echo json_encode($inMsgs);
        }
        else {

            if($selectedUser == -1)
                $outMsgs = Message::whereSenderId($uId)->orderBy('date', $sortMode)->get();
            else
                $outMsgs = Message::whereSenderId($uId)->whereReceiverId($selectedUser)->orderBy('date', $sortMode)->get();

            foreach ($outMsgs as $outMsg) {
                $outMsg->target = User::whereId($outMsg->receiverId)->username;
                $outMsg->date = $this->formatDate($outMsg);
            }
            echo json_encode($outMsgs);
        }

    }

    public function controlMsg() {

        return view('controlMsg', ['pendingCount' => Message::whereStatus(0)->count(),
            'acceptedCount' => Message::whereStatus(1)->count(),
            'rejectedCount' => Message::whereStatus(-1)->count()
        ]);

    }

    public function pendingMsgs() {

        $sortMode = makeValidInput($_POST["sortMode"]);

        $inMsgs = Message::whereStatus(0)->orderBy('date', $sortMode)->get();

        foreach ($inMsgs as $inMsg) {
            $inMsg->target = User::whereId($inMsg->senderId)->username;
            $inMsg->date = $this->formatDate($inMsg);
        }
        echo json_encode($inMsgs);

    }

    public function acceptedMsgs() {

        $sortMode = makeValidInput($_POST["sortMode"]);

        $inMsgs = Message::whereStatus(1)->orderBy('date', $sortMode)->get();

        foreach ($inMsgs as $inMsg) {
            $inMsg->target = User::whereId($inMsg->senderId)->username;
            $inMsg->date = $this->formatDate($inMsg);
        }
        echo json_encode($inMsgs);
    }

    public function rejectedMsgs() {

        $sortMode = makeValidInput($_POST["sortMode"]);

        $inMsgs = Message::whereStatus(-1)->orderBy('date', $sortMode)->get();

        foreach ($inMsgs as $inMsg) {
            $inMsg->target = User::whereId($inMsg->senderId)->username;
            $inMsg->date = $this->formatDate($inMsg);
        }
        echo json_encode($inMsgs);
    }

    public function acceptMsgs() {

        if(isset($_POST["selectedMsg"])) {
            $msgs = $_POST["selectedMsg"];
            for($i = 0; $i < count($msgs); $i++) {
                $tmp = Message::whereId(makeValidInput($msgs[$i]));
                if($tmp != null) {
                    $tmp->status = 1;
                    $tmp->save();
                }
            }
        }

        echo 'ok';
    }

    public function rejectMsgs() {

        if(isset($_POST["selectedMsg"])) {
            $msgs = $_POST["selectedMsg"];
            for($i = 0; $i < count($msgs); $i++) {
                $tmp = Message::whereId(makeValidInput($msgs[$i]));
                if($tmp != null) {
                    $tmp->status = -1;
                    $tmp->save();
                }
            }
        }

        echo 'ok';
    }

}