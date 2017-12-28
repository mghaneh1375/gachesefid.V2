@extends('layouts.form')

@section('head')
    @parent
    <link rel="stylesheet" href="{{URL::asset('css/form.css')}}">
    <script>
        var getGradesDir = '{{route('getGrades')}}';
        var getLessonsDir = '{{route('getLessons')}}';
        var doAssignToController = '{{route('doAssignToController')}}';
        var getControllerLevelsDir = '{{route('getControllerLevelsDir')}}';
    </script>
    <script src="{{URL::asset('js/jsNeededForQuestion.js')}}"></script>
    <script>
        $(document).ready(function () {
            getControllerLevels();
        });
    </script>
@stop


@section('caption')
    <div class="title">مدیریت سطح دسترسی ناظران
    </div>
@stop

@section('main')
    <center class="myRegister">
        <div class="row data">

            <div class="col-xs-12">
                <div class="col-xs-7">
                    <select id="controller" onchange="getControllerLevels()" style="float: right">
                        @foreach($controllers as $controller)
                            <option value="{{$controller->id}}">{{$controller->username}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xs-5">
                    <span style="float: left">ناظر مورد نظر</span>
                </div>
            </div>

            <div class="col-xs-12" style="margin-top: 10px">
                <center>
                    <div class="primaryBtn" onclick="showAddQuestionPane()">افزودن درس به ناظر</div>
                </center>
            </div>

            <div class="col-xs-12" style="margin-top: 10px">
                <center>
                    <input type="submit" onclick="assignToController()" value="تایید" class="btn btn-primary">
                    <p class="errorText" id="errMsg"></p>
                </center>
            </div>

            <div class="col-xs-12">
                <center>
                    <h4 style="font-family: IRANSans !important; border-bottom: 3px solid black; width: 400px; padding: 5px">دروس</h4>
                    <div id="selectedLessons"></div>
                </center>
            </div>
        </div>
    </center>

    <span id="addNewSubjectPane" class="ui_overlay item hidden" style="position: fixed; max-width: 400px; left: 40%; right: auto; top: 174px; bottom: auto">
        <div class="header_text">درس جدید</div>
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
                    <select id="lessons"></select>
                </label>
            </div>

            <div class="submitOptions" style="margin-top: 10px">
                <button onclick="doAddNewLesson($('#lessons').val(), $('#lessons :selected').text())" class="btn btn-success">تایید</button>
                <input type="submit" onclick="hideElement()" value="خیر" class="btn btn-default">
            </div>
        </div>
    </span>
@stop