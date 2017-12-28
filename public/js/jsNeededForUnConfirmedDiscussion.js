
var currPage;
var kindSort = 0;
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

$(document).ready(function () {
    currPage = 1;
    fetchUnConfirmedQuestions();
});

function fetchUnConfirmedQuestions() {
    $.ajax({
        type: 'post',
        url: getUnConfirmedQuestionsDir,
        data: {
            'page': currPage
        },
        success: function (response) {
            showQuestions(JSON.parse(response));
        }
    });
}

function changeSort(val) {
    
    if(val == 0) {
        kindSort = 0;
        currPage = 1;
        fetchUnConfirmedQuestions();
    }
    else if(val == 1) {
        kindSort = 1;
        currPage = 1;
        fetchConfirmedQuestions();
    }
    else {
        currPage = 1;
        kindSort = 2;
        fetchConfirmedAndUnConfirmedQuestions();
    }
    
}

function fetchConfirmedAndUnConfirmedQuestions() {
    $.ajax({
        type: 'post',
        url: getConfirmedAndUnConfirmedQuestions,
        data: {
            'page': currPage
        },
        success: function (response) {
            showQuestions(JSON.parse(response));
        }
    });
}

function changeQuestionStatus(val) {

    mode = 0;

    if($("#confirm_" + val).is(':checked')) {
        mode = 1;
    }

    $.ajax({
        type: 'post',
        url: changeQuestionStatusDir,
        data: {
            'qId': val,
            'status': mode
        },
        success: function (response) {
            if(response == "ok") {
                if(kindSort == 1)
                    fetchConfirmedQuestions();
                else if(kindSort == 0)
                    fetchUnConfirmedQuestions();
            }
        }
    });

}

function fetchConfirmedQuestions() {
    $.ajax({
        type: 'post',
        url: getConfirmedQuestionsDir,
        data: {
            'page': currPage
        },
        success: function (response) {
            showQuestions(JSON.parse(response));    
        }
    });
}

function showQuestions(arr) {

    $("#questionsContainer").empty();

    srcIds = [];
    destIds = [];

    for(i = 0; i < arr.length; i++) {
        newElement = "<div id='question_div_" + arr[i].id + "'>";
        newElement += "<div class='col-xs-12' style='max-height: 300px'><img width='100%' style='max-height: 300px' src='"+ arr[i].fileName +"'></div>";
        newElement += "<div class='col-xs-12' style='min-height: 100px; padding: 10px'><div class='col-xs-10'>";
        newElement += "<textarea class='hidden' id='desc_" + arr[i].id + "'>" + arr[i].description + "</textarea>";
        newElement += "<div id='mirrorDesc_" + arr[i].id + "'></div>";
        newElement += "<div class='question_date'></div>";
        newElement += "</div>";
        newElement += "<div class='col-xs-2'>";
        newElement += "<div class='avatar_wrap'>";
        newElement += "<div class='username'>" + arr[i].uId + "</div>";
        newElement += "<div class='username'> تاریخ ارسال: " + arr[i].date + "</div>";
        newElement += "</div></div>";
        newElement += "</div>";
        newElement += "<center style='padding: 10px; border-bottom: 2px solid #4dc7bc; margin-top: 20px'>";

        if(arr[i].status == 0)
            newElement += "<label class='switch'><input onchange='changeQuestionStatus(\"" + arr[i].id + "\")' id='confirm_" + arr[i].id + "' type='checkbox'><span class='slider round'></span></label>";
        else
            newElement += "<label class='switch'><input onchange='changeQuestionStatus(\"" + arr[i].id + "\")' id='confirm_" + arr[i].id + "' type='checkbox' checked><span class='slider round'></span></label>";

        newElement += "</center></div>";
        $("#questionsContainer").append(newElement);

        srcIds[i] = "desc_" + arr[i].id;
        destIds[i] = "mirrorDesc_" + arr[i].id;
    }

    if(arr.length > 0) {
        Preview2.Init(srcIds, destIds);
        Preview2.Update();
    }

    $("#pageNumQuestionContainer").empty();

    newElement = "";

    if(arr.length > 0) {

        limit = Math.ceil(arr[0].totalCount / 5);

        preCurr = passCurr = false;

        for (k = 1; k <= limit; k++) {
            if (Math.abs(currPage - k) < 4 || k == 1 || k == limit) {
                if (k == currPage) {
                    newElement += "<span data-page-number='" + k + "' class='pageNum currentPage'>" + k + "</span>";
                }
                else {
                    newElement += "<a onclick='changePage(\"" + k + "\")' data-page-number='" + k + "' class='pageNum taLnk pageNumQuestion'>" + k + "</a>";
                }
            }
            else if (k < currPage && !preCurr) {
                preCurr = true;
                newElement += "<span class='separator'>&hellip;</span>";
            }
            else if (k > currPage && !passCurr) {
                passCurr = true;
                newElement += "<span class='separator'>&hellip;</span>";
            }
        }

        $("#pageNumQuestionContainer").append(newElement);
    }
}

function changePage(val) {

    currPage = val;

    if(kindSort == 0)
        fetchUnConfirmedQuestions();
    else if(kindSort == 1)
        fetchConfirmedQuestions();
    else
        fetchConfirmedAndUnConfirmedQuestions();
}