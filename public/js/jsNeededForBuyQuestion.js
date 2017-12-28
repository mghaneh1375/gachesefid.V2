var boxes = [];
var questions = [];
var totalPrice = 0;
var totalTotalPrice = 0;

function goBack() {

    $(".receipt").removeClass('hidden');

    newElement = "";

    for(i = 0; i < questions.length; i++) {

        totalTmp = 0;

        for(j = 0; j < questions[i].length; j++) {
            totalTmp += parseInt(questions[i][j].price);
        }

        newElement += "<p><span>جعبه </span><span>" + (i + 1) + "</span><span>:&nbsp;</span><span>" + totalTmp + "</span></p>";
    }

    newElement += "<p><span>جمع کل:&nbsp;</span><span>" + totalTotalPrice + "</span></p>";

    if(totalTotalPrice > 0) {
        newElement += "<button onclick='goToPreTransaction()' class='btn btn-success'>پرداخت</button>";
    }

    $("#totalPrice").empty().append(newElement).persiaNumber();
    $(".addBox").addClass('hidden');
}

function addBox() {
    $(".receipt").addClass('hidden');
    $(".addBox").removeClass('hidden');
    getLessons();
}

function getLessons() {

    $.ajax({
        type: 'post',
        url: getLessonsDir,
        data: {
            'gradeId': $("#grades").val()
        },
        success: function (response) {

            $("#lessons").empty();

            newElement = "<option value='-1'>مهم نیست</option>";

            if(response.length != 0) {
                response = JSON.parse(response);
                for (i = 0; i < response.length; i++) {
                    newElement += "<option value='" + response[i].id + "'>" + response[i].name + "</option>";
                }
            }

            $("#lessons").append(newElement);
            getSubjects();
        }
    });
}

function getSubjects() {

    $.ajax({
        type: 'post',
        url: getSubjectsDir,
        data: {
            'lessonId': $("#lessons").val()
        },
        success: function (response) {

            $("#subjects").empty();

            newElement = "<option value='-1'>مهم نیست</option>";

            if(response.length != 0) {

                response = JSON.parse(response);

                for (i = 0; i < response.length; i++) {
                    newElement += "<option value='" + response[i].id + "'>" + response[i].name + "</option>";
                }
            }

            $("#subjects").append(newElement);
        }
    });
}

function doAddBox() {

    if($("#qNo").val().length == 0) {
        $("#err").empty().append('لطفا تعداد سوالات مورد نظر خود را وارد کنید');
        return;
    }

    for(i = 0; i < boxes.length; i++) {
        if(boxes[i].gradeId == $("#grades").val() && (boxes[i].lId == $("#lessons").val() || boxes[i].lId == -1)) {
            $("#err").empty().append('درس مورد نظر در جعبه های ایجاد شده موجود است');
            return;
        }
    }

    $.ajax({
        type: 'post',
        url: getSuggestionQuestionsCount,
        data: {
            'gradeId': $("#grades").val(),
            'lId': $("#lessons").val(),
            'sId': $("#subjects").val(),
            'level': $("#level").val(),
            'needed': $("#qNo").val(),
            'like': ($("#sort").is(':checked')) ? 1 : 0
        },
        success: function (response) {


            response = JSON.parse(response);

            if(response.length == 0) {
                $("#err").empty().append('تعداد سوالات بانک به اندازه مورد نظر نمی رسد');
                return;
            }

            totalPrice = 0;
            tmpIdx = questions.length;
            questions[tmpIdx] = [];

            for(i = 0; i < response.length; i++) {
                questions[tmpIdx][i] = {
                    'subject': response[i].subjectName,
                    'lesson': response[i].lessonName,
                    'price': parseInt(response[i].price),
                    'level': response[i].level,
                    'id': response[i].qId
                };

                totalPrice += parseInt(response[i].price);
            }

            totalTotalPrice += parseInt(totalPrice);

            boxes[boxes.length] = {
                'gradeId': $("#grades").val(),
                'lId': $("#lessons").val(),
                'sId': $("#subjects").val(),
                'level': $("#level").val(),
                'needed': $("#qNo").val(),
                'name': 'جعبه ' + (boxes.length + 1),
                'like': ($("#sort").is(':checked')) ? true : false,
                'grade': $("#grades").find(":selected").text(),
                'lesson': ($("#lessons").val() == -1) ? 'مهم نیست' : $("#lessons").find(":selected").text(),
                'subject': ($("#subjects").val() == -1) ? 'مهم نیست' : $("#subjects").find(":selected").text()
            };

            showBoxes();
        }
    });
}

