<?php

namespace App\Http\Controllers;

use Exception;
use App\models\Tag;

class TagController extends Controller {

    public function tags() {
        return view('tags', array('tags' => Tag::all()));
    }

    public function addTag() {

        if(isset($_POST["tagName"])) {

            $tag = new Tag();
            $tag->name = makeValidInput($_POST["tagName"]);
            try{
                $tag->save();
                echo "ok";
                return;
            }
            catch (Exception $x) {}
        }

        echo "تگ مورد نظر در سامانه موجود است";
    }

    public function editTag() {

        if(isset($_POST["tagId"]) && isset($_POST["newName"])) {

            $tag = Tag::find(makeValidInput($_POST["tagId"]));
            if($tag != null) {
                $tag->name = makeValidInput($_POST["newName"]);
                try {
                    $tag->save();
                    echo "ok";
                    return;
                }
                catch (Exception $x) {}
            }
        }
        echo "تگ جدید در سامانه موجود است";
    }

    function deleteTag() {

        if(isset($_POST["tagId"])) {
            try {
                Tag::destroy(makeValidInput($_POST["tagId"]));
                echo "ok";
                return;
            }
            catch (Exception $x) {}
        }
        echo "شما نمی توانید تگ مربوطه را حذف نمایید";
    }
}