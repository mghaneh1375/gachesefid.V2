var subjects = [];
var lessons = [];
var ansFile;
var file;

function hideElement() {
    $(".item").addClass('hidden');
}

function changeKindQ(val) {

    if(val == 1) {
        $("#kindQ").empty().append('تستی').attr('data-val', "1");
        $("#teloranceDiv").hide();
        $("#ansDiv").show();
        $("#shortAnsDiv").hide();
        $("#choice").show();
    }
    else {
        $("#kindQ").empty().append('کوتاه پاسخ').attr('data-val', "0");
        $("#teloranceDiv").show();
        $("#ansDiv").hide();
        $("#choice").hide();
        $("#shortAnsDiv").show();
    }
}

$(document).ready(function(){
    
    $("input:file[id='pic']").on('change', prepareUpload);
    $("input:file[id='ansPic']").on('change', prepareUpload2);

    $('#submitBtn').on('click', submitQuestion);
    
    $('#addBatchBtn').on('click', showAddBatchPane);

    $("#selectedSubjects").append('<p>مبحثی موجود نیست</p>');
    $("#selectedLessons").append('<p>درسی موجود نیست</p>');
    changeChoicesCount();
    
});

function setQuestionFileName() {
    $("#questionFileName").empty().append($("#pic").val());
}

function setAnsFileName() {
    $("#ansFileName").empty().append($("#ansPic").val());
}

function changeChoicesCount() {

    limit = $("#choicesNum").val();
    newElement = "";
    selected = $("#ans").attr('data-val');

    for(i = 1; i <= limit; i++) {
        newElement += "<li><a onclick='changeAns(\"" + i + "\")'>" + "گزینه&nbsp;" + i + "</a></li>";
    }

    if(selected <= limit)
        changeAns(selected);
    else
        changeAns(1);

    $("#ansUL").empty().append(newElement);
}

function changeAns(val) {
    $("#ans").attr('data-val', val).empty().append('گزینه&nbsp;' + val);
}

function showAddBatchPane() {
    hideElement();
    $("#addBatch").removeClass('hidden');
}

function prepareUpload(event)  {
    file = event.target.files;
}

function prepareUpload2(event)  {
    ansFile = event.target.files;
}

function showAddQuestionPane() {
    hideElement();
    getGrades();
    $("#addNewSubjectPane").removeClass('hidden');
}

function removeSubject(subjectId) {

    var index = subjects.indexOf(subjectId);

    if (index > -1) {
        subjects.splice(index, 1);
        var child = document.getElementById("subject_" + subjectId);
        child.parentNode.removeChild(child);
    }

    if(subjects.length == 0) {
        $("#selectedSubjects").append('<p>مبحثی موجود نیست</p>');
    }
}

function changeLevel(val) {
    switch (val) {
        case 1:
        default:
            $("#level").attr('data-val', '1').empty().append('ساده');
            break;
        case 2:
            $("#level").attr('data-val', '2').empty().append('متوسط');
            break;
        case 3:
            $("#level").attr('data-val', '3').empty().append('دشوار');
            break;

    }
}

function removeLesson(lessonId) {

    var index = lessons.indexOf(lessonId);

    if (index > -1) {
        lessons.splice(index, 1);
        var child = document.getElementById("lesson_" + lessonId);
        child.parentNode.removeChild(child);
    }

    if(lessons.length == 0) {
        $("#selectedLessons").append('<p>درسی موجود نیست</p>');
    }
}

function doAddNewSubject() {

    subjectId = $("#subjects :selected").val();

    if(subjectId == "none")
        return;

    for (i = 0; i < subjects.length; i++) {
        if(subjects[i] == subjectId)
            return
    }

    if(subjects.length == 0)
        $("#selectedSubjects").empty();

    subjects.push(subjectId);
    subjectText = $("#subjects :selected").text();

    newElement  = "<div style='margin-top: 10px' id='subject_" + subjectId + "'>";
    newElement += "<span>" + subjectText + "</span>";
    newElement += "<span class='btn btn-danger' onclick='removeSubject(\"" + subjectId + "\")'>";
    newElement += "<span class='glyphicon glyphicon-remove' style='margin-left: 30%'></span>";
    newElement += "</span>";
    newElement += "</div>";
    $("#selectedSubjects").append(newElement);
    hideElement();
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

            getLessons($("#grades").val());

        }
    });
}

