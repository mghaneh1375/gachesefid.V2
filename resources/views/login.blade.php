@extends('layouts.form')

@section('head')
    @parent
    <link href="{{URL::asset('css/form.css')}}" rel="stylesheet" type="text/css">
@stop

@section('caption')
    <div class="title">وارد شوید!</div>
@stop

@section('main')
    <form method="post" action="{{route('doLogin')}}">
        {{ csrf_field() }}
        <center class="myRegister">
            <div class="row data">
                <div class="col-xs-12">
                    <div class="col-xs-7">
                        <input type="text" name="username" maxlength="40" required>
                    </div>
                    <div class="col-xs-5">
                        <span>نام کاربری</span>
                    </div>
                </div>
                <div class="col-xs-12">
                    <div class="col-xs-7">
                        <input type="password" name="password" value="" maxlength="40" required>
                    </div>
                    <div class="col-xs-5">
                        <span>رمز عبور</span>
                    </div>
                </div>
                <div class="col-xs-12">
                    <center>
                        <input type="submit" name="login" value="ورود">
                        @if(isset($msg) && !empty($msg))
                            <p class="errorText">{{$msg}}</p>
                        @endif
                    </center>
                </div>
            </div>
        </center>
    </form>

    <div class="col-xs-12">
        <center id="recPass">
            <a href="{{route('resetPas')}}">رمز عبور را فراموش کردم!</a>

            <center style="margin-top: 10px">
                <p class="errorText">نام کاربری و رمز ورود برای تمام دانش‌آموزانی که در آزمون به صورت حضوری شرکت کرده‌اند، به مسوول حوزه‌ی ایشان تحویل داده شده است. لطفاً و حتماً این اطلاعات را فقط از ایشان دریافت کنید.</p>
                {{--<p>درصورت بروز هر مشکلی با پشتیبانی تماس بگیرید.(09214915905)</p>--}}
            </center>
        </center>
    </div>
@stop