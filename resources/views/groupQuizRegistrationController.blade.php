@extends('layouts.form')

@section('head')
    @parent

    <script>
        var studentsOfAdviserInQuizDir = '{{route('studentsOfAdviserInQuiz')}}';
        var quizId = '{{$quiz->id}}';
        var selectedAdviserId;
        var totalRegister = '{{route('totalRegister')}}';
    </script>

    <script src="{{URL::asset('js/groupQuizRegistrationController.js')}}"></script>
    <style>
        td {
            padding: 10px;
        }
    </style>
@stop


@section('caption')
    <div class="title"> وضعیت ثبت نام های گروهی آزمون {{$quiz->name}}
    </div>
@stop

@section('main')

    <center class="row" style="margin-top: 20px">
        <table>
            @foreach($advisers as $adviser)
                <tr>
                    <td>{{$adviser->firstName . " " . $adviser->lastName}}</td>
                    <td>{{$adviser->phoneNum}}</td>
                    <td><button onclick="studentsOfAdviserInQuiz('{{$adviser->id}}')" class="btn btn-success">مشاهده دانش آموزان</button></td>
                </tr>
            @endforeach
        </table>
    </center>

    <span id="studentsPane" class="ui_overlay item hidden" style="position: fixed; left: 40%; right: auto; top: 174px; bottom: auto">
        <div class="header_text">دانش آموزان</div>
        <div onclick="hideElement()" class="ui_close_x"></div>
        <div class="body_text" style="margin-top: 20px; max-height: 300px; overflow: auto" id="students">
        </div>

        <label>
            <span>مبلغ دریافتی برای ثبت نام</span>
            <input type="text" id="totalPrice">
        </label>

        <button onclick="register()" class="btn btn-primary">تایید</button>
    </span>
@stop