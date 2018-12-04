var boxes = [];
var questions = [];
var totalPrice = 0;
var selectedGrade;
var selectedLesson;

function addGradeBox(id, name) {

    $(".item").addClass('hidden');

    for(i = 0; i < boxes.length; i++) {
        if (boxes[i].filter == 'grade' && boxes[i].id == id) {
            $("#errAddBox").removeClass('hidden');
            $(".dark").removeClass('hidden');
            return;
        }

        if(boxes[i].selectedGrade == id) {
            $("#errAddBox2").removeClass('hidden');
            $(".dark").removeClass('hidden');
            return;
        }
    }

    boxes[boxes.length] = {
        'id': id,
        'filter': 'grade',
        'name': name,
        'needed': 0,
        'like': false,
        'price': 0
    };

    addNewEntry(name, id);
}

function showRecipe() {

    price = 0;
    newElement = "";

    for(i = 0; i < boxes.length; i++) {
        if(boxes[i].needed > 0) {
            price += boxes[i].price;
            newElement += "<div><span>" + boxes[i].needed + "</span><span> تا </span>";
            newElement += "<span>" + boxes[i].name + "</span><span>&nbsp;</span>";
            newElement += "<span>" + boxes[i].price + "</span><span> تومان </span></div>";
        }
    }

    newElement += "<center><span>جمع کل:</span><span>&nbsp;</span><span>" + price + "</span></center>";

    if(price > 0)
        $("#transactionBtn").removeAttr('disabled');
    else
        $("#transactionBtn").attr('disabled', 'disabled');

    $("#recipeBody").empty().append(newElement);
    $(".dark").removeClass('hidden');
    $(".item").addClass('hidden');
    $("#recipe").removeClass('hidden');
}

function addLessonBox(id, name) {

    $(".item").addClass('hidden');

    for (i = 0; i < boxes.length; i++) {
        
        if (boxes[i].filter == 'lesson' && boxes[i].id == id) {
            $("#errAddBox").removeClass('hidden');
            $(".dark").removeClass('hidden');
            return;
        }
        
        if(boxes[i].id == selectedGrade || boxes[i].selectedLesson == id) {
            $("#errAddBox2").removeClass('hidden');
            $(".dark").removeClass('hidden');
            return;
        }
    }

    boxes[boxes.length] = {
        'id': id,
        'filter': 'grade',
        'name': name,
        'needed': 0,
        'like': false,
        'price': 0,
        'selectedGrade': selectedGrade
    };

    addNewEntry(name, id);
}

function addSubjectBox(id, name) {

    $(".item").addClass('hidden');

    for (i = 0; i < boxes.length; i++) {
        if (boxes[i].filter == 'subject' && boxes[i].id == id) {
            $("#errAddBox").removeClass('hidden');
            $(".dark").removeClass('hidden');
            return;
        }
        if(boxes[i].id == selectedLesson || boxes[i].id == selectedGrade) {
            $("#errAddBox2").removeClass('hidden');
            $(".dark").removeClass('hidden');
            return;
        }
    }

    boxes[boxes.length] = {
        'id': id,
        'filter': 'grade',
        'name': name,
        'needed': 0,
        'like': false,
        'price': 0,
        'selectedGrade': selectedGrade,
        'selectedLesson': selectedLesson
    };

    addNewEntry(name, id);
}

function getSuggest(idx, newVal) {

    $.ajax({
        type: 'post',
        url: getSuggestionQuestionsCount,
        data: {
            'id': boxes[idx].id,
            'filter': boxes[idx].filter,
            'level': boxes[idx].level,
            'needed': newVal
            // 'like': ($("#sort").is(':checked')) ? 1 : 0
        },
        success: function (response) {

            $(".item").addClass('hidden');

            response = JSON.parse(response);

            if(response.length == 0) {
                $("#errAddBox3").removeClass('hidden');
                $("#needed_" + boxes[idx].id).val(0);
                $(".dark").removeClass('hidden');
                return;
            }

            totalPrice = 0;
            tmpArr = [];

            for(i = 0; i < response.length; i++) {
                tmpArr[i] = {
                    'subject': response[i].subjectName,
                    'lesson': response[i].lessonName,
                    'price': parseInt(response[i].price),
                    'level': response[i].level,
                    'id': response[i].qId
                };

                totalPrice += parseInt(response[i].price);
            }

            allow = true;

            for(i = 0; i < questions.length; i++) {
                if(questions[i].boxIdx == idx) {
                    questions[i].arr = tmpArr;
                    allow = false;
                }
            }

            if(allow) {
                questions[questions.length] = {
                    'boxIdx': idx,
                    'arr': tmpArr
                };
            }


            boxes[idx].needed = newVal;
            boxes[idx].price = parseInt(totalPrice);
        }
    });
}

function changeQNo(id, newVal) {
    
    for(i = 0; i < boxes.length; i++) {
        if(boxes[i].id == id) {
            getSuggest(i, newVal);
        }
    }

}

function changeLevel(id, newVal) {

    for(i = 0; i < boxes.length; i++) {
        if(boxes[i].id == id) {
            boxes[i].level = newVal;
            return getSuggest(i, boxes[i].needed);
        }
    }
}

function addNewEntry(name, id) {

    newElement = "<p id='addedBox_" + id + "'><span> " + name + " - </span>تعداد: <input id='needed_" + id + "' style='max-width: 100px' onchange='changeQNo(\"" + id + "\", this.value)' type='number' min='0' value='0'>";
    newElement += "<span>&nbsp;</span><span>سطح سختی: <select onchange='changeLevel(\"" + id + "\", this.value)'>";
    newElement += "<option value='-1'>انتخاب تصادفی</option><option value='1'>ساده</option><option value='2'>متوسط</option><option value='3'>دشوار</option></select></span>";
    newElement += "<span>&nbsp;&nbsp;</span><span onclick='removeBox(\"" + id + "\")' class='btn btn-danger' data-toggle='tooltip' title='حذف از لیست'><span class='glyphicon glyphicon-remove'></span></span></p>";

    $("#boxes").append(newElement);
}

