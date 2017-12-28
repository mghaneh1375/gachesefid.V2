<?php

namespace App\Http\Controllers;

use App\models\Calender;

class CalenderController extends Controller {

    public function calender() {
        return view('calender');
    }

    public function getEvents() {

        if(!isset($_POST["date"])) {
            echo "empty";
            return;
        }

        $date = makeValidInput($_POST["date"]);

        $date = explode('/', $date);

        if(count($date) == 3) {
            $date = $date[0] . $date[1] . $date[2];
            echo Calender::where('date', '=', $date)->get();
        }
        else {
            echo "empty";
        }

    }

    public function addEvent() {

        if(!isset($_POST["date"]) || !isset($_POST["desc"])) {
            echo "nok";
            return;
        }

        $date = makeValidInput($_POST["date"]);
        $desc = makeValidInput($_POST["desc"]);

        $date = explode('/', $date);

        $date = $date[0] . $date[1] . $date[2];

        $event = new Calender();
        $event->date = $date;
        $event->event = $desc;

        $event->save();

        echo "ok";

    }

    public function deleteEvent() {

        if(!isset($_POST["id"])) {
            echo "nok";
            return;
        }

        $id = makeValidInput($_POST["id"]);

        Calender::destroy($id);

        echo "ok";
    }
}