@extends('layouts.form')

@section('head')
    @parent

    <link href="{{URL::asset('css/form.css')}}" rel="stylesheet" type="text/css">

    <script>
        function changePicStatus(val) {
            if(val == 0) {
                $("#pic").hide();
                $("#text").show();
            }
            else {
                $("#pic").show();
                $("#text").hide();
            }
        }
    </script>
@stop

@section('caption')
    <div class="title" style="width: 500px !important;">وضعیت های آزمون
    </div>
@stop

@section('main')

    <center>
        <div class="myRegister">
            <div class="data row">
                <form method="post" action="{{URL(route('quizStatus'))}}">
                    @foreach($quizStatus as $itr)
                        <div class="col-xs-12">
                            <label>
                                @if($itr->pic)
                                    <img width="40px" height="40px" src="{{URL::asset('status') . '/' . $itr->status}}">
                                @else
                                    <span>{{$itr->status}}</span>
                                @endif
                                @if($itr->type)
                                    <span> از </span>
                                    <span>{{$itr->floor}}</span>
                                    <span> تا </span>
                                    <span>{{$itr->ceil}}</span>
                                @else
                                    <span> میزان اختلاف با میانگین </span>
                                    <span>{{$itr->ceil}}</span>
                                @endif
                                <span> سطح تعریف </span>
                                @if($itr->level == 1)
                                    <span>در درس</span>
                                @elseif($itr->level == 2)
                                    <span>در مبحث</span>
                                @elseif($itr->level == 3)
                                    <span>در حیطه</span>
                                @endif

                                <input type="color" value="{{$itr->color}}" disabled>

                                <button class="MyBtn btn btn-danger" name="removeStatus" value="{{$itr->id}}" style="width: auto" data-toggle="tooltip" title="حذف وضعیت">
                                    <span class="glyphicon glyphicon-remove" style="margin-left: 40%"></span>
                                </button>
                            </label>
                        </div>
                    @endforeach
                    <button class="MyBtn btn btn-info circleBtn" style="width: auto" data-toggle="tooltip" title="ایجاد وضعیت جدید" name="addNewStatus">
                        <span class="glyphicon glyphicon-plus" style="margin-left: 40%"></span>
                    </button>
                </form>
            </div>
            @if($mode == 'addNewStatus')
                <div class="data row">
                    <form method="post" action="{{URL('quizStatus')}}" enctype="multipart/form-data">
                        <div class="col-xs-12">
                            <label>
                                <span>محتوای وضعیت</span>
                                <select name="isPicSet" onchange="changePicStatus(this.value)">
                                    <option value="0">متن</option>
                                    <option value="1">عکس</option>
                                </select>
                            </label>
                        </div>
                        <div class="col-xs-12" id="text">
                            <label>
                                <span>نام وضعیت جدید</span>
                                <input type="text" name="statusName">
                            </label>
                        </div>
                        <div class="col-xs-12" id="pic" hidden="hidden">
                            <label>
                                <span>نام فایل تصویر</span>
                                <input type="file" accept="image/jpeg" name="pic">
                            </label>
                        </div>
                        <div class="col-xs-12">
                            <label>
                                <span>نوع وضعیت</span>
                                <select id="type" name="type">
                                    <option value="1">مطلق</option>
                                    <option value="0">نسبی</option>
                                </select>
                            </label>
                        </div>
                        <div class="col-xs-12" id="floor">
                            <label>
                                <span>کران پایین</span>
                                <input type="number" min="0" value="0" max="100" name="floorStatus">
                            </label>
                        </div>
                        <div class="col-xs-12" id="ceil">
                            <label>
                                <span>کران بالا</span>
                                <input type="number" min="0" value="100" max="100" name="ceilStatus">
                            </label>
                        </div>
                        <div class="col-xs-12">
                            <label>
                                <span>سطح تعریف</span>
                                <select name="level">
                                    <option value="1">در درس</option>
                                    <option value="2">در مبحث</option>
                                    <option value="3">در حیطه</option>
                                </select>
                            </label>
                        </div>
                        <div class="col-xs-12">
                            <label>
                                <span>رنگ مربوط به وضعیت</span>
                                <input type="color" name="color">
                            </label>
                        </div>
                        <div class="col-xs-12">
                            <center style="margin-top: 10px">{{$msg}}</center>
                            <input type="submit" class="btn btn-default" style="width: auto" name="doAddStatus" value="تایید">
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </center>

@stop
