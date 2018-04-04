@extends('layouts.form')

@section('head')
    @parent
    
    <script>

        var getRankingDir = '{{route('getOnlineStanding')}}';
        var mode = "{{$mode}}";

        if(mode == "normal") {
            var total_time = "{{$reminder}}";
            var c_minutes = Math.floor(total_time / 60);
            var c_seconds = parseInt(total_time % 60);
        }

        var answer = {!! json_encode($roqs) !!};
        var qIdx = 0;
        var questionArr = {!! json_encode($questions) !!};
        var quizId = "{{$quiz->id}}";
        var submitAns = '{{route('submitAnsSystemQuiz')}}';

        $(document).ready(function () {
            SUQ();
            if(mode == "normal") {
                if (total_time > 0)
                    setTimeout("checkTime()", 1);
                else
                    goToProfile();
            }
        });

        function checkTime() {
            document.getElementById("quiz_time").innerHTML = "زمان باقی مانده : " + c_seconds + " : " + c_minutes;
            if (total_time <= 0)
                setTimeout("goToProfile()", 1);
            else {
                total_time--;
                c_minutes = Math.floor(total_time / 60);
                c_seconds = parseInt(total_time % 60);
                setTimeout("checkTime()", 1000);
            }
        }

        function submitC(val) {

            if(mode == "special")
                return;

            $.ajax({
                type: 'post',
                url: submitAns,
                data: {
                    'questionId': questionArr[qIdx].id,
                    'quizId': quizId,
                    'newVal': val
                },
                success: function (response) {
                    if(response == "correct") {
                        answer[qIdx].result = val;
                        answer[qIdx].status = 1;
                        SUQ();
                    }
                    else if(response == "incorrect") {
                        answer[qIdx].result = val;
                        answer[qIdx].status = 0;
                        SUQ();
                    }
                    else if(response == "noAccess") {
                        if(questionArr[qIdx].kindQ == 1)
                            $("#choices").val(answer[qIdx].result);
                        else
                            $("#yourAns").val(answer[qIdx].result);
                        $("#subErr").empty();
                        $("#subErr").append("شما قبلا به این سوال پاسخ داده اید");
                    }
                    else {
                        if(questionArr[qIdx].kindQ == 1)
                            $("#choices").val(answer[qIdx].result);
                        else
                            $("#yourAns").val(answer[qIdx].result);
                        $("#subErr").empty();
                        $("#subErr").append("مشکلی در انجام عملیات مورد نظر رخ داده است");
                    }

                },
                error: function (response) {
                    alert('Something went wrong' + response.responseText);
                }
            });
        }

        function incQ() {
            if(qIdx + 1 < questionArr.length) {
                qIdx++;
                SUQ();
            }
        }

        function decQ() {
            if(qIdx - 1 >= 0) {
                qIdx--;
                SUQ();
            }
        }

        function JMPTOQUIZ(idx) {
            qIdx = idx;
            SUQ();
        }

        function SUQ() {

            for(i = 0; i < answer.length; i++) {
                if(answer[i].result == 0)
                    document.getElementById("td_" + i).style.backgroundColor = "white";
                else
                    document.getElementById("td_" + i).style.backgroundColor = "gray";
            }

            document.getElementById("td_" + qIdx).style.backgroundColor = "yellow";

            if(qIdx == 0)
                $("#backQ").attr('disabled', 'disabled');
            else
                $("#backQ").removeAttr('disabled');

            if(qIdx == questionArr.length - 1)
                $("#nxtQ").attr('disabled', 'disabled');
            else
                $("#nxtQ").removeAttr('disabled');

            var newNode = "<span><img alt='در حال بارگذاری تصویر' style='max-width: 100%' src='{{URL::asset('images/questions/system')}}/" + questionArr[qIdx].questionFile + "'><br/>";
            $("#BQ").empty().append(newNode);

            newNode = "<center>امتیاز سوال: <span>" + questionArr[qIdx].mark + "</span></center>";

            if(questionArr[qIdx].kindQ == "1") {

                newNode += "<center style='margin-top: 20px;'><span style='font-size: 20px; color: #ff0000'>پاسخ : </span><select style='width: 60px; font-size: 14px' id='choices'>";

                if (answer[qIdx].result == 0)
                    newNode = newNode + "<option value='0' selected>سفید</option>";
                else
                    newNode = newNode + "<option value='0'>سفید</option>";

                for(i = 1 ; i <= questionArr[qIdx].choicesCount; i++) {
                    if (answer[qIdx].result == i)
                        newNode = newNode + "<option value='" + i + "' selected>" + i + "</option>";
                    else
                        newNode = newNode + "<option value='" + i + "'>" + i + "</option>";
                }

                newNode += "</select>";
                if(answer[qIdx].status == 0 && answer[qIdx].result != 0)
                    newNode += "<span id='ansStatus'><i class='fa fa-remove' style='margin-right: 5px; color: red' aria-hidden='true'></i></span>";
                else if(answer[qIdx].result != 0)
                    newNode += "<span id='ansStatus'><i class='fa fa-check' style='margin-right: 5px; color: darkgreen' aria-hidden='true'></i></span>";

                newNode += "</center>";
                newNode += "<center style='margin-top: 10px'><button onclick='submitC($(\"#choices\").val())' class='btn btn-primary'>ثبت</button></center>";
            }
            else {
                newNode += "<center style='margin-top: 20px'><label for='yourAns'>پاسخ شما:</label><input style='max-width: 100px' id='yourAns' type='text' value='" + answer[qIdx].result + "'>";

                if(answer[qIdx].status == 0 && answer[qIdx].result != "")
                    newNode += "<span id='ansStatus'><i class='fa fa-remove' style='margin-right: 5px; color: red' aria-hidden='true'></i></span>";
                else if(answer[qIdx].result != "")
                    newNode += "<span id='ansStatus'><i class='fa fa-check' style='margin-right: 5px; color: darkgreen' aria-hidden='true'></i></span>";

                newNode += "</center>";
                newNode += "<center style='margin-top: 10px'><button onclick='submitC($(\"#yourAns\").val())' class='btn btn-primary'>ثبت</button></center>";
            }

            newNode += "<center><p>تذکر: دقت داشته باشید که تنها یکبار می توانید جواب خود را ارسال کنید و سوالات نمره ی منفی دارند</p></center>";
            newNode += "<center><p id='subErr' class='errorText'></p></center>";

            $("#BQ").append(newNode);
        }
    </script>
