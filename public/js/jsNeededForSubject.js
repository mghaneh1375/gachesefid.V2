var selectedLesson;
var selectedSubject;

$(document).ready(function () {
    getLessons($("#gradeId").val());
});

function showElement(element) {
    $(".item").addClass('hidden');
    $("#" + element).removeClass('hidden');
}

function hideElement() {
    $(".item").addClass('hidden');
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

    selectedLesson = lessonId;
    
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
                newElement = "<p style='margin-top: 10px'>مبحثی موجود نیست</p>";

            for(i = 0; i < response.length; i++) {
                newElement += "<div class='col-xs-12' style='margin-top: 10px'>";
                newElement += "<span>" + response[i].name + " قیمت سوالات آسان " + response[i].price1 + " - قیمت سوالات متوسط - " + response[i].price2 + " قیمت سوالات سخت " + response[i].price3 + "</span>";
                newElement += "<button onclick='deleteSubject(\"" + response[i].id + "\")' class='btn btn-danger' data-toggle='tooltip' title='حذف مبحث'><span class='glyphicon glyphicon-remove' style='margin-left: 30%'></span></button>";
                newElement += "<button onclick='editSubject(\"" + response[i].id + "\", \"" + response[i].name + "\", \"" + response[i].price1 + "\", \"" + response[i].price2 + "\", \"" + response[i].price3 + "\")' class='btn btn-primary' data-toggle='tooltip' title='ویرایش مبحث'><span class='glyphicon glyphicon-edit' style='margin-left: 30%'></span></button>";
                newElement += "</div>";
            }

            $("#subjects").append(newElement);

        }
    });
}

function editSubject(val, name, price1, price2, price3) {
    selectedSubject = val;
    $("#subjectNameEdit").val(name);
    $("#subjectPrice1Edit").val(price1);
    $("#subjectPrice2Edit").val(price2);
    $("#subjectPrice3Edit").val(price3);
    showElement('editSubjectContainer');
}

function doEditSubject() {

    if($("#subjectNameEdit").val() == "")
        return;

    $.ajax({
        type: 'post',
        url: editSubjectDir,
        data: {
            'subjectId': selectedSubject,
            'subjectName': $("#subjectNameEdit").val(),
            'price1': $("#subjectPrice1Edit").val(),
            'price2': $("#subjectPrice2Edit").val(),
            'price3': $("#subjectPrice3Edit").val()
        },
        success: function (response) {
            if(response == "ok")
                document.location.href = subjects;
            else
                $("#errMsgEdit").append("مبحث جدید در سامانه موجود است");
        }
    });
}

function deleteSubject(subjectId) {

    $.ajax({
        type: 'post',
        url: deleteSubjectDir,
        data: {
            'subjectId': subjectId
        },
        success: function (response) {
            if(response == "ok") {
                getSubjects(selectedLesson);
            }
        }
    });

}

function doAddNewSubject() {

    if($("#subjectName").val() == "")
        return;

    $.ajax({
        type: 'post',
        url: addNewSubjectDir,
        data: {
            'subjectName': $("#subjectName").val(),
            'price1': $("#price1").val(),
            'price2': $("#price2").val(),
            'price3': $("#price3").val(),
            'lessonId': selectedLesson
        },
        success: function (response) {
            if(response == "ok") {
                hideElement();
                getSubjects(selectedLesson);
            }
            else
                $("#errMsg").append("مبحث مورد نظر در درس مورد نظر وجود دارد");
        }
    });

}