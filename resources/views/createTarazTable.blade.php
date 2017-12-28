@extends('layouts.form2')

@section('head')
    @parent
@stop


@section('caption')
    <div class="title">ساخت جدول تراز آزمون
    </div>
@stop


@section('main')

    @if($mode == "select" || $mode == "err")

        <form method="post" action="{{route('createTarazTable')}}">
            <center class="col-xs-12" style="margin-top: 100px">
                <label>
                    <span>آزمون مورد نظر</span>
                    <select class="mySelect" name="quizId">
                        @foreach($quizes as $quiz)
                            <option value="{{$quiz->id}}">{{$quiz->name}}</option>
                        @endforeach
                    </select>
                </label>
            </center>
            <center class="col-xs-12">
                <label>
                    <span>امتیاز دهی به نفرات برتر</span>
                    <input name="final" type="checkbox">
                </label>
            </center>
            <center>
                <input name="submitQID" type="submit" class="btn btn-primary" value="تایید">
                @if($mode == "err")
                    <p style="margin-top: 10px" class="errorText">جدول تراز برای آزمون مورد نظر ساخته شده است</p>
                @endif
            </center>
        </form>

    @endif
@stop