@extends('layouts.form')

@section('head')
    @parent
@stop

@section('caption')

    <style>
        td {
            padding: 10px;
            min-width: 100px;
        }
    </style>

    <script>

        var users = {!! json_encode($users) !!};
        var quizName = "{{$quizName}}";

    </script>
    <div class="title">
        <p>آزمون {{$quizName}}</p>
        <p style="margin-top: -30px; font-size: 24px">(تا این لحظه)</p>
    </div>
@stop

@section('main')
    <center style="margin-top: 10px">

        <div style="overflow-x: auto">
            <table style="margin-top: 10px">
                <tr>
                    <td><center>نام و نام خانوادگی</center></td>
                    <td><center>شهر</center></td>
                    <td><center>نام مدرسه</center></td>
                    <td><center>تراز کل</center></td>
                    <td><center>رتبه در شهر</center></td>
                    <td><center>رتبه در استان</center></td>
                    <td><center>رتبه در کشور</center></td>
                    <td><center>کارنامه</center></td>
                </tr>

                <?php $counter = 0; ?>

                @foreach($users as $user)
                    <?php $sumTaraz = 0; $sumLesson = 0; $sumCoherence = 0; ?>
                    <tr>
                        <td><center>{{$user->name}}</center></td>
                        <td><center>{{$user->city}}</center></td>
                        <td><center>{{(empty($user->schoolName)) ? '...' : $user->schoolName}}</center></td>
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
                        @endforeach
                        @if($sumCoherence != 0)
                            <td><center style="direction: ltr">{{round(($sumTaraz / $sumCoherence), 0)}}</center></td>
                        @else
                            <td><center style="direction: ltr">{{round(($sumTaraz), 0)}}</center></td>
                        @endif
                        <td><center>{{$user->cityRank}}</center></td>
                        <td><center>{{$user->stateRank}}</center></td>
                        <td><center>{{$user->rank}}</center></td>

                        @if($sumCoherence != 0)
                            <td style="cursor:pointer" class="userRank" data-taraz="{{round(($sumTaraz / $sumCoherence), 0)}}" data-id="{{$counter}}"><center><span class="glyphicon glyphicon-list-alt"></span></center></td>
                        @else
                            <td style="cursor:pointer" class="userRank" data-taraz="0" data-id="{{$counter}}"><center><span class="glyphicon glyphicon-list-alt"></span></center></td>
                        @endif
                        <?php $counter++; ?>
                    </tr>
                @endforeach
            </table>
        </div>
    </center>

    <script>

        $(".userRank").click(function () {
            showWithDetail($(this).attr('data-id'), $(this).attr('data-taraz'));
        });

        function showWithDetail(idx, taraz) {

            $(".dark").removeClass('hidden');

            lessons = users[idx].lessons;
            newElement = "<table style='width: 100%'><tr style='background-color: #ccc'><td>" + users[idx].name + "</td><td>" + quizName + "</td></tr></table>";

            newElement += "<table style='width: 100%; margin-top: 10px'>";
            newElement += "<tr style='background-color: #ccc;'>";
            newElement += "<td><center>تراز</center></td>";
            newElement += "<td><center>رتبه در کشور</center></td>";
            newElement += "<td><center>رتبه در استان</center></td>";
            newElement += "<td><center>رتبه در شهر</center></td>";
            newElement += "</tr>";

            newElement += "<tr>";
            newElement += "<td><center>" + taraz + "</center></td>";
            newElement += "<td><center>" + users[idx].rank + "</center></td>";
            newElement += "<td><center>" + users[idx].stateRank + "</center></td>";
            newElement += "<td><center>" + users[idx].cityRank + "</center></td>";
            newElement += "</tr>";

            newElement += "</table>";
            newElement += "<table style='font-size: 12px; width: 100%'>";
            for (i = 0; i < lessons.length; i++) {
                newElement += "<tr>";
                newElement += '<td style="width: 50%">' + lessons[i].name + '</td>';
                newElement += '<td style="width: 25%"><center style=" direction: ltr">' + lessons[i].percent + '</center></td>';
                newElement += '<td style="width: 25%"><center>' + lessons[i].taraz + '</center></td>';
                newElement += "</tr>";
            }

            newElement += "</table>";

            $('.body_text').empty().append(newElement).persiaNumber();
            $("#stdInfo").removeClass('hidden');
        }
    </script>
@stop


<span id="stdInfo" class="ui_overlay item hidden" style="position: fixed; width: 40%; left: 30%; right: auto; top: 100px; bottom: auto">
    <div style="color: #963019" class="header_text">کارنامه آزمون</div>
    <div onclick="$('#stdInfo').addClass('hidden'); $('.dark').addClass('hidden')" class="ui_close_x"></div>
    <div class="body_text" style="margin-top: 10px; max-height: 400px; overflow: auto"></div>
</span>