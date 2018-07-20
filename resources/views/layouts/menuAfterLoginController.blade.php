@extends('layouts.menu.structure')

@section('items')

    <li id="menu-item-1989" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-home current-menu-item page_item page-item-507 current_page_item menu-item-has-icon menu-item-1989"><a href="{{route('unConfirmedQuestions')}}" ><i class="menu-item-icon fa fa-home" ></i>سوالات تایید نشده</a></li>
    @include('layouts.menu.schools')
    @include('layouts.menu.ranking')
@stop
