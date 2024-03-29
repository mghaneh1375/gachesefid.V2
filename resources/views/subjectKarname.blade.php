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

        .highcharts-tooltip-box {
            fill: #6e6e6e;
            stroke-width: 0;
        }
    </style>

@stop


@section('caption')
    <div class="title">مشاهده کارنامه مبحثی
    </div>
@stop

@section('main')
    <center style="margin-top: 10px">
        <a href="{{route('seeResult', ['quizId' => $quizId])}}"><button class="btn btn-primary">بازگشت به مرحله قبل</button></a>
        <div class="line"></div>

        <div style="overflow-x: auto">

            <?php $i = 0; $allow = false; ?>
            @foreach($subjects as $subject)
                @if($roq[$i++][2] != 0)
                    <?php $allow = true; ?>
                @endif
            @endforeach

            @if($allow)
                <h3>کارنامه سوالات تستی</h3>

                <table style="margin-top: 10px; font-size: 11px">
                    <tr>
                        <td><center>نام مبحث</center></td>
                        <td><center>تعداد کل سوالات</center></td>
                        <td><center>درست</center></td>
                        <td><center>نادرست</center></td>
                        <td><center>نزده</center></td>
                        @if($kindKarname->subjectMark)
                            <td><center><span>نمره از </span><span>{{$totalMark}}</span></center></td>
                        @endif
                        <td><center>درصد پاسخ گویی</center></td>
                        @if($kindKarname->subjectAvg)
                            <td><center>میانگین درصد پاسخ گویی</center></td>
                        @endif
                        @if($kindKarname->subjectMinPercent)
                            <td><center>کمترین درصد پاسخ گویی</center></td>
                        @endif
                        @if($kindKarname->subjectMaxPercent)
                            <td><center>بیشترین درصد پاسخ گویی</center></td>
                        @endif

                    </tr>

                    <?php
                    $i = 0;
                    ?>

                    @foreach($subjects as $subject)

                        @if($roq[$i][2] != 0)

                            <?php
                                $minPercent = round($avgs[$i]->minPercent * 100 / $roq[$i][2], 2);
                                $maxPercent = round($avgs[$i]->maxPercent * 100 / $roq[$i][2], 2);
                                $avgPercent = round($avgs[$i]->avg * 100 / $roq[$i][2], 2);
                            ?>

                            <tr>
                                <td><center>{{$subject->name}}</center></td>
                                <td><center>{{$roq[$i][2]}}</center></td>
                                <td><center>{{$roq[$i][1]}}</center></td>
                                <td><center>{{$roq[$i][0]}}</center></td>
                                <td><center>{{$roq[$i][2] - $roq[$i][1] - $roq[$i][0]}}</center></td>

                                @if($kindKarname->subjectMark)
                                    <td><center style="direction: ltr">{{$subjectPercents[$i]->percent}}</center></td>
                                @endif
                                <td><center style="direction: ltr">{{$subjectPercents[$i]->percent}}</center></td>
                                @if($kindKarname->subjectAvg)
                                    <td><center style="direction: ltr">{{$avgPercent}}</center></td>
                                @endif
                                @if($kindKarname->subjectMinPercent)
                                    <td><center style="direction: ltr">{{$minPercent}}</center></td>
                                @endif
                                @if($kindKarname->subjectMaxPercent)
                                    <td><center style="direction: ltr">{{$maxPercent}}</center></td>
                                @endif
                            </tr>
                            <?php
                            $i++;
                            ?>
                        @endif
                    @endforeach
                </table>
            @endif

            <?php $i = 0; $allow = false; ?>
            @foreach($subjects as $subject)
                @if($roq2[$i++][2] != 0)
                    <?php $allow = true; ?>
                @endif
            @endforeach

            @if($allow)
                <h3>کارنامه سوالات کوتاه پاسخ</h3>

                <table style="margin-top: 10px; font-size: 11px">
                    <tr>
                        <td><center>نام مبحث</center></td>
                        <td><center>تعداد کل سوالات</center></td>
                        <td><center>درست</center></td>
                        <td><center>نادرست</center></td>
                        <td><center>نزده</center></td>
                        <td><center>نمره</center></td>
                        <td><center>میانگین نمره</center></td>
                        <td><center>کمترین نمره</center></td>
                        <td><center>بیشترین نمره</center></td>
                    </tr>

                    <?php
                    $i = 0;
                    ?>

                    @foreach($subjects as $subject)

                        @if($roq2[$i][2] != 0)

                            <tr>
                                <td><center>{{$subject->name}}</center></td>
                                <td><center>{{$roq2[$i][2]}}</center></td>
                                <td><center>{{$roq2[$i][1]}}</center></td>
                                <td><center>{{$roq2[$i][0]}}</center></td>
                                <td><center>{{$roq2[$i][2] - $roq2[$i][1] - $roq2[$i][0]}}</center></td>

                                <td><center style="direction: ltr">{{$subjectPercents[$i]->percent2}}</center></td>
                                <td><center style="direction: ltr">{{round($avgs[$i]->avg2, 2)}}</center></td>
                                <td><center style="direction: ltr">{{$avgs[$i]->minPercent2}}</center></td>
                                <td><center style="direction: ltr">{{$avgs[$i]->maxPercent2}}</center></td>
                            </tr>
                        @endif

                        <?php
                        $i++;
                        ?>
                    @endforeach
                </table>
            @endif

            <?php $i = 0; $allow = false; ?>
            @foreach($subjects as $subject)
                @if($roq3[$i++][2] != 0)
                    <?php $allow = true; ?>
                @endif
            @endforeach

            @if($allow)
                <h3>کارنامه سوالات چند گزاره ای</h3>

                <table style="margin-top: 10px; font-size: 11px">
                    <tr>
                        <td><center>نام مبحث</center></td>
                        <td><center>تعداد کل سوالات</center></td>
                        <td><center>درست</center></td>
                        <td><center>نادرست</center></td>
                        <td><center>نزده</center></td>
                        <td><center>نمره</center></td>
                        <td><center>میانگین نمره</center></td>
                        <td><center>کمترین نمره</center></td>
                        <td><center>بیشترین نمره</center></td>
                    </tr>

                    <?php
                    $i = 0;
                    ?>

                    @foreach($subjects as $subject)

                        @if($roq3[$i][2] != 0)

                            <tr>
                                <td><center>{{$subject->name}}</center></td>
                                <td><center>{{$roq3[$i][2]}}</center></td>
                                <td><center>{{$roq3[$i][1]}}</center></td>
                                <td><center>{{$roq3[$i][0]}}</center></td>
                                <td><center>{{$roq3[$i][2] - $roq3[$i][1] - $roq3[$i][0]}}</center></td>

                                <td><center style="direction: ltr">{{$subjectPercents[$i]->percent3}}</center></td>
                                <td><center style="direction: ltr">{{round($avgs[$i]->avg3, 2)}}</center></td>
                                <td><center style="direction: ltr">{{$avgs[$i]->minPercent3}}</center></td>
                                <td><center style="direction: ltr">{{$avgs[$i]->maxPercent3}}</center></td>
                            </tr>
                        @endif

                        <?php
                        $i++;
                        ?>
                    @endforeach
                </table>
            @endif

            <?php $percents = []; $calcAvg = []; $i = 0; ?>

            <table>

                <td><center>نام مبحث</center></td>

                @if($kindKarname->subjectCityRank)
                    <td><center>رتبه در شهر/منطقه</center></td>
                @endif
                @if($kindKarname->subjectStateRank)
                    <td><center>رتبه در استان</center></td>
                @endif
                @if($kindKarname->subjectCountryRank)
                    <td><center>رتبه در کشور</center></td>
                @endif
                {{--@if(count($status) > 0)--}}
                {{--<td><center>وضعیت</center></td>--}}
                {{--@endif--}}

                @foreach($subjects as $subject)

                    <?php
                        $percents[$i] = ($subjectPercents[$i]->percent + $subjectPercents[$i]->percent2 + $subjectPercents[$i]->percent3);
                        $calcAvg[$i] = ($avgs[$i]->avg + $avgs[$i]->avg2 + $avgs[$i]->avg3);
                    ?>


                    <tr>
                        <td><center>{{$subject->name}}</center></td>

                        @if($kindKarname->subjectCityRank)
                            <td><center>{{$cityRank[$i]}}</center></td>
                        @endif
                        @if($kindKarname->subjectStateRank)
                            <td><center>{{$stateRank[$i]}}</center></td>
                        @endif
                        @if($kindKarname->subjectCountryRank)
                            <td><center>{{$countryRank[$i]}}</center></td>
                        @endif
                        {{--@if(count($status) > 0)--}}
                        {{--<td>--}}
                        {{--<center>--}}
                        {{--@foreach($status as $itr)--}}
                        {{--@if($itr->type && $itr->floor <= $percent[$i] && $percent[$i] <= $itr->ceil--}}
                        {{--|| (!$itr->type && $kindKarname->subjectAvg &&--}}
                        {{--$avgs[$i]->avg - $itr->floor <= $percent[$i] && $avgs[$i]->avg + $itr->ceil >= $percent[$i]))--}}
                        {{--@if($itr->pic)--}}
                        {{--<img width="40px" height="40px" src="{{URL('public/status') . '/' . $itr->status}}">--}}
                        {{--@else--}}
                        {{--<p style="background-color: {{$itr->color}}">{{$itr->status}}</p>--}}
                        {{--@endif--}}
                        {{--@endif--}}
                        {{--@endforeach--}}
                        {{--</center>--}}
                        {{--</td>--}}
                        {{--@endif--}}

                    </tr>

                    <?php $i++; ?>

                    @endforeach
            </table>

        </div>


        @if($kindKarname->subjectBarChart)
            <div id="barChart1" style="min-width: 310px; height: 400px; margin-top: 10px; direction: ltr"></div>

            <script type="text/javascript">

                var percents = {!! json_encode($percents) !!};
                var subjects = {!! json_encode($subjects) !!};
                var avgs = {!! json_encode($calcAvg) !!};

                var min = 0;

                for(i = 0; i < subjects.length; i++) {
                    subjects[i] = subjects[i].name;

                    if(avgs[i] < min)
                        min = avgs[i];
                    if(percents[i] < min)
                        min = percents[i];
                }

                Highcharts.chart('barChart1', {

                    chart: {
                        polar: true,
                        type: 'line'
                    },

                    title: {
                        text: 'نمودار مقایسه ای مباحث',
                        x: -80
                    },

                    pane: {
                        size: '90%'
                    },

                    xAxis: {
                        categories: subjects,
                        tickmarkPlacement: 'on',
                        lineWidth: 0
                    },

                    yAxis: {
                        gridLineInterpolation: 'polygon',
                        lineWidth: 0,
                        min: min
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