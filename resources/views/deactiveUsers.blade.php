@extends('layouts.form')

@section('head')
    @parent
@stop

@section('caption')
    <div class="title" style="width: 500px !important;">تایید کاربران غیرفعال</div>
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

        <div class="col-xs-12" style="margin-top: 20px">
            <table>
                <tr>
                    <td><center>نام</center></td>
                    <td><center>نام کاربری</center></td>
                    <td><center>شماره همراه</center></td>
                    <td><center>کد ملی</center></td>
                    <td><center>عملیات</center></td>
                </tr>
                @foreach($users as $user)

                    <tr id="user_{{$user->id}}">
                        <td><center>{{$user->firstName . ' ' . $user->lastName}}</center></td>
                        <td><center>{{$user->username}}</center></td>
                        <td><center>{{$user->phoneNum}}</center></td>
                        <td><center>{{$user->NID}}</center></td>
                        <td>
                            <center>
                                <span onclick="active('{{$user->id}}')" class="btn btn-primary">تایید کاربر</span>
                            </center>
                        </td>
                    </tr>
                @endforeach
            </table>
    </div>

    </center>

    <script>

        function active(userId) {

            $.ajax({
                type: 'post',
                url: '{{route('activeUser')}}',
                data: {
                    'userId': userId
                },
                success: function (response) {
                    $("#user_" + userId).remove();
                }
            });
        }

    </script>

@stop