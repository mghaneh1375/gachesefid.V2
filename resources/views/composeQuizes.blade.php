@extends('layouts.form')

@section('head')
    @parent

    <script>
        var cities = '{{route('cities')}}';
        var addCityDir = '{{route('addCity')}}';
        var getStates = '{{route('getStates')}}';
    </script>

    <script src="{{URL::asset('js/jsNeededForCity.js')}}"></script>

    <style>
        td {
            padding: 7px;
        }
    </style>
@stop


@section('caption')
    <div class="title">آزمون های بسته ای
    </div>
@stop

@section('main')

    <style>
        button {
            padding: 5px;
            margin: 10px;
        }

        .btn-info {
            min-width: 250px;
        }
    </style>

    <center class="myRegister">
        <div class="row data">

            @if(count($composeQuizes) > 0)
                <form method="post" action="{{route('removeCompose')}}">
                    {{csrf_field()}}
                    <div class="col-xs-12" style="margin-top: 10px">
                        <table>
                            @foreach($composeQuizes as $quiz)
                                <tr>
                                    <td>
                                        <span> {{$quiz->name}} </span>

                                        <button data-toggle="tooltip" title="حذف بسته" name="composeId" value="{{$quiz->id}}" class="btn btn-danger">
                                            <span class="glyphicon glyphicon-remove" style="margin-left: 30%"></span>
                                        </button>

                                        @if($quiz->items != null && count($quiz->items) > 0)
                                            <span> / </span>
                                            <span> محتویات : </span>
                                            @foreach($quiz->items as $itr)
                                                <p style="margin-right: 5em">
                                                    @if($itr->quizMode == getValueInfo('regularQuiz'))
                                                        <span> سنجش پشت میز </span>
                                                    @elseif($itr->quizMode == getValueInfo('systemQuiz'))
                                                        <span> سنجش پای تخته </span>
                                                    @endif
                                                    <span> {{$itr->quizName}} </span>
                                                    <span onclick="deleteFromPackage('{{$itr->quizId}}', '{{$itr->quizMode}}')" data-toggle="tooltip" title="حذف آزمون از بسته" class="btn btn-warning">
                                                        <span class="glyphicon glyphicon-remove"></span>
                                                    </span>
                                                </p>
                                            @endforeach
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </form>
            @else
                <p class="errorText">آزمونی موجود نیست</p>
            @endif

            <button onclick="$('#createPackagePane').removeClass('hidden'); $('.dark').removeClass('hidden');" class="circleBtn btn btn-primary" data-toggle="tooltip" title="ساخت آزمون بسته ای جدید"><span class="glyphicon glyphicon-plus"></span></button>

            <p class="title">آزمون های موجود</p>

            @foreach($regulars as $quiz)
                <div class="col-xs-12"><button onclick="showPackages('{{$quiz->id}}', '{{getValueInfo('regularQuiz')}}')" class="btn btn-info">{{$quiz->name}}</button></div>
            @endforeach

            @foreach($systems as $quiz)
                <div class="col-xs-12"><button onclick="showPackages('{{$quiz->id}}', '{{getValueInfo('systemQuiz')}}')" class="btn btn-info">{{$quiz->name}}</button></div>
            @endforeach

        </div>
    </center>

    <span id="createPackagePane" class="ui_overlay item hidden" style="position: fixed; left: 30%; width: 40%; right: auto; top: 174px; bottom: auto">
        <div class="header_text">ایجاد بسته جدید</div>
        <div onclick="$('#createPackagePane').addClass('hidden'); $('.dark').addClass('hidden');" class="ui_close_x"></div>
            <div class="body_text">

                <form method="post" action="{{route('addCompose')}}">
                    {{csrf_field()}}
                    <div class="col-xs-12">
                        <label>
                            <span>نام بسته</span>
                            <input type="text" name="name">
                        </label>
                    </div>

                    <div class="submitOptions" style="margin-top: 10px">
                        <input type="submit" value="تایید" class="btn btn-success">
                    </div>
                </form>
            </div>
    </span>

    <span id="packageList" class="ui_overlay hidden" style="position: fixed; left: 30%; width: 40%; right: auto; top: 174px; bottom: auto">
        <div class="header_text">افزودن به بسته ها</div>
        <div onclick="$('#packageList').addClass('hidden'); $('.dark').addClass('hidden');" class="ui_close_x"></div>
            <center class="body_text">

                @foreach($composeQuizes as $itr)
                    <div class="col-xs-12">
                        <label>
                            <span>{{$itr->name}}</span>
                            <input type="radio" id="compose_{{$itr->id}}" name="packages" value="{{$itr->id}}">
                        </label>
                    </div>
                @endforeach

                <div class="submitOptions" style="margin-top: 10px">
                    <button onclick="doAddQuizToPackages()" class="btn btn-success">تایید</button>
                </div>
            </center>
    </span>

    <script>
        
        var selectedQuiz = -1;
        var selectedQuizMode = -1;

        function deleteFromPackage(quizId, quizMode) {

            $.ajax({
                type: 'post',
                url: '{{route('deleteFromPackage')}}',
                data: {
                    'qId': quizId,
                    'quizMode': quizMode
                },
                success: function (response) {

                    if(response == "ok")
                        document.location.href = '{{route('composeQuizes')}}';
                }
            });

        }

        function showPackages(quizId, quizMode) {

            selectedQuiz = quizId;
            selectedQuizMode = quizMode;

            $("input:radio[name=packages]").prop('checked', false);

            $.ajax({
                type: 'post',
                url: '{{route('getComposeListOfQuiz')}}',
                data: {
                    'qId': quizId,
                    'quizMode': quizMode
                },
                success: function (response) {

                    if(response.length > 0) {
                        response = JSON.parse(response);

                        for(i = 0; i < response.length; i++)
                            $("#compose_" + response[i].composeId).prop('checked', true);
                    }

                    $("#packageList").removeClass('hidden');
                    $(".dark").removeClass('hidden');
                }
            });
        }
        
        function doAddQuizToPackages() {

            var selected = -1;

            $("input:radio[name=packages]:checked").each(function () {
                selected = $(this).val();
            });

            if(selected == -1)
                return;

            $.ajax({
                type: 'post',
                url: '{{route('addQuizToCompose')}}',
                data: {
                    'quizId': selectedQuiz,
                    'quizMode': selectedQuizMode,
                    'composeId': selected
                },
                success: function (response) {
                    if(response == "ok")
                        document.location.href = '{{route('composeQuizes')}}';
                    else if(response == "nok")
                        alert("آزمون مورد نظر در بسته ای دیگر موجود است");
                }
            });

            $('#packageList').addClass('hidden');
            $('.dark').addClass('hidden');
        }
        
    </script>
    
@stop