<?php

namespace App\Http\Controllers;

use App\models\Lesson;
use App\models\Subject;
use App\models\Grade;
use Exception;
use PHPExcel_IOFactory;

class ContentController extends Controller {

    public function grades() {
        return view('grades', array('grades' => Grade::all()));
    }

    public function addGrade() {

        if(isset($_POST["gradeName"])) {
            try {
                $gradeName = makeValidInput($_POST["gradeName"]);
                $grade = new Grade();
                $grade->name = $gradeName;
                $grade->save();
                echo "ok";
            } catch (Exception $e) {
                echo "nok";
            }
        }
        else
            echo "nok";

    }

    public function deleteGrade() {

        if(isset($_POST["gradeId"])) {
            Grade::destroy(makeValidInput($_POST["gradeId"]));
            echo "ok";
        }

    }

    public function editGrade() {

        if(isset($_POST["gradeName"]) && isset($_POST["gradeId"])) {

            $grade = Grade::whereId(makeValidInput($_POST['gradeId']));
            $grade->name = makeValidInput($_POST["gradeName"]);

            try {
                $grade->save();
                echo "ok";
            }
            catch (Exception $x) {}

        }

    }

    public function getGradesOfField() {
        
        if(isset($_POST["field"])) {
            echo json_encode(Grade::whereField(makeValidInput($_POST["field"]))->get());
        }
        
    }

    public function getGrades() {

        echo json_encode(Grade::all());

    }

    public function lessons($err = "") {
        return view('lessons', array('grades' => Grade::all(), 'err' => $err));
    }

    public function getLessons() {
        if(isset($_POST["gradeId"])) {
            echo json_encode(Lesson::whereGradeId(makeValidInput($_POST["gradeId"]))->get());
        }
    }

    public function addLesson() {

        if(isset($_POST["gradeId"]) && isset($_POST["lessonName"])) {

            $lesson = new Lesson();
            $lesson->gradeId = makeValidInput($_POST["gradeId"]);
            $lesson->name = makeValidInput($_POST["lessonName"]);

            try {
                $lesson->save();
                echo "ok";
            }
            catch (Exception $x) {}
        }

    }

    public function deleteLesson() {

        if(isset($_POST["lessonId"])) {
            Lesson::destroy(makeValidInput($_POST["lessonId"]));
            echo "ok";
        }
    }

    public function addLessons($lessons) {

        $errors = [];

        for ($i = 0; $i < count($lessons); $i++) {

            $lesson = new Lesson();
            $lesson->gradeId = Grade::where('name', '=', $lessons[$i][0])->first()->id;
            $lesson->name = $lessons[$i][1];

            try {
                $lesson->save();
            }
            catch (Exception $e) {
                $errors[count($errors)] = $lessons[$i][1];
            }

        }

        return $errors;

    }

    public function addLessonBatch() {

        if (isset($_FILES["lessons"])) {

            $path = __DIR__ . '/../../../public/tmp/' . $_FILES["lessons"]["name"];

            $err = uploadCheck($path, "lessons", "اکسل دروس", 20000000, "xlsx");

            if (empty($err)) {
                upload($path, "lessons", "اکسل دروس");
                
                $excelReader = PHPExcel_IOFactory::createReaderForFile($path);
                $excelObj = $excelReader->load($path);
                $workSheet = $excelObj->getSheet(0);
                $lessons = array();
                $lastRow = $workSheet->getHighestRow();
                $cols = $workSheet->getHighestColumn();

                if (count($cols) < 'B') {
                    unlink($path);
                    $err = "تعداد ستون های فایل شما معتبر نمی باشد";
                } else {
                    for ($row = 1; $row <= $lastRow; $row++) {
                        $lessons[$row - 1][0] = $workSheet->getCell('A' . $row)->getValue();
                        $lessons[$row - 1][1] = $workSheet->getCell('B' . $row)->getValue();
                    }
                    unlink($path);
                    $errors = $this->addLessons($lessons);
                    if (count($errors) == 0)
                        return Redirect::to('lessons');
                    else {
                        $err = "بجز دروس زیر که در سامانه موجود است بقیه به درستی اضافه شدند" . "<br/>";
                        $size = count($errors);
                        for ($i = 0; $i < $size; $i++)
                            $err .= $errors[$i] . "<br/>";
                    }
                }
            }
        }
        else
            $err = "خطایی در انجام عملیات مورد نظر رخ داده است";

        return $this->lessons($err);
    }

    public function editLesson() {

        if(isset($_POST["lessonName"]) && isset($_POST["lessonId"])) {

            $lesson = Lesson::whereId(makeValidInput($_POST['lessonId']));
            $lesson->name = makeValidInput($_POST["lessonName"]);

            try {
                $lesson->save();
                echo "ok";
            }
            catch (Exception $x) {}

        }

    }

    public function subjects($err = "") {
        return view('subjects', array('grades' => Grade::all(), 'err' => $err));
    }

