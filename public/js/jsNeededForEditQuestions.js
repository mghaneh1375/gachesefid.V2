var questions = [];
var currIdx = 0;
var lessons = [];

$(document).ready(function () {

    $("input:file[id='pic']").on('change', prepareUpload);
    $("input:file[id='ansPic']").on('change', prepareUpload2);

    $('#submitQBtn').on('click', submitQuestion);
    $('#submitABtn').on('click', submitAns);
    
    getLessons();
});

function getLessons() {

    if($("#gradeId").val() == "")
        return;

    $.ajax({
        type: 'post',
        url: getLessonsDir,
        data: {
            'gradeId': $("#gradeId").val()
        },
        success: function (response) {
            response = JSON.parse(response);
            newElement = "";

            for(i = 0; i < response.length; i++) {
                if(response[i].id == selectedLesson)
                    newElement += "<option selected value='" + response[i].id + "'>" + response[i].name + "</option>";
                else
                    newElement += "<option value='" + response[i].id + "'>" + response[i].name + "</option>";
            }

            $("#lessonId").empty().append(newElement);
            getQuestions();
        }
    });
}

function getQuestions() {

    if($("#lessonId").val() == "" || $("#lessonId").val() == null)
        return showQuestion();

    $.ajax({
        type: 'post',
        url: getQuestionsDir,
        data: {
            'lessonId': $("#lessonId").val()
        },
        success: function (response) {

            response = JSON.parse(response);

            if(response.length == 0) {
                currIdx = -1;
                showQuestion();
                return;
            }

            questions = response;

            if(questions.length > 0) {
                if(selectedQId == -1)
                    currIdx = 0;
                else  {
                    for (i = 0; i < questions.length; i++) {
                        if(selectedQId == questions[i].id)
                            currIdx = i;
                    }
                }
                showQuestion();
            }

        }
    });

}

function jumpToSpecificQuestion() {

    var jumpVal = $("#jumpVal").val();

    $.ajax({
        type: 'post',
        url: getQuestionByOrganizationId,
        data: {
            'organizationId': jumpVal
        },
        success: function (response) {
            response = JSON.parse(response);
            if(response.status == "ok") {
                questions = [];
                questions[0] = response.question;
                currIdx = 0;
                showQuestion();
            }
        }
    });
}

function prevQ() {
    currIdx--;
    showQuestion();
}

function nextQ() {
    currIdx++;
    showQuestion();
}

function changeKindQ() {

    if($("#kindQ").val() == "1") {
        $("#teloranceDiv").addClass('hidden');
        $("#ansDiv").removeClass('hidden');
        $("#shortAnsDiv").addClass('hidden');
        $("#choice").removeClass('hidden');
        changeChoicesCount();
    }
    else {
        $("#teloranceDiv").removeClass('hidden');
        $("#ansDiv").addClass('hidden');
        $("#choice").addClass('hidden');
        $("#shortAnsDiv").removeClass('hidden');
    }
}

function changeChoicesCount() {

    limit = $("#choicesNum").val();
    newElement = "";
    selected = $("#ans").val();
    $("#ans").empty();

    for(i = 1; i <= limit; i++) {
        if(i == selected)
            newElement += "<option selected value='" + i +"'>گزینه ی " + i + "</option>";
        else
            newElement += "<option value='" + i +"'>گزینه ی " + i + "</option>";
    }

    $("#ans").append(newElement);
}

function removeLesson(lessonId) {

    var  index = -1;

    for (i = 0; i < lessons.length; i++) {
        if(lessons[i] == lessonId)
            index = i;
    }

    if (index > -1) {
        lessons.splice(index, 1);
        var child = document.getElementById("lesson_" + lessonId);
        child.parentNode.removeChild(child);
    }

    if(lessons.length == 0) {
        $("#selectedLessons").append('<p>مبحثی موجود نیست</p>');
    }
}

function setQuestionFileName() {
    $("#questionFileName").empty().append($("#pic").val());
}

