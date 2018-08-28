@extends('layouts.form')

@section('head')
    @parent
@stop


@section('main')

    <span class="ui_overlay item" style="position: fixed; left: 40%; right: auto; top: 174px; bottom: auto">
        <div class="header_text">شماره تماس</div>
            <div class="body_text">
                <form method="post" action="{{route('getActivation')}}">
                    {{csrf_field()}}
                    <input type="text" name="phoneNum" maxlength="50" autofocus>
                    <div class="submitOptions" style="margin-top: 10px">
                        <input type="submit" class="btn btn-success" value="تایید">
                        @if(!empty($err))
                            <p style="padding: 10px" class="errorText">{{$err}}</p>
                        @endif
                    </div>
                </form>
            </div>
    </span>


@stop