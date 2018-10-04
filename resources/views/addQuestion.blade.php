@extends('layouts.form')

@section('head')
    @parent
    <link rel="stylesheet" href="{{URL::asset('css/form.css')}}">
    <script>
        var getGradesDir = '{{route('getGrades')}}';
        var getLessonsDir = '{{route('getLessons')}}';
        var getSubjectsDir = '{{route('getSubjects')}}';
        var addQuestionPicDir = '{{route('doAddQuestionPic')}}';
        var addQuestion = '{{route('addQuestion')}}';
        var rootDir = '{{route('home')}}';
    </script>
    <script src="{{URL::asset('js/jsNeededForQuestion.js')}}"></script>
    <style>
        .caret {
            display: inline-block !important;
        }
    </style>
@stop


@section('caption')
    <div class="title">افزودن سوال جدید
    </div>
@stop

@section('main')
    <style type="text/css">
        .col-xs-12 {
            line-height: 32px;
            padding-bottom: 10px;
        }
    </style>
    <center class="myRegister">

        <div class="row data">

            <div class="col-xs-12">
                <div class="col-xs-6">
                    <input id="pic" onchange="setQuestionFileName()" type="file" style="display: none">
                    <label for="pic" style="float: right; width: 30%; margin: 0px;">
                        <div id="questionFileName" class="btn btn-primary" style="width: 100%;">انتخاب فایل</div>
                    </label>
                </div>
                <div class="col-xs-6">
                    <span style="float: left">فایل مورد نظر برای صورت سوال</span>
                </div>
            </div>

            <div class="col-xs-12">
                <div class="col-xs-6">
                    <input id="ansPic" onchange="setAnsFileName()" type="file" style="display: none">
                    <label for="ansPic" style="float: right; width: 30%; margin-bottom: 0px;">
                        <div id="ansFileName" class="btn btn-primary" style="width: 100%;">انتخاب فایل</div>
                    </label>
                </div>
                <div class="col-xs-6">
                    <span style="float: left">فایل مورد نظر برای پاسخ تشریحی</span>
                </div>
            </div>

            <div class="col-xs-12">
                <div class="col-xs-6">
                    <div class="btn-group" style="float: right; width: 30%;">
                        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" style="border-radius: 4px 0px 0px 4px;">
                            <span class="caret"></span>
                        </button>
                        <button id="level" data-val="1" type="button" class="btn btn-primary" style="border-radius: 0px 4px 4px 0px; width: 75%;">ساده</button>
                        <ul class="dropdown-menu" role="menu" style="right: 0 !important; text-align: right !important;">
                            <li><a onclick="changeLevel(1)">ساده</a></li>
                            <li><a onclick="changeLevel(2)">متوسط</a></li>
                            <li><a onclick="changeLevel(3)">دشوار</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-xs-6">
                    <span style="float: left">سطح سختی سوال</span>
                </div>
            </div>

            <div class="col-xs-12">
                <div class="col-xs-6">
                    <input class="btn btn-primary" style="float: right; width:30%;" id="neededTime" type="number" max="100" min="0" value="90" width="30%">
                </div>
                <div class="col-xs-6">
                    <span style="float: left">زمان مورد نیاز برای پاسخ گویی</span>
                </div>
            </div>

            <div class="col-xs-12">
                <div class="col-xs-6">
                    <div class="btn-group" style="float: right; width: 30%;">
                        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" style="border-radius: 4px 0px 0px 4px;">
                            <span class="caret"></span>
                        </button>
                        <button data-val="1" id="kindQ" type="button" class="btn btn-primary" style="border-radius: 0px 4px 4px 0px; width: 75%;">تستی</button>
                        <ul class="dropdown-menu" role="menu" style="right: 0 !important; text-align: right !important;">
                            <li><a onclick="changeKindQ(1)">تستی</a></li>
                            <li><a onclick="changeKindQ(0)">کوتاه پاسخ</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-xs-6">
                    <span style="float: left">نوع سوال</span>
                </div>
            </div>

            <div class="col-xs-12" id="ansDiv">
                <div class="col-xs-6">
                    <div class="btn-group" id="kindQ" onchange="changeKindQ()" style="float: right; width: 30%;">
                        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" style="border-radius: 4px 0px 0px 4px;">
                            <span class="caret"></span>
                        </button>
                        <button id="ans" data-val="1" type="button" class="btn btn-primary" style="border-radius: 0px 4px 4px 0px; width: 75%;">گزینه 1</button>
                        <ul id="ansUL" class="dropdown-menu" role="menu" style="right: 0 !important; text-align: right !important; padding: 3px 20px;"></ul>
                    </div>
                </div>
                <div class="col-xs-6">
                    <span style="float: left !important;">پاسخ</span>
                </div>
            </div>

            <div class="col-xs-12" id="choice">
                <div class="col-xs-6">
                    <input class="btn btn-primary" onchange="changeChoicesCount()" type="number" max="10" min="2" value="4" id="choicesNum" style="float: right !important; width:30%;">
                </div>
                <div class="col-xs-6">
                    <span style="float: left !important;">تعداد گزینه</span>
                </div>
            </div>

            <div class="col-xs-12" id="teloranceDiv" hidden>
                <div class="col-xs-6">
                    <input class="btn btn-primary" style="border-radius:4px; margin-right:0 !important; height:34px; background-color: #337ab7; border-color: #2e6da4; width: 30%; float: right" type="text" max="100" min="0" value="0" id="telorance">
                </div>
                <div class="col-xs-6">
                    <span style="float: left">میزان خطا</span>
                </div>
            </div>

            <div class="col-xs-12" id="shortAnsDiv" hidden>
                <div class="col-xs-6">
                    <input class="btn btn-primary" type="text" style="border-radius:4px; margin-right:0 !important; height:34px; background-color: #337ab7; border-color: #2e6da4; width: 30%; float: right" maxlength="40" id="shortAns">
                </div>
                <div class="col-xs-6">
                    <span style="float: left">پاسخ کوتاه</span>
                </div>
            </div>

            <div class="col-xs-12" >
                <div class="col-xs-6">
                    <input class="btn btn-primary" type="text" style="border-radius:4px; margin-right:0 !important; height:34px; background-color: #337ab7; border-color: #2e6da4; width: 30%; float: right" maxlength="20" id="organizationId">
                </div>
                <div class="col-xs-6">
                    <span style="float: left">کد سازمانی سوال</span>
                </div>
            </div>

            <div class="col-xs-12" style="margin-top: 10px">
                <center>
                    <div class="btn btn-primary" onclick="showAddQuestionPane()">افزودن مبحث به سوال</div>
                </center>
            </div>

            <div class="col-xs-12">
                <center>
                    <h4 style="font-family: IRANSans !important; border-bottom: 3px solid black; width: 400px; padding: 5px">مباحث</h4>
                    <div id="selectedSubjects"></div>
                </center>
            </div>

            <div class="col-xs-12" style="margin-top: 10px">
                <center>
                    <input type="submit" id="submitBtn" value="افزودن سوال" class="btn btn-primary">
                    <input type="submit" id="addBatchBtn" value="افزودن دسته ای سوالات" class="btn btn-primary">
                </center>
            </div>
        </div>
    </center>

    <span id="addNewSubjectPane" class="ui_overlay item hidden" style="position: fixed; max-width: 400px; left: 40%; right: auto; top: 174px; bottom: auto">
        <div class="header_text">مبحث جدید</div>
        <div onclick="hideElement()" class="ui_close_x"></div>
        <div class="body_text row">
            <div class="col-xs-12">
                <label>
                    <span>پایه ی تحصیلی</span>
                    <select id="grades" onchange="getLessons(this.value)"></select>
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
                <button onclick="doAddNewSubject()" class="btn btn-success">تایید</button>
                <input type="submit" onclick="hideElement()" value="خیر" class="btn btn-default">
            </div>
        </div>
    </span>

    <span id="addBatch" class="ui_overlay item hidden" style="position: fixed; left: 40%; right: auto; top: 174px; bottom: auto">
        <div class="header_text">افزودن دسته ای مباحث</div>
        <div onclick="hideElement()" class="ui_close_x"></div>
        <div class="body_text">
            <form method="post" action="{{route('addQuestionBatch')}}" enctype="multipart/form-data">
                {{csrf_field()}}
                <input type="file" name="questions">
                <div class="submitOptions" style="margin-top: 10px">
                    <button name="submitBtn" class="btn btn-success">تایید</button>
                </div>
                <div class="errorText">{{$err}}</div>
            </form>
        </div>
    </span>

    <script>

        @if(!empty($err))
            showAddBatchPane();
        @endif
    </script>
@stop