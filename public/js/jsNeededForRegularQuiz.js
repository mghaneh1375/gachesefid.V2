var questions = [];
var currIdx = 0;
var subQuestions = [];
var subCurrIdx = 0;
var currQuiz = -1;

function changeRankingCount(quizId) {

    $.ajax({
        type: 'post',
        url: changeRankingCountDir,
        data: {
            val: $("#rankingCount").val(),
            'quizId': quizId
        }
    });
}

function deleteDeletedQ(quizId, questionId) {

    $.ajax({
        type: 'post',
        url: deleteDeletedQFromQ,
        data: {
            'quizId': quizId,
            'questionId': questionId
        },
        success: function (response) {
            if (response == "ok")
                elseQuiz(quizId);
            else
                $("#msgDeleteQFromQ").empty().append('مشکلی در انجام عملیات مورد نظر رخ داده است');
        }
    });
}

function deleteQuestionFromQuiz(quizId) {

    questionId = $("#deleteQuestionFromQuizInput").val();

    if(questionId == "")
        return;

    $.ajax({
        type: 'post',
        url: deleteQFromQ,
        data: {
            'quizId': quizId,
            'questionId': questionId,
            'quizMode': 'regularQuiz'
        },
        success: function (response) {
            if (response == "ok")
                elseQuiz(quizId);
            else
                $("#msgDeleteQFromQ").empty().append('سوال مورد نظر در آزمون وجود ندارد');
        }
    });

}

function elseQuiz(quizId) {
    
    $.ajax({
        type: 'post',
        url: elseQuizDir,
        data: {
            'quizId': quizId
        },
        success: function (response) {

            response = JSON.parse(response);

            newElement = "<div class='col-xs-12' style='margin-top: 10px'><label><span>تعداد نفرات برتر</span><input style='margin-right: 10px' value='" + response.ranking + "' id='rankingCount' type='number'><span style='margin-right: 10px' class='btn btn-success' onclick='changeRankingCount(\"" + quizId + "\")'>اعمال تغییرات</span></label></div>";

            newElement += "<div class='col-xs-12' style='margin-top: 10px'><h4>سوالات حذف شده از آزمون</h4>";

            deleteQ = JSON.parse(response.deleteQ);

            if(deleteQ.length == 0)
                newElement += "<div class='col-xs-12' style='margin-top: 10px'>سوالی موجود نیست</div>";

            for(i = 0; i < deleteQ.length; i++) {
                newElement += "<div class='col-xs-12' style='margin-top: 10px'><label><span>سوال " + deleteQ[i].qNo + "</span>";
                newElement += "<button onclick='deleteDeletedQ(\"" + quizId + "\", \"" + deleteQ[i].qNo + "\")' class='btn btn-danger'><span class='glyphicon glyphicon-remove'></span></button>";
                newElement += "</label></div>";
            }

            newElement += "<div class='col-xs-12' style='margin-top: 10px'>";
            newElement += "<label><span>حذف سوال از آزمون</span><input id='deleteQuestionFromQuizInput' style='margin-right: 10px' type='number'></label>";
            newElement += "</div>";

            newElement += "<div class='col-xs-12'><p style='cursor: pointer; width: 200px' onclick='deleteQuestionFromQuiz(\"" + quizId + "\")' class=' btn btn-primary'>حذف کن</p></div>";

            newElement += "<div class='col-xs-12'><p id='msgDeleteQFromQ' class='errorText'></p></div>";

            $("#body_elseQuiz").empty().append(newElement);
        }
        
    });
    
    hideElement();
    $(".dark").removeClass('hidden');
    $("#elseQuiz").removeClass('hidden');
    
}

function addQuiz() {
    hideElement();
    $(".form-detail").val("");
    $("#newQuizContainer").removeClass('hidden');

}

