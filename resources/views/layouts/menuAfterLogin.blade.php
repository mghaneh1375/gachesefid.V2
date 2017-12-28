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
                            <li class="menuItem home active"><a href="{{route('home')}}"><span>خانه</span></a></li>
                            {{--<li class="menuItem nb-medical"><a href="#"> <span>مشاور من</span></a></li>--}}
                            <li data-val="quiz" class="menuItem exam"><a><span>آزمون ها</span></a>
                                <ul class="subItem hidden quiz">
                                    <li><a href="{{route('myQuizes')}}">آزمون های من</a></li>
                                    <li><a href="{{route('seeResult')}}">کارنامه آزمون</a></li>
                                    <li class="sub_item" data-val="subQuiz"><a href="#">ثبت نام در آزمون</a>
                                        <ul class="subSubItem hidden subQuiz">
                                            <li><a href="{{route('quizRegistry')}}">سنجش پای تخته!</a></li>
                                            <li><a href="{{route('regularQuizRegistry')}}">سنجش پشت میز!</a></li>
                                        </ul>
                                    </li>
                                    <li><a href="{{route('createCustomQuiz')}}">ساخت آزمون جدید</a></li>
                                </ul>
                            </li>

                            <li class="menuItem quizRanking"><a href="{{route('ranking1')}}"><span>رتبه بندی آزمون ها</span></a></li>
                            <li class="menuItem schools"><a href="{{route('schoolsList')}}"><span>لیست مدارس</span></a></li>
                            {{--<li data-val="question" class="menuItem question"><a href="#"><span>سوالات</span></a>--}}
                                {{--<ul class="subItem hidden question">--}}
                                    {{--<li class="sub_item" data-val="subQuestion"><a href="#">سوال های من</a>--}}
                                        {{--<ul class="subSubItem hidden subQuestion">--}}
                                            {{--<li><a href="">سوالات تایید شده</a></li>--}}
                                            {{--<li><a href="">سوالات تایید نشده</a></li>--}}
                                            {{--<li><a href="">سوالات بررسی نشده</a></li>--}}
                                        {{--</ul>--}}
                                    {{--</li>--}}
                                    {{--<li class="sub_item" data-val="subBoughtQuestion"><a>سوالات خریداری شده</a>--}}
                                        {{--<ul class="subSubItem hidden subBoughtQuestion">--}}
                                            {{--<li><a href="">تفکیک شده بر اساس آزمون ها</a></li>--}}
                                            {{--<li><a href="{{route('showSubjects')}}">تفکیک شده بر اساس مباحث</a></li>--}}
                                        {{--</ul>--}}
                                    {{--</li>--}}
                                {{--</ul>--}}
                            {{--</li>--}}
                            {{--<li data-val="ranking" class="menuItem ranking"><a href="#"> <span>رتبه بندی</span></a>--}}
                                {{--<ul class="subItem hidden ranking">--}}
                                    {{--<li><a>دانش آموزان</a></li>--}}
                                    {{--<li><a>مشاوران</a></li>--}}
                                {{--</ul>--}}
                            {{--</li>--}}
                            {{--<li class="menuItem nb-art"><a href="#"> <span>صندوق پیام ها</span></a></li>--}}
                            <li data-val="profile" class="menuItem profile"><a href="{{route('profile')}}"> <span>پروفایل</span></a>
                                <ul class="subItem hidden profile">
                                    <li><a href="{{route('userInfo')}}">تغییر اطلاعات کاربری</a></li>
                                    <li><a href="{{route('changePas')}}">تغییر رمزعبور</a></li>
                                </ul>
                            </li>
                            <li data-val="wallet" class="menuItem money"><a><span>کیف پول</span></a>
                                <ul class="subItem hidden wallet">
                                    <li><a href="{{route('chargeAccount')}}">شارژ حساب</a></li>
                                    {{--<li><a href="{{route('chargeAccount')}}">فعالیت های من</a></li>--}}
                                </ul>
                            </li>
                            <li class="menuItem exit"><a href="{{route('logout')}}"><span>خروج</span></a>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{URL::asset('js/menu.js')}}"></script>

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