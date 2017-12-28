@extends('layouts.form')

@section('head')
    @parent
    <style>
        td {
            padding: 10px;
        }
    </style>
@stop

@section('caption')
    @if($mode == 'system')
        <div class="title">چالش سرعتیِ پای تخته</div>
    @else
        <div class="title">پشت میزت محکم بشین و امتحان بده</div>
    @endif
@stop

@section('main')
    <center class="row" style="margin-top: 50px">
        @if(count($quizes) == 0)
            <div class="col-xs-12">
                <center>آزمونی برای ثبت نام وجود ندارد</center>
            </div>
        @endif
        @if($mode == 'system')
            <table>
                <tr>
                    <td><center>نام آزمون:</center></td>
                    <td><center>تاریخ برگزاری:</center></td>
                    <td><center>ساعت برگزاری:</center></td>
                    <td><center>تاریخ شروع ثبت نام:</center></td>
                    <td><center>تاریخ اتمام ثبت نام:</center></td>
                    <td><center>هزینه آزمون:</center></td>
                </tr>
                @foreach($quizes as $quiz)

                    <tr>
                    {{--<div class="col-xs-12 quiz" data-val="quiz_{{$quiz->id}}" id="quiz_{{$quiz->id}}" style="margin-top: 10px; padding: 10px; border: 2px solid black; background-color: #ccc; border-radius: 6px">--}}
                        <td><center>{{$quiz->name}}</center></td>
                        <td><center>{{$quiz->startDate}}</center></td>
                        <td><center>{{$quiz->startTime}}</center></td>
                        <td><center>{{$quiz->startReg}}</center></td>
                        <td><center>{{$quiz->endReg}}</center></td>
                        <td><center>{{$quiz->price}} تومان</center></td>
                        <td>
                            <center>
                                <button onclick="document.location.href = '{{route('doQuizRegistry', ['quizId' => $quiz->id, 'mode' => 'system'])}}'" class="btn btn-primary">ثبت نام در آزمون</button>
                            </center>
                        </td>
                    </tr>

                    {{--</div>--}}
                @endforeach
            </table>
        @else
            <table>
                <tr>
                    <td><center>نام آزمون:</center></td>
                    <td><center>تاریخ برگزاری:</center></td>
                    <td><center>تاریخ اتمام:</center></td>
                    <td><center>ساعت برگزاری:</center></td>
                    <td><center>ساعت اتمام:</center></td>
                    <td><center>تاریخ شروع ثبت نام:</center></td>
                    <td><center>تاریخ اتمام ثبت نام:</center></td>
                    <td><center>هزینه آزمون:</center></td>
                </tr>
                @foreach($quizes as $quiz)
                {{--<div class="col-xs-12 quiz" data-val="quiz_{{$quiz->id}}" id="quiz_{{$quiz->id}}" style="margin-top: 10px; padding: 10px; border: 2px solid black; background-color: #ccc; border-radius: 6px">--}}
                    <tr>
                        <td><center>{{$quiz->name}}</center></td>
                        <td><center>{{$quiz->startDate}}</center></td>
                        <td><center>{{$quiz->endDate}}</center></td>
                        <td><center>{{$quiz->startTime}}</center></td>
                        <td><center>{{$quiz->endTime}}</center></td>
                        <td><center>{{$quiz->startReg}}</center></td>
                        <td><center>{{$quiz->endReg}}</center></td>
                        <td><center>{{$quiz->price}} تومان</center></td>
                        <td>
                            <center>
                                <button onclick="document.location.href = '{{route('doQuizRegistry', ['quizId' => $quiz->id, 'mode' => 'regular'])}}'" class="btn btn-primary">ثبت نام در آزمون</button>
                            </center>
                        </td>
                    </tr>
                {{--</div>--}}
            @endforeach
            </table>
        @endif
    </center>

    <script>
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