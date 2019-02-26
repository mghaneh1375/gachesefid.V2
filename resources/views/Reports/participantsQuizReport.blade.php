@extends('layouts.form')

@section('head')
    @parent
@stop


@section('caption')
    <div class="title">نفرات شرکت کننده آزمون
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
                <td><center>نام خانوادگی</center></td>
                <td><center>شماره همراه</center></td>
                <td><center>آنلاین</center></td>
                <td><center>عملیات</center></td>
            </tr>

            @foreach($items as $itr)
                <tr>
                    <td><center>{{$itr->firstName}}</center></td>
                    <td><center>{{$itr->lastName}}</center></td>
                    <td><center>{{$itr->phoneNum}}</center></td>
                    <td><center id="status_{{$itr->id}}" data-toggle="{{$itr->online}}">{{$itr->online}}</center></td>
                    <td>
                        <center>
                            <button onclick="toggleStatus('{{$itr->id}}')" class="btn btn-red">تغییر وضعیت آزمون</button>
                        </center>
                    </td>
                </tr>
            @endforeach
        </table>

        <div style="margin-top: 10px">
            <button onclick="document.location.href = '{{route('participantsQuizReportExcel', ['quizId' => $quizId])}}'" class="btn btn-success">دانلود فایل اکسل</button>
        </div>
    </center>

    <script>

        function toggleStatus(id) {

            $.ajax({
                type: 'post',
                url: '{{route('toggleStatusOnline')}}',
                data: {
                    'id': id
                },
                success: function (response) {
                    if(response == "ok") {
                        if ($("#status_" + id).attr('data-toggle') == 'آنلاین')
                            $("#status_" + id).attr('data-toggle', 0).empty().append('حضوری');
                        else
                            $("#status_" + id).attr('data-toggle', 1).empty().append('آنلاین');
                    }
                }
            });
        }


    </script>

@stop