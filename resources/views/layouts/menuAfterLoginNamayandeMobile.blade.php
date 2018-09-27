
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
    <p>مدارس</p>
    <p class="sub-header" onclick="document.location.href = '{{route('groupRegistration')}}'">ثبت لیست دانش‌آموزان</p>
    <p class="sub-header" onclick="document.location.href = '{{route('namayandeStudent')}}'">گزارش گیری از دانش آموزان</p>
</div>

<div class="header">
    <p>مدارس</p>
    <p class="sub-header" onclick="document.location.href = '{{route('addSchool')}}'">افزودن مدرسه</p>
    <p class="sub-header" onclick="document.location.href = '{{route('namayandeSchool')}}'">گزارش گیری از مدارس</p>
    <p class="sub-header" onclick="document.location.href = '{{route('schoolsList')}}'">لیست کل مدارس</p>
</div>


<div class="header">
    <p>آزمون ها</p>
    <p class="sub-header" onclick="document.location.href = '{{route('groupQuizRegistration')}}'">ثبت نام در آزمون</p>
    <p class="sub-header" onclick="document.location.href = '{{route('ranking1')}}'">رتبه بندی آزمون ها</p>
    <p class="sub-header" onclick="document.location.href = '{{route('quizReports')}}'">گزارشات مربوط به آزمون ها</p>
</div>

<div class="header">
    <p>پروفایل</p>
    <p class="sub-header" onclick="document.location.href = '{{route('userInfo')}}'"><span> - </span><span>تغییر اطلاعات کاربری</span></p>
    <p class="sub-header" onclick="document.location.href = '{{route('changePas')}}'"><span> - </span><span>تغییر رمزعبور</span></p>
</div>

<div style="border: none !important;" class="header" onclick="document.location.href = '{{route('logout')}}'">خروج</div>