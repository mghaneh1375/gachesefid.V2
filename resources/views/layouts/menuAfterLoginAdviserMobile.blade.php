
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
<p class="header" onclick="document.location.href = '{{route('quizReports')}}'">گزارشات مربوط به آزمون ها</p>
<p class="header" onclick="document.location.href = '{{route('schoolsList')}}'">لیست مدارس</p>
<p class="header" onclick="document.location.href = '{{route('message')}}'"><span> - </span><span>صندوق پیام ها</span></p>
<div style="border: none !important;" class="header" onclick="document.location.href = '{{route('logout')}}'">خروج</div>