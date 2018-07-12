@extends('layouts.form')

@section('head')
    @parent
@stop


@section('caption')
    <div class="title">گزارش گیری از آزمون ها
    </div>
@stop

@section('main')

    <style>
        td {
            padding: 10px;
        }
    </style>

    <center style="margin-top: 10px">

        <table style="padding: 10px">
            <tr>
                <td><center>نام آزمون</center></td>
                <td><center>آی دی آزمون</center></td>
                <td><center>نوع آزمون</center></td>
                <td><center>تعداد ثبت نام ها</center></td>
                <td><center>تاریخ برگزاری</center></td>
                <td><center>ساعت برگزاری</center></td>
            </tr>

            @foreach($regularQuizes as $itr)
                <tr>
                    <td style="cursor: pointer" onclick="document.location.href = '{{route('partialQuizReport', ['quizId' => $itr->id, 'quizMode' => getValueInfo('regularQuiz')])}}'"><center>{{$itr->name}}</center></td>
                    <td><center>{{$itr->id}}</center></td>
                    <td><center>پشت میز</center></td>
                    <td><center> حضوری: {{$itr->nonOnlineRegistered}} - غیر حضوری:  {{$itr->onlineRegistered}} </center></td>
                    <td><center> شروع:  {{$itr->startDate}} - اتمام : {{$itr->endDate}} </center></td>
                    <td><center> شروع:  {{$itr->startTime}} - اتمام : {{$itr->endTime}} </center></td>
                </tr>
            @endforeach

            @foreach($systemQuizes as $itr)
                <tr>
                    <td style="cursor: pointer" onclick="document.location.href = '{{route('partialQuizReport', ['quizId' => $itr->id, 'quizMode' => getValueInfo('systemQuiz')])}}'"><center>{{$itr->name}}</center></td>
                    <td><center>{{$itr->id}}</center></td>
                    <td><center>پای تخته</center></td>
                    <td><center>{{$itr->registered}}</center></td>
                    <td><center> شروع:  {{$itr->startDate}}</center></td>
                    <td><center> شروع:  {{$itr->startTime}}</center></td>
                </tr>
            @endforeach
        </table>

        <div style="margin-top: 10px">
            <button onclick="document.location.href = '{{route('quizReportExcel')}}'" class="btn btn-success">دانلود فایل اکسل</button>
        </div>
    </center>
@stop