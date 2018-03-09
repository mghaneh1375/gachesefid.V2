@extends('layouts.form2')

@section('head')
    @parent
@stop


@section('caption')
    <div class="title">گزارش گیری از دانش آموزان
    </div>
@stop

@section('main')

    <style>
        td {
            padding: 10px;
        }

        tr:nth-child(even) {
            background-color: #dddddd;
        }
    </style>

    <script>
        var selectedUser;
    </script>

    <center style="margin-top: 10px">

        <p style="background-color: #86af79; padding: 10px"><span>تعداد کل دانش آموزان:</span><span>&nbsp;</span><span>{{$total}}</span></p>

        <div class="col-xs-12">
            <div style="float: right">
                <label for="name" style="min-width: 130px; text-align: right">
                    نام و نام خانوادگی
                </label>
                <input id="name" value="{{(!empty($name) ? $name : '')}}" type="text">
                <span style="cursor: pointer" onclick="document.location.href = '{{route('home')}}' + '/studentReport/name/' + $('#name').val() + '/{{$page}}'" class="glyphicon glyphicon-search"></span>
            </div>
            <div style="clear: both"></div>
            <div style="float: right">
                <label for="username" style="min-width: 130px; text-align: right">
                    نام کاربری
                </label>
                <input id="username" value="{{(!empty($username) ? $username : '')}}" type="text">
                <span style="cursor: pointer" onclick="document.location.href = '{{route('home')}}' + '/studentReport/username/' + $('#username').val() + '/{{$page}}'" class="glyphicon glyphicon-search"></span>
            </div>
        </div>


        @if(count($users))

            <table style="padding: 20px">
                <tr>
                    <td><center>نام</center></td>
                    <td><center>نام خانوادگی</center></td>
                    <td><center>نام کاربری</center></td>
                    <td><center>شماره تماس</center></td>
                    <td><center>پایه تحصیلی</center></td>
                    <td><center>مشاور</center></td>
                    <td><center>شهر</center></td>
                    <td><center>مدرسه</center></td>
                    <td><center>عملیات</center></td>
                </tr>
                @foreach($users as $user)
                    <tr>
                        <td><center>{{$user->firstName}}</center></td>
                        <td><center>{{$user->lastName}}</center></td>
                        <td><center>{{$user->username}}</center></td>
                        <td><center>{{$user->phoneNum}}</center></td>
                        <td><center>{{$user->grade}}</center></td>
                        <td><center>{{$user->adviser}}</center></td>
                        <td><center>{{$user->city}}</center></td>
                        <td><center>{{$user->school}}</center></td>
                        <td>
                            <center>
                                <button onclick="showEdit('{{$user->id}}', '{{$user->firstName}}', '{{$user->lastName}}', '{{$user->username}}', '{{$user->phoneNum}}')" class="btn btn-primary" data-toggle="tooltip" title="ویرایش">
                                    <span class="glyphicon glyphicon-edit"></span>
                                </button>

                                <button onclick="showConfirmationPane('{{$user->id}}')" class="btn btn-danger" data-toggle="tooltip" title="حذف">
                                    <span class="glyphicon glyphicon-remove"></span>
                                </button>
                            </center>
                        </td>
                    </tr>
                @endforeach

            </table>

            <div style="margin-top: 10px">
                <button onclick="document.location.href = '{{route('studentReportExcel')}}'" class="btn btn-success">دانلود فایل اکسل</button>
            </div>
        @endif

        <script src="{{URL::asset('js/paging.js')}}"></script>

        <div class="col-xs-12" id="pageBar"></div>

        <script> 
            @if(empty($name) && empty($username))
                init('{{route('studentReport')}}', '{{$total}}', 20, '{{$page}}', 'pageBar');
            @elseif(!empty($name))
                init('{{route('home')}}' + '/studentReport/name/' + $('#name').val(), '{{$total}}', 20, '{{$page}}', 'pageBar');
            @else
                init('{{route('home')}}' + '/studentReport/username/' + $('#username').val(), '{{$total}}', 20, '{{$page}}', 'pageBar');
            @endif
        </script>

    </center>

    <span id="editUser" class="hidden ui_overlay" style="position: fixed; left: 30%; width: 40%; right: auto; top: 174px; bottom: auto">
        <div class="header_text">ویرایش اطلاعات کاربری</div>
        <div onclick="$('.dark').addClass('hidden'); $('#editUser').addClass('hidden')" class="ui_close_x"></div>
        <div class="body_text">
            <div>
                <label style="min-width: 150px" for="firstName">نام</label>
                <input id="firstName" type="text">
            </div>
            <div>
                <label style="min-width: 150px" for="lastName">نام خانوادگی</label>
                <input id="lastName" type="text">
            </div>
            <div>
                <label style="min-width: 150px" for="username">نام کاربری</label>
                <input id="userName" type="text">
            </div>
            <div>
                <label style="min-width: 150px" for="phone">شماره همراه</label>
                <input id="phone" type="tel">
            </div>

            <div>
                <label style="min-width: 150px" for="password">رمز عبور جدید</label>
                <input id="password" type="password">
            </div>

            <div>
                <label style="min-width: 150px" for="confirm">تکرار رمز عبور جدید</label>
                <input id="confirm" type="password">
            </div>

            <center style="margin-top: 10px">
                <span onclick="doEdit()" class="btn btn-success">تایید</span>
            </center>

            <center>
                <p class="errorText" id="errMsg"></p>
            </center>

        </div>
    </span>

    <span id="removeUser" class="hidden ui_overlay" style="position: fixed; left: 30%; width: 40%; right: auto; top: 174px; bottom: auto">
        <div class="header_text">حذف کاربر</div>
        <div onclick="$('.dark').addClass('hidden'); $('#removeUser').addClass('hidden')" class="ui_close_x"></div>
        <div class="body_text">
            <p>آیا از حذف کاربر مورد نظر اطمینان دارید؟</p>

            <center style="margin-top: 10px">
                <span onclick="doRemove()" class="btn btn-success">بله</span>
            </center>
        </div>
    </span>

    <script>

        function doRemove() {
            $.ajax({
                type: 'post',
                url: '{{route('doRemoveUser')}}',
                data: {
                    'uId': selectedUser
                },
                success: function (response) {
                    if(response == "ok")
                        document.location.href = "{{route('studentReport')}}";
                }
            });
        }

        function doEdit() {
            
            $.ajax({
                type: 'post',
                url: '{{route('doEditUser')}}',
                data: {
                    'uId': selectedUser,
                    'firstName': $("#firstName").val(),
                    'lastName': $("#lastName").val(),
                    'username': $("#userName").val(),
                    'phone': $("#phone").val(),
                    'password': $("#password").val(),
                    'confirm': $("#confirm").val()
                },
                success: function (response) {

                    if(response == "ok") {
                        @if(empty($name) && empty($username))
                            document.location.href = '{{route('home')}}' + '/studentReport/' + '{{$page}}';
                        @elseif(!empty($name))
                            document.location.href = '{{route('home')}}' + '/studentReport/name/' + $('#name').val() + '/{{$page}}';
                        @else
                                document.location.href = '{{route('home')}}' + '/studentReport/username/' + $('#username').val() + '/{{$page}}';
                        @endif
                    }
                    else if(response == "nok1") {
                        $("#errMsg").empty().append('رمز عبور جدید و تکرار آن یکی نیستند');
                    }
                    else if(response == "nok2") {
                        $("#errMsg").empty().append('نام کاربری وارد شده در سامانه موجود است');
                    }
                    else
                        $("#errMsg").empty().append('مشکلی در انجام فرآیند مورد نظر رخ داده است');
                }
            })
            
        }

        function showConfirmationPane(uId) {
            selectedUser = uId;
            $('.dark').removeClass('hidden');
            $("#removeUser").removeClass('hidden');
        }
        
        function showEdit(uId, firstName, lastName, username, phone) {
            selectedUser = uId;
            $("#firstName").val(firstName);
            $("#lastName").val(lastName);
            $("#userName").val(username);
            $("#phone").val(phone);
            $('.dark').removeClass('hidden');
            $("#editUser").removeClass('hidden');
        }

    </script>
@stop