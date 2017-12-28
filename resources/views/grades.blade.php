@extends('layouts.form')

@section('head')
    @parent

    <script>
        var grades = '{{route('grades')}}';
        var addGrade = '{{route('addGrade')}}';
        var deleteGradeDir = '{{route('deleteGrade')}}';
        var editGradeDir = '{{route('editGrade')}}';
    </script>

    <script src="{{URL::asset('js/jsNeededForGrade.js')}}"></script>
@stop


@section('caption')
    <div class="title">پایه های تحصیلی
    </div>
@stop

@section('main')
    <center class="myRegister">
        <div class="row data">

            @foreach($grades as $grade)
                <div class="col-xs-12" style="margin-top: 10px">
                    <span>{{$grade->name}}</span>
                    <button onclick="deleteGrade('{{$grade->id}}')" class="btn btn-danger" data-toggle="tooltip" title="حذف پایه ی تحصیلی">
                        <span class="glyphicon glyphicon-remove" style="margin-left: 30%"></span>
                    </button>
                    <button onclick="editGrade('{{$grade->id}}', '{{$grade->name}}')" class="btn btn-primary" data-toggle="tooltip" title="ویرایش پایه ی تحصیلی">
                        <span class="glyphicon glyphicon-edit" style="margin-left: 30%"></span>
                    </button>
                </div>
            @endforeach

            <div class="col-xs-12" style="margin-top: 10px">
                <button class="btn btn-default circleBtn" data-toggle="tooltip" title="افزودن پایه ی جدید" onclick="showElement('newGradeContainer')">
                    <span class="glyphicon glyphicon-plus" style="margin-left: 30%"></span>
                </button>
            </div>
        </div>
    </center>

    <span id="newGradeContainer" class="ui_overlay item hidden" style="position: fixed; left: 40%; right: auto; top: 174px; bottom: auto">
        <div class="header_text">پایه ی جدید</div>
        <div onclick="hideElement()" class="ui_close_x"></div>
            <div class="body_text">
                <input type="text" id="gradeName" maxlength="50" autofocus>
                <div class="submitOptions" style="margin-top: 10px">
                    <button onclick="doAddNewGrade()" class="btn btn-success">تایید</button>
                    <input type="submit" onclick="hideElement()" value="خیر" class="btn btn-default">
                    <p id="msg" class="errorText"></p>
                </div>
            </div>
    </span>

    <span id="editGradeContainer" class="ui_overlay item hidden" style="position: fixed; left: 40%; right: auto; top: 174px; bottom: auto">
        <div class="header_text">ویرایش پایه</div>
        <div onclick="hideElement()" class="ui_close_x"></div>
            <div class="body_text">
                <input type="text" id="gradeNameEdit" maxlength="50" autofocus>
                <div class="submitOptions" style="margin-top: 10px">
                    <button onclick="doEditGrade()" class="btn btn-success">تایید</button>
                    <input type="submit" onclick="hideElement()" value="خیر" class="btn btn-default">
                    <p id="errMsg" class="errorText"></p>
                </div>
            </div>
    </span>

@stop