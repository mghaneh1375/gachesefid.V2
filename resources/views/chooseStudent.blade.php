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

        @if(!empty($err))
            <p>{{$err}}</p>
        @endif

        <div class="col-xs-12">
            <button style="margin-top: 10px; min-width: 300px" class="btn btn-danger" onclick="document.location.href = '{{route('getQuizReport', ['quizId' => $quizId])}}'">بازگشت</button>
        </div>

        @if(count($uIds) == 0)
            <p>دانش آموزی وجود ندارد</p>
        @else
            @foreach($uIds as $uId)
                <div class="col-xs-12">
                    <button style="margin-top: 10px; min-width: 300px" class="btn btn-primary" onclick="document.location.href = '{{route('A3', ['quizId' => $quizId, 'uId' => $uId->id])}}'">{{$uId->firstName . " " . $uId->lastName}}</button>
                </div>
            @endforeach
        @endif

    </center>

@stop