@extends('layouts.form')

@section('head')
    @parent
@stop


@section('caption')
    <div class="title">گزارشات
    </div>
@stop

@section('main')

    <style>
        .btn {
            width: 200px;
            height: 100px;
            border-radius: 15px;
            margin: 20px;
        }
        .btn p {
            margin-top: 35px;
        }
    </style>

    <center class="col-xs-12" style="margin-top: 50px">

        <div onclick="document.location.href = '{{route('chooseSystemQuiz')}}'" class="btn btn-info">
            <p>سنجش پای تخته</p>
        </div>

        <div onclick="document.location.href = '{{route('chooseRegularQuiz')}}'" class="btn btn-warning">
            <p>سنجش پشت میز</p>
        </div>

    </center>

@stop