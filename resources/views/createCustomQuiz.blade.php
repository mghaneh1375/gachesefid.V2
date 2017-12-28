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
    </style>

    <script src="{{URL::asset('js/jsNeededForBuyQuestion.js')}}"></script>

@stop

@section('caption')
    <div class="title">ساخت آزمون
    </div>
@stop


@section('main')

    <div class="col-xs-12 receipt">
        <center class="col-xs-6" style="margin-top: 10px; border-right: 2px dotted black; height: 60vh; overflow: auto">
            <h4 style="border-bottom: 2px solid black">فاکتور خرید</h4>
            <h5 style="margin-top: 10px" id="totalPrice"></h5>
            <p class="errorText hidden" id="errMsg"></p>
        </center>
        <center class="col-xs-6" style="margin-top: 10px">
            <h4 style="border-bottom: 2px solid black">جعبه های موجود</h4>
            <div class="col-xs-12" id="boxes"></div>
            <div style="margin-top: 20px" data-toggle="tooltip" title="افزودن جعبه ی جدید">
                <button onclick="addBox()" class="btn btn-primary circleBtn"><span class="glyphicon glyphicon-plus"></span></button>
            </div>
        </center>
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

    <span id="boxInfo" class="ui_overlay item hidden" style="position: fixed; left: 30%; width: 40%; right: auto; top: 100px; bottom: auto; max-height: 60vh; overflow: auto">
        <div onclick="$('.item').addClass('hidden'); $('.dark').addClass('hidden');" class="ui_close_x"></div>
        <div class="header_text">اطلاعات جعبه</div>
        <div class="body_text">
            <div class="col-xs-12">
                <p><span>نام جعبه:</span><span>&nbsp;</span><span id="boxName"></span></p>
                <p><span>پایه تحصیلی:</span><span>&nbsp;</span><span id="boxGrade"></span></p>
                <p><span>درس:</span><span>&nbsp;</span><span id="boxLesson"></span></p>
                <p><span>مبحث:</span><span>&nbsp;</span><span id="boxSubject"></span></p>
                <p><span>تعداد سوالات:</span><span>&nbsp;</span><span id="boxQNo"></span></p>
                <p><span>انتخاب بر اساس بیشترین لایک:</span><span>&nbsp;</span><span id="boxLike"></span></p>
                <p>سوالات</p>
                <p id="questionsDiv"></p>
            </div>
        </div>
    </span>
@stop