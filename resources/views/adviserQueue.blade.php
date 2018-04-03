@extends('layouts.form')

@section('head')
    @parent
@stop


@section('caption')
    <div class="title">دانش آموزان در صف انتظار
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
                <td><center>نام</center></td>
                <td><center>نام کاربری</center></td>
                <td><center>شهر</center></td>
                <td><center>پایه تحصیلی</center></td>
                <td><center>عملیات</center></td>
            </tr>

            @foreach($students as $itr)
                <tr>
                    <td><center>{{$itr->user->firstName . ' ' . $itr->user->lastName}}</center></td>
                    <td><center>{{$itr->user->username}}</center></td>
                    <td><center>{{$itr->city}}</center></td>
                    <td><center>{{$itr->grade}}</center></td>
                    <td>
                        <center>
                            <button onclick="accept('{{$itr->user->id}}')" data-toggle="tooltip" title="تایید دانش آموز" class="btn btn-success"><span class="glyphicon glyphicon-ok"></span></button>
                            <button onclick="reject('{{$itr->user->id}}')" data-toggle="tooltip" title="رد دانش آموز" class="btn btn-danger"><span class="glyphicon glyphicon-remove"></span></button>
                        </center>
                    </td>
                </tr>
            @endforeach
        </table>
    </center>

    <script>

        function accept(uId) {

            $.ajax({
                type: 'post',
                url: '{{route('acceptStudent')}}',
                data: {
                    'uId': uId
                },
                success: function (response) {
                    if(response == "ok") {
                        document.location.href = '{{route('adviserQueue')}}'
                    }
                }
            });

        }

        function reject(uId) {

            $.ajax({
                type: 'post',
                url: '{{route('rejectStudent')}}',
                data: {
                    'uId': uId
                },
                success: function (response) {
                    if(response == "ok") {
                        document.location.href = '{{route('adviserQueue')}}'
                    }
                }
            });
        }

    </script>
@stop