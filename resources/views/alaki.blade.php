@extends('layouts.form')

@section('head')
    @parent

    <script>
        var getRegularQuizesOfStdDir = '{{route('getRegularQuizesOfStd')}}';
        var registerableListDir = '{{route('registerableList')}}';
        var submitRegistryDir = '{{route('submitRegistry')}}';
        var getQueuedQuizesDir = '{{route('getQueuedQuizes')}}';

        $(document).ready(function () {
            screen.orientation.lock('landscape');
        });

    </script>

    <script src="{{URL::asset('js/jsNeededForGroupQuizRegistration.js')}}"></script>

    <style>
        td {
            padding: 10px;
        }
    </style>
@stop


@section('caption')
    <div class="title">ثبت نام گروهی در آزمون
    </div>
@stop

@section('main')

    <center class="col-xs-12 hiddenOnMobile" style="margin-top: 20px">
        <table>
            @foreach($students as $student)
                <tr>
                    <td>{{$student->firstName . ' ' . $student->lastName}}</td>
                    <td>
                        <button class="btn btn-success" onclick="registerableList('{{$student->id}}')">ثبت نام جدید</button>
                    </td>
                    <td>
                        <button class="btn btn-primary" onclick="getRegularQuizesOfStd('{{$student->id}}')">آزمون های ثبت نام شده</button>
                    </td>
                    <td>
                        <button class="btn btn-default" onclick="getQueuedQuizes('{{$student->id}}')">در حال بررسی</button>
                    </td>
                </tr>
            @endforeach
        </table>
    </center>



    <center class="col-xs-12 hiddenOnScreen" style="margin-top: 20px">

        <style>
            .col-xs-12 {
                margin-top: 10px;
            }

            .col-xs-12 > button {
                min-width: 200px;
            }
        </style>

        @foreach($students as $student)

            <div class="col-xs-12"></div>
            <div class="col-xs-12">
                <button class="btn btn-success" onclick="registerableList('{{$student->id}}')">ثبت نام جدید</button>
            </div>
            <div class="col-xs-12">
                <button class="btn btn-primary" onclick="getRegularQuizesOfStd('{{$student->id}}')">آزمون های ثبت نام شده</button>
            </div>
            <div class="col-xs-12" style="border-bottom: 2px solid #ccc; padding-bottom: 10px">
                <button class="btn btn-default" onclick="getQueuedQuizes('{{$student->id}}')">در حال بررسی</button>
            </div>
        @endforeach
    </center>


    <span id="quizStatus" class="ui_overlay item hidden" style="position: fixed; left: 40%; right: auto; top: 174px; bottom: auto">
        <div class="header_text">آزمون های ثبت نام شده</div>
        <div onclick="hideElement()" class="ui_close_x"></div>
        <div class="body_text" id="quizes">
        </div>
    </span>

    <span id="queuedQuiz" class="ui_overlay item hidden" style="position: fixed; left: 40%; right: auto; top: 174px; bottom: auto">
        <div class="header_text">آزمون های در حال بررسی</div>
        <div onclick="hideElement()" class="ui_close_x"></div>
        <div class="body_text" id="queuedQuizes">
        </div>
    </span>

    <span id="quizRegistry" class="ui_overlay item hidden" style="position: fixed; left: 25%; width: 55%; right: auto; top: 174px; bottom: auto">
        <div class="header_text">آزمون های موجود</div>
        <div onclick="hideElement()" class="ui_close_x"></div>
        <div class="body_text" id="availableQuizes">
        </div>
    </span>

@stop