function editQuiz(quizId) {

    $.ajax({
        type: 'post',
        url: getRegularQuizDetails,
        data: {
            'quizId': quizId
        },
        success: function (response) {

            if(response == "timeOut") {
                alert("زمان آزمون مورد نظر به اتمام رسیده است");
                return;
            }

            response = JSON.parse(response);
            hideElement();
            currQuiz = quizId;
            $("#newQuizContainer").removeClass('hidden');
            $("#name").val(response.name);
            $("#price").val(response.price);
            $("#date_input").val(response.startDate);
            $("#date_input_end").val(response.endDate);
            $("#date_input_reg").val(response.startReg);
            $("#date_input_reg_end").val(response.endReg);
            $("#sTime").val(response.startTime);
            $("#eTime").val(response.endTime);

        }
    });
}

function hideElement() {
    currQuiz = -1;
    $(".dark").addClass('hidden');
    $(".item").addClass('hidden');
}

function addQuestionToQuiz(quizId) {

    hideElement();
    currQuiz = quizId;

    $.ajax({
        type: 'post',
        url: getQuizQuestions,
        data: {
            'quizId': quizId
        },
        success: function (response) {

            $("#prevQ").addClass('hidden');
            $("#nextQ").addClass('hidden');
            currIdx = 0;

            if(response == "timeOut") {
                alert("زمان آزمون مورد نظر به اتمام رسیده است");
                return;
            }

            if(response != "") {
                response = JSON.parse(response);
                questions = response;
            }
            else{
                $("#msg").empty();
                $("#msg").append('سوالی موجود نیست');
                $("#nextQ").addClass('hidden');
            }

            showQuestion();

            $("#addQuestionPane").removeClass('hidden');
        }
    });
}

function addQuestion() {
    getGrades();
}

function prevQ() {
    if(currIdx - 1 >= 0)
        currIdx--;
    showQuestion();
}

function nextQ() {
    if(currIdx + 1 < questions.length)
        currIdx++;
    showQuestion();
}

function subPrevQ() {
    if(subCurrIdx - 1 >= 0)
        subCurrIdx--;
    showSubQuestion();
}

function subNextQ() {
    if(subCurrIdx + 1 < subQuestions.length)
        subCurrIdx++;
    showSubQuestion();
}

function doAddQuestionToQuiz() {

    $("#subMsgBottom").empty();

    $.ajax({
        type: 'post',
        url: doAddQuestionToQuizDir,
        data: {
            'quizId': currQuiz,
            'questionId': subQuestions[subCurrIdx].id
        },
        success: function (response) {

            response = JSON.parse(response);

            if(response.status == "ok") {
                hideAddQuestion();
                idxTmp = questions.length;
                questions[idxTmp] = subQuestions[subCurrIdx];
                questions[idxTmp].mark = 10;
                questions[idxTmp].qNo = response.qNo;
                showQuestion();
            }
            else
                $("#subMsgBottom").append('سوال مورد نظر در آزمون انتخابی وجود دارد');
        }
    });

}

function getGrades() {

    $.ajax({
        type: 'post',
        url: getGradesDir,
        success: function (response) {

            response = JSON.parse(response);

            newElement = "";
            $("#grades").empty();

            if(response.length == 0)
                newElement = "<option value='none'>پایه ی تحصیلی ای موجود نیست</option>";

            for(i = 0; i < response.length; i++) {
                newElement += "<option value='" + response[i].id + "'>" + response[i].name + "</option>";
            }

            $("#grades").append(newElement);
            getLessons($("#grades").val());

        }
    });
}

function getLessons(gradeId) {

    $.ajax({
        type: 'post',
        url: getLessonsDir,
        data: {
            'gradeId' : gradeId
        },
        success: function (response) {

            response = JSON.parse(response);

            newElement = "";
            $("#lessons").empty();

            if(response.length == 0)
                newElement = "<option value='none'>درسی موجود نیست</option>";

            for(i = 0; i < response.length; i++) {
                newElement += "<option value='" + response[i].id + "'>" + response[i].name + "</option>";
            }

            $("#lessons").append(newElement);
            getSubjects($("#lessons").val());

        }
    });
}

