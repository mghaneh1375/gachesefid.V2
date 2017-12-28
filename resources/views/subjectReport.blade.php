@extends('layouts.form2')

@section('head')
    @parent
@stop


@section('caption')
    <div class="title">گزارش گیری از مباحث
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
                <td><center>آی دی مبحث</center></td>
                <td><center>نام مبحث</center></td>
                <td><center>نام درس</center></td>
                <td><center>نام مقطع تحصیلی</center></td>
            </tr>

            @foreach($subjects as $subject)
                <tr>
                    <td><center>{{$subject->id}}</center></td>
                    <td><center>{{$subject->name}}</center></td>
                    <td><center>{{$subject->lessonName}}</center></td>
                    <td><center>{{$subject->gradeName}}</center></td>
                </tr>
            @endforeach
        </table>

        <div style="margin-top: 10px">
            <button onclick="document.location.href = '{{route('subjectReportExcel')}}'" class="btn btn-success">دانلود فایل اکسل</button>
        </div>
    </center>
@stop