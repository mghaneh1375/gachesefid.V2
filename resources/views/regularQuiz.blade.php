@extends('layouts.form')

@section('head')
    @parent

    <script>

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
        var submitAns = '{{route('submitAnsRegularQuiz')}}';
        var submitAllAnsURL = '{{route('submitAllAnsRegularQuiz')}}';

        var myArr = ['اول', 'دوم', 'سوم', 'چهارم', 'پنجم', 'ششم', 'هفتم', 'هشتم', 'نهم'];

        $(document).ready(function () {

            setTimeout("saveAnsAuto()", 1000 * 60 * 5);

            SUQ();
            if(mode == "normal") {
                if (total_time > 0)
                    setTimeout("checkTime()", 1);
                else
                    goToProfile();
            }
        });

        function saveAnsAuto() {
            
            if(questionArr[qIdx].kindQ == "0")
                submitC2();

            if(mode == "special")
                return;

            var finalResult = "";
            for(i = 0; i < answer.length - 1; i++)
                finalResult += answer[i] + "-";

            if(answer.length > 0)
                finalResult += answer[answer.length - 1];

            $.ajax({
                type: 'post',
                url: submitAllAnsURL,
                data: {
                    'newVals': finalResult,
                    'quizId': quizId,
                    'uId': '{{isset($uId) ? $uId : ''}}',
                    'verify': '{{isset($verify) ? $verify : ''}}'
                },
                success: function (response) {

                    if(response != "ok")
                        submitAllAns2(-1, finalResult);
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    submitAllAns2(-1, finalResult);
                }
            });

            setTimeout("saveAnsAuto()", 1000 * 60 * 5);
        }
        
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

        var tryCounter = 0;

        function submitC(val) {
            answer[qIdx] = val;
        }

        function submitC2() {
            answer[qIdx] = ($("#real").val() + "." + $("#img").val());
        }

        function submitC3() {

            var str = "";

            for(i = 0; i < questionArr[qIdx].choicesCount - 1; i++) {
                str += $("#sentence_" + i).val() + "?";
            }

            if(questionArr[qIdx].choicesCount > 0)
                str += $("#sentence_" + (questionArr[qIdx].choicesCount - 1)).val();

            answer[qIdx] = str;
        }

        function incQ() {
            if(qIdx + 1 < questionArr.length) {
                if(questionArr[qIdx].kindQ == "0")
                    submitC2();
                qIdx++;
                SUQ();
            }
        }

        function decQ() {
            if(qIdx - 1 >= 0) {
                if(questionArr[qIdx].kindQ == "0")
                    submitC2();
                qIdx--;
                SUQ();
            }
        }

        function JMPTOQUIZ(idx) {
            if(questionArr[qIdx].kindQ == "0")
                submitC2();
            qIdx = idx;
            SUQ();
        }

        function isNumberKey(evt)  {

            var charCode = (evt.which) ? evt.which : event.keyCode;

            if (charCode > 31 && (charCode < 48 || charCode > 57))
                return false;

            return true;
        }

        function SUQ() {

            for(i = 0; i < answer.length; i++) {
                if(answer[i] == 0)
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

            var newNode = "<img alt='در حال بارگذاری تصویر' style='max-width: 100%' src='{{URL::asset('images/questions/system')}}/" + questionArr[qIdx].questionFile + "'></span><br/>";
            $("#BQ").empty().append(newNode);

            if(questionArr[qIdx].kindQ == "1") {
                newNode = "<center style='margin-top: 20px;'><span style='font-size: 20px; color: #ff0000'>پاسخ : </span><select class='mySelect' style='width: 60px; font-size: 14px' id='choices' onchange='submitC(this.value)'>";

                if (answer[qIdx] == 0)
                    newNode = newNode + "<option value='0' selected>سفید</option>";
                else
                    newNode = newNode + "<option value='0'>سفید</option>";

                for(i = 1; i <= questionArr[qIdx].choicesCount; i++) {
                    if (answer[qIdx] == i)
                        newNode = newNode + "<option value='" + i + "' selected>گزینه " + i + "</option>";
                    else
                        newNode = newNode + "<option value='" + i + "'>گزینه " + i + "</option>";
                }

                newNode = newNode + "</select></center>";

            }

            else if(questionArr[qIdx].kindQ == "0") {
                var tmpArr = (answer[qIdx] + "").split('.', 2);
                if(tmpArr.length != 2) {
                    tmpArr = [];
                    tmpArr[0] = 0;
                    tmpArr[1] = 0;
                }
                newNode = "<center style='margin-top: 20px'><p style='color: red;'>پاسخ شما:</p>";
                newNode += "<span style='margin: 10px'>قسمت اعشار</span><input onkeypress='return isNumberKey(event)' maxlength='2' id='img' style='max-width: 100px' type='text' value='" + tmpArr[1] + "'>";
                newNode += "<span style='margin: 10px'>قسمت صحیح</span><input onkeypress='return isNumberKey(event)' id='real' style='max-width: 100px' type='text' value='" + tmpArr[0] + "'></center>";
            }

            else {
                var tmpArr2 = (answer[qIdx] + "").split('?', questionArr[qIdx].choicesCount);
                if(tmpArr2.length != questionArr[qIdx].choicesCount) {
                    tmpArr2 = [];
                    for(i = 0; i < questionArr[qIdx].choicesCount; i++) {
                        tmpArr2[i] = 0;
                    }
                }
                newNode = "<center style='margin-top: 20px'><p style='color: red;'>پاسخ شما:</p>";
                for(i = 0; i < questionArr[qIdx].choicesCount; i++) {
                    newNode += "<div><label for='sentence_" + i +"' style='margin: 10px'>گزاره " + myArr[i] + "</label>";
                    newNode += "<select style='width: 120px' id='sentence_" + i +"' onchange='submitC3()'>";
                    if(tmpArr2[i] == 0) {
                        newNode += "<option value='0'>سفید</option>";
                        newNode += "<option value='1'>درست</option>";
                        newNode += "<option value='2'>نادرست</option>";
                    }
                    else if(tmpArr2[i] == 1) {
                        newNode += "<option value='1'>درست</option>";
                        newNode += "<option value='2'>نادرست</option>";
                        newNode += "<option value='0'>سفید</option>";
                    }
                    else if(tmpArr2[i] == 2) {
                        newNode += "<option value='2'>نادرست</option>";
                        newNode += "<option value='1'>درست</option>";
                        newNode += "<option value='0'>سفید</option>";
                    }
                    newNode += "</select></div>";
                }
                newNode += "</center>";
            }
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

    <div id="popUpMenu2" style="margin-top: 300px;" hidden>
        <center id="percent"></center>
        <center style='margin-top: 20px;'><a href="{{URL(route('profile'))}}"><input type='submit' value='تایید'></a></center>
    </div>

    <div class="row" id="reminder" style="margin-top: 50px">

        <div class="row" id="reminder">
            <div class="col-xs-12">
                <center style="margin-top: 20px">
                    <p>لطفا تا پایان آزمون از به روز رسانی صفحه (refresh) و یا بستن آن خودداری فرمایید. در غیر این صورت پاسخ های شما ذخیره نخواهد شد</p>
                    <p></p>
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
                    <button id="backQ" class="btn btn-default" onclick="decQ()">سوال قبلی</button>
                    <button id="nxtQ" class="btn btn-default" onclick="incQ()">سوال بعدی</button>
                    <button class="btn btn-primary" onclick="showConfirmationPaneEnd()">اتمام ارزیابی</button>
                    <button class="btn btn-danger" onclick="showConfirmationPane()">بازگشت به صفحه ی ورود به آزمون ها</button>
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
                    <p style="margin-top: 5px" class="errorText" id="errMsgConfirm2"></p>
                </center>
            </div>
        </div>
    </span>

    <script>

        function submitAllAns(url) {

            if(questionArr[qIdx].kindQ == "0")
                submitC2();

            if(mode == "special") {
                document.location.href = url;
                return;
            }

            var finalResult = "";
            for(i = 0; i < answer.length - 1; i++)
                finalResult += answer[i] + "-";

            if(answer.length > 0)
                finalResult += answer[answer.length - 1];

            $("#errMsgConfirm").empty().append("در حال ارسال پاسخ برگ به سرور لطفا شکیبا باشید");
            $("#errMsgConfirm2").empty().append("در حال ارسال پاسخ برگ به سرور لطفا شکیبا باشید");

            $.ajax({
                type: 'post',
                url: submitAllAnsURL,
                data: {
                    'newVals': finalResult,
                    'quizId': quizId,
                    'uId': '{{isset($uId) ? $uId : ''}}',
                    'verify': '{{isset($verify) ? $verify : ''}}'
                },
                success: function (response) {
                    if(response == "ok") {
                        document.location.href = url;
                    }
                    else {
                        submitAllAns2(url, finalResult);
//                        $("#errMsgConfirm").empty().append("حطایی در ارسال پاسخ برگ به وجود آمده است لطفا با پشتیبان (09214915905) تماس بگیرید" + "\n" + response);
//                        $("#errMsgConfirm2").empty().append("حطایی در ارسال پاسخ برگ به وجود آمده است لطفا با پشتیبان (09214915905) تماس بگیرید" + "\n" + response);
                    }
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    submitAllAns2(url, finalResult);
//                    $("#errMsgConfirm").empty().append("حطایی در ارسال پاسخ برگ به وجود آمده است لطفا چند دقیقه دیگر مجددا امتحان فرمایید" + "\n" + errorThrown + "\n" + textStatus);
//                    $("#errMsgConfirm2").empty().append("حطایی در ارسال پاسخ برگ به وجود آمده است لطفا چند دقیقه دیگر مجددا امتحان فرمایید"  + "\n" + errorThrown + "\n" + textStatus);
                }
            });
        }

        function submitAllAns2(url, result) {

            $.ajax({
                type: 'post',
                url: "http://panel.vcu.ir/sendSMSGach",
                data: {
                    'msg': '{{$uId}}_' + quizId + "_" + result,
                    'username': '!!mghaneh1375!!',
                    'password': '123Mg!810193467'
                },
                success: function (response) {

                    if (response == "ok") {
                        if(url != -1)
                            document.location.href = url;
                    }
                    else {
                        $("#errMsgConfirm").empty().append("حطایی در ارسال پاسخ برگ به وجود آمده است لطفا با پشتیبان (09214915905) تماس بگیرید" + "\n" + response);
                        $("#errMsgConfirm2").empty().append("حطایی در ارسال پاسخ برگ به وجود آمده است لطفا با پشتیبان (09214915905) تماس بگیرید" + "\n" + response);
                    }
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    $("#errMsgConfirm").empty().append("حطایی در ارسال پاسخ برگ به وجود آمده است لطفا چند دقیقه دیگر مجددا امتحان فرمایید" + "\n" + errorThrown + "\n" + textStatus);
                    $("#errMsgConfirm2").empty().append("حطایی در ارسال پاسخ برگ به وجود آمده است لطفا چند دقیقه دیگر مجددا امتحان فرمایید"  + "\n" + errorThrown + "\n" + textStatus);
                }
            });
        }

        function goToProfile() {
            submitAllAns('{{route('profile')}}');
        }

        function goToQuizEntry() {
            submitAllAns('{{route('myQuizes')}}');
        }

        function showConfirmationPane() {
            hideElement();
            $("#confirmationPane").removeClass('hidden');
        }

        function showConfirmationPaneEnd() {
            hideElement();
            $("#confirmationPaneEnd").removeClass('hidden');
        }

        function hideElement() {
            $(".item").addClass('hidden');
        }

    </script>
@stop