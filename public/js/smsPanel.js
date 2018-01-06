var selectedTemplateId;
var t = 0;

function getCities() {

    if($("#state").val() == -1) {
        $("#city").empty().append('<option value="-1">مهم نیست</option>');
        return;
    }

    $.ajax({
        type: 'post',
        url: getStates,
        data : {
            'stateId': $("#state").val()
        },
        success: function (response) {

            newElement = '<option value="-1">مهم نیست</option>';

            if(response.length > 0) {
                response = JSON.parse(response);

                for (i = 0; i < response.length; i++)
                    newElement += "<option value='" + response[i].id + "'>" + response[i].name + "</option>";
            }

            $("#city").empty().append(newElement);
        }
    });
}

function sendSMS() {

    if(t == 0 && $("#text").val() == "")
        return;

    $.ajax({
        type: 'post',
        url: sendSMSDir,
        data: {
            'stateId': $("#state").val(),
            'cityId': $("#city").val(),
            'quiz': $("#quiz").val(),
            'level': $("#level").val(),
            'sex': $("#sex").val(),
            'grade': $("#grade").val(),
            'point': $("#point").val(),
            'text': $("#text").val(),
            'templateId': $("#templateId").val(),
            'sendToAll': $("#sendToAll").prop('checked')
        },
        success: function (response) {
            if(response == "nok")
                alert("اشکالی در ارسال پیام رخ داده است" + " " + response);
            else if(response == "nok3")
                alert("کاربری با شرایط مورد نظر وجود ندارد");
            else {
                $("#progressDiv").removeClass('hidden');
                selectedTemplateId = response;
                getStatus();
            }
        }
    });

}

function getStatus() {
    $.ajax({
        type: 'post',
        url: sendSMSStatus,
        data: {
            'templateId': selectedTemplateId
        },
        success: function (response) {
            if(response == "finish") {
                document.location.href = profileDir;
            }
            else if(response == "nok") {
                $("#progress").empty().append('اشکالی در ارسال پیام رخ داده است');
            }
            else {
                $("#progress").empty().append(response);
                return getStatus();
            }
        }
    });
}

function changeTemplate() {

    val = $("#templateId").val();

    if(val == -1) {
        $(".customMsg").removeClass('hidden');
        t = 0;
    }
    else {
        $(".customMsg").addClass('hidden');
        t = 1;
    }
}