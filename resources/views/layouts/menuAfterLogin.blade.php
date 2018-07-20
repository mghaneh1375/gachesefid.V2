@extends('layouts.menu.structure')

@section('items')
    <li id="menu-item-1981" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children menu-item-has-icon menu-item-1981"><a href="#" ><i class="menu-item-icon fa fa-check-circle" ></i>آزمون ها</a>
        <ul class="sub-menu sub-menu-has-icons">
            <li id="menu-item-1982" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-icon menu-item-1982"><a href="{{route('myQuizes')}}" ><i class="menu-item-icon fa fa fa-edit" ></i>آزمون های من
                    <i class="seoicon-right-arrow"></i></a></li>
            <li id="menu-item-1983" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-icon menu-item-1983"><a href="{{route('seeResult')}}" ><i class="menu-item-icon fa fa-edit" ></i>کارنامه آزمون<i class="seoicon-right-arrow"
                    ></i></a></li>

            <li id="menu-item-1986" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-icon menu-item-1986">
                <a href="{{route('quizRegistry')}}" ><i class="menu-item-icon fa fa-edit" >
                    </i>ثبت نام در سنجش پای تخته!<i class="seoicon-right-arrow"></i>
                </a>
            </li>
            <li id="menu-item-1987" class="menu-item menu-item-type-custom menu-item-object-custom
                                menu-item-has-icon menu-item-1987"><a href="{{route('regularQuizRegistry')}}" ><i class="menu-item-icon fa fa-edit" ></i>ثبت نام در سنجش پشت میز!<i class="seoicon-right-arrow"
                    ></i></a></li>

            <li id="menu-item-1988" class="menu-item menu-item-type-custom menu-item-object-custom
                                menu-item-has-icon menu-item-1988"><a href="{{route('createCustomQuiz')}}" ><i class="menu-item-icon fa fa-edit" ></i>ساخت آزمون جدید<i class="seoicon-right-arrow"
                    ></i></a>
            </li>

        </ul>
    </li>

    @include('layouts.menu.schools')
    @include('layouts.menu.ranking')

    <li id="menu-item-1991" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-home current-menu-item page_item page-item-507 current_page_item menu-item-has-icon menu-item-1991"><a href="{{route('myAdviser')}}" ><i class="menu-item-icon fa fa-glasses" ></i>مشاور من</a></li>
@stop
