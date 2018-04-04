@extends('layouts.form')

@section('head')
    @parent

    <script>
        var subjects = '{{route('subjects')}}';
        var addSubject = '{{route('addSubject')}}';
        var getLessonsDir = '{{route('getLessons')}}';
        var getSubjectsDir = '{{route('getSubjects')}}';
        var addNewSubjectDir = '{{route('addSubject')}}';
        var deleteSubjectDir = '{{route('deleteSubject')}}';
        var editSubjectDir = '{{route('editSubject')}}';

        $(document).ready(function () {
            getLessons($("#gradeId").val());
        });

    </script>

    <script src="{{URL::asset('js/jsNeededForSubject.js')}}"></script>
    <link rel="stylesheet" href="{{URL::asset('css/form.css')}}">
@stop


@section('caption')
    <div class="title">مباحث
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

            <div class="col-xs-12">
                <div class="col-xs-7">
                    <select id="lessons" onchange="getSubjects(this.value)">
                    </select>
                </div>
                <div class="col-xs-5">
                    <span>دروس</span>
                </div>
            </div>

            <div id="subjects">

            </div>

            <div class="col-xs-12" style="margin-top: 10px">
                <button class="btn btn-primary circleBtn" data-toggle="tooltip" title="افزودن مبحث جدید" onclick="showElement('newSubjectContainer')">
                    <span class="glyphicon glyphicon-plus" style="margin-left: 30%"></span>
                </button>
                <button class="btn btn-primary" data-toggle="tooltip" title="افزودن دسته ای مباحث" onclick="showElement('addBatch')">
                    اضافه کردن دسته ای مباحث
                </button>
            </div>
        </div>
    </center>

    <span id="newSubjectContainer" class="ui_overlay item hidden" style="position: fixed; max-width: 400px; left: 40%; right: auto; top: 174px; bottom: auto">
        <div class="header_text">مبحث جدید</div>
        <div onclick="hideElement()" class="ui_close_x"></div>
        <div class="body_text row">
            <div class="col-xs-12">
                <label>
                    <span>نام مبحث</span>
                    <input type="text" id="subjectName" maxlength="40" autofocus>
                </label>
            </div>
            <div class="col-xs-12">
                <label>
                    <span>قیمت سوالات آسان</span>
                    <input type="number" id="price1" min="0">
                </label>
            </div>
            <div class="col-xs-12">
                <label>
                    <span>قیمت سوالات متوسط</span>
                    <input type="number" id="price2" min="0">
                </label>
            </div>
            <div class="col-xs-12">
                <label>
                    <span>قیمت سوالات دشوار</span>
                    <input type="number" id="price3" min="0">
                </label>
            </div>

            <div class="submitOptions" style="margin-top: 10px">
                <button onclick="doAddNewSubject()" class="btn btn-success">تایید</button>
                <input type="submit" onclick="hideElement()" value="خیر" class="btn btn-default">
                <p id="errMsg" class="errorText"></p>
            </div>
        </div>
    </span>

    <span id="editSubjectContainer" class="ui_overlay item hidden" style="position: fixed; max-width: 400px; left: 40%; right: auto; top: 174px; bottom: auto">
        <div class="header_text">ویرایش درس</div>
        <div onclick="hideElement()" class="ui_close_x"></div>
        <div class="body_text row">
            <div class="col-xs-12">
                <label>
                    <span>نام مبحث</span>
                    <input type="text" id="subjectNameEdit" maxlength="40" autofocus>
                </label>
            </div>
            <div class="col-xs-12">
                <label>
                    <span>قیمت سوالات آسان</span>
                    <input type="number" id="subjectPrice1Edit" min="0">
                </label>
            </div>
            <div class="col-xs-12">
                <label>
                    <span>قیمت سوالات متوسط</span>
                    <input type="number" id="subjectPrice2Edit" min="0">
                </label>
            </div>
            <div class="col-xs-12">
                <label>
                    <span>قیمت سوالات دشوار</span>
                    <input type="number" id="subjectPrice3Edit" min="0">
                </label>
            </div>
            <div class="submitOptions" style="margin-top: 10px">
                <button onclick="doEditSubject()" class="btn btn-success">تایید</button>
                <input type="submit" onclick="hideElement()" value="خیر" class="btn btn-default">
                <p id="errMsgEdit" class="errorText"></p>
            </div>
        </div>
    </span>

    <span id="addBatch" class="ui_overlay item hidden" style="position: fixed; left: 40%; right: auto; top: 174px; bottom: auto">
        <div class="header_text">افزودن دسته ای مباحث</div>
        <div onclick="hideElement()" class="ui_close_x"></div>
        <div class="body_text">
            <form method="post" action="{{route('addSubjectBatch')}}" enctype="multipart/form-data">
                {{csrf_field()}}
                <input type="file" name="subjects">
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