function setAnsFileName() {
    $("#ansFileName").empty().append($("#ansPic").val());
}

function prepareUpload(event)  {
    file = event.target.files;
}

function prepareUpload2(event)  {
    ansFile = event.target.files;
}

function submitQuestion(event) {

    if($("#pic").val() == "") {
        $("#qErrMsg").empty().append('لطفا عکسی را به عنوان صورت سوال مشخص کنید');
        return;
    }

    event.stopPropagation();
    event.preventDefault();

    var data = new FormData();
    $.each(file, function(key, value) {
        data.append(key, value);
    });

    $.ajax({
        type: 'POST',
        url: changeQuestionPicDir + questions[currIdx].id,
        data: data,
        cache: false,
        processData: false, // Don't process the files
        contentType: false, // Set content type to false as jQuery will tell the server its a query string request
        success: function(response) {

            response = JSON.parse(response);

            if(response.status == "ok") {
                document.location.href = currUrl;
            }
            else {
                $("#qErrMsg").empty().append(response.status);
            }
        }
    });
}

function submitAns(event) {

    if($("#ansPic").val() == "") {
        $("#aErrMsg").empty().append('لطفا عکسی را به عنوان صورت سوال مشخص کنید');
        return;
    }

    event.stopPropagation();
    event.preventDefault();

    var data = new FormData();
    $.each(ansFile, function(key, value) {
        data.append(key, value);
    });

    $.ajax({
        type: 'POST',
        url: changeAnsPicDir + questions[currIdx].id,
        data: data,
        cache: false,
        processData: false, // Don't process the files
        contentType: false, // Set content type to false as jQuery will tell the server its a query string request
        success: function(response) {

            response = JSON.parse(response);

            if(response.status == "ok") {
                document.location.href = currUrl;
            }
            else {
                $("#aErrMsg").empty().append(response.msg);
            }
        }
    });
}

function hideElement() {
    $(".dark").addClass('hidden');
    $(".item").addClass('hidden');
}

function doAddNewLesson(lessonId, lessonText) {

    if(lessonId == "none")
        return;

    for (i = 0; i < lessons.length; i++) {
        if(lessons[i] == lessonId)
            return
    }

    if(lessons.length == 0)
        $("#selectedLessons").empty();

    lessons.push(lessonId);

    newElement  = "<div style='margin-top: 10px' id='lesson_" + lessonId + "'>";
    newElement += "<span>" + lessonText + "</span>";
    newElement += "<span class='btn btn-danger' onclick='removeLesson(\"" + lessonId + "\")'>";
    newElement += "<span class='glyphicon glyphicon-remove' style='margin-left: 30%'></span>";
    newElement += "</span>";
    newElement += "</div>";
    $("#selectedLessons").append(newElement);
    hideElement();
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

            getLessons2($("#grades").val());

        }
    });
}

function getLessons2(gradeId) {

    $.ajax({
        type: 'post',
        url: getLessonsDir2,
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

            if($("#subjects").length != 0)
                getSubjects($("#lessons").val());

        }
    });
}

function getSubjects(lessonId) {

    $.ajax({
        type: 'post',
        url: getSubjectsDir,
        data: {
            'lessonId' : lessonId
        },
        success: function (response) {

            response = JSON.parse(response);

            newElement = "";
            $("#subjects").empty();

            if(response.length == 0)
                newElement = "<option value='none'>مبحثی موجود نیست</option>";

            for(i = 0; i < response.length; i++) {
                newElement = "<option value='" + response[i].id + "'>" + response[i].name + "</option>";
            }

            $("#subjects").append(newElement);

        }
    });
}

function showAddQuestionPane() {
    hideElement();
    $(".dark").removeClass('hidden');
    getGrades();
    $("#addNewSubjectPane").removeClass('hidden');
}

function showChangeQuestionPane() {
    hideElement();
    $(".dark").removeClass('hidden');
    $("#changeQuestionPane").removeClass('hidden');
}

function showChangeAnsPane() {
    hideElement();
    $(".dark").removeClass('hidden');
    $("#changeAnsPane").removeClass('hidden');
}