function getSubjects(lessonId) {

    if(lessonId != "none") {
        $.ajax({
            type: 'post',
            url: getSubjectsDir,
            data: {
                'lessonId': lessonId
            },
            success: function (response) {

                response = JSON.parse(response);

                newElement = "";
                $("#subjects").empty();

                if (response.length == 0)
                    newElement = "<option value='none'>مبحثی موجود نیست</option>";

                for (i = 0; i < response.length; i++) {
                    newElement = "<option value='" + response[i].id + "'>" + response[i].name + "</option>";
                }

                $("#subjects").append(newElement);
                getSubjectQuestions($("#subjects").val());
            }
        });
    }
    getSubjectQuestions("none");
}

function deleteQuiz(quizId) {

    $.ajax({
        type: 'post',
        url: deleteQuizDir,
        data: {
            quizId: quizId
        },
        success:function (response) {
            if(response == "ok")
                document.location.href = quizDir;
            else if(response == "timeOut")
                alert("زمان ثبت نام آزمون مورد نظر شروع شده است و امکان حذف آزمون وجود ندارد");
        }
    });

}

function getSubjectQuestions (sId) {

    $("#subPrevQ").addClass('hidden');
    $("#subNextQ").addClass('hidden');
    subCurrIdx = 0;
    subQuestions = [];
    $("#hiddenCurrQuiz").val(currQuiz);
    if(sId != "none") {
        $.ajax({
            type: 'post',
            url: getSubjectQuestionsDir,
            data: {
                'sId': sId
            },
            success: function (response) {
                if(response != "") {
                    response = JSON.parse(response);
                    subQuestions = response;
                }
                else {
                    $("#subMsg").empty();
                    $("#subMsg").append('سوالی موجود نیست');
                }

                showSubQuestion();
                $("#addQuestion").removeClass('hidden');
            }
        });
    }
}

function showQuestion() {

    $("#qInfo").empty();
    $("#msg").empty();

    if(currIdx < 0 || currIdx >= questions.length) {
        $("#msg").append('سوالی موجود نیست');
        $("#questionPane").css('background', 'none');
        $("#ansPane").css('background', 'none');
        return;
    }

    if(currIdx == 0)
        $("#prevQ").addClass('hidden');
    else
        $("#prevQ").removeClass('hidden');

    if(currIdx == questions.length - 1)
        $("#nextQ").addClass('hidden');
    else
        $("#nextQ").removeClass('hidden');

    $("#questionPane").css('background', 'url("' + questions[currIdx].questionFile + '")')
        .css('background-repeat', 'no-repeat')
        .css('background-size', 'contain')
        .click(function () {
            window.open(homeDir + "/totalQuestions/" + questions[currIdx].id, "_blank");
        });

    $("#ansPane").css('background', 'url("' + questions[currIdx].ansFile + '")')
        .css('background-repeat', 'no-repeat')
        .css('background-size', 'contain');


    newElement = "<div class='col-xs-4'> زمان مورد نیاز: " + questions[currIdx].neededTime + " ثانیه</div>";
    switch (questions[currIdx].level) {
        case 1:
        default:
            level = "ساده";
            break;
        case 2:
            level = "متوسط";
            break;
        case 3:
            level = "دشوار";
            break;
    }
    newElement += "<div class='col-xs-4'>سطح سختی " + level + "</div>";
    newElement += "<div class='col-xs-4'> پاسخ " + questions[currIdx].ans + "</div>";
    newElement += "<div class='col-xs-6'><center><button class='btn btn-danger' onclick='removeQFromQ(\"" + questions[currIdx].id + "\")' data-toggle='tooltip' title='حذف سوال از آزمون'><span class='glyphicon glyphicon-remove'></span></button></center></div>";
    newElement += "<div class='col-xs-6'><center> شماره سوال <input style='max-width: 100px' type='number' value='" + questions[currIdx].qNo + "' onchange='changeQNo(\"" + questions[currIdx].id + "\", this.value)'></center></div>";
    $("#qInfo").append(newElement);
}

function changeQNo(qId, val) {
    $.ajax({
        type: 'post',
        url: changeQNoDir,
        data: {
            'quizId': currQuiz,
            'questionId': qId,
            'val': val
        },
        success: function (response) {
            if(response == "ok") {
                addQuestionToQuiz(currQuiz);
            }
        }
    })
}

