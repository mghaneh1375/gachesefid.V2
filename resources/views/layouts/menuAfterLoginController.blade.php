<link rel="stylesheet" href="{{URL::asset('css/menu.css')}}">

<div class="main-area" style="margin-top: 120px; position: fixed; z-index: 1000;">
    <div class="sidebar">
        <div class="affixed-holder" id="affixed-holder" style="height: 371px;">
            <div class="affixed affix-top" id="affixed">
                <div class="widget filtering-list-holder">
                    <div>
                        <ul class="categories" id="categories">
                            <li class="menuItem home"><a href="{{route('home')}}"><span>خانه</span></a></li>
                            <li class="menuItem nb-medical"><a href="{{route('unConfirmedQuestions')}}"> <span>سوالات تایید نشده</span></a></li>
                            <li class="menuItem schools"><a href="{{route('schoolsList')}}"><span>لیست مدارس</span></a></li>
                            <li data-val="quiz" class="menuItem exit"><a href="{{route('logout')}}"><span>خروج</span></a></li>
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