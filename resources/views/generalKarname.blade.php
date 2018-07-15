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

        .highcharts-series-0 > .highcharts-point{
            fill: #ecd6a3;
            stroke: #ab4e1e;
        }

        .highcharts-series-1 > .highcharts-point{
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
    <div class="title">مشاهده کارنامه کلی
    </div>
@stop

@section('main')

    <center style="margin-top: 10px">
        <a href="{{route('seeResult', ['quizId' => $quizId])}}"><button class="btn btn-primary">بازگشت به مرحله قبل</button></a>
        <div class="line"></div>
        <div style="overflow-x: auto">
            <table style="margin-top: 10px; width: 100%; border-bottom: 2px solid #656565; margin-bottom: 10px">
                <tr>
                    <td><center>نام درس</center></td>
                    @if($kindKarname->coherences)
                        <td><center>ضریب درس</center></td>
                    @endif
                    <td><center>تعداد کل سوالات</center></td>
                    <td><center>درست</center></td>
                    <td><center>نادرست</center></td>
                    <td><center>نزده</center></td>
                    @if($kindKarname->lessonMark)
                        <td><center><span>نمره از </span><span>{{$totalMark}}</span></center></td>
                    @endif
                    <td><center>درصد پاسخ گویی</center></td>
                    @if($kindKarname->lessonAvg)
                        <td><center>میانگین درصد پاسخ گویی</center></td>
                    @endif
                    @if($kindKarname->lessonMaxPercent)
                        <td><center>بیشترین درصد پاسخ گویی</center></td>
                    @endif
                    @if($kindKarname->lessonMinPercent)
                        <td><center>کمترین درصد پاسخ گویی</center></td>
                    @endif
                </tr>

                <?php
                $i = 0;
                $sum = 0;
                $total = 0;
                ?>

                @foreach($lessons as $lesson)

                        <tr>
                            <td><center>{{$lesson->name}}</center></td>
                            @if($kindKarname->coherences)
                                <td><center>{{$lesson->coherence}}</center></td>
                            @endif
                            <td><center>{{$roq[2][$i]}}</center></td>
                            <td><center>{{$roq[1][$i]}}</center></td>
                            <td><center>{{$roq[0][$i]}}</center></td>
                            <td><center>{{$roq[2][$i] - $roq[0][$i] - $roq[1][$i]}}</center></td>

                            @if($kindKarname->lessonMark)
                                <td><center style="direction: ltr">{{($taraz[$i]->percent <= 0) ? 0 : round($taraz[$i]->percent * $totalMark / 100, 0)}}</center></td>
                            @endif
                            <td><center style="direction: ltr">{{round($taraz[$i]->percent, 0)}}</center></td>
                            @if($kindKarname->lessonAvg)
                                <td><center style="direction: ltr">{{round($avgs[$i]->avg, 0)}}</center></td>
                            @endif
                            @if($kindKarname->lessonMaxPercent)
                                <td><center style="direction: ltr">{{round($avgs[$i]->maxPercent, 0)}}</center></td>
                            @endif
                            @if($kindKarname->lessonMinPercent)
                                <td><center style="direction: ltr">{{round($avgs[$i]->minPercent, 0)}}</center></td>
                            @endif
                        </tr>
                        <?php
                        $sum += $taraz[$i]->taraz * $lesson->coherence;
                        $total += $lesson->coherence;
                        $i++;
                        ?>
                        @endforeach
            </table>
        </div>

        <?php
            $i = 0;
        ?>

        <table style="margin-top: 30px; width: 100%">
            <tr>

                <td><center>نام درس</center></td>

                @if($kindKarname->lessonCountryRank)
                    <td><center>رتبه در کشور</center></td>
                @endif
                @if($kindKarname->lessonStateRank)
                    <td><center>رتبه در استان</center></td>
                @endif
                @if($kindKarname->lessonCityRank)
                    <td><center>رتبه در شهر</center></td>
                @endif
                @if($kindKarname->partialTaraz)
                    <td><center>تراز</center></td>
                @endif
                @if($kindKarname->lessonStatus)
                    <td><center>وضعیت</center></td>
                @endif
            </tr>

            @foreach($lessons as $lesson)

                <tr>

                    <td><center>{{$lesson->name}}</center></td>

                    @if($kindKarname->lessonCountryRank)
                        <td><center>{{$rankInLesson[$i]}}</center></td>
                    @endif
                    @if($kindKarname->lessonStateRank)
                        <td><center>{{$rankInLessonState[$i]}}</center></td>
                    @endif
                    @if($kindKarname->lessonCityRank)
                        <td><center>{{$rankInLessonCity[$i]}}</center></td>
                    @endif
                    @if($kindKarname->partialTaraz)
                        <td><center style="direction: ltr">{{$taraz[$i]->taraz}}</center></td>
                    @endif
                    @if($kindKarname->lessonStatus)
                        <td>
                            <center>
                                @foreach($status as $itr)
                                    @if(($itr->type && $itr->floor <= $taraz[$i]->percent &&
                                     $taraz[$i]->percent <= $itr->ceil) || (!$itr->type && $kindKarname->lessonAvg &&
                                     $taraz[$i]->percent <= $avgs[$i]->avg + $itr->ceil && $taraz[$i]->percent >= $avgs[$i]->avg - $itr->floor))
                                        @if($itr->pic)
                                            <img width="40px" height="40px" src="{{URL('status') . '/' . $itr->status}}">
                                        @else
                                            <p style="background-color: {{$itr->color}}">{{$itr->status}}</p>
                                        @endif
                                    @endif
                                @endforeach
                            </center>
                        </td>
                    @endif
                </tr>
                <?php $i++; ?>
            @endforeach
        </table>

        <table style="margin-top: 10px">
            @if($pack)
                <caption><center>منفک از بسته</center></caption>
            @endif
            <tr>
                @if($kindKarname->generalTaraz)
                    <td><center>میانگین تراز</center></td>
                @endif
                @if($kindKarname->generalCountryRank)
                    <td><center>رتبه در کشور</center></td>
                @endif
                @if($kindKarname->generalStateRank)
                    <td><center>رتبه در استان</center></td>
                @endif
                @if($kindKarname->generalCityRank)
                    <td><center>رتبه در شهر</center></td>
                @endif
            </tr>
            <tr>
                @if($kindKarname->generalTaraz)
                    <td><center>{{round(($sum / $total), 0)}}</center></td>
                @endif
                @if($kindKarname->generalCountryRank)
                    <td><center>{{$rank}}</center></td>
                @endif
                @if($kindKarname->generalStateRank)
                    <td><center>{{$stateRank}}</center></td>
                @endif
                @if($kindKarname->generalCityRank)
                    <td><center>{{$cityRank}}</center></td>
                @endif
            </tr>
        </table>
        @if($pack)
            <table style="margin-top: 10px">
                <caption><center>بسته ای</center></caption>
                <tr>
                    <td><center>نوع رتبه بندی</center></td>
                    <td><center>جمع/میانگین</center></td>
                    <td><center>رتبه</center></td>
                </tr>
                <tr>
                    <td><center>جمع انباره ای</center></td>
                    <td><center>{{$sumTaraz}}</center></td>
                    <td><center>{{$sumRate}}</center></td>
                </tr>
                <tr>
                    <td><center>میانگین</center></td>
                    <td><center>{{$avgTaraz}}</center></td>
                    <td><center>{{$avgRate}}</center></td>
                </tr>
            </table>
        @endif

        <div class="col-xs-12" style="margin-top: 10px; padding: 5px;">
            <a href="{{route('printKarname', ['quizId' => $quizId])}}" target="_blank" class="btn btn-success">چاپ کارنامه</a>
        </div>

        @if($kindKarname->lessonBarChart)
            <div id="barChart1" style="min-width: 310px; height: 400px; margin-top: 10px; direction: ltr"></div>
            <script type="text/javascript">

                var taraz = {!! json_encode($taraz) !!};
                var lessons = {!! json_encode($lessons) !!};
                var avgs = {!! json_encode($avgs) !!};


                percents = [];
                for(i = 0; i < taraz.length; i++)
                    percents[i] = Math.round(taraz[i].percent);

                for(i = 0; i < lessons.length; i++)
                    lessons[i] = lessons[i].name;

                maxPercents = [];
                for(i = 0; i < avgs.length; i++) {
                    maxPercents[i] = Math.round(avgs[i].maxPercent);
                    avgs[i] = Math.round(avgs[i].avg);
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