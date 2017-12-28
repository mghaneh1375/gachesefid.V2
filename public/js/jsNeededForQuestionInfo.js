var currQ = 0;

$(document).ready(function () {
    showInfo();
});

function likeMouseEnterEvent(val) {
    if($(val).attr('data-val') == "unselected") {
        $(val).removeClass('fa-heart-o');
        $(val).addClass('fa-heart');
    }
    else {
        $(val).addClass('fa-heart-o');
        $(val).removeClass('fa-heart');
    }
}

function likeMouseLeaveEvent(val) {
    if($(val).attr('data-val') == "unselected") {
        $(val).addClass('fa-heart-o');
        $(val).removeClass('fa-heart');
    }
    else {
        $(val).removeClass('fa-heart-o');
        $(val).addClass('fa-heart');
    }
}

function nextQ() {
    if(currQ + 1 < limit) {
        currQ++;
        showInfo();
    }
}

function JUMP(idx) {
    if(idx >= 0 && idx < limit) {
        currQ = idx;
        showInfo();
    }
}

function prevQ() {
    if(currQ - 1 >= 0) {
        currQ--;
        showInfo();
    }
}

function likeQuestion(val) {

    if(currQ < 0 || currQ >= limit)
        return;

    $.ajax({
        type: 'post',
        url: likeQuestionDir,
        data: {
            'qId': $("#td_" + currQ).attr('data-val')
        },
        success: function (response) {
            if(response == "nok")
                return;
            showInfo();
        }
    })
}

function goToDiscussionRoom() {

    if(currQ < 0 || currQ >= limit)
        return;

    document.location.href = $("#discussion").attr('data-val');
}

function showInfo() {

    if(currQ < 0 || currQ >= limit)
        return;

    for(i = 0; i < limit; i++) {
        document.getElementById("td_" + i).style.backgroundColor = "white";
    }

    document.getElementById("td_" + currQ).style.backgroundColor = "yellow";

    $.ajax({
        type: 'post',
        url: getQuestionInfo,
        data: {
            'qId': $("#td_" + currQ).attr('data-val')
        },
        success: function (response) {

            $("#likesNo").empty();
            $("#msg").empty();
            $("#correctNo").empty();
            $("#incorrectNo").empty();
            $("#totalAns").empty();
            $("#whiteNo").empty();
            $("#percent").empty();
            $("#qLevel").empty();
            $("#subQInfo").empty();
            $("#likeDiv").empty();
            $("#controller").empty();
            $("#author").empty();

            if(currQ == 0)
                $("#prevQ").addClass('hidden');
            else
                $("#prevQ").removeClass('hidden');

            if(currQ == limit - 1)
                $("#nextQ").addClass('hidden');
            else
                $("#nextQ").removeClass('hidden');

            if(response == "nok") {
                $("#msg").append('مشکلی در نمایش سوال به وجود آمده است');
                $("#questionPane").css('background', 'none');
                $("#ansPane").css('background', 'none');
                return;
            }

            response = JSON.parse(response);

            $("#questionPane").css('background', 'url("' + response.questionFile + '")');
            $("#questionPane").css('background-repeat', 'no-repeat');
            $("#questionPane").css('background-size', '100% 100%');

            $("#likesNo").append(response.likeNo);
            $("#correctNo").append(response.correct);
            $("#incorrectNo").append(response.incorrect);
            $("#totalAns").append(response.correct + response.incorrect + response.white);
            $("#whiteNo").append(response.white);
            $("#percent").append((response.correct / (response.correct + response.incorrect + response.white) * 100).toFixed(2));
            $("#qLevel").append(response.level);
            $("#controller").append(response.controller);
            $("#author").append(response.author);

            $("#discussion").attr('data-val', response.discussion);
            
            if(response.ans == response.yourAns) {
                if(response.kindQ == 1)
                    $("#subQInfo").append("<p><span STYLE='float: right'>جواب شما:&nbsp;&nbsp;گزینه&nbsp;" + response.yourAns + "</span><span style='float: left'>جواب صحیح:&nbsp;&nbsp;گزینه&nbsp;" + response.ans + "&nbsp;&nbsp;</span><i class='fa fa-check' style='margin-right: 5px; color: darkgreen' aria-hidden='true'></i></p>");
                else
                    $("#subQInfo").append("<p><span STYLE='float: right'>جواب شما:&nbsp;&nbsp;" + response.yourAns + "</span><span style='float: left'>جواب صحیح:&nbsp;&nbsp;" + response.ans + "&nbsp;&nbsp;</span><i class='fa fa-check' style='margin-right: 5px; color: darkgreen' aria-hidden='true'></i></p>");
            }
            else {
                if(response.kindQ == 1)
                    $("#subQInfo").append("<p><span STYLE='float: right'>جواب شما:&nbsp;&nbsp;گزینه&nbsp;" + response.yourAns + "</span><span style='float: left'>جواب صحیح:&nbsp;&nbsp;گزینه&nbsp;" + response.ans + "&nbsp;&nbsp;</span><i class='fa fa-remove' style='margin-right: 5px; color: red' aria-hidden='true'></i></p>");
                else
                    $("#subQInfo").append("<p><span STYLE='float: right'>جواب شما:&nbsp;&nbsp;" + response.yourAns + "</span><span style='float: left'>جواب صحیح:&nbsp;&nbsp;" + response.ans + "&nbsp;&nbsp;</span><i class='fa fa-remove' style='margin-right: 5px; color: red' aria-hidden='true'></i></p>");
            }

            if(response.hasLike)
                $('#likeDiv').append('<i onclick="likeQuestion(this)" data-val="selected" onmouseleave="likeMouseLeaveEvent(this)" onmouseenter="likeMouseEnterEvent(this)" style="cursor: pointer; font-size: 20px" class="fa fa-heart" aria-hidden="true"></i>');
            else
                $('#likeDiv').append('<i onclick="likeQuestion(this)" data-val="unselected" onmouseleave="likeMouseLeaveEvent(this)" onmouseenter="likeMouseEnterEvent(this)" style="cursor: pointer; font-size: 20px" class="fa fa-heart-o" aria-hidden="true"></i>');
        }
    });
}