@extends('layouts.form2')

@section('head')
    @parent
@stop


@section('caption')
    <div class="title">حذف جدول تراز آزمون
    </div>
@stop


@section('main')

    @if($mode == "select")

        <form method="post" action="{{route('deleteTarazTable')}}">
            {{csrf_field()}}
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
            <center>
                <input name="deleteTaraz" type="submit" class="btn btn-primary" value="تایید">
                @if(!empty($msg))
                    <p style="margin-top: 10px" class="errorText">{{$msg}}</p>
                @endif
            </center>
        </form>

    @endif
@stop