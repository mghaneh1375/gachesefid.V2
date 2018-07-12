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
    </style>

</head>


<header class="header hiddenOnMobile" id="site-header"
        data-pinned="swingInX"
        data-unpinned="swingOutX">
    <div class="container">
        <div class="header-content-wrapper">
            <div class="logo">
                <a href="http://uitest.gachesefid.com" class="full-block-link" rel="home"></a><img src="//uitest.gachesefid.com/wp-content/uploads/2018/07/Logo-Gach-1-e1531226722605.png" alt="گچ سفید - سامانه آزمون و رقابت هوشمند" style="width:94px; height:50px;"/>			</div>

            <nav class="primary-menu">

                <!-- menu-icon-wrapper -->
                <a href='javascript:void(0)' id="menu-icon-trigger" class="menu-icon-trigger showhide">
                    <span class="mob-menu--title">فهرست</span>
					<span id="menu-icon-wrapper" class="menu-icon-wrapper">
                            <svg width="1000px" height="1000px">
                                <path id="pathD"
                                      d="M 300 400 L 700 400 C 900 400 900 750 600 850 A 400 400 0 0 1 200 200 L 800 800"></path>
                                <path id="pathE" d="M 300 500 L 700 500"></path>
                                <path id="pathF"
                                      d="M 700 600 L 300 600 C 100 600 100 200 400 150 A 400 380 0 1 1 200 800 L 800 200"></path>
                            </svg>
                        </span>
                </a>

                <ul id="primary-menu" class="primary-menu-menu"><li id="menu-item-578" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-home current-menu-item page_item page-item-507 current_page_item menu-item-has-icon menu-item-578"><a href="http://uitest.gachesefid.com/" ><i class="menu-item-icon fa fa-home" ></i>صفحه نخست</a></li>
                    <li id="menu-item-1981" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children menu-item-has-icon menu-item-1981"><a href="#" ><i class="menu-item-icon fa fa-group" ></i>کاربران</a>
                        <ul class="sub-menu sub-menu-has-icons">
                            <li id="menu-item-1982" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-icon menu-item-1982"><a href="http://exam.gachesefid.com/login" ><i class="menu-item-icon fa fa-check-square-o" ></i>ورود<i class="seoicon-right-arrow" ></i></a></li>
                            <li id="menu-item-1983" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-icon menu-item-1983"><a href="http://exam.gachesefid.com/registration" ><i class="menu-item-icon fa fa-edit" ></i>ثبت‌نام<i class="seoicon-right-arrow" ></i></a></li>
                        </ul>
                    </li>
                    <li id="menu-item-254" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children menu-item-has-mega-menu menu-item-has-icon menu-item-254"><a href="#" ><i class="menu-item-icon fa fa-bar-chart-o" ></i>آمار و اطلاعات</a><div class="megamenu" style="" >
                            <ul class="mega-menu-row">
                                <li id="menu-item-1985" class="menu-item menu-item-type-custom menu-item-object-custom mega-menu-col menu-item-1985"><a href="#" >برنامه‌ریزی<i class="seoicon-right-arrow" ></i></a><div class="megamenu-item-info-text" >برنامه‌ریزی سال 97-1398</div></li>
                                <li id="menu-item-1984" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children mega-menu-col menu-item-1984"><a href="#" >آمار گچ سفید<i class="seoicon-right-arrow" ></i></a><div class="megamenu-item-info-text" >اعداد و آمار گچ سفید</div>
                                    <ul class="sub-menu">
                                        <li id="menu-item-1954" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-1954"><a href="http://exam.gachesefid.com/ranking1" >رتبه‌بندی آزمون‌ها<i class="seoicon-right-arrow" ></i></a></li>
                                        <li id="menu-item-1975" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-1975"><a href="http://exam.gachesefid.com/schoolsList" >لیست مدارس همکار<i class="seoicon-right-arrow" ></i></a></li>
                                        <li id="menu-item-1976" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-1976"><a href="#" >عددبازی در گچ<i class="seoicon-right-arrow" ></i></a></li>
                                    </ul>
                                </li>
                            </ul>
                        </div></li>
                    <li id="menu-item-1970" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children menu-item-has-icon menu-item-1970"><a href="#" ><i class="menu-item-icon fa fa-stack-overflow" ></i>راهنما</a>
                        <ul class="sub-menu">
                            <li id="menu-item-1974" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1974"><a href="http://uitest.gachesefid.com/%d8%b1%d8%a7%d9%87%d9%86%d9%85%d8%a7%db%8c-%d8%af%d8%a7%d9%86%d8%b4%e2%80%8c%d8%a2%d9%85%d9%88%d8%b2%d8%a7%d9%86/" >راهنمای دانش‌آموزان<i class="seoicon-right-arrow" ></i></a><div class="megamenu-item-info-text" > </div></li>
                            <li id="menu-item-1971" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1971"><a href="http://uitest.gachesefid.com/%d8%b1%d8%a7%d9%87%d9%86%d9%85%d8%a7%db%8c-%d9%85%d8%b4%d8%a7%d9%88%d8%b1%d8%a7%d9%86/" >راهنمای مشاوران<i class="seoicon-right-arrow" ></i></a><div class="megamenu-item-info-text" > </div></li>
                            <li id="menu-item-1973" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1973"><a href="http://uitest.gachesefid.com/%d8%b1%d8%a7%d9%87%d9%86%d9%85%d8%a7%db%8c-%d9%85%d8%af%db%8c%d8%b1%d8%a7%d9%86-%d9%85%d8%af%d8%a7%d8%b1%d8%b3/" >راهنمای مدیران مدارس<i class="seoicon-right-arrow" ></i></a><div class="megamenu-item-info-text" > </div></li>
                            <li id="menu-item-1972" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1972"><a href="http://uitest.gachesefid.com/%d8%b1%d8%a7%d9%87%d9%86%d9%85%d8%a7%db%8c-%d9%86%d9%85%d8%a7%db%8c%d9%86%d8%af%da%af%db%8c%e2%80%8c%d9%87%d8%a7/" >راهنمای نمایندگی‌ها<i class="seoicon-right-arrow" ></i></a><div class="megamenu-item-info-text" > </div></li>
                        </ul>
                    </li>
                    <li id="menu-item-457" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-has-icon menu-item-457"><a href="http://uitest.gachesefid.com/news/" ><i class="menu-item-icon seosight seosight-targeting" ></i>اخبار</a></li>
                    <li id="menu-item-506" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-has-icon menu-item-506"><a href="http://uitest.gachesefid.com/contact-information/" ><i class="menu-item-icon seosight seosight-mail-send" ></i>تماس با ما</a></li>
                </ul>							<ul class="nav-add">
                    <li class="search search_main"><a href="#" class="js-open-search"><i class="seoicon-loupe"></i></a></li>
                </ul>

            </nav>


        </div>
    </div>
</header>
<div id="header-spacer" class="header-spacer"></div>

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

            <div class="col-xs-12" onclick="$('#mobileMenuBar').addClass('hidden');">

                <div class="col-xs-1 hideOn1200">
                    @section('sideBar')
                    @show
                </div>

                <div class="col-xs-10 fillWidthOnMobile">

                    <div id="warning" class="hidden" style="margin-top: 100px">
                        <center><p class="errorText">برای نمایش بهتر گوشی خود را در حالت افقی قرار دهید</p></center>
                    </div>

                    <div style="margin-top: 50px">

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
