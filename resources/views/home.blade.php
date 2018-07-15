<!DOCTYPE html>
<html lang="en">
<head>
    @include('layouts.commonLibraries')

    <link rel="stylesheet" href="{{URL::asset('css/homeCSS.css')}}">
    <link rel="stylesheet" href="{{URL::asset('css/mobileMenuCSS.css')}}">
    <script src="{{URL::asset('js/jsNeededForCalender.js')}}"></script>
    <script src = {{URL::asset("js/calendar2.js") }}></script>
    <script src = {{URL::asset("js/calendar-setup2.js") }}></script>
    <script src = {{URL::asset("js/calendar-fa.js") }}></script>
    <script src = {{URL::asset("js/jalali.js") }}></script>
    <script src = {{URL::asset("js/mobileMenu.js") }}></script>
    <link rel="stylesheet" href = {{URL::asset("css/calendar-green2.css") }}>

    <script>

        var getEvents = '{{route('getEvents')}}';
        var selectedPage = 1;
        var windowHeight = window.innerHeight;
        var qNos = '{{$qNos}}';
        var usersNo = '{{$usersNo}}';
        var quizNo = '{{$quizNo}}';
        var adviserNos = '{{$adviserNos}}';
        var sliders = '{{count($sliders)}}';

        var timeVal1, timeVal2, timeVal3, timeVal4;

        $(document).ready(function () {

            changeSelectedPageScrollItem();
            showSlideBar();

            timeVal1 = Math.floor(1000 / usersNo);
            countUserNos(-1);

            timeVal2 = Math.floor(1000 / qNos);
            countQNos(-1);

            timeVal3 = Math.floor(1000 / quizNo);
            countQuizNos(-1);

            timeVal4 = Math.floor(1000 / adviserNos);
            countAdviserNos(-1);

            $.ajax({
                type: 'post',
                url: "{{route('showRSSGach')}}",
                success: function (response) {
                    $("#rss").append(response).persiaNumber();
                }
            });

        });

        function changeSelectedPageScrollItem() {
            $(".scrollItem").css('width', '14px').css('height', '14px').css('background-color', 'rgba(114, 112, 115, 1)');
            $("#menu_" + selectedPage).css('width', '10px').css('height', '10px').css('background-color', 'gray');
        }

        function showEvent(val) {

            $.ajax({
                type: 'post',
                url: getEvents,
                data: {
                    'date' : val
                },
                success: function (response) {

                    $("#events").empty();

                    response = JSON.parse(response);

                    newElement = "";

                    if(response.length == 0)
                        newElement = "رویدادی موجود نیست";
                    else {
                        for(i = 0; i < response.length; i++){
                            newElement += "<p>" + response[i].event + "</p>";
                        }
                    }

                    $("#events").append(newElement);

                }
            });
        }

        function countAdviserNos(idx) {

            if(idx == adviserNos)
                return;

            if(idx + 10 < adviserNos) {
                idx += 10;
            }
            else
                idx++;

            $("#adviserNos").empty().append(idx).persiaNumber();

            setTimeout("countAdviserNos(" + idx + ")", timeVal4);
        }

        function countQuizNos(idx) {

            if(idx == quizNo)
                return;

            if(idx + 10 < quizNo) {
                idx += 10;
            }
            else
                idx++;

            $("#quizNos").empty().append(idx).persiaNumber();

            setTimeout("countQuizNos(" + idx + ")", timeVal3);
        }

        function countQNos(idx) {

            if(idx == qNos)
                return;

            if(idx + 10 < qNos) {
                idx += 10;
            }
            else
                idx++;

            $("#qNos").empty().append(idx).persiaNumber();

            setTimeout("countQNos(" + idx + ")", timeVal2);
        }

        function countUserNos(idx) {

            if(idx >= usersNo)
                return;

            if(idx + 10 < usersNo) {
                idx += 10;
            }
            else
                idx++;

            $("#usersNo").empty().append(idx).persiaNumber();

            setTimeout("countUserNos(" + idx + ")", timeVal1);
        }

    </script>

    <style>
        .wholePage {
            margin-right: -20%;
            height: 100vh;
        }

        .scrollBar {
            position: fixed;
            left: 2%;
            top: 40%;
            width: 30px;
            z-index: 100;
        }

        .scrollItem {
            background-color: rgba(114, 112, 115, 1);
            width: 14px;
            height: 14px;
            border-radius: 100%;
            cursor: pointer;
            margin-top: 6px;
        }

    </style>

    <style>
        @media only screen and (max-width:1000px) and (min-width:767px){
            .hideOn1000 {
                display: none;
            }
        }
    </style>
</head>

