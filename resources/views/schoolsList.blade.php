@extends('layouts.form')

@section('head')
    @parent
    <style>
        td {
            padding: 6px;
            min-width: 100px;
        }
        tr:nth-child(even) {
            background-color: #dddddd;
        }
    </style>

@stop

@section('caption')
    <div class="title" style="width: 500px !important;">
        فهرست مدارس المپیادی ایران
    </div>
@stop

@section('main')
    
    <?php
        $allowShowSchoolCode = \Illuminate\Support\Facades\Auth::check() && (\Illuminate\Support\Facades\Auth::user()->level != getValueInfo('studentLevel'));
    ?>
    
    <center style="margin-top: 50px">
        <p>
            اگر مدرسه شما در جدول زیر نیست، <a target="_blank" style="color: red; display: inline-block" href="https://www.irysc.com/irysc-mag.html">این فرم را تکمیل کنید</a> تا پس از بررسی، در لیست اضافه شود.
            <br/>
            تا پیش از اضافه شدن نام مدرسه، برای دیدن کارنامه و شرکت در آزمون، <span onclick="setAsMySchool('705')" style="color: red; cursor: pointer">آیریسک تهران</span> را انتخاب کنید.

        </p>
            <table id="table">
                <tr>
                    <td data-sort="asc" data-col="0" class="alphabeticallySortable" style="cursor: pointer"><span>نام مدرسه</span><span class="sortIcon" id="sortIcon_0"><i style="margin-right: 5px" class="fa fa-sort" aria-hidden="true"></i></span></td>
                    <td data-sort="asc" data-col="1" style="cursor: pointer" class="alphabeticallySortable"><span>شهر</span><span class="sortIcon" id="sortIcon_1"><i style="margin-right: 5px" class="fa fa-sort" aria-hidden="true"></i></span></td>
                    <td data-sort="asc" data-col="2" style="cursor: pointer" class="alphabeticallySortable"><span>استان</span><span class="sortIcon" id="sortIcon_2"><i style="margin-right: 5px" class="fa fa-sort" aria-hidden="true"></i></span></td>
                    <td data-sort="asc" data-col="3" style="cursor: pointer" class="alphabeticallySortable"><span>نوع مدرسه</span><span class="sortIcon" id="sortIcon_3"><i style="margin-right: 5px" class="fa fa-sort" aria-hidden="true"></i></span></td>
                    <td data-sort="asc" data-col="4" style="cursor: pointer" class="alphabeticallySortable"><span>مقطع</span><span class="sortIcon" id="sortIcon_4"><i style="margin-right: 5px" class="fa fa-sort" aria-hidden="true"></i></span></td>
                    <td data-sort="asc" data-col="5" style="cursor: pointer" class="alphabeticallySortable"><span>جنسیت</span><span class="sortIcon" id="sortIcon_5"><i style="margin-right: 5px" class="fa fa-sort" aria-hidden="true"></i></span></td>
                    <td>عملیات</td>
                    @if($allowShowSchoolCode)
                        <td>کد مدرسه</td>
                    @endif
                </tr>
                @foreach($users as $user)
                    <tr>
                        <td data-toggle="tooltip" title="انتخاب به عنوان مدرسه من" class="myTooltip schoolName">{{$user->schoolName}}</td>
                        <td>{{$user->schoolCity}}</td>
                        <td>{{$user->schoolState}}</td>
                        <td>{{$user->schoolKind}}</td>
                        <td>{{$user->schoolLevel}}</td>
                        <td>{{($user->sex == 0) ? 'دخترانه' : 'پسرانه'}}</td>

                        @if($user->id != $sId)
                            <td onclick="setAsMySchool('{{$user->id}}')"><button class="btn btn-primary">انتخاب به عنوان مدرسه من</button></td>
                        @else
                            <td style="color: red"><center>مدرسه من</center></td>
                        @endif
                        @if($allowShowSchoolCode)
                            <td>{{$user->invitationCode}}</td>
                        @endif
                    </tr>
                @endforeach
            </table>
    </center>

    <script>

        function sortTable(sort, col) {

            var table, rows, switching, i, x, y, shouldSwitch;
            table = document.getElementById("table");
            switching = true;

            while (switching) {

                switching = false;
                rows = table.getElementsByTagName("tr");
                for (i = 1; i < (rows.length - 1); i++) {

                    shouldSwitch = false;

                    x = rows[i].getElementsByTagName("td")[col].innerHTML.toLowerCase();
                    y = rows[i + 1].getElementsByTagName("td")[col].innerHTML.toLowerCase();

                    if(sort == 'asc') {
                        if (x < y) {
                            shouldSwitch = true;
                            break;
                        }
                    }
                    else {
                        if (y < x) {
                            shouldSwitch = true;
                            break;
                        }
                    }
                }
                if (shouldSwitch) {
                    rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                    switching = true;
                }
            }
        }

        $('.alphabeticallySortable').click(function() {

            sortTable($(this).attr('data-sort'), $(this).attr('data-col'));

            $(".sortIcon").empty().append('<i style="margin-right: 5px" class="fa fa-sort" aria-hidden="true"></i>');

            if ($(this).attr('data-sort') == 'asc') {
                $("#sortIcon_" + $(this).attr('data-col')).empty().append('<i style="margin-right: 5px" class="fa fa-sort-desc" aria-hidden="true"></i>');
                $(this).attr('data-sort', 'desc');
            }
            else {
                $("#sortIcon_" + $(this).attr('data-col')).empty().append('<i style="margin-right: 5px" class="fa fa-sort-asc" aria-hidden="true"></i>');
                $(this).attr('data-sort', 'asc');
            }
        });

        function setAsMySchool(sId) {

            $.ajax({
                type: 'post',
                url: '{{route('setAsMySchool')}}',
                data: {
                    'sId': sId
                },
                success: function (response) {
                    if(response == "ok")
                        document.location.href = '{{route('profile')}}';
                }
            });

        }

    </script>
@stop