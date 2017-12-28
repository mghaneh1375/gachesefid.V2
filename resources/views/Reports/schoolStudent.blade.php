@extends('layouts.form2')

@section('head')
    @parent
@stop


@section('caption')
    <div class="title">لیست دانش آموزان
    </div>
@stop

@section('main')

    <style>
        td {
            padding: 10px;
        }
    </style>

    <center style="margin-top: 10px">

        <?php $level = (Auth::user()->level == getValueInfo('namayandeLevel')) ?>

        @if($level)
            <a href="{{route('namayandeSchool')}}"><button class="btn btn-primary">بازگشت به مرحله ی قبل</button></a>
        @endif
        
        <table style="padding: 10px">
            <tr>
                <td><center>نام</center></td>
                <td><center>نام خانوادگی</center></td>
                <td><center>نام کاربری</center></td>
                <td><center>شماره تماس</center></td>
                <td><center>جنسیت</center></td>
                <td><center>کد معرفی</center></td>
            </tr>

            @foreach($students as $itr)
                <tr>
                    <td><center>{{$itr->firstName}}</center></td>
                    <td><center>{{$itr->lastName}}</center></td>
                    <td><center>{{$itr->username}}</center></td>
                    <td><center>{{$itr->phoneNum}}</center></td>
                    <td><center>{{($itr->sex == 1) ? 'پسر' : 'دختر'}}</center></td>
                    <td><center>{{$itr->invitationCode}}</center></td>
                    @if($level)
                        <td><center><button onclick="changeSchoolCode('{{$itr->id}}')" class="btn btn-success">تغییر کد مدرسه</button></center></td>
                    @endif

                    <td><center><button data-toggle="tooltip" title="ویرایش" onclick="editStdOfSchool('{{$itr->id}}', '{{$itr->firstName}}', '{{$itr->lastName}}', '{{$itr->sex}}')" class="btn btn-primary"><span class="glyphicon glyphicon-edit"></span></button></center></td>
                    <td><center><button data-toggle="tooltip" title="حذف" onclick="deleteStdFromSchool('{{$itr->id}}')" class="btn btn-danger"><span class="glyphicon glyphicon-remove"></span></button></center></td>
                </tr>
            @endforeach

        </table>
    </center>

    <span id="changeSchoolCode" class="ui_overlay item hidden" style="position: fixed; left: 30%; right: 30%; width: auto; top: 70px; bottom: auto">
        <div class="header_text">تغییر کد مدرسه</div>
        <div onclick="$('#changeSchoolCode').addClass('hidden')" class="ui_close_x"></div>
        <div class="body_text">
            <div class="col-xs-12">
                <label for="newCode">کد جدید</label>
                <input id="newCode" type="text" max="10">
            </div>
            <div class="col-xs-12">
                <input type="submit" value="تایید" class="btn btn-primary" onclick="doChangeSchoolCode()">
                <p class="errorText hidden" id="msg"></p>
            </div>
        </div>
    </span>

    <span id="editStudent" class="ui_overlay item hidden" style="position: fixed; left: 30%; right: 30%; width: auto; top: 70px; bottom: auto">
        <div class="header_text">ویرایش دانش آموز</div>
        <div onclick="$('#editStudent').addClass('hidden')" class="ui_close_x"></div>
        <div class="body_text">
            <div class="col-xs-12">
                <label for="firstName">نام</label>
                <input id="firstName" type="text">
            </div>
            <div class="col-xs-12">
                <label for="lastName">نام خانوادگی</label>
                <input id="lastName" type="text">
            </div>
            <div class="col-xs-12">
                <label for="sex">جنسیت</label>
                <select id="sex">
                    <option value="1">پسر</option>
                    <option value="0">دختر</option>
                </select>
            </div>
            <div class="col-xs-12">
                <input type="submit" value="تایید" class="btn btn-primary" onclick="doEditStudent()">
                <p class="errorText hidden" id="msg"></p>
            </div>
        </div>
    </span>


    <span id="confirmation" class="ui_overlay item hidden" style="position: fixed; left: 30%; right: 30%; width: auto; top: 30vh; bottom: auto">
        <div class="header_text">آیا از حذف دانش آموز مورد نظر اطمینان دارید؟</div>
        <div onclick="$('#confirmation').addClass('hidden'); $('.dark').addClass('hidden');" class="ui_close_x"></div>
        <div class="body_text">
            <div class="col-xs-12">
                <input type="submit" value="بله" class="btn btn-primary" onclick="doDeleteStdFromSchool()">
                <input type="submit" value="خیر" class="btn btn-primary" onclick="$('#confirmation').addClass('hidden'); $('.dark').addClass('hidden');">
            </div>
        </div>
    </span>

    <script>

        var selectedUId;

        function changeSchoolCode(uId) {

            selectedUId = uId;
            $("#changeSchoolCode").removeClass('hidden');

        }

        function editStdOfSchool(id, f, l, s) {

            selectedUId = id;

            $("#firstName").val(f);
            $("#lastName").val(l);
            $("#sex").val(s);

            $("#editStudent").removeClass('hidden');

        }

        function doEditStudent() {

            $.ajax({
                type: 'post',
                url: '{{route('editStudent')}}',
                data: {
                    'uId': selectedUId,
                    'firstName': $("#firstName").val(),
                    'lastName': $("#lastName").val(),
                    'sex': $("#sex").val()
                },
                success: function (response) {
                    if(response == "ok") {
                        document.location.href = '{{$backURL}}';
                    }
                }
            });
        }

        function doChangeSchoolCode() {

            val = $("#newCode").val();

            if(val == "")
                return;

            $.ajax({
                type: 'post',
                url: '{{route('changeSchoolCode')}}',
                data: {
                    'uId': selectedUId,
                    'newCode': val
                },
                success: function (response) {
                    if(response == "ok") {
                        document.location.href = '{{$backURL}}';
                    }
                    else {
                        $("#msg").empty().append('کد وارد شده نامعتبر است').removeClass('hidden');
                    }
                }
            });
        }

        function doDeleteStdFromSchool() {

            $.ajax({
                type: 'post',
                url: '{{route('deleteStdFromSchool')}}',
                data: {
                    'uId': selectedUId
                },
                success: function (response) {
                    if(response == "ok") {
                        document.location.href = '{{$backURL}}';
                    }
                }
            });
        }

        function deleteStdFromSchool(uId) {
            selectedUId = uId;
            $('.item').addClass('hidden');
            $(".dark").removeClass('hidden');
            $("#confirmation").removeClass('hidden');
        }
    </script>
@stop