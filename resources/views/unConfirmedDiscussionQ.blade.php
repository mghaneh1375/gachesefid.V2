@extends('layouts.form')

@section('head')
    @parent

    <link rel="stylesheet" href="{{URL::asset('css/discussion.css')}}">
    <script src="{{URL::asset('js/jsNeededForUnConfirmedDiscussion.js')}}"></script>

    <script>
        var getUnConfirmedQuestionsDir = '{{route('getUnConfirmedQuestions')}}';
        var getConfirmedQuestionsDir = '{{route('getConfirmedQuestions')}}';
        var getConfirmedAndUnConfirmedQuestions = '{{route('getConfirmedAndUnConfirmedQuestions')}}';
        var changeQuestionStatusDir = '{{route('changeQuestionStatus')}}';
    </script>

    <script type="text/x-mathjax-config">
  MathJax.Hub.Config({
    showProcessingMessages: false,
    TeX: {equationNumbers: {autoNumber: "AMS"}},
    tex2jax: { inlineMath: [['$','$'],['\\(','\\)']] }
  });
</script>

    <script type="text/javascript" src="http://cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS_HTML-full"></script>

    <script type="text/x-mathjax-config">
        Preview2.callback = MathJax.Callback(["CreatePreview",Preview2]);
        Preview2.callback.autoReset = true;  // make sure it can run more than once
    </script>

@stop

@section('caption')
    <div class="title">تالار گفتمان
    </div>
@stop

@section('main')

    <div class="col-xs-12" style="padding: 10px; border-bottom: 2px solid #4dc7bc; height: 50px">

        <div class="col-xs-4">
            <label>
                <span>تایید نشده ها</span>
                <input onclick="changeSort('0')" name="sort" type="radio" checked>
            </label>
        </div>

        <div class="col-xs-4">
            <label>
                <span>تایید شده ها</span>
                <input onclick="changeSort('1')" name="sort" type="radio">
            </label>
        </div>

        <div class="col-xs-4">
            <label>
                <span>همه</span>
                <input onclick="changeSort('2')" name="sort" type="radio">
            </label>
        </div>
    </div>

    <DIV id="ansAndQeustionDiv" class="ppr_rup ppr_priv_location_qa">
        <div data-tab="TABS_ANSWERS" style="margin-bottom: 60px">
            <div style="clear: both;"></div>

            <div class="block_body_top">

                <DIV class="prw_rup">
                    <div class="question row" id="questionsContainer">
                    </div>
                </DIV>

                <center class="pageNumberContainer" id="pageNumQuestionContainer">
                </center>
            </div>

            <div class="shouldUpdateOnLoad"></div>
        </div>
    </DIV>
@stop