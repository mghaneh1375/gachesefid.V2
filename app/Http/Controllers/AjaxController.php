<?php

namespace App\Http\Controllers;

use App\models\QuizRegistry;
use App\models\Enheraf;
use App\models\State;
use App\models\City;
use App\models\Taraz;

class AjaxController extends Controller {

    public function getStateCity() {

        if(isset($_POST["cityId"])) {
            echo State::whereId(City::whereId(makeValidInput($_POST["cityId"]))->stateId)->id;
        }

    }

    public function calcTaraz() {

        if (isset($_POST["qEntryId"])) {

            $qEntryId = makeValidInput($_POST["qEntryId"]);
            $quizId = QuizRegistry::whereId($qEntryId);

            if($quizId == null) {
                echo "nok";
                return;
            }

            $quizId = $quizId->qId;
            $enherafMeyars = Enheraf::whereQId($quizId)->get();
            if($enherafMeyars == null || count($enherafMeyars) == 0) {
                echo "nok";
                return;
            }
            
            foreach ($enherafMeyars as $itr) {
                $lId = $itr->lId;
                $enherafMeyar = $itr->val;
                $lessonAVG = $itr->lessonAVG;
                $conditions = ["qEntryId" => $qEntryId, "lId" => $lId];
                $taraz = Taraz::where($conditions)->first();

                if ($enherafMeyar == 0)
                    $enherafMeyar++;

                if ($taraz != null) {
                    if ($enherafMeyar == 0)
                        $taraz->taraz = 5000;
                    else
                        $taraz->taraz = 1000 * ((($taraz->percent + $taraz->percent2 + $taraz->percent3) - $lessonAVG) / $enherafMeyar) + 5000;
                    $taraz->save();
                }
            }
            echo "ok";
            return;
        }
        echo "nok";
    }
    
    public function getQuizLessons() {
        $qId = makeValidInput($_POST["qId"]);
        echo json_encode(getLessonQuiz($qId));
    }
}