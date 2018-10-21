@extends('layouts.menu.structure')

@section('items')

    <li id="menu-item-1981" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children menu-item-has-icon menu-item-1981"><a href="#" ><i class="menu-item-icon fa fa-group" ></i>دانش آموزان</a>
        <ul class="sub-menu sub-menu-has-icons">
            <li id="menu-item-1982" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-icon menu-item-1982"><a href="{{route('groupRegistration')}}" ><i class="menu-item-icon fa fa-check-square-o" ></i>ثبت لیست دانش‌آموزان<i
                            class="seoicon-right-arrow"
                    ></i></a></li>
            <li id="menu-item-23001" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-icon menu-item-1982"><a href="{{route('oneByOneRegistration')}}" ><i class="menu-item-icon fa fa-edit" ></i>ثبت تک تک دانش‌آموزان<i
                            class="seoicon-right-arrow"
                    ></i></a></li>
            <li id="menu-item-1983" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-icon menu-item-1983"><a href="{{route('schoolStudent', ['sId' => Auth::user()->id])}}" ><i class="menu-item-icon fa fa-edit" ></i>گزارش گیری از دانش آموزان<i class="seoicon-right-arrow"
                    ></i></a></li>

        </ul>
    </li>

    <li id="menu-item-600" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children menu-item-has-icon menu-item-600">
        <a href="#"><i class="menu-item-icon fa fa-check-circle" ></i>آزمون ها</a>
        <ul class="sub-menu sub-menu-has-icons">
            <li id="menu-item-1954" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-1954"><a href="{{route('groupQuizRegistration')}}" >ثبت نام در آزمون<i class="seoicon-right-arrow" ></i></a></li>
            <li id="menu-item-1975" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-1975"><a href="{{route('ranking1')}}" >رتبه بندی آزمون ها<i class="seoicon-right-arrow" ></i></a></li>

            <li id="menu-item-1975" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-1975"><a href="{{route('quizReports')}}" >گزارشات مربوط به آزمون ها<i class="seoicon-right-arrow" ></i></a></li>
        </ul>
    </li>

    @include('layouts.menu.schools')
    @include('layouts.menu.ranking')
@stop