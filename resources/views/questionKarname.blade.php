@extends('layouts.form')

@section('head')
    @parent
    <style>
        td {
            padding: 10px;
            min-width: 100px;
        }
    </style>
@stop

@section('caption')
    <div class="title">مشاهده کارنامه سوال به سوال
    </div>
@stop

@section('main')
    <center style="margin-top: 10px">
        <a href="{{route('seeResult', ['quizId' => $quizId])}}"><button class="btn btn-primary">بازگشت به مرحله ی قبل</button></a>
        <div class="line"></div>

        <div style="overflow-x: auto">
            <table style="margin-top: 10px">
                <tr>
                    <td><center>شماره سوال</center></td>
                    <td><center>نام مبحث</center></td>
                    <td><center>نام درس</center></td>
                    <td><center>کلید</center></td>
                    <td><center>پاسخ</center></td>
                    <td><center>درصد پاسخ درست</center></td>
                    <td><center>درصد پاسخ نادرست</center></td>
                    <td><center>درصد بدون جواب</center></td>
                    <td><center>وضعیت دشواری</center></td>
                </tr>

                <?php
                $i = 1;
                ?>
                
                @foreach($qInfos as $qInfo)

                    <tr>
                        <td><center>{{$i}}</center></td>
                        <td><center>
                            @foreach($qInfo->subjects as $itr)
                                <span>{{$itr}}</span><span>&nbsp;</span>
                            @endforeach
                        </center></td>
                        <td><center>
                            @foreach($qInfo->lessons as $itr)
                                <span>{{$itr}}</span><span>&nbsp;</span>
                            @endforeach
                        </center></td>
                        <td><center>{{$qInfo->ans}}</center></td>
                        <td><center style="direction: ltr">{{$qInfo->result}}</center></td>
                        <td><center style="direction: ltr">{{round($qInfo->correct * 100 / $total, 0)}}</center></td>
                        <td><center style="direction: ltr">{{round((($total - $qInfo->correct - $qInfo->white) * 100 / $total), 0)}}</center></td>
                        <td><center style="direction: ltr">{{round($qInfo->white * 100 / $total, 0)}}</center></td>
                        <td><center style="direction: ltr">{{$qInfo->level}}</center></td>
                    </tr>

                    <?php
                    $i++;
                    ?>
                @endforeach
            </table>
        </div>
    </center>
@stop