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

        <button onclick="showPopUp()" class="btn btn-success">افزودن دانش آموز به آزمون</button>

        <table style="padding: 10px">
            <tr>
                <td><center>نام</center></td>
                <td><center>نام خانوادگی</center></td>
                <td><center>کد ملی</center></td>
                <td><center>شماره همراه</center></td>
                <td><center>آنلاین</center></td>
                <td><center>عملیات</center></td>
            </tr>

            @foreach($items as $itr)
                <tr id="row_{{$itr->id}}">
                    <td><center>{{$itr->firstName}}</center></td>
                    <td><center>{{$itr->lastName}}</center></td>
                    <td><center>{{$itr->NID}}</center></td>
                    <td><center>{{$itr->phoneNum}}</center></td>
                    <td><center id="status_{{$itr->id}}" data-toggle="{{$itr->online}}">{{$itr->online}}</center></td>
                    <td>
                        <center>
                            <button onclick="toggleStatus('{{$itr->id}}')" class="btn btn-primary">تغییر وضعیت آزمون</button>
                            <button onclick="deleteFromQuiz('{{$itr->id}}')" class="btn btn-danger">حذف از آزمون</button>
                        </center>
                    </td>
                </tr>
            @endforeach
        </table>

        <div style="margin-top: 10px">
            <button onclick="document.location.href = '{{route('participantsQuizReportExcel', ['quizId' => $quizId])}}'" class="btn btn-success">دانلود فایل اکسل</button>
        </div>
    </center>

    <span id="add" class="hidden ui_overlay" style="position: fixed; left: 30%; width: 40%; right: auto; top: 174px; bottom: auto">
        <div class="header_text">افزودن کاربر</div>
        <div onclick="$('.dark').addClass('hidden'); $('#add').addClass('hidden')" class="ui_close_x"></div>
        <div class="body_text">

            <div>
                <input type="text" id="nid" placeholder="کد ملی">
            </div>

            <div>
                <label for="online">نوع حضور در آزمون</label>
                <select id="online">
                    <option value="0">حضوری</option>
                    <option value="1">آنلاین</option>
                </select>
            </div>

            <center style="margin-top: 10px">
                <span onclick="addToQuiz()" class="btn btn-success">افزودن</span>
            </center>
        </div>
    </span>

    <script>
        
        function addToQuiz() {

            var nid = $("#nid").val();
            if(nid.length == 0)
                return;

            var online = $("#online").val();

            $.ajax({
                type: 'post',
                url: '{{route('addToRegularQuiz')}}',
                data: {
                    'online': online,
                    'nid': nid,
                    'quizId': '{{$quizId}}'
                },
                success: function (response) {
                    if(response == "ok")
                        document.location.reload();
                }
            });

        }

        function deleteFromQuiz(id) {
            $.ajax({
                type: 'post',
                url: '{{route('deleteFromQuiz')}}',
                data: {
                    'id': id
                },
                success: function (response) {
                    if(response == "ok")
                        $("#row_" + id).remove();
                }
            });
        }
        
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
        
        function showPopUp() {
            $('.dark').removeClass('hidden');
            $('#add').removeClass('hidden');
        }


    </script>

@stop