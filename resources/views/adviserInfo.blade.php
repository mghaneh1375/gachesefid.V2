@extends('layouts.form')

@section('head')
    @parent
@stop


@section('caption')
    <div class="title">مشخصات مشاور
    </div>
@stop

@section('main')

    <style>

        .col-xs-12 {
            padding: 6px;
        }

        .col-xs-12 > center {
            width: 600px;
        }
        label {
            float: right;
            width: 200px;
        }
        span {
            float: right;
        }
    </style>

    <center class="row" style="margin-top: 10px">
        <div class="col-xs-12">
            <center>
                <p>
                    <label><span>نام</span></label>
                    <span>{{$adviser->firstName}} {{$adviser->lastName}}</span>
                </p>
            </center>
        </div>
        <div class="col-xs-12">
            <center>
                <p>
                    <label><span>شهر</span></label>
                    <span>{{$adviserInfo->cityId}}</span>
                </p>
            </center>
        </div>
        <div class="col-xs-12">
            <center>
                <p>
                    <label><span>مدارس فعال</span></label>
                    <span>{{$adviserInfo->schools}}</span>
                </p>
            </center>
        </div>
        <div class="col-xs-12">
            <center>
                <p>
                    <label><span>تعداد سال سابقه:</span></label>
                    <span>{{substr(getToday()['date'], 0, 4) - $adviserInfo->workYears}}</span>
                </p>
            </center>
        </div>
        <div class="col-xs-12">
            <center>
                <p>
                    <label><span>سن:</span></label>
                    <span>{{substr(getToday()['date'], 0, 4) - $adviserInfo->birthDay}}</span>
                </p>
            </center>
        </div>
        <div class="col-xs-12">
            <center>
                <p>
                    <label><span>تالیفات و ترجمه ها:</span></label>
                    <span>{{$adviserInfo->essay}}</span>
                </p>
            </center>
        </div>
        <div class="col-xs-12">
            <center>
                <p>
                    <label><span>افتخارات علمی:</span></label>
                    <span>{{$adviserInfo->honors}}</span>
                </p>
            </center>
        </div>
        <div class="col-xs-12">
            <center>
                <p>
                    <label><span>آخرین مدرک تحصیلی:</span></label>
                    <span>{{$adviserInfo->lastCertificate}}</span>
                </p>
            </center>
        </div>
        <div class="col-xs-12">
            <center>
                <p>
                    <label><span>تخصص:</span></label>
                    <span>{{$adviserInfo->field}}</span>
                </p>
            </center>
        </div>
        <div class="col-xs-12">
            <center>
                <p>
                    <label><span>دروس تخصصی:</span></label>
                    @if(count($adviserFields) > 0)
                        <span>{{$adviserFields[0]->gradeId}}</span>
                    @endif
                    @for($i = 1; $i < count($adviserFields); $i++)
                        <span> - &nbsp;{{$adviserFields[$i]->gradeId}}</span>
                    @endfor
                </p>
            </center>
        </div>

        <div class="col-xs-12">
            <center><button onclick="setAsMyAdviser('{{$adviser->id}}')" class="btn btn-primary">انتخاب به عنوان مشاور من</button></center>
        </div>

    </center>

    <script>
        function setAsMyAdviser(adviserId) {

            $.ajax({
                type: 'post',
                url: '{{route('setAsMyAdviser')}}',
                data: {
                    'adviserId': adviserId
                },
                success: function (response) {
                    if(response == "ok") {
                        document.location.href = '{{route('advisersList')}}';
                    }
                    else {
                        alert("خطایی در انجام عملیات مورد نظر رخ داده است");
                    }
                }
            });

        }
    </script>
@stop