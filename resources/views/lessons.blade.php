@extends('layouts.form')

@section('head')
    @parent

    <script>
        var lessons = '{{route('lessons')}}';
        var addLesson = '{{route('addLesson')}}';
        var getLessonsDir = '{{route('getLessons')}}';
        var addNewLessonDir = '{{route('addLesson')}}';
        var deleteLessonDir = '{{route('deleteLesson')}}';
        var editLessonDir = '{{route('editLesson')}}';

        $(document).ready(function () {
            getLessons($("#gradeId").val());
        });

    </script>

    <script src="{{URL::asset('js/jsNeededForLesson.js')}}"></script>
    <link rel="stylesheet" href="{{URL::asset('css/form.css')}}">
@stop


@section('caption')
    <div class="title">دروس
    </div>
@stop

@section('main')
    <center class="myRegister">
        <div class="row data">

            <div class="col-xs-12">
                <div class="col-xs-7">
                    <select id="gradeId" onchange="getLessons(this.value)">
                        @foreach($grades as $grade)
                            <option value="{{$grade->id}}">{{$grade->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xs-5">
                    <span>پایه ی تحصیلی</span>
                </div>
            </div>

            <div id="lessons">

            </div>

            <div class="col-xs-12" style="margin-top: 10px">
                <button class="btn btn-primary circleBtn" data-toggle="tooltip" title="افزودن درس جدید" onclick="showElement('newLessonContainer')">
                    <span class="glyphicon glyphicon-plus" style="margin-left: 30%"></span>
                </button>
                <button class="btn btn-primary" data-toggle="tooltip" title="افزودن دسته ای دروس" onclick="showElement('addBatch')">
                    اضافه کردن دسته ای دروس
                </button>
            </div>
        </div>
    </center>

    <span id="newLessonContainer" class="ui_overlay item hidden" style="position: fixed; left: 40%; right: auto; top: 174px; bottom: auto">
        <div class="header_text">درس جدید</div>
        <div onclick="hideElement()" class="ui_close_x"></div>
        <div class="body_text">
            <input type="text" id="lessonName" maxlength="40" autofocus>
            <div class="submitOptions" style="margin-top: 10px">
                <button onclick="doAddNewLesson()" class="btn btn-success">تایید</button>
                <input type="submit" onclick="hideElement()" value="خیر" class="btn btn-default">
                <p id="errMsg" class="errorText"></p>
            </div>
        </div>
    </span>

    <span id="editLessonContainer" class="ui_overlay item hidden" style="position: fixed; left: 40%; right: auto; top: 174px; bottom: auto">
        <div class="header_text">ویرایش درس</div>
        <div onclick="hideElement()" class="ui_close_x"></div>
        <div class="body_text">
            <input type="text" id="lessonNameEdit" maxlength="40" autofocus>
            <div class="submitOptions" style="margin-top: 10px">
                <button onclick="doEditLesson()" class="btn btn-success">تایید</button>
                <input type="submit" onclick="hideElement()" value="خیر" class="btn btn-default">
                <p id="errMsgEdit" class="errorText"></p>
            </div>
        </div>
    </span>

    <span id="addBatch" class="ui_overlay item hidden" style="position: fixed; left: 40%; right: auto; top: 174px; bottom: auto">
        <div class="header_text">افزودن دسته ای دروس</div>
        <div onclick="hideElement()" class="ui_close_x"></div>
        <div class="body_text">
            <form method="post" action="{{route('addLessonBatch')}}" enctype="multipart/form-data">
                <input type="file" name="lessons">
                <div class="submitOptions" style="margin-top: 10px">
                    <button name="submitBtn" class="btn btn-success">تایید</button>
                    <p class="errorText">
                        {{$err}}
                    </p>
                </div>
            </form>
        </div>
    </span>

    <script>

        @if(!empty($err))
            showElement('addBatch');
        @endif
    </script>
@stop