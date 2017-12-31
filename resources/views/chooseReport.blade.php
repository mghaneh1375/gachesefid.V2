@extends('layouts.form')

@section('head')
    @parent
@stop


@section('caption')
    <div class="title">گزارشات
    </div>
@stop

<?php

    $reportsArr = ["نمای کلی آزمون", "وضعیت شهرهای شرکت‌کننده", "کارنامه تفصیلی دانش‌آموزان", "پراکندگی درصد شهرها", "کارنامه کلی دانش‌آموزان",
    "گزارش درس به درس", "پراکندگی نمرات هر درس"];
?>

@section('main')

    <center class="col-xs-12" style="margin-top: 50px">

        @if(count($reports) == 0)
            <p>گزارشی وجود ندارد</p>
        @else
            @foreach($reports as $report)
                <div class="col-xs-12">
                    <button style="margin-top: 10px; min-width: 200px" class="btn btn-primary" onclick="document.location.href = '{{($report->reportNo != '3') ? route('A' . $report->reportNo, ['quizId' => $quizId]) :  route('preA3', ['quizId' => $quizId])}}'">{{$reportsArr[$report->reportNo - 1]}}</button>
                </div>
            @endforeach
        @endif

    </center>

@stop