<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    });
</script>

<link rel="stylesheet" href="{{URL::asset('css/menu.css')}}">

<div class="main-area" style="margin-top: 120px; position: fixed; z-index: 1000;">
    <div class="sidebar">
        <div class="affixed-holder" id="affixed-holder" style="height: 371px;">
            <div class="affixed affix-top" id="affixed" style="">
                <div class="widget filtering-list-holder">
                    <div>
                        <ul class="categories" id="categories">
                            <li data-val="configuration" class="menuItem nb-sport"><a><span>پیکربندی</span></a>
                                <ul class="subItem hidden configuration">
                                    <li><a href="{{route('pointsConfig')}}">تعیین امتیازات</a></li>
                                    <li><a href="{{route('config')}}">تعیین سایر</a></li>
                                    <li><a href="{{route('defineKarname')}}">تعیین کارنامه</a></li>
                                    <li><a href="{{route('quizStatus')}}">وضعیت آزمون</a></li>
                                    <li><a href="{{route('states')}}">استان ها</a></li>
                                    <li><a href="{{route('cities')}}">شهر ها</a></li>
                                    <li><a href="{{route('tags')}}">تگ های به رد سوالات</a></li>
                                    <li><a href="{{route('reportsAccess')}}">مدیریت سطح دسترسی گزارشات</a></li>
                                    <li><a href="{{route('answer_sheet_templates')}}">قالب های پاسخ نامه</a></li>
                                </ul>
                            </li>
                            <li data-val="showSetting" class="menuItem nb-sport"><a><span>تنظیمات نمایشی</span></a>
                                <ul class="subItem hidden showSetting">
                                    <li><a href="{{route('slideBarManagement')}}">مدیریت اسلاید بار</a></li>
                                    <li><a href="{{route('calenderManagement')}}">مدیریت تقویم</a></li>
                                </ul>
                            </li>
                            <li data-val="questions" class="menuItem nb-sport"><a><span>سوالات</span></a>
                                <ul class="subItem hidden questions">
                                    <li><a href="{{route('addQuestion')}}">افزودن سوال جدید</a></li>
                                    <li><a href="{{route('unConfirmedQuestions')}}">سوالات تایید نشده</a></li>
                                    <li><a href="{{route('totalQuestions')}}">تمام سوالات</a></li>
                                </ul>
                            </li>

                            <?php
                            $level = Auth::user()->level
                            ?>

                            <li data-val="efficients" class="menuItem nb-sport"><a><span>کاربران</span></a>
                                <ul class="subItem hidden efficients">
                                    @if($level == getValueInfo('superAdminLevel'))
                                        <li><a href="{{route('admins')}}">ادمین ها</a></li>
                                    @endif
                                    <li><a href="{{route('controllers')}}">ناظران</a></li>
                                    <li><a href="{{route('assignControllers')}}">مدیریت سطح دسترسی ناظران</a></li>
                                    <li><a href="{{route('operators_1')}}">اپراتور های نوع 1</a></li>
                                    <li><a href="{{route('operators_2')}}">اپراتور های نوع 2</a></li>
                                    <li><a href="{{route('advisers')}}">مشاوران</a></li>
                                    <li><a href="{{route('namayandeha')}}">نمایندگان</a></li>
                                    <li><a href="{{route('schools')}}">مدارس</a></li>
                                </ul>
                            </li>
                            <li data-val="test" class="menuItem nb-sport"><a><span>آزمون</span></a>
                                <ul class="subItem hidden test">
                                    <li class="sub_item" data-val="subOnlineQuiz"><a>سنجش پای تخته!</a>
                                        <ul class="subSubItem hidden subOnlineQuiz">
                                            <li><a href="{{route('onlineQuizes')}}">ساخت آزمون</a></li>
                                            <li><a href="{{route('finishQuiz')}}">اتمام آزمون</a></li>
                                        </ul>
                                    </li>
                                    <li class="sub_item" data-val="subRegularQuiz"><a>سنجش پشت میز!</a>
                                        <ul class="subSubItem hidden subRegularQuiz">
                                            <li><a href="{{route('regularQuizes')}}">ساخت آزمون</a></li>
                                            <li><a href="{{route('createTarazTable')}}">ساخت جدول تراز آزمون</a></li>
                                            <li><a href="{{route('deleteTarazTable')}}">حذف جدول تراز آزمون</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                            <li class="menuItem quizRanking"><a href="{{route('ranking1')}}"><span>رتبه بندی آزمون ها</span></a></li>
                            <li data-val="content" class="menuItem nb-sport"><a><span>محتوا</span></a>
                                <ul class="subItem hidden content">
                                    <li><a href="{{route('grades')}}">پایه های تحصیلی</a></li>
                                    <li><a href="{{route('lessons')}}">دروس</a></li>
                                    <li><a href="{{route('subjects')}}">مبحث</a></li>
                                </ul>
                            </li>
                            {{--<li class="menuItem nb-sport"><a href="#"><span>مالی</span></a></li>--}}
                            <li data-val="reports" class="menuItem nb-sport"><a><span>گزارشات</span></a>
                                <ul class="subItem hidden reports">
                                    <li class="sub_item"><a href="{{route('quizReports')}}">گزارشات مربوط به آزمون</a></li>
                                    <li class="sub_item"><a href="{{route('subjectReport')}}">گزارش گیری از مباحث</a></li>
                                    <li class="sub_item"><a href="{{route('barcodeReport')}}">گزارش گیری بارکد آزمون</a></li>
                                    <li class="sub_item"><a href="{{route('studentReport')}}">گزارش گیری دانش آموزان</a></li>
                                    <li class="sub_item"><a href="{{route('gradeReport')}}">گزارش گیری پایه های تحصیلی</a></li>
                                    <li class="sub_item"><a href="{{route('quizReport')}}">گزارش گیری کلی آزمون ها</a></li>
                                    <li class="sub_item"><a href="{{route('moneyReport')}}">گزارش گیری مالی</a></li>
                                </ul>
                            </li>
                            <li class="menuItem schools"><a href="{{route('schoolsList')}}"><span>لیست مدارس</span></a></li>
                            <li data-val="communication" class="menuItem nb-sport"><a><span>ارتباطات</span></a>
                                <ul class="subItem hidden communication">
                                    <li class="sub_item"><a href="{{route('smsPanel')}}">سامانه پیام رسانی</a>
                                </ul>
                            </li>
                            <li class="menuItem nb-sport"><a href="{{route('message')}}"><span>صندوق پیام ها</span></a></li>
                            <li data-val="profile" class="menuItem profile"><a href="{{route('profile')}}"> <span>پروفایل</span></a>
                                <ul class="subItem hidden profile">
                                    <li><a href="{{route('userInfo')}}">تغییر اطلاعات کاربری</a></li>
                                    <li><a href="{{route('changePas')}}">تغییر رمزعبور</a></li>
                                </ul>
                            </li>

                            <li class="menuItem exit"><a href="{{route('logout')}}"><span>خروج</span></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(".menuItem").mouseenter(function () {
        val = $(this).attr('data-val');
        $(".subItem").addClass('hidden');
        $(".subSubItem").addClass('hidden');
        $("." + val).removeClass('hidden');
    });

    $(".sub_item").mouseenter(function () {
        val = $(this).attr('data-val');
        $(".subSubItem").addClass('hidden');
        $("." + val).removeClass('hidden');
    });

    $(".subItem").mouseleave(function () {
        $(".subItem").addClass('hidden');
    });

    $(".subSubItem").mouseleave(function () {
        $(".subItem").addClass('hidden');
        $(".subSubItem").addClass('hidden');
    });
</script>

<?php /*
<nav class="w3-bar-block w3-small w3-hide-small w3-center" id="NAV">
<!-- Avatar image in top left corner -->

<?php
include_once __DIR__ . '/../../controllers/MoneyController.php';
$money1 = getMoneyKind1();
$total = getTotalMoney();
?>

<center><h4> پول نوع اول {{$money1}}</h4></center>
<center><h4> پول قابل خرج {{$total}}</h4></center>
</nav>

 */ ?>