function removeQFromQ(id) {

    $.ajax({
        type: 'post',
        url: removeQFromQDir,
        data: {
            'quizId': currQuiz,
            'questionId': id
        },
        success: function (response) {
            if(response == "ok") {
                addQuestionToQuiz(currQuiz);
            }
        }
    });

}

function hideAddQuestion() {
    $("#addQuestion").addClass('hidden');
}

function showSubQuestion() {

    $("#subQInfo").empty();
    $("#subMsg").empty();

    if(subCurrIdx < 0 || subCurrIdx >= subQuestions.length) {
        $("#subMsg").append('سوالی موجود نیست');
        $("#subQuestionPane").css('background', 'none');
        $("#subAnsPane").css('background', 'none');
        return;
    }

    if(subCurrIdx == 0)
        $("#subPrevQ").addClass('hidden');
    else
        $("#subPrevQ").removeClass('hidden');

    if(subCurrIdx == subQuestions.length - 1)
        $("#subNextQ").addClass('hidden');
    else
        $("#subNextQ").removeClass('hidden');

    $("#subQuestionPane").css('background', 'url("' + subQuestions[subCurrIdx].questionFile + '")');
    $("#subQuestionPane").css('background-repeat', 'no-repeat');
    $("#subQuestionPane").css('background-size', '100% 100%');

    $("#subAnsPane").css('background', 'url("' + subQuestions[subCurrIdx].ansFile + '")');
    $("#subAnsPane").css('background-repeat', 'no-repeat');
    $("#subAnsPane").css('background-size', '100% 100%');

    newElement = "<div class='col-xs-4'> زمان مورد نیاز: " + subQuestions[subCurrIdx].neededTime + " ثانیه</div>";
    switch (subQuestions[subCurrIdx].level) {
        case 1:
        default:
            level = "ساده";
            break;
        case 2:
            level = "متوسط";
            break;
        case 3:
            level = "دشوار";
            break;
    }
    newElement += "<div class='col-xs-4'>سطح سختی " + level + "</div>";
    newElement += "<div class='col-xs-4'> پاسخ " + subQuestions[subCurrIdx].ans + "</div>";

    $("#subQInfo").append(newElement);
}

function doAddQuiz() {

    if($("#name").val() == "" || $("#price").val() == "" ||
        $("#date_input").val() == "" || $("#date_input_end").val() == "" ||
        $("#sTime").val() == "" || $("#eTime").val() == "" || $("#date_input_reg").val() == "" ||
        $("#date_input_reg_end").val() == "") {

        $("#errMsg").empty();
        $("#errMsg").append('لطفا تمام موارد را پر نمایید');
        return;
    }

    if(currQuiz == -1)
        url = addQuizDir;
    else
        url = editQuizDir;

    $.ajax({
        type: 'post',
        url: url,
        data: {
            'name': $("#name").val(),
            'price': $("#price").val(),
            'sDate': $("#date_input").val(),
            'eDate': $("#date_input_end").val(),
            'sTime': $("#sTime").val(),
            'eTime': $("#eTime").val(),
            'sDateReg': $("#date_input_reg").val(),
            'eDateReg': $("#date_input_reg_end").val(),
            'quizId': currQuiz
        },
        success: function (response) {
            if(response == "ok")
                document.location.href = quizDir;

            else {
                $("#errMsg").empty();
                $("#errMsg").append(response);
            }
        }
    });

}

function fetchQ() {

    if($("#organizationId").val() == "")
        return;

    $("#subPrevQ").addClass('hidden');
    $("#subNextQ").addClass('hidden');
    subCurrIdx = 0;
    subQuestions = [];

    $.ajax({
        type: 'post',
        url: fetchQuestionByOrganizationId,
        data: {
            'organizationId': $("#organizationId").val()
        },
        success: function (response) {
            if(response == "nok") {
                $("#fetchErr").empty();
                $("#fetchErr").append('سوالی با کد سازمانی وارد شده وجود ندارد');
            }
            else {
                response = JSON.parse(response);
                subQuestions = response;
            }

            showSubQuestion();
            $("#hiddenCurrQuiz").val(currQuiz);
            $("#addQuestion").removeClass('hidden');
        }
    });
}
