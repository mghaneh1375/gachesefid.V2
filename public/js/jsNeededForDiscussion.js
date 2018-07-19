var currPage;


var Preview2 = {
    delay: 150,        // delay after keystroke before updating

    preview: null,     // filled in by Init below
    buffer: null,      // filled in by Init below
    src: null,
    timeout: null,     // store setTimout id
    mjRunning: false,  // true when MathJax is processing
    oldText: null,     // used to check if an update is needed

    //
    //  Get the preview and buffer DIV's
    //
    Init: function (srcArr, bufferDivArr) {
        this.src = srcArr;
        this.preview = bufferDivArr;
        this.buffer = bufferDivArr;
    },

    //
    //  Switch the buffer and preview, and display the right one.
    //  (We use visibility:hidden rather than display:none since
    //  the results of running MathJax are more accurate that way.)
    //
    SwapBuffers: function () {
        // var buffer = this.preview, preview = this.buffer;
        // this.buffer = buffer; this.preview = preview;
        // buffer.style.visibility = "hidden"; buffer.style.position = "absolute";
        // preview.style.position = ""; preview.style.visibility = "";
    },

    //
    //  This gets called when a key is pressed in the textarea.
    //  We check if there is already a pending update and clear it if so.
    //  Then set up an update to occur after a small delay (so if more keys
    //    are pressed, the update won't occur until after there has been
    //    a pause in the typing).
    //  The callback function is set up below, after the Preview object is set up.
    //
    Update: function () {
        if (this.timeout) {clearTimeout(this.timeout)}
        this.timeout = setTimeout(this.callback,this.delay);
    },

    //
    //  Creates the preview and runs MathJax on it.
    //  If MathJax is already trying to render the code, return
    //  If the text hasn't changed, return
    //  Otherwise, indicate that MathJax is running, and start the
    //    typesetting.  After it is done, call PreviewDone.
    //
    CreatePreview: function () {

        Preview2.timeout = null;
        if (this.mjRunning) return;
        for(i = 0; i < this.src.length; i++) {
            var text = document.getElementById(this.src[i]).value;
            // if (text === this.oldtext) return;
            document.getElementById(this.buffer[i]).innerHTML = this.oldtext = text;
            MathJax.Hub.Queue(
                ["Typeset", MathJax.Hub, document.getElementById(this.buffer[i])],
                ["PreviewDone", this]
            );
        }
        this.mjRunning = true;
    },

    //
    //  Indicate that MathJax is no longer running,
    //  and swap the buffers to show the results.
    //
    PreviewDone: function () {
        this.mjRunning = false;
        this.SwapBuffers();
    }

};

//
//  Cache a callback to the CreatePreview action
//

$(document).ready(function () {

    currPage = 1;
    fetchQuestions();
    
});

function fetchQuestions() {
    $.ajax({
        type: 'post',
        url: getQuestionsDir,
        data: {
            'qId': qId,
            'page': currPage
        },
        success: function (response) {
            showQuestions(JSON.parse(response));
        }
    });
}

function showAskQuestion() {

    Preview.Init('questionTextId', 'MathPreview');
    $(".showItem").addClass('hidden');
    $(".askQuestionForm").removeClass('hidden');

}

function hideAskQuestion() {
    $(".askQuestionForm").addClass('hidden');
}

