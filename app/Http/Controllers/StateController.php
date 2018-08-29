<?php

namespace App\Http\Controllers;

use App\models\State;
use App\models\City;
use Exception;
use Illuminate\Support\Facades\Redirect;
use PHPExcel_IOFactory;

class StateController extends Controller {

    public function states($err = "") {

        return view('states', array('err' => $err, 'states' => State::all()));
    }

    public function addState() {

        if(isset($_POST["stateName"])) {
            try {
                $stateName = makeValidInput($_POST["stateName"]);
                $state = new State();
                $state->name = $stateName;
                $state->save();
                echo "ok";
            } catch (Exception $e) {
                echo "nok";
            }
        }
        else
            echo "nok";
    }

    public function deleteState() {

        if(isset($_POST["stateId"])) {
            State::destroy(makeValidInput($_POST["stateId"]));
        }

        return Redirect::to(route('states'));
    }

    public function addStates($states) {

        $errors = [];

        for($i = 0; $i < count($states); $i++) {
            try{
                $state = new State();
                $state->name = $states[$i];
                $state->save();
            }
            catch (Exception $e) {
                $errors[count($errors)] = $states[$i];
            }
        }

        return $errors;
    }

    public function addStateBatch() {

        if (isset($_FILES["states"])) {

            $path = __DIR__ . '/../../../public/tmp/' . $_FILES["states"]["name"];

            $err = uploadCheck($path, "states", "اکسل استان ها", 20000000, "xlsx");

            if (empty($err)) {
                upload($path, "states", "اکسل استان ها");
                $excelReader = PHPExcel_IOFactory::createReaderForFile($path);
                $excelObj = $excelReader->load($path);
                $workSheet = $excelObj->getSheet(0);
                $states = array();
                $lastRow = $workSheet->getHighestRow();
                $cols = $workSheet->getHighestColumn();
                if ($cols < 1) {
                    unlink($path);
                    $err = "تعداد ستون های فایل شما معتبر نمی باشد";
                } else {
                    for ($row = 1; $row <= $lastRow; $row++) {
                        $states[$row - 1] = $workSheet->getCell('A' . $row)->getValue();
                    }
                    unlink($path);
                    $errors = $this->addStates($states);
                    if (count($errors) == 0)
                        return Redirect::to('states');
                    else {
                        $err = "بجز استان های زیر که در سامانه موجود است بقیه به درستی اضافه شدند" . "<br/>";
                        $size = count($errors);
                        for ($i = 0; $i < $size; $i++)
                            $err .= $errors[$i] . "<br/>";
                    }
                }
            }
        }
        else
            $err = "خطایی در انجام عملیات مورد نظر رخ داده است";

        return $this->states($err);
    }

    public function cities($err = "") {

        $cities = City::paginate(20);

        foreach ($cities as $city) {
            $city->stateId = State::whereId($city->stateId)->name;
        }

        return view('cities', array('err' => $err, 'cities' => $cities));
    }

    public function addCity() {

        if(isset($_POST["cityName"]) && isset($_POST["stateId"])) {

            try {
                $cityName = makeValidInput($_POST["cityName"]);
                $stateId = makeValidInput($_POST["stateId"]);
                $city = new City();
                $city->name = $cityName;
                $city->stateId = $stateId;
                $city->save();
                echo "ok";
            } catch (Exception $e) {
                echo "nok";
            }
        }
        else
            echo "nok";
    }

    public function deleteCity() {

        if(isset($_POST["cityId"])) {
            City::destroy(makeValidInput($_POST["cityId"]));
        }

        return Redirect::to(route('cities'));
    }

    public function getStates() {
        echo json_encode(State::all());

    }

    public function addCityBatch() {

        if (isset($_FILES["cities"])) {

            $path = __DIR__ . '/../../../public/tmp/' . $_FILES["cities"]["name"];

            $err = uploadCheck($path, "cities", "اکسل شهر ها", 20000000, "xlsx");

            if (empty($err)) {
                upload($path, "cities", "اکسل شهر ها");
                $excelReader = PHPExcel_IOFactory::createReaderForFile($path);
                $excelObj = $excelReader->load($path);
                $workSheet = $excelObj->getSheet(0);
                $cities = array();
                $lastRow = $workSheet->getHighestRow();
                $cols = $workSheet->getHighestColumn();
                if ($cols < 'B') {
                    unlink($path);
                    $err = "تعداد ستون های فایل شما معتبر نمی باشد";
                } else {
                    for ($row = 1; $row <= $lastRow; $row++) {
                        $cities[$row - 1]["stateName"] = $workSheet->getCell('A' . $row)->getValue();
                        $cities[$row - 1]["name"] = $workSheet->getCell('B' . $row)->getValue();
                    }
                    unlink($path);
                    $errors = $this->addCities($cities);
                    if (count($errors) == 0)
                        return Redirect::to('cities');
                    else {
                        $err = "بجز شهر های زیر که در سامانه موجود است بقیه به درستی اضافه شدند" . "<br/>";
                        $size = count($errors);
                        for ($i = 0; $i < $size; $i++)
                            $err .= $errors[$i] . "<br/>";
                    }
                }
            }
        }
        else
            $err = "خطایی در انجام عملیات مورد نظر رخ داده است";

        return $this->cities($err);
    }

    public function addCities($cities) {

        $errors = [];

        for($i = 0; $i < count($cities); $i++) {
            $state = State::whereStateName($cities[$i]["stateName"])->first();
            if($state != null) {

                try {
                    $city = new City();
                    $city->name = $cities[$i]["name"];
                    $city->stateId = $state->id;
                    $city->save();
                } catch (Exception $e) {
                    $errors[count($errors)] = $cities[$i]["name"];
                }
            }
            else
                $errors[count($errors)] = $cities[$i]["name"];
        }

        return $errors;
    }

    public function getCities() {

        if(isset($_POST["stateId"])) {

            $stateId = makeValidInput($_POST["stateId"]);

            if($stateId != -1)
                echo json_encode(City::whereStateId($stateId)->get());
        }

    }
}