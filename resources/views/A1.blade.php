<?php $myArr = ['اول', 'دوم', 'سوم', 'چهارم', 'پنجم', 'ششم', 'هفتم', 'هشتم', 'نهم'] ?>
@extends('layouts.form')

@section('head')
    @parent
    <style>
        td {
            padding: 10px;
            min-width: 100px;
        }
        tr:nth-child(even) {
             background-color: #dddddd;
        }
    </style>
@stop

@section('caption')
    <div class="title">نمای کلی آزمون
    </div>
@stop

@section('main')
    <center style="margin-top: 10px">
        <a href="{{route('getQuizReport', ['quizId' => $quizId])}}"><button class="btn btn-primary">بازگشت به مرحله ی قبل</button></a>
        <div class="line"></div>

        <div class="col-xs-12" style="margin-top: 10px">
            <button class="btn btn-success" onclick="document.location.href = '{{route('A1Excel', ['quizId' => $quizId])}}'">دریافت فایل اکسل</button>
        </div>

        <div class="col-xs-12" style="overflow-x: auto">
            <table style="margin-top: 10px">
                <tr>
                    <td><center>شماره سوال</center></td>
                    <td><center>گزینه صحیح</center></td>
                    <td><center>مبحث</center></td>
                    <td><center>درس</center></td>
                    <td><center>درصد پاسخ درست</center></td>
                    <td><center>درصد پاسخ نادرست</center></td>
                    <td><center>درصد بدون پاسخ</center></td>
                    <td><center>وضعیت دشواری</center></td>
                </tr>

                <?php $i = 1; ?>
                @foreach($qInfos as $qInfo)
                    @if($qInfo->kindQ != 2)
                        <tr>
                            <td><center>{{$i}}</center></td>

                            <td><center>{{$qInfo->ans}}</center></td>

                            <td><center>
                                @foreach($qInfo->subjects as $itr)
                                    <span>{{$itr}}</span><span>&nbsp;</span>
                                @endforeach
                            </center></td>

                            <td>
                                <center>
                                    @foreach($qInfo->lessons as $itr)
                                        <span>{{$itr}}</span><span>&nbsp;</span>
                                    @endforeach
                                </center>
                            </td>

                            @if($total != 0)
                                <td><center style="direction: ltr">{{round(($qInfo->correct * 100 / $total), 0)}}</center></td>
                                <td><center style="direction: ltr">{{round((($total - $qInfo->correct - $qInfo->white) * 100 / $total), 0)}}</center></td>
                                <td><center style="direction: ltr">{{round(($qInfo->white * 100 / $total), 0)}}</center></td>
                            @else
                                <td><center>0</center></td>
                                <td><center>0</center></td>
                                <td><center>0</center></td>
                            @endif
                            <td><center>{{$qInfo->level}}</center></td>
                        </tr>
                    @else
                        @for($k = 0; $k < strlen($qInfo->ans); $k++)
                            <tr>
                                <td><center>{{$i}} -گزاره {{$myArr[$k]}}</center></td>

                                <td>
                                    <center>
                                        @if($qInfo->ans[$k] == 1)
                                            <span>صحیح</span>
                                        @else
                                            <span>ناصحیح</span>
                                        @endif
                                    </center>
                                </td>

                                <td>
                                    <center>
                                        @foreach($qInfo->subjects as $itr)
                                            <span>{{$itr}}</span><span>&nbsp;</span>
                                        @endforeach
                                    </center>
                                </td>

                                <td>
                                    <center>
                                        @foreach($qInfo->lessons as $itr)
                                            <span>{{$itr}}</span><span>&nbsp;</span>
                                        @endforeach
                                    </center>
                                </td>

                                <?php $total = $qInfo->corrects[$k] + $qInfo->inCorrects[$k] + $qInfo->whites[$k]; ?>

                                <td><center style="direction: ltr">{{round(($qInfo->corrects[$k] * 100 / $total), 0)}}</center></td>
                                <td><center style="direction: ltr">{{round(($qInfo->inCorrects[$k] * 100 / $total), 0)}}</center></td>
                                <td><center style="direction: ltr">{{round(($qInfo->whites[$k] * 100 / $total), 0)}}</center></td>

                                <td><center>{{$qInfo->level}}</center></td>
                            </tr>
                        @endfor
                    @endif
                    <?php $i++; ?>
                @endforeach
            </table>
        </div>

    </center>
@stop