@extends('layouts.form')

@section('head')
    @parent
@stop


@section('caption')
    <div class="title">گزارشات
    </div>
@stop

@section('main')

    <center class="col-xs-12" style="margin-top: 50px">

        @if(count($quizes) == 0)
            <p>دانش‌آموزان شما در آزمونی شرکت نکرده‌اند.</p>
        @else
            <div>
                <label style="min-width: 300px !important;">
                    <span>آزمون مورد نظر</span>
                    <select class="mySelect" style="min-width: 200px !important;" id="quizId">
                        @foreach($quizes as $quiz)
                            <option value="{{route('getQuizReport', ['quizId' => $quiz->id])}}">{{$quiz->name}}</option>
                        @endforeach
                    </select>
                </label>
            </div>

            <button style="margin-top: 10px" onclick="document.location.href = $('#quizId').val()" class="btn btn-primary">تایید</button>
        @endif

    </center>

@stop