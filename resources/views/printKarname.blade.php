<!DOCTYPE>
<html>
    <head>

        <script src="{{URL::asset('js/highcharts.js')}}"></script>
        <script src="{{URL::asset('js/exporting.js')}}"></script>

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <link rel="stylesheet" href="{{URL::asset('css/commonCSS.css')}}">
        <style>

            td > center{
                padding: 5px;
                font-size: 10px;
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
        <script src="{{URL::asset('js/persianumber.js')}}"></script>
        <link href="{{URL::asset('css/myFont.css')}}" rel="stylesheet" type="text/css">
        <script>
            $(document).ready(function () {
                $(document.body).persiaNumber();
            });

        </script>

        <style>
            .sk-circle {
                margin: 100px auto;
                width: 40px;
                height: 40px;
                position: relative;
            }
            .sk-circle .sk-child {
                width: 100%;
                height: 100%;
                position: absolute;
                left: 0;
                top: 0;
            }
            .sk-circle .sk-child:before {
                content: '';
                display: block;
                margin: 0 auto;
                width: 15%;
                height: 15%;
                background-color: #333;
                border-radius: 100%;
                -webkit-animation: sk-circleBounceDelay 1.2s infinite ease-in-out both;
                animation: sk-circleBounceDelay 1.2s infinite ease-in-out both;
            }
            .sk-circle .sk-circle2 {
                -webkit-transform: rotate(30deg);
                -ms-transform: rotate(30deg);
                transform: rotate(30deg); }
            .sk-circle .sk-circle3 {
                -webkit-transform: rotate(60deg);
                -ms-transform: rotate(60deg);
                transform: rotate(60deg); }
            .sk-circle .sk-circle4 {
                -webkit-transform: rotate(90deg);
                -ms-transform: rotate(90deg);
                transform: rotate(90deg); }
            .sk-circle .sk-circle5 {
                -webkit-transform: rotate(120deg);
                -ms-transform: rotate(120deg);
                transform: rotate(120deg); }
            .sk-circle .sk-circle6 {
                -webkit-transform: rotate(150deg);
                -ms-transform: rotate(150deg);
                transform: rotate(150deg); }
            .sk-circle .sk-circle7 {
                -webkit-transform: rotate(180deg);
                -ms-transform: rotate(180deg);
                transform: rotate(180deg); }
            .sk-circle .sk-circle8 {
                -webkit-transform: rotate(210deg);
                -ms-transform: rotate(210deg);
                transform: rotate(210deg); }
            .sk-circle .sk-circle9 {
                -webkit-transform: rotate(240deg);
                -ms-transform: rotate(240deg);
                transform: rotate(240deg); }
            .sk-circle .sk-circle10 {
                -webkit-transform: rotate(270deg);
                -ms-transform: rotate(270deg);
                transform: rotate(270deg); }
            .sk-circle .sk-circle11 {
                -webkit-transform: rotate(300deg);
                -ms-transform: rotate(300deg);
                transform: rotate(300deg); }
            .sk-circle .sk-circle12 {
                -webkit-transform: rotate(330deg);
                -ms-transform: rotate(330deg);
                transform: rotate(330deg); }
            .sk-circle .sk-circle2:before {
                -webkit-animation-delay: -1.1s;
                animation-delay: -1.1s; }
            .sk-circle .sk-circle3:before {
                -webkit-animation-delay: -1s;
                animation-delay: -1s; }
            .sk-circle .sk-circle4:before {
                -webkit-animation-delay: -0.9s;
                animation-delay: -0.9s; }
            .sk-circle .sk-circle5:before {
                -webkit-animation-delay: -0.8s;
                animation-delay: -0.8s; }
            .sk-circle .sk-circle6:before {
                -webkit-animation-delay: -0.7s;
                animation-delay: -0.7s; }
            .sk-circle .sk-circle7:before {
                -webkit-animation-delay: -0.6s;
                animation-delay: -0.6s; }
            .sk-circle .sk-circle8:before {
                -webkit-animation-delay: -0.5s;
                animation-delay: -0.5s; }
            .sk-circle .sk-circle9:before {
                -webkit-animation-delay: -0.4s;
                animation-delay: -0.4s; }
            .sk-circle .sk-circle10:before {
                -webkit-animation-delay: -0.3s;
                animation-delay: -0.3s; }
            .sk-circle .sk-circle11:before {
                -webkit-animation-delay: -0.2s;
                animation-delay: -0.2s; }
            .sk-circle .sk-circle12:before {
                -webkit-animation-delay: -0.1s;
                animation-delay: -0.1s; }

            @-webkit-keyframes sk-circleBounceDelay {
                0%, 80%, 100% {
                    -webkit-transform: scale(0);
                    transform: scale(0);
                } 40% {
                      -webkit-transform: scale(1);
                      transform: scale(1);
                  }
            }

            @keyframes sk-circleBounceDelay {
                0%, 80%, 100% {
                    -webkit-transform: scale(0);
                    transform: scale(0);
                } 40% {
                      -webkit-transform: scale(1);
                      transform: scale(1);
                  }
            }
        </style>

    </head>

    <body onload="setTimeout('printPage()', 1000)" style="font-family: IRANSans; direction: rtl">

        <center style="margin-top: 20px; font-size: 24px; color: #963019">{{$name}}</center>

        <div class="sk-circle">
            <div class="sk-circle1 sk-child"></div>
            <div class="sk-circle2 sk-child"></div>
            <div class="sk-circle3 sk-child"></div>
            <div class="sk-circle4 sk-child"></div>
            <div class="sk-circle5 sk-child"></div>
            <div class="sk-circle6 sk-child"></div>
            <div class="sk-circle7 sk-child"></div>
            <div class="sk-circle8 sk-child"></div>
            <div class="sk-circle9 sk-child"></div>
            <div class="sk-circle10 sk-child"></div>
            <div class="sk-circle11 sk-child"></div>
            <div class="sk-circle12 sk-child"></div>
        </div>

        <center style="margin-top: 10px" class="row">
            <div style="overflow-x: auto; height: 30vh" class="col-xs-12">
                <div id="barChart1" style="height: 200px; direction: ltr" class="col-xs-3"></div>

                <div class="col-xs-8">
                    <table style="margin-top: 10px; border-bottom: 2px solid #656565; margin-bottom: 10px; float: right">
                        <tr>
                            <td><center>درس</center></td>
                            <td><center>ضریب</center></td>
                            <td><center>کل</center></td>
                            <td><center>درست</center></td>
                            <td><center>نادرست</center></td>
                            <td><center>نزده</center></td>
                            <td><center><span>نمره از </span><span>{{$totalMark}}</span></center></td>
                            <td><center>درصد</center></td>
                            <td><center>میانگین درصد</center></td>
                            <td><center>بیشترین درصد</center></td>
                            <td><center>کمترین درصد</center></td>
                        </tr>

                        <?php
                        $i = 0;
                        $sum = 0;
                        $total = 0;
                        ?>

                        @foreach($lessons as $lesson)

                            <tr>
                                <td><center>{{$lesson->name}}</center></td>
                                <td><center>{{$lesson->coherence}}</center></td>
                                <td><center>{{$roq[2][$i]}}</center></td>
                                <td><center>{{$roq[1][$i]}}</center></td>
                                <td><center>{{$roq[0][$i]}}</center></td>
                                <td><center>{{$roq[2][$i] - $roq[0][$i] - $roq[1][$i]}}</center></td>

                                <td><center style="direction: ltr">{{($taraz[$i]->percent <= 0) ? 0 : round($taraz[$i]->percent * $totalMark / 100, 0)}}</center></td>
                                <td><center style="direction: ltr">{{round($taraz[$i]->percent, 0)}}</center></td>
                                <td><center style="direction: ltr">{{round($avgs[$i]->avg, 0)}}</center></td>
                                <td><center style="direction: ltr">{{round($avgs[$i]->maxPercent, 0)}}</center></td>
                                <td><center style="direction: ltr">{{round($avgs[$i]->minPercent, 0)}}</center></td>
                            </tr>
                            <?php
                            $sum += $taraz[$i]->taraz * $lesson->coherence;
                            $total += $lesson->coherence;
                            $i++;
                            ?>
                        @endforeach
                    </table>
                    <?php
                    $i = 0;
                    ?>

                    <div class="col-xs-12" >
                        <table style="margin-top: 30px; float: right">
                        <tr>

                            <td><center>درس</center></td>

                            <td><center>رتبه در کشور</center></td>

                            <td><center>رتبه در استان</center></td>

                            <td><center>رتبه در شهر</center></td>

                            <td><center>تراز</center></td>

                            <td><center>وضعیت</center></td>
                        </tr>

                        @foreach($lessons as $lesson)

                            <tr>

                                <td><center>{{$lesson->name}}</center></td>

                                <td><center>{{$rankInLesson[$i]}}</center></td>

                                <td><center>{{$rankInLessonState[$i]}}</center></td>

                                <td><center>{{$rankInLessonCity[$i]}}</center></td>

                                <td><center style="direction: ltr">{{$taraz[$i]->taraz}}</center></td>
                                <td>
                                    <center>
                                        @foreach($status as $itr)
                                            @if(($itr->type && $itr->floor <= $taraz[$i]->percent &&
                                             $taraz[$i]->percent <= $itr->ceil) || (!$itr->type &&
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
                            </tr>
                            <?php $i++; ?>
                        @endforeach
                    </table>
                    </div>
                </div>
            </div>
            <div class="col-xs-12">
                <?php
                    $i = 1;
                    $size = count($qInfos);
                ?>

                @foreach($qInfos as $qInfo)
                    @if(($i - 1) % 15 == 0)
                        <div style="width: 30%; float: right; position: relative; display: block; height: {{(($i > 45) ? '100vh' : '')}}">
                            <table style="margin-top: 10px">
                                <tr>
                                    <td><center>سوال</center></td>
                                    <td><center>پاسخ داوطلب</center></td>
                                    <td><center>کلید</center></td>
                                    <td><center>دشواری</center></td>
                                    <td><center>مبحث</center></td>
                                </tr>
                    @endif

                                <tr>
                                    <td><center>{{$i}}</center></td>
                                    <td><center style="direction: ltr">{{$qInfo->result}}</center></td>
                                    <td><center>{{$qInfo->ans}}</center></td>
                                    <td><center style="direction: ltr">{{$qInfo->level}}</center></td>
                                    <td>
                                        <center>
                                            @foreach($qInfo->subjects as $itr)
                                                <span>{{$itr}}</span><span>&nbsp;</span>
                                            @endforeach
                                        </center>
                                    </td>
                                </tr>

                    @if(($i - 1) % 15 == 14 || $i == $size)
                            </table>
                        </div>
                    @endif
                    <?php
                    $i++;
                    ?>
                @endforeach
            </div>


            <script type="text/javascript">

                var taraz = {{json_encode($taraz)}};
                var lessons = {{json_encode($lessons)}};
                var avgs = {{json_encode($avgs)}};


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
        </center>

        <script>
            function printPage() {
                var css = '@page { size: landscape; }',
                        head = document.head || document.getElementsByTagName('head')[0],
                        style = document.createElement('style');

                style.type = 'text/css';
                style.media = 'print';

                if (style.styleSheet){
                    style.styleSheet.cssText = css;
                } else {
                    style.appendChild(document.createTextNode(css));
                }

                head.appendChild(style);
                $(".sk-circle").css('display', 'none');
                window.print();
            }
        </script>
    </body>
</html>