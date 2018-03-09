
var sortMode = "ASC";
var paneMode;

function accept() {

    var checkedValues = $("input:checkbox[name='selectedMsg[]']:checked").map(function() {
        return this.value;
    }).get();

    if(checkedValues.length <= 0)
        return;

    $.ajax({
        type: 'post',
        url: acceptMsgDir,
        data: {
            'selectedMsg': checkedValues
        },
        success: function (response) {
            if(response == "ok")
                document.location.href = selfPage;
        }
    });
}

function reject() {

    var checkedValues = $("input:checkbox[name='selectedMsg[]']:checked").map(function() {
        return this.value;
    }).get();

    if(checkedValues.length <= 0)
        return;

    $.ajax({
        type: 'post',
        url: rejectMsgDir,
        data: {
            'selectedMsg': checkedValues
        },
        success: function (response) {
            if(response == "ok")
                document.location.href = selfPage;
        }
    });
}

function pendingMode() {

    $(".messagingButton").removeClass('hidden');
    paneMode = "PENDING";
    $(".subMsgItems").addClass('hidden');
    $(".menu_bar").removeClass("selectedFolder");
    $("#pendingFolder").addClass("selectedFolder");

    showTable();
}

function acceptMode() {

    paneMode = "ACCEPTED";
    $(".operationButton").addClass('hidden');
    $("#rejectBtn").removeClass('hidden');
    $(".subMsgItems").addClass('hidden');
    $(".menu_bar").removeClass("selectedFolder");
    $("#acceptedFolder").addClass("selectedFolder");

    showTable();
}

function rejectMode() {

    paneMode = "REJECTED";
    $(".operationButton").addClass('hidden');
    $("#acceptBtn").removeClass('hidden');
    $(".subMsgItems").addClass('hidden');
    $(".menu_bar").removeClass("selectedFolder");
    $("#rejectedFolder").addClass("selectedFolder");

    showTable();
}

function sortByDate() {

    if(sortMode == "DESC")
        sortMode = "ASC";
    else
        sortMode = "DESC";

    showTable();
}

function closePrompt() {
    $(".subMsgItems").addClass('hidden');
}

function confirmationBeforeSubmit() {
    $(".dark").removeClass('hidden');
    $("#deleteMsg").removeClass('hidden');
}

function hideConfirmationPane() {
    $("#deleteMsg").addClass('hidden');
    $(".dark").addClass('hidden');
}

function showConfirmationForDelete() {

    var checkedValues = $("input:checkbox[name='selectedMsg[]']:checked").map(function() {
        return this.value;
    }).get();

    if(checkedValues.length > 0)
        confirmationBeforeSubmit();
}

function showMsg(id) {

    $("#showMsgContainer").empty();

    $.ajax({

        type: 'post',
        url: 'getMessage',
        data: {
            mId: id
        },
        success: function (response) {

            response = JSON.parse(response);

            newElement = "<div onclick='$(\".dark\").addClass(\"hidden\"); closePrompt()' class='ui_close_x'></div>";
            newElement += "<div class='header_text'> موضوع :  " + response.subject + "</div>";
            newElement += "<div class='header_text'> ارسال شده از طرف  :  " + response.senderId + "</div>";
            newElement += "<div class='header_text'> ارسال شده به  :  " + response.receiverId + "</div>";
            newElement += "<div class='header_text'> تاریخ ارسال  :  " + response.date + "</div>";
            newElement += "<div class='subheader_text'> متن پیام: " + response.message + "</div>";

            $("#showMsgContainer").append(newElement).removeClass('hidden');
            $(".dark").removeClass('hidden');
        }

    });
}

function showTable() {

    $("#tableId").empty();
    var url;

    if(paneMode == "PENDING")
        url = getPendingMsgs;
    else if(paneMode == "ACCEPTED")
        url = getAccpetedMsgs;
    else
        url = getRejectedMsgs;

    $.ajax({

        type: 'post',
        url: url,
        data: {
            'sortMode' : sortMode
        },
        success: function (response) {

            response = JSON.parse(response);

            if(response.length == 0) {
                newElement = "<tr class='bottomNav'>";
                newElement += "<td align='right' class='p5' colspan='4'>";
                newElement += "هیچ پیامی موجود نیست";
                newElement += "</td></tr>";
                $("#tableId").append(newElement);
            }

            else {
                for(i = 0; i < response.length; i++) {
                    newElement = "<tr style='cursor: pointer'  class='bottomNav'>";
                    newElement += "<td onclick='showMsg(" + response[i].id + ")' style='width: 15%; text-align: center'>" + response[i].target + '</td>';
                    newElement += "<td onclick='showMsg(" + response[i].id + ")' style='width: 55%; text-align: center'>" + response[i].subject + "</td>";
                    newElement += "<td onclick='showMsg(" + response[i].id + ")' style='width: 15%; text-align: center'>" + response[i].date + "</td>";
                    newElement += "<td style='text-align: center'>";
                    newElement += "<input name='selectedMsg[]' value='" + response[i].id + "' type='checkbox'></td></tr>";
                    $("#tableId").append(newElement);
                }
            }
        }
    });
}