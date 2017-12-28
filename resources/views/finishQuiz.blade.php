@extends('layouts.form')

@section('head')
    @parent
@stop


@section('caption')
    <div class="title">اتمام آزمون
    </div>
@stop

@section('main')
    <center class="myRegister">
        <div class="row data">
            @if(count($quizes) == 0)
                <div class="col-xs-12" style="margin-top: 10px"><center>آزمونی وجود ندارد</center></div>
            @endif
            @foreach($quizes as $quiz)
                <div class="col-xs-12 quiz" data-val="quiz_{{$quiz->id}}" id="quiz_{{$quiz->id}}" style="margin-top: 10px; padding: 10px; border: 2px solid black; background-color: #ccc; border-radius: 6px">
                    <p><span>نام آزمون:&nbsp;&nbsp;</span><span>{{$quiz->name}}</span></p>
                    <p><span>تاریخ برگزاری:&nbsp;&nbsp;</span><span>{{$quiz->startDate}}</span></p>
                    <p><span>ساعت برگزاری:&nbsp;&nbsp;</span><span>{{$quiz->startTime}}</span></p>
                    <p><span>مدت آزمون:&nbsp;&nbsp;</span><span>{{$quiz->timeLen}}&nbsp;</span><span>دقیقه</span></p>
                    <button onclick="finishQuiz('{{$quiz->id}}')" class="btn btn-primary">اتمام آزمون</button>
                </div>
            @endforeach
        </div>
    </center>

    <script>

        var finishQuizDir = '{{route('doFinishQuiz')}}';
        var finishDir = '{{route('finishQuiz')}}';

        function finishQuiz(val) {

            $.ajax({
                type: 'post',
                url: finishQuizDir,
                data: {
                    'quizId': val
                },
                success: function (response) {
                    if(response == "ok")
                        document.location.href = finishDir;
                }
            });
        }

        $(".quiz").mouseenter(function () {
            val = $(this).attr('data-val');

            $(".quiz").css('background-color', '#ccc');
            $(".quiz").css('border-color', 'black');

            $("#" + val).css('background-color', '#fafef5');
            $("#" + val).css('border-color', '#7ed321');

        });

        $(".quiz").mouseleave(function () {
            $(".quiz").css('background-color', '#ccc');
            $(".quiz").css('border-color', 'black');
        });
    </script>
@stop