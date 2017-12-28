@extends('layouts.form')

@section('head')
    @parent
    <link href="{{URL::asset('css/form.css')}}" rel="stylesheet" type="text/css">
@stop

@section('caption')
    <div class="title">تعیین امتیازات
    </div>
@stop

@section('main')
    <form method="post" action="{{route('pointsConfig')}}">
        <center class="myRegister">
            <div class="row data">
                <div class="col-xs-12">
                    <div class="col-xs-7">
                        <input type="number" name="invitationPoint" min="0" value="{{$config->invitationPoint}}" required>
                    </div>
                    <div class="col-xs-5">
                        <span>استفاده از کد معرف</span>
                    </div>
                </div>
                <div class="col-xs-12">
                    <div class="col-xs-7">
                        <input type="number" name="rankInQuizPoint" min="0" value="{{$config->rankInQuizPoint}}" required>
                    </div>
                    <div class="col-xs-5">
                        <span>رتبه ی برتری در آزمون</span>
                    </div>
                </div>
                <div class="col-xs-12">
                    <div class="col-xs-7">
                        <input type="number" name="makeQuestionPoint" min="0" value="{{$config->makeQuestionPoint}}" required>
                    </div>
                    <div class="col-xs-5">
                        <span>طراحی سوال</span>
                    </div>
                </div>
                <div class="col-xs-12">
                    <div class="col-xs-7">
                        <input type="number" name="solveQuestionPoint" min="0" value="{{$config->solveQuestionPoint}}" required>
                    </div>
                    <div class="col-xs-5">
                        <span>حل سوالات</span>
                    </div>
                </div>
                <div class="col-xs-12">
                    <div class="col-xs-7">
                        <input type="number" name="infoPass2Point" min="0" value="{{$config->infoPass2Point}}" required>
                    </div>
                    <div class="col-xs-5">
                        <span>تکمیل مرحله ی دوم ثبت نام</span>
                    </div>
                </div>
                <div class="col-xs-12">
                    <div class="col-xs-7">
                        <input type="number" name="infoPass3Point" min="0" value="{{$config->infoPass3Point}}" required>
                    </div>
                    <div class="col-xs-5">
                        <span>تکمیل مرحله ی سوم ثبت نام</span>
                    </div>
                </div>
                <div class="col-xs-12">
                    <div class="col-xs-7">
                        <input type="number" name="init" min="0" value="{{$config->init}}" required>
                    </div>
                    <div class="col-xs-5">
                        <span>امتیاز اولیه</span>
                    </div>
                </div>
                <div class="col-xs-12" style="margin-top: 10px">
                    <input type="submit" value="تایید" name="submitPointConfig" class="btn btn-default">
                </div>
            </div>
        </center>
    </form>
@stop