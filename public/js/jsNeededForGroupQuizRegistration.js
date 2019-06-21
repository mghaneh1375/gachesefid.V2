
function getRegularQuizesOfStd(uId) {

    $.ajax({
        type: 'post',
        url: getRegularQuizesOfStdDir,
        data: {
            'uId': uId
        },
        success: function (response) {

            var newElement;

            if(response.length < 0)
                newElement = "آزمونی موجود نیست";

            else {
                response = JSON.parse(response);

                newElement = "";

                if(response.length == 0)
                    newElement = "آزمونی موجود نیست";

                for (i = 0; i < response.length; i++) {
                    newElement += "<center>" + response[i].name + "</center>";
                }
            }
            $("#quizes").empty().append(newElement);
            $(".dark").removeClass('hidden');
            $("#quizStatus").removeClass('hidden');
        }
    });
}

function selectAll(val) {
    if(val)
        $(":checkbox[name='selectedStd[]']").prop('checked', true);
    else
        $(":checkbox[name='selectedStd[]']").prop('checked', false);
}

$(document).ready(function () {
    if($("#qId").val() != "")
        getStdOfQuiz($("#qId").val());
});

function getStdOfQuiz(qId) {

    var presence = $("#qId").find(":selected").attr('data-presence');

    if(presence == 0) {
        $("#presenceBtn").addClass('hidden');
    }
    else {
        $("#presenceBtn").removeClass('hidden');
    }

    $(":checkbox[name='selectedStd[]']").prop('checked', false);

    $("#msg").removeClass('hidden').empty().append('در حال به روز رسانی جدول');
    $(".spinner").removeClass('hidden');

    $.ajax({
        type: 'post',
        url: getStdOfQuizDir,
        data: {
            'qId': qId
        },
        success: function (response) {

            response = JSON.parse(response);
            if(response.length == 0) {
                $("#msg").empty().append('نتیجه ای یافت نشد');
                $(".spinner").addClass('hidden');
                return;
            }

            $("#msg").addClass('hidden');
            $(".spinner").addClass('hidden');

            for(i = 0; i < response.length; i++) {

                if(response[i].status == 1)
                    $("#status_" + response[i].id).empty().append('<span style="color: #d9534f" ">ثبت نام نشده</span>');
                else if(response[i].status == 2) {
                    if(response[i].online == "آنلاین")
                        $("#status_" + response[i].id).empty().append('<span style="color: #12b780">' + 'در حال بررسی' + ' ' + 'ثبت نام' + ' ' + response[i].online + '</span>');
                    else
                        $("#status_" + response[i].id).empty().append('<span style="color: #95b722">' + 'در حال بررسی' + ' ' + 'ثبت نام' + ' ' + response[i].online + '</span>');
                }
                else if(response[i].status == 3) {
                    if(response[i].online == "آنلاین")
                        $("#status_" + response[i].id).empty().append('<span style="color: #34a8b7">' + 'ثبت نام شده' + ' ' + 'ثبت نام' + ' ' + response[i].online + "</span>");
                    else
                        $("#status_" + response[i].id).empty().append('<span style="color: #5434b7">' + 'ثبت نام شده' + ' ' + 'ثبت نام' + ' ' + response[i].online + "</span>");
                }
            }

        }
    });
    
}

function submitRegistry(mode) {

    var elems = $("input:checkbox[name='selectedStd[]']:checked").map(function() {
        return this.value;
    }).get();

    if(elems.length == 0)
        return;

    qId = $("#qId").val();

    $.ajax({
        type: 'post',
        url: submitRegistryDir,
        data: {
            'qId': qId,
            'stds': elems,
            'mode': mode
        },
        success: function (response) {
            if(response == "ok") {
                getStdOfQuiz(qId);
            }
        }
    });
}

function deleteFromQueue() {

    var elems = $("input:checkbox[name='selectedStd[]']:checked").map(function() {
        return this.value;
    }).get();

    if(elems.length == 0)
        return;

    qId = $("#qId").val();

    $.ajax({
        type: 'post',
        url: deleteFromQueueDir,
        data: {
            'qId': qId,
            'stds': elems
        },
        success: function (response) {
            if(response == "ok") {
                getStdOfQuiz(qId);
            }
        }
    });
}

function changeSubmitStatus() {

    var checkedValuesP = $("input:checkbox[name='selectedQuizP[]']:checked");
    var checkedValuesO = $("input:checkbox[name='selectedQuizO[]']:checked");

    if(checkedValuesP.length + checkedValuesO.length == 0)
        $("#submitRegistry").attr('disabled', 'disabled');
    else
        $("#submitRegistry").removeAttr('disabled');
}

function registerableList(uId) {

    $.ajax({
        type: 'post',
        url: registerableListDir,
        data: {
            'uId': uId
        },
        success: function (response) {

            if (response.length < 0) {
                newElement += "آزمونی موجود نیست";
            }
            else {
                response = JSON.parse(response);
                newElement = "";

                if (response.length == 0)
                    newElement += "آزمونی موجود نیست";

                for (i = 0; i < response.length; i++) {
                    newElement += "<center><span>" + response[i].name + "</span> - شروع آزمون: ";
                    newElement += "<span>" + response[i].startDate + "</span> - اتمام آزمون: ";
                    newElement += "<span>" + response[i].endDate + "</span> - شروع ثبت نام: ";
                    newElement += "<span>" + response[i].startReg + "</span> - اتمام ثبت نام: ";
                    newElement += "<span>" + response[i].endReg + "</span>&nbsp;&nbsp;";
                    newElement += "<span> آزمون حضوری <input value='" + response[i].id + "' onchange='changeSubmitStatus()' type='checkbox' name='selectedQuizP[]'></span>";
                    newElement += "<span> آزمون آنلاین <input value='" + response[i].id + "' onchange='changeSubmitStatus()' type='checkbox' name='selectedQuizO[]'></span>";
                    newElement += "</center>";
                }

                if(response.length != 0) {
                    newElement += "<center><button id='submitRegistry' onclick='submitRegistry(\"" + uId + "\")' class='btn btn-primary' disabled>ثبت</button></center>";
                }
            }

            $("#availableQuizes").empty().append(newElement);
            $(".dark").removeClass('hidden');
            $("#quizRegistry").removeClass('hidden');

        }
    });
}

function getQueuedQuizes(uId) {
    $.ajax({
        type: 'post',
        url: getQueuedQuizesDir,
        data: {
            'uId': uId
        },
        success: function (response) {

            if(response.length < 0)
                newElement = "آزمونی موجود نیست";

            else {
                response = JSON.parse(response);
                newElement = "";

                if(response.length == 0)
                    newElement = "آزمونی موجود نیست";

                for (i = 0; i < response.length; i++) {
                    if(response[i].online == 0)
                        newElement += "<center>" + response[i].name + " - آنلاین </center>";
                    else
                        newElement += "<center>" + response[i].name + " - حضوری </center>";
                }
            }

            $("#queuedQuizes").empty().append(newElement);
            $(".dark").removeClass('hidden');
            $("#queuedQuiz").removeClass('hidden');

        }
    });
}

function hideElement() {
    $(".item").addClass('hidden');
    $(".dark").addClass('hidden');
}