@extends('layouts.menu.structure')

@section('items')

    <li id="menu-item-1989" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-home current-menu-item page_item page-item-507 current_page_item menu-item-has-icon menu-item-1989"><a href="{{route('quizReports')}}" ><i class="menu-item-icon fa fa-home" ></i>گزارش دانش‌آموزان من</a></li>
    @include('layouts.menu.schools')
    @include('layouts.menu.ranking')
    <li id="menu-item-4343" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-home current-menu-item page_item page-item-507 current_page_item menu-item-has-icon menu-item-4343"><a href="{{route('adviserQueue')}}" ><i class="menu-item-icon fa fa-home" ></i>دانش آموزان در صف انتظار</a></li>
@stop
