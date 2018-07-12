@extends('layouts.form')

@section('head')
    @parent
@stop


@section('caption')
    <div class="title">گزارش گیری از پایه تحصیلی
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
                <td><center>نام پایه</center></td>
                <td><center>تعداد سوالات</center></td>
                <td><center>تعداد دانش آموزان</center></td>
            </tr>

            @foreach($grades as $grade)
                <tr>
                    <td><center>{{$grade->name}}</center></td>
                    <td><center>{{$grade->qNo}}</center></td>
                    <td><center>{{$grade->studentNo}}</center></td>
                </tr>
            @endforeach
        </table>

        <div style="margin-top: 10px">
            <button onclick="document.location.href = '{{route('gradeReportExcel')}}'" class="btn btn-success">دانلود فایل اکسل</button>
        </div>
    </center>
@stop