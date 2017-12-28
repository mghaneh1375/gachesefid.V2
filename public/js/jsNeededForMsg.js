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

function showMsg(id, element, mode) {
    $("#" + element).empty();

    $.ajax({

        type: 'post',
        url: 'getMessage',
        data: {
            mId: id
        },
        success: function (response) {
            response = JSON.parse(response);

            newElement = "<div onclick='closePrompt(\"" + element + "\")' class='ui_close_x'></div>";
            newElement += "<div class='header_text'> موضوع :  " + response.subject + "</div>";
            if(mode)
                newElement += "<div class='header_text'> ارسال شده از طرف  :  " + response.senderId + "</div>";
            else
                newElement += "<div class='header_text'> ارسال شده به  :  " + response.recieverId + "</div>";
            newElement += "<div class='header_text'> تاریخ ارسال  :  " + response.date + "</div>";
            newElement += "<div class='subheader_text'>" + response.message + "</div>";

            $("#" + element).append(newElement);
            $("#" + element).css("visibility", 'visible');
        }

    });
}

function showTable(element, mode) {

    $("#" + element).empty();

    $.ajax({

        type: 'post',
        url: getListOfMsgs,
        data: {
            'mode': mode,
            'sortMode' : sortMode
        },
        success: function (response) {

            response = JSON.parse(response);

            if(response.length == 0) {
                newElement = "<tr class='bottomNav'>";
                newElement += "<td align='right' class='p5' colspan='4'>";
                newElement += "هیچ پیامی موجود نیست";
                newElement += "</td></tr>";
                $("#" + element).append(newElement);
            }

            else {
                for(i = 0; i < response.length; i++) {
                    newElement = '<tr class="bottomNav">';
                    newElement += '<td style="width: 15%; text-align: center">' + response[i].target + '</td>';
                    newElement += "<td onclick='showMsg(" + response[i].id + ", \"showMsgContainer\", " + mode + ")' style='cursor: pointer; width: 55%; text-align: center'>" + response[i].subject + "</td>";
                    newElement += "<td style='width: 15%; text-align: center'>" + response[i].date + "</td>";
                    newElement += "<td style='text-align: center'>";
                    newElement += "<input name='selectedMsg[]' value='" + response[i].id + "' type='checkbox'></td></tr>";
                    $("#" + element).append(newElement);
                }
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