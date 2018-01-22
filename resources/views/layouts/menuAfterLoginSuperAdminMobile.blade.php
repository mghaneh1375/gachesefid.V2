
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

    .sub-sub-header {
        margin-right: 80px;
        color: #af3300;
    }

    #mobileMenuBar {
        position: absolute !important;
        height: fit-content;
        max-height: 92vh;
        overflow: auto;
    }

</style>

<p class="header" onclick="document.location.href = '{{route('home')}}'">خانه</p>
<div class="header">
    <p>پیکربندی</p>
    <p class="sub-header" onclick="document.location.href = '{{route('pointsConfig')}}'"><span> - </span><span>تعیین امتیازات</span></p>
    <p class="sub-header" onclick="document.location.href = '{{route('config')}}'"><span> - </span><span>تعیین سایر</span></p>
    <p class="sub-header" onclick="document.location.href = '{{route('defineKarname')}}'"><span> - </span><span>تعیین کارنامه</span></p>
    <p class="sub-header" onclick="document.location.href = '{{route('quizStatus')}}'"><span> - </span><span>وضعیت آزمون</span></p>
    <p class="sub-header" onclick="document.location.href = '{{route('states')}}'"><span> - </span><span>استان ها</span></p>
    <p class="sub-header" onclick="document.location.href = '{{route('cities')}}'"><span> - </span><span>شهر ها</span></p>
    <p class="sub-header" onclick="document.location.href = '{{route('tags')}}'"><span> - </span><span>تگ های به رد سوالات</span></p>
    <p class="sub-header" onclick="document.location.href = '{{route('reportsAccess')}}'"><span> - </span><span>مدیریت سطح دسترسی گزارشات</span></p>
    <p class="sub-header" onclick="document.location.href = '{{route('answer_sheet_templates')}}'"><span> - </span><span>قالب های پاسخ نامه</span></p>
</div>

<div class="header">
    <p>تنظیمات نمایشی</p>
    <p class="sub-header" onclick="document.location.href = '{{route('slideBarManagement')}}'"><span> - </span><span>مدیریت اسلاید بار</span></p>
    <p class="sub-header" onclick="document.location.href = '{{route('calenderManagement')}}'"><span> - </span><span>مدیریت تقویم</span></p>
</div>

<div class="header">
    <p>سوالات</p>
    <p class="sub-header" onclick="document.location.href = '{{route('addQuestion')}}'"><span> - </span><span>افزودن سوال جدید</span></p>
    <p class="sub-header" onclick="document.location.href = '{{route('unConfirmedQuestions')}}'"><span> - </span><span>سوالات تایید نشده</span></p>
    <p class="sub-header" onclick="document.location.href = '{{route('totalQuestions')}}'"><span> - </span><span>تمام سوالات</span></p>
</div>

<div class="header">
    <p>کاربران</p>

    <?php
    $level = Auth::user()->level
    ?>
    @if($level == getValueInfo('superAdminLevel'))
        <p class="sub-header" onclick="document.location.href = '{{route('admins')}}'"><span> - </span><span>ادمین ها</span></p>
    @endif
    <p class="sub-header" onclick="document.location.href = '{{route('controllers')}}'"><span> - </span><span>ناظران</span></p>
    <p class="sub-header" onclick="document.location.href = '{{route('assignControllers')}}'"><span> - </span><span>مدیریت سطح دسترسی ناظران</span></p>
    <p class="sub-header" onclick="document.location.href = '{{route('operators_1')}}'"><span> - </span><span>اپراتور های نوع 1</span></p>
    <p class="sub-header" onclick="document.location.href = '{{route('operators_2')}}'"><span> - </span><span>اپراتور های نوع 2</span></p>
    <p class="sub-header" onclick="document.location.href = '{{route('advisers')}}'"><span> - </span><span>مشاوران</span></p>
    <p class="sub-header" onclick="document.location.href = '{{route('namayandeha')}}'"><span> - </span><span>نمایندگان</span></p>
    <p class="sub-header" onclick="document.location.href = '{{route('schools')}}'"><span> - </span><span>مدارس</span></p>
</div>


<div class="header">
    <p>آزمون</p>
    <p class="sub-header"><span> - </span><span>سنجش پای تخته!</span></p>
    <p class="sub-sub-header" onclick="document.location.href = '{{route('onlineQuizes')}}'"><span> - </span><span>ساخت آزمون</span></p>
    <p class="sub-sub-header" onclick="document.location.href = '{{route('finishQuiz')}}'"><span> - </span><span>اتمام آزمون</span></p>
    <p class="sub-header"><span> - </span><span>سنجش پشت میز!</span></p>
    <p class="sub-sub-header" onclick="document.location.href = '{{route('regularQuizes')}}'"><span> - </span><span>ساخت آزمون</span></p>
    <p class="sub-sub-header" onclick="document.location.href = '{{route('createTarazTable')}}'"><span> - </span><span>ساخت جدول تراز آزمون</span></p>
</div>

<p class="header" onclick="document.location.href = '{{route('ranking1')}}'">رتبه بندی آزمون ها</p>

<div class="header">
    <p>محتوا</p>
    <p class="sub-header" onclick="document.location.href = '{{route('grades')}}'"><span> - </span><span>پایه های تحصیلی</span></p>
    <p class="sub-header" onclick="document.location.href = '{{route('lessons')}}'"><span> - </span><span>دروس</span></p>
    <p class="sub-header" onclick="document.location.href = '{{route('subjects')}}'"><span> - </span><span>مبحث</span></p>
</div>

<div class="header">
    <p>گزارشات</p>
    <p class="sub-header" onclick="document.location.href = '{{route('quizReports')}}'"><span> - </span><span>گزارشات مربوط به آزمون</span></p>
    <p class="sub-header" onclick="document.location.href = '{{route('subjectReport')}}'"><span> - </span><span>گزارش گیری از مباحث</span></p>
    <p class="sub-header" onclick="document.location.href = '{{route('barcodeReport')}}'"><span> - </span><span>گزارش گیری بارکد آزمون</span></p>
    <p class="sub-header" onclick="document.location.href = '{{route('studentReport')}}'"><span> - </span><span>گزارش گیری دانش آموزان</span></p>
    <p class="sub-header" onclick="document.location.href = '{{route('gradeReport')}}'"><span> - </span><span>گزارش گیری پایه های تحصیلی</span></p>
    <p class="sub-header" onclick="document.location.href = '{{route('quizReport')}}'"><span> - </span><span>گزارش گیری کلی آزمون ها</span></p>
    <p class="sub-header" onclick="document.location.href = '{{route('moneyReport')}}'"><span> - </span><span>گزارش گیری مالی</span></p>
</div>

<p class="header" onclick="document.location.href = '{{route('schoolsList')}}'">لیست مدارس</p>
<div class="header">
    <p>ارتباطات</p>
    <p class="sub-header" onclick="document.location.href = '{{route('smsPanel')}}'"><span> - </span><span>سامانه پیام رسانی</span></p>
</div>

<p class="header" onclick="document.location.href = '{{route('message')}}'">صندوق پیام ها</p>

<div class="header">
    <p>پروفایل</p>
    <p class="sub-header" onclick="document.location.href = '{{route('userInfo')}}'"><span> - </span><span>تغییر اطلاعات کاربری</span></p>
    <p class="sub-header" onclick="document.location.href = '{{route('changePas')}}'"><span> - </span><span>تغییر رمزعبور</span></p>
</div>
<div style="border: none !important;" class="header" onclick="document.location.href = '{{route('logout')}}'">خروج</div>