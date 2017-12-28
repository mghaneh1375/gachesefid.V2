@extends('layouts.form')

@section('head')
    @parent

    <script>

        var getQuizLessonsDir = '{{route('getQuizLessons')}}';

        $(document).ready(function () {
            changeKindKarname();
        });

        function getQuizLessons(qId) {

            $("#lessonContainer").empty();

            $.ajax({

                type: 'post',
                url: getQuizLessonsDir,
                data: {
                    'qId': qId
                },
                success: function (response) {

                    response = JSON.parse(response);

                    newElement = "";

                    for(i = 0; i < response.length; i++) {
                        newElement += "<option value='" + response[i].id + "'>" + response[i].name + "</option>";
                    }

                    $("#lessonContainer").append(newElement);
                    $("#divLessonContainer").css("visibility", "visible");
                    $("#getKarname").removeAttr('disabled');
                }
            });
        }

        function changeKindKarname() {

            if($("#kindKarname").find(":selected").val() == 2) {
                $("#getKarname").attr('disabled', 'disabled');
                getQuizLessons($("#quizId").find(":selected").val(), 'lessonContainer');
            }
            else {
                $("#divLessonContainer").css("visibility", "hidden");
            }

        }
    </script>
    
@stop


@section('caption')
    <div class="title">مشاهده کارنامه
    </div>
@stop

@section('main')

    <center style="margin-top: 20px">
        <form method="post" action="{{URL(route('seeResult'))}}">
            <div class="col-xs-12">
                <label style="min-width: 350px !important;">
                    <span style="float: right; width: 200px; text-align: -webkit-right;">آزمون مورد نظر</span>
                    @if(count($quizes) == 0)
                        <p class="warning_color" style="margin-top: 10px">آزمونی جهت نمایش وجود ندارد</p>
                    @else
                        <select id="quizId" style="float: left; min-width: 200px !important;" class="mySelect" name="quizId" onchange="changeKindKarname()">
                            @foreach($quizes as $quiz)
                                @if(isset($selectedQuiz) && !empty($selectedQuiz) && $selectedQuiz == $quiz->id)
                                    <option selected value="{{$quiz->id}}">{{$quiz->name}}</option>
                                @else
                                    <option value="{{$quiz->id}}">{{$quiz->name}}</option>
                                @endif
                            @endforeach
                        </select>
                    @endif
                </label>
            </div>
            @if(count($quizes) != 0)
                <div class="col-xs-12" style="margin-top: 10px">
                    <label style="min-width: 350px !important;">
                        <span style="float: right; width: 200px; text-align: -webkit-right;">نوع کارنامه مورد نظر</span>
                        <select class="mySelect" style="float: left; min-width: 200px !important;" id="kindKarname" name="kindKarname" onchange="changeKindKarname()">
                            <option value="1">کارنامه کلی</option>
                            <option value="2">کارنامه مبحثی</option>
                            <option value="3">کارنامه سوال به سوال</option>
                        </select>
                    </label>
                </div>

                <div id="divLessonContainer" class="col-xs-12" style="margin-top: 10px">
                    <label>
                        <span style="float: right; width: 200px; text-align: -webkit-right;">درس مورد نظر</span>
                        <select class="mySelect" name='lId' id="lessonContainer">
                        </select>
                    </label>
                </div>

                <div class="col-xs-12">
                    <input type="submit" id="getKarname" name="getKarname" class="btn btn-primary" style="width: auto; margin-top: 10px" value="مشاهده کارنامه">
                    @if($msg != "")
                        <center class="errorText" style="margin-top: 10px">{{$msg}}</center>
                    @endif
                </div>
            @endif
        </form>
</center>

@stop