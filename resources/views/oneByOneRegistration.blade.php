@extends('layouts.form')

@section('head')
    @parent
@stop


@section('caption')
    <div class="title">ثبت نام تک تک</div>
    <style>
        input {
            max-width: 200px;
        }
        select {
            width: auto;
        }
    </style>
@stop

@section('main')

    <div class="row">

        <div class="col-xs-12" id="studentContainer">

        </div>

        <div class="col-xs-12">
            <center>
                <button onclick="addStudent()" data-toggle="tooltip" title="افزودن دانش آموز جدید" class="btn btn-primary circleBtn"><span class="glyphicon glyphicon-plus"></span></button>
                <button onclick="send()" data-toggle="tooltip" title="ارسال اطلاعات" class="btn btn-success circleBtn"><span class="glyphicon glyphicon-ok"></span></button>
            </center>
        </div>

        <div class="col-xs-12" style="margin: 20px; padding: 10px">
            <center id="msgPane"></center>
        </div>

        <div class="col-xs-12 hidden" id="downloadPane" style="margin: 20px; padding: 10px">
            <center>
                <div style="padding: 10px" class="btn btn-danger"><a style="color: white" href='{{URL::asset('registrations/report_' . Auth::user()->id . '.xlsx')}}' download>دانلود فایل اکسل گزارش ثبت نام</a></div>
            </center>
        </div>
    </div>


    <script>

        var schools = [];
        var row = 0;
        var grades = {!! json_encode($grades) !!};
        var level = false;
        @if(\Illuminate\Support\Facades\Auth::user()->level == getValueInfo('namayandeLevel'))
            level = true;
            schools = {!! json_encode($schools) !!};
        @endif

        function addStudent() {

            var newElement = '<div id="row_' + row + '" class="col-xs-12" style="margin: 20px; padding: 10px; border-bottom: 3px dotted black">';
            newElement += '<div class="col-xs-4"><label>پایه تحصیلی</label><select name="grade[]">';

            for(i = 0; i < grades.length; i++)
                newElement += "<option value='" + grades[i].id + "'>" + grades[i].name + "</option>";

            newElement += '</select></div>';

            newElement += '<div class="col-xs-4"><label>نام خانوادگی</label><input type="text" name="lastName[]"></div>';
            newElement += '<div class="col-xs-4"><label>نام</label><input type="text" name="firstName[]"></div>';
            newElement += '<div class="col-xs-4"><label>کد ملی</label><input type="tel" name="NID[]"></div>';
            newElement += '<div class="col-xs-4"><label>جنسیت</label><select style="width: 100px !important;" name="sex[]"><option value="0">خانم</option><option value="1">آقا</option></select></div>';
            newElement += '<div class="col-xs-4">';
            if(level) {
                newElement += '<label>مدرسه</label><select name="school[]">';
                for(i = 0; i < schools.length; i++)
                    newElement += "<option value='" + schools[i].id + "'>" + schools[i].name + "-" + schools[i].cityId + "</option>";
            }
            newElement += "</select></div>";
            newElement += "<div class='col-xs-12'><center><button onclick='deleteRow(" + row + ")' class='btn btn-danger'><span class='glyphicon glyphicon-remove'></span></button></center></div>";
            newElement += "</div>";
            row++;
            $("#studentContainer").append(newElement);
        }

        function deleteRow(idx) {
            $("#row_" + idx).addClass('hidden');
        }

        var firstNameArr = [];
        var lastNameArr = [];
        var NIDArr = [];
        var schoolArr = [];
        var gradeArr = [];
        var sexArr = [];
        var allow;
        var counter;

        function send() {

            allow = true;
            counter = 0;

            $("input[name='firstName[]']").each(function() {
                tmp = $(this).val();
                if(tmp.length == 0) {
                    alert("لطفا نام تمام دانش آموزان را وارد نمایید");
                    allow = false;
                    return;
                }

                firstNameArr[counter++] = tmp;
            });

            if(!allow)
                return;

            counter = 0;

            $("input[name='lastName[]']").each(function() {
                tmp = $(this).val();
                if(tmp.length == 0) {
                    alert("لطفا نام خانوادگی تمام دانش آموزان را وارد نمایید");
                    allow = false;
                    return;
                }

                lastNameArr[counter++] = tmp;
            });

            if(!allow)
                return;

            counter = 0;

            $("input[name='NID[]']").each(function() {
                tmp = $(this).val();
                if(tmp.length == 0) {
                    alert("لطفا کد ملی تمام دانش آموزان را وارد نمایید");
                    allow = false;
                    return;
                }

                NIDArr[counter++] = tmp;
            });

            if(!allow)
                return;

            counter = 0;

            $("select[name='sex[]']").each(function() {
                sexArr[counter++] = $(this).val();
            });

            counter = 0;

            $("select[name='grade[]']").each(function() {
                gradeArr[counter++] = $(this).val();
            });

            if(level) {

                counter = 0;

                $("select[name='school[]']").each(function() {
                    schoolArr[counter++] = $(this).val();
                });

                if(firstNameArr.length != lastNameArr.length || firstNameArr.length != NIDArr.length ||
                    firstNameArr.length != schoolArr.length || firstNameArr.length != gradeArr.length ||
                    firstNameArr.length != sexArr.length) {
                    alert("اشکالی در انجام عملیات مورد نظر رخ داده است");
                    return;
                }

                $.ajax({
                    type: 'post',
                    url: '{{route('doOneByOneRegistration')}}',
                    data: {
                        'firstNameArr': firstNameArr,
                        'lasNameArr': lastNameArr,
                        'schoolArr': schoolArr,
                        'NIDArr': NIDArr,
                        'sexArr': sexArr,
                        'gradeArr': gradeArr
                    },
                    success: function (response) {

                        response = JSON.parse(response);

                        if(response.status == "ok"){
                            newElement = "";
                            msg = response.msg;

                            for(i = 0; i < msg.length; i++) {
                                newElement += "<div class='col-xs-12'><p>" + msg[i] + "</p></div>";
                            }

                            $("#msgPane").empty().append(newElement);
                            $("#downloadPane").removeClass('hidden');
                        }
                        else {
                            $("#msgPane").empty().append(response.msg);
                        }
                    }
                });

                return;
                
            }

            else if(firstNameArr.length != lastNameArr.length || firstNameArr.length != NIDArr.length ||
                    firstNameArr.length != gradeArr.length || firstNameArr.length != sexArr.length) {
                alert("اشکالی در انجام عملیات مورد نظر رخ داده است");
                return;
            }

            $.ajax({
                type: 'post',
                url: '{{route('doOneByOneRegistration')}}',
                data: {
                    'firstNameArr': firstNameArr,
                    'lasNameArr': lastNameArr,
                    'NIDArr': NIDArr,
                    'sexArr': sexArr,
                    'gradeArr': gradeArr
                },
                success: function (response) {

                    response = JSON.parse(response);

                    if(response.status == "ok"){

                        newElement = "";
                        msg = response.msg;

                        for(i = 0; i < msg.length; i++) {
                            newElement += "<div class='col-xs-12'><p>" + msg[i] + "</p></div>";
                        }

                        $("#msgPane").empty().append(newElement);
                        $("#downloadPane").removeClass('hidden');
                    }
                    else {
                        $("#msgPane").empty().append(response.msg);
                    }
                }
            });

        }

        $(document).ready(function () {
            addStudent();
        });

    </script>

@stop