function showQuestions(arr) {

    $("#questionsContainer").empty();

    srcIds = [];
    destIds = [];

    for(i = 0; i < arr.length; i++) {
        newElement = "<div class='col-xs-12' style='min-height: 100px; border-bottom: 2px solid #4dc7bc; padding: 10px'><div class='col-xs-10'>";
        newElement += "<textarea class='hidden' id='desc_" + arr[i].id + "'>" + arr[i].description + "</textarea>";
        newElement += "<div id='mirrorDesc_" + arr[i].id + "'></div>";
        newElement += "<div class='question_date'></div>";
        newElement += "<span style='float: none' class='ui_button primary small answerButton' onclick='showAnsPane(\"" + arr[i].id + "\")'>پاسخ</span> ";
        newElement += "<span class='ui_button secondary small showAll' id='showAll_" + arr[i].id + "' onclick='showAllAns(\"" + arr[i].id + "\")'>نمایش " + arr[i].ansNum + " جواب</span> ";
        newElement += "<div class='votingForm'>";
        newElement += "<div class='voteIcon' onclick='likeAns(" + arr[i].id + ")'>";
        newElement += "<div class='ui_icon single-chevron-up-circle'></div>";
        newElement += "<div class='ui_icon single-chevron-up-circle-fill'></div>";
        newElement += "<div class='contents hidden'>پاسخ مفید</div>";
        newElement += "</div>";
        newElement += "<div class='voteCount'>";
        newElement += "<div class='number score' data-val='" + arr[i].rate + "' id='score_" + arr[i].id + "'>" + arr[i].rate + "</div>";
        newElement += "</div>";
        newElement += "<div class='voteIcon' onclick='dislikeAns(" + arr[i].id + ")'>";
        newElement += "<div class='ui_icon single-chevron-down-circle-fill'></div>";
        newElement += "<div class='ui_icon single-chevron-down-circle'></div>";
        newElement += "<div class='contents hidden'>پاسخ غیر مفید</div>";
        newElement += "</div></div>";
        newElement += "<span class='ui_button secondary small hideAll hidden' id='hide_" + arr[i].id + "' onclick='hideAllAns(\"" + arr[i].id + "\")'>پنهان کردن جواب ها</span>";
        newElement += "<div class='answerForm hidden showItem' id='answerForm_" + arr[i].id + "'>";
        newElement += "<div class='whatIsYourAnswer'>جواب شما چیست ؟</div>";
        newElement += "<div class='col-xs-6'>";
        newElement += "<div id='MathPreview_" + arr[i].id + "' style='border:1px solid; height: 250px; overflow: auto'></div>";
        newElement += "</div>";
        newElement += "<div class='col-xs-6'>";
        newElement += "<textarea style='height: 250px; overflow: hidden' onkeyup='Preview.Update()' id='answerTextArea_" + arr[i].id + "' class='answerText ui_textarea' placeholder='سلام ، جواب خود را وارد کنید'></textarea>";
        newElement += "</div>";
        newElement += "<div class='col-xs-12'>";
        newElement += "<ul class='errors hidden'></ul>";
        newElement += "<span class='postingGuidelines' style='float: left; margin-top: 10px'>راهنما  و قوانین</span>";
        newElement += "<div style='margin-top: 10px'><span class='ui_button primary small formSubmit' style='float: none' onclick='sendAns(\"" + arr[i].id + "\")'>ارسال</span>";
        newElement += "<span class='ui_button secondary small' onclick='hideAnsPane(\"" + arr[i].id + "\")'>لغو</span></div>";
        newElement += "</div></div>";
        newElement += "<div id='response_" + arr[i].id + "' class='answerList showItem'>";
        newElement += "</div></div>";
        newElement += "<div class='col-xs-2'>";
        newElement += "<div class='avatar_wrap'>";
        newElement += "<div class='username'>" + arr[i].uId + "</div>";
        newElement += "<div class='username'> تاریخ ارسال: " + arr[i].date + "</div>";
        newElement += "</div></div></div>";
        $("#questionsContainer").append(newElement);

        srcIds[i] = "desc_" + arr[i].id;
        destIds[i] = "mirrorDesc_" + arr[i].id;
    }

    Preview2.Init(srcIds, destIds);
    Preview2.Update();

    $("#pageNumQuestionContainer").empty();

    // newElement = "";
    // limit = Math.ceil(response[0] / 6);
    // preCurr = passCurr = false;
    //
    // for(k = 1; k <= limit; k++) {
    //     if(Math.abs(currPageQuestions - k) < 4 || k == 1 || k == limit) {
    //         if (k == currPageQuestions) {
    //             newElement += "<span data-page-number='" + k + "' class='pageNum current pageNumQuestion'>" + k + "</span>";
    //         }
    //         else {
    //             newElement += "<a onclick='changePageQuestion(this)' data-page-number='" + k + "' class='pageNum taLnk pageNumQuestion'>" + k + "</a>";
    //         }
    //     }
    //     else if(k < currPage && !preCurr) {
    //         preCurr = true;
    //         newElement += "<span class='separator'>&hellip;</span>";
    //     }
    //     else if(k > currPage && !passCurr) {
    //         passCurr = true;
    //         newElement += "<span class='separator'>&hellip;</span>";
    //     }
    // }
    //
    // $("#pageNumQuestionContainer").append(newElement);
}

function sendAns(logId) {

    if($("#answerTextArea_" + logId).val() == "")
        return;

    $.ajax({
        type: 'post',
        url: sendAnsDir,
        data: {
            'qId': logId,
            'text': $("#answerTextArea_" + logId).val()
        },
        success: function (response) {

            if(response == "ok") {
                $(".errors").empty().append('جواب شما اضافه گردید و پس از تایید به نمایش در می آید');
            }
            else {
                $(".errors").empty().append('مشکلی در انجام عملیات مورد نظر رخ داده است');
            }

            $(".errors").removeClass('hidden');
        }
    });
    
}

function showAnsPane(logId) {

    Preview.Init('answerTextArea_' + logId, 'MathPreview_' + logId);
    $(".showItem").addClass('hidden');
    $(".errors").addClass('hidden');
    $("#answerForm_" + logId).removeClass('hidden');
}

