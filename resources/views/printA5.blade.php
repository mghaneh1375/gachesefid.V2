<!DOCTYPE>
<html>
<head>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="{{URL::asset('css/commonCSS.css')}}">
    <style>

        td > center{
            padding: 5px;
            font-size: 10px;
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

    <div class="col-xs-12" style="overflow-x: auto">
        <table style="margin-top: 10px">
            <tr>
                <td><center>نام و نام خانوادگی</center></td>
                <td><center>شهر</center></td>
                <td><center>استان</center></td>
                <td><center>مدرسه</center></td>
                <?php $allow = false; ?>
                @if(count($users) > 0)
                    <?php $allow = (count($users[0]->lessons) == 1) ? false : true ?>
                    @foreach($users[0]->lessons as $itr)
                        <td><center>{{$itr->name}}</center></td>
                    @endforeach
                @endif

                @if($allow)
                    <td><center>میانگین</center></td>
                @endif
                <td><center>تراز کل</center></td>
                <td><center>رتبه در شهر/منطقه</center></td>
                <td><center>رتبه در استان</center></td>
                <td><center>رتبه در کشور</center></td>
            </tr>

            @foreach($users as $user)
                <?php $sumTaraz = 0; $sumLesson = 0; $sumCoherence = 0; ?>
                <tr style="cursor: pointer" onclick="document.location.href = '{{route('A3', ['quizId' => $quizId, 'uId' => $user->uId, 'backURL' => 'A5'])}}'">
                    <td><center>{{$user->name}}</center></td>
                    <td><center>{{$user->city}}</center></td>
                    <td><center>{{$user->state}}</center></td>
                    <td><center>{{$user->schoolName}}</center></td>
                    @foreach($user->lessons as $itr)
                        <?php
                        if($itr->coherence == 0) {
                            $sumTaraz += $itr->taraz;
                            $sumLesson += $itr->percent;
                            $sumCoherence += 1;
                        }
                        else {
                            $sumTaraz += $itr->taraz * $itr->coherence;
                            $sumLesson += $itr->percent * $itr->coherence;
                            $sumCoherence += $itr->coherence;
                        }
                        ?>
                        <td><center style="direction: ltr">{{$itr->percent}}</center></td>
                    @endforeach
                    @if($sumCoherence != 0)
                        @if($allow)
                            <td><center style="direction: ltr">{{round(($sumLesson / $sumCoherence), 0)}}</center></td>
                        @endif
                        <td><center style="direction: ltr">{{round(($sumTaraz / $sumCoherence), 0)}}</center></td>
                    @else
                        @if($allow)
                            <td><center style="direction: ltr">{{round(($sumLesson), 0)}}</center></td>
                        @endif
                        <td><center style="direction: ltr">{{round(($sumTaraz), 0)}}</center></td>
                    @endif
                    <td><center>{{$user->cityRank}}</center></td>
                    <td><center>{{$user->stateRank}}</center></td>
                    <td><center>{{$user->rank}}</center></td>
                </tr>
            @endforeach
        </table>
    </div>
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