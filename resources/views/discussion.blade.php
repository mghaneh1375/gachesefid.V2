@extends('layouts.form')

@section('head')
    @parent

    <link rel="stylesheet" href="{{URL::asset('css/discussion.css')}}">
    <script src="{{URL::asset('js/jsNeededForDiscussion.js')}}"></script>

    <script>
        var qId = '{{$qId}}';
        var askQuestionDir = '{{route('askQuestion')}}';
        var getQuestionsDir = '{{route('getQuestions')}}';
        var showAllAnsDir = '{{route('showAllAns')}}';
        var sendAnsDir = '{{route('sendAns')}}';
        var opOnQuestion = '{{route('opOnQuestion')}}';
    </script>

    <script type="text/x-mathjax-config">
  MathJax.Hub.Config({
    showProcessingMessages: false,
    TeX: {equationNumbers: {autoNumber: "AMS"}},
    tex2jax: { inlineMath: [['$','$'],['\\(','\\)']] }
  });
</script>

    <script type="text/javascript" src="http://cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS_HTML-full"></script>

    <script>
        var Preview = {
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
            Init: function (s, bufferDiv) {
                this.src = document.getElementById(s);
                this.preview = document.getElementById(bufferDiv);
                this.buffer = document.getElementById(bufferDiv);
            },

            //
            //  Switch the buffer and preview, and display the right one.
            //  (We use visibility:hidden rather than display:none since
            //  the results of running MathJax are more accurate that way.)
            //
            SwapBuffers: function () {
                var buffer = this.preview, preview = this.buffer;
                this.buffer = buffer; this.preview = preview;
                buffer.style.visibility = "hidden"; buffer.style.position = "absolute";
                preview.style.position = ""; preview.style.visibility = "";
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
                Preview.timeout = null;
                if (this.mjRunning) return;
                var text = this.src.value;
                if (text === this.oldtext) return;
                this.buffer.innerHTML = this.oldtext = text;
                this.mjRunning = true;
                MathJax.Hub.Queue(
                        ["Typeset",MathJax.Hub,this.buffer],
                        ["PreviewDone",this]
                );
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

    </script>

    <script type="text/x-mathjax-config">

        Preview.callback = MathJax.Callback(["CreatePreview",Preview]);
        Preview.callback.autoReset = true;  // make sure it can run more than once


        Preview2.callback = MathJax.Callback(["CreatePreview",Preview2]);
        Preview2.callback.autoReset = true;  // make sure it can run more than once
    </script>
@stop

@section('caption')
    <div class="title">پرسش و پاسخ درباره این سوال</div>
@stop

@section('main')

    <DIV id="ansAndQeustionDiv" class="ppr_rup ppr_priv_location_qa">
        <div data-tab="TABS_ANSWERS" style="margin-bottom: 60px">
            <div>
                <p class="errorText" style="width: auto !important" id="msgText"></p>
            </div>
            <div class="block_header">
                <span class="block_title">سوال و جواب</span>
                <span class="ui_button primary fr" onclick="showAskQuestion()" style="float: left;">سوال بپرس</span>
            </div>
            <div style="width: 100%; float: right; direction: rtl;" class="askQuestionForm hidden control showItem">
                <div class="askExplanation">سوال خودتو بپرس تا کسانی که می دونند کمکت کنند.</div>
                <div class="overlayNote">سوال شما به صورت عمومی نمایش داده خواهد شد.</div>

                <div class="col-xs-6">
                    <div id="MathPreview" style="border:1px solid; height: 250px; overflow: auto"></div>
                </div>

                <div class="col-xs-6">
                    <textarea style="width: 100%; height: 250px; overflow: auto" name="topicText" id="questionTextId" onkeyup="Preview.Update()" class="topicText ui_textarea" placeholder="سلام هرچی میخواهی بپرسید. بدون خجالت"></textarea>
                </div>

                <span class="postingGuidelines" style="float: right;">راهنما و قوانین</span>
                <div class="underForm" style="float: left;">
                    <span class="ui_button primary formSubmit" style="margin-right: 10px" onclick="askQuestion()">ثبت</span>
                    <span class="ui_button secondary formCancel" onclick="hideAskQuestion()">انصراف</span>
                </div>
                <div style="clear: both;"></div>
            </div>
            <div style="clear: both;"></div>

            <div class="block_body_top">

                <DIV class="prw_rup">
                    <div class="question row" id="questionsContainer">
                    </div>
                </DIV>

                <DIV class="prw_rup prw_common_north_star_pagination" id="pageNumQuestionContainer">
                </DIV>
            </div>

            <div class="shouldUpdateOnLoad"></div>
        </div>
    </DIV>
@stop