function assignToController() {

    if(lessons.length == 0)
        return;

    $.ajax({
        type: 'post',
        url: doAssignToController,
        data: {
            'controllerId': $("#controller").val(),
            'lessons': lessons
        },
        success: function (response) {
            if(response == "ok") {
                $("#errMsg").empty();
                $("#errMsg").append("عملیات مورد نظر با موفقیت انجام پذیرفت");
            }
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

function submitQuestion(event) {

    $("#errMsg").empty();

    if(subjects.length == 0) {
        $("#errMsg").append('لطفا مبحثی به سوال تخصیص دهید');
        return;
    }

    if($("#organizationId").val() == "") {
        $("#errMsg").append('لطفا کد سازمانی سوال را وارد نمایید');
        return;
    }

    if($("#pic").val() == "") {
        $("#errMsg").append('لطفا عکسی را به عنوان صورت سوال مشخص کنید');
        return;
    }

    if($("#ansPic").val() == "") {
        $("#errMsg").append('لطفا عکسی را به عنوان پاسخ تشریحی مشخص کنید');
        return;
    }

    if($("#kindQ").attr('data-val') == '1') {
        ans = $("#ans").attr('data-val');
        additional = $("#choicesNum").val();
    }
    else {
        ans = $("#shortAns").val();
        additional = $("#telorance").val();
    }



    event.stopPropagation();
    event.preventDefault();

    var data = new FormData();
    $.each(file, function(key, value) {
        data.append(key, value);
    });

    $.ajax({
        type: 'POST',
        url: addQuestionPicDir,
        data: data,
        cache: false,
        processData: false, // Don't process the files
        contentType: false, // Set content type to false as jQuery will tell the server its a query string request
        success: function(response) {

            response = JSON.parse(response);
            
            if(response.status == "ok") {

                data = new FormData();
                $.each(ansFile, function(key, value) {
                    data.append(key, value);
                });

                // url: "http://gachesefid.com/addAnsToQuestion/" + response.msg,
                // url: "http://localhost:8080/gachesefid/public/addAnsToQuestion/" + response.msg,
                $.ajax({
                    url: rootDir + "/addAnsToQuestion/" + response.msg,
                    type: 'POST',
                    data: data,
                    cache: false,
                    processData: false, // Don't process the files
                    contentType: false, // Set content type to false as jQuery will tell the server its a query string request
                    success: function (response) {

                        response = JSON.parse(response);

                        if(response.status == "ok") {

                            // url: "http://gachesefid.com/addDetailToQuestion/" + response.msg,
                            // url: "http://localhost:8080/gachesefid/public/addDetailToQuestion/" + response.msg,
                            $.ajax({
                                type: 'post',
                                url: rootDir + "/addDetailToQuestion/" + response.msg,
                                data: {
                                    'level': $("#level").attr('data-val'),
                                    'kindQuestion': $("#kindQ").attr('data-val'),
                                    'neededTime': $("#neededTime").val(),
                                    'ans': ans,
                                    'additional': additional,
                                    'subjects': subjects,
                                    'organizationId': $("#organizationId").val()
                                },
                                success: function (response) {

                                    if(response == "ok")
                                        document.location.href = addQuestion;
                                    else
                                        $("#errMsg").append(response);
                                }
                            });
                        }
                        else {
                            $("#errMsg").append(response.msg);
                        }
                    }
                });
            }

            else {
                $("#errMsg").append(response.msg);
            }
        }
    });
}

function getControllerLevels() {

    controllerId = $("#controller").val();
    $.ajax({
        type: 'post',
        url: getControllerLevelsDir,
        data: {
            'controllerId': controllerId
        },
        success: function (response) {

            response = JSON.parse(response);

            for(i = 0; i < response.length; i++) {
                doAddNewLesson(response[i].lessonId, response[i].lessonName);
            }
        }
    });
}