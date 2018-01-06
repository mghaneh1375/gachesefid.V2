@extends('layouts.form')

@section('head')
    @parent

    <script>
        var getStates = '{{route('getCities')}}';
        var sendSMSDir = '{{route('sendSMS')}}';
        var profileDir = '{{route('profile')}}';
        var sendSMSStatus = '{{route('sendSMSStatus')}}';
    </script>
    
    <script src="{{URL::asset('js/smsPanel.js')}}"></script>
    
    <style>
        .col-xs-12 {
            margin-top: 10px;
        }
        label {
            min-width: 200px;
        }

        label > span {
            float: right;
        }

        .mySelect {
            min-width: 200px !important;
        }
        textarea {
            width: 400px;
            height: 200px;
        }
    </style>
@stop

@section('caption')
    <div class="title">سامانه پیام رسانی
    </div>
@stop

@section('main')

    <center class="col-xs-12" style="margin-top: 50px">
        <div class="col-xs-12">
            <label for="state"><span>استان</span></label>
            <select id="state" class="mySelect" onchange="getCities()">
                <option value="-1">مهم نیست</option>
                @foreach($states as $state)
                    <option value="{{$state->id}}">{{$state->name}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-xs-12">
            <label for="city"><span>شهر</span></label>
            <select id="city" class="mySelect">
                <option value="-1">مهم نیست</option>
            </select>
        </div>
        <div class="col-xs-12">
            <label for="quiz"><span>آزمون</span></label>
            <select id="quiz" class="mySelect">
                <option value="-1_1">مهم نیست</option>
                @foreach($quizes as $quiz)
                    @if($quiz['quizMode'] == getValueInfo('regularQuiz'))
                        <option value="{{$quiz['id']}}_{{$quiz['quizMode']}}">{{$quiz['name']}}</option>
                    @else
                        <option value="{{$quiz['id']}}_{{$quiz['quizMode']}}">{{$quiz['name']}}</option>
                    @endif
                @endforeach
            </select>
        </div>
        <div class="col-xs-12">
            <label for="level"><span>سطح دسترسی</span></label>
            <select id="level" class="mySelect">
                <option value="{{getValueInfo('studentLevel')}}">دانش آموز</option>
                <option value="{{getValueInfo('adviserLevel')}}">مشاور</option>
                <option value="{{getValueInfo('controllerLevel')}}">ناظر</option>
                <option value="{{getValueInfo('operator1Level')}}">اپراتور 1</option>
                <option value="{{getValueInfo('operator2Level')}}">اپراتور 2</option>
            </select>
        </div>
        <div class="col-xs-12">
            <label for="sex"><span>جنسیت</span></label>
            <select id="sex" class="mySelect">
                <option value="-1">مهم نیست</option>
                <option value="1">آقا</option>
                <option value="0">خانم</option>
            </select>
        </div>
        <div class="col-xs-12">
            <label for="grade"><span>پایه تحصیلی</span></label>
            <select id="grade" class="mySelect">
                <option value="-1">مهم نیست</option>
                @foreach($grades as $grade)
                    <option value="{{$grade->id}}">{{$grade->name}}</option>
                @endforeach
            </select>
        </div>

        <div class="col-xs-12">
            <label for="templateId"><span>قالب متن</span></label>
            <select onchange="changeTemplate()" id="templateId" class="mySelect">
                <option value="-1">جدید</option>
                <option value="2">اتمام آزمون</option>
                <option value="3">آزمون جدید</option>
                <option value="4">یادآوری آزمون</option>
            </select>
        </div>

        <div class="col-xs-12 customMsg">
            <label for="text"><span>متن پیامک</span></label>
            <textarea id="text"></textarea>
        </div>

        <div class="col-xs-12">
            <label for="sendToAll"><span>ارسال به همه</span></label>
            <input type="checkbox" name="sendToAll" id="sendToAll">
        </div>

        <div class="col-xs-12">
            <input type="submit" onclick="sendSMS()" class="btn btn-success" value="ارسال">
        </div>

        <div class="col-xs-12 hidden" id="progressDiv">
            <p id="progress"></p>
        </div>

    </center>

@stop