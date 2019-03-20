@extends('layouts.form')

@section('head')
    @parent

    <script>

        var myArr = ['اول', 'دوم', 'سوم', 'چهارم', 'پنجم', 'ششم', 'هفتم', 'هشتم', 'نهم'];
        var likeQuestionDir = '{{route('likeQuestion')}}';
        var answer = {!! json_encode($roqs) !!};
        var qIdx = 0;
        var questionArr = {!! json_encode($questions) !!};
        var quizId = "{{$quiz->id}}";
        var getRankingDir = '{{route('getOnlineStanding')}}';

        $(document).ready(function () {
            SUQ();
        });

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

            var newNode = "<span><img alt='در حال بارگذاری تصویر' style='max-width: 100%' src='{{URL::asset('images/questions/system')}}/" + questionArr[qIdx].questionFile + "'></span><br/>";

            if(questionArr[qIdx].kindQ == "1") {
                newNode += "<center style='margin-top: 20px;'><span style='font-size: 20px; color: #ff0000'>پاسخ شما : </span><select disabled class='mySelect' style='width: 60px; font-size: 14px' id='choices'>";

                if (answer[qIdx].result == -1)
                    newNode = newNode + "<option selected value='0' selected>خطا</option>";

                if (answer[qIdx].result == 0)
                    newNode = newNode + "<option value='0' selected>سفید</option>";
                else
                    newNode = newNode + "<option value='0'>سفید</option>";
                if (answer[qIdx].result == 1)
                    newNode = newNode + "<option value='1' selected>گزینه 1</option>";
                else
                    newNode = newNode + "<option value='1'>گزینه 1</option>";
                if (answer[qIdx].result == 2)
                    newNode = newNode + "<option value='2' selected>گزینه 2</option>";
                else
                    newNode = newNode + "<option value='2'>گزینه 2</option>";
                if (answer[qIdx].result == 3)
                    newNode = newNode + "<option value='3' selected>گزینه 3</option>";
                else
                    newNode = newNode + "<option value='3'>گزینه 3</option>";
                if (answer[qIdx].result == 4)
                    newNode = newNode + "<option value='4' selected>گزینه 4</option>";
                else
                    newNode = newNode + "<option value='4'>گزینه 4</option>";

                if (answer[qIdx].result == 5)
                    newNode = newNode + "<option value='5' selected>گزینه 5</option>";
                else
                    newNode = newNode + "<option value='5'>گزینه 5</option>";

                newNode = newNode + "</select></center>";
            }
            else if(questionArr[qIdx].kindQ == "0") {
                newNode += "<center style='margin-top: 20px'><label for='yourAns'>پاسخ شما:</label><input readonly style='max-width: 100px' onchange='submitC(this.value)' type='text' value='" + answer[qIdx].result + "'></center>";
            }
            else {

                var tmpArr2 = [];
                answer[qIdx].result = answer[qIdx].result + "";

                for (i = 0; i < questionArr[qIdx].choicesCount; i++)
                    tmpArr2[i] = answer[qIdx].result[i];

                newNode = "<center style='margin-top: 20px'><p style='color: red;'>پاسخ شما:</p>";
                for (i = 0; i < questionArr[qIdx].choicesCount; i++) {
                    newNode += "<div><label for='sentence_" + i + "' style='margin: 10px'>گزاره " + myArr[i] + "</label>";
                    newNode += "<select style='width: 120px' id='sentence_" + i + "' readonly='true' '>";
                    if (tmpArr2[i] == 0) {
                        newNode += "<option value='0'>سفید</option>";
                        newNode += "<option value='1'>درست</option>";
                        newNode += "<option value='2'>نادرست</option>";
                    }
                    else if (tmpArr2[i] == 1) {
                        newNode += "<option value='1'>درست</option>";
                        newNode += "<option value='2'>نادرست</option>";
                        newNode += "<option value='0'>سفید</option>";
                    }
                    else if (tmpArr2[i] == 2) {
                        newNode += "<option value='2'>نادرست</option>";
                        newNode += "<option value='1'>درست</option>";
                        newNode += "<option value='0'>سفید</option>";
                    }
                    newNode += "</select></div>";
                }


                var qInfo = "";

                for (i = 0; i < questionArr[qIdx].choicesCount; i++) {
                    totalAns = (questionArr[qIdx].corrects[i] + questionArr[qIdx].inCorrects[i] + questionArr[qIdx].whites[i]);
                    qInfo += '<h4>گزاره ' + myArr[i] + '</h4>';
                    qInfo += '<p><span>تعداد پاسخ گویی:&nbsp;&nbsp;</span><span>' + totalAns + '</span></p>';
                    qInfo += '<p><span>تعداد جواب صحیح:&nbsp;&nbsp;</span><span>' + questionArr[qIdx].corrects[i] + '</span></p>';
                    qInfo += '<p><span>تعداد جواب ناصحیح:&nbsp;&nbsp;</span><span>' + questionArr[qIdx].inCorrects[i] + '</span></p>';
                    qInfo += '<p><span>تعداد جواب بدون پاسخ:&nbsp;&nbsp;</span><span>' + questionArr[qIdx].whites[i] + '</span></p>';
                    qInfo += '<p><span>درصد پاسخ گویی:&nbsp;&nbsp;</span><span>' + Math.round(questionArr[qIdx].corrects[i] * 100 / totalAns) + '</span></p>';
                }

                $("#infoForKind2").removeClass('hidden');
                $("#infoForKind0").addClass('hidden');
                $('#paneForKind2').empty().append(qInfo);

                if (questionArr[qIdx].hasLike)
                    $('#likeDiv').empty().append('<i onclick="likeQuestion()" data-val="selected" onmouseleave="likeMouseLeaveEvent(this)" onmouseenter="likeMouseEnterEvent(this)" style="cursor: pointer; font-size: 20px" class="fa fa-heart" aria-hidden="true"></i>');
                else
                    $('#likeDiv').empty().append('<i onclick="likeQuestion()" data-val="unselected" onmouseleave="likeMouseLeaveEvent(this)" onmouseenter="likeMouseEnterEvent(this)" style="cursor: pointer; font-size: 20px" class="fa fa-heart-o" aria-hidden="true"></i>');

            }

            if(questionArr[qIdx].kindQ != "2") {

                $("#likesNo").empty().append(questionArr[qIdx].likeNo);
                $("#correctNo").empty().append(questionArr[qIdx].correct);
                $("#incorrectNo").empty().append(questionArr[qIdx].incorrect);
                $("#whiteNo").empty().append(questionArr[qIdx].white);
                $("#percent").empty().append(Math.round((questionArr[qIdx].correct * 100) / (questionArr[qIdx].correct + questionArr[qIdx].incorrect + questionArr[qIdx].white)));
                $("#qLevel").empty().append(questionArr[qIdx].level);
                $("#totalAns").empty().append(questionArr[qIdx].correct + questionArr[qIdx].incorrect + questionArr[qIdx].white);
                $("#discussion").attr('data-val', questionArr[qIdx].discussion);

                if (questionArr[qIdx].hasLike)
                    $('#likeDiv').empty().append('<i onclick="likeQuestion()" data-val="selected" onmouseleave="likeMouseLeaveEvent(this)" onmouseenter="likeMouseEnterEvent(this)" style="cursor: pointer; font-size: 20px" class="fa fa-heart" aria-hidden="true"></i>');
                else
                    $('#likeDiv').empty().append('<i onclick="likeQuestion()" data-val="unselected" onmouseleave="likeMouseLeaveEvent(this)" onmouseenter="likeMouseEnterEvent(this)" style="cursor: pointer; font-size: 20px" class="fa fa-heart-o" aria-hidden="true"></i>');

                $("#infoForKind0").removeClass('hidden');
                $("#infoForKind2").addClass('hidden');
            }

            $("#BQ").empty().append(newNode);

            newNode = "<span><img alt='در حال بارگذاری تصویر' style='max-width: 100%' src='{{URL::asset('images/answers/system')}}/" + questionArr[qIdx].ansFile + "'></span><br/>";
            $("#BA").empty().append(newNode);
        }

        function goToDiscussionRoom() {

            if(qIdx < 0 || qIdx >= questionArr.length)
                return;

            document.location.href = $("#discussion").attr('data-val');
        }

        function likeQuestion() {

            if(qIdx < 0 || qIdx >= questionArr.length)
                return;

            $.ajax({
                type: 'post',
                url: likeQuestionDir,
                data: {
                    'qId': questionArr[qIdx].id
                },
                success: function(response) {
                    if(response == "select") {
                        $('#likeDiv').empty().append('<i onclick="likeQuestion()" data-val="selected" onmouseleave="likeMouseLeaveEvent(this)" onmouseenter="likeMouseEnterEvent(this)" style="cursor: pointer; font-size: 20px" class="fa fa-heart" aria-hidden="true"></i>');
                        questionArr[qIdx].hasLike = true;
                        questionArr[qIdx].likeNo = questionArr[qIdx].likeNo + 1;
                    }
                    else {
                        questionArr[qIdx].hasLike = false;
                        $('#likeDiv').empty().append('<i onclick="likeQuestion()" data-val="unselected" onmouseleave="likeMouseLeaveEvent(this)" onmouseenter="likeMouseEnterEvent(this)" style="cursor: pointer; font-size: 20px" class="fa fa-heart-o" aria-hidden="true"></i>');
                        questionArr[qIdx].likeNo = questionArr[qIdx].likeNo - 1;
                    }
                    SUQ();
                }
            })
        }

        function likeMouseEnterEvent(val) {
            if($(val).attr('data-val') == "unselected") {
                $(val).removeClass('fa-heart-o');
                $(val).addClass('fa-heart');
            }
            else {
                $(val).addClass('fa-heart-o');
                $(val).removeClass('fa-heart');
            }
        }

        function likeMouseLeaveEvent(val) {
            if($(val).attr('data-val') == "unselected") {
                $(val).addClass('fa-heart-o');
                $(val).removeClass('fa-heart');
            }
            else {
                $(val).removeClass('fa-heart-o');
                $(val).addClass('fa-heart');
            }
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

    <div class="row" id="reminder" style="margin-top: 50px">

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
                <span style='margin-right: 5px;'>در حال مشاهده</span>
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

        <div class="col-xs-12">
            <div class="col-xs-1" id="likeDiv"></div>

            <div class='col-xs-8 well well-sm' style="margin-top: 10px; border: 3px solid black; background-color: #ffffff">
                <div id="BQ" style='height: auto; width: auto; max-width: 100%'></div>
                <div id="BA" style='height: auto; width: auto; max-width: 100%'></div>
            </div>

            <div id="infoForKind0" class="col-xs-3" style="margin-top: 50px">
                <div class="col-xs-12">
                    <p><span id="likesNo"></span><span>&nbsp;&nbsp;&nbsp;<i class="fa fa-thumbs-up" aria-hidden="true"></i></span></p>
                    <p><span>تعداد پاسخ گویی:&nbsp;&nbsp;</span><span id="totalAns"></span></p>
                    <p><span>تعداد جواب صحیح:&nbsp;&nbsp;</span><span id="correctNo"></span></p>
                    <p><span>تعداد جواب ناصحیح:&nbsp;&nbsp;</span><span id="incorrectNo"></span></p>
                    <p><span>تعداد جواب بدون پاسخ:&nbsp;&nbsp;</span><span id="whiteNo"></span></p>
                    <p><span>درصد پاسخ گویی:&nbsp;&nbsp;</span><span id="percent"></span></p>
                    <p><span>سطح سختی:&nbsp;&nbsp;</span><span id="qLevel"></span></p>
                </div>
            </div>

            <div id="infoForKind2" class="col-xs-3 hidden" style="margin-top: 50px">
                <p><span id="likesNo"></span><span>&nbsp;&nbsp;&nbsp;<i class="fa fa-thumbs-up" aria-hidden="true"></i></span></p>
                <div class="col-xs-12" id="paneForKind2"></div>
            </div>
        </div>

        <div class="col-xs-12">
            <div style='margin-top: 5px; padding: 10px'>
                <center>
                    {{--<button class="btn btn-default" id="discussion" data-val="" onclick="goToDiscussionRoom()">ورود به پرسش و پاسخ سوال</button>--}}
                    <button id="backQ" class="btn btn-default" onclick="decQ()">سوال قبلی</button>
                    <button id="nxtQ" class="btn btn-default" onclick="incQ()">سوال بعدی</button>
                    <button class="btn btn-danger" onclick="showConfirmationPane()">بازگشت به صفحه آزمون های من</button>
                    @if($quizMode == getValueInfo('systemQuiz'))
                        <button class="btn btn-primary" onclick="showRanking()">نمایش رتبه بندی</button>
                    @endif
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


    <span id="rankingPane" class="ui_overlay item hidden" style="position: fixed; left: 10%; right: 10%; width: auto; top: 70px; bottom: auto">
        <div class="header_text">رتبه بندی</div>
        <div onclick="hideElement()" class="ui_close_x"></div>
        <div class="body_text">
            <div id="ranking"></div>
        </div>
    </span>

    <script>

        function goToQuizEntry() {
            document.location.href = '{{route('myQuizes')}}';
        }

        function showConfirmationPane() {
            hideElement();
            $("#confirmationPane").removeClass('hidden');
        }

        function hideElement() {
            $(".item").addClass('hidden');
        }

    </script>
@stop