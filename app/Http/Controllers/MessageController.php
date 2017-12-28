<?php

class MessageController extends Controller {

    private function formatDate($msg) {
        return $msg->date[0] . $msg->date[1] . $msg->date[2] . $msg->date[3] . '/'
        . $msg->date[4] . $msg->date[5] . '/' . $msg->date[6] . $msg->date[7];
    }

    public function showMessages($err = "", $currMsg = "", $subject = "") {

        $uId = Auth::user()->id;

        $user = User::find($uId);

        return view('message', array('user' => $user, "err" => $err, "currMsg" => $currMsg,
            'subject' => $subject, 'inMsgCount' => Message::where('recieverId', '=', $user->id)->count(),
            'outMsgCount' => Message::where('senderId', '=', $user->id)->count()));

    }

    public function getMessage() {

        $mId = makeValidInput($_POST["mId"]);

        $msg = Message::find($mId);

        $msg->senderId = User::find($msg->senderId)->username;
        $msg->recieverId = User::find($msg->recieverId)->username;

        $msg->date = $this->formatDate($msg);

        echo json_encode($msg);

    }

    public function opOnMsgs() {

        $uId = makeValidInput(Session::get('uId', -1));

        if(isset($_POST["selectedMsg"]))
            $this->deleteMsg($_POST["selectedMsg"], $uId);

        echo 'ok';
    }

    public function deleteMsg($msgs, $uId) {

        for($i = 0; $i < count($msgs); $i++) {
            if(Message::find($msgs[$i])->senderId == $uId ||
                Message::find($msgs[$i])->recieverId == $uId)
            Message::destroy(makeValidInput($msgs[$i]));
        }
    }

    public function sendMsg() {

        $uId = Auth::user()->id;

        $destUser = makeValidInput($_POST["destUser"]);

        $destUser = User::where("username", '=', $destUser)->first();
        $currMsg = makeValidInput($_POST["msg"]);
        $subject = makeValidInput($_POST["subject"]);

        if($destUser == null || count($destUser) == 0)
            return $this->showMessages("نام کاربری وارد شده نامعتبر است", $currMsg, $subject);

        if($destUser->id == $uId)
            return $this->showMessages("نمی توانید پیامی را به خودتان ارسال کنید", $currMsg, $subject);

        $msg = new Message();
        $msg->senderId = $uId;
        $msg->recieverId = $destUser->id;
        $msg->subject = $subject;
        $msg->message = $currMsg;
        $msg->date = getToday()["date"];

        $msg->save();

        return Redirect::to(route('message'));
    }

    public function getListOfMsgs() {

        $uId = makeValidInput(Session::get('uId', -1));

        $mode = makeValidInput($_POST["mode"]);
        $sortMode = makeValidInput($_POST["sortMode"]);

        if($mode == "true") {

            $inMsgs = Message::where('recieverId', '=', $uId)->orderBy('date', $sortMode)->get();

            foreach ($inMsgs as $inMsg) {
                $inMsg->target = User::find($inMsg->senderId)->username;
                $inMsg->date = $this->formatDate($inMsg);
            }
            echo json_encode($inMsgs);
        }
        else {

            $outMsgs = Message::where('senderId', '=', $uId)->orderBy('date', $sortMode)->get();

            foreach ($outMsgs as $outMsg) {
                $outMsg->target = User::find($outMsg->recieverId)->username;
                $outMsg->date = $this->formatDate($outMsg);
            }
            echo json_encode($outMsgs);
        }

    }
}