<body style="font-family: IRANSans">

    {{--<div class="scrollBar hiddenOnMobile">--}}
        {{--<div data-val="1" class="scrollItem" id="menu_1"></div>--}}
        {{--<div data-val="2" class="scrollItem" id="menu_2"></div>--}}
        {{--<div data-val="3" class="scrollItem" id="menu_3"></div>--}}
    {{--</div>--}}


    <body class="rtl home page-template-default page page-id-507 kingcomposer kc-css-system _masterslider _msp_version_3.2.2 footer-widgets crumina-grid">


    @if(Auth::check())
        <?php
        $level = Auth::user()->level;
        ?>

        @if($level == getValueInfo('adminLevel') || $level == getValueInfo('superAdminLevel'))
            @include('layouts.menuAfterLoginSuperAdmin')
        @elseif($level == getValueInfo('namayandeLevel'))
            @include('layouts.menuAfterLoginNamayande')
        @elseif($level == getValueInfo('adviserLevel'))
            @include('layouts.menuAfterLoginAdviser')
        @elseif($level == getValueInfo('studentLevel'))
            @include('layouts.menuAfterLogin')
        @elseif($level == getValueInfo('operator2Level'))
            @include('layouts.menuAfterLoginOperator2')
        @elseif($level == getValueInfo('operator1Level'))
            @include('layouts.menuAfterLoginOperator2')
        @elseif($level == getValueInfo('controllerLevel'))
            @include('layouts.menuAfterLoginController')
        @elseif($level == getValueInfo('schoolLevel'))
            @include('layouts.menuAfterLoginSchool')
        @else
            @include('layouts.preLoginMenu')
        @endif
    @else
        @include('layouts.preLoginMenu')
    @endif

    <Div class="row">

        <div class="col-xs-12 hiddenOnScreenMain topTitle">
            <p style="position: absolute; left: 35%; margin-top: 5px; font-size: 32px; font-family: ghasem !important;">گچ سفید</p>
            <img onclick="toggleMenu()" src="{{URL::asset('images/menuIcon.png')}}" style="cursor: pointer; float: right; margin-top: 10px" width="50px">
        </div>

        <div class="col-xs-12 hiddenOnScreenMain">
            <div id="mobileMenuBar" class="hidden">

                @if(Auth::check())
                    <?php
                    $level = Auth::user()->level;
                    ?>

                    @if($level == getValueInfo('adminLevel') || $level == getValueInfo('superAdminLevel'))
                        @include('layouts.menuAfterLoginSuperAdmin')
                    @elseif($level == getValueInfo('adviserLevel'))
                        @include('layouts.menuAfterLoginAdviser')
                    @elseif($level == getValueInfo('studentLevel'))
                        @include('layouts.menuAfterLoginMobile')
                    @elseif($level == getValueInfo('operator2Level'))
                        @include('layouts.menuAfterLoginOperator2')
                    @elseif($level == getValueInfo('operator1Level'))
                        @include('layouts.menuAfterLoginOperator2')
                    @elseif($level == getValueInfo('controllerLevel'))
                        @include('layouts.menuAfterLoginController')
                    @else
                        @include('layouts.preLoginMenuMobile')
                    @endif
                @else
                    @include('layouts.preLoginMenuMobile')
                @endif
            </div>
        </div>

        <div class="col-xs-12" onclick="$('#mobileMenuBar').addClass('hidden');">
            <div class="wholePage" style="background-color: white">
                <div style="margin-right: 20%">
                    @include('layouts.slideBar')
                    <div class="col-xs-12" style="margin-top: 20px">
                        <div class="col-md-3 col-xs-12" style="height: 150px">
                            <div style="float: right">
                                <img style="width: 150px; margin: 10px" src="{{URL::asset('images/teacher.png')}}">
                                <center style="font-weight: bolder; color: white; margin-top: -85px; font-size: 30px" id="adviserNos"></center>
                            </div>
                        </div>
                        <div class="col-md-3 col-xs-12" style="margin-top: 10px; height: 150px">
                            <div style="float: right">
                                <img style="width: 150px; margin: 10px" src="{{URL::asset('images/exam.png')}}">
                                <center style="font-weight: bolder; margin-top: -85px; color: white; font-size: 30px" id="quizNos"></center>
                            </div>
                        </div>
                        <div class="col-md-3 col-xs-12" style="margin-top: 10px; height: 150px">
                            <div style="float: right">
                                <img style="width: 150px; margin: 10px" src="{{URL::asset('images/blackboard.png')}}">
                                <center style="font-weight: bolder; color: white; margin-top: -85px; font-size: 30px" id="qNos"></center>
                            </div>
                        </div>
                        <div class="col-md-3 col-xs-12" style="margin-top: 10px; height: 150px">
                            <div style="float: right">
                                <img style="width: 150px; margin: 10px" src="{{URL::asset('images/student.png')}}">
                                <center style="font-weight: bolder; margin-top: -85px; color: white; font-size: 28px" id="usersNo"></center>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="wholePage hidden" style="background-color: #3498db" id="wholePage2">
                <div style="margin-right: 20%">
                    <div class="col-xs-12" style="margin-top: 50px">
                        <div class="col-md-1 hiddenOnMobile"></div>
                        <center class="col-md-3 col-xs-12 homeBox" style="background: url('{{URL::asset('images/GACH-Caracter-WEB-1.png')}}'); background-repeat: round">
                            <div style="height: 200px"></div>
                        </center>
                        <div class="col-md-1 hiddenOnMobile"></div>
                        <center class="col-md-3 col-xs-12 homeBox" style="background: url('{{URL::asset('images/GACH-Caracter-WEB-2.png')}}'); background-repeat: round">
                            <div style="height: 200px">
                            </div>
                        </center>
                        <div class="col-md-1 hiddenOnMobile"></div>
                        <center class="col-md-3 col-xs-12 homeBox" style="background: url('{{URL::asset('images/GACH-Caracter-WEB-3.png')}}'); background-repeat: round">
                            <div style="height: 200px"></div>
                        </center>
                    </div>
                </div>
                <div style="margin-right: 20%; margin-top: 100px">
                    <div class="col-xs-12" style="margin-top: 50px">
                        <div class="col-md-1 hiddenOnMobile"></div>
                        <center class="col-md-3 col-xs-12 homeBox" style="background: url('{{URL::asset('images/GACH-Caracter-WEB-4.png')}}'); background-repeat: round">
                            <div style="height: 200px">
                            </div>
                        </center>
                        <div class="col-md-1 hiddenOnMobile"></div>
                        <center class="col-md-3 col-xs-12 homeBox" style="background: url('{{URL::asset('images/GACH-Caracter-WEB-5.png')}}'); background-repeat: round">
                            <div style="height: 200px">
                            </div>
                        </center>
                        <div class="col-md-1 hiddenOnMobile"></div>
                        <center class="col-md-3 col-xs-12 homeBox" style="background: url('{{URL::asset('images/GACH-Caracter-WEB-6.png')}}'); background-repeat: round">
                            <div style="height: 200px">
                            </div>
                        </center>
                    </div>
                </div>
            </div>

            <div class="wholePage hidden" style="background-color: #e67e22" id="wholePage3">
                <div style="margin-right: 20%">
                    <div class="calender">

                        <div class="col-md-1 hiddenOnMobile"></div>

                        <div class="col-xs-12 col-md-4">
                            <center>
                                <h3 style="font-family: IRANSans; text-align: center;">رویداد ها</h3>
                            </center>
                            <div id="events" style="direction: rtl"></div>
                        </div>

                        <div class="col-md-1 hiddenOnMobile"></div>

                        <div class="col-xs-12 col-md-6" id="calendar-container">
                        </div>

                    </div>
                </div>
            </div>


            <script>
                Calendar.setup({
                    displayArea: "calendar-container",
                    flat: "calendar-container",
                    autoShowOnFocus: true,
                    ifFormat: "%Y/%m/%d",
                    dateType: "jalali",
                    onClose: function () {}
                });
            </script>

        </div>
    </Div>

    <script>
        $(".scrollItem").mouseenter(function () {
            if($(this).attr('data-val') != selectedPage)
                $(this).css('width', '10px').css('height', '10px').css('background-color', 'gray');
        });

        $(".scrollItem").mouseleave(function () {
            if($(this).attr('data-val') != selectedPage)
                $(this).css('width', '14px').css('height', '14px').css('background-color', 'rgba(114, 112, 115, 1)');
        });

        $('#menu_1').click(function () {
            if(1 != selectedPage) {
                $('body,html').animate({ scrollTop: 0 }, 800);
                selectedPage = 1;
                changeSelectedPageScrollItem();
            }
        });

        $('#menu_2').click(function () {
            if(2 != selectedPage) {
                $('body,html').animate({ scrollTop: windowHeight }, 800);
                selectedPage = 2;
                changeSelectedPageScrollItem();
            }
        });
        $('#menu_3').click(function () {
            if(3 != selectedPage) {
                selectedPage = 3;
                $('body,html').animate({ scrollTop: windowHeight * 2 }, 800);
                changeSelectedPageScrollItem();
            }
        });

        $(document.body).on("mousewheel", function() {
            selectedPage = Math.floor($(document).scrollTop() / windowHeight) + 1;
            changeSelectedPageScrollItem();
        });
    </script>
</body>

@include('layouts.footer2')

</html>