function removeBox(id) {

    for(i = 0; i < boxes.length; i++) {
        if(boxes[i].id == id) {
            $("#addedBox_" + id).remove();
            boxes.splice(i, 1);
            for(j = 0; j < questions.length; j++) {
                if(questions[j].boxIdx == i) {
                    questions.splice(j, 1);
                    return;
                }
            }
        }
    }
}

function openGrade(gradeId) {

    selectedGrade = gradeId;
    status = $("#grade_" + gradeId).attr('data-status');

    $(".SubjectClass").addClass('hidden').attr('data-status', 'close');
    $(".LessonClass").addClass('hidden').attr('data-status', 'close');
    $(".addGradeText").removeClass('hidden');
    $(".addLessonText").removeClass('hidden');
    $(".addSubjectText").removeClass('hidden');

    if(status == "open") {
        $("#grade_" + gradeId).attr('data-status', 'close').addClass('hidden');
        $("#add_" + gradeId).removeClass('hidden');
        return;
    }
    else {

        $("#grade_" + gradeId).removeClass('hidden').attr('data-status', 'open');
        $("#add_" + gradeId).addClass('hidden');

        if($("#grade_" + gradeId).attr('data-repeat') == "true")
            return;

        $("#grade_" + gradeId).attr('data-repeat', 'true');
    }

    $.ajax({
        type: 'post',
        url: getLessonsDir,
        data: {
            'gradeId': gradeId
        },
        success: function (response) {

            newElement = "";

            if(response.length != 0) {

                response = JSON.parse(response);

                if(response.length == 0)
                    newElement += "<span>درسی موجود نیست</span><span class='addLessonText'><span>&nbsp;&nbsp;&nbsp;</span></span>";

                for (i = 0; i < response.length; i++) {
                    newElement += "<li><span onclick='openLesson(\"" + response[i].id + "\")'>" + response[i].name + "</span><span class='addLessonText' id='addL_" + response[i].id + "'><span>&nbsp;&nbsp;&nbsp;</span><span onclick='addLessonBox(\"" + response[i].id + "\", \"" + response[i].name + "\")' class='add'>افزودن به لیست</span></span>";
                    newElement += "<ul class='SubjectClass' data-repeat='false' data-status='close' id='lesson_" + response[i].id + "'></ul></li>";
                }
            }
            else
                newElement += "<span>درسی موجود نیست</span><span class='addLessonText'><span>&nbsp;&nbsp;&nbsp;</span></span>";

            $("#grade_" + gradeId).empty().append(newElement);
        }
    });
}

function openLesson(lessonId) {

    selectedLesson = lessonId;
    $(".SubjectClass").addClass('hidden').attr('data-status', 'close');
    $(".add").attr('data-status', 'close');
    $(".addLessonText").removeClass('hidden');
    $(".addSubjectText").removeClass('hidden');

    if($("#lesson_" + lessonId).attr('data-status') == "open") {
        $("#lesson_" + lessonId).attr('data-status', 'close').addClass('hidden');
        $("#addL_" + lessonId).removeClass('hidden');
        return;
    }
    else {

        $("#lesson_" + lessonId).removeClass('hidden').attr('data-status', 'open');
        $("#addL_" + lessonId).addClass('hidden');

        if($("#lesson_" + lessonId).attr('data-repeat') == "true")
            return;

        $("#lesson_" + lessonId).attr('data-repeat', 'true');
    }

    $.ajax({
        type: 'post',
        url: getSubjectsDir,
        data: {
            'lessonId': lessonId
        },
        success: function (response) {

            newElement = "";

            if(response.length != 0) {

                response = JSON.parse(response);

                if(response.length == 0)
                    newElement += "<span>مبحثی موجود نیست</span><span class='addSubjectText'><span>&nbsp;&nbsp;&nbsp;</span></span>";

                for (i = 0; i < response.length; i++) {
                    newElement += "<span>" + response[i].name + "</span><span class='addSubjectText'><span>&nbsp;&nbsp;&nbsp;</span><span onclick='addSubjectBox(\"" + response[i].id + "\", \"" +response[i].name  + "\")' class='add'>افزودن به لیست</span></span>";
                }
            }
            else
                newElement += "<span>مبحثی موجود نیست</span><span class='addSubjectText'><span>&nbsp;&nbsp;&nbsp;</span></span>";

            $("#lesson_" + lessonId).empty().append(newElement);
        }
    });
}

function goToPreTransaction() {

    qIds = [];
    price = 0;
    counter = 0;

    for(idx = 0; idx < boxes.length; idx++) {
        price += boxes[idx].price;
        for (i = 0; i < questions.length; i++) {
            if(questions[i].boxIdx == idx) {
                for(j = 0; j < questions[i].arr.length; j++)
                    qIds[counter++] = questions[i].arr[j].id;
                break;
            }
        }
    }

    $.ajax({
        type: 'post',
        url: preTransactionDir,
        data: {
            'toPay': price,
            'qIds': qIds
        },
        success: function (response) {

            if(response == "nok") {
                $("#errMsg").empty().append("خطایی در پرداخت رخ داده است").removeClass('hidden');
            }
            else {
                document.location.href = homeDir + "/preTransactionBuyQuestion/" + response;
            }
        }
    });
}