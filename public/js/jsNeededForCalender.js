function getEvents(val) {
    $.ajax({
        type: 'post',
        url: getEventDir,
        data: {
            'date': val
        },
        success: function (response) {

            response = JSON.parse(response);
            $("#events").empty();

            newElement = "";

            if(response != "empty") {
                for (i = 0; i < response.length; i++) {
                    newElement += "<div style='margin-top: 10px' class='col-xs-12'>";
                    newElement += "<button onclick='deleteEvent(" + response[i].id + ")' class='btn btn-danger' data-toggle='tooltip' title='حذف رویداد'>";
                    newElement += "<span class='glyphicon glyphicon-remove'></span></button>";
                    newElement += "<span style='margin-left: 5px'>" + response[i].event + "</span>";
                    newElement += "</div>";
                }
            }

            if(newElement == "")
                newElement = "<p>رویدادی موجود نیست</p>";

            newElement += "<button data-toggle='tooltip' title='افزودن رویداد جدید' onclick='showAddEvent()' style='margin-top: 10px' class='btn btn-info circleBtn'>";
            newElement += "<span class='glyphicon glyphicon-plus'></span>";
            newElement += "</button>";

            $("#events").append(newElement);
        }
    });
}

function showAddEvent() {
    $("#calenderContainer").css('visibility', 'visible');
}

function closeEventPrompt() {
    $("#calenderContainer").css('visibility', 'hidden');
}

function addEvent() {

    date = $("#date_input").val();

    if($("#eventDesc").val() == "" || date == "")
        return;

    $.ajax({
        type: 'post',
        url: addEventDir,
        data: {
            "date": date,
            "desc": $("#eventDesc").val()
        },
        success: function (response) {
            if(response == "ok") {
                getEvents(date);
                closeEventPrompt();
            }
        }
    });
}

function deleteEvent(id) {

    date = $("#date_input").val();

    if(date == "")
        return;

    $.ajax({
        type: 'post',
        url: deleteEventDir,
        data: {
            "id": id
        },
        success: function (response) {
            if(response == "ok") {
                getEvents(date);
            }
        }
    });
}