@stop

<?php
$numQ = count($questions);
if ($questions == null || $numQ == 0) {
    echo "<div style='margin-top: 140px;'><center>سوالی در این آزمون وجود ندارد</center></div>";
    return;
}
?>

@section('main')

    <style>
        td {
            padding: 10px;
        }
    </style>

    <div id="popUpMenu2" style="margin-top: 300px;" hidden>
        <center id="percent"></center>
        <center style='margin-top: 20px;'><a href="{{URL(route('profile'))}}"><input type='submit' value='تایید'></a></center>
    </div>

    <div class="row" id="reminder" style="margin-top: 50px">

        <div class="row" id="reminder">
            <div class="col-xs-12">
                <center style="margin-top: 20px">
                    <div id="quiz_time" style='font-size: 14px;'></div>
                </center>
            </div>
        </div>
        <div class="col-xs-12">
            <center style="margin-top: 20px;">
                <table style="min-width: 100px;">
                    <?php
                    $counter = 0;
                    for($i = 0; $i < $numQ; $i++) {
                        if($counter == 0)
                            echo "<tr>";
                        $counter++;
                        echo "<td id='td_$i' onclick='JMPTOQUIZ($i)' style='cursor: pointer; background-color: white; width: 30px; border: 2px solid black;'><center>".($i + 1)."</center></td>";
                        if($counter == 15 || $i == $numQ - 1) {
                            echo "</tr>";
                            $counter = 0;
                        }
                    }
                    ?>
                </table>
            </center>
        </div>

        <div class="col-xs-12" style="float: right; margin-top: 10px;">
            <div class="col-xs-12 col-md-4" style='float: right; margin-top: 5px;'>
                <div style="width: 30px; height: 30px; margin-right: 5px; background-color: yellow; border: 1px solid black; float: right;"></div>
                <span style='margin-right: 5px;'>در حال پاسخ گویی</span>
            </div>
            <div class="col-xs-12 col-md-4" style='float: right; margin-top: 5px;'>
                <div style="width: 30px; height: 30px; margin-right: 5px; background-color:gray; border: 1px solid black; float: right;"></div>
                <span style='margin-right: 5px;'>پاسخ داده شده</span>
            </div>
            <div class="col-xs-12 col-md-4" style='float: right; margin-top: 5px;'>
                <div style="width: 30px; height: 30px; margin-right: 5px; background-color: white; border: 1px solid black; float: right"></div>
                <span style='margin-right: 5px;'>هنوز پاسخ داده نشده</span>
            </div>
        </div>

        <div class='col-xs-12 well well-sm' style="margin-top: 10px; border: 3px solid black; background-color: #ffffff">
            <div id="BQ" style='height: auto; width: auto; max-width: 100%'></div>
        </div>
        <div class="col-xs-12">
            <div style='margin-top: 5px;'>
                <center>
                    <button id="nxtQ" class="btn btn-default" onclick="incQ()">سوال بعدی</button>
                    <button id="backQ" class="btn btn-default" onclick="decQ()">سوال قبلی</button>
                    <button class="btn btn-primary" onclick="showConfirmationPaneEnd()">اتمام ارزیابی</button>
                    <button class="btn btn-danger" onclick="showConfirmationPane()">بازگشت به صفحه ی ورود به آزمون ها</button>
                    <button class="btn btn-danger" onclick="showRanking()">نمایش رتبه بندی آزمون</button>
                </center>
            </div>
        </div>
    </div>

    <span id="confirmationPane" class="ui_overlay item hidden" style="position: fixed; left: 30%; right: 30%; top: 170px; bottom: auto">
        <div class="header_text">بازگشت</div>
        <div onclick="hideElement()" class="ui_close_x"></div>
        <div class="body_text">
            <div class="col-xs-12" style="margin-top: 10px">
                آیا از بازگشت به مرحله ی قبل اطمینان دارید؟
            </div>
            <div class="col-xs-12" style="margin-top: 10px">
                <center>
                    <button onclick="goToQuizEntry()" class="btn btn-primary">