function showBoxes() {

    newElement = "";

    for(i = 0; i < boxes.length; i++) {
        newElement += "<div style='float: right; padding: 10px; border-radius: 6px; margin-right: 6px; border: 2px solid black; margin-top: 10px'><span style='padding: 5px; cursor: pointer' onclick='removeBox(\"" + i + "\")' class='glyphicon glyphicon-remove'></span><span style='padding: 5px; cursor: pointer' onclick='infoBox(\"" + i + "\")' class='glyphicon glyphicon-info-sign'></span><span>" + boxes[i].name + "</span></div>";
    }

    $("#boxes").empty().append(newElement);
    goBack();
}

function infoBox(idx) {

    $(".dark").removeClass('hidden');

    $("#boxInfo").removeClass('hidden');

    $("#boxName").empty().append(boxes[idx].name);

    $("#boxGrade").empty().append(boxes[idx].grade);

    $("#boxLesson").empty().append(boxes[idx].lesson);

    $("#boxSubject").empty().append(boxes[idx].subject);

    $("#boxLike").empty();
    if(boxes[idx].like)
        $("#boxLike").append('<i class="fa fa-check" aria-hidden="true"></i>');
    else
        $("#boxLike").append('<i class="fa fa-times" aria-hidden="true"></i>');

    $("#boxQNo").empty().append(boxes[idx].needed);

    $("#questionsDiv").empty();
    newElement = "";
    for(i = 0; i < questions[idx].length; i++) {
        newElement += "<div><span> درس: " + questions[idx][i].lesson + " - </span>";
        newElement += "<span> مبحث: " + questions[idx][i].subject + " - </span>";
        newElement += "<span> قیمت: " + questions[idx][i].price + " - </span>";
        newElement += "<span> سطح سختی " + questions[idx][i].level + "</span></div>";
    }
    $("#questionsDiv").append(newElement);
}

function goToPreTransaction() {

    qIds = [];
    counter = 0;

    for(idx = 0; idx < boxes.length; idx++) {
        for (i = 0; i < questions[idx].length; i++) {
            qIds[counter++] = questions[idx][i].id;
        }
    }

    $.ajax({
        type: 'post',
        url: preTransactionDir,
        data: {
            'toPay': totalTotalPrice,
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

function removeBox(idx) {

    if(idx > -1) {

        total = 0;

        for(i = 0; i < questions[idx].length; i++) {
            total += questions[idx][i].price;
        }

        totalTotalPrice -= total;

        questions.splice(idx, 1);
        boxes.splice(idx, 1);


        newElement = "";

        for(i = 0; i < questions.length; i++) {

            totalTmp = 0;
            for(j = 0; j < questions[i].length; j++)
                totalTmp += questions[i][j].price;

            newElement += "<p><span>جعبه </span><span>" + (i + 1) + "</span><span>:&nbsp;</span><span>" + totalTmp + "</span></p>";
        }

        newElement += "<p><span>جمع کل:&nbsp;</span><span>" + totalTotalPrice + "</span></p>";

        if(totalTotalPrice > 0) {
            newElement += "<button onclick='goToPreTransaction()' class='btn btn-success'>پرداخت</button>";
        }

        $("#totalPrice").empty().append(newElement).persiaNumber();

        showBoxes();
    }

}