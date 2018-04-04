@extends('layouts.form')

@section('head')
    @parent
    <script src="{{URL::asset('js/jsNeededForSlides.js')}}"></script>
    <link rel="stylesheet" href="{{URL::asset('css/slideCSS.css')}}">

    <script>

        var err = '{{$err}}';
        var mode = '{{$mode}}';

        $(document).ready(function () {

            if(err != "" || mode == "edit")
                $("#newSlidePane").css("visibility", "visible");

        });

    </script>

@stop

@section('main')
    <div class="row">
        <form method="post" action="{{route('opOnSlides')}}">
            {{csrf_field()}}
            <?php $i = 1; ?>
            @foreach($slides as $slide)
                <div class="col-xs-12" style="margin-top: 10px">
                    <center>
                        <div>
                            <button data-toggle="tooltip" name="editSlide" value="{{$slide->id}}" title="ویرایش" class="btn btn-info"><span class="glyphicon glyphicon-edit"></span></button>
                            <button data-toggle="tooltip" name="deleteSlide" value="{{$slide->id}}" title="حذف" class="btn btn-danger"><span class="glyphicon glyphicon-remove"></span></button>
                            <span>{{$i}}اسلاید </span>
                            <p><span>لینک:</span><span>&nbsp;</span><span onclick="document.location.href = '{{$slide->link}}'">{{$slide->link}}</span></p>
                        </div>
                        <img style="margin-top: 10px" src="{{URL::asset('images/slideBar') . '/' . $slide->pic}}">
                    </center>
                </div>
                <?php $i++; ?>
            @endforeach
        </form>

        <div class="col-xs-12" style="margin-top: 10px">
            <center>
                <button data-toggle="tooltip" title="اضافه کردن اسلاید جدید" class="btn btn-success" onclick="addNewSlide()">
                    <span class="glyphicon glyphicon-plus"></span>
                </button>
            </center>
        </div>


        <form method="post" action="{{route('opOnSlides')}}" enctype="multipart/form-data">
            {{csrf_field()}}
            <span id="newSlidePane" class="ui_overlay" style="visibility: hidden; position: fixed; left: 30%; right: auto; top: 174px; bottom: auto">
                <div class="fromUpload">
                    <div class="fileContainer">
                        <input name="newPic" type="file"/>
                    </div>
                    <div>
                        تصویر شما باید jpg و یا png و یا bmp باشد و سایز آن کمتر از 5 مگابایت باشد
                    </div>
                    <div>
                        <label>
                            <span>لینک: </span>
                            <input type="text" style="width: 100%" name="link">
                        </label>
                    </div>
                </div>
                <div id="uploadBtn">
                    <center style="margin-top: 10px">
                        <input type="submit" name="cancel" style="padding: 5px" class="btn btn-default" value="لغو">
                        @if($mode == "edit")
                            <input type="submit" name="doEditPhoto" style="padding: 5px; margin-left: 10px" class="btn btn-success " value="عوض کردن تصویر پروفایل">
                        @else
                            <input type="submit" name="submitPhoto" style="padding: 5px; margin-left: 10px" class="btn btn-success " value="عوض کردن تصویر پروفایل">
                        @endif
                        @if(!empty($err))
                            <p class="errorText">{{$err}}</p>
                        @endif
                    </center>
                </div>
            </span>
        </form>

    </div>
@stop