@extends('layouts.form')

@section('head')
    @parent

    <link href="{{URL::asset('css/form.css')}}" rel="stylesheet" type="text/css">

    @if($url == route('addNamayande') || $url == route('addSchool'))
        <script>
            $(document).ready(function () {
                getCities();
            });
            function getCities() {

                if($("#state").val() == -1) {
                    return;
                }

                $.ajax({
                    type: 'post',
                    url: '{{route('getCities')}}',
                    data : {
                        'stateId': $("#state").val()
                    },
                    success: function (response) {

                        newElement = "";

                        if(response.length > 0) {
                            response = JSON.parse(response);

                            for (i = 0; i < response.length; i++)
                                newElement += "<option value='" + response[i].id + "'>" + response[i].name + "</option>";
                        }

                        $("#city").empty().append(newElement);
                    }
                });
            }
        </script>
    @endif
    
@stop

@section('caption')
    <div class="title">ثبت نام
    </div>
@stop

@section('main')

    <form method="post" action="{{$url}}">
        
        @if($url == route('addSchool'))
            <center class="myRegister">
                <div class="row data">

                    <div class="col-xs-12">
                        <div class="col-xs-7">
                            <input type="text" name="schoolName" value="{{(isset($schoolName) ? $schoolName : '')}}" maxlength="100" required>
                        </div>
                        <div class="col-xs-5">
                            <span>
                                <span  class="help" data-toggle="tooltip" data-placement="top" title="لطفا نام مدرسه را کامل وارد کنید">
                                    <img src="{{URL::asset('images/help.png')}}" alt="SchoolName">
                                </span> نام مدرسه <span class="required">*</span>
                            </span>
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <div class="col-xs-7">
                            <select class="mySelect" name="kindSchool" style="min-width: 200px !important;">
                                @if(isset($kindSchool) && !empty($kindSchool)
                                    && $kindSchool == getValueInfo('sampadSch'))
                                    <option selected value="{{getValueInfo('sampadSch')}}">سمپاد</option>
                                @else
                                    <option value="{{getValueInfo('sampadSch')}}">سمپاد</option>
                                @endif

                                @if(isset($kindSchool) && !empty($kindSchool)
                                    && $kindSchool == getValueInfo('gheyrSch'))
                                    <option selected value="{{getValueInfo('gheyrSch')}}">غیر انتفاعی</option>
                                @else
                                    <option value="{{getValueInfo('gheyrSch')}}">غیر انتفاعی</option>
                                @endif

                                @if(isset($kindSchool) && !empty($kindSchool)
                                    && $kindSchool == getValueInfo('nemoneSch'))
                                    <option selected value="{{getValueInfo('nemoneSch')}}">نمونه دولتی</option>
                                @else
                                    <option value="{{getValueInfo('nemoneSch')}}">نمونه دولتی</option>
                                @endif

                                @if(isset($kindSchool) && !empty($kindSchool)
                                    && $kindSchool == getValueInfo('shahedSch'))
                                    <option selected value="{{getValueInfo('shahedSch')}}">شاهد</option>
                                @else
                                    <option value="{{getValueInfo('shahedSch')}}">شاهد</option>
                                @endif

                                @if(isset($kindSchool) && !empty($kindSchool)
                                    && $kindSchool == getValueInfo('HeyatSch'))
                                    <option selected value="{{getValueInfo('HeyatSch')}}">هیات امنایی</option>
                                @else
                                    <option value="{{getValueInfo('HeyatSch')}}">هیات امنایی</option>
                                @endif

                                @if(isset($kindSchool) && !empty($kindSchool)
                                    && $kindSchool == getValueInfo('dolatiSch'))
                                    <option selected value="{{getValueInfo('dolatiSch')}}">دولتی</option>
                                @else
                                    <option value="{{getValueInfo('dolatiSch')}}">دولتی</option>
                                @endif

                                @if(isset($kindSchool) && !empty($kindSchool)
                                    && $kindSchool == getValueInfo('sayerSch'))
                                    <option selected value="{{getValueInfo('sayerSch')}}">سایر</option>
                                @else
                                    <option value="{{getValueInfo('sayerSch')}}">سایر</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-xs-5">
                            <span>
                                <span  class="help" data-toggle="tooltip" data-placement="top" title="لطفا نوع مدرسه وارد کنید">
                                    <img src="{{URL::asset('images/help.png')}}" alt="SchoolName">
                                </span> نوع مدرسه <span class="required">*</span>
                            </span>
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <div class="col-xs-7">
                            <select class="mySelect" name="schoolLevel" style="min-width: 200px !important;">
                                @if(isset($schoolLevel) && !empty($schoolLevel)
                                    && $schoolLevel == getValueInfo('motevaseteAval'))
                                    <option selected value="{{getValueInfo('motevaseteAval')}}">متوسطه اول</option>
                                    <option value="{{getValueInfo('motevaseteDovom')}}">متوسطه دوم</option>
                                    <option value="{{getValueInfo('dabestan')}}">دبستان</option>
                                @elseif(isset($schoolLevel) && !empty($schoolLevel)
                                    && $schoolLevel == getValueInfo('motevaseteDovom'))
                                    <option value="{{getValueInfo('motevaseteAval')}}">متوسطه اول</option>
                                    <option selected value="{{getValueInfo('motevaseteDovom')}}">متوسطه دوم</option>
                                    <option value="{{getValueInfo('dabestan')}}">دبستان</option>
                                @else
                                    <option value="{{getValueInfo('motevaseteAval')}}">متوسطه اول</option>
                                    <option value="{{getValueInfo('motevaseteDovom')}}">متوسطه دوم</option>
                                    <option selected value="{{getValueInfo('dabestan')}}">دبستان</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-xs-5">
                            <span>
                                <span  class="help" data-toggle="tooltip" data-placement="top" title="لطفا مقطع مدرسه را مشخص کنید">
                                    <img src="{{URL::asset('images/help.png')}}" alt="FirstName">
                                </span> مقطع مدرسه <span class="required">*</span>
                            </span>
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <div class="col-xs-7">
                            <input type="text" name="firstName" value="{{(isset($firstName) ? $firstName : '')}}" maxlength="40" required>
                        </div>
                        <div class="col-xs-5">
                            <span>
                                <span  class="help" data-toggle="tooltip" data-placement="top" title="لطفا نام مسئول مدرسه را وارد کنید">
                                    <img src="{{URL::asset('images/help.png')}}" alt="FirstName">
                                </span> نام مسئول مدرسه <span class="required">*</span>
                            </span>
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <div class="col-xs-7">
                            <input type="text" name="lastName" value="{{(isset($lastName) ? $lastName : '')}}" maxlength="40" required>
                        </div>
                        <div class="col-xs-5">
                            <span>
                                <span  class="help" data-toggle="tooltip" data-placement="top" title="لطفا نام خانوادگی مسئول مدرسه را وارد کنید">
                                    <img src="{{URL::asset('images/help.png')}}" alt="LastName">
                                </span> نام خانوادگی مسئول مدرسه <span class="required">*</span>
                            </span>
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <div class="col-xs-7">
                            <input type="text" name="username" value="{{(isset($username) ? $username : '')}}" maxlength="40" required>
                        </div>
                        <div class="col-xs-5">
                    <span><span  class="help" data-toggle="tooltip" data-placement="top" title="لطفا یک نام کاربری غیر تکراری وارد نمایید"><img
                                    src="{{URL::asset('images/help.png')}}" alt="UserName"></span> نام کاربری <span class="required">*</span></span>
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <div class="col-xs-7">
                            <input type="password" name="password" value="" maxlength="40" required>
                        </div>
                        <div class="col-xs-5">
                    <span><span  class="help" data-toggle="tooltip" data-placement="top" title="لطفا رمزعبور مناسبی انتخاب کنید"><img
                                    src="{{URL::asset('images/help.png')}}" alt="PassWord"></span> رمز عبور <span class="required">*</span></span>
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <div class="col-xs-7">
                            <input type="text" name="phoneNum" value="{{(isset($phoneNum) ? $phoneNum : '')}}" maxlength="40" required>
                        </div>
                        <div class="col-xs-5">
                    <span><span  class="help" data-toggle="tooltip" data-placement="top" title="لطفا شماره موبایل مسئول مدرسه را به طور کامل وارد نمایید"><img
                                    src="{{URL::asset('images/help.png')}}" alt="PhoneNumber"></span> شماره موبایل <span class="required">*</span></span>
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <div class="col-xs-7">
                            <input type="text" name="schoolPhone" value="{{(isset($schoolPhone) ? $schoolPhone : '')}}" maxlength="40" required>
                        </div>
                        <div class="col-xs-5">
                    <span><span  class="help" data-toggle="tooltip" data-placement="top" title="لطفا شماره ثابت مدرسه را به طور کامل وارد نمایید"><img
                                    src="{{URL::asset('images/help.png')}}" alt="PhoneNumber"></span> شماره تلفن ثابت<span class="required">*</span></span>
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <div class="col-xs-7">
                            <select name="sex" class="mySelect">
                                <option value="none">انتخاب کنید</option>
                                @if(isset($sex) && $sex == 1)
                                    <option value="1" selected>پسرانه</option>
                                @else
                                    <option value="1">پسرانه</option>
                                @endif

                                @if(isset($sex) && !$sex)
                                    <option  value="0" selected>دخترانه</option>
                                @else
                                    <option value="0">دخترانه</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-xs-5">
                    <span><span  class="help" data-toggle="tooltip" data-placement="top" title="نوع مدرسه را مشخص کنید؟!"><img
                                    src="{{URL::asset('images/help.png')}}" alt="Gender?!"></span> جنسیت <span class="required">*</span></span>
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <div class="col-xs-7">
                            <select id="state" class="mySelect" onchange="getCities()">
                                @foreach($states as $state)
                                    <option value="{{$state->id}}">{{$state->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-xs-5">
                    <span><span  class="help" data-toggle="tooltip" data-placement="top" title="استان"><img
                                    src="{{URL::asset('images/help.png')}}" alt="State"></span> استان <span class="required">*</span></span>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <div class="col-xs-7">
                            <select name="city" class="mySelect" id="city"></select>
                        </div>
                        <div class="col-xs-5">
                    <span><span  class="help" data-toggle="tooltip" data-placement="top" title="شهر"><img
                                    src="{{URL::asset('images/help.png')}}" alt="City"></span> شهر <span class="required">*</span></span>
                        </div>
                    </div>



                    @if(Auth::user()->level != getValueInfo('namayandeLevel'))
                        <div class="col-xs-12">
                            <div class="col-xs-7">
                                <input type="text" name="namayandeCode" value="{{(isset($namayandeCode) ? $namayandeCode : '')}}" maxlength="6" required>
                            </div>
                            <div class="col-xs-5">
                            <span>
                                <span  class="help" data-toggle="tooltip" data-placement="top" title="لطفا کد معرف نمایندگی مربوطه را وارد کنید">
                                    <img src="{{URL::asset('images/help.png')}}" alt="FirstName">
                                </span> کد نمایندگی <span class="required">*</span>
                            </span>
                            </div>
                        </div>
                    @endif

                    <div class="col-xs-12">
                        <center>
                            <input type="submit" name="doAdd" value="ارسال">
                        </center>
                    </div>

                    @if(isset($msg))
                        <div class="col-xs-12">
                            <center>
                                <div class="errorText">{{$msg}}</div>
                            </center>
                        </div>
                    @endif
                </div>
            </center>
        @else
            <center class="myRegister">
                <div class="row data">

                    <div class="col-xs-12">
                        <div class="col-xs-7">
                            <input type="text" name="firstName" value="{{(isset($firstName) ? $firstName : '')}}" maxlength="40" required>
                        </div>
                        <div class="col-xs-5">
                        <span>
                            <span  class="help" data-toggle="tooltip" data-placement="top" title="لطفا اسم خود را کامل وارد کنید">
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
                    <span><span  class="help" data-toggle="tooltip" data-placement="top" title="لطفا فامیلی خود را کامل وارد کنید"><img
                                    src="{{URL::asset('images/help.png')}}" alt="LastName"></span> نام خانوادگی <span class="required">*</span></span>
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <div class="col-xs-7">
                            <input type="text" name="username" value="{{(isset($username) ? $username : '')}}" maxlength="40" required>
                        </div>
                        <div class="col-xs-5">
                    <span><span  class="help" data-toggle="tooltip" data-placement="top" title="لطفا یک نام کاربری غیر تکراری وارد نمایید"><img
                                    src="{{URL::asset('images/help.png')}}" alt="UserName"></span> نام کاربری <span class="required">*</span></span>
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <div class="col-xs-7">
                            <input type="password" name="password" value="" maxlength="40" required>
                        </div>
                        <div class="col-xs-5">
                    <span><span  class="help" data-toggle="tooltip" data-placement="top" title="لطفا رمزعبور مناسبی انتخاب کنید"><img
                                    src="{{URL::asset('images/help.png')}}" alt="PassWord"></span> رمز عبور <span class="required">*</span></span>
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <div class="col-xs-7">
                            <input type="phone" name="phoneNum" value="{{(isset($phoneNum) ? $phoneNum : '')}}" maxlength="40" required>
                        </div>
                        <div class="col-xs-5">
                    <span><span  class="help" data-toggle="tooltip" data-placement="top" title="لطفا شماره همراه خود را به طور کامل وارد نمایید"><img
                                    src="{{URL::asset('images/help.png')}}" alt="PhoneNumber"></span> شماره تلفن <span class="required">*</span></span>
                        </div>
                    </div>

                    @if($url != route('addNamayande'))
                        <div class="col-xs-12">
                            <div class="col-xs-7">
                                <select name="sex" class="mySelect">
                                    <option value="none">انتخاب کنید</option>
                                    @if(isset($sex) && $sex == 1)
                                        <option value="1" selected>پسر</option>
                                    @else
                                        <option value="1">پسر</option>
                                    @endif

                                    @if(isset($sex) && !$sex)
                                        <option  value="0" selected>دختر</option>
                                    @else
                                        <option value="0">دختر</option>
                                    @endif
                                </select>
                            </div>
                            <div class="col-xs-5">
                        <span><span  class="help" data-toggle="tooltip" data-placement="top" title="لطفا برای ما تعیین کنید که دختر خانوم هستید یا آقا پسر؟!"><img
                                        src="{{URL::asset('images/help.png')}}" alt="Gender?!"></span> جنسیت <span class="required">*</span></span>
                            </div>
                        </div>
                    @endif

                    @if($url == route('addNamayande'))
                        <div class="col-xs-12">
                            <div class="col-xs-7">
                                <select id="state" class="mySelect" onchange="getCities()">
                                    @foreach($states as $state)
                                        <option value="{{$state->id}}">{{$state->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-xs-5">
                    <span><span  class="help" data-toggle="tooltip" data-placement="top" title="استان"><img
                                    src="{{URL::asset('images/help.png')}}" alt="State"></span> استان <span class="required">*</span></span>
                            </div>
                        </div>
                        <div class="col-xs-12">
                            <div class="col-xs-7">
                                <select name="city" class="mySelect" id="city"></select>
                            </div>
                            <div class="col-xs-5">
                    <span><span  class="help" data-toggle="tooltip" data-placement="top" title="شهر"><img
                                    src="{{URL::asset('images/help.png')}}" alt="City"></span> شهر <span class="required">*</span></span>
                            </div>
                        </div>
                    @endif

                    <div class="col-xs-12">
                        <center>
                            <input type="submit" name="doAdd" value="ارسال">
                        </center>
                    </div>

                    @if(isset($msg))
                        <div class="col-xs-12">
                            <center>
                                <div class="errorText">{{$msg}}</div>
                            </center>
                        </div>
                    @endif
                </div>
            </center>
        @endif

    </form>

@stop
