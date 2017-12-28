@extends('layouts.form')

@section('head')
    @parent
    <link rel="stylesheet" href="{{URL::asset('css/form.css')}}">
    <script>
        var addTagDir = '{{route('addTag')}}';
        var editTagDir = '{{route('editTag')}}';
        var tags = '{{route('tags')}}';
        var deleteTagDir = '{{route('deleteTag')}}';
    </script>
    <script src="{{URL::asset('js/jsNeededForTag.js')}}"></script>
@stop


@section('caption')
    <div class="title">تگ های مربوط به رد سوالات
    </div>
@stop

@section('main')
    <center class="myRegister">
        <div class="row data">

            @if(count($tags) == 0)
                <p>تگی موجود نیست</p>

            @else
                @foreach($tags as $tag)
                    <div class="col-xs-12" style="margin-top: 10px">
                        <span>{{$tag->name}}</span>
                        <button class="btn btn-danger" data-toggle="tooltip" title="حذف تگ" onclick="deleteTag('{{$tag->id}}')">
                            <span style="margin-left: 30%" class="glyphicon glyphicon-remove"></span>
                        </button>
                        <button class="btn btn-primary" data-toggle="tooltip" title="ویرایش تگ" onclick="editTag('{{$tag->id}}')">
                            <span style="margin-left: 30%" class="glyphicon glyphicon-edit"></span>
                        </button>
                    </div>
                @endforeach
            @endif

            <div class="col-xs-12" style="margin-top: 10px">
                <center>
                    <input type="submit" value="افزودن تگ جدید" onclick="showAddTag()" class="btn btn-primary">
                </center>
            </div>

            <div class="col-xs-12">
                <center>
                    <p class="errorText" id="errMsg"></p>
                </center>
            </div>

        </div>
    </center>

    <span id="addNewTagPane" class="ui_overlay item hidden" style="position: fixed; max-width: 400px; left: 40%; right: auto; top: 174px; bottom: auto">
        <div class="header_text">تگ جدید</div>
        <div onclick="hideElement()" class="ui_close_x"></div>
        <div class="body_text row">
            <div class="col-xs-12">
                <label>
                    <span>تگ جدید</span>
                    <input type="text" maxlength="40" id="tagName">
                </label>
            </div>

            <div class="submitOptions" style="margin-top: 10px">
                <button onclick="doAddNewTag()" class="btn btn-success">تایید</button>
                <input type="submit" onclick="hideElement()" value="خیر" class="btn btn-default">
            </div>
            <p class="errorText" id="errMsg"></p>
        </div>
    </span>

    <span id="editTagPane" class="ui_overlay item hidden" style="position: fixed; max-width: 400px; left: 40%; right: auto; top: 174px; bottom: auto">
        <div class="header_text">ویرایش تگ</div>
        <div onclick="hideElement()" class="ui_close_x"></div>
        <div class="body_text row">
            <div class="col-xs-12">
                <label>
                    <span>نام جدید تگ</span>
                    <input type="text" maxlength="40" id="newName">
                </label>
            </div>

            <div class="submitOptions" style="margin-top: 10px">
                <button onclick="doEditTag()" class="btn btn-success">تایید</button>
                <input type="submit" onclick="hideElement()" value="خیر" class="btn btn-default">
            </div>
            <p class="errorText" id="errMsgEdit"></p>
        </div>
    </span>
@stop