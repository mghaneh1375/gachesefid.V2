@extends('layouts.form')

@section('head')
    @parent
@stop

@section('caption')
    <div class="title">آزمون های من</div>
@stop

@section('main')

    <style>

        td {
            padding: 10px;
            min-width: 100px;
        }

        table {
            max-height: 150vh;
            overflow: auto;
        }

        .btn {
            min-width: 100px;
        }

    </style>

    <center class="row alaki" style="margin-top: 50px">

        @if(count($quizes) == 0)
            <div class="col-xs-12">
                <h4>هیچ آزمونی وجود ندارد</h4>
            </div>
        @endif

        @if(!empty($err))
            <p class="errorText" style="padding: 10px">{{$err}}</p>
        @endif

        <table>

            <tr>
                <td><center>نام آزمون</center></td>
                <td><center>تاریخ شروع آزمون</center></td>
                <td><center>تاریخ اتمام آزمون</center></td>
                <td><center>زمان شروع آزمون</center></td>
                <td><center>زمان اتمام آزمون</center></td>
                <td><center>مدت ارزیابی(دقیقه)</center></td>
                <td></td>
            </tr>

            @foreach($quizes as $quiz)
                <tr>
                    <td><center>{{$quiz->name}}</center></td>
                    <td><center>{{$quiz->startDate}}</center></td>
                    @if($quiz->mode == "system")
                        <td></td>
                    @else
                        <td><center>{{$quiz->endDate}}</center></td>
                    @endif

                    <td><center>{{$quiz->startTime}}</center></td>

                    @if($quiz->mode == "system")
                        <td></td>
                    @else
                        <td><center>{{$quiz->endTime}}</center></td>
                    @endif

                    <td><center>{{$quiz->timeLen}}</center></td>

                    @if($quiz->mode == "system")
                        @if($quiz->quizEntry == 1)
                            <td><button id="btn_{{$quiz->id}}" onclick="showConfirmationPane('{{$quiz->id}}')" data-val="{{route('doQuiz', ['quizId' => $quiz->id])}}" class="btn btn-success">ورود به آزمون</button></td>
                        @elseif($quiz->quizEntry == -2)
                            <td><button onclick="document.location.href = '{{route('showQuizWithOutTime', ['quizId' => $quiz->id, 'quizMode' => $quiz->quizMode])}}'" class="btn btn-primary">مرور آزمون</button></td>
                        @elseif($quiz->quizEntry == -1)
                            <td><button class="btn btn-default">آزمون مورد نظر هنوز باز نشده است</button></td>
                        @endif
                    @else
                        @if($quiz->quizEntry == 1)
                            <td><button id="btn_{{$quiz->id}}" onclick="showConfirmationPane('{{$quiz->id}}')" data-val="{{route('doRegularQuiz', ['quizId' => $quiz->id])}}" class="btn btn-success">ورود به آزمون</button></td>
                        @elseif($quiz->quizEntry == -2)
                            <td><button onclick="document.location.href = '{{route('showQuizWithOutTime', ['quizId' => $quiz->id, 'quizMode' => $quiz->quizMode])}}'" class="btn btn-primary">مرور آزمون</button></td>
                        @elseif($quiz->quizEntry == -1)
                            <td><button style="cursor: auto" class="btn btn-default">آزمون مورد نظر هنوز باز نشده است</button></td>
                        @endif
                    @endif
                </tr>
            @endforeach

            @foreach($selfQuizes as $quiz)
                <tr>
                    <td><center> آزمون دست ساز  {{$quiz->created}}</center></td>
                    <td><center></center></td>
                    <td></td>
                    <td><center></center></td>
                    <td></td>
                    <td><center>{{$quiz->timeLen}}</center></td>

                    @if($quiz->quizEntry == 1)
                        <td><button id="btn_{{$quiz->id}}" onclick="showConfirmationPane('{{$quiz->id}}')" data-val="{{route('doSelfQuiz', ['quizId' => $quiz->id])}}" class="btn btn-success">ورود به آزمون</button></td>
                    @elseif($quiz->quizEntry == -2)
                        <td><button onclick="document.location.href = '{{route('showQuizWithOutTime', ['quizId' => $quiz->id, 'quizMode' => $quiz->quizMode])}}'" class="btn btn-primary">مرور آزمون</button></td>
                    @endif
                </tr>
            @endforeach
        </table>

    </center>

    <span id="confirmationPane" class="ui_overlay item hidden" style="position: fixed; left: 30%; right: 30%; top: 170px; bottom: auto">
        <div class="header_text">ورود به آزمون</div>
        <div onclick="hideElement()" class="ui_close_x"></div>
        <div class="body_text">
            <div class="col-xs-12" style="margin-top: 10px">
                آیا از ورود به آزمون اطمینان دارید؟
            </div>
            <div class="col-xs-12" style="margin-top: 10px">
                <center>
                    <button onclick="goToQuiz()" class="btn btn-primary">
بله
                    </button>
                    <button onclick="hideElement()" class="btn btn-danger">خیر</button>
                    <p style="margin-top: 5px" class="errorText" id="errMsgConfirm"></p>
                </center>
            </div>
        </div>
    </span>

    <script>

        var selectedQuizId;

        function goToQuiz() {
            document.location.href = $("#btn_" + selectedQuizId).attr('data-val');
        }

        function showConfirmationPane(quizId) {
            selectedQuizId = quizId;
            $("#confirmationPane").removeClass('hidden');
        }

        function hideElement() {
            $("#confirmationPane").addClass('hidden');
        }

        $(document).ready(function () {
            @foreach($quizes as $quiz)
                @if($quiz->mode == "system" && $quiz->quiz->quizEntry)
                    showReminderTime('{{$quiz->quiz->id}}');
                @endif
            @endforeach
        });
        
        function showReminderTime(qId) {

            var total_time = $("#reminder_" + qId).attr('data-val');
            if (total_time > 0)
                setTimeout("checkTime(" + qId + ", " + total_time + ")", 1);
            else
                document.location.href = '{{route('quizEntry')}}';
        }

        function checkTime(qId, total_time) {

            var c_minutes = parseInt(total_time / 60);
            var c_seconds = parseInt(total_time % 60);
            document.getElementById("reminder_" + qId).innerHTML =  c_seconds + " : " + c_minutes;
            if (total_time <= 0)
                document.location.href = '{{route('quizEntry')}}';
            else {
                total_time--;
                setTimeout("checkTime(" + qId + ", " + total_time + ")", 1000);
            }
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