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
    <div class="title">شهر ها
    </div>
@stop

@section('main')
    <center class="myRegister">
        <div class="row data">

            <form method="post" action="{{route('deleteCity')}}">
                {{csrf_field()}}
                <div class="col-xs-12" style="margin-top: 10px">
                    <table>
                        @foreach($cities as $city)
                            <tr>
                                <td>
                                    <span> {{$city->name}} </span>
                                    <span> در </span>
                                    <span> {{$city->stateId}} </span>
                                </td>
                                <td>
                                    <button name="cityId" value="{{$city->id}}" class="btn btn-danger">
                                        <span class="glyphicon glyphicon-remove" style="margin-left: 30%"></span>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </table>

                    {{ $cities->links()  }}
                </div>
            </form>

            <div class="col-xs-12" style="margin-top: 10px">
                <button class="btn btn-default circleBtn" data-toggle="tooltip" title="افزودن شهر جدید" onclick="addCity()">
                    <span class="glyphicon glyphicon-plus" style="margin-left: 30%"></span>
                </button>
                <button class="btn btn-default" data-toggle="tooltip" title="افزودن دسته ای شهر ها" onclick="showElement('addBatch')">
                    افزودن دسته ای شهر ها
                </button>
            </div>
        </div>
    </center>

    <span id="newCityContainer" class="ui_overlay item" style="visibility: hidden; position: fixed; left: 40%; right: auto; top: 174px; bottom: auto">
        <div class="header_text">شهر جدید</div>
        <div onclick="hideElement('newCityContainer')" class="ui_close_x"></div>
            <div class="body_text">

                <div class="col-xs-12">
                    <label>
                        <span>نام شهر</span>
                        <input type="text" id="cityName" maxlength="50" autofocus>
                    </label>
                </div>

                <div class="col-xs-12">
                    <label>
                        <span>استان</span>
                        <select id="states"></select>
                    </label>
                </div>



                <div class="submitOptions" style="margin-top: 10px">
                    <button onclick="doAddNewCity()" class="btn btn-success">تایید</button>
                    <input type="submit" onclick="hideElement('newCityContainer')" value="خیر" class="btn btn-default">
                    <p id="msg" class="errorText"></p>
                </div>
            </div>
    </span>

    <span id="addBatch" class="ui_overlay item" style="visibility: hidden; position: fixed; left: 40%; right: auto; top: 174px; bottom: auto">
        <div class="header_text">افزودن دسته ای شهر های جدید</div>
        <div onclick="hideElement('addBatch')" class="ui_close_x"></div>
            <div class="body_text">
                <form method="post" action="{{route('addCityBatch')}}" enctype="multipart/form-data">
                    {{csrf_field()}}
                    <input type="file" name="cities">
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