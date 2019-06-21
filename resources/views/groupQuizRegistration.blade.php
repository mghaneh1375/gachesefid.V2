@extends('layouts.form')

@section('head')
    @parent

    <script>
        var getRegularQuizesOfStdDir = '{{route('getRegularQuizesOfStd')}}';
        var registerableListDir = '{{route('registerableList')}}';
        var getQueuedQuizesDir = '{{route('getQueuedQuizes')}}';
        var getStdOfQuizDir = '{{route('getStdOfQuiz')}}';
        var submitRegistryDir = '{{route('submitRegistry')}}';
        var deleteFromQueueDir = '{{route('deleteFromQueue')}}';

        $(document).ready(function () {
            screen.orientation.lock('landscape');
        });

    </script>

    <script src="{{URL::asset('js/jsNeededForGroupQuizRegistration.js')}}"></script>

    <style>

        .spinner {
            width: 50px;
            height: 40px;
            text-align: center;
            font-size: 10px;
        }

        td {
            padding: 10px;
            min-width: 300px;
        }
        tr:nth-child(even) {
            background-color: #ccc;
        }
    </style>
@stop


@section('caption')
    <div class="title">ثبت نام در آزمون
    </div>
@stop

@section('main')

    <center style="margin-top: 50px">
        <div class="col-xs-12">
            <label style="float: right; font-size: 20px; color: #d9534f; padding: 10px" for="qId">توجه: ابتدا آزمون مورد نظر خود را انتخاب کنید</label>
            <select id="qId" onchange="getStdOfQuiz(this.value)">
                @foreach($quizes as $quiz)
                    <option data-presence="{{$quiz->presence}}" value="{{$quiz->id}}">{{$quiz->name}}- شروع آزمون:
                        {{$quiz->startDate}}- اتمام آزمون:
                        {{$quiz->endDate}}- شروع ثبت نام:
                        {{$quiz->startReg}}- اتمام ثبت نام:
                        {{$quiz->endReg}}
                    </option>
                @endforeach
            </select>
        </div>
    </center>

    <center class="col-xs-12" style="margin-top: 20px;  max-height: 50vh; overflow: auto">
        <p class="errorText hidden" id="msg"></p>
        <div class="spinner hidden">
            <div class="rect1"></div>
            <div class="rect2"></div>
            <div class="rect3"></div>
            <div class="rect4"></div>
            <div class="rect5"></div>
        </div>

        <table style="margin-top: 10px;">
            <tr>
                <td><center>دانش آموز</center></td>
                <td><center>وضعیت</center></td>
                <td><center><span>انتخاب همه</span><span style="margin-right: 5px"><input type="checkbox" onclick="selectAll($(this).is(':checked'))"></span></center></td>
            </tr>

            @foreach($students as $student)
                <tr>
                    <td><center>{{$student->firstName . ' ' . $student->lastName}}</center></td>
                    <td><center id="status_{{$student->id}}"></center></td>
                    <td><center><input value="{{$student->id}}" type="checkbox" name="selectedStd[]"></center></td>
                </tr>
            @endforeach

        </table>
    </center>

    <center class="col-xs-12" style="padding: 10px">
        <button id="presenceBtn" onclick="submitRegistry('nonOnline')" class="btn btn-primary">ثبت نام در آزمون حضوری</button>
        <button onclick="submitRegistry('online')" class="btn btn-success">ثبت نام در آزمون آنلاین</button>
        <button onclick="deleteFromQueue()" class="btn btn-danger">حذف از در حال بررسی</button>
    </center>

@stop