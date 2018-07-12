@extends('layouts.form')

@section('head')
    @parent
@stop


@section('caption')
    <div class="title">گزارش گیری جزئی مضاعف از آزمون
    </div>
@stop

@section('main')

    <style>
        td {
            padding: 10px;
        }
    </style>

    <center style="margin-top: 10px">

        <p style="padding: 10px; font-size: 20px; color: #2ab27b">
            <span>{{$schoolName}}</span>
            <span>&nbsp;</span>
            <span>{{$cityName}}</span>
            <span>آزمون</span>
            <span>&nbsp;</span>
            <span>{{$quizName}}</span>
            <span>&nbsp;</span>
            <span>{{($online == 1) ? "آنلاین" : "حضوری"}}</span>
        </p>

        <table style="padding: 10px">
            <tr>
                <td><center>نام</center></td>
                <td><center>نام خانوادگی</center></td>
                <td><center>نام کاربری</center></td>
                <td><center>شماره همراه</center></td>
            </tr>

            @foreach($users as $itr)
                <tr>
                    <td><center>{{$itr->firstName}}</center></td>
                    <td><center>{{$itr->lastName}}</center></td>
                    <td><center>{{$itr->username}}</center></td>
                    <td><center>{{$itr->phoneNum}}</center></td>
                </tr>
            @endforeach
        </table>

        <div style="margin-top: 10px">
            <button onclick="document.location.href = '{{route('quizDoublePartialReportExcel', ['quizId' => $quizId, 'sId' => $sId, 'online' => $online, 'quizMode' => $quizMode])}}'" class="btn btn-success">دانلود فایل اکسل</button>
        </div>
    </center>
@stop