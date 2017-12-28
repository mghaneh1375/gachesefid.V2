
var selectedGrade;
var selectedLesson;

function showElement(element) {
    $(".item").addClass('hidden');
    $("#" + element).removeClass('hidden');
}

function hideElement() {
    $(".item").addClass('hidden');
}

function getLessons(gradeId) {

    selectedGrade = gradeId;

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
                newElement = "<p style='margin-top: 10px'>درسی موجود نیست</p>";

            for(i = 0; i < response.length; i++) {
                newElement += "<div class='col-xs-12' style='margin-top: 10px'>";
                newElement += "<span>" + response[i].name + "</span>";
                newElement += "<button onclick='deleteLesson(\"" + response[i].id + "\")' class='btn btn-danger' data-toggle='tooltip' title='حذف درس'><span class='glyphicon glyphicon-remove' style='margin-left: 30%'></span></button>";
                newElement += "<button onclick='editLesson(\"" + response[i].id + "\", \"" + response[i].name + "\")' class='btn btn-primary' data-toggle='tooltip' title='ویرایش درس'><span class='glyphicon glyphicon-edit' style='margin-left: 30%'></span></button>";
                newElement += "</div>";
            }

            $("#lessons").append(newElement);

        }
    });
}

function editLesson(val, name) {
    selectedLesson = val;
    $("#lessonNameEdit").val(name);
    showElement('editLessonContainer');
}

function doEditLesson() {

    if($("#lessonNameEdit").val() == "")
        return;

    $.ajax({
        type: 'post',
        url: editLessonDir,
        data: {
            'lessonId': selectedLesson,
            'lessonName': $("#lessonNameEdit").val()
        },
        success: function (response) {
            if(response == "ok")
                document.location.href = lessons;
            else
                $("#errMsgEdit").append("پایه ی جدید در سامانه موجود است");
        }
    });
}

function deleteLesson(lessonId) {

    $.ajax({
        type: 'post',
        url: deleteLessonDir,
        data: {
            'lessonId': lessonId
        },
        success: function (response) {
            if(response == "ok") {
                getLessons(selectedGrade);
            }
        }
    });

}

function doAddNewLesson() {

    if($("#lessonName").val() == "")
        return;

    $.ajax({
        type: 'post',
        url: addNewLessonDir,
        data: {
            'lessonName': $("#lessonName").val(),
            'gradeId': selectedGrade
        },
        success: function (response) {
            if(response == "ok") {
                hideElement();
                getLessons(selectedGrade);
            }
            else
                $("#errMsg").append("درس مورد نظر در پایه ی تحصیلی مورد نظر وجود دارد");
        }
    });

}