@extends('layouts.form')

@section('head')
    @parent

    <script src="{{URL::asset('js/jsNeededForRegularQuiz.js')}}"></script>
    <script src = {{URL::asset("js/calendar.js") }}></script>
    <script src = {{URL::asset("js/calendar-setup.js") }}></script>
    <script src = {{URL::asset("js/calendar-fa.js") }}></script>
    <script src = {{URL::asset("js/jalali.js") }}></script>
    <link rel="stylesheet" href="{{URL::asset('css/standalone.css')}}">
    <script src="{{URL::asset('js/jquery.timepicker.min.js')}}"></script>
    <link rel="stylesheet" href="{{URL::asset('css/clockpicker.css')}}">
    <script src="{{URL::asset('js/clockpicker.js')}}"></script>
    <link rel="stylesheet" href = {{URL::asset("css/calendar-green.css") }}>
    <link rel="stylesheet" href="{{URL::asset('css/form.css')}}">

    <script>

        var quizDir = '{{route('regularQuizes')}}';
        var addQuizDir = '{{route('addQuizRegular')}}';
        var editQuizDir = '{{route('editQuizRegular')}}';
        var getRegularQuizDetails = '{{route('getRegularQuizDetails')}}';
        var getQuizQuestions = '{{route('getRegularQuizQuestions')}}';
        var getGradesDir = '{{route('getGrades')}}';
        var getLessonsDir = '{{route('getLessons')}}';
        var getSubjectsDir = '{{route('getSubjects')}}';
        var getSubjectQuestionsDir = '{{route('getSubjectQuestions')}}';
        var fetchQuestionByOrganizationId = '{{route('fetchQuestionByOrganizationId')}}';
        var doAddQuestionToQuizDir = '{{route('doAddQuestionToRegularQuiz')}}';
        var deleteQuizDir = '{{route('deleteRegularQuiz')}}';
        var removeQFromQDir = '{{route('removeQFromRegularQ')}}';
        var changeQNoDir = '{{route('changeQNoRegularQuiz')}}';
        var elseQuizDir = '{{route('elseQuiz')}}';
        var deleteQFromQ = '{{route('deleteQFromQ')}}';
        var deleteDeletedQFromQ = '{{route('deleteDeletedQFromQ')}}';
        var changeRankingCountDir = '{{route('changeRankingCount')}}';
        var homeDir = '{{route('home')}}';

        $(document).ready(function(){

            $('input.timepicker').timepicker({
                timeFormat: 'hh:mm:ss',
                interval: 5,
                minTime: '00:00',
                maxTime: '11:55',
                defaultTime: '07:00',
                startTime: '00:00',
                dynamic: true,
                dropdown: true,
                scrollbar: true
            });

            $('.clockpicker').clockpicker();
        });
    </script>

    <style>
        .calendar {
            z-index: 100000;
            position: fixed !important;
            left: 40% !important;
            margin-top: -20px;
            top: 40% !important;;
        }
        .clockpicker-popover {
            z-index: 100000;
            direction: ltr;
        }
    </style>
@stop


@section('caption')
    <div class="title">آزمون های معمولی
    </div>
@stop

