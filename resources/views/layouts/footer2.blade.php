
<link rel="stylesheet" type="text/css" href="{{URL::asset('css/footer2.css')}}">

<style>
    @media only screen and (max-width:767px) {
        .footer {
            /*display: none;*/
        }
    }
</style>

<script>

    var i = 1;

    function changeAwardPic() {

        $(".award-img").addClass('hidden');
        $(".award-caption").addClass('hidden');
        $(".award-" + i).removeClass('hidden');


        i++;
        i = i % 4;

        if(i == 0)
            i = 1;

        setTimeout(changeAwardPic, 5000);
    }

    $(document).ready(function () {
        changeAwardPic();
    });

</script>

<footer class="footer col-xs-12">
    <div class="footer-area col-xs-12">
        <div class="col-md-12">
            <div class="row footer-links-holder footer-row">
                <div class="col-md-3 footer-links">
                    <h6>راهنمای ثبت نام</h6>
                    <ul>
                        {{--<li><a title="آیریسک" href="http://www.irysc.com">آیریسک</a></li>--}}
                        {{--<li><a title="انتشارات گچ" href="http://www.gachpub.com">انتشارات گچ</a></li>--}}

                        <li><a title="ثبت نام گروهی (مدارس-نمایندگی‌ها)" href="http://www.news.gachesefid.com/school-register/">ثبت نام گروهی (مدارس-نمایندگی‌ها)</a></li>
                        <li><a title="ثبت نام انفرادی" href="http://www.news.gachesefid.com/%D8%AB%D8%A8%D8%AA-%D9%86%D8%A7%D9%85-%D8%A7%D9%86%D9%81%D8%B1%D8%A7%D8%AF%DB%8C-%D8%A7%D9%84%D9%85%D9%BE%DB%8C%D8%A7%D8%AF-%D8%A2%D8%B2%D9%85%D8%A7%DB%8C%D8%B4%DB%8C/">ثبت نام انفرادی</a></li>
                    </ul>
                </div>
                <div class="col-md-3 footer-links">
                    <h6>راهنمای استفاده</h6>
                    <ul>
                        <li><a title="راهنمای دانش آموزان" href="http://www.news.gachesefid.com/%D8%B1%D8%A7%D9%87%D9%86%D9%85%D8%A7%DB%8C-%D8%AF%D8%A7%D9%86%D8%B4%E2%80%8C%D8%A2%D9%85%D9%88%D8%B2%D8%A7%D9%86/">راهنمای دانش آموزان</a></li>
                        <li><a title="راهنمای مدیران مدارس" href="http://www.news.gachesefid.com/%D8%B1%D8%A7%D9%87%D9%86%D9%85%D8%A7%DB%8C-%D9%85%D8%AF%DB%8C%D8%B1%D8%A7%D9%86-%D9%85%D8%AF%D8%A7%D8%B1%D8%B3/">راهنمای مدیران مدارس</a></li>
                        <li><a title="راهنمای نمایندگی ها" href="http://www.news.gachesefid.com/%D8%B1%D8%A7%D9%87%D9%86%D9%85%D8%A7%DB%8C-%D9%86%D9%85%D8%A7%DB%8C%D9%86%D8%AF%DA%AF%DB%8C%E2%80%8C%D9%87%D8%A7/">راهنمای نمایندگی ها</a></li>
                        <li><a title="راهنمای مشاوران" href="http://www.news.gachesefid.com/%D8%B1%D8%A7%D9%87%D9%86%D9%85%D8%A7%DB%8C-%D9%85%D8%B4%D8%A7%D9%88%D8%B1%D8%A7%D9%86/">راهنمای مشاوران</a></li>
                    </ul>
                </div>
                <div class="col-md-3 footer-links">
                    <h6>دسترسی آسان</h6>
                    <ul>
                        <li><a title="تماس با ما" href="http://www.news.gachesefid.com/gach-exam/calendar-exam/">تقویم و سر فصل گچ سفید</a></li>
                        <li><a title="دریافت نمایندگی" href="http://www.news.gachesefid.com/irysc-olympiad/calendar-olympiad/">المپیاد های آزمایشی</a></li>

                    </ul>
                </div>
                <div class="col-md-3 footer-links">
                    {{--<div class="ft-right ft-awards" id="ft-awards">--}}
                    {{--<div class="award">--}}
                    {{--<div class="award-img award-1 hidden"></div>--}}
                    {{--<div class="award-caption award-1 hidden"><span>همایش تجلیل از چهره‌های کارآفرین و مدیران موفق</span></div>--}}
                    {{--</div>--}}
                    {{--<div class="award">--}}
                    {{--<div class="award-img award-2 hidden"></div>--}}
                    {{--<div class="award-caption award-2 hidden"><span>دومین اجلاس جهانی نشان منتخب</span></div>--}}
                    {{--</div>--}}
                    {{--<div class="award">--}}
                    {{--<div class="award-img award-3 hidden"></div>--}}
                    {{--<div class="award-caption award-3 hidden"><span>کنفرانس مدیریت بیمارستانی و تجهیزات پزشکی</span></div>--}}
                    {{--</div>--}}
                    {{--</div>--}}
                </div>
            </div>
        </div>
    </div>
    <div class="copyright col-xs-12">
        <div class="col-md-6">
            <div class="ft-phone rtl"><span> 66917230-1 (021)</span></div>
            <ul class="socials">
                <li><a href="https://t.me/gachesefid" class="color-telegram" title="تلگرام" target="_blank"><i class="fa fa-telegram"></i></a></li>
                <li><a href="https://twitter.com/gachesefid" class="color-twitter" title="توییتر" target="_blank"><i class="fa fa-twitter-square"></i></a></li>
                <li><a href="https://www.instagram.com/gachesefid/" class="color-instagram" title="اینستاگرام" target="_blank"><i class="fa fa-instagram"></i></a></li>
                <li><a href="https://www.youtube.com" class="color-youtube" title="یوتیوب" target="_blank"><i class="fa fa-youtube"></i></a></li>
                <li><a href="http://www.aparat.com/gachesefid" class="color-aparat" title="آپارات" target="_blank"><img src="{{URL::asset('images/aparat.png')}}" width="30px" style="margin-bottom: 5px"></a></li>
            </ul>
        </div>

        <div class="col-md-6">
            <div class="copyright-text">
                <span>گچ سفید، سنجش گر موفقیت دانش آموزان است. تمام حقوق برای انتشارات گچ محفوظ است.</span>
            </div>
            <div class="copyright-links">
                <a class="nextLineOnMobile" rel="nofollow" href="http://www.news.gachesefid.com/%D9%82%D9%88%D8%A7%D9%86%DB%8C%D9%86-%D9%88-%D9%85%D9%82%D8%B1%D8%B1%D8%A7%D8%AA-%DA%AF%DA%86-%D8%B3%D9%81%DB%8C%D8%AF/">قوانین و مقرارت گچ سفید</a> -
                <a class="nextLineOnMobile" rel="nofollow" href="http://www.news.gachesefid.com/%D8%AD%D8%B1%DB%8C%D9%85-%D8%AE%D8%B5%D9%88%D8%B5%DB%8C-%D8%AF%D8%B1-%DA%AF%DA%86-%D8%B3%D9%81%DB%8C%D8%AF/">ضوابط حفظ حریم خصوصی</a> -
                <a class="nextLineOnMobile" rel="nofollow" href="{{route('aboutUs')}}">آشنایی با گچ سفید</a>
            </div>
        </div>
    </div>
</footer>

