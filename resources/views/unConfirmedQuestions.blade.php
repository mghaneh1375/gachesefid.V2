@extends('layouts.form')

@section('head')
    @parent
    <link rel="stylesheet" href="{{URL::asset('css/form.css')}}">
    <script>
        var getControllerLevelsDir = '{{route('getControllerLevelsDir')}}';
        var getLessonsDir = '{{route('getLessonsController')}}';
        var getQuestionsDir = '{{route('getControllerQuestions')}}';
        var getQuestionSubjects = '{{route('getQuestionSubjects')}}';
        var getGradesDir = '{{route('getGrades')}}';
        var getLessonsDir2 = '{{route('getLessons')}}';
        var getSubjectsDir = '{{route('getSubjects')}}';
        var rejectQuestionDir = '{{route('rejectQuestion')}}';
        var acceptDir = '{{route('home')}}' + "/editDetailQuestion/";
    </script>
    <script src="{{URL::asset('js/jsNeededForControllers.js')}}"></script>
@stop


@section('caption')
    <div class="title">سوالات تایید نشده
    </div>
@stop

@section('main')
    <center class="myRegister">
        <div class="row data">


            <div class="col-xs-3">
                <select  style="float: right" class="mySelect" id="lessonId" onchange="getQuestions()">
                </select>
            </div>

            <div class="col-xs-3">
                <span style="float: left">درس مورد نظر</span>
            </div>

            <div class="col-xs-3">
                <select class="mySelect" style="float: right" id="gradeId" onchange="getLessons()">
                    @foreach($grades as $grade)
                        <option value="{{$grade->id}}">{{$grade->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-xs-3">
                <span style="float: left">پایه ی تحصیلی</span>
            </div>

            <div class="col-xs-12" style="margin-top: 20px">
                <div class="col-xs-4">
                    <div class="col-xs-12 hidden" id="levelDiv">
                        <div class="col-xs-6">
                            <select id="level" class="mySelect" style="float: right">
                                <option value="1">ساده</option>
                                <option value="2">متوسط</option>
                                <option value="3">دشوار</option>
                            </select>
                        </div>
                        <div class="col-xs-6">
                            <span style="float: left">سطح سختی سوال</span>
                        </div>
                    </div>
                </div>
                <div class="col-xs-4">
                    <div class="col-xs-12" id="ansDiv">
                        <div class="col-xs-6">
                            <select class="mySelect" style="float: right" id="ans">
                            </select>
                        </div>
                        <div class="col-xs-6">
                            <span style="float: left">پاسخ</span>
                        </div>
                    </div>
                    <div class="col-xs-12 hidden" id="shortAnsDiv">
                        <div class="col-xs-6">
                            <input style="margin-top: -7px; float: right" type="text" maxlength="40" id="shortAns">
                        </div>
                        <div class="col-xs-6">
                            <span style="float: left">پاسخ کوتاه</span>
                        </div>
                    </div>
                </div>
                <div class="col-xs-4">
                    <div class="col-xs-12" id="kindQDiv">
                        <div class="col-xs-6">
                            <select class="mySelect" style="float: right" id="kindQ" onchange="changeKindQ()">
                                <option value="1">تستی</option>
                                <option value="0">کوتاه پاسخ</option>
                            </select>
                        </div>
                        <div class="col-xs-6">
                            <span style="float: left">نوع سوال</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xs-12" style="margin-top: 20px">


            <div class="col-xs-8">
                <div class="col-xs-12 hidden" id="neededTimeDiv">
                    <div class="col-xs-6">
                        <input style="float: right; margin-top: -7px" id="neededTime" type="number" max="100" min="0" value="1">
                    </div>
                    <div class="col-xs-6">
                        <span style="float: left">زمان مورد نیاز برای پاسخ گویی</span>
                    </div>
                </div>
            </div>

            <div class="col-xs-4">
                <div class="col-xs-12" id="teloranceDiv">
                    <div class="col-xs-6">
                        <input style="margin-top: -7px; float: right" type="number" max="100" min="0" value="0" id="telorance">
                    </div>
                    <div class="col-xs-6">
                        <span style="float: left">میزان خطا</span>
                    </div>
                </div>
                <div class="col-xs-12" id="choice">
                    <div class="col-xs-6">
                        <input onchange="changeChoicesCount()" style="margin-top: -7px; float: right" type="number" max="10" min="2" value="4" id="choicesNum">
                    </div>
                    <div class="col-xs-6">
                        <span style="float: left">تعداد گزینه</span>
                    </div>
                </div>
            </div>

        </div>

        <div class="col-xs-12" style="min-height: 50vh; border: 3px solid black; margin-top: 20px">
            <div class="col-xs-6" style="min-height: 50vh" id="ansPane"></div>
            <div class="col-xs-6" style="min-height: 50vh" id="questionPane"></div>
            <p id="msg" style="position: absolute; top: 25vh"></p>
            <button id="prevQ" onclick="prevQ()" style="position: absolute; left: 0; top: 25vh">قبلی</button>
            <button id="nextQ" onclick="nextQ()" style="position: absolute; right: 0; top: 25vh">بعدی</button>
        </div>

        <div class="col-xs-12" style="margin-top: 10px">
            <center>
                <div class="primaryBtn" onclick="showAddQuestionPane()">افزودن مبحث به سوال</div>
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
    </script>
@stop