function hideAnsPane(logId) {
    $(".errors").addClass('hidden');
    $("#answerForm_" + logId).addClass('hidden');
}

function hideAllAns(logId) {
    $("#hide_" + logId).addClass('hidden');
    $("#showAll_" + logId).removeClass('hidden');
    $("#response_" + logId).addClass('hidden');
}

function likeAns(logId) {

    $.ajax({
        type: 'post',
        url: opOnQuestion,
        data: {
            'logId': logId,
            'mode': 'like'
        },
        success: function (response) {
            if(response == "1") {
                $("#score_" + logId).empty()
                    .attr('data-val', parseInt($("#score_" + logId).attr('data-val')) + 1)
                    .append($("#score_" + logId).attr('data-val'));
            }
            else if(response == "2") {
                $("#score_" + logId).empty()
                    .attr('data-val', parseInt($("#score_" + logId).attr('data-val')) + 2)
                    .append($("#score_" + logId).attr('data-val'));
            }
        }
    });
}

function dislikeAns(logId) {

    $.ajax({
        type: 'post',
        url: opOnQuestion,
        data: {
            'logId': logId,
            'mode': 'dislike'
        },
        success: function (response) {
            if(response == "1") {
                $("#score_" + logId).empty()
                    .attr('data-val', parseInt($("#score_" + logId).attr('data-val')) - 1)
                    .append($("#score_" + logId).attr('data-val'));
            }
            else if(response == "2") {
                $("#score_" + logId).empty()
                    .attr('data-val', parseInt($("#score_" + logId).attr('data-val')) - 2)
                    .append($("#score_" + logId).attr('data-val'));
            }
        }
    });
}

function showAllAns(logId) {

    $.ajax({
        type: 'post',
        url: showAllAnsDir,
        data: {
            'logId': logId
        },
        success: function (response) {

            $(".showItem").addClass('hidden');

            $("#hide_" + logId).removeClass('hidden');
            $("#showAll_" + logId).addClass('hidden');

            response = JSON.parse(response);

            srcIds = [];
            destIds = [];

            for(i = 0; i < response.length; i++) {
                newElement = "<DIV class='prw_rup prw_common_location_posting' style='margin-top: 5px; min-height: 100px'>";
                newElement += "<div class='response'>";
                newElement += "<div class='header'><span>پاسخ از " + response[i].uId +"</span>";
                newElement += "</div>";
                newElement += "<div class='content'>";
                newElement += "<textarea class='abbreviate hidden' id='ansDesc_" + response[i].id + "'>" + response[i].description + "</textarea>";
                newElement += "<div class='abbreviate' id='mirrorAnsDesc_" + response[i].id + "'></div>";
                newElement += "</div>";
                newElement += "<div class='votingForm2'>";
                newElement += "<div class='voteIcon' onclick='likeAns(" + response[i].id + ")'>";
                newElement += "<div class='ui_icon single-chevron-up-circle'></div>";
                newElement += "<div class='ui_icon single-chevron-up-circle-fill'></div>";
                newElement += "<div class='contents hidden'>پاسخ مفید</div>";
                newElement += "</div>";
                newElement += "<div class='voteCount'>";
                newElement += "<div class='number score' data-val='" + response[i].rate + "' id='score_" + response[i].id + "'>" + response[i].rate + "</div>";
                newElement += "</div>";
                newElement += "<div class='voteIcon' onclick='dislikeAns(" + response[i].id + ")'>";
                newElement += "<div class='ui_icon single-chevron-down-circle-fill'></div>";
                newElement += "<div class='ui_icon single-chevron-down-circle'></div>";
                newElement += "<div class='contents hidden'>پاسخ غیر مفید</div>";
                newElement += "</div></div></div></DIV>";
                $("#response_" + logId).append(newElement);

                srcIds[i] = 'ansDesc_' + response[i].id;
                destIds[i] = 'mirrorAnsDesc_' + response[i].id;
            }

            Preview2.Init(srcIds, destIds);
            Preview2.Update();

            $("#response_" + logId).empty().removeClass('hidden');
        }
    });
}

function askQuestion() {
    
    if($("#questionTextId").val() == "")
        return;
    
    $.ajax({
        type: 'post',
        url: askQuestionDir,
        data: {
            'qId': qId,
            'text': $("#questionTextId").val()
        },
        success: function (response) {

            if(response == "ok") {
                $("#msgText").empty().append('سوال شما به سامانه اضافه گردید و پس از تایید به سوالات اضافه می گردد');
                hideAskQuestion();
            }
            else {
                $("#msgText").empty().append('اشکالی در اضافه نمودن سوال مورد نظر به وجود آمده است');
            }
        }
    })
    
    
}