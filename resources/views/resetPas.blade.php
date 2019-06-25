@extends('layouts.form')

@section('head')
    @parent
    <link rel="stylesheet" href="{{URL::asset('css/restPasCSS.css')}}">
    <script src="{{URL::asset('js/jsNeededForResetPas.js')}}"></script>

    <script>
        var resetPasPath = '{{route('doResetPas')}}';
    </script>

    <style>
        .animatedContainer {
            z-index: 1000000110101;
            width: 200px;
            height: 200px;
            position: absolute;
            top: 35%;
            left: 40%;
            transform: translate(-50%, -50%);
            margin: auto;
            filter: url('#goo');
            animation: rotate-move 2s ease-in-out infinite;
        }

        .dot {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background-color: #000;
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            margin: auto;
        }

        .dot-3 {
            background-color: #f74d75;
            animation: dot-3-move 2s ease infinite, index 6s ease infinite;
        }

        .dot-2 {
            background-color: #10beae;
            animation: dot-2-move 2s ease infinite, index 6s -4s ease infinite;
        }

        .dot-1 {
            background-color: #ffe386;
            animation: dot-1-move 2s ease infinite, index 6s -2s ease infinite;
        }

        @keyframes dot-3-move {
            20% {transform: scale(1)}
            45% {transform: translateY(-18px) scale(.45)}
            60% {transform: translateY(-90px) scale(.45)}
            80% {transform: translateY(-90px) scale(.45)}
            100% {transform: translateY(0px) scale(1)}
        }

        @keyframes dot-2-move {
            20% {transform: scale(1)}
            45% {transform: translate(-16px, 12px) scale(.45)}
            60% {transform: translate(-80px, 60px) scale(.45)}
            80% {transform: translate(-80px, 60px) scale(.45)}
            100% {transform: translateY(0px) scale(1)}
        }

        @keyframes dot-1-move {
            20% {transform: scale(1)}
            45% {transform: translate(16px, 12px) scale(.45)}
            60% {transform: translate(80px, 60px) scale(.45)}
            80% {transform: translate(80px, 60px) scale(.45)}
            100% {transform: translateY(0px) scale(1)}
        }

        @keyframes rotate-move {
            55% {transform: translate(-50%, -50%) rotate(0deg)}
            80% {transform: translate(-50%, -50%) rotate(360deg)}
            100% {transform: translate(-50%, -50%) rotate(360deg)}
        }

        @keyframes index {
            0%, 100% {z-index: 3}
            33.3% {z-index: 2}
            66.6% {z-index: 1}
        }
    </style>
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
                        <label style="min-width: 100px">نام کاربری یا کد ملی</label>
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
                        <input type="submit" style="margin-top: 20px; padding: 10px" onclick="resetPas('notice')" class="btn btn-success" value="بازیابی رمز عبور">
                        <div id="msg" class="errorText"  style="visibility: hidden"></div>
                    </center>
                </div>

                <div class="animatedContainer hidden">
                    <div class="dot dot-1"></div>
                    <div class="dot dot-2"></div>
                    <div class="dot dot-3"></div>
                </div>

                <svg xmlns="http://www.w3.org/2000/svg" version="1.1">
                    <defs>
                        <filter id="goo">
                            <feGaussianBlur in="SourceGraphic" stdDeviation="10" result="blur" />
                            <feColorMatrix in="blur" mode="matrix" values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 21 -7"/>
                        </filter>
                    </defs>
                </svg>

                <span id="notice" class="ui_overlay item hidden" style="position: fixed; left: 30%; right: 30%; top: 60px; bottom: auto">
                    <p style="padding-top: 20px;"><strong>رمز عبور جدید به ایمیل/موبایل شما ارسال شد.</strong></p>
                    <p>موفق باشی فراموش‌ کار!</p>
                    <center>
                        <button onclick="document.location.href = '{{route('login')}}'">تایید</button>
                    </center>
                </span>
            </div>
        </div>
    </div>
@stop