<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Telegram\Bot\Commands\StartCommand;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramController extends Controller {

    public function getHome() {
        return view('home');
    }

    public function getUpdates() {

        $updates = Telegram::getUpdates();
        dd($updates);
    }

    public function getSendMessage()
    {
        return view('send-message');
    }

    public function postSendMessage() {


//        $msg = "Salam";
//
//        $command = new StartCommand();
//        Telegram::addCommand($command);

//        $update = Telegram::commandsHandler(true);
//
//        var_dump($update);
//
//        $response = Telegram::sendMessage([
//            'chat_id' => '88737881',
//            'text' => $msg,
//            'reply_markup' => $reply_markup
//        ]);

//        dd($response);

//        $messageId = $response->getMessageId();
//
//        dd($messageId);


//        $rules = [
//            'message' => 'required'
//        ];
//
//        $validator = Validator::make($request->all(), $rules);

//        if($validator->fails())
//        {
//            return redirect()->back()
//                ->with('status', 'danger')
//                ->with('message', 'Message is required');
//        }
//        env('GROUP_ID')
//        Telegram::sendMessage([
//            'chat_id' => '88737881',
//            'text' => $msg
//        ]);

//        return redirect()->back()
//            ->with('status', 'success')
//            ->with('message', 'Message sent');
    }

}