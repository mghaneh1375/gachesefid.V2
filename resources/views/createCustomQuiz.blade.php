@extends('layouts.form')

@section('head')
    @parent
    <script>
        var getLessonsDir = '{{route('getLessons')}}';
        var getSubjectsDir = '{{route('getSubjects')}}';
        var getSuggestionQuestionsCount = '{{route('getSuggestionQuestionsCount')}}';
        var preTransactionDir = '{{route('preTransactionQuestion')}}';
        var homeDir = '{{route('home')}}';
    </script>

    <style>
        td {
            padding: 6px;
            min-width: 200px;
        }
        #myUl {
            display: block;
            list-style-type: disc !important;
            margin-top: 1em;
            margin-bottom: 1em;
            margin-left: 0;
            margin-right: 0;
            padding-left: 40px;
            cursor: pointer;
        }
        .LessonClass {
            margin-right: 30px;
        }
        .LessonClass > li {
            list-style-type: square !important;
            cursor: pointer;
        }
        .SubjectClass {
            margin-right: 60px;
        }
        .SubjectClass > li {
            list-style-type: circle !important;
            cursor: pointer;
        }
        .add {
            font-size: 9px;
            color: #963019;
        }
    </style>

    <script src="{{URL::asset('js/jsNeededForBuyQuestion.js')}}"></script>

@stop

@section('caption')
    <div class="title">ساخت آزمون
    </div>
@stop


@section('main')

    <div class="col-xs-12">

        <div class="col-xs-8" style="margin-top: 10px; border-right: 2px dotted black; height: 60vh; overflow: auto">
            <div class="col-xs-12" style="max-height: 50vh; min-height: 50vh; overflow: auto">
                <center><p style="font-weight: bolder; font-size: 28px; color: #963019">لیست خرید</p></center>
                <center id="boxes"></center>
            </div>

            <center>
                <button onclick="showRecipe()" class="btn btn-primary">مشاهده فاکتور و پرداخت</button>
            </center>
        </div>

        <div class="col-xs-4" style="margin-top: 10px">
            <ul id="myUl">
                @foreach($grades as $grade)
                    <li><span onclick="openGrade('{{$grade->id}}')">{{$grade->name}}</span><span class="addGradeText" id="add_{{$grade->id}}"><span>&nbsp;&nbsp;&nbsp;</span><span onclick="addGradeBox('{{$grade->id}}', '{{$grade->name}}')" class="add">افزودن به لیست</span></span>
                       <ul class="LessonClass" data-repeat="false" data-status="close" id="grade_{{$grade->id}}"></ul>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <center class="col-xs-12 hidden addBox" style="margin-top: 10px">

        <table>
            <tr>
                <td>پایه تحصیلی</td>
                <td>
                    <select id="grades" onchange="getLessons()" class="mySelect">
                        @foreach($grades as $grade)
                            <option value="{{$grade->id}}">{{$grade->name}}</option>
                        @endforeach
                    </select>
                </td>
            </tr>

            <tr>
                <td>درس</td>
                <td>
                    <select class="mySelect" onchange="getSubjects()" id="lessons"></select>
                </td>
            </tr>

            <tr>
                <td>مبحث</td>
                <td>
                    <select class="mySelect" id="subjects"></select>
                </td>
            </tr>

            <tr>
                <td>سطح سختی</td>
                <td>
                    <select id="level" class="mySelect">
                        <option value="-1">مهم نیست</option>
                        <option value="1">ساده</option>
                        <option value="2">متوسط</option>
                        <option value="3">دشوار</option>
                    </select>
                </td>
            </tr>

            <tr>
                <td>انتخاب بر اساس بیشترین لایک</td>
                <td>
                    <input type="checkbox" id="sort">
                </td>
            </tr>

            <tr>
                <td>تعداد سوال مورد نظر</td>
                <td>
                    <input type="number" id="qNo">
                </td>
            </tr>
        </table>

        <div class="col-xs-12" style="margin-top: 10px">
            <button onclick="doAddBox()" class="btn btn-primary">اضافه کن</button>
            <button onclick="goBack()" class="btn btn-danger">بازگشت</button>
        </div>

        <div class="col-xs-12">
            <p id="err" class="errorText"></p>
        </div>

    </center>

    <span id="errAddBox" class="ui_overlay item hidden" style="position: fixed; left: 30%; width: 40%; right: auto; top: 100px; bottom: auto; max-height: 60vh; overflow: auto">
        <div onclick="$('.item').addClass('hidden'); $('.dark').addClass('hidden');" class="ui_close_x"></div>
        <div class="header_text">خطا</div>
        <div class="body_text">
            <div class="col-xs-12">
                <p>آیتم مورد نظر در لیست خرید وجود دارد.</p>
            </div>
        </div>
    </span>

    <span id="errAddBox2" class="ui_overlay item hidden" style="position: fixed; left: 30%; width: 40%; right: auto; top: 100px; bottom: auto; max-height: 60vh; overflow: auto">
        <div onclick="$('.item').addClass('hidden'); $('.dark').addClass('hidden');" class="ui_close_x"></div>
        <div class="header_text">خطا</div>
        <div class="body_text">
            <div class="col-xs-12">
                <p>درس و یا پایه تحصیلی مورد نظر در لیست خرید وجود دارد.</p>
            </div>
        </div>
    </span>

    <span id="errAddBox3" class="ui_overlay item hidden" style="position: fixed; left: 30%; width: 40%; right: auto; top: 100px; bottom: auto; max-height: 60vh; overflow: auto">
        <div onclick="$('.item').addClass('hidden'); $('.dark').addClass('hidden');" class="ui_close_x"></div>
        <div class="header_text">خطا</div>
        <div class="body_text">
            <div class="col-xs-12">
                <p>تعداد سوالات بانک به اندازه مورد نظر نمی رسد</p>
            </div>
        </div>
    </span>

    <span id="recipe" class="ui_overlay item hidden" style="position: fixed; left: 30%; width: 40%; right: auto; top: 100px; bottom: auto; max-height: 60vh; overflow: auto">
        <div onclick="$('.item').addClass('hidden'); $('.dark').addClass('hidden');" class="ui_close_x"></div>
        <div class="header_text">اطلاعات جعبه</div>
        <div  style='max-height: 30vh; min-height: 30vh; overflow: auto' id="recipeBody" class="body_text"></div>

        <center>
            <button id="transactionBtn" onclick="goToPreTransaction()" class="btn btn-success">تایید و پرداخت</button>
        </center>
    </span>
@stop