    public function getSubjects() {

        if(isset($_POST["lessonId"])) {

            echo json_encode(Subject::where('lessonId', '=', makeValidInput($_POST["lessonId"]))->get());

        }

    }

    public function getSubjects2() {

        if(isset($_POST["lessonId"])) {

            $subjects = Subject::where('lessonId', '=', makeValidInput($_POST["lessonId"]))->get();

            foreach ($subjects as $subject) {
                $subject->url = route('questionListSubject', ['sId' => $subject->id]);
            }
            echo json_encode($subjects);

        }

    }

    public function deleteSubject() {

        if(isset($_POST["subjectId"])) {
            Subject::destroy(makeValidInput($_POST["subjectId"]));
            echo "ok";
        }
    }

    public function addSubject() {

        if(isset($_POST["lessonId"]) && isset($_POST["subjectName"]) && isset($_POST["price1"])
            && isset($_POST["price2"]) && isset($_POST["price3"])) {

            $subject = new Subject();
            $subject->name = makeValidInput($_POST["subjectName"]);
            $subject->lessonId = makeValidInput($_POST["lessonId"]);
            $subject->price1 = makeValidInput($_POST["price1"]);
            $subject->price2 = makeValidInput($_POST["price2"]);
            $subject->price3 = makeValidInput($_POST["price3"]);

            try {
                $subject->save();
                echo "ok";
            }
            catch (Exception $x) {}
        }

    }

    public function editSubject() {

        if(isset($_POST["subjectName"]) && isset($_POST["price1"]) && isset($_POST["price2"]) &&
            isset($_POST["price3"]) && isset($_POST["subjectId"])) {

            $subject = Subject::find(makeValidInput($_POST['subjectId']));
            $subject->name = makeValidInput($_POST["subjectName"]);
            $subject->price1 = makeValidInput($_POST["price1"]);
            $subject->price2 = makeValidInput($_POST["price2"]);
            $subject->price3 = makeValidInput($_POST["price3"]);

            try {
                $subject->save();
                echo "ok";
            }
            catch (Exception $x) {}

        }

    }

    public function addSubjects($subjects) {

        $errors = [];

        for ($i = 0; $i < count($subjects); $i++) {

            $condition = ['name' => $subjects[$i][1], 'gradeId' => Grade::where('name', '=', $subjects[$i][0])->first()->id];
            $lesson = Lesson::where($condition)->first();

            if($lesson != null && count($lesson) != 0) {

                $subject = new Subject();
                $subject->lessonId = $lesson->id;
                $subject->name = $subjects[$i][2];
                $subject->price1 = $subjects[$i][3];
                $subject->price2 = $subjects[$i][4];
                $subject->price3 = $subjects[$i][5];

                try {
                    $subject->save();
                }
                catch (Exception $e) {
                    $errors[count($errors)] = $subjects[$i][2];
                }
            }
            else {
                $errors[count($errors)] = $subjects[$i][2];
            }

        }

        return $errors;

    }

    public function addSubjectBatch() {

        if (isset($_FILES["subjects"])) {

            $path = __DIR__ . '/../../../public/tmp/' . $_FILES["subjects"]["name"];

            $err = uploadCheck($path, "subjects", "اکسل مباحث", 20000000, "xlsx");

            if (empty($err)) {
                upload($path, "subjects", "اکسل مباحث");
                $excelReader = PHPExcel_IOFactory::createReaderForFile($path);
                $excelObj = $excelReader->load($path);
                $workSheet = $excelObj->getSheet(0);
                $subjects = array();
                $lastRow = $workSheet->getHighestRow();
                $cols = $workSheet->getHighestColumn();

                if (count($cols) < 'F') {
                    unlink($path);
                    $err = "تعداد ستون های فایل شما معتبر نمی باشد";
                } else {
                    for ($row = 1; $row <= $lastRow; $row++) {
                        $subjects[$row - 1][0] = $workSheet->getCell('A' . $row)->getValue();
                        $subjects[$row - 1][1] = $workSheet->getCell('B' . $row)->getValue();
                        $subjects[$row - 1][2] = $workSheet->getCell('C' . $row)->getValue();
                        $subjects[$row - 1][3] = $workSheet->getCell('D' . $row)->getValue();
                        $subjects[$row - 1][4] = $workSheet->getCell('E' . $row)->getValue();
                        $subjects[$row - 1][5] = $workSheet->getCell('F' . $row)->getValue();
                    }
                    unlink($path);
                    $errors = $this->addSubjects($subjects);
                    if (count($errors) == 0)
                        return Redirect::to('subjects');
                    else {
                        $err = "بجز مباحث زیر که در سامانه موجود است بقیه به درستی اضافه شدند" . "<br/>";
                        $size = count($errors);
                        for ($i = 0; $i < $size; $i++)
                            $err .= $errors[$i] . "<br/>";
                    }
                }
            }
        }
        else
            $err = "خطایی در انجام عملیات مورد نظر رخ داده است";

        return $this->subjects($err);
    }

    public function showSubjects() {
        return view('showSubjects', array('grades' => Grade::all()));
    }
}