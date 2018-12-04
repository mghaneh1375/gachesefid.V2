@extends('layouts.form')

@section('head')
    @parent
    <link href="{{URL::asset('css/form.css')}}" rel="stylesheet" type="text/css">
@stop

@section('caption')
    <div class="title">وارد شوید!</div>
@stop

@section('main')

    <form method="post" action="{{route('checkLogin')}}">
        {{ csrf_field() }}
        <center class="myRegister">
            <div class="row data">
                <div class="col-xs-12">
                    <div class="col-xs-7">
                        <input type="text" name="username" maxlength="40" required>
                    </div>
                    <div class="col-xs-5">
                        <span>نام کاربری یا شماره موبایل یا ایمیل</span>
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
                            @if($msg == "حساب کاربری شما هنوز فعال نشده است")
                                <a class="btn btn-warning" href="{{route('getActivation')}}">وارد کردن کد فعال سازی</a>
                            @endif
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
                <p style="width: 500px !important; text-align: justify" class="hidden errorText">نام کاربری و رمز ورود برای تمام دانش‌آموزانی که در آزمون به صورت حضوری شرکت کرده‌اند، به مسوول حوزه‌ی ایشان تحویل داده شده است. لطفاً و حتماً این اطلاعات را فقط از ایشان دریافت کنید.</p>
                <p>درصورت بروز هر مشکلی در ساعات اداری با پشتیبانی تماس بگیرید.(1 - 66917230 - 021) یا 09214915905</p>
            </center>
        </center>
    </div>
@stop