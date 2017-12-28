@extends('layouts.form')

@section('head')
    @parent
    <script>
        var getLessonsDir = '{{route('getLessons')}}';
        var getSubjectsDir = '{{route('getSubjects2')}}';
        var getSubjectQuestionNumsUser = '{{route('getSubjectQuestionNumsUser')}}';

        $(document).ready(function () {
            getLessons($("#gradeId").val());
        });

    </script>

    <link rel="stylesheet" href="{{URL::asset('css/form.css')}}">
    <script>
        
        function redirect() {

            if($("#subjects").val() == -1) {
                $("#err").empty().append('سوالی در این مبحث موجود نیست');
                return;
            }

            $.ajax({
                type: 'post',
                url: getSubjectQuestionNumsUser,
                data: {
                    'sId': $("#subjects").val()
                },
                success: function (response) {
                    if(response == "nok") {
                        $("#err").empty().append('سوالی در این مبحث موجود نیست');
                        return;
                    }
                    
                    document.location.href = $('#subjects').find(":selected").attr('data-val');
                }
            });
        }
        
        function getLessons(gradeId) {

            $.ajax({
                type: 'post',
                url: getLessonsDir,
                data: {
                    'gradeId' : gradeId
                },
                success: function (response) {

                    response = JSON.parse(response);

                    newElement = "";
                    $("#lessons").empty();

                    if(response.length == 0)
                        newElement = "<option value='none'>درسی موجود نیست</option>";

                    for(i = 0; i < response.length; i++) {
                        newElement += "<option value='" + response[i].id + "'>" + response[i].name + "</option>";
                    }

                    $("#lessons").append(newElement);

                    getSubjects($("#lessons").val());

                }
            });
        }

        function getSubjects(lessonId) {

            selectedLesson = lessonId;

            $.ajax({
                type: 'post',
                url: getSubjectsDir,
                data: {
                    'lessonId' : lessonId
                },
                success: function (response) {

                    response = JSON.parse(response);

                    newElement = "";

                    if(response.length == 0)
                        newElement = "<option value='-1'>مبحثی موجود نیست</option>";

                    for(i = 0; i < response.length; i++) {
                        newElement += "<option value='"+ response[i].id +"' data-val='" + response[i].url + "'>" + response[i].name + "</option>";
                    }

                    $("#subjects").empty().append(newElement);

                }
            });
        }
    </script>
@stop


@section('caption')
    <div class="title">سوالات خریداری شده به تفکیک مبحث
    </div>
@stop

@section('main')

    <style>
        .mySelect {
            min-width: 200px !important;
        }
    </style>

    <center class="myRegister">
        <div class="row data">

            <div class="col-xs-12">
                <div class="col-xs-7">
                    <select class="mySelect" id="gradeId" onchange="getLessons(this.value)">
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
                    <select class="mySelect" id="lessons" onchange="getSubjects(this.value)">
                    </select>
                </div>
                <div class="col-xs-5">
                    <span>دروس</span>
                </div>
            </div>

            <div class="col-xs-12">
                <div class="col-xs-7">
                    <select class="mySelect" id="subjects"></select>
                </div>
                <div class="col-xs-5">
                    <span>مبحث مورد نظر</span>
                </div>
            </div>


            <div class="col-xs-12" style="margin-top: 10px">
                <button onclick="redirect()" class="btn btn-primary">تایید</button>
                <p class="errText" id="err"></p>
            </div>

        </div>
    </center>
@stop