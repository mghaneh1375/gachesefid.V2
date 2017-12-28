@extends('layouts.form')

@section('head')
    @parent
@stop


@section('caption')
    <div class="title">خطای {{$err}}
    </div>
@stop

@section('main')
    <center style="margin-top: 100px">
        @if($err == "404")
            <h3>صفحه مورد نظر وجود ندارد</h3>
        @elseif($err == "سیستمی")
            <h3>ببخشید که هم اکنون مشکلی در این بخش وجود دارد.</h3>
            <p>لطفا آن با را با ما در میان بگذارید<span>[info@irysc.com]</span>یا کمی شکیبا باشید.</p>
        @endif
    </center>
@stop