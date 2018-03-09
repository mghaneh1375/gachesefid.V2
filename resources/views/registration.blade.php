@extends('layouts.form')

@section('head')
    @parent

    <link href="{{URL::asset('css/form.css')}}" rel="stylesheet" type="text/css">

    <script>
        function validate(evt) {
            var theEvent = evt || window.event;
            var key = theEvent.keyCode || theEvent.which;
            key = String.fromCharCode( key );
            var regex = /[0-9]|\./;
            if( !regex.test(key) ) {
                theEvent.returnValue = false;
                if(theEvent.preventDefault) theEvent.preventDefault();
            }
        }
    </script>

    @if($mode == "pending")
        <script>

            var total_time = "{{$reminder}}";
            var c_minutes = parseInt(total_time / 60);
            var c_seconds = parseInt(total_time % 60);

            $(document).ready(function () {

                if (total_time > 0)
                    setTimeout("checkTime()", 1);
                else
                    showResendBtn();
            });

            function checkTime() {
                document.getElementById("reminder_time").innerHTML =  c_seconds + " : " + c_minutes;
                if (total_time <= 0)
                    setTimeout("showResendBtn()", 1);
                else {
                    total_time--;
                    c_minutes = parseInt(total_time / 60);
                    c_seconds = parseInt(total_time % 60);
                    setTimeout("checkTime()", 1000);
                }
            }

            function showResendBtn() {

                newElement = "<center>";
                newElement += '<input type="hidden" name="phoneNum" value="' + {{$phoneNum}} + '">';
                newElement += '<input type="hidden" name="uId" value="' + {{$uId}} + '">';
                newElement += "<input type='submit' value='ارسال مجدد کد فعال سازی' name='resendActivation'>";
                newElement += "</center>";

                $("#activationCode").removeAttr('required');
                $("#reminderTimeDiv").css("visibility", "hidden");
                $("#resendDiv").append(newElement);
            }
        </script>
    @endif

@stop

@section('caption')

    @if($mode == "pass1")
        <div class="title">        فرم ثبت نام
        </div>

    @else
        <div class="title">تایید حساب کاربری
        </div>
    @endif

    <div class="line"></div>
@stop

