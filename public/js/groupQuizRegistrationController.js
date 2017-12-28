
function studentsOfAdviserInQuiz(adviserId) {

    selectedAdviserId = adviserId;

    $.ajax({
        type: 'post',
        url: studentsOfAdviserInQuizDir,
        data: {
            'qId': quizId,
            'adviserId': adviserId
        },
        success: function (response) {

            if(response.length == 0)
                newElement = "دانش آموزی موجود نیست";
            else {

                response = JSON.parse(response);
                newElement = "";


                if(response.length == 0)
                    newElement = "دانش آموزی موجود نیست";
                else
                    newElement = "<p><span>تعداد کل:</span><span>&nbsp;</span><span>" + response.length + "</span></p>";

                for(i = 0; i < response.length; i++) {
                    if(response[i].online == 1)
                        newElement += "<center><span>" + response[i].firstName + " " + response[i].lastName + "</span> - <span>" + response[i].phoneNum + "</span> - <span>آنلاین</span></center>";
                    else
                        newElement += "<center><span>" + response[i].firstName + " " + response[i].lastName + "</span> - <span>" + response[i].phoneNum + "</span> - <span>حضوری</span></center>";
                }
            }

            $("#students").empty().append(newElement);
            $("#studentsPane").removeClass('hidden');
            $(".dark").removeClass('hidden');
        }
    });
}

function hideElement() {
    $(".item").addClass('hidden');
    $(".dark").addClass('hidden');
}

function register() {

    if($("#totalPrice").val() == "")
        return;

    $.ajax({
        type: 'post',
        url: totalRegister,
        data: {
            'adviserId': selectedAdviserId,
            'qId': quizId,
            'totalPrice': $("#totalPrice").val()
        },
        success: function (response) {
            if(response == "ok")
                hideElement();
        }
    });
    
}