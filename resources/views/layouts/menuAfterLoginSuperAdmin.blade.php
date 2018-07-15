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

                    <li id="menu-item-1981" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children menu-item-has-icon menu-item-1981"><a href="#" ><i class="menu-item-icon fa fa-group" ></i>پیکربندی</a>
                        <ul class="sub-menu sub-menu-has-icons">
                            <li id="menu-item-1982" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-icon menu-item-1982"><a href="{{route('pointsConfig')}}" ><i class="menu-item-icon fa fa-check-square-o" ></i>تعیین امتیازات<i
                                            class="seoicon-right-arrow"
                                    ></i></a></li>
                            <li id="menu-item-1983" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-icon menu-item-1983"><a href="{{route('config')}}" ><i class="menu-item-icon fa fa-edit" ></i>تعیین سایر<i class="seoicon-right-arrow"
                                    ></i></a></li>
                            <li id="menu-item-1986" class="menu-item menu-item-type-custom menu-item-object-custom
                            menu-item-has-icon menu-item-1986"><a href="{{route('defineKarname')}}" ><i class="menu-item-icon fa fa-edit" ></i>تعیین کارنامه<i class="seoicon-right-arrow"
                                    ></i></a></li>
                            <li id="menu-item-1987" class="menu-item menu-item-type-custom menu-item-object-custom
                            menu-item-has-icon menu-item-1987"><a href="{{route('quizStatus')}}" ><i class="menu-item-icon fa fa-edit" ></i>وضعیت آزمون<i class="seoicon-right-arrow"
                                    ></i></a></li>

                            <li id="menu-item-1988" class="menu-item menu-item-type-custom menu-item-object-custom
                            menu-item-has-icon menu-item-1988"><a href="{{route('states')}}" ><i class="menu-item-icon fa fa-edit" ></i>استان ها<i class="seoicon-right-arrow"
                                    ></i></a>
                            </li>

                            <li id="menu-item-2000" class="menu-item menu-item-type-custom menu-item-object-custom
                            menu-item-has-icon menu-item-2000"><a href="{{route('cities')}}" ><i class="menu-item-icon fa fa-edit" ></i>شهر ها<i class="seoicon-right-arrow"
                                    ></i></a>
                            </li>


                            <li id="menu-item-2001" class="menu-item menu-item-type-custom menu-item-object-custom
                            menu-item-has-icon menu-item-2001"><a href="{{route('tags')}}" ><i class="menu-item-icon fa fa-edit" ></i>تگ های به رد سوالات ها<i class="seoicon-right-arrow"
                                    ></i></a>
                            </li>


                            <li id="menu-item-2002" class="menu-item menu-item-type-custom menu-item-object-custom
                            menu-item-has-icon menu-item-2002"><a href="{{route('reportsAccess')}}" ><i class="menu-item-icon fa fa-edit" ></i>مدیریت سطح دسترسی گزارشات<i class="seoicon-right-arrow"
                                    ></i></a>
                            </li>


                            <li id="menu-item-2003" class="menu-item menu-item-type-custom menu-item-object-custom
                            menu-item-has-icon menu-item-2003"><a href="{{route('answer_sheet_templates')}}" ><i class="menu-item-icon fa fa-edit" ></i>قالب های پاسخ نامه<i class="seoicon-right-arrow"
                                    ></i></a>
                            </li>


                            <li id="menu-item-2004" class="menu-item menu-item-type-custom menu-item-object-custom
                            menu-item-has-icon menu-item-2004"><a href="{{route('adviserQuestions')}}" ><i class="menu-item-icon fa fa-edit" ></i>سوالات نظرسنجی مشاوران<i class="seoicon-right-arrow"
                                    ></i></a>
                            </li>

                        </ul>
                    </li>

                    <li id="menu-item-254" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children menu-item-has-icon menu-item-254">
                        <a href="#"><i class="menu-item-icon fa fa-group" ></i>تنظیمات نمایشی</a>
                        <ul class="sub-menu sub-menu-has-icons">
                            <li id="menu-item-1954" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-1954"><a href="{{route('slideBarManagement')}}" >مدیریت اسلاید بار<i class="seoicon-right-arrow" ></i></a></li>
                            <li id="menu-item-1975" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-1975"><a href="{{route('calenderManagement')}}" >مدیریت تقویم<i class="seoicon-right-arrow" ></i></a></li>
                        </ul>
                    </li>

                    <li id="menu-item-2007" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children menu-item-has-icon menu-item-2007">
                        <a href="#"><i class="menu-item-icon fa fa-group" ></i>سوالات</a>
                        <ul class="sub-menu sub-menu-has-icons">
                            <li id="menu-item-2008" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-2008"><a href="{{route('addQuestion')}}" >افزودن سوال جدید<i class="seoicon-right-arrow" ></i></a></li>
                            <li id="menu-item-2009" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-2009"><a href="{{route('unConfirmedQuestions')}}" >سوالات تایید نشده<i class="seoicon-right-arrow" ></i></a></li>
                            <li id="menu-item-2010" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-2010"><a href="{{route('totalQuestions')}}" >تمام سوالات<i class="seoicon-right-arrow" ></i></a></li>
                        </ul>
                    </li>

                    <li id="menu-item-2011" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children menu-item-has-icon menu-item-2011">
                        <a href="#"><i class="menu-item-icon fa fa-group" ></i>کاربران</a>
                        <ul class="sub-menu sub-menu-has-icons">
                            @if($level == getValueInfo('superAdminLevel'))
                                <li id="menu-item-2012" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-2012"><a href="{{route('addQuestion')}}" >ادمین ها<i class="seoicon-right-arrow" ></i></a></li>
                            @endif
                            <li id="menu-item-2013" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-2013"><a href="{{route('controllers')}}" >ناظران<i class="seoicon-right-arrow" ></i></a></li>
                            <li id="menu-item-2014" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-2014"><a href="{{route('assignControllers')}}" >مدیریت سطح دسترسی ناظران<i class="seoicon-right-arrow" ></i></a></li>
                            <li id="menu-item-2015" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-2015"><a href="{{route('operators_1')}}" >اپراتور های نوع 1<i class="seoicon-right-arrow" ></i></a></li>
                            <li id="menu-item-2016" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-2016"><a href="{{route('operators_2')}}" >اپراتور های نوع 2<i class="seoicon-right-arrow" ></i></a></li>
                            <li id="menu-item-2017" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-2017"><a href="{{route('advisers')}}" >مشاوران<i class="seoicon-right-arrow" ></i></a></li>
                            <li id="menu-item-2018" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-2018"><a href="{{route('namayandeha')}}" >نمایندگان<i class="seoicon-right-arrow" ></i></a></li>
                            <li id="menu-item-2019" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-2019"><a href="{{route('schools')}}" >مدارس<i class="seoicon-right-arrow" ></i></a></li>
                        </ul>
                    </li>


                    <li id="menu-item-100" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children menu-item-has-mega-menu menu-item-has-icon menu-item-100"><a href="#" ><i class="menu-item-icon fa fa-bar-chart-o" ></i>آزمون</a><div class="megamenu" style="" >
                            <ul class="mega-menu-row">
                                <li id="menu-item-8000" class="menu-item menu-item-type-custom menu-item-object-custom mega-menu-col menu-item-1985"><a href="#" >سنجش پای تخته!<i class="seoicon-right-arrow" ></i></a>
                                    <ul class="sub-menu">
                                        <li id="menu-item-8002" class="menu-item menu-item-type-custom
                                        menu-item-object-custom menu-item-8002"><a href="{{route('onlineQuizes')}}" >ساخت آزمون<i class="seoicon-right-arrow" ></i></a></li>
                                        <li id="menu-item-8003" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-8003"><a href="{{route('finishQuiz')}}" >اتمام آزمون<i class="seoicon-right-arrow"
                                                ></i></a></li>

                                        <li id="menu-item-8008" class="menu-item menu-item-type-custom
                                        menu-item-object-custom menu-item-8008"><a href="{{route('composeQuizes')}}" >ساخت بسته های آزمونی<i class="seoicon-right-arrow" ></i></a></li>
                                    </ul>
                                </li>

                                <li id="menu-item-1984" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children mega-menu-col menu-item-1984"><a href="#" >سنجش پشت میز!</a>
                                    <ul class="sub-menu">
                                        <li id="menu-item-1954" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-1954"><a href="{{route('regularQuizes')}}" >ساخت آزمون<i class="seoicon-right-arrow" ></i></a></li>
                                        <li id="menu-item-1975" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-1975"><a href="{{route('createTarazTable')}}" >ساخت جدول تراز آزمون<i class="seoicon-right-arrow" ></i></a></li>
                                        <li id="menu-item-1976" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-1976"><a href="{{route('deleteTarazTable')}}" >حذف جدول تراز آزمون<i class="seoicon-right-arrow" ></i></a></li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <li id="menu-item-1989" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-home current-menu-item page_item page-item-507 current_page_item menu-item-has-icon menu-item-1989"><a href="{{route('ranking1')}}" ><i class="menu-item-icon fa fa-home" ></i>رتبه بندی آزمون ها</a>
                    </li>


                    <li id="menu-item-3000" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children menu-item-has-icon menu-item-3000">
                        <a href="#"><i class="menu-item-icon fa fa-group" ></i>محتوا</a>
                        <ul class="sub-menu sub-menu-has-icons">
                            <li id="menu-item-2008" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-2008"><a href="{{route('grades')}}" >پایه های تحصیلی<i class="seoicon-right-arrow" ></i></a></li>
                            <li id="menu-item-2009" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-2009"><a href="{{route('lessons')}}" >دروس<i class="seoicon-right-arrow" ></i></a></li>
                            <li id="menu-item-2010" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-2010"><a href="{{route('subjects')}}" >مبحث<i class="seoicon-right-arrow" ></i></a></li>
                        </ul>
                    </li>

                    <li id="menu-item-9090" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children menu-item-has-icon menu-item-9090"><a href="#" ><i class="menu-item-icon fa fa-group"
                            ></i>گزارشات</a>
                        <ul class="sub-menu sub-menu-has-icons">
                            <li id="menu-item-9091" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-icon menu-item-1982"><a href="{{route('quizReports')}}" ><i class="menu-item-icon fa fa-check-square-o" ></i>گزارشات مربوط به آزمون<i class="seoicon-right-arrow"></i></a></li>
                            <li id="menu-item-1983" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-icon menu-item-1983"><a href="{{route('subjectReport')}}" ><i class="menu-item-icon fa fa-edit" ></i>گزارش گیری از مباحث<i class="seoicon-right-arrow"></i></a></li>
                            <li id="menu-item-1986" class="menu-item menu-item-type-custom menu-item-object-custom
                            menu-item-has-icon menu-item-1986"><a href="{{route('barcodeReport')}}" ><i class="menu-item-icon fa fa-edit" ></i>گزارش گیری بارکد آزمون<i class="seoicon-right-arrow"></i></a></li>
                            <li id="menu-item-1987" class="menu-item menu-item-type-custom menu-item-object-custom
                            menu-item-has-icon menu-item-1987"><a href="{{route('studentReport')}}" ><i class="menu-item-icon fa fa-edit" ></i>گزارش گیری دانش آموزان<i class="seoicon-right-arrow"
                                    ></i></a></li>

                            <li id="menu-item-1988" class="menu-item menu-item-type-custom menu-item-object-custom
                            menu-item-has-icon menu-item-1988"><a href="{{route('gradeReport')}}" ><i class="menu-item-icon fa fa-edit" ></i>گزارش گیری پایه های تحصیلی<i class="seoicon-right-arrow"
                                    ></i></a>
                            </li>

                            <li id="menu-item-2000" class="menu-item menu-item-type-custom menu-item-object-custom
                            menu-item-has-icon menu-item-2000"><a href="{{route('quizReport')}}" ><i class="menu-item-icon fa fa-edit" ></i>گزارش گیری کلی آزمون ها<i class="seoicon-right-arrow"
                                    ></i></a>
                            </li>


                            <li id="menu-item-2001" class="menu-item menu-item-type-custom menu-item-object-custom
                            menu-item-has-icon menu-item-2001"><a href="{{route('moneyReport')}}" ><i class="menu-item-icon fa fa-edit" ></i>گزارش گیری مالی<i class="seoicon-right-arrow"
                                    ></i></a>
                            </li>
                        </ul>
                    </li>

                    <li id="menu-item-300" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-home current-menu-item page_item page-item-507 current_page_item menu-item-has-icon menu-item-300"><a href="{{route('schoolsList')}}" ><i class="menu-item-icon fa fa-home" ></i>لیست مدارس</a>
                    </li>

                    <li id="menu-item-301" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-home current-menu-item page_item page-item-507 current_page_item menu-item-has-icon menu-item-301"><a href="{{route('smsPanel')}}" ><i class="menu-item-icon fa fa-home" ></i>سامانه پیام رسانی</a>
                    </li>

                    <li id="menu-item-2020" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children menu-item-has-icon menu-item-2020">
                        <a href="#"><i class="menu-item-icon fa fa-group" ></i>پیام ها</a>
                        <ul class="sub-menu sub-menu-has-icons">
                            <li id="menu-item-1954" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-1954"><a href="{{route('message')}}" >صندوق پیام ها<i class="seoicon-right-arrow" ></i></a></li>
                            <li id="menu-item-1975" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-1975"><a href="{{route('controlMsg')}}" >نظارت بر پیام ها<i class="seoicon-right-arrow" ></i></a></li>
                        </ul>
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
