@extends('layouts.form')

@section('head')
    @parent

    <script src="{{URL::asset('js/highcharts.js')}}"></script>
    <script src="{{URL::asset('js/exporting.js')}}"></script>

    <style>

        td > center{
            padding: 5px;
            min-width: 80px;
        }
        tr:nth-child(even) {
            background-color: #dddddd;
        }
        .highcharts-series-0 > .highcharts-point{
            fill: #ecd6a3;
            stroke: #ab4e1e;
        }

        .highcharts-series-1 > .highcharts-point{
            fill: #65ec38;
            stroke: #270767;
        }

        .highcharts-background {
            fill: #b6b6b6;
            stroke: #000000;
            stroke-width: 2px;
        }
        .highcharts-color-0 {
            fill: #ecd6a3;
            stroke: #ab4e1e;
        }
        .highcharts-color-1 {
            fill: #65ec38;
            stroke: #270767;
        }

        .highcharts-tooltip-box {
            fill: #6e6e6e;
            stroke-width: 0;
        }
    </style>

@stop


@section('caption')
    <div class="title">کارنامه تفصیلی دانش‌آموزان
    </div>
@stop

@section('main')

    <center style="margin-top: 10px">
        @if(empty($backURL))
            <a href="{{route('preA3', ['quizId' => $quizId])}}"><button class="btn btn-primary">بازگشت به مرحله قبل</button></a>
        @else
            <a href="{{route('A5', ['quizId' => $quizId])}}"><button class="btn btn-primary">بازگشت به مرحله قبل</button></a>
        @endif
        <div class="line"></div>

        @if(isset($msg) && !empty($msg) && $msg == "err")
            <p style="margin-top: 10px; color: #963019 ">در حال حاضر مشاهده کارنامه امکان پذیر نمی باشد</p>
        @else

            <center style="font-size: 24px; color: #963019">{{$name}}</center>
            <div style="overflow-x: auto">

                <h3>کارنامه سوالات تستی</h3>

                <table style="margin-top: 10px; width: 100%; margin-bottom: 10px; border-bottom: 2px solid #656565">
                    <tr>
                        <td><center>نام درس</center></td>
                        <td><center>ضریب درس</center></td>
                        <td><center>تعداد کل سوالات</center></td>
                        <td><center>درست</center></td>
                        <td><center>نادرست</center></td>
                        <td><center>نزده</center></td>
                        <td><center><span>نمره از </span><span>{{$totalMark}}</span></center></td>
                        <td><center>درصد پاسخ گویی</center></td>
                        <td><center>میانگین درصد پاسخ گویی</center></td>
                        <td><center>بیشترین درصد پاسخ گویی</center></td>
                        <td><center>کمترین درصد پاسخ گویی</center></td>
                    </tr>

                    <?php
                    $i = 0;
                    ?>

                    @foreach($lessons as $lesson)

                        @if($roq[$i][2] != 0)
                            
                            <?php $percent = round($taraz[$i]->percent * 100 / $roq[$i][3], 2); ?>
                            <?php $minPercent = round($avgs[$i]->minPercent * 100 / $roq[$i][3], 2); ?>
                            <?php $maxPercent = round($avgs[$i]->maxPercent * 100 / $roq[$i][3], 2); ?>
                            <?php $avgPercent = round($avgs[$i]->avg * 100 / $roq[$i][3], 2); ?>

                            <tr>
                                <td><center>{{$lesson->name}}</center></td>
                                <td><center>{{$lesson->coherence}}</center></td>
                                <td><center>{{$roq[$i][2]}}</center></td>
                                <td><center>{{$roq[$i][1]}}</center></td>
                                <td><center>{{$roq[$i][0]}}</center></td>
                                <td><center>{{$roq[$i][2] - $roq[$i][0] - $roq[$i][1]}}</center></td>

                                <td><center style="direction: ltr">{{($percent <= 0) ? 0 : round($percent * $totalMark / 100, 0)}}</center></td>
                                <td><center style="direction: ltr">{{$percent}}</center></td>
                                <td><center style="direction: ltr">{{$avgPercent}}</center></td>
                                <td><center style="direction: ltr">{{$maxPercent}}</center></td>
                                <td><center style="direction: ltr">{{$minPercent}}</center></td>
                            </tr>
                        @endif
                        <?php
                        $i++;
                        ?>
                    @endforeach
                </table>

                <?php $i = 0; $allow = false; ?>
                @foreach($lessons as $lesson)
                    @if($roq2[$i++][2] != 0)
                        <?php $allow = true; ?>
                    @endif
                @endforeach

                @if($allow)
                    <h3>کارنامه سوالات کوتاه پاسخ</h3>
                    <table style="margin-top: 10px; width: 100%; border-bottom: 2px solid #656565; margin-bottom: 10px">
                        <tr>
                            <td><center>نام درس</center></td>
                            <td><center>تعداد کل سوالات</center></td>
                            <td><center>درست</center></td>
                            <td><center>نادرست</center></td>
                            <td><center>نزده</center></td>
                            <td><center>نمره</center></td>
                            <td><center>میانگین نمره</center></td>
                            <td><center>بیشترین نمره</center></td>
                            <td><center>کمترین نمره</center></td>
                        </tr>

                        <?php
                        $i = 0;
                        ?>

                        @foreach($lessons as $lesson)
                            @if($roq2[$i][2] != 0)
                                <tr>
                                    <td><center>{{$lesson->name}}</center></td>
                                    <td><center>{{$roq2[$i][2]}}</center></td>
                                    <td><center>{{$roq2[$i][1]}}</center></td>
                                    <td><center>{{$roq2[$i][0]}}</center></td>
                                    <td><center>{{$roq2[$i][2] - $roq2[$i][0] - $roq2[$i][1]}}</center></td>
                                    <td><center style="direction: ltr">{{round($taraz[$i]->percent2, 2)}}</center></td>
                                    <td><center style="direction: ltr">{{round($avgs[$i]->avg2, 2)}}</center></td>
                                    <td><center style="direction: ltr">{{round($avgs[$i]->maxPercent2, 2)}}</center></td>
                                    <td><center style="direction: ltr">{{round($avgs[$i]->minPercent2, 2)}}</center></td>
                                </tr>
                            @endif
                            <?php
                            $i++;
                            ?>
                        @endforeach
                    </table>
                @endif

                <?php $i = 0; $allow = false; ?>
                @foreach($lessons as $lesson)
                    @if($roq3[$i++][2] != 0)
                        <?php $allow = true; ?>
                    @endif
                @endforeach

                @if($allow)

                    <h3>کارنامه سوالات چند گزاره ای</h3>
                    <table style="margin-top: 10px; width: 100%; border-bottom: 2px solid #656565; margin-bottom: 10px">
                        <tr>
                            <td><center>نام درس</center></td>
                            <td><center>تعداد کل سوالات</center></td>
                            <td><center>درست</center></td>
                            <td><center>نادرست</center></td>
                            <td><center>نزده</center></td>
                            <td><center>نمره</center></td>
                            <td><center>میانگین نمره</center></td>
                            <td><center>بیشترین نمره</center></td>
                            <td><center>کمترین نمره</center></td>
                        </tr>

                        <?php
                        $i = 0;
                        ?>

                        @foreach($lessons as $lesson)
                            @if($roq3[$i][2] != 0)
                                <tr>
                                    <td><center>{{$lesson->name}}</center></td>
                                    <td><center>{{$roq3[$i][2]}}</center></td>
                                    <td><center>{{$roq3[$i][1]}}</center></td>
                                    <td><center>{{$roq3[$i][0]}}</center></td>
                                    <td><center>{{$roq3[$i][2] - $roq3[$i][0] - $roq3[$i][1]}}</center></td>
                                    <td><center style="direction: ltr">{{round($taraz[$i]->percent3, 2)}}</center></td>
                                    <td><center style="direction: ltr">{{round($avgs[$i]->avg3, 2)}}</center></td>
                                    <td><center style="direction: ltr">{{round($avgs[$i]->maxPercent3, 2)}}</center></td>
                                    <td><center style="direction: ltr">{{round($avgs[$i]->minPercent3, 2)}}</center></td>
                                </tr>
                            @endif
                            <?php
                            $i++;
                            ?>
                        @endforeach
                    </table>
                @endif
            </div>


            <?php
            $i = 0;
            $sum = 0;
            $total = 0;
            ?>
            <table style="margin-top: 30px; width: 100%">
                <tr>

                    <td><center>نام درس</center></td>

                    <td><center>رتبه در کشور</center></td>
                    <td><center>رتبه در استان</center></td>
                    <td><center>رتبه در شهر/منطقه</center></td>
                    <td><center>تراز</center></td>
                    {{--<td><center>وضعیت</center></td>--}}
                </tr>

                @foreach($lessons as $lesson)

                    <?php
                        $sum += $taraz[$i]->taraz;
                        $total++;
                    ?>

                    <tr>

                        <td><center>{{$lesson->name}}</center></td>

                        <td><center>{{$rankInLesson[$i]}}</center></td>

                        <td><center>{{$rankInLessonState[$i]}}</center></td>

                        <td><center>{{$rankInLessonCity[$i]}}</center></td>

                        <td><center style="direction: ltr">{{$taraz[$i]->taraz}}</center></td>

                        {{--<td>--}}
                            {{--<center>--}}
                                {{--@foreach($status as $itr)--}}
                                    {{--@if(($itr->type && $itr->floor <= $taraz[$i]->percent &&--}}
                                     {{--$taraz[$i]->percent <= $itr->ceil) || (!$itr->type && $kindKarname->lessonAvg &&--}}
                                     {{--$taraz[$i]->percent <= $avgs[$i]->avg + $itr->ceil && $taraz[$i]->percent >= $avgs[$i]->avg - $itr->floor))--}}
                                        {{--@if($itr->pic)--}}
                                            {{--<img width="40px" height="40px" src="{{URL::asset('status') . '/' . $itr->status}}">--}}
                                        {{--@else--}}
                                            {{--<p style="background-color: {{$itr->color}}">{{$itr->status}}</p>--}}
                                        {{--@endif--}}
                                    {{--@endif--}}
                                {{--@endforeach--}}
                            {{--</center>--}}
                        {{--</td>--}}
                    </tr>
                    <?php $i++; ?>
                @endforeach
            </table>

            <table style="margin-top: 10px">
                <tr>
                    <td><center>میانگین تراز</center></td>
                    <td><center>رتبه در کشور</center></td>
                    <td><center>رتبه در استان</center></td>
                    <td><center>رتبه در شهر/منطقه</center></td>
                </tr>
                <tr>
                    <td><center>{{round(($sum / $total), 0)}}</center></td>
                    <td><center>{{$rank}}</center></td>
                    <td><center>{{$stateRank}}</center></td>
                    <td><center>{{$cityRank}}</center></td>
                </tr>
            </table>

            <div class="col-xs-12" style="margin-top: 10px; padding: 5px;">
                <a href="{{route('printKarnameMaster', ['quizId' => $quizId, 'uId' => $uId])}}" target="_blank" class="btn btn-success">چاپ کارنامه</a>
            </div>

            <div id="barChart1" style="min-width: 310px; height: 400px; margin-top: 10px; direction: ltr"></div>
            <script type="text/javascript">

                var taraz = {!! json_encode($taraz) !!};
                var lessons = {!! json_encode($lessons) !!};
                var avgs = {!! json_encode($avgs) !!};


                percents = [];
                for(i = 0; i < taraz.length; i++)
                    percents[i] = Math.round(taraz[i].percent + taraz[i].percent2 + taraz[i].percent3);

                for(i = 0; i < lessons.length; i++)
                    lessons[i] = lessons[i].name;

                maxPercents = [];
                for(i = 0; i < avgs.length; i++) {
                    maxPercents[i] = Math.round(avgs[i].maxPercent + avgs[i].maxPercent2 + avgs[i].maxPercent3);
                    avgs[i] = Math.round(avgs[i].avg + avgs[i].avg2 + avgs[i].avg3);
                }
                Highcharts.chart('barChart1', {

                    chart: {
                        polar: true,
                        type: 'line'
                    },

                    title: {
                        text: 'نمودار مقایسه ای دورس',
                        x: -80
                    },

                    pane: {
                        size: '90%'
                    },

                    xAxis: {
                        categories: lessons,
                        tickmarkPlacement: 'on',
                        lineWidth: 0
                    },

                    yAxis: {
                        gridLineInterpolation: 'polygon',
                        lineWidth: 0,
                        min: 0
                    },
                    tooltip: {
                        useHTML: true,
                        headerFormat: '<table>',
                        pointFormat: '<tr><td style="direction: ltr"><b>{point.y}</b></td></tr>',
                        footerFormat: '</table>',
                        valueDecimals: 2
                    },

                    legend: {
                        align: 'right',
                        verticalAlign: 'top',
                        y: 70,
                        layout: 'vertical'
                    },

                    series: [{
                        name: 'درصد داوطلب',
                        data: percents,
                        pointPlacement: 'on'
                    },{
                        name: 'میانگین',
                        data: avgs,
                        pointPlacement: 'on'
                    }]

                });
            </script>
        @endif



    </center>
@stop