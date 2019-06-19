
<style>

    .header {
        margin-right: 20px;
        margin-top: 20px;
        border-bottom: 2px solid;
    }

    .sub-header {
        margin-right: 40px;
        color: #00AF87;
    }

</style>

<p class="header" onclick="document.location.href = '{{route('home')}}'">خانه</p>

<div class="header">
    <p>آزمون ها</p>
    <p class="sub-header" onclick="document.location.href = '{{route('regularQuizRegistry')}}'"><span> - </span><span>ثبت نام در آزمون</span></p>
    <p class="sub-header" onclick="document.location.href = '{{route('myQuizes')}}'"><span> - </span><span>آزمون های من</span></p>
    {{--<p class="sub-header" onclick="document.location.href = '{{route('quizRegistry')}}'"><span> - </span><span>ثبت نام در سنجش پای تخته</span></p>--}}
    <p class="sub-header" onclick="document.location.href = '{{route('createCustomQuiz')}}'"><span> - </span><span>ساخت آزمون جدید</span></p>
</div>

<p class="header" onclick="document.location.href = '{{route('seeResult')}}'">کارنامه آزمون</p>
<p class="header" onclick="document.location.href = '{{route('ranking1')}}'">رتبه بندی آزمون ها</p>
<p class="header" onclick="document.location.href = '{{route('schoolsList')}}'">لیست کل مدارس</p>


<div class="header">
    <p>کیف پول</p>
    <p class="sub-header" onclick="document.location.href = '{{route('chargeAccount')}}'"><span> - </span><span>شارژ حساب</span></p>
    <p class="sub-header" onclick="document.location.href = '{{route('myActivities')}}'"><span> - </span><span>فعالیت های من</span></p>
</div>

<p class="header" onclick="document.location.href = '{{route('message')}}'"><span> - </span><span>صندوق پیام ها</span></p>

<div class="header">
    <p>پروفایل</p>
    <p class="sub-header" onclick="document.location.href = '{{route('userInfo')}}'"><span> - </span><span>تغییر اطلاعات کاربری</span></p>
    <p class="sub-header" onclick="document.location.href = '{{route('changePas')}}'"><span> - </span><span>تغییر رمزعبور</span></p>
</div>

<div style="border: none !important;" class="header" onclick="document.location.href = '{{route('logout')}}'">خروج</div>