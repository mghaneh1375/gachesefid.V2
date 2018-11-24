@extends('layouts.form')

@section('head')
    @parent
    <link rel="stylesheet" href="{{URL::asset('css/form.css')}}">
    <script>
        var getLessonsDir = '{{route('getLessonsController')}}';
        var getQuestionsDir = '{{route('getTotalQuestions')}}';
        var getQuestionSubjects = '{{route('getQuestionSubjects')}}';
        var getGradesDir = '{{route('getGrades')}}';
        var getLessonsDir2 = '{{route('getLessons')}}';
        var getSubjectsDir = '{{route('getSubjects')}}';
        var rejectQuestionDir = '{{route('rejectQuestion')}}';
        var acceptDir = '{{route('home')}}' + "/editDetailQuestion/";
        var currUrl = '{{Request::url()}}';
        var selectedLesson = -1;
        var selectedQId = -1;
        var changeQuestionPicDir = '{{route('home') . '/doChangeQuestionPic/'}}';
        var changeAnsPicDir = '{{route('home') . '/doChangeAnsPic/'}}';
        var getQuestionByOrganizationId  = '{{route('getQuestionByOrganizationId')}}';
    </script>
    <script src="{{URL::asset('js/jsNeededForEditQuestions.js')}}"></script>

    <style>
        label {
            float: right;
            padding-left: 10px;
        }
    </style>
@stop

@section('caption')
    <div class="title">سوالات
    </div>
@stop

