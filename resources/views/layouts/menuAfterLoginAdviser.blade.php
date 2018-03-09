<link rel="stylesheet" href="{{URL::asset('css/menu.css')}}">

<div class="main-area" style="margin-top: 120px; position: fixed; z-index: 1000;">
    <div class="sidebar">
        <div class="affixed-holder" id="affixed-holder" style="height: 371px;">
            <div class="affixed affix-top" id="affixed" style="">
                <div class="widget filtering-list-holder">
                    <div>
                        <ul class="categories" id="categories">
                            <li class="menuItem home active"><a href="{{route('home')}}"><span>خانه</span></a></li>
                            <li class="menuItem nb-vitrin"><a href="{{route('quizReports')}}"><span>گزارشات مربوط به آزمون ها</span></a></li>
                            <li class="menuItem schools"><a href="{{route('schoolsList')}}"><span>لیست مدارس</span></a></li>
                            <li class="menuItem nb-art"><a href='{{route('message')}}'> <span>صندوق پیام ها</span></a></li>
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

<script src="{{URL::asset('js/menu.js')}}"></script>