@section('main')

    <center class="myRegister">
        <div class="row data">
            
            @if(count($quizes) == 0)
                <p>آزمونی موجود نیست</p>
            @else
                @foreach($quizes as $quiz)
                    <div class="col-xs-12" style="margin-top: 10px;">
                        <div class="col-xs-3" style="text-align: right">
                            <button class="btn btn-primary" onclick="elseQuiz('{{$quiz->id}}')" data-toggle="tooltip" title="حواشی آزمون">
                                <span class="glyphicon glyphicon-cog"></span>
                            </button>
                            <button class="btn btn-danger" onclick="deleteQuiz('{{$quiz->id}}')" data-toggle="tooltip" title="حذف آزمون">
                                <span class="glyphicon glyphicon-remove"></span>
                            </button>
                            <button class="btn btn-info" onclick="editQuiz('{{$quiz->id}}')" data-toggle="tooltip" title="ویرایش آزمون">
                                <span class="glyphicon glyphicon-edit"></span>
                            </button>
                            <button class="btn btn-success" onclick="addQuestionToQuiz('{{$quiz->id}}')" data-toggle="tooltip" title="افزودن سوال به آزمون">
                                <span class="glyphicon glyphicon-plus"></span>
                            </button>
                            <button class="btn btn-default" onclick="document.location.href = '{{route('groupQuizRegistrationController', ['qId' => $quiz->id])}}'" data-toggle="tooltip" title="مشاهده وضعیت ثبت نام های گروهی">
                                <span class="glyphicon glyphicon-list"></span>
                            </button>
                        </div>
                        <div class="col-xs-7" style="padding: 5px; font-size: 12px">
                            <div class="col-xs-12">
                                <span>
                                    <span>تاریخ برگزاری آزمون</span>
                                    <span>&nbsp;:&nbsp;</span>
                                    <span>{{$quiz->startDate}}</span>
                                    <span>&nbsp;-&nbsp;</span>
                                    <span>تاریخ اتمام آزمون</span>
                                    <span>&nbsp;:&nbsp;</span>
                                    <span>{{$quiz->endDate}}</span>
                                </span>
                            </div>
                            <div class="col-xs-12">
                                <span>
                                    <span>ساعت برگزاری آزمون</span>
                                    <span>&nbsp;:&nbsp;</span>
                                    <span>{{$quiz->startTime}}</span>
                                    <span>&nbsp;-&nbsp;</span>
                                    <span>ساعت اتمام آزمون</span>
                                    <span>&nbsp;:&nbsp;</span>
                                    <span>{{$quiz->endTime}}</span>
                                </span>
                            </div>
                            <div class="col-xs-12">
                                <span>
                                <span>تاریخ شروع ثبت نام</span>
                                <span>&nbsp;:&nbsp;</span>
                                <span>{{$quiz->startReg}}</span>
                                <span>&nbsp;-&nbsp;</span>
                                <span>تاریخ اتمام ثبت نام</span>
                                <span>&nbsp;:&nbsp;</span>
                                <span>{{$quiz->endReg}}</span>
                            </span>
                            </div>
                        </div>
                        <span class="col-xs-2" style="padding: 5px; text-align: left">  {{$quiz->name}} : </span>
                    </div>
                @endforeach
            @endif

            <button class="btn btn-primary circleBtn" style="margin-top: 10px" onclick="addQuiz()" data-toggle="tooltip" title="افزودن آزمون">
                <span class="glyphicon glyphicon-plus"></span>
            </button>
        </div>
    </center>

    <span id="newQuizContainer" class="ui_overlay item hidden" style="max-height: 80%; overflow: auto; position:
    fixed; left: 30%; right: 30%; top: 10px; bottom: auto">
        <div class="header_text">آزمون جدید</div>
        <div onclick="hideElement()" class="ui_close_x"></div>
        <center class="body_text">

            <div class="col-xs-12" style="margin-top: 5px">
                <label for="name">نام آزمون</label>
                <input type="text" style="max-width: 200px" class="form-detail" id="name" maxlength="40">
            </div>

            <div class="col-xs-12" style="margin-top: 5px">
                <label>قیمت</label>
                <input type="number" style="max-width: 200px" class="form-detail" id="price" min="0" value="1000">
            </div>

            <div class="col-xs-12" style="margin-top: 5px">
                <label>
                    <span>تاریخ برگزاری آزمون</span>
                    <input type="button" style="border: none; width: 30px; height: 30px; background: url({{ URL::asset('images/calendar-flat.png') }}) repeat 0 0; background-size: 100% 100%;" id="date_btn">
                </label>
                <input type="text" style="max-width: 200px" class="form-detail" id="date_input" readonly>
                <script>
                    Calendar.setup({
                        inputField: "date_input",
                        button: "date_btn",
                        ifFormat: "%Y/%m/%d",
                        dateType: "jalali"
                    });
                </script>
            </div>

            <div class="col-xs-12" style="margin-top: 5px">
                <label>
                    <span>تاریخ اتمام آزمون</span>
                    <input type="button" style="border: none; width: 30px; height: 30px; background: url({{ URL::asset('images/calendar-flat.png') }}) repeat 0 0; background-size: 100% 100%;" id="date_btn_end">
                </label>
                <input type="text" style="max-width: 200px" class="form-detail" id="date_input_end" readonly>
                <script>
                    Calendar.setup({
                        inputField: "date_input_end",
                        button: "date_btn_end",
                        ifFormat: "%Y/%m/%d",
                        dateType: "jalali"
                    });
                </script>
            </div>

            <div class="col-xs-12" style="margin-top: 5px">
                <label>
                    <span>ساعت شروع</span>
                </label>
                <div class="clockpicker">
                    <input type="text" name="sTime" autocomplete="off" id="sTime" style="direction: ltr" class="form-detail form-control"
                           value="09:30">
                </div>
            </div>

            <div class="col-xs-12" style="margin-top: 5px">
                <label for="eTime">ساعت اتمام</label>
                <div class="clockpicker">
                    <input type="text" name="eTime" autocomplete="off" id="eTime" style="direction: ltr" class="form-detail
                    form-control"
                           value="09:30">
                </div>
            </div>

            <div class="col-xs-12" style="margin-top: 5px">
                <label>
                    <span>تاریخ شروع ثبت نام</span>
                    <input type="button" style="border: none; width: 30px; height: 30px; background: url({{ URL::asset('images/calendar-flat.png') }}) repeat 0 0; background-size: 100% 100%;" id="date_btn_reg">
                </label>
                <input type="text" style="max-width: 200px" id="date_input_reg" class="form-detail" readonly>
                <script>
                    Calendar.setup({
                        inputField: "date_input_reg",
                        button: "date_btn_reg",
                        ifFormat: "%Y/%m/%d",
                        dateType: "jalali"
                    });
                </script>
            </div>

            <div class="col-xs-12" style="margin-top: 5px">
                <label>
                    <span>تاریخ اتمام ثبت نام</span>
                    <input type="button" style="border: none; width: 30px; height: 30px; background: url({{ URL::asset('images/calendar-flat.png') }}) repeat 0 0; background-size: 100% 100%;" id="date_btn_reg_end">
                </label>
                <input type="text" class="form-detail" style="max-width: 200px" id="date_input_reg_end" readonly>
                <script>
                    Calendar.setup({
                        inputField: "date_input_reg_end",
                        button: "date_btn_reg_end",
                        ifFormat: "%Y/%m/%d",
                        dateType: "jalali"
                    });
                </script>
            </div>

            <div class="submitOptions" style="margin-top: 10px">
                <button onclick="doAddQuiz()" class="btn btn-success">تایید</button>
                <input type="submit" onclick="hideElement()" value="خیر" class="btn btn-default">
                <p id="errMsg" class="form-detail errorText"></p>
            </div>
        </center>
    </span>

    <span id="addQuestionPane" class="ui_overlay item hidden" style="position: fixed; left: 10%; right: 10%; top: 40px; bottom: auto">
        <div class="header_text">نمایش آزمون</div>
        <div onclick="hideElement()" class="ui_close_x"></div>
        <div class="body_text">
            <div class="col-xs-12" style="min-height: 50vh; border: 3px solid black">
                <div class="col-xs-6" style="min-height: 45vh" id="ansPane"></div>
                <div class="col-xs-6" style="min-height: 45vh" id="questionPane"></div>
                <div class="col-xs-12" id="qInfo" style="border: 2px solid #ccc; border-radius: 6px; min-height: 3vh"></div>
                <p id="msg" style="position: absolute; top: 25vh"></p>
                <div id="prevQ" onclick="prevQ()" class="fa fa-chevron-left" style="position: absolute; left: 0; font-size: 30px; cursor: pointer; z-index: 1000; top: 25vh"></div>
                <div id="nextQ" onclick="nextQ()" class="fa fa-chevron-right" style="position: absolute; right: 0; font-size: 30px; cursor: pointer; z-index: 1000; top: 25vh"></div>
            </div>
            <div class="col-xs-12">
                <center>
                    <button onclick="addQuestion()" class="btn btn-primary" data-toggle="tooltip" title="افزودن سوال جدید">
                        <span class="glyphicon glyphicon-plus"></span>
                    </button>
                </center>
            </div>
        </div>
    </span>

    <span id="addQuestion" class="ui_overlay item hidden" style="position: fixed; left: 20%; right: 20%; top: 60px;
    bottom: auto">
        <div class="header_text">لیست سوالات</div>
        <div onclick="hideAddQuestion()" class="ui_close_x"></div>
        <div class="body_text row">
            <div class="col-xs-6">
                <div class="col-xs-12">
                    <label for="organizationId">جست و جو بر اساس کد سازمانی سوال</label>
                    <input id="organizationId" type="text" maxlength="10">
                </div>
                <div class="col-xs-12" style="margin-top: 10px">
                    <center>
                        <button class="btn btn-primary" onclick="fetchQ()">بیار</button>
                        <p class="errorText" id="fetchErr"></p>
                    </center>
                </div>

                <form method="post" action="{{route('addBatchQToQ')}}" enctype="multipart/form-data">
                    {{csrf_field()}}
                    <input type="hidden" id="hiddenCurrQuiz" name="quizId">
                    <div class="col-xs-12">
                        <label for="batchQ">فایل دسته ای سوالات</label>
                        <input id="batchQ" name="batchQ" type="file" onchange="if(this.value.length != 0) $('#submitBatch').removeAttr('disabled'); else $('#submitBatch').attr('disabled', 'disabled');">
                    </div>
                    <div class="col-xs-12" style="margin-top: 10px">
                        <center>
                            <input disabled type="submit" id="submitBatch" name="submitBatch" value="افزودن دسته ای سوالات به آزمون" class="btn btn-primary">
                        </center>
                    </div>
                </form>

            </div>
            <div class="col-xs-6">
                <div class="col-xs-12">
                    <label>
                        <span>پایه ی تحصیلی</span>
                        <select class="mySelect" id="grades" onchange="getLessons(this.value)"></select>
                    </label>
                </div>
                <div class="col-xs-12">
                    <label>
                        <span>درس</span>
                        <select class="mySelect" id="lessons" onchange="getSubjects(this.value)"></select>
                    </label>
                </div>
                <div class="col-xs-12">
                    <label>
                        <span>مبحث</span>
                        <select class="mySelect" id="subjects" onchange="getSubjectQuestions($('#subjects').val())"></select>
                    </label>
                </div>
            </div>

            <div class="col-xs-12" style="min-height: 40vh; border: 3px solid black">
                <div class="col-xs-6" style="min-height: 35vh" id="subAnsPane"></div>
                <div class="col-xs-6" style="min-height: 35vh" id="subQuestionPane"></div>
                <div class="col-xs-12" id="subQInfo" style="border: 2px solid #ccc; border-radius: 6px; min-height: 3vh"></div>
                <p id="subMsg" style="position: absolute; top: 20vh"></p>
                <div id="subPrevQ" onclick="subPrevQ()" class="fa fa-chevron-left" style="position: absolute; left: 0; font-size: 30px; cursor: pointer; z-index: 1000; top: 20vh"></div>
                <div id="subNextQ" onclick="subNextQ()"  class="fa fa-chevron-right" style="position: absolute; right: 0; font-size: 30px; cursor: pointer; z-index: 1000; top: 20vh"></div>
            </div>

            <div class="col-xs-12 submitOptions" style="margin-top: 10px">
                <button onclick="doAddQuestionToQuiz()" class="btn btn-success">تایید</button>
                <input type="submit" onclick="hideAddQuestion()" value="خیر" class="btn btn-default">
            <p id="subMsgBottom"></p>
            </div>
        </div>
    </span>

    <span id="elseQuiz" class="ui_overlay item hidden" style="position: fixed; left: 20%; right: 20%; top: 60px; bottom: auto">
        <div class="header_text">حواشی آزمون</div>
        <div onclick="hideElement(); $('#elseQuiz').addClass('hidden')" class="ui_close_x"></div>
        <center class="body_text" id="body_elseQuiz"></center>
    </span>

    <span id="confirmation" class="ui_overlay item hidden" style="position: fixed; left: 20%; right: 20%; top: 60px; bottom: auto">
        @if(!empty($err))
            <p>{{$err}}</p>
        @endif
        <button class="btn btn-success" onclick="document.location.href = '{{route('regularQuizes')}}'">تایید</button>
    </span>


    @if(!empty($err))
        <script>
            $(".dark").removeClass('hidden');
            $("#confirmation").removeClass('hidden');
        </script>
    @endif

@stop