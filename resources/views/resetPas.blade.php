@extends('layouts.form')

@section('head')
    @parent
    <link rel="stylesheet" href="{{URL::asset('css/restPasCSS.css')}}">
    <script src="{{URL::asset('js/jsNeededForResetPas.js')}}"></script>

    <script>
        var resetPasPath = '{{route('resetPas')}}';
    </script>

@stop

@section('caption')
    <div class="title">بازیابی رمز عبور
    </div>
@stop

@section('main')

    <div class="MemberResetPassword">

        <div class="BODYCON">

            <div class="ui_container">

                <div style="padding-right: 15px;padding-top: 17px;text-align: right;" class="col one">

                    <div style="padding: 10px; padding-right: 0; border-bottom: 2px dotted black">
                        <label style="min-width: 100px">نام کاربری</label>
                        <input type="text" id="username" name="username" maxlength="40" required><span class="required">*</span>
                    </div>

                    <div>
                        <label style="min-width: 100px">ایمیل </label>
                        <input type="email" id="email"/>
                    </div>

                    <p style="margin-top: 10px">یا</p>

                    <div>
                        <label style="min-width: 100px">شماره ی تلفن </label>
                        <input type="text" onkeypress="validate(event)" id="phone"/>
                    </div>
                    <center>
                        <input type="submit" style="margin-top: 20px" onclick="resetPas('notice')" class="btn btn-success" value="بازیابی رمز عبور">
                        <div id="msg" class="errorText"  style="visibility: hidden"></div>
                    </center>
                </div>

                <div id="notice" style="display: none">
                    <p style="padding-top: 20px;"><strong>رمز عبور جدید به ایمیل/موبایل شما ارسال شد.</strong></p>
                    <p>موفق باشی فراموش‌ کار!</p>
                </div>
            </div>
        </div>
    </div>
@stop