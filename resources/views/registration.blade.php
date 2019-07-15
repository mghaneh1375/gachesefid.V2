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

                var newElement = "<center>";
                newElement += '<input type="text" class="hidden" name="phoneNum" value="' + {{$phoneNum}} + '">';
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
        <div class="title">فرم ثبت نام</div>
    @else
        <div class="title">تایید شماره موبایل</div>
    @endif

    <div class="line"></div>
@stop

@section('main')

    <div class="col-md-2 col-xs-12 col-md-push-10 hiddenOnScreen">
        <div onclick="document.location.href = 'http://gachesefid.com'" class="SiteName" style="position: fixed; z-index: 10001; cursor: pointer">
            <img class="mobile-gach-icon" src="{{URL::asset('images/banner-gach-4.png')}}">
        </div>
    </div>

    <form method="post" action="{{route('doRegistration')}}">
        {{csrf_field()}}
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
                            <span>
                                <span class="help" data-toggle="tooltip" data-placement="top" title="نام خانوادگی خود را وارد کنید.">
                                    <img src="{{URL::asset('images/help.png')}}" alt="LastName">
                                </span> نام خانوادگی
                                <span class="required">*</span>
                            </span>
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <div class="col-xs-7">
                            <input type="text" name="username" value="{{(isset($username) ? $username : '')}}" maxlength="40" required>
                        </div>
                        <div class="col-xs-5">
                            <span>
                                <span class="help" data-toggle="tooltip" data-placement="top" title="به دل‌خواه یک نام کاربری انتخاب کنید. پیشنهاد می‌کنیم این نام با حروف انگلیسی و اعداد باشد.">
                                    <img src="{{URL::asset('images/help.png')}}" alt="UserName">
                                </span> نام کاربری
                                <span class="required">*</span>
                            </span>
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <div class="col-xs-7">
                            <input type="password" name="password" value="" maxlength="40" required>
                        </div>
                        <div class="col-xs-5">
                            <span>
                                <span class="help" data-toggle="tooltip" data-placement="top" title="رمز عبور خود را برای ورود به سامانه انتخاب کنید. ">
                                    <img src="{{URL::asset('images/help.png')}}" alt="PassWord">
                                </span> رمز عبور
                                <span class="required">*</span>
                            </span>
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <div class="col-xs-7">
                            <input type="text" placeholder="09123456789" onkeypress="validate(event)" name="phoneNum" value="{{(isset($phoneNum) ? $phoneNum : '')}}" maxlength="40" required>
                        </div>
                        <div class="col-xs-5">
                            <span>
                                <span class="help" data-toggle="tooltip" data-placement="top" title="شماره موبایل شما برای دریافت کد فعالسازی و ارتباط با سامانه نیاز است.">
                                    <img src="{{URL::asset('images/help.png')}}" alt="PhoneNumber">
                                </span> شماره موبایل
                                <span class="required">*</span>
                            </span>
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <div class="col-xs-7">
                            <input type="text" onkeypress="validate(event)" name="NID" value="{{(isset($NID) ? $NID : '')}}" maxlength="40" required>
                        </div>
                        <div class="col-xs-5">
                            <span>
                                <span class="help" data-toggle="tooltip" data-placement="top" title="کد ملی خود را وارد کنید.">
                                    <img src="{{URL::asset('images/help.png')}}" alt="NationalID">
                                </span> کد ملی
                                <span class="required">*</span>
                            </span>
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
                            <select onchange="changeLevel(this.value)" class="mySelect"  style="min-width: 200px !important;" name="level">
                                <option value="none">انتخاب کنید</option>

                                @if(isset($level) && $level == "0")
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

                    <div class="row hidden" id="justForAdviser">

                        <div class="col-xs-12">
                            <div class="col-xs-7">
                                <select class="mySelect" id="states" onchange="getCities(this.value)"></select>
                            </div>
                            <div class="col-xs-5">
                                <span>
                                    <span  class="help" data-toggle="tooltip" data-placement="top" title="استان خود را با توجه به استان های موجود انتخاب نمایید">
                                        <img src="{{URL::asset('images/help.png')}}" alt="">
                                    </span>استان
                                </span>
                            </div>
                        </div>

                        <div class="col-xs-12">
                            <div class="col-xs-7">
                                <select class="mySelect" id="cities" name="cityId"></select>
                            </div>
                            <div class="col-xs-5">
                                <span>
                                    <span  class="help" data-toggle="tooltip" data-placement="top" title="شهر خود را با توجه به شهر های موجود انتخاب نمایید">
                                        <img src="{{URL::asset('images/help.png')}}" alt="">
                                    </span>شهر
                                </span>
                            </div>
                        </div>

                        <div class="col-xs-12">
                            <div class="col-xs-7">
                                <select class="mySelect" id="field" onchange="changeField(this.value)" name="field">
                                    <option value="{{getValueInfo('konkurAdvise')}}">کنکور</option>
                                    <option value="{{getValueInfo('olympiadAdvise')}}">المپیاد</option>
                                    <option value="{{getValueInfo('doore1Advice')}}">متوسطه دوره اول</option>
                                    <option value="{{getValueInfo('doore2Advice')}}">متوسطه دوره دوم</option>
                                    <option value="{{getValueInfo('baliniAdvice')}}">بالینی</option>
                                </select>
                            </div>
                            <div class="col-xs-5">
                                <span>
                                    <span  class="help" data-toggle="tooltip" data-placement="top" title="تخصص خود را از بین گزینه های موجود انتخاب نمایید">
                                        <img src="{{URL::asset('images/help.png')}}" alt="">
                                    </span>تخصص
                                </span>
                            </div>
                        </div>

                        <div class="col-xs-12" id="gradesDiv">
                            <div class="col-xs-7">
                                <div id="grades"></div>
                            </div>
                            <div class="col-xs-5">
                                <span>
                                    <span  class="help" data-toggle="tooltip" data-placement="top" title="تخصص خود را از بین گزینه های موجود انتخاب نمایید">
                                        <img src="{{URL::asset('images/help.png')}}" alt="">
                                    </span>رشته تحصیلی
                                </span>
                            </div>
                        </div>

                        <div class="col-xs-12">
                            <div class="col-xs-7">
                                <select class="mySelect" name="lastCertificate">
                                    <option value="{{getValueInfo('diplom')}}">دیپلم</option>
                                    <option value="{{getValueInfo('foghDiplom')}}">فوق دیپلم</option>
                                    <option value="{{getValueInfo('lisans')}}">لیسانس</option>
                                    <option value="{{getValueInfo('foghLisans')}}">فوق لیسانس</option>
                                    <option value="{{getValueInfo('phd')}}">دکترا</option>
                                </select>
                            </div>

                            <div class="col-xs-5">
                                <span>
                                    <span  class="help" data-toggle="tooltip" data-placement="top" title="آخرین مدرک تحصیلی خود را از بین گزینه های موجود انتخاب نمایید">
                                        <img src="{{URL::asset('images/help.png')}}" alt="">
                                    </span>آخرین مدرک تحصیلی
                                </span>
                            </div>
                        </div>

                        <div class="col-xs-12">
                            <div class="col-xs-7">
                                <textarea style="float: right; margin: 10px" name="honors" placeholder="حداکثر 1000 کاراکتر">{{(isset($honors)) ? $honors : ''}}</textarea>
                            </div>
                            <div class="col-xs-5">
                            <span>
                                <span class="help" data-toggle="tooltip" data-placement="top" title="افتخارات علمی خود را وارد کنید.">
                                    <img src="{{URL::asset('images/help.png')}}" alt="NationalID">
                                </span>افتخارات علمی
                            </span>
                            </div>
                        </div>

                        <div class="col-xs-12">
                            <div class="col-xs-7">
                                <textarea style="float: right; margin: 10px" name="essay" placeholder="حداکثر 1000 کاراکتر">{{isset($essay) ? $essay : ''}}</textarea>
                            </div>
                            <div class="col-xs-5">
                            <span>
                                <span class="help" data-toggle="tooltip" data-placement="top" title="تالیفات و ترجمه های خود را وارد کنید.">
                                    <img src="{{URL::asset('images/help.png')}}" alt="NationalID">
                                </span>تالیفات و ترجمه ها
                            </span>
                            </div>
                        </div>

                        <div class="col-xs-12">
                            <div class="col-xs-7">
                                <textarea style="float: right; margin: 10px" name="schools" placeholder="حداکثر 1000 کاراکتر">{{isset($schools) ? $schools : ''}}</textarea>
                            </div>
                            <div class="col-xs-5">
                            <span>
                                <span class="help" data-toggle="tooltip" data-placement="top" title="مدارس فعال خود را وارد کنید.">
                                    <img src="{{URL::asset('images/help.png')}}" alt="NationalID">
                                </span>مدارس فعال
                            </span>
                            </div>
                        </div>

                        <div class="col-xs-12">
                            <div class="col-xs-7">
                                <select name="workYears">
                                    @for($i = 1330; $i <= substr(getToday()['date'], 0, 4); $i++)
                                        @if(isset($workYears) && $workYears == $i)
                                            <option selected value="{{$i}}">{{$i}}</option>
                                        @else
                                            <option value="{{$i}}">{{$i}}</option>
                                        @endif
                                    @endfor
                                </select>
                            </div>
                            <div class="col-xs-5">
                                <span>
                                    <span class="help" data-toggle="tooltip" data-placement="top" title="سال شروع به کار خود را وارد کنید.">
                                        <img src="{{URL::asset('images/help.png')}}" alt="since">
                                    </span>سال شروع به کار
                                </span>
                            </div>
                        </div>

                        <div class="col-xs-12">
                            <div class="col-xs-7">
                                <select name="birthDay">
                                    @for($i = 1330; $i <= substr(getToday()['date'], 0, 4); $i++)
                                        @if(isset($birthDay) && $birthDay == $i)
                                            <option selected value="{{$i}}">{{$i}}</option>
                                        @else
                                            <option value="{{$i}}">{{$i}}</option>
                                        @endif
                                    @endfor
                                </select>
                            </div>
                            <div class="col-xs-5">
                                <span>
                                    <span class="help" data-toggle="tooltip" data-placement="top" title="سال تولد خود را وارد کنید.">
                                        <img src="{{URL::asset('images/help.png')}}" alt="birth day">
                                    </span>سال تولد
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-12" id="justForStudent">
                        <div class="col-xs-7">
                            <input type="text" name="invitationCode" value="{{(isset($invitationCode) ? $invitationCode : '')}}" maxlength="40">
                        </div>
                        <div class="col-xs-5">
                            <span>
                                <span  class="help" data-toggle="tooltip" data-placement="top" title="اگر کسی گچ سفید را به شما معرفی کرده، کد وی را در این بخش وارد کنید.">
                                    <img src="{{URL::asset('images/help.png')}}" alt="">
                                </span>کد معرف (اختیاری)
                            </span>
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

                        @if(isset($firstName))
                            <p style="max-width: 600px; text-align: justify;"><span>{{$firstName}}</span><span> عزیز ثبت نام شما با نام کاربری </span><span>{{$username}}</span><span>&nbsp</span><span>و رمز انتخابی انجام شده است. برای تایید شماره موبایل کد دریافت شده را در کادر زیر وارد کنید. اگر کد را دریافت نکرده اید با شماره خود عدد 110 را به شماره 02166591203 پیامک کنید تا حداکثر تا 24 ساعت کاری بعد حساب کاربری شما فعال خواهد شد.</span></p>
                        @endif

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

    <script>

        var first = true;

        function changeField(val) {

            $.ajax({
                type: 'post',
                url: '{{route('getGradesOfField')}}',
                data: {
                    'field': val
                },
                success: function (response) {

                    response = JSON.parse(response);

                    var newElement = "";

                    for(i = 0; i < response.length; i++) {
                        newElement += "<div style='float: right; padding: 5px'><label for='grade_" + response[i].id + "'>" + response[i].name + "</label><input id='grade_" + response[i].id + "' type='checkbox' value='" + response[i].id + "' name='grades[]'></div>";
                    }

                    if(response.length == 0) {
                        $("#gradesDiv").addClass('hidden');
                    }
                    else {
                        $("#gradesDiv").removeClass('hidden');
                    }

                    $("#grades").empty().append(newElement);

                }
            });
            
        }
        
        function getStates() {

            $.ajax({
                type: 'post',
                url: '{{route('getStates')}}',
                success: function(response) {

                    response = JSON.parse(response);
                    var newElement = "";

                    for(i = 0; i < response.length; i++) {
                        newElement += "<option value='" + response[i].id + "'>" + response[i].name + "</option>";
                    }

                    $("#states").empty().append(newElement);

                    if(response.length > 0)
                        getCities(response[0].id);

                }
            });

        }

        function getCities(val) {

            $.ajax({
                type: 'post',
                url: '{{route('getCities')}}',
                data: {
                    'stateId': val
                },
                success: function (response) {

                    response = JSON.parse(response);

                    var newElement = "";

                    for(i = 0; i < response.length; i++) {
                        newElement += "<option value='" + response[i].id +"'>" + response[i].name + "</option>";
                    }

                    $("#cities").empty().append(newElement);

                }
            });

        }

        function changeLevel(val) {
            if(val == 2) {
                if(first) {
                    getStates();
                    changeField($("#field").val());
                    first = false;
                }
                $("#justForAdviser").removeClass('hidden');
                $("#justForStudent").addClass('hidden');
            }
            else {
                $("#justForAdviser").addClass('hidden');
                $("#justForStudent").removeClass('hidden');
            }
        }
    </script>

@stop
