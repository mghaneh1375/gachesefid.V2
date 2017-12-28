@extends('layouts.form')

@section('head')
    @parent
@stop

@section('caption')
    <div class="title">افزودن قالب پاسخ برگ
    </div>
@stop

@section('main')
    <center style="margin-top: 10px">
        @if($aId == -1)
            <form method="post" action="{{route('add_answer_sheet_template')}}">
        @else
            <form method="post" action="{{route('edit_answer_sheet_template', ['aId' => $aId])}}">
        @endif
            <div class="row">
                <div class="col-xs-12">
                    <label>
                        <span>نام</span>
                        <input type="text" name="name" maxlength="40" value="{{$name}}" required autofocus>
                    </label>
                </div>
                <div class="col-xs-12">
                    <label>
                        <span>تعداد ردیف</span>
                        <input value="{{$rowCount}}" type="number" name="row_count" required>
                    </label>
                </div>
                <div class="col-xs-12">
                    <label>
                        <span>تعداد ستون</span>
                        <input value="{{$colCount}}" type="number" name="col_count" required>
                    </label>
                </div>

                <div class="col-xs-12">
                    <input type="submit" name="submitForm" value="تایید" class="btn btn-primary">
                </div>
            </div>
        </form>
    </center>
@stop