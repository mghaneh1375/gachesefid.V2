<?php

namespace App\Http\Controllers;

use App\models\SlideBar;
use Illuminate\Support\Facades\Redirect;

class SlideBarController extends Controller {

    public function slides($err = "", $mode = "") {
        return view('slides', array('slides' => SlideBar::all(), 'err' => $err, 'mode' => $mode));
    }

    public function opOnSlides() {

        if(isset($_POST["cancel"]))
            return Redirect::to(route('slideBarManagement'));

        if(isset($_POST["deleteSlide"])) {

            $picId = makeValidInput($_POST["deleteSlide"]);
            $targetFile = __DIR__ . "/../../../public/images/slideBar/" . SlideBar::find($picId)->pic;

            if(file_exists($targetFile))
                unlink($targetFile);

            SlideBar::destroy($picId);
        }

        elseif(isset($_POST["submitPhoto"]) && isset($_FILES["newPic"])) {

            $file = $_FILES["newPic"];

            if(SlideBar::wherePic($file["name"])->count() == 0) {
                
                $targetFile =  __DIR__ . "/../../../public/images/slideBar/" . $file["name"];

                $err = "";

                if (!file_exists($targetFile)) {
                    $err = uploadCheck($targetFile, "newPic", "اضافه کردن اسلاید جدید", 5000000, -1);
                    if (empty($err)) {
                        $err = upload($targetFile, "newPic", "اضافه کردن اسلاید جدید");
                        if(empty($err)) {
                            $slide = new SlideBar();
                            $slide->pic = $file["name"];
                            $slide->link = makeValidInput($_POST["link"]);
                            $slide->save();
                        }
                    }
                }

                if(!empty($err))
                    return $this->slides($err);
            }
        }

        elseif(isset($_POST["editSlide"])) {
//
//            Session::put('slideId', makeValidInput($_POST["editSlide"]));
            return $this->slides('', 'edit');
        }

        elseif(isset($_POST["doEditPhoto"]) && isset($_FILES["newPic"])) {

//            $slideId = makeValidInput(Session::get('slideId', -1));
            $slideId = 1;

            if($slideId == -1)
                return Redirect::to(route('slideBarManagement'));

            $file = $_FILES["newPic"];

            $slide = SlideBar::find($slideId);

            if($slide->pic != $file["name"] && !empty($file["name"])) {

                $targetFile = __DIR__ . "/../../../public/images/slideBar/" . $file["name"];

                if (!file_exists($targetFile)) {

                    $err = uploadCheck($targetFile, "newPic", "تغییر تصویر اسلاید بار", 5000000, -1);
                    if (empty($err)) {
                        $err = upload($targetFile, "newPic", "تغییر تصویر اسلاید بار");
                        if(empty($err)) {
                            $slide->pic = $file["name"];    
                            $slide->link = makeValidInput($_POST["link"]);
                            $slide->save();

                            unlink(__DIR__ . "/../../../public/images/slideBar/" . $slide->pic);
                        }
                    }
                }
                if(!empty($err))
                    return $this->slides($err);
            }
            else {
                $slide->link = makeValidInput($_POST["link"]);
                $slide->save();
            }
        }

        return Redirect::to(route('slideBarManagement'));
        
    }

}