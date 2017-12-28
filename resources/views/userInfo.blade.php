@extends('layouts.form')

@section('head')
    @parent

    <link href="{{URL::asset('css/form.css')}}" rel="stylesheet" type="text/css">
    <script src="{{URL::asset('js/jsNeededForUserInfo.js')}}"></script>

    <script>
        var getCities = '{{route('getCities')}}';

        $(document).ready(function () {

            @if($selectedPart == "necessary")
                changeTitle('necessary', 'editInfo1');
            @elseif($selectedPart == "additional1")
                changeTitle('additional1', 'editInfo2');
            @endif

            @if(Auth::user()->level == getValueInfo('studentLevel'))
                changeState($("#states").val(), '{{(isset($redundant1) && !empty($redundant1->cityId)) ? $redundant1->cityId : -1}}');
            @endif
        });

        function changeTitle(val, idx) {

            $(".titleBar").removeClass('focus');
            $("#" + val).addClass('focus');

            $(".content").addClass('hidden');
            $("#" + idx).removeClass('hidden');
        }

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

    @if($msg == "pending" || $msg == "pendingErrTime" || $msg == "pendingErr")
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
                $("#reminder_time").persiaNumber();
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
    <div class="title">ویرایش اطلاعات کاربری
    </div>
@stop

@section('main')

    <style>

        .focus {
            color: #555 !important;
            background-color: #fff !important;
            border: 1px solid #636363 !important;
            border-bottom-color: transparent !important;
            cursor: default !important;
        }

        .titleBar {
            margin-left: auto;
            margin-right: -2px;
            border-radius: 4px 4px 0 0;
            line-height: 1.42857143;
            border: 1px solid transparent;
            position: relative;
            padding: 10px 15px;
            color: #0699d4;
            text-decoration: none !important;
            font-style: normal;
            cursor: pointer;
            font-size: 100%;
            font-family: inherit;
        }

        .titleBarPane {
            border: 2px solid #6c6c70;
            background-color: #ddd;
            border-radius: 6px;
            padding: 10px;
            height: 50px;
            width: 500px;
        }
    </style>

    <center class="myRegister">

        @if(Auth::user()->level == getValueInfo('studentLevel'))

            @if(Auth::user()->phoneNum == "")
                <p class="errorText">لطفا قسمت مربوط به تلفن همراه خود را پر نمایید</p>
            @endif

            <diV class="data row titleBarPane">
                <a id="necessary" onclick="changeTitle('necessary', 'editInfo1')" class="titleBar">اطلاعات اولیه</a>
                <a id="additional1" onclick="changeTitle('additional1', 'editInfo2')" class="titleBar">اطلاعات تکمیلی</a>
                <a id="additional2" onclick="changeTitle('additional2', 'editInfo3')" class="titleBar">اطلاعات ارسال جایزه‌ها</a>

                <div id="containerDiv" style="margin-top: 20px">

                    <form id="editInfo1" class="content hidden" method="post" action="{{route('editInfo')}}">

                        @if(!(isset($msg) && $mode == "editInfo" && ($msg == "pending" || $msg == "pendingErr" || $msg == "pendingErrTime")))

                            <div class="col-xs-12">
                                <div class="col-xs-7">
                                    <input type="text" name="firstName" value="{{$user->firstName}}" maxlength="40" required autofocus>
                                </div>
                                <div class="col-xs-5">
                        <span>
                             نام <span class="required">*</span>
                        </span>
                                </div>
                            </div>

                            <div class="col-xs-12">
                                <div class="col-xs-7">
                                    <input type="text" name="lastName" value="{{$user->lastName}}" maxlength="40" required>
                                </div>
                                <div class="col-xs-5">
                        <span>
                         نام خانوادگی <span class="required">*</span></span>
                                </div>
                            </div>

                            <div class="col-xs-12">
                                <div class="col-xs-7">
                                    <input type="text" name="username" value="{{$user->username}}" maxlength="40" required>
                                </div>
                                <div class="col-xs-5">
                        <span>
                                         نام کاربری <span class="required">*</span></span>
                                </div>
                            </div>

                            <div class="col-xs-12">
                                <div class="col-xs-7">
                                    <input type="text" onkeypress="validate(event)" name="phoneNum" value="{{$user->phoneNum}}" maxlength="40" required>
                                </div>
                                <div class="col-xs-5">
                        <span>
                                        شماره تلفن <span class="required">*</span></span>
                                </div>
                            </div>

                            <div class="col-xs-12">
                                <div class="col-xs-7">
                                    <input type="text" readonly value="{{$user->invitationCode}}" required>
                                </div>
                                <div class="col-xs-5">
                            <span  class="help" data-toggle="tooltip" data-placement="top" title="کد معرفیِ دیگران">
                                <img src="{{URL::asset('images/help.png')}}" alt="FirstName">
                            </span>
                                    <span>کد معرفیِ دیگران</span>
                                </div>
                            </div>

                            <div class="col-xs-12">
                                <div class="col-xs-7">
                                    <input name="namayandeCode" type="text" value="{{$namayande}}">
                                </div>
                                <div class="col-xs-5">

                            <span  class="help" data-toggle="tooltip" data-placement="top" title="کد مدیر مدرسه">
                                <img src="{{URL::asset('images/help.png')}}" alt="FirstName">
                            </span>
                                    <span>کد مدیر مدرسه</span>
                                </div>
                            </div>

                            <div class="col-xs-12" style="margin-bottom: 10px">
                                <center>
                                    <input type="submit" name="editInfo" value="ویرایش">
                                </center>
                            </div>

                        @endif

                        @if(isset($msg) && $mode == "editInfo")
                            <div class="col-xs-12" style="margin-bottom: 10px">
                                <center>
                                    @if($msg != "pending" && $msg != "pendingErr" && $msg != "pendingErrTime")
                                        <div class="errorText">{{$msg}}</div>
                                    @else
                                        <div class="col-xs-12">
                                            <div class="col-xs-7">
                                                <input type="text" id="activationCode" name="activationCode" required autofocus maxlength="10">
                                                <input type="hidden" value="{{$phoneNum}}" name="phoneNum">
                                            </div>
                                            <div class="col-xs-5">
                                        <span>
                                            <span class="help" data-toggle="tooltip" data-placement="top" title="لطفا کد 5 رقمی ای را که برای شما ارسال شده؛ وارد نمایید">
                                                <img src="{{URL::asset('images/help.png')}}" alt="">
                                            </span> کد فعال سازی <span class="required">*</span>
                                        </span>
                                            </div>
                                        </div>

                                        <div class="col-xs-12">
                                            <center>
                                                <input type="submit" name="activeProfile" value="فعال سازی حساب کاربری">
                                            </center>
                                        </div>

                                        @if($msg == "pendingErr")
                                            <div class="col-xs-12" style="margin-top: 10px">
                                                <center>
                                                    <span class="errorText">کد فعال سازی وارد شده نامعتبر است</span>
                                                </center>
                                            </div>
                                        @endif

                                        @if($msg == "pendingErrTime")
                                            <div class="col-xs-12" style="margin-top: 10px">
                                                <center>
                                                    <span class="errorText">تا ارسال مجدد کد فعال سازی باید زمان لازم طی شود</span>
                                                </center>
                                            </div>
                                        @endif

                                        <div class="col-xs-12" id="reminderTimeDiv">
                                            <p style="margin-top: 10px">زمان باقی مانده</p><div style="margin-top: 10px" id="reminder_time"></div>
                                        </div>

                                        <div id="resendDiv" class="col-xs-12"></div>
                                    @endif
                                </center>
                            </div>
                        @endif

                    </form>

                    <form id="editInfo2" class="content hidden" method="post" action="{{route('editRedundantInfo1')}}">
                        <div class="col-xs-12" style="padding: 10px">
                            <div class="col-xs-7">
                                <input style="min-width: 200px" type="text" onkeypress="validate(event)" name="NID" value="{{(isset($redundant1) && !empty($redundant1->NID)) ? $redundant1->NID : ''}}" required>
                            </div>
                            <div class="col-xs-5">
                                <span>کد ملی</span>
                            </div>
                        </div>

                        <div class="col-xs-12">
                            <div class="col-xs-7">
                                <input style="min-width: 200px" type="text" name="fatherName" value="{{(isset($redundant1) && !empty($redundant1->fatherName)) ? $redundant1->fatherName : ''}}" required>
                            </div>
                            <div class="col-xs-5">
                                <span>نام پدر</span>
                            </div>
                        </div>

                        <div class="col-xs-12">
                            <div class="col-xs-7">
                                <select style="min-width: 200px !important" class="mySelect" id="states" onchange="changeState(this.value, -1)">
                                    @foreach($states as $state)
                                        @if($state->id == $stateId)
                                            <option selected value="{{$state->id}}">{{$state->name}}</option>
                                        @else
                                            <option value="{{$state->id}}">{{$state->name}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-xs-5">
                                <span>استان محل زندگی</span>
                            </div>
                        </div>

                        <div class="col-xs-12">
                            <div class="col-xs-7">
                                <select style="min-width: 200px !important;" class="mySelect" name="cityId" id="cities"></select>
                            </div>
                            <div class="col-xs-5">
                                <span>شهر محل زندگی</span>
                            </div>
                        </div>

                        <div class="col-xs-12">
                            <div class="col-xs-7">
                                <input style="min-width: 200px" type="text" name="schoolName" value="{{(isset($redundant1) && !empty($redundant1->schoolName)) ? $redundant1->schoolName : ''}}" required>
                            </div>
                            <div class="col-xs-5">
                                <span>نام مدرسه</span>
                            </div>
                        </div>

                        <div class="col-xs-12">
                            <div class="col-xs-7">
                                <select style="min-width: 200px !important;" class="mySelect" name="gradeId">
                                    @foreach($grades as $grade)
                                        @if(isset($redundant1) && !empty($redundant1->gradeId) && $redundant1->gradeId == $grade->id)
                                            <option selected value="{{$grade->id}}">{{$grade->name}}</option>
                                        @else
                                            <option value="{{$grade->id}}">{{$grade->name}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-xs-5">
                                <span>پایه ی تحصیلی</span>
                            </div>
                        </div>

                        <div class="col-xs-12">
                            <div class="col-xs-7">
                                <input style="min-width: 200px" type="email" name="email" value="{{(isset($redundant1) && !empty($redundant1->email)) ? $redundant1->email : ''}}" required>
                            </div>
                            <div class="col-xs-5">
                                <span>ایمیل</span>
                            </div>
                        </div>

                        <div class="col-xs-12" style="margin-bottom: 10px">
                            <center>
                                <input type="submit" name="editRedundantInfo1" value="ویرایش">
                            </center>
                        </div>

                        @if(isset($msg) && $mode == "editRedundant1")
                            <div class="col-xs-12" style="margin-bottom: 10px">
                                <center>
                                    <div class="errorText">{{$msg}}</div>
                                </center>
                            </div>
                        @endif

                    </form>

                    <form id="editInfo3" class="content hidden" method="post" action="{{route('editRedundantInfo2')}}">
                        <div class="col-xs-12" style="padding: 10px">
                            <div class="col-xs-7">
                                <textarea name="address" style="float: right; width: 300px; height: 300px" maxlength="1000" placeholder="حداکثر 1000 کاراکتر" required>{{(isset($redundant2) && !empty($redundant2->address)) ? $redundant2->address : ''}}</textarea>
                            </div>
                            <div class="col-xs-5">
                                <span>نشانی</span>
                            </div>
                        </div>

                        <div class="col-xs-12">
                            <div class="col-xs-7">
                                <input style="min-width: 200px" type="text" onkeypress="validate(event)" name="homePhone" value="{{(isset($redundant2) && !empty($redundant2->homePhone)) ? $redundant2->homePhone : ''}}" required>
                            </div>
                            <div class="col-xs-5">
                                <span>تلفن منزل</span>
                            </div>
                        </div>

                        <div class="col-xs-12">
                            <div class="col-xs-7">
                                <input style="min-width: 200px" type="text" onkeypress="validate(event)" name="fatherPhone" value="{{(isset($redundant2) && !empty($redundant2->fatherPhone)) ? $redundant2->fatherPhone : ''}}" required>
                            </div>
                            <div class="col-xs-5">
                                <span>موبایل پدر</span>
                            </div>
                        </div>

                        <div class="col-xs-12">
                            <div class="col-xs-7">
                                <input style="min-width: 200px" type="text" onkeypress="validate(event)" name="motherPhone" value="{{(isset($redundant2) && !empty($redundant2->motherPhone)) ? $redundant2->motherPhone : ''}}" required>
                            </div>
                            <div class="col-xs-5">
                                <span>موبایل مادر</span>
                            </div>
                        </div>

                        <div class="col-xs-12">
                            <div class="col-xs-7">
                                <input style="min-width: 200px" type="text" onkeypress="validate(event)" name="homePostCode" value="{{(isset($redundant2) && !empty($redundant2->homePostCode)) ? $redundant2->homePostCode : ''}}" required>
                            </div>
                            <div class="col-xs-5">
                                <span>کد پستی منزل</span>
                            </div>
                        </div>

                        <div class="col-xs-12">
                            <div class="col-xs-7">
                                <select class="mySelect" name="kindSchool" style="min-width: 200px !important;">
                                    @if(isset($redundant2) && !empty($redundant2->kindSchool)
                                        && $redundant2->kindSchool == getValueInfo('sampadSch'))
                                        <option selected value="{{getValueInfo('sampadSch')}}">سمپاد</option>
                                    @else
                                        <option value="{{getValueInfo('sampadSch')}}">سمپاد</option>
                                    @endif

                                    @if(isset($redundant2) && !empty($redundant2->kindSchool)
                                        && $redundant2->kindSchool == getValueInfo('gheyrSch'))
                                        <option selected value="{{getValueInfo('gheyrSch')}}">غیر انتفاعی</option>
                                    @else
                                        <option value="{{getValueInfo('gheyrSch')}}">غیر انتفاعی</option>
                                    @endif

                                    @if(isset($redundant2) && !empty($redundant2->kindSchool)
                                        && $redundant2->kindSchool == getValueInfo('nemoneSch'))
                                        <option selected value="{{getValueInfo('nemoneSch')}}">نمونه دولتی</option>
                                    @else
                                        <option value="{{getValueInfo('nemoneSch')}}">نمونه دولتی</option>
                                    @endif

                                    @if(isset($redundant2) && !empty($redundant2->kindSchool)
                                        && $redundant2->kindSchool == getValueInfo('shahedSch'))
                                        <option selected value="{{getValueInfo('shahedSch')}}">شاهد</option>
                                    @else
                                        <option value="{{getValueInfo('shahedSch')}}">شاهد</option>
                                    @endif

                                    @if(isset($redundant2) && !empty($redundant2->kindSchool)
                                        && $redundant2->kindSchool == getValueInfo('HeyatSch'))
                                        <option selected value="{{getValueInfo('HeyatSch')}}">هیات امنایی</option>
                                    @else
                                        <option value="{{getValueInfo('HeyatSch')}}">هیات امنایی</option>
                                    @endif

                                    @if(isset($redundant2) && !empty($redundant2->kindSchool)
                                        && $redundant2->kindSchool == getValueInfo('dolatiSch'))
                                        <option selected value="{{getValueInfo('dolatiSch')}}">دولتی</option>
                                    @else
                                        <option value="{{getValueInfo('dolatiSch')}}">دولتی</option>
                                    @endif
                                    @if(isset($redundant2) && !empty($redundant2->kindSchool)
                                        && $redundant2->kindSchool == getValueInfo('sayerSch'))
                                        <option selected value="{{getValueInfo('sayerSch')}}">سایر</option>
                                    @else
                                        <option value="{{getValueInfo('sayerSch')}}">سایر</option>
                                    @endif
                                </select>
                            </div>
                            <div class="col-xs-5">
                                <span>نوع مدرسه</span>
                            </div>
                        </div>

                        <div class="col-xs-12" style="margin-bottom: 10px">
                            <center>
                                <input type="submit" name="editRedundantInfo2" value="ویرایش">
                            </center>
                        </div>

                        @if(isset($msg) && $mode == "editRedundant2")
                            <div class="col-xs-12" style="margin-bottom: 10px">
                                <center>
                                    <div class="errorText">{{$msg}}</div>
                                </center>
                            </div>
                        @endif

                    </form>
                </div>

            </diV>

            @else

                <div class="data row">
                    <div id="containerDiv" style="margin-top: 20px">

                        <form id="editInfo1" class="content" method="post" action="{{route('editInfo')}}">

                            @if(!(isset($msg) && $mode == "editInfo" && ($msg == "pending" || $msg == "pendingErr" || $msg == "pendingErrTime")))

                                <div class="col-xs-12">
                                    <div class="col-xs-7">
                                        <input type="text" name="firstName" value="{{$user->firstName}}" maxlength="40" required autofocus>
                                    </div>
                                    <div class="col-xs-5">
                        <span>
                             نام <span class="required">*</span>
                        </span>
                                    </div>
                                </div>

                                <div class="col-xs-12">
                                    <div class="col-xs-7">
                                        <input type="text" name="lastName" value="{{$user->lastName}}" maxlength="40" required>
                                    </div>
                                    <div class="col-xs-5">
                        <span>
                         نام خانوادگی <span class="required">*</span></span>
                                    </div>
                                </div>

                                <div class="col-xs-12">
                                    <div class="col-xs-7">
                                        <input type="text" name="username" value="{{$user->username}}" maxlength="40" required>
                                    </div>
                                    <div class="col-xs-5">
                        <span>
                                         نام کاربری <span class="required">*</span></span>
                                    </div>
                                </div>

                                <div class="col-xs-12">
                                    <div class="col-xs-7">
                                        <input type="text" onkeypress="validate(event)" name="phoneNum" value="{{$user->phoneNum}}" maxlength="40" required>
                                    </div>
                                    <div class="col-xs-5">
                        <span>
                                        شماره تلفن <span class="required">*</span></span>
                                    </div>
                                </div>

                                <div class="col-xs-12">
                                    <div class="col-xs-7">
                                        <input type="text" readonly value="{{$user->invitationCode}}" required>
                                    </div>
                                    <div class="col-xs-5">
                            <span  class="help" data-toggle="tooltip" data-placement="top" title="کد معرفیِ دیگران">
                                <img src="{{URL::asset('images/help.png')}}" alt="FirstName">
                            </span>
                                        <span>کد مدرسه</span>
                                    </div>
                                </div>

                                <div class="col-xs-12" style="margin-bottom: 10px">
                                    <center>
                                        <input type="submit" name="editInfo" value="ویرایش">
                                    </center>
                                </div>

                            @endif

                            @if(isset($msg) && $mode == "editInfo")
                                <div class="col-xs-12" style="margin-bottom: 10px">
                                    <center>
                                        @if($msg != "pending" && $msg != "pendingErr" && $msg != "pendingErrTime")
                                            <div class="errorText">{{$msg}}</div>
                                        @else
                                            <div class="col-xs-12">
                                                <div class="col-xs-7">
                                                    <input type="text" id="activationCode" name="activationCode" required autofocus maxlength="10">
                                                    <input type="hidden" value="{{$phoneNum}}" name="phoneNum">
                                                </div>
                                                <div class="col-xs-5">
                                        <span>
                                            <span class="help" data-toggle="tooltip" data-placement="top" title="لطفا کد 5 رقمی ای را که برای شما ارسال شده؛ وارد نمایید">
                                                <img src="{{URL::asset('images/help.png')}}" alt="">
                                            </span> کد فعال سازی <span class="required">*</span>
                                        </span>
                                                </div>
                                            </div>

                                            <div class="col-xs-12">
                                                <center>
                                                    <input type="submit" name="activeProfile" value="فعال سازی حساب کاربری">
                                                </center>
                                            </div>

                                            @if($msg == "pendingErr")
                                                <div class="col-xs-12" style="margin-top: 10px">
                                                    <center>
                                                        <span class="errorText">کد فعال سازی وارد شده نامعتبر است</span>
                                                    </center>
                                                </div>
                                            @endif

                                            @if($msg == "pendingErrTime")
                                                <div class="col-xs-12" style="margin-top: 10px">
                                                    <center>
                                                        <span class="errorText">تا ارسال مجدد کد فعال سازی باید زمان لازم طی شود</span>
                                                    </center>
                                                </div>
                                            @endif

                                            <div class="col-xs-12" id="reminderTimeDiv">
                                                <p style="margin-top: 10px">زمان باقی مانده</p><div style="margin-top: 10px" id="reminder_time"></div>
                                            </div>

                                            <div id="resendDiv" class="col-xs-12"></div>
                                        @endif
                                    </center>
                                </div>
                            @endif

                        </form>
                    </div>
                </div>
        @endif

    </center>
@stop