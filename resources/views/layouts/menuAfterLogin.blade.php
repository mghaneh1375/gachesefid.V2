
<header class="header" data-pinned="swingInX" data-unpinned="swingOutX" id="site-header" style="font-family: IRANSans !important;">
    <div class="container">
        <div class="header-content-wrapper">
            <div class="logo">
                <a href="{{route('home')}}" class="full-block-link" rel="home"></a>
                <img src="{{URL::asset('images/Logo-Gach.png')}}" alt="گچ سفید - سامانه آزمون و رقابت هوشمند" style="width:94px; height:50px;"/>
            </div>

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

                <ul id="primary-menu" class="primary-menu-menu">

                    <li id="menu-item-1981" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children menu-item-has-icon menu-item-1981"><a href="#" ><i class="menu-item-icon fa fa-group" ></i>آزمون ها</a>
                        <ul class="sub-menu sub-menu-has-icons">
                            <li id="menu-item-1982" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-icon menu-item-1982"><a href="{{route('myQuizes')}}" ><i class="menu-item-icon fa fa-check-square-o" ></i>آزمون های من<i
                                            class="seoicon-right-arrow"
                                    ></i></a></li>
                            <li id="menu-item-1983" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-icon menu-item-1983"><a href="{{route('seeResult')}}" ><i class="menu-item-icon fa fa-edit" ></i>کارنامه آزمون<i class="seoicon-right-arrow"
                                    ></i></a></li>
                            <li id="menu-item-1986" class="menu-item menu-item-type-custom menu-item-object-custom
                            menu-item-has-icon menu-item-1986"><a href="{{route('quizRegistry')}}" ><i class="menu-item-icon fa fa-edit" ></i>ثبت نام در سنجش پای تخته!<i class="seoicon-right-arrow"
                                    ></i></a></li>
                            <li id="menu-item-1987" class="menu-item menu-item-type-custom menu-item-object-custom
                            menu-item-has-icon menu-item-1987"><a href="{{route('regularQuizRegistry')}}" ><i class="menu-item-icon fa fa-edit" ></i>ثبت نام در سنجش پشت میز!<i class="seoicon-right-arrow"
                                    ></i></a></li>

                            <li id="menu-item-1988" class="menu-item menu-item-type-custom menu-item-object-custom
                            menu-item-has-icon menu-item-1988"><a href="{{route('message')}}" ><i class="menu-item-icon fa fa-edit" ></i>ساخت آزمون جدید<i class="seoicon-right-arrow"
                                    ></i></a>
                            </li>

                        </ul>
                    </li>

                    <li id="menu-item-1989" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-home current-menu-item page_item page-item-507 current_page_item menu-item-has-icon menu-item-1989"><a href="{{route('ranking1')}}" ><i class="menu-item-icon fa fa-home" ></i>رتبه بندی آزمون ها</a>
                    </li>

                    <li id="menu-item-1989" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-home current-menu-item page_item page-item-507 current_page_item menu-item-has-icon menu-item-1989"><a href="{{route('schoolsList')}}" ><i class="menu-item-icon fa fa-home" ></i>لیست مدارس</a>
                    </li>

                    <li id="menu-item-254" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children menu-item-has-icon menu-item-254">
                        <a href="#"><i class="menu-item-icon fa fa-group" ></i>رتبه بندی</a>
                        <ul class="sub-menu sub-menu-has-icons">
                            <li id="menu-item-1954" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-1954"><a href="{{route('studentsRanking')}}" >دانش آموزان<i class="seoicon-right-arrow" ></i></a></li>
                            <li id="menu-item-1975" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-1975"><a href="{{route('myAdviser')}}" >مشاوران<i class="seoicon-right-arrow" ></i></a></li>
                        </ul>
                    </li>

                    <li id="menu-item-1991" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-home current-menu-item page_item page-item-507 current_page_item menu-item-has-icon menu-item-1991"><a href="{{route('myAdviser')}}" ><i class="menu-item-icon fa fa-home" ></i>مشاور من</a>
                    </li>

                    <li id="menu-item-1990" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-home current-menu-item page_item page-item-507 current_page_item menu-item-has-icon menu-item-1990"><a href="{{route('schoolsList')}}" ><i class="menu-item-icon fa fa-home" ></i>صندوق پیام ها</a>
                    </li>


                    <li id="menu-item-1992" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children menu-item-has-icon menu-item-1992">
                        <a href="{{route('profile')}}"><i class="menu-item-icon fa fa-group" ></i>پروفایل</a>
                        <ul class="sub-menu sub-menu-has-icons">
                            <li id="menu-item-1954" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-1954"><a href="{{route('userInfo')}}" >تغییر اطلاعات کاربری<i class="seoicon-right-arrow" ></i></a></li>
                            <li id="menu-item-1975" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-1975"><a href="{{route('changePas')}}" >تغییر رمزعبور<i class="seoicon-right-arrow" ></i></a></li>
                        </ul>
                    </li>

                    <li id="menu-item-2021" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-home current-menu-item page_item page-item-507 current_page_item menu-item-has-icon menu-item-2021"><a href="{{route('logout')}}" ><i class="menu-item-icon fa fa-sign-out" ></i>خروج</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</header>
<div id="header-spacer" class="header-spacer"></div>
