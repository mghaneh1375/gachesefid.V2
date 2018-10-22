@extends('layouts.form')

@section('head')
    @parent
@stop

@section('caption')
    <div class="title" style="width: 500px !important;">
        @if($mode == getValueInfo('operator2Level'))
            اپراتور های نوع 2
        @elseif($mode == getValueInfo('operator1Level'))
            اپراتور های نوع 1
        @elseif($mode == getValueInfo('controllerLevel'))
            ناظران
        @elseif($mode == getValueInfo('adviserLevel'))
            مشاوران
        @elseif($mode == getValueInfo('namayandeLevel'))
            نمایندگان
        @elseif($mode == getValueInfo('schoolLevel'))
            مدارس
        @endif
    </div>
@stop

@section('main')
    <center>

        <style>
            td {
                padding: 6px;
            }
            tr:nth-child(even) {
                background-color: #ccc;
            }
        </style>
        @if($mode == getValueInfo('schoolLevel'))
            <form method="post" action="{{route('removeUser', ['mode' => $mode])}}">
                {{csrf_field()}}
                <div class="col-xs-12" style="margin-top: 20px">
                    <table>
                        <tr>
                            <td><center>نام مدرسه</center></td>
                            <td><center>شهر</center></td>
                            <td><center>استان</center></td>
                            <td><center>نوع مدرسه</center></td>
                            <td><center>مقطع</center></td>
                            <td><center>جنسیت</center></td>
                            <td><center>نمایندگی</center></td>
                            <td><center>نام مسئول مدرسه</center></td>
                            <td><center>نام کاربری مسئول مدرسه</center></td>
                            <td><center>شماره همراه</center></td>
                            <td><center>شماره ثابت</center></td>
                            <td><center>کد مدرسه</center></td>
                            <td><center>حذف</center></td>
                        </tr>
                        @foreach($users as $user)
                            <tr>
                                <td><center>{{$user->schoolName}}</center></td>
                                <td><center>{{$user->schoolCity}}</center></td>
                                <td><center>{{$user->schoolState}}</center></td>
                                <td><center>{{$user->schoolKind}}</center></td>
                                <td><center>{{$user->schoolLevel}}</center></td>
                                <td><center>{{($user->sex == 0) ? 'دخترانه' : 'پسرانه'}}</center></td>
                                <td><center>{{$user->schoolNamayande}}</center></td>
                                <td><center>{{$user->firstName}} {{$user->lastName}}</center></td>
                                <td><center>{{$user->username}}</center></td>
                                <td><center>{{$user->phoneNum}}</center></td>
                                <td><center>{{$user->introducer}}</center></td>
                                <td><center>{{$user->invitationCode}}</center></td>
                                <td>
                                    <center>
                                        <button value="{{$user->id}}" name="uId" class="btn btn-danger">
                                            <span class="glyphicon glyphicon-remove" style="margin-left: 30%"></span>
                                        </button>

                                        <span data-toggle="tooltip" title="ویرایش" onclick="editSchool('{{$user->id}}', '{{$user->firstName}}', '{{$user->lastName}}', '{{$user->sex}}', '{{$user->schoolName}}', '{{$user->schoolKindId}}', '{{$user->cityId}}', '{{$user->phoneNum}}', '{{$user->introducer}}', '{{$user->schoolLevelId}}', '{{$user->username}}')" class="btn btn-primary">
                                            <span class="glyphicon glyphicon-edit" style="margin-left: 30%"></span>
                                        </span>
                                    </center>
                                </td>
                            </tr>
                        @endforeach
                    </table>

                    <a class="btn btn-success" href="{{route('schoolsExcel')}}">دانلود فایل اکسل</a>
                </div>
            </form>
        @else
            <form method="post" action="{{route('removeUser', ['mode' => $mode])}}">
                {{csrf_field()}}
                <div class="col-xs-12" style="margin-top: 20px">
                    <table>
                        <tr>
                            <td><center>نام</center></td>
                            <td><center>نام کاربری</center></td>
                            <td><center>شماره همراه</center></td>
                            <td><center>کد معرف</center></td>
                            @if($mode == getValueInfo('namayandeLevel'))
                                <td><center>شهر</center></td>
                            @endif
                            <td><center>عملیات</center></td>
                        </tr>
                        @foreach($users as $user)

                            <tr>
                                @if($mode == getValueInfo('adviserLevel'))
                                    <td style="cursor: pointer" onclick="document.location.href = '{{route('adviserInfo', ['adviserId' => $user->id])}}'"><center>{{$user->firstName}} {{$user->lastName}}</center></td>
                                @else
                                    <td><center>{{$user->firstName}} {{$user->lastName}}</center></td>
                                @endif

                                <td><center>{{$user->username}}</center></td>
                                <td><center>{{$user->phoneNum}}</center></td>
                                <td><center>{{$user->invitationCode}}</center></td>
                                @if($mode == getValueInfo('namayandeLevel'))
                                    <td><center>{{$user->city}}</center></td>
                                @endif
                                <td>
                                    <center>
                                        <button value="{{$user->id}}" name="uId" class="btn btn-danger">
                                            <span class="glyphicon glyphicon-remove" style="margin-left: 30%"></span>
                                        </button>
                                        @if($user->status == 2)
                                            <button value="{{$user->id}}" name="uId" formaction="{{route('confirmAdviser')}}" class="btn btn-success">تایید مشاور</button>
                                        @else
                                            <span onclick="disableUser('{{$user->id}}')" class="btn btn-warning">غیر فعال کردن کاربر</span>
                                        @endif
                                        <span onclick="editUser('{{$user->id}}', '{{$user->firstName}}', '{{$user->lastName}}', '{{$user->username}}', '{{$user->phoneNum}}')" class="btn btn-primary">
                                            <span class="glyphicon glyphicon-edit" style="margin-left: 30%"></span>
                                        </span>
                                    </center>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </form>
        @endif

        <div class="col-xs-12" style="margin-top: 10px">

            @if($mode == getValueInfo('operator2Level'))
                <a href="{{route('addOperator2')}}">
                    <button class="btn btn-primary" style="border-radius: 50% 50% 50% 50%" data-toggle="tooltip" title="اضافه کردن اپراتور جدید">
                    <span style="margin-left: 30%" class="glyphicon glyphicon-plus"></span>
                    </button>
                </a>
            @elseif($mode == getValueInfo('operator1Level'))
                <a href="{{route('addOperator1')}}">
                    <button class="btn btn-primary" style="border-radius: 50% 50% 50% 50%" data-toggle="tooltip" title="اضافه کردن اپراتور جدید">
                        <span style="margin-left: 30%" class="glyphicon glyphicon-plus"></span>
                    </button>
                </a>
            @elseif($mode == getValueInfo('controllerLevel'))
                <a href="{{route('addControllers')}}">
                    <button class="btn btn-primary" style="border-radius: 50% 50% 50% 50%" data-toggle="tooltip" title="اضافه کردن ناظر جدید">
                        <span style="margin-left: 30%" class="glyphicon glyphicon-plus"></span>
                    </button>
                </a>
            @elseif($mode == getValueInfo('namayandeLevel'))
                <a href="{{route('addNamayande')}}">
                    <button class="btn btn-primary" style="border-radius: 50% 50% 50% 50%" data-toggle="tooltip" title="اضافه کردن نماینده جدید">
                        <span style="margin-left: 30%" class="glyphicon glyphicon-plus"></span>
                    </button>
                </a>

            @elseif($mode == getValueInfo('schoolLevel'))
                <a href="{{route('addSchool')}}">
                    <button class="btn btn-primary" style="border-radius: 50% 50% 50% 50%" data-toggle="tooltip" title="اضافه کردن مدرسه جدید">
                        <span style="margin-left: 30%" class="glyphicon glyphicon-plus"></span>
                    </button>
                </a>
            @endif
        </div>
    </center>

    <span id="editUser" class="ui_overlay item hidden" style="position: fixed; left: 30%; right: 30%; width: auto; top: 70px; bottom: auto">
        <div class="header_text">ویرایش کاربر</div>
        <div onclick="$('#editUser').addClass('hidden')" class="ui_close_x"></div>
        <div class="body_text">
            <div class="col-xs-12">
                <label for="username">نام کاربری</label>
                <input id="username" type="text">
            </div>

            <div class="col-xs-12">
                <label for="firstName">نام</label>
                <input id="firstName" type="text">
            </div>

            <div class="col-xs-12">
                <label for="lastName">نام خانوادگی</label>
                <input id="lastName" type="text">
            </div>

            <div class="col-xs-12">
                <label for="phone">شماره همراه</label>
                <input id="phone" type="tel">
            </div>

            <div class="col-xs-12">
                <label for="pass">رمزعبور جدید</label>
                <input id="pass" type="password">
            </div>

            <div class="col-xs-12">
                <label for="rpass">تکرار رمزعبور جدید</label>
                <input id="rpass" type="password">
            </div>

            <div class="col-xs-12">
                <input type="submit" value="تایید" class="btn btn-primary" onclick="doEditUser()">
                <p class="errorText hidden" id="msg2"></p>
            </div>
        </div>
    </span>

    @if($mode == getValueInfo('schoolLevel'))
        <span id="editSchool" class="ui_overlay item hidden" style="position: fixed; left: 30%; right: 30%; width: auto; top: 70px; bottom: auto">
            <div class="header_text">ویرایش مدرسه</div>
            <div onclick="$('#editSchool').addClass('hidden')" class="ui_close_x"></div>
            <div class="body_text">
                <div class="col-xs-12">
                    <label for="schoolName">نام مدرسه</label>
                    <input id="schoolName" type="text">
                </div>

                <div class="col-xs-12">
                    <label for="kindSchool">نوع مدرسه</label>
                    <select id="kindSchool">
                        <option value="{{getValueInfo('sampadSch')}}">سمپاد</option>
                        <option value="{{getValueInfo('gheyrSch')}}">غیر انتفاعی</option>
                        <option value="{{getValueInfo('nemoneSch')}}">نمونه دولتی</option>
                        <option value="{{getValueInfo('shahedSch')}}">شاهد</option>
                        <option value="{{getValueInfo('HeyatSch')}}">هیات امنایی</option>
                        <option value="{{getValueInfo('dolatiSch')}}">دولتی</option>
                        <option value="{{getValueInfo('sayerSch')}}">سایر</option>
                    </select>
                </div>

                <div class="col-xs-12">
                    <label for="schoolLevel">مقطع مدرسه</label>
                    <select id="schoolLevel">
                        <option selected value="{{getValueInfo('motevaseteAval')}}">متوسطه اول</option>
                        <option value="{{getValueInfo('motevaseteDovom')}}">متوسطه دوم</option>
                        <option value="{{getValueInfo('dabestan')}}">دبستان</option>
                    </select>
                </div>

                <div class="col-xs-12">
                    <label for="sex">جنسیت</label>
                    <select id="sex">
                        <option value="1">پسرانه</option>
                        <option value="0">دخترانه</option>
                    </select>
                </div>

                <div class="col-xs-12">
                    <label for="state">استان</label>
                    <select id="state" class="mySelect" onchange="getCities()">
                        @foreach($states as $state)
                            <option value="{{$state->id}}">{{$state->name}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-xs-12">
                    <label for="city">شهر</label>
                    <select name="city" class="mySelect" id="city"></select>
                </div>

                <div class="col-xs-12">
                    <label for="firstNameSch">نام مسئول مدرسه</label>
                    <input id="firstNameSch" type="text">
                </div>

                <div class="col-xs-12">
                    <label for="lastNameSch">نام خانوادگی مسئول مدرسه</label>
                    <input id="lastNameSch" type="text">
                </div>

                <div class="col-xs-12">
                    <label for="phoneSch">شماره همراه مسئول مدرسه</label>
                    <input id="phoneSch" type="tel">
                </div>

                <div class="col-xs-12">
                    <label for="telPhone">شماره ثابت مسئول مدرسه</label>
                    <input id="telPhone" type="tel">
                </div>

                <div class="col-xs-12">
                    <label for="usernameSch">نام کاربری</label>
                    <input id="usernameSch" type="text">
                </div>

                <div class="col-xs-12">
                    <label for="passSch">رمزعبور جدید</label>
                    <input id="passSch" type="password">
                </div>

                <div class="col-xs-12">
                    <label for="rpassSch">تکرار رمزعبور جدید</label>
                    <input id="rpassSch" type="password">
                </div>

                <div class="col-xs-12">
                    <input type="submit" value="تایید" class="btn btn-primary" onclick="doEditSchool()">
                    <p class="errorText hidden" id="msg"></p>
                </div>

            </div>
        </span>
    @endif

    <script>

        var selectedUId;

        function editUser(id, f, l, u, p) {
            selectedUId = id;

            $("#firstName").val(f);
            $("#lastName").val(l);
            $("#username").val(u);
            $("#phone").val(p);

            $("#editUser").removeClass('hidden');
        }

        function disableUser(uId) {
            
            $.ajax({
                type: 'post',
                url: '{{route('disableUser')}}',
                data: {
                    'uId': uId
                },
                success: function () {
                    location.reload();
                }
            });
        }

        function doEditUser() {

            if($("#pass").val().length == 0 || $("#rpass").val().length == 0) {

                $.ajax({
                    type: 'post',
                    url: '{{route('doEditUser')}}',
                    data: {
                        'uId': selectedUId,
                        'firstName': $("#firstName").val(),
                        'lastName': $("#lastName").val(),
                        'phone': $("#phone").val(),
                        'username': $("#username").val()
                    },
                    success: function (response) {
                        if (response == "ok") {
                            location.reload();
                        }
                        else if (response == "nok1") {
                            $("#msg2").empty().append('نام کاربری وارد شده در سامانه موجود است').removeClass('hidden');
                        }
                        else {
                            $("#msg2").empty().append('مشکلی در انجام عملیات مورد نظر رخ داده است').removeClass('hidden');
                        }
                    }
                });
            }
            else {

                $.ajax({
                    type: 'post',
                    url: '{{route('doEditUser')}}',
                    data: {
                        'uId': selectedUId,
                        'firstName': $("#firstName").val(),
                        'lastName': $("#lastName").val(),
                        'phone': $("#phone").val(),
                        'username': $("#username").val(),
                        'password': $("#pass").val(),
                        'confirm': $("#rpass").val()
                    },
                    success: function (response) {
                        if (response == "ok") {
                            location.reload();
                        }
                        else if (response == "nok1") {
                            $("#msg2").empty().append('نام کاربری وارد شده در سامانه موجود است').removeClass('hidden');
                        }
                        else if (response == "nok2") {
                            $("#msg2").empty().append('رمزعبور وارد شده و تکرار آن یکی نیستند').removeClass('hidden');
                        }
                        else {
                            $("#msg2").empty().append('مشکلی در انجام عملیات مورد نظر رخ داده است').removeClass('hidden');
                        }
                    }
                });
            }
        }

        function editSchool(id, f, l, s, sN, kS, c, p, tP, sL, u) {
            selectedUId = id;
            getStateCity(c, f, l, s, sN, kS, p, tP, sL, u);
        }

        function doEditSchool() {

            if($("#passSch").val().length == 0 || $("#rpassSch").val().length == 0) {

                $.ajax({
                    type: 'post',
                    url: '{{route('editSchool')}}',
                    data: {
                        'uId': selectedUId,
                        'firstName': $("#firstNameSch").val(),
                        'schoolName': $("#schoolName").val(),
                        'phone': $("#phoneSch").val(),
                        'telPhone': $("#telPhone").val(),
                        'kindSchool': $("#kindSchool").val(),
                        'schoolLevel': $("#schoolLevel").val(),
                        'lastName': $("#lastNameSch").val(),
                        'schoolCity': $("#city").val(),
                        'sex': $("#sex").val(),
                        'username': $("#usernameSch").val()
                    },
                    success: function (response) {
                        if(response == "ok")
                            location.reload();
                        else if (response == "nok1") {
                            $("#msg").empty().append('نام کاربری وارد شده در سامانه موجود است').removeClass('hidden');
                        }
                        else {
                            $("#msg").empty().append('مشکلی در انجام عملیات مورد نظر رخ داده است').removeClass('hidden');
                        }
                    }
                });
            }
            else {

                $.ajax({
                    type: 'post',
                    url: '{{route('editSchool')}}',
                    data: {
                        'uId': selectedUId,
                        'firstName': $("#firstNameSch").val(),
                        'schoolName': $("#schoolName").val(),
                        'phone': $("#phoneSch").val(),
                        'telPhone': $("#telPhone").val(),
                        'kindSchool': $("#kindSchool").val(),
                        'schoolLevel': $("#schoolLevel").val(),
                        'lastName': $("#lastNameSch").val(),
                        'schoolCity': $("#city").val(),
                        'sex': $("#sex").val(),
                        'password': $("#passSch").val(),
                        'confirm': $("#rpassSch").val(),
                        'username': $("#usernameSch").val()
                    },
                    success: function (response) {
                        if(response == "ok")
                            location.reload();
                        else if (response == "nok1") {
                            $("#msg").empty().append('نام کاربری وارد شده در سامانه موجود است').removeClass('hidden');
                        }
                        else if (response == "nok2") {
                            $("#msg").empty().append('رمزعبور وارد شده و تکرار آن یکی نیستند').removeClass('hidden');
                        }
                        else {
                            $("#msg").empty().append('مشکلی در انجام عملیات مورد نظر رخ داده است').removeClass('hidden');
                        }
                    }
                });

            }
        }

        function getCities(cityId) {

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

                        for (i = 0; i < response.length; i++) {
                            if(cityId == response[i].id)
                                newElement += "<option selected value='" + response[i].id + "'>" + response[i].name + "</option>";
                            else
                                newElement += "<option value='" + response[i].id + "'>" + response[i].name + "</option>";
                        }
                    }

                    $("#city").empty().append(newElement);
                }
            });
        }

        function getStateCity(cityId, f, l, s, sN, kS, p, tP, sL, u) {

            $.ajax({
                type: 'post',
                url: '{{route('getStateCity')}}',
                data : {
                    'cityId': cityId
                },
                success: function (response) {
                    $("#state").val(response);
                    getCities(cityId);

                    $("#firstNameSch").val(f);
                    $("#lastNameSch").val(l);
                    $("#schoolName").val(sN);
                    $("#schoolLevel").val(sL);
                    $("#kindSchool").val(kS);
                    $("#phoneSch").val(p);
                    $("#telPhone").val(tP);
                    $("#sex").val(s);
                    $("#usernameSch").val(u);

                    $("#editSchool").removeClass('hidden');
                }
            });
        }
    </script>
    
@stop