function showQuestion() {

    $("#msg").empty();
    $("#levelDiv").removeClass('hidden');
    $("#neededTimeDiv").removeClass('hidden');
    $("#kindQDiv").removeClass('hidden');
    $("#ans").empty();
    $("#choice").removeClass('hidden');
    $("#teloranceDiv").removeClass('hidden');

    if(currIdx < 0) {
        if(questions.length > 0) {
            currIdx = 0;
            return showQuestion();
        }
    }

    if(currIdx < 0 || currIdx >= questions.length) {
        $("#prevQ").addClass('hidden');
        $("#nextQ").addClass('hidden');
        $("#teloranceDiv").addClass('hidden');
        $("#levelDiv").addClass('hidden');
        $("#neededTimeDiv").addClass('hidden');
        $("#shortAnsDiv").addClass('hidden');
        $("#kindQDiv").addClass('hidden');
        $("#choice").addClass('hidden');
        $("#ansDiv").addClass('hidden');
        $("#questionPane").css('background', 'none');
        $("#ansPane").css('background', 'none');
        $("#msg").append('سوالی موجود نیست');
        return;
    }

    $("#selectedLessons").empty();
    lessons = [];

    $.ajax({
        type: 'post',
        url: getQuestionSubjects,
        data: {
            'questionId': questions[currIdx].id
        },
        success: function (response) {

            response = JSON.parse(response);

            for(i = 0; i < response.length; i++) {
                doAddNewLesson(response[i].id, response[i].name);
            }
        }
    });

    if(currIdx == 0)
        $("#prevQ").addClass('hidden');
    else
        $("#prevQ").removeClass('hidden');

    if(currIdx == questions.length - 1)
        $("#nextQ").addClass('hidden');
    else
        $("#nextQ").removeClass('hidden');

    $("#kindQ").val(questions[currIdx].kindQ);

    if(questions[currIdx].kindQ == 1) {
        $("#ansDiv").removeClass('hidden');
        $("#shortAnsDiv").addClass('hidden');
        limit = questions[currIdx].choicesCount;
        $("#teloranceDiv").addClass('hidden');
        $("#choice").removeClass('hidden');
        $("#choicesNum").val(questions[currIdx].choicesCount);
        newElement = "";
        for (i = 1; i <= limit; i++) {
            newElement += "<option value='" + i + "'>گزینه ی " + i + "</option>";
        }
        $("#ans").append(newElement).val(questions[currIdx].ans);
    }
    else {
        $("#choice").addClass('hidden');
        $("#shortAnsDiv").removeClass('hidden');
        $("#ansDiv").addClass('hidden');
        $("#teloranceDiv").removeClass('hidden');
        $("#telorance").val(questions[currIdx].telorance);
        $("#shortAns").val(questions[currIdx].ans);
    }

    $("#level").val(questions[currIdx].level);
    $("#neededTime").val(questions[currIdx].neededTime);

    $("#questionPane").css('background', 'url("' + questions[currIdx].questionFile + '")');
    $("#questionPane").css('background-repeat', 'no-repeat');
    $("#questionPane").css('background-size', 'contain');

    $("#ansPane").css('background', 'url("' + questions[currIdx].ansFile + '")');
    $("#ansPane").css('background-repeat', 'no-repeat');
    $("#ansPane").css('background-size', 'contain');
}

function rejectQuestion() {

    if(currIdx < 0 || currIdx >= questions.length)
        return;

    $(".item").addClass('hidden');
    $("#rejectPane").removeClass('hidden');
}

function doReject() {

    if($("#description").val() == "")
        return;

    $.ajax({
        type: 'post',
        url: rejectQuestionDir,
        data: {
            'desc': $("#description").val(),
            'qId': questions[currIdx].id
        },
        success: function (response) {

            if(response == "ok") {
                questions.splice(currIdx, 1);
                currIdx--;
                $(".item").addClass('hidden');
                showQuestion();
            }
        }
    });
}