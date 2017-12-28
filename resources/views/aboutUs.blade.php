@extends('layouts.form')

@section('head')
    @parent

    <style>

        .pic {
            width: 150px;
            border-radius: 50%;

        }

        .black {
            -webkit-filter: grayscale(100%);
            filter: grayscale(100%);
        }
    </style>


    <script>


    </script>

@stop

<div style="position: absolute; max-height: 400px; top: 0; left: 0; right: 0">
    <img src="{{URL::asset('images/team2.jpg')}}" style="width: 100%; height: 100%;">
</div>

@section('main')

    <div class="col-xs-12" style="margin-top: 400px">

        <p style="font-size: 28px">
            گچ سفید، سامانه ای برای پیشرفت تحصیلی
        </p>

        <p style="text-align: justify">

            گروه المپیادهای علمی ایران (آیریسک) از 26 شهریور سال 1386 کار خود را آغاز نموده و در فاز اول به صورت مجازی اطلاع‌رسانی و آموزش گسترده‌ی المپیادهای علمی را در سراسر ایران هدف خود قرار داد. تیم مدیریت در طی این سال‌ها همواره بر این باور بوده است که می‌تواند نقش مفیدی در پیشرفت دانش و دانش‌پژوهان ایران داشته باشد و با استقبال خوب دانش‌پژوهان و کسب نتایج عالی توسط دانش‌آموختگان این گروه، فعالیت‌های آیریسک روز‌به‌روز در حال گسترش است. در ادامه با فعالیت‌های درحال اجرای آن بیشتر آشنا خواهید شد.


        </p>

        <div class="col-xs-12" style="margin-top: 10px">
            <div class="col-xs-1 kAA"></div>
            <div onmouseleave="$('.kAA').removeClass('hidden'); $('.kAP').addClass('black'); $('#kA').addClass('col-xs-10').removeClass('col-xs-12').css('opacity', '0.5')" onmouseenter="$('.kAP').removeClass('black'); $('.kAA').addClass('hidden'); $('#kA').addClass('col-xs-12').removeClass('col-xs-10').css('opacity', '1');" id="kA" class="col-xs-10 myBox" style="border: 2px solid #c8d0d6; border-radius: 4px; padding: 10px; background-color: rgb(226, 235, 242); transform: scale(1.05, 1.05); position: relative; box-shadow: none; opacity: 0.5">

                <div class="col-xs-9" style="margin-top: 20px; text-align: justify">
                    <p style="margin-top: 5px">مدیر و موسس مجموعه</p>
                    <p>دانشجوی دکتری مهندسی پلیمر</p>
                    <p>مدرس المپیاد شیمی از سال 1381</p>
                    <p>برنامه‌ریز آموزشی و المپیاد در مدارس 21 استان</p>
                </div>

                <div class="col-xs-3">
                    <div class="kAP pic black" style="height: 150px; background: url('{{URL::asset('images/khalina.jpg')}}'); -webkit-background-size: ;background-size: cover;: "></div>
                    <center style="margin-top: 5px">مرتضی خلینا</center>
                </div>
            </div>
            <div  class="col-xs-1 kAA"></div>

            <div class="col-xs-12" style="height: 50px"></div>

            <div class="col-xs-1 mGA"></div>

            <div onmouseleave="$('.mGA').removeClass('hidden'); $('.mGP').addClass('black'); $('#mG').addClass('col-xs-10').removeClass('col-xs-12').css('opacity', '0.5')" onmouseenter="$('.mGP').removeClass('black'); $('.mGA').addClass('hidden'); $('#mG').addClass('col-xs-12').removeClass('col-xs-10').css('opacity', '1');" id="mG" class="col-xs-10 myBox" style="border: 2px solid #c8d0d6; border-radius: 4px; padding: 10px; background-color: rgb(226, 235, 242); transform: scale(1.05, 1.05); position: relative; box-shadow: none; opacity: 0.5">

                <div class="col-xs-9" style="margin-top: 20px; text-align: justify">
                    <p style="margin-top: 5px">طراح و پشتیبان سایت</p>
                    <p>رتبه 237 کنکور 93</p>
                    <p>دانشجوی کارشناسی رشته فناوری اطلاعات دانشگاه تهران</p>
                    <p>دارای 2 سال سابقه کار در طراحی و پیاده سازی سیستم های آموزشی</p>
                </div>

                <div class="col-xs-3">
                    <div class="mGP pic black" style="height: 150px; background: url('{{URL::asset('images/mghane.jpg')}}'); -webkit-background-size: ;background-size: cover;: "></div>
                    <center style="margin-top: 5px">محمد قانع</center>
                </div>
            </div>
            <div class="col-xs-1 mGA"></div>

        </div>
    </div>
@stop