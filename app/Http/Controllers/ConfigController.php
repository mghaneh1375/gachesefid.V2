<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;
use App\models\PointConfig;
use App\models\ConfigModel;
use App\models\KindKarname;

class ConfigController extends Controller {

    public function pointsConfig() {

        return view('pointConfig', array('config' => PointConfig::first()));

    }

    public function doPointsConfig() {

        if(isset($_POST["invitationPoint"]) && isset($_POST["makeQuestionPoint"]) && isset($_POST["init"]) &&
            isset($_POST["rankInQuizPoint"]) && isset($_POST["solveQuestionPoint"])) {

            $config = PointConfig::first();
            $config->invitationPoint = makeValidInput($_POST["invitationPoint"]);
            $config->rankInQuizPoint = makeValidInput($_POST["rankInQuizPoint"]);
            $config->makeQuestionPoint = makeValidInput($_POST["makeQuestionPoint"]);
            $config->solveQuestionPoint = makeValidInput($_POST["solveQuestionPoint"]);
            $config->infoPass2Point = makeValidInput($_POST["infoPass2Point"]);
            $config->infoPass3Point = makeValidInput($_POST["infoPass3Point"]);
            $config->init = makeValidInput($_POST["init"]);

            $config->save();

        }
        
        return Redirect::to(route('pointsConfig'));

    }

    public function config() {
        return view('config', array('config' => ConfigModel::first()));
    }

    public function doConfig() {

        if(isset($_POST["advisorPercent"]) && isset($_POST["makeQuestionMin"]) &&
            isset($_POST["rankInQuiz"]) && isset($_POST["moneyMin"]) && isset($_POST["questionMin"]) &&
            isset($_POST["likeMin"]) && isset($_POST["percentOfPackage"]) && isset($_POST["percentOfQuizes"])) {

            $config = ConfigModel::first();
            $config->advisorPercent = makeValidInput($_POST["advisorPercent"]);
            $config->makeQuestionMin = makeValidInput($_POST["makeQuestionMin"]);
            $config->rankInQuiz = makeValidInput($_POST["rankInQuiz"]);
            $config->moneyMin = makeValidInput($_POST["moneyMin"]);
            $config->questionMin = makeValidInput($_POST["questionMin"]);
            $config->likeMin = makeValidInput($_POST["likeMin"]);
            $config->percentOfPackage = makeValidInput($_POST["percentOfPackage"]);
            $config->percentOfQuizes = makeValidInput($_POST["percentOfQuizes"]);

            $config->save();

        }

        return Redirect::to(route('config'));

    }

    public function defineKarname() {
        return view('defineKarname', array('kindKarname' => KindKarname::first()));
    }

    public function doDefineKarname() {
        if(isset($_POST["doDefine"])) {
            $kindKarname = KindKarname::first();
            $kindKarname->lessonAvg = (isset($_POST["lessonAvg"]));
            $kindKarname->subjectAvg = (isset($_POST["subjectAvg"]));
            $kindKarname->lessonStatus = (isset($_POST["lessonStatus"]));
            $kindKarname->subjectStatus = (isset($_POST["subjectStatus"]));
            $kindKarname->lessonMaxPercent = (isset($_POST["lessonMaxPercent"]));
            $kindKarname->subjectMaxPercent = (isset($_POST["subjectMaxPercent"]));
            $kindKarname->partialTaraz = (isset($_POST["partialTaraz"]));
            $kindKarname->generalTaraz = (isset($_POST["generalTaraz"]));
            $kindKarname->lessonCityRank = (isset($_POST["lessonCityRank"]));
            $kindKarname->subjectCityRank = (isset($_POST["subjectCityRank"]));
            $kindKarname->lessonStateRank = (isset($_POST["lessonStateRank"]));
            $kindKarname->subjectStateRank = (isset($_POST["subjectStateRank"]));
            $kindKarname->lessonCountryRank = (isset($_POST["lessonCountryRank"]));
            $kindKarname->subjectCountryRank = (isset($_POST["subjectCountryRank"]));
            $kindKarname->generalCityRank = (isset($_POST["generalCityRank"]));
            $kindKarname->generalStateRank = (isset($_POST["generalStateRank"]));
            $kindKarname->generalCountryRank = (isset($_POST["generalCountryRank"]));
            $kindKarname->coherences = (isset($_POST["coherences"]));
            $kindKarname->lessonBarChart = (isset($_POST["lessonBarChart"]));
            $kindKarname->subjectBarChart = (isset($_POST["subjectBarChart"]));
            $kindKarname->lessonMark = (isset($_POST["lessonMark"]));
            $kindKarname->subjectMark = (isset($_POST["subjectMark"]));
            $kindKarname->save();
        }

        return Redirect::to(route('defineKarname'));
    }
}