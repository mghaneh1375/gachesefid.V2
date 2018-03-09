
var selectedId = -1;

function doAddNewAdviserQuestion() {

    if($("#questionId").val() == "")
        return;

    $.ajax({
        type: 'post',
        url: addAdviserQuestion,
        data: {
            'question': $("#questionId").val()
        },
        success: function(response) {

            if(response == "ok")
                document.location.href = adviserQuestions;
            else
                $("#err").empty().append('مشکلی در انجام عملیات مورد نظر رخ داده است');
        }
    });

}

function editQuestion(id, name) {

    selectedId = id;

    $("#editQuestionId").val(name);
    $('.dark').removeClass('hidden');
    $('#editAdviserQuestionContainer').removeClass('hidden');
}

function doEditAdviserQuestion() {

    if(selectedId == -1 || $("#editQuestionId").val() == "") {
        return;
    }

    $.ajax({
        type: 'post',
        url: editAdviserQuestion,
        data: {
            'qId': selectedId,
            'question': $("#editQuestionId").val()
        },
        success: function(response) {

            if(response == "ok")
                document.location.href = adviserQuestions;
            else
                $("#err2").empty().append('مشکلی در انجام عملیات مورد نظر رخ داده است');
        }
    });

}