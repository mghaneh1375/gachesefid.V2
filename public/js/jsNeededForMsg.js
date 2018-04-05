var mode = false;

var sortMode = "DESC";
var paneMode = "INBOX";

function setAllChecked() {

    if(!mode) {
        $("input:checkbox[name='selectedMsg[]']").each(function() {
            this.checked = true;
        });
        $("#selectAll").text("غیر فعال کردن همه");
        $("#selectAllImg").attr('src', '/gachesefid/public/images/deselectAll.gif');
    }
    else {
        $("input:checkbox[name='selectedMsg[]']").each(function() {
            this.checked = false;
        });
        $("#selectAll").text("فعال کردن همه");
        $("#selectAllImg").attr('src', '/gachesefid/public/images/selectAll.gif');
    }

    mode = !mode;
}

function closePrompt(element) {
    $("#" + element).css("visibility", "hidden");
}

function deleteMsg() {

    var checkedValues = $("input:checkbox[name='selectedMsg[]']:checked").map(function() {
        return this.value;
    }).get();

    if(checkedValues.length <= 0)
        return;

    $.ajax({
        type: 'post',
        url: deleteMsgDir,
        data: {
            'selectedMsg': checkedValues
        },
        success: function (response) {
            if(response == "ok")
                document.location.href = messageDir;
        }
    });
}

function sendMode(sendFolder, inbox, msgContainer, showMsgContainer) {
    paneMode = "SEND";
    $("#" + showMsgContainer).css("visibility", 'hidden');
    $("#" + inbox).css("visibility", 'hidden');
    $(".menu_bar").removeClass("selectedFolder");
    $("#" + sendFolder).addClass("selectedFolder");
    $("#" + msgContainer).css("visibility", 'visible');
}

function inboxMode(inboxFolder, inbox, table, msgContainer, showMsgContainer) {
    paneMode = "INBOX";
    $("#" + showMsgContainer).css("visibility", 'hidden');
    $("#" + inbox).css("visibility", "visible");
    $(".menu_bar").removeClass("selectedFolder");
    $("#" + inboxFolder).addClass("selectedFolder");
    $("#" + msgContainer).css("visibility", 'hidden');

    showTable(table, true);
}

function outboxMode(outboxFolder, inbox, table, msgContainer, showMsgContainer) {
    paneMode = "OUTBOX";
    $("#" + showMsgContainer).css("visibility", 'hidden');
    $("#" + msgContainer).css("visibility", 'hidden');
    $("#" + inbox).css("visibility", "visible");
    $(".menu_bar").removeClass("selectedFolder");
    $("#" + outboxFolder).addClass("selectedFolder");
    showTable(table, false);
}

function confirmationBeforeSubmit() {
    $(".dark").removeClass('hidden');
    $("#deleteMsg").css('visibility', 'visible');
}

function hideConfirmationPane() {
    $("#deleteMsg").css('visibility', 'hidden');
    $(".dark").addClass('hidden');
}

function showConfirmationForDelete() {

    var checkedValues = $("input:checkbox[name='selectedMsg[]']:checked").map(function() {
        return this.value;
    }).get();

    if(checkedValues.length > 0)
        confirmationBeforeSubmit();
}

function showMsg(id, element, mode) {

    if(!$("#" + element).hasClass('hidden')) {
        $("#" + element).empty().addClass('hidden');
        return;
    }

    $('.messageTR').empty().addClass('hidden');

    $("#" + element).empty();
    var newElement = "";

    $.ajax({

        type: 'post',
        url: 'getMessage',
        data: {
            mId: id
        },
        success: function (response) {
            response = JSON.parse(response);
            newElement += "<td style='width: 20%; text-align: center; color: #963019'> وضعیت پیام  :  " + ((response.status == 1) ? "تایید شده" : "تایید نشده") + "</td>";
            newElement += "<td style='width: 60%; text-align: center'> متن پیام: " + response.message + "</td>";
            newElement += "<td style='width: 20%; text-align: center'>";
            if(mode) {
                newElement += "<button onclick='$(\"#destUserSendMsg\").val(\"" + response.senderId + "\"); $(\"#subjectSendMsg\").val(\"Re: " + response.subject + "\"); sendMode(\"sendFolder\", \"inbox\", \"sendMsgDiv\", \"showMsgContainer\");' class='btn btn-warning'><span>پاسخ به </span><span>" + response.senderId + "</span></button></td>";
            }
            else {
                newElement += "<button onclick='$(\"#destUserSendMsg\").val(\"" + response.receiverId + "\"); $(\"#subjectSendMsg\").val(\"Re: " + response.subject + "\"); sendMode(\"sendFolder\", \"inbox\", \"sendMsgDiv\", \"showMsgContainer\");' class='btn btn-warning'><span>پاسخ به </span><span>" + response.receiverId + "</span></button></td>";
            }

            $("#" + element).empty().append(newElement).removeClass('hidden');
        }

    });
}

function showTable(element, mode) {

    var newElement = "";

    $.ajax({

        type: 'post',
        url: getListOfMsgs,
        data: {
            'mode': mode,
            'sortMode' : sortMode,
            'selectedUser': selectedUser
        },
        success: function (response) {

            response = JSON.parse(response);

            if(response.length == 0) {
                newElement = "<tr class='bottomNav'>";
                newElement += "<td align='right' class='p5' colspan='4'>";
                newElement += "هیچ پیامی موجود نیست";
                newElement += "</td></tr>";
                $("#" + element).empty().append(newElement);
            }

            else {
                for(i = 0; i < response.length; i++) {
                    newElement += "<tr onclick='showMsg(" + response[i].id + ", \"row_" + i + "\", " + mode + ")' style='cursor: pointer' class='bottomNav'>";
                    newElement += "<td style='width: 15%; text-align: center'>" + response[i].target + '</td>';
                    newElement += "<td style='width: 55%; text-align: center'>" + response[i].subject + "</td>";
                    newElement += "<td style='width: 15%; text-align: center'>" + response[i].date + "</td>";
                    newElement += "<td style='width: 15%; text-align: center'>";
                    newElement += "<input name='selectedMsg[]' value='" + response[i].id + "' type='checkbox'></td></tr><tr class='messageTR hidden' style='height: 200px; background-color: white; overflow: auto' id='row_" + i + "'></tr>";
                }
                $("#" + element).empty().append(newElement);
            }
        }
    });

}

function sortByDate() {

    if(sortMode == "DESC")
        sortMode = "ASC";
    else
        sortMode = "DESC";
    
    if(paneMode == "INBOX")
        inboxMode('inboxFolder', 'inbox', 'tableId', 'outbox', 'sendMsgDiv', 'showMsgContainer');
    else if(paneMode == "OUTBOX")
        outboxMode('outboxFolder', 'inbox', 'tableId', 'sendMsgDiv', 'showMsgContainer')
}