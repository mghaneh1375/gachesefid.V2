@extends('layouts.form')

@section('head')
    @parent

    <script>
        var getEventDir = '{{route('getEvents')}}';
        var addEventDir = '{{route('addEvent')}}';
        var deleteEventDir = '{{route('deleteEvent')}}';
    </script>

    <script src="{{URL::asset('js/jsNeededForCalender.js')}}"></script>
    <script src = {{URL::asset("js/calendar.js") }}></script>
    <script src = {{URL::asset("js/calendar-setup.js") }}></script>
    <script src = {{URL::asset("js/calendar-fa.js") }}></script>
    <script src = {{URL::asset("js/jalali.js") }}></script>
    <link rel="stylesheet" href = {{URL::asset("css/calendar-green.css") }}>
@stop

@section('main')
    <center class="row">
        <div class="col-xs-12">
            <label>
                <span>تاریخ مورد نظر</span>
                <input type="button" style="border: none; width: 30px; height: 30px; background: url({{ URL::asset('images/calendar-flat.png') }}) repeat 0 0; background-size: 100% 100%;" id="date_btn">
                <br/>
                <input onchange="getEvents(this.value)" type="text" id="date_input" readonly>
                <script>
                    Calendar.setup({
                        inputField: "date_input",
                        button: "date_btn",
                        ifFormat: "%Y/%m/%d",
                        dateType: "jalali"
                    });
                </script>
            </label>
        </div>
        <div class="col-xs-12">
            <label>
                <h3 style="font-family: IRANSans">رویداد ها</h3>
                <div id="events"></div>
            </label>
        </div>
    </center>

    <span id="calenderContainer" class="ui_overlay" style="visibility: hidden; position: fixed; left: 40%; right: auto; top: 174px; bottom: auto">
        <div class="header_text">رویداد جدید</div>
        <div onclick="closeEventPrompt()" class="ui_close_x"></div>
            <div class="body_text">
                <textarea maxlength="1000" style="direction: rtl; width: 300px; height: 300px;" placeholder="حداکثر 1000 کاراکتر" id="eventDesc"></textarea>
                <div class="submitOptions">
                    <button onclick="addEvent()" class="btn btn-success">تایید</button>
                    <input type="submit" onclick="closeEventPrompt()" value="خیر" class="btn btn-default">
                </div>
            </div>
    </span>
@stop