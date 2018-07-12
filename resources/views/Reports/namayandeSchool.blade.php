@extends('layouts.form')

@section('head')
    @parent
@stop


@section('caption')
    <div class="title">گزارش گیری از مدارس
    </div>
@stop

@section('main')

    <style>
        td {
            padding: 10px;
        }
    </style>

    <center style="margin-top: 10px">

        <table style="padding: 10px">
            <tr>
                <td><center>نام مدرسه</center></td>
                <td><center>نوع مدرسه</center></td>
                <td><center>شهر</center></td>
                <td><center>مقطع</center></td>
                <td><center>جنسیت</center></td>
                <td><center>نام مسئول مدرسه</center></td>
                <td><center>شماره همراه</center></td>
                <td><center>شماره ثابت</center></td>
                <td><center>کد مدرسه</center></td>
                <td><center>تعداد دانش آموزان</center></td>
                <td><center>عملیات</center></td>
            </tr>

            <form method="post" action="{{route('removeSchool')}}">
                {{csrf_field()}}
                @foreach($schools as $user)
                    <tr>
                        <td onclick="document.location.href = '{{route('schoolStudent', ['sId' => $user->id])}}'">
                            <center style="cursor: pointer; padding: 6px; border-radius: 4px; background-color: #337ab7; color: white; ">{{$user->schoolName}}</center>
                        </td>
                        <td><center>{{$user->schoolKind}}</center></td>
                        <td><center>{{$user->schoolCity}}</center></td>
                        <td><center>{{$user->schoolLevel}}</center></td>
                        <td><center>{{($user->sex == 0) ? 'دخترانه' : 'پسرانه'}}</center></td>
                        <td><center>{{$user->firstName}} {{$user->lastName}}</center></td>
                        <td><center>{{$user->phoneNum}}</center></td>
                        <td><center>{{$user->introducer}}</center></td>
                        <td><center>{{$user->invitationCode}}</center></td>
                        <td><center>{{$user->students}}</center></td>
                        <td>
                            <center>
                                <button value="{{$user->id}}" name="uId" class="btn btn-danger" data-toggle="tooltip" title="حذف">
                                    <span class="glyphicon glyphicon-remove" style="margin-left: 30%"></span>
                                </button>
                                <span data-toggle="tooltip" title="ویرایش" onclick="editSchool('{{$user->id}}', '{{$user->firstName}}', '{{$user->lastName}}', '{{$user->sex}}', '{{$user->schoolName}}', '{{$user->schoolKindId}}', '{{$user->cityId}}', '{{$user->phoneNum}}', '{{$user->introducer}}', '{{$user->schoolLevelId}}')" class="btn btn-primary">
                                    <span class="glyphicon glyphicon-edit" style="margin-left: 30%"></span>
                                </span>
                            </center>
                        </td>
                    </tr>
                @endforeach
            </form>

        </table>
    </center>

    <style>
        .body_text > .col-xs-12 {
            margin-top: 10px;
        }

    </style>

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
                <label for="firstName">نام مسئول مدرسه</label>
                <input id="firstName" type="text">
            </div>

            <div class="col-xs-12">
                <label for="lastName">نام خانوادگی مسئول مدرسه</label>
                <input id="lastName" type="text">
            </div>

            <div class="col-xs-12">
                <label for="phone">شماره همراه مسئول مدرسه</label>
                <input id="phone" type="tel">
            </div>

            <div class="col-xs-12">
                <label for="telPhone">شماره ثابت مسئول مدرسه</label>
                <input id="telPhone" type="tel">
            </div>

            <div class="col-xs-12">
                <input type="submit" value="تایید" class="btn btn-primary" onclick="doEditSchool()">
                <p class="errorText hidden" id="msg"></p>
            </div>
        </div>
    </span>


    <script>

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

        function getStateCity(cityId, f, l, s, sN, kS, p, tP, sL) {

            $.ajax({
                type: 'post',
                url: '{{route('getStateCity')}}',
                data : {
                    'cityId': cityId
                },
                success: function (response) {
                    $("#state").val(response);
                    getCities(cityId);

                    $("#firstName").val(f);
                    $("#lastName").val(l);
                    $("#schoolName").val(sN);
                    $("#schoolLevel").val(sL);
                    $("#kindSchool").val(kS);
                    $("#phone").val(p);
                    $("#telPhone").val(tP);
                    $("#sex").val(s);

                    $("#editSchool").removeClass('hidden');
                }
            });
        }

        var selectedUId = -1;

        function editSchool(id, f, l, s, sN, kS, c, p, tP, sL) {
            selectedUId = id;
            getStateCity(c, f, l, s, sN, kS, p, tP, sL);
        }

        function doEditSchool() {

            $.ajax({
                type: 'post',
                url: '{{route('editSchool')}}',
                data: {
                    'uId': selectedUId,
                    'firstName': $("#firstName").val(),
                    'schoolName': $("#schoolName").val(),
                    'phone': $("#phone").val(),
                    'telPhone': $("#telPhone").val(),
                    'kindSchool': $("#kindSchool").val(),
                    'schoolLevel': $("#schoolLevel").val(),
                    'lastName': $("#lastName").val(),
                    'schoolCity': $("#city").val(),
                    'sex': $("#sex").val()
                },
                success: function (response) {
                    if(response == "ok") {
                        document.location.href = '{{route('namayandeSchool')}}';
                    }
                }
            });
        }
    </script>
@stop