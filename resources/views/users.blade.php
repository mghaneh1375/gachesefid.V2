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
                                <td><center>{{$user->firstName}} {{$user->lastName}}</center></td>
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
                                    </center>
                                </td>
                                @if($user->status == 2)
                                    <td>
                                        <center>
                                            <button value="{{$user->id}}" name="uId" formaction="{{route('confirmAdviser')}}" class="btn btn-success">تایید مشاور</button>
                                        </center>
                                    </td>
                                @endif
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
@stop