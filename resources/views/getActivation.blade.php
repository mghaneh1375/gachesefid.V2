@extends('layouts.form')

@section('head')
    @parent
@stop


@section('main')

    <center class="col-xs-12">
        <div style="max-width: 500px !important;" class="header_text">شماره تماس</div>
            <div class="body_text">
                <form method="post" action="{{route('getActivation')}}">
                    {{csrf_field()}}
                    <input type="text" name="phoneNum" maxlength="50" autofocus>
                    <center class="submitOptions" style="margin-top: 10px">
                        <input type="submit" class="btn btn-success" value="تایید">
                        @if(!empty($err))
                            <p style="padding: 10px; margin-top: 10px" class="errorText">{{$err}}</p>
                            <a href="{{route('resetPas')}}">رمز عبور خود را فراموش کرده اید!</a>
                        @endif
                    </center>
                </form>
            </div>
    </center>

@stop