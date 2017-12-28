var selectedGrade;

function hideElement() {
    $("#msg").empty();
    $(".item").addClass('hidden');
}

function showElement(element) {
    $(".item").addClass('hidden');
    $("#" + element).removeClass('hidden');
}

function doAddNewGrade() {

    $("#msg").empty();

    if($("#gradeName").val() == "") {
        $("#msg").append("لطفا نام پایه ی مورد نظر خود را وارد نمایید");
        return;
    }

    $.ajax({
        type: 'post',
        url: addGrade,
        data: {
            'gradeName' : $("#gradeName").val()
        },
        success: function (response) {
            if(response == "ok")
                document.location.href = grades;
            else {
                $("#msg").append("پایه ی مورد نظر در سامانه موجود است");
            }
        }
    });
}

function editGrade(val, name) {
    selectedGrade = val;
    $("#gradeNameEdit").val(name);
    showElement('editGradeContainer');
}

function doEditGrade() {
    if($("#gradeNameEdit").val() == "")
        return;

    $.ajax({
        type: 'post',
        url: editGradeDir,
        data: {
            'gradeId': selectedGrade,
            'gradeName': $("#gradeNameEdit").val()
        },
        success: function (response) {
            if(response == "ok")
                document.location.href = grades;
            else
                $("#errMsg").append("پایه ی جدید در سامانه موجود است");
        }
    });
}

function deleteGrade(val) {
    
    $.ajax({
        type: 'post',
        url: deleteGradeDir,
        data: {
            gradeId: val
        },
        success: function (response) {
            if(response == "ok")
                document.location.href = grades;
        }
    });
}