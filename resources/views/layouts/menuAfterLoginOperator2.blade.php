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

@extends('layouts.menu.structure')

@section('items')

    <li id="menu-item-1989" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-home current-menu-item page_item page-item-507 current_page_item menu-item-has-icon menu-item-1989"><a href="{{route('unConfirmedDiscussionQ')}}" ><i class="menu-item-icon fa fa-home" ></i>
            <span>سوالات تایید نشده</span>
            <span>&nbsp;(</span>
            <span>{{$qCountNum}}</span>
            <span>)</span>
        </a>
    </li>

    <li id="menu-item-4000" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-home current-menu-item page_item page-item-507 current_page_item menu-item-has-icon menu-item-4000"><a href="{{route('unConfirmedDiscussionAns')}}" ><i class="menu-item-icon fa fa-home" ></i>
            <span>پاسخ تایید نشده</span>
            <span>&nbsp;(</span>
            <span>{{$ansCountNum}}</span>
            <span>)</span>
        </a>
    </li>

    @include('layouts.menu.schools')
    @include('layouts.menu.ranking')
@stop
