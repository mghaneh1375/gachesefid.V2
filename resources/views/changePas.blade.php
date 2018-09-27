@extends('layouts.form')

@section('head')
    @parent
    <link rel="stylesheet" href="{{URL::asset('css/form.css')}}">
@stop


@section('caption')
    <div class="title">تغییر رمزعبور
    </div>
@stop

@section('main')
    
    <form method="post" action="{{route('doChangePas')}}">
        {{csrf_field()}}
        <center class="myRegister">
            <div class="data row">
                <div class="col-xs-12">
                    <div class="col-xs-7">
                        <input type="password" name="oldPas" maxlength="40" required autofocus>
                    </div>
                    <div class="col-xs-5">
                        <span>رمزعبور فعلی</span>
                    </div>
                </div>
                <div class="col-xs-12">
                    <div class="col-xs-7">
                        <input type="password" name="newPas" maxlength="40" required>
                    </div>
                    <div class="col-xs-5">
                        <span>رمزعبور جدید</span>
                    </div>
                </div>
                <div class="col-xs-12">
                    <div class="col-xs-7">
                        <input type="password" name="confirmPas" maxlength="40" required>
                    </div>
                    <div class="col-xs-5">
                        <span>تکرار رمزعبور جدید</span>
                    </div>
                </div>

                <div class="col-xs-12">
                    <center>
                        <input type="submit" value="تایید" name="submitBtn">
                    </center>
                </div>

                <div class="col-xs-12">
                    <p class="errorText">
                        {{$msg}}
                    </p>
                </div>
            </div>
        </center>
    </form>

@stop