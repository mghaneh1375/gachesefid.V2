@extends('layouts.form2')

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
                    @foreach($adviserFields as $adviserField)
                        <span>{{$adviserField->gradeId}} - &nbsp;</span>
                    @endforeach
                </p>
            </center>
        </div>
    </center>

@stop