@section('main')

    <div class="col-md-2 col-xs-12 col-md-push-10 hiddenOnScreen">
        <div onclick="document.location.href = '{{route('home')}}'" class="SiteName" style="position: fixed; z-index: 10001; cursor: pointer">
            <img class="mobile-gach-icon" src="{{URL::asset('images/banner-gach-4.png')}}">
        </div>
    </div>

    <form method="post" action="{{route('doRegistration')}}">
        <center class="myRegister">
            <div class="row data">

                @if($mode == "pass1")

                    <div class="col-xs-12">
                        <div class="col-xs-7">
                            <input type="text" name="firstName" value="{{(isset($firstName) ? $firstName : '')}}" maxlength="40" required>
                        </div>
                        <div class="col-xs-5">
                            <span>
                                <span  class="help" data-toggle="tooltip" data-placement="top" title="نام خود را وارد کنید.">
                                    <img src="{{URL::asset('images/help.png')}}" alt="FirstName">
                                </span> نام <span class="required">*</span>
                            </span>
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <div class="col-xs-7">
                            <input type="text" name="lastName" value="{{(isset($lastName) ? $lastName : '')}}" maxlength="40" required>
                        </div>
                        <div class="col-xs-5">
                        <span><span  class="help" data-toggle="tooltip" data-placement="top" title="نام خانوادگی خود را وارد کنید."><img
                                        src="{{URL::asset('images/help.png')}}" alt="LastName"></span> نام خانوادگی <span class="required">*</span></span>
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <div class="col-xs-7">
                            <input type="text" name="username" value="{{(isset($username) ? $username : '')}}" maxlength="40" required>
                        </div>
                        <div class="col-xs-5">
                        <span><span  class="help" data-toggle="tooltip" data-placement="top" title="به دل‌خواه یک نام کاربری انتخاب کنید. پیشنهاد می‌کنیم این نام با حروف انگلیسی و اعداد باشد."><img
                                        src="{{URL::asset('images/help.png')}}" alt="UserName"></span> نام کاربری <span class="required">*</span></span>
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <div class="col-xs-7">
                            <input type="password" name="password" value="" maxlength="40" required>
                        </div>
                        <div class="col-xs-5">
                        <span><span  class="help" data-toggle="tooltip" data-placement="top" title="رمز عبور خود را برای ورود به سامانه انتخاب کنید. "><img
                                        src="{{URL::asset('images/help.png')}}" alt="PassWord"></span> رمز عبور <span class="required">*</span></span>
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <div class="col-xs-7">
                            <input type="text" placeholder="09123456789" onkeypress="validate(event)" name="phoneNum" value="{{(isset($phoneNum) ? $phoneNum : '')}}" maxlength="40" required>
                        </div>
                        <div class="col-xs-5">
                        <span><span  class="help" data-toggle="tooltip" data-placement="top" title="شماره موبایل شما برای دریافت کد فعالسازی و ارتباط با سامانه نیاز است."><img
                                        src="{{URL::asset('images/help.png')}}" alt="PhoneNumber"></span> شماره موبایل <span class="required">*</span></span>
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <div class="col-xs-7">
                            <input type="text" onkeypress="validate(event)" name="NID" value="{{(isset($NID) ? $NID : '')}}" maxlength="40" required>
                        </div>
                        <div class="col-xs-5">
                        <span><span  class="help" data-toggle="tooltip" data-placement="top" title="کد ملی خود را وارد کنید."><img
                                        src="{{URL::asset('images/help.png')}}" alt="NationalID"></span> کد ملی <span class="required">*</span></span>
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <div class="col-xs-7">
                            <select class="mySelect" style="min-width: 200px !important;" name="sex" required>
                                <option value="none">انتخاب کنید</option>
                                @if(isset($sex) && $sex == 1)
                                    <option value="1" selected>آقا</option>
                                @else
                                    <option value="1">آقا</option>
                                @endif

                                @if(isset($sex) && !$sex)
                                    <option  value="0" selected>خانم</option>
                                @else
                                    <option value="0">خانم</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-xs-5">
                         <span>جنسیت <span class="required">*</span></span>
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <div class="col-xs-7">
                            <select class="mySelect"  style="min-width: 200px !important;" name="level">
                                <option value="none">انتخاب کنید</option>
                                @if(isset($level) && $level == "1")
                                    <option selected value="1">دانش آموز</option>
                                    <option value="2">مشاور</option>
                                @elseif(isset($level) && $level == "0")
                                    <option value="1">دانش آموز</option>
                                    <option selected value="2">مشاور</option>
                                @else
                                    <option value="1">دانش آموز</option>
                                    <option value="2">مشاور</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-xs-5">
                            <span>
                                <span class="help" data-toggle="tooltip" data-placement="top" title="انتخاب کنید که دانش‌آموز هستید یا مشاور تحصیلی">
                                    <img src="{{URL::asset('images/help.png')}}" alt="WhoAreYou?!">
                                </span> ثبت نام به عنوان <span class="required">*</span>
                            </span>
                        </div>

                    </div>

                    <div class="col-xs-12">
                        <div class="col-xs-7">
                            <input type="text" name="invitationCode" value="{{(isset($invitationCode) ? $invitationCode : '')}}" maxlength="40">
                        </div>
                        <div class="col-xs-5">
                        <span><span  class="help" data-toggle="tooltip" data-placement="top" title="اگر کسی گچ سفید را به شما معرفی کرده، کد وی را در این بخش وارد کنید."><img
                                        src="{{URL::asset('images/help.png')}}" alt=""></span>کد معرف </span>
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <center>
                            <input type="submit" name="doRegistration" value="ارسال">
                        </center>
                    </div>

                    @if(isset($msg))
                        <div class="col-xs-12">
                            <center>
                                <div class="errorText">{{$msg}}</div>
                            </center>
                        </div>
                    @endif

                @elseif($mode == "pending")

                    <div class="col-xs-12">
                        <div class="col-xs-7">
                            <input type="text" id="activationCode" name="activationCode" required autofocus maxlength="10">
                        </div>
                        <div class="col-xs-5">
                        <span>
                            <span  class="help" data-toggle="tooltip" data-placement="top" title="لطفا کد 5 رقمی ای را که برای شما ارسال شده؛ وارد نمایید">
                                <img src="{{URL::asset('images/help.png')}}" alt="">
                            </span> کد فعال سازی <span class="required">*</span>
                        </span>
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <center>
                            <input class="hidden" type="text" name="phoneNum" value="{{$phoneNum}}">
                            <input type="hidden" name="uId" value="{{$uId}}">
                            <input type="submit" name="activeProfile" value="فعال سازی حساب کاربری">
                        </center>
                    </div>
                    @if(isset($msg))
                        <div class="col-xs-12" style="margin-top: 10px">
                            <center>
                                <span class="errorText">{{$msg}}</span>
                            </center>
                        </div>
                    @endif

                    <div class="col-xs-12" id="reminderTimeDiv">
                        <p style="margin-top: 10px">زمان باقی مانده</p><div style="margin-top: 10px" id="reminder_time"></div>
                    </div>

                    <div id="resendDiv" class="col-xs-12"></div>

                @endif
            </div>
        </center>
    </form>

@stop
