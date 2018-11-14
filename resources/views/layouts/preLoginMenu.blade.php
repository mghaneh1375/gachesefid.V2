@extends('layouts.menu.structure')

@section('items')


    <li id="menu-item-1981" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children menu-item-has-icon menu-item-1981"><a href="#" ><i class="menu-item-icon fa fa-group" ></i>کاربران</a>
        <ul class="sub-menu sub-menu-has-icons">
            <li id="menu-item-1982" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-icon menu-item-1982"><a href="{{route('login')}}" ><i class="menu-item-icon fa fa-edit"></i>ورود<i
                            class="seoicon-right-arrow"
                    ></i></a></li>
            <li id="menu-item-1983" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-icon menu-item-1983"><a href="{{route('registration')}}" ><i class="menu-item-icon fa fa-edit" ></i>ثبت‌نام<i class="seoicon-right-arrow"
                    ></i></a></li>

            <li id="menu-item-19812" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-icon menu-item-1983"><a href="{{route('getActivation')}}" ><i class="menu-item-icon fa fa-edit" ></i>دریافت مجدد کد فعال‌سازی<i class="seoicon-right-arrow"
                    ></i></a></li>
        </ul>
    </li>

    <li id="menu-item-119970" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children menu-item-has-icon menu-item-1970">
        <a href="#" ><i class="menu-item-icon fa fa-stack-overflow" ></i>آمار و اطلاعات</a>
        <ul class="sub-menu sub-menu-has-icons">
            <li id="menu-item-1954" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-1954"><a href="http://exam.gachesefid.com/ranking1" ><i class="menu-item-icon fa fa-edit" ></i>رتبه‌بندی آزمون‌ها<i class="seoicon-right-arrow" ></i></a></li>
            <li id="menu-item-1975" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-1975"><a href="http://exam.gachesefid.com/schoolsList" ><i class="menu-item-icon fa fa-edit" ></i>لیست کل مدارس همکار<i class="seoicon-right-arrow" ></i></a></li>
            <li id="menu-item-1976" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-1976"><a href="https://gachesefid.com/irysc-olympiad/"><i class="menu-item-icon fa fa-edit" ></i>شبیه ساز المپیاد آیریسک<i class="seoicon-right-arrow" ></i></a></li>
        </ul>

    </li>


    <li id="menu-item-1970" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children menu-item-has-icon menu-item-1970">

        <a href="#" ><i class="menu-item-icon fa fa-stack-overflow" ></i>راهنما</a>

        <ul class="sub-menu sub-menu-has-icons">
            <li id="menu-item-1974" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1974">
                <a href="http://gachesefid.com/%d8%b1%d8%a7%d9%87%d9%86%d9%85%d8%a7%db%8c-%d8%af%d8%a7%d9%86%d8%b4%e2%80%8c%d8%a2%d9%85%d9%88%d8%b2%d8%a7%d9%86/" >
                    <i class="menu-item-icon fa fa-edit"></i>
                    راهنمای دانش‌آموزان
                    <i class="seoicon-right-arrow"></i>
                </a>
                <div class="megamenu-item-info-text"></div>
            </li>

            <li id="menu-item-1971" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1971">
                <a href="http://gachesefid.com/%d8%b1%d8%a7%d9%87%d9%86%d9%85%d8%a7%db%8c-%d9%85%d8%b4%d8%a7%d9%88%d8%b1%d8%a7%d9%86/">
                    <i class="menu-item-icon fa fa-edit"></i>
                    راهنمای مشاوران
                    <i class="seoicon-right-arrow"></i>
                </a>
                <div class="megamenu-item-info-text"> </div>
            </li>
            <li id="menu-item-1973" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1973">
                <a href="http://gachesefid.com/%d8%b1%d8%a7%d9%87%d9%86%d9%85%d8%a7%db%8c-%d9%85%d8%af%db%8c%d8%b1%d8%a7%d9%86-%d9%85%d8%af%d8%a7%d8%b1%d8%b3/" >
                    <i class="menu-item-icon fa fa-edit"></i>
                    راهنمای مدیران مدارس
                    <i class="seoicon-right-arrow"></i>
                </a>
                <div class="megamenu-item-info-text"> </div>
            </li>

            <li id="menu-item-1972" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1972">
                <a href="http://gachesefid.com/%d8%b1%d8%a7%d9%87%d9%86%d9%85%d8%a7%db%8c-%d9%86%d9%85%d8%a7%db%8c%d9%86%d8%af%da%af%db%8c%e2%80%8c%d9%87%d8%a7/" >
                    <i class="menu-item-icon fa fa-edit"></i>
                    راهنمای نمایندگی‌ها
                    <i class="seoicon-right-arrow" ></i>
                </a>
                <div class="megamenu-item-info-text" > </div>
            </li>
        </ul>
    </li>

    <li id="menu-item-457" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-has-icon menu-item-457">
        <a href="http://gachesefid.com/news/" >
            <i class="menu-item-icon seosight seosight-targeting" ></i>
            اخبار
        </a>
    </li>
    <li id="menu-item-506" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-has-icon menu-item-506">
        <a href="http://gachesefid.com/contact-information/" >
            <i class="menu-item-icon seosight seosight-mail-send" ></i>تماس با ما
        </a>
    </li>

@stop
