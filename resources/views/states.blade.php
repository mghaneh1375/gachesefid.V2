@extends('layouts.form')

@section('head')
    @parent

    <script>
        var states = '{{route('states')}}';
        var addState = '{{route('addState')}}';
    </script>

    <script src="{{URL::asset('js/jsNeededForState.js')}}"></script>
@stop


@section('caption')
    <div class="title">استان ها
    </div>
@stop

@section('main')
    <center class="myRegister">
        <div class="row data">
            
            <form method="post" action="{{route('deleteState')}}">
                @foreach($states as $state)
                    <div class="col-xs-12" style="margin-top: 10px">
                        <span>{{$state->name}}</span>
                        <button name="stateId" value="{{$state->id}}" class="btn btn-danger">
                            <span class="glyphicon glyphicon-remove" style="margin-left: 30%"></span>
                        </button>
                    </div>
                @endforeach
            </form>

            <div class="col-xs-12" style="margin-top: 10px">
                <button class="btn btn-default circleBtn" data-toggle="tooltip" title="افزودن استان جدید" onclick="showElement('newStateContainer')">
                    <span class="glyphicon glyphicon-plus" style="margin-left: 30%"></span>
                </button>
                <button class="btn btn-default" data-toggle="tooltip" title="افزودن دسته ای استان ها" onclick="showElement('addBatch')">
                    افزودن دسته ای استان ها
                </button>
            </div>
        </div>
    </center>

    <span id="newStateContainer" class="ui_overlay item" style="visibility: hidden; position: fixed; left: 40%; right: auto; top: 174px; bottom: auto">
        <div class="header_text">استان جدید</div>
        <div onclick="hideElement('newStateContainer')" class="ui_close_x"></div>
            <div class="body_text">
                <input type="text" id="stateName" maxlength="50" autofocus>
                <div class="submitOptions" style="margin-top: 10px">
                    <button onclick="doAddNewState()" class="btn btn-success">تایید</button>
                    <input type="submit" onclick="hideElement('newStateContainer')" value="خیر" class="btn btn-default">
                    <p id="msg" class="errorText"></p>
                </div>
            </div>
    </span>

    <span id="addBatch" class="ui_overlay item" style="visibility: hidden; position: fixed; left: 40%; right: auto; top: 174px; bottom: auto">
        <div class="header_text">افزودن دسته ای استان های جدید</div>
        <div onclick="hideElement('addBatch')" class="ui_close_x"></div>
            <div class="body_text">
                <form method="post" action="{{route('addStateBatch')}}" enctype="multipart/form-data">
                    <input type="file" name="states">
                    <div class="submitOptions" style="margin-top: 10px">
                        <button name="submitBtn" class="btn btn-success">تایید</button>
                        <p class="errorText">
                            {{$err}}
                        </p>
                    </div>
                </form>
            </div>
    </span>

    @if(!empty($err))
        <script>
            showElement('addBatch');
        </script>
    @endif

@stop