بله
                    </button>
                    <button onclick="hideElement()" class="btn btn-danger">خیر</button>
                    <p style="margin-top: 5px" class="errorText" id="errMsgConfirm"></p>
                </center>
            </div>
        </div>
    </span>

    <span id="confirmationPaneEnd" class="ui_overlay item hidden" style="position: fixed; left: 30%; right: 30%; top: 170px; bottom: auto">
        <div class="header_text">خروج</div>
        <div onclick="hideElement()" class="ui_close_x"></div>
        <div class="body_text">
            <div class="col-xs-12" style="margin-top: 10px">
                آیا از اتمام آزمون اطمینان دارید؟
            </div>
            <div class="col-xs-12" style="margin-top: 10px">
                <center>
                    <button onclick="goToProfile()" class="btn btn-primary">
بله
                    </button>
                    <button onclick="hideElement()" class="btn btn-danger">خیر</button>
                    <p style="margin-top: 5px" class="errorText" id="errMsgConfirm"></p>
                </center>
            </div>
        </div>
    </span>

    <span id="rankingPane" class="ui_overlay item hidden" style="position: fixed; left: 10%; right: 10%; width: auto; top: 70px; bottom: auto">
        <div class="header_text">رتبه بندی</div>
        <div onclick="hideElement()" class="ui_close_x"></div>
        <div class="body_text">
            <div id="ranking"></div>
        </div>
    </span>

    <script>

        function goToProfile() {
            document.location.href = '{{route('profile')}}';
        }

        function goToQuizEntry() {
            document.location.href = '{{route('quizEntry')}}';
        }

        function showConfirmationPane() {
            hideElement();
            $("#confirmationPane").removeClass('hidden');
        }

        function showConfirmationPaneEnd() {
            hideElement();
            $("#confirmationPaneEnd").removeClass('hidden');
        }

        function showRanking() {

            hideElement();

            $.ajax({
                type: 'post',
                url: getRankingDir,
                data: {
                    'quizId': quizId
                },
                success: function (response) {
                    response = JSON.parse(response);

                    newElement = "<table style='max-height: 70vh; overflow: auto'><tr><td><center>رتبه بندی</center></td><td><center>نام کاربری</center></td>";
                    for(j = 1; j <= questionArr.length; j++) {
                        newElement += "<td><center>سوال " + j + "</center></td>";
                    }
                    newElement += "<td><center>امتیاز کلی</center></td>";
                    newElement += "</tr>";


                    for(i = 0; i < response.length; i++) {
                        newElement += "<tr><td><center>" + (i + 1) + "</center></td>";
                        newElement += "<td><center>" + response[i].name + "</center></td>";
                        sum = 0;
                        for(j = 0; j < questionArr.length; j++) {
                            newElement += "<td><center class='number'>" + response[i].roq[j] + "</center></td>";
                            sum += response[i].roq[j];
                        }
                        newElement += "<td><center class='number'>" + sum + "</center></td></tr>";
                    }

                    $("#ranking").empty().append(newElement);

                    $("#rankingPane").removeClass('hidden');
                }
            });

        }
        
        function hideElement() {
            $(".item").addClass('hidden');
        }

    </script>
@stop