@section('main')
    <center class="myRegister">
        <div class="row data">

            <div class="col-xs-6">
                <label for="lessonId">درس مورد نظر</label>
                <select style="float: right" class="mySelect" id="lessonId" onchange="getQuestions()"></select>
            </div>

            <div class="col-xs-6">
                <label for="gradeId">پایه ی تحصیلی</label>
                <select class="mySelect" style="float: right" id="gradeId" onchange="getLessons()">
                    @foreach($grades as $grade)
                        @if($grade->id == $gradeId)
                            <option selected value="{{$grade->id}}">{{$grade->name}}</option>
                        @else
                            <option value="{{$grade->id}}">{{$grade->name}}</option>
                        @endif
                    @endforeach
                </select>
            </div>

            <div class="col-xs-12" style="margin-top: 20px">
                <div class="col-xs-6">
                    <div class="col-xs-12 hidden" id="levelDiv">
                        <label for="level">سطح سختی سوال</label>
                        <select id="level" class="mySelect" style="float: right">
                            <option value="1">ساده</option>
                            <option value="2">متوسط</option>
                            <option value="3">دشوار</option>
                        </select>
                    </div>
                </div>

                <div class="col-xs-6">
                    <div class="col-xs-12" id="ansDiv">
                        <label for="ans" style="float: right">پاسخ</label>
                        <select class="mySelect" style="float: right" id="ans">
                        </select>
                    </div>
                    <div class="col-xs-12 hidden" id="shortAnsDiv">
                        <label for="shortAns">پاسخ کوتاه</label>
                        <input style="margin-top: -7px; float: right" type="text" maxlength="40" id="shortAns">
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xs-12" style="margin-top: 20px">

            <div class="col-xs-6">
                <div class="col-xs-12" id="kindQDiv">
                    <label for="kindQ">نوع سوال</label>
                    <select class="mySelect" style="float: right" id="kindQ" onchange="changeKindQ()">
                        <option value="1">تستی</option>
                        <option value="0">کوتاه پاسخ</option>
                    </select>
                </div>
            </div>

            <div class="col-xs-6">
                <div class="col-xs-12 hidden" id="neededTimeDiv">
                    <label for="neededTime">زمان مورد نیاز برای پاسخ گویی</label>
                    <input style="float: right; margin-top: -7px" id="neededTime" type="number" max="100" min="0" value="1">
                </div>
            </div>

        </div>

        <div class="col-xs-12" style="margin-top: 20px">
            <div class="col-xs-12">

                <div class="col-xs-6">
                    <label for="jumpVal">پرش به سوال </label>
                    <input style="margin-top: -7px; float: right" placeholder="کد سازمانی سوال" type="text" id="jumpVal">
                    <span class="btn btn-danger" onclick="jumpToSpecificQuestion()">بپر</span>
                </div>

                <div class="col-xs-6" id="teloranceDiv">
                    <label for="telorance">میزان خطا</label>
                    <input style="margin-top: -7px; float: right" type="number" max="100" min="0" value="0" id="telorance">
                </div>
                <div class="col-xs-6" id="choice">
                    <label for="choicesNum">تعداد گزینه</label>
                    <input onchange="changeChoicesCount()" style="margin-top: -7px; float: right" type="number" max="10" min="2" value="4" id="choicesNum">
                </div>
            </div>
        </div>

        <div class="col-xs-12" style="min-height: 50vh; border: 3px solid black; margin-top: 20px">
            <div class="col-xs-6" style="min-height: 50vh" id="ansPane"></div>
            <div class="col-xs-6" style="min-height: 50vh" id="questionPane"></div>
            <p id="msg" style="position: absolute; top: 25vh"></p>
            <div id="prevQ" onclick="prevQ()" class="fa fa-chevron-left" style="position: absolute; left: 0; font-size: 30px; cursor: pointer; z-index: 1000; top: 20vh"></div>
            <div id="nextQ" onclick="nextQ()" class="fa fa-chevron-right" style="position: absolute; right: 0; font-size: 30px; cursor: pointer; z-index: 1000; top: 20vh"></div>
        </div>

        <div class="col-xs-12" style="margin-top: 10px">
            <center>
                <div class="primaryBtn" onclick="showAddQuestionPane()">افزودن مبحث به سوال</div>
                <div class="btn btn-default" onclick="showChangeQuestionPane()">تغییر تصویر سوال</div>
                <div class="btn btn-info" onclick="showChangeAnsPane()">تغییر تصویر پاسخ سوال</div>
            </center>
        </div>

        <div class="col-xs-12">
            <center>
                <h4 style="font-family: IRANSans !important; border-bottom: 3px solid black; width: 400px; padding: 5px">مباحث</h4>
                <div id="selectedLessons"></div>
            </center>
        </div>

        <div class="col-xs-12" style="margin-top: 10px">
            <center>
                <input type="submit" onclick="submitQuestion()" value="تایید" class="btn btn-primary">
                <input type="submit" onclick="rejectQuestion()" value="رد سوال" class="btn btn-danger">
            </center>
        </div>
    </center>

    <span id="addNewSubjectPane" class="ui_overlay item hidden" style="position: fixed; max-width: 400px; left: 40%; right: auto; top: 174px; bottom: auto">
        <div class="header_text">مبحث جدید</div>
        <div onclick="hideElement()" class="ui_close_x"></div>
        <div class="body_text row">
            <div class="col-xs-12">
                <label>
                    <span>پایه ی تحصیلی</span>
                    <select id="grades" onchange="getLessons2(this.value)"></select>
                </label>
            </div>
            <div class="col-xs-12">
                <label>
                    <span>درس</span>
                    <select id="lessons" onchange="getSubjects(this.value)"></select>
                </label>
            </div>
            <div class="col-xs-12">
                <label>
                    <span>مبحث</span>
                    <select id="subjects"></select>
                </label>
            </div>

            <div class="submitOptions" style="margin-top: 10px">
                <button onclick="doAddNewLesson($('#subjects').val(), $('#subjects :selected').text())" class="btn btn-success">تایید</button>
                <input type="submit" onclick="hideElement()" value="خیر" class="btn btn-default">
            </div>
        </div>
    </span>

    <span id="changeQuestionPane" class="ui_overlay item hidden" style="position: fixed; max-width: 400px; left: 40%; right: auto; top: 174px; bottom: auto">
        <div class="header_text">تغییر تصویر سوال</div>
        <div onclick="hideElement()" class="ui_close_x"></div>
        <div class="body_text row">
            <div class="col-xs-12">
                <input id="pic" onchange="setQuestionFileName()" type="file" style="display: none">
                <label for="pic">
                    <div id="fileName" class="btn btn-primary" style="width: 100%;">انتخاب فایل</div>
                </label>
                <input type="submit" class="btn btn-danger" value="تایید" id="submitQBtn">
            </div>
            <div class="col-xs-12">
                <p class="errorText" id="qErrMsg"></p>
            </div>
        </div>
    </span>

    <span id="changeAnsPane" class="ui_overlay item hidden" style="position: fixed; max-width: 400px; left: 40%; right: auto; top: 174px; bottom: auto">
        <div class="header_text">تغییر تصویر پاسخ سوال</div>
        <div onclick="hideElement()" class="ui_close_x"></div>
        <div class="body_text row">
            <div class="col-xs-12">
                <input id="ansPic" onchange="setAnsFileName()" type="file" style="display: none">
                <label for="ansPic">
                    <div id="ansFileName" class="btn btn-primary" style="width: 100%;">انتخاب فایل</div>
                </label>
                <input type="submit" class="btn btn-danger" value="تایید" id="submitABtn">
            </div>

            <div class="col-xs-12">
                <p class="errorText" id="aErrMsg"></p>
            </div>
        </div>
    </span>

    <span id="rejectPane" class="ui_overlay item hidden" style="position: fixed; left: 30%; right: 30%; width: auto; top: 174px; bottom: auto">
        <div class="header_text">رد سوال</div>
        <div onclick="hideElement()" class="ui_close_x"></div>
        <div class="body_text row">
            <div class="col-xs-12">
                <span>دلیل رد سوال را بنویسید</span>
            </div>
            <div class="col-xs-12">
                <textarea id="description" style="width: 80%; min-height: 350px; max-height: 350px; overflow: auto" maxlength="1000" placeholder="حداکثر 1000 کاراکتر"></textarea>
            </div>

            <div class="submitOptions" style="margin-top: 10px">
                <button onclick="doReject()" class="btn btn-success">تایید</button>
                <input type="submit" onclick="hideElement()" value="خیر" class="btn btn-default">
            </div>
        </div>
    </span>

    <script>

        @if(!empty($err))
            showAddBatchPane();
        @endif


        @if(!empty($qId) && $lId != -1)
            selectedLesson = '{{$lId}}';
            selectedQId = '{{$qId}}';
        @endif
    </script>
@stop