@extends('layouts.form')

@section('head')
    @parent

    <script>
        var adviserQuestions = '{{route('adviserQuestions')}}';
        var addAdviserQuestion = '{{route('addAdviserQuestion')}}';
        var editAdviserQuestion = '{{route('editAdviserQuestion')}}';
    </script>

    <script src="{{URL::asset('js/jsNeededForAdviserQuestion.js')}}"></script>
@stop


@section('caption')
    <div class="title">سوالات نظرسنجی</div>
@stop

@section('main')
    <center class="myRegister">
        <div class="row data">

            <form method="post" action="{{route('deleteAdviserQuestion')}}">
                {{csrf_field()}}
                @foreach($adviserQuestions as $itr)
                    <div class="col-xs-12" style="margin-top: 10px">
                        <span>{{$itr->name}}</span>
                        <button name="deleteId" value="{{$itr->id}}" class="btn btn-danger" data-toggle="tooltip" title="حذف سوال">
                            <span class="glyphicon glyphicon-remove" style="margin-left: 30%"></span>
                        </button>
                        <span onclick="editQuestion('{{$itr->id}}', '{{$itr->name}}')" data-toggle="tooltip" title="ویرایش سوال" class="btn btn-primary">
                            <span class="glyphicon glyphicon-edit" style="margin-left: 30%"></span>
                        </span>
                    </div>
                @endforeach
            </form>

            <div class="col-xs-12" style="margin-top: 10px">
                <button class="btn btn-default circleBtn" data-toggle="tooltip" title="افزودن سوال جدید" onclick="$('.dark').removeClass('hidden'); $('#newAdviserQuestionContainer').removeClass('hidden')">
                    <span class="glyphicon glyphicon-plus" style="margin-left: 30%"></span>
                </button>
            </div>
        </div>
    </center>

    <span id="newAdviserQuestionContainer" class="ui_overlay item hidden" style="position: fixed; left: 40%; right: auto; top: 174px; bottom: auto">
        <div class="header_text">سوال جدید</div>
        <div onclick="$('.dark').addClass('hidden'); $('#newAdviserQuestionContainer').addClass('hidden')" class="ui_close_x"></div>
            <div class="body_text">
                <textarea style="height: 200px; width: 400px" id="questionId" maxlength="1000" placeholder="حداکثر 1000 کاراکتر"></textarea>
                <div class="submitOptions" style="margin-top: 10px">
                    <button onclick="doAddNewAdviserQuestion()" class="btn btn-success">تایید</button>
                    <input type="submit" onclick="$('.dark').addClass('hidden'); $('#newAdviserQuestionContainer').addClass('hidden')" value="خیر" class="btn btn-default">
                    <p id="err" class="errorText"></p>
                </div>
            </div>
    </span>

    <span id="editAdviserQuestionContainer" class="ui_overlay item hidden" style="position: fixed; left: 40%; right: auto; top: 174px; bottom: auto">
        <div class="header_text">سوال جدید</div>
        <div onclick="$('.dark').addClass('hidden'); $('#editAdviserQuestionContainer').addClass('hidden')" class="ui_close_x"></div>
            <div class="body_text">
                <textarea style="height: 200px; width: 400px" id="editQuestionId" maxlength="1000" placeholder="حداکثر 1000 کاراکتر"></textarea>
                <div class="submitOptions" style="margin-top: 10px">
                    <button onclick="doEditAdviserQuestion()" class="btn btn-success">تایید</button>
                    <input type="submit" onclick="$('.dark').addClass('hidden'); $('#editAdviserQuestionContainer').addClass('hidden')" value="خیر" class="btn btn-default">
                    <p id="err2" class="errorText"></p>
                </div>
            </div>
    </span>

@stop