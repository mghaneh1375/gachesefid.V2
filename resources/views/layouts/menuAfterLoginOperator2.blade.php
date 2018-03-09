<?php
    $qCountNum = DB::Select('select count(*) as countNum from discussion WHERE status = 0 and id = relatedTo');
    if($qCountNum == null || count($qCountNum) == 0)
        $qCountNum = 0;
    else
        $qCountNum = $qCountNum[0]->countNum;

    $ansCountNum = DB::Select('select count(*) as countNum from discussion WHERE status = 0 and id <> relatedTo');
    if($ansCountNum == null || count($ansCountNum) == 0)
        $ansCountNum = 0;
    else
        $ansCountNum = $ansCountNum[0]->countNum;
?>

<link rel="stylesheet" href="{{URL::asset('css/menu.css')}}">

<div class="main-area" style="margin-top: 120px; position: fixed; z-index: 1000;">
    <div class="sidebar">
        <div class="affixed-holder" id="affixed-holder" style="height: 371px;">
            <div class="affixed affix-top" id="affixed" style="">
                <div class="widget filtering-list-holder">
                    <div>
                        <ul class="categories" id="categories">
                            <li class="menuItem home active"><a href="{{route('home')}}"><span>خانه</span></a></li>
                            <li class="menuItem nb-medical">
                                <a href="{{route('unConfirmedDiscussionQ')}}">
                                    <span>سوالات تایید نشده</span>
                                    <span>&nbsp;(</span>
                                    <span>{{$qCountNum}}</span>
                                    <span>)</span>
                                </a>
                            </li>
                            <li class="menuItem schools"><a href="{{route('schoolsList')}}"><span>لیست مدارس</span></a></li>
                            <li class="menuItem nb-medical">
                                <a href="{{route('unConfirmedDiscussionAns')}}">
                                    <span>پاسخ تایید نشده</span>
                                    <span>&nbsp;(</span>
                                    <span>{{$ansCountNum}}</span>
                                    <span>)</span>
                                </a>
                            </li>

                            <li data-val="msgs" class="menuItem profile"><a><span>پیام ها</span></a>
                                <ul class="subItem hidden msgs">
                                    <li><a href="{{route('message')}}">صندوق پیام ها</a></li>
                                    <li><a href="{{route('controlMsg')}}">نظارت بر پیام ها</a></li>
                                </ul>
                            </li>

                            <li data-val="profile" class="menuItem profile"><a href="{{route('profile')}}"> <span>پروفایل</span></a>
                                <ul class="subItem hidden profile">
                                    <li><a href="{{route('userInfo')}}">تغییر اطلاعات کاربری</a></li>
                                    <li><a href="{{route('changePas')}}">تغییر رمزعبور</a></li>
                                </ul>
                            </li>

                            <li data-val="quiz" class="item exit"><a href="{{route('logout')}}"><span>خروج</span></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{URL::asset('js/menu.js')}}"></script>