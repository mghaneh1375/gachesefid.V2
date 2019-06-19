@extends('layouts.menu.structure')

@section('items')

    <li id="menu-item-1981" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children menu-item-has-icon menu-item-1981"><a href="#" >
            <svg width="24.5px" height="18.5px" aria-hidden="true" data-prefix="far" data-icon="cog" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-inline--fa fa-cog fa-w-16 fa-3x"><path fill="currentColor" d="M452.515 237l31.843-18.382c9.426-5.441 13.996-16.542 11.177-27.054-11.404-42.531-33.842-80.547-64.058-110.797-7.68-7.688-19.575-9.246-28.985-3.811l-31.785 18.358a196.276 196.276 0 0 0-32.899-19.02V39.541a24.016 24.016 0 0 0-17.842-23.206c-41.761-11.107-86.117-11.121-127.93-.001-10.519 2.798-17.844 12.321-17.844 23.206v36.753a196.276 196.276 0 0 0-32.899 19.02l-31.785-18.358c-9.41-5.435-21.305-3.877-28.985 3.811-30.216 30.25-52.654 68.265-64.058 110.797-2.819 10.512 1.751 21.613 11.177 27.054L59.485 237a197.715 197.715 0 0 0 0 37.999l-31.843 18.382c-9.426 5.441-13.996 16.542-11.177 27.054 11.404 42.531 33.842 80.547 64.058 110.797 7.68 7.688 19.575 9.246 28.985 3.811l31.785-18.358a196.202 196.202 0 0 0 32.899 19.019v36.753a24.016 24.016 0 0 0 17.842 23.206c41.761 11.107 86.117 11.122 127.93.001 10.519-2.798 17.844-12.321 17.844-23.206v-36.753a196.34 196.34 0 0 0 32.899-19.019l31.785 18.358c9.41 5.435 21.305 3.877 28.985-3.811 30.216-30.25 52.654-68.266 64.058-110.797 2.819-10.512-1.751-21.613-11.177-27.054L452.515 275c1.22-12.65 1.22-25.35 0-38zm-52.679 63.019l43.819 25.289a200.138 200.138 0 0 1-33.849 58.528l-43.829-25.309c-31.984 27.397-36.659 30.077-76.168 44.029v50.599a200.917 200.917 0 0 1-67.618 0v-50.599c-39.504-13.95-44.196-16.642-76.168-44.029l-43.829 25.309a200.15 200.15 0 0 1-33.849-58.528l43.819-25.289c-7.63-41.299-7.634-46.719 0-88.038l-43.819-25.289c7.85-21.229 19.31-41.049 33.849-58.529l43.829 25.309c31.984-27.397 36.66-30.078 76.168-44.029V58.845a200.917 200.917 0 0 1 67.618 0v50.599c39.504 13.95 44.196 16.642 76.168 44.029l43.829-25.309a200.143 200.143 0 0 1 33.849 58.529l-43.819 25.289c7.631 41.3 7.634 46.718 0 88.037zM256 160c-52.935 0-96 43.065-96 96s43.065 96 96 96 96-43.065 96-96-43.065-96-96-96zm0 144c-26.468 0-48-21.532-48-48 0-26.467 21.532-48 48-48s48 21.533 48 48c0 26.468-21.532 48-48 48z" class=""></path></svg>
            پیکربندی</a>
        <ul class="sub-menu sub-menu-has-icons">
            <li id="menu-item-1982" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-icon menu-item-1982"><a href="{{route('pointsConfig')}}" ><i class="menu-item-icon fa fa-edit"></i>تعیین امتیازات<i
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
            <li id="menu-item-1954" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-1954"><a href="{{route('slideBarManagement')}}" ><i class="menu-item-icon fa fa-edit"></i>مدیریت اسلاید بار<i class="seoicon-right-arrow" ></i></a></li>
            <li id="menu-item-1975" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-1975"><a href="{{route('calenderManagement')}}" ><i class="menu-item-icon fa fa-edit"></i>مدیریت تقویم<i class="seoicon-right-arrow" ></i></a></li>
        </ul>
    </li>

    <li id="menu-item-2007" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children menu-item-has-icon menu-item-2007">
        <a href="#"><i class="menu-item-icon fa fa-group" ></i>سوالات</a>
        <ul class="sub-menu sub-menu-has-icons">
            <li id="menu-item-2008" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-2008"><a href="{{route('addQuestion')}}" ><i class="menu-item-icon fa fa-edit"></i>افزودن سوال جدید<i class="seoicon-right-arrow" ></i></a></li>
            <li id="menu-item-2009" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-2009"><a href="{{route('unConfirmedQuestions')}}" ><i class="menu-item-icon fa fa-edit"></i>سوالات تایید نشده<i class="seoicon-right-arrow" ></i></a></li>
            <li id="menu-item-2010" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-2010"><a href="{{route('totalQuestions')}}" ><i class="menu-item-icon fa fa-edit"></i>تمام سوالات<i class="seoicon-right-arrow" ></i></a></li>
        </ul>
    </li>

    <li id="menu-item-2011" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children menu-item-has-icon menu-item-2011">
        <a href="#"><i class="menu-item-icon fa fa-group" ></i>کاربران</a>
        <ul class="sub-menu sub-menu-has-icons">
            @if($level == getValueInfo('superAdminLevel'))
                <li id="menu-item-2012" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-2012"><a href="{{route('addQuestion')}}" ><i class="menu-item-icon fa fa-edit"></i>ادمین ها<i class="seoicon-right-arrow" ></i></a></li>
            @endif
            <li id="menu-item-2013" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-2013"><a href="{{route('controllers')}}" ><i class="menu-item-icon fa fa-edit"></i>ناظران<i class="seoicon-right-arrow" ></i></a></li>
            <li id="menu-item-2014" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-2014"><a href="{{route('assignControllers')}}" ><i class="menu-item-icon fa fa-edit"></i>مدیریت سطح دسترسی ناظران<i class="seoicon-right-arrow" ></i></a></li>
            <li id="menu-item-2015" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-2015"><a href="{{route('operators_1')}}" ><i class="menu-item-icon fa fa-edit"></i>اپراتور های نوع 1<i class="seoicon-right-arrow" ></i></a></li>
            <li id="menu-item-2016" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-2016"><a href="{{route('operators_2')}}" ><i class="menu-item-icon fa fa-edit"></i>اپراتور های نوع 2<i class="seoicon-right-arrow" ></i></a></li>
            <li id="menu-item-2017" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-2017"><a href="{{route('advisers')}}" ><i class="menu-item-icon fa fa-edit"></i>مشاوران<i class="seoicon-right-arrow" ></i></a></li>
            <li id="menu-item-2018" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-2018"><a href="{{route('namayandeha')}}" ><i class="menu-item-icon fa fa-edit"></i>نمایندگان<i class="seoicon-right-arrow" ></i></a></li>
            <li id="menu-item-2019" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-2019"><a href="{{route('schools')}}" ><i class="menu-item-icon fa fa-edit"></i>مدارس<i class="seoicon-right-arrow" ></i></a></li>
                <li id="menu-item-2019" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-2019"><a href="{{route('deactiveUsers')}}" ><i class="menu-item-icon fa fa-edit"></i>تایید کاربران غیر فعال<i class="seoicon-right-arrow" ></i></a></li>
        </ul>
    </li>


    <li id="menu-item-100" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children menu-item-has-mega-menu menu-item-has-icon menu-item-100"><a href="#" ><i class="menu-item-icon fa fa-bar-chart-o" ></i>آزمون</a><div class="megamenu" style="" >
            <ul class="mega-menu-row">
                {{--<li id="menu-item-8000" class="menu-item menu-item-type-custom menu-item-object-custom mega-menu-col menu-item-1985"><a href="#" >سنجش پای تخته!<i class="seoicon-right-arrow" ></i></a>--}}
                    {{--<ul class="sub-menu sub-menu-has-icons">--}}
                        {{--<li id="menu-item-8002" class="menu-item menu-item-type-custom--}}
                                        {{--menu-item-object-custom menu-item-8002"><a href="{{route('onlineQuizes')}}" ><i class="menu-item-icon fa fa-edit"></i>ساخت آزمون<i class="seoicon-right-arrow" ></i></a></li>--}}
                        {{--<li id="menu-item-8003" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-8003"><a href="{{route('finishQuiz')}}" ><i class="menu-item-icon fa fa-edit"></i>اتمام آزمون<i class="seoicon-right-arrow"--}}
                                {{--></i></a></li>--}}

                        {{--<li id="menu-item-8008" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-8008">--}}
                            {{--<a href="{{route('composeQuizes')}}" ><i class="menu-item-icon fa fa-edit"></i>ساخت بسته های آزمونی<i class="seoicon-right-arrow" ></i></a></li>--}}
                    {{--</ul>--}}
                {{--</li>--}}

                <li id="menu-item-1984" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children mega-menu-col menu-item-1984"><a href="#" >سنجش پشت میز!</a>
                    <ul class="sub-menu sub-menu-has-icons">
                        <li id="menu-item-1954" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-1954"><a href="{{route('regularQuizes')}}" ><i class="menu-item-icon fa fa-edit"></i>ساخت آزمون<i class="seoicon-right-arrow" ></i></a></li>
                        <li id="menu-item-1975" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-1975"><a href="{{route('createTarazTable')}}" ><i class="menu-item-icon fa fa-edit"></i>ساخت جدول تراز آزمون<i class="seoicon-right-arrow" ></i></a></li>
                        <li id="menu-item-1976" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-1976"><a href="{{route('deleteTarazTable')}}" ><i class="menu-item-icon fa fa-edit"></i>حذف جدول تراز آزمون<i class="seoicon-right-arrow" ></i></a></li>
                        <li id="menu-item-8008" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-8008">
                        <a href="{{route('composeQuizes')}}" ><i class="menu-item-icon fa fa-edit"></i>ساخت بسته های آزمونی<i class="seoicon-right-arrow" ></i></a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </li>

    <li id="menu-item-3000" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children menu-item-has-icon menu-item-3000">
        <a href="#"><i class="menu-item-icon fa fa-group" ></i>محتوا</a>
        <ul class="sub-menu sub-menu-has-icons">
            <li id="menu-item-2008" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-2008"><a href="{{route('grades')}}" ><i class="menu-item-icon fa fa-edit"></i>پایه های تحصیلی<i class="seoicon-right-arrow" ></i></a></li>
            <li id="menu-item-2009" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-2009"><a href="{{route('lessons')}}" ><i class="menu-item-icon fa fa-edit"></i>دروس<i class="seoicon-right-arrow" ></i></a></li>
            <li id="menu-item-2010" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-2010"><a href="{{route('subjects')}}" ><i class="menu-item-icon fa fa-edit"></i>مبحث<i class="seoicon-right-arrow" ></i></a></li>
        </ul>
    </li>

    <li id="menu-item-9090" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children menu-item-has-icon menu-item-9090"><a href="#" ><i class="menu-item-icon fa fa-group"></i>گزارشات</a>
        <ul class="sub-menu sub-menu-has-icons">
            <li id="menu-item-9091" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-icon menu-item-1982"><a href="{{route('quizReports')}}" ><i class="menu-item-icon fa fa-edit" ></i>گزارشات مربوط به آزمون<i class="seoicon-right-arrow"></i></a></li>
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

    <li id="menu-item-2020" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children menu-item-has-icon menu-item-2020">
        <a href="#"><i class="menu-item-icon fa fa-envelope"></i>پیام ها</a>
        <ul class="sub-menu sub-menu-has-icons">
            <li id="menu-item-1954" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-1954"><a href="{{route('smsPanel')}}" ><i class="menu-item-icon fa fa-edit"></i>ارسال پیام های گروهی<i class="seoicon-right-arrow" ></i></a></li>
            <li id="menu-item-1975" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-1975"><a href="{{route('controlMsg')}}" ><i class="menu-item-icon fa fa-edit"></i>نظارت بر پیام ها<i class="seoicon-right-arrow" ></i></a></li>
        </ul>
    </li>

    @include('layouts.menu.schools')
    @include('layouts.menu.ranking')
@stop