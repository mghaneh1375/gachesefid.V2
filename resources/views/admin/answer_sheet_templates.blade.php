@extends('layouts.form')

@section('head')
    @parent
@stop

@section('caption')
    <div class="title">قالب های پاسخ برگ
    </div>
@stop

@section('main')
    <center style="margin-top: 10px">
            <div class="row">

                @if(count($answerSheetTemplates) == 0)
                    <div class="col-xs-12">
                        <p class="errorText">پاسخ برگی موجود نیست</p>
                    </div>

                @else

                    <div class="col-xs-4">امکانات</div>
                    <div class="col-xs-4">تعداد سوالات</div>
                    <div class="col-xs-4">نام</div>

                    @foreach($answerSheetTemplates as $answerSheetTemplate)
                        <div class="col-xs-12" style="margin-top: 10px">
                            <div class="col-xs-4">

                                <button onclick="document.location.href = '{{route('delete_answer_sheet_template', ['answer_sheet_template' => $answerSheetTemplate->id])}}'" class="btn btn-danger" data-toggle="tooltip" title="حذف پاسخ برگ">
                                    <span class="glyphicon glyphicon-remove"></span>
                                </button>

                                <button onclick="document.location.href = '{{route('edit_answer_sheet_template', ['aId' => $answerSheetTemplate->id])}}'" class="btn btn-primary" data-toggle="tooltip" title="ویرایش پاسخ برگ">
                                    <span class="glyphicon glyphicon-edit"></span>
                                </button>
                                <button onclick="document.location.href = '{{route('answer_answer_sheet_template', ['answer_sheet_template' => $answerSheetTemplate->id])}}'" class="btn btn-default" data-toggle="tooltip" title="افزودن به پاسخ برگ">
                                    <span class="glyphicon glyphicon-list"></span>
                                </button>
                            </div>
                            <div class="col-xs-4">{{$answerSheetTemplate->countNum}}</div>
                            <div class="col-xs-4">{{$answerSheetTemplate->name}}</div>
                        </div>
                    @endforeach
                @endif

                    <div class="col-xs-12">
                        <button onclick="document.location.href = '{{route('add_answer_sheet_template')}}'" class="btn btn-success">افزودن</button>
                    </div>
            </div>

    </center>
@stop