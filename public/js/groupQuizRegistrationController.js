
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
                    newElement = "<table><tr><td><center>تعداد کل:</center></td><td><center>" + response.length + "</center></td></tr>";

                for(i = 0; i < response.length; i++) {
                    if(response[i].online == 1)
                        newElement += "<tr><td><center>" + response[i].firstName + " " + response[i].lastName + "</center></td><td><center>" + response[i].phoneNum + "</center></td><td><center>آنلاین</center></td></tr>";
                    else
                        newElement += "<tr><td><center>" + response[i].firstName + " " + response[i].lastName + "</center></td><td><center>" + response[i].phoneNum + "</center></td><td><center>حضوری</center></td></tr>";
                }

                if(response.length > 0)
                    newElement += "</table>";
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
            if(response == "ok") {
                hideElement();
                location.reload();
            }
        }
    });
    
}