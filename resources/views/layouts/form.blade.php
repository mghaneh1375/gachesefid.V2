<!DOCTYPE html>
<html lang="en">
<head>
    @section('head')
        @include('layouts.commonLibraries')
    @show

    <style>
        @media only screen and (max-width:1200px) and (min-width:767px){
            .hideOn1200 {
                display: none;
            }
        }
        .primary-menu {
            float: right !important;
        }
    </style>

</head>

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

<div class="col-xs-12 hiddenOnScreen">
    <div id="mobileMenuBar" class="hidden">

        @if(Auth::check())
            <?php
            $level = Auth::user()->level;
            ?>

            @if($level == getValueInfo('adminLevel') || $level == getValueInfo('superAdminLevel'))
                @include('layouts.menuAfterLoginSuperAdminMobile')
            @elseif($level == getValueInfo('namayandeLevel'))
                @include('layouts.menuAfterLoginNamayandeMobile')
            @elseif($level == getValueInfo('adviserLevel'))
                @include('layouts.menuAfterLoginAdviserMobile')
            @elseif($level == getValueInfo('studentLevel'))
                @include('layouts.menuAfterLoginMobile')
            @elseif($level == getValueInfo('schoolLevel'))
                @include('layouts.menuAfterLoginSchoolMobile')
            @elseif($level == getValueInfo('operator2Level'))
                {{--                            @include('layouts.menuAfterLoginOperator2Mobile')--}}
            @elseif($level == getValueInfo('operator1Level'))
                {{--@include('layouts.menuAfterLoginOperator2Mobile')--}}
            @elseif($level == getValueInfo('controllerLevel'))
                {{--                            @include('layouts.menuAfterLoginControllerMobile')--}}
            @else
                @include('layouts.preLoginMenuMobile')
            @endif
        @else
            @include('layouts.preLoginMenuMobile')
        @endif
    </div>
</div>

<body>

    <div class="dark hidden" style="position: absolute; left: 0; top: 0; z-index: 10000; width: 100%; height: 100%; background-color: rgba(120, 119, 120, 0.62)"></div>

    <div class="_MAIN_" style="min-height: 90vh">
        <div class="row">

            <div class="col-xs-12 hiddenOnScreen topTitle">
                <p style="position: absolute; left: 35%; margin-top: 5px; font-size: 32px; font-family: ghasem !important;">گچ سفید</p>
                <img onclick="toggleMenu()" src="{{URL::asset('images/menuIcon.png')}}" style="cursor: pointer; float: right; margin-top: 10px" width="50px">
            </div>

            <div style="margin-top: 50px" class="col-xs-12" onclick="$('#mobileMenuBar').addClass('hidden');">

                <div class="col-xs-1 hideOn1200">
                    @section('sideBar')
                    @show
                </div>

                <div class="col-xs-10 fillWidthOnMobile">

                    <div id="warning" class="hidden" style="margin-top: 100px">
                        <center><p class="errorText">برای نمایش بهتر گوشی خود را در حالت افقی قرار دهید</p></center>
                    </div>

                    <div>

                        @yield('caption')

                        @yield('main')
                    </div>
                </div>

                <div class="col-xs-1 hideOn1200">
                    @section('sideBar')
                    @show
                </div>

            </div>
        </div>
    </div>

</body>
@include('layouts.footer2')
</html>
