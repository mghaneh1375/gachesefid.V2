<style>
    .pointer {
        width: 200px;
        height: 100px;
        position: relative;
    }

    .pointerRow1 {
        background: #5b206c;
    }

    .pointerRow2 {
        background: #7c0812;
    }

    .pointerRow3 {
        background: #c96504;
    }

    .pointer > center > h3 {
        color: white;
        padding: 10px;
    }

    .pointer > center > p {
        color: white;
        padding: 10px;
    }

    .pointerLeft:after {
        content: "";
        position: absolute;
        right: 0;
        bottom: 0;
        width: 0;
        height: 0;
        border-right: 20px solid white;
        border-top: 50px solid transparent;
        border-bottom: 50px solid transparent;
    }

    .pointerLeftRow1:before {
        content: "";
        position: absolute;
        right: 200px;
        bottom: 0;
        width: 0;
        height: 0;
        border-right: 20px solid #5b206c;
        border-top: 50px solid transparent;
        border-bottom: 50px solid transparent;
    }

    .pointerLeftRow3:before {
        content: "";
        position: absolute;
        right: 200px;
        bottom: 0;
        width: 0;
        height: 0;
        border-right: 20px solid #c96504;
        border-top: 50px solid transparent;
        border-bottom: 50px solid transparent;
    }

    .pointerRight:after {
        content: "";
        position: absolute;
        left: 0;
        bottom: 0;
        width: 0;
        height: 0;
        border-left: 20px solid white;
        border-top: 50px solid transparent;
        border-bottom: 50px solid transparent;
    }

    .pointerRight:before {
        content: "";
        position: absolute;
        left: 200px;
        bottom: 0;
        width: 0;
        height: 0;
        border-left: 20px solid #7c0812;
        border-top: 50px solid transparent;
        border-bottom: 50px solid transparent;
    }

    .pointerDown:after {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 0;
        height: 0;
        border-top: 20px solid white;
        border-left: 100px solid transparent;
        border-right: 100px solid transparent;
    }

    .pointerDownRow1:before {
        content: "";
        position: absolute;
        top: 100px;
        left: 0;
        width: 0;
        height: 0;
        border-top: 20px solid #5b206c;
        border-left: 100px solid transparent;
        border-right: 100px solid transparent;
    }

    .pointerDownRow2:before {
        content: "";
        position: absolute;
        top: 100px;
        left: 0;
        width: 0;
        height: 0;
        border-top: 20px solid #7c0812;
        border-left: 100px solid transparent;
        border-right: 100px solid transparent;
    }

    .lineRightToLeft {
        position: absolute;
        right: 220px;
        top: 50px;
        height: 5px;
        background-color: black; z-index: -1
    }

    .lineLeftToRight {
        position: absolute;
        left: 220px;
        top: 50px;
        height: 5px;
        direction: ltr;
        background-color: black; z-index: -1
    }

    .lineTopToDown {
        position: absolute;
        right: 100px;
        top: 120px;
        width: 5px;
        background-color: black;
        z-index: -1
    }

    @media only screen and (max-width:767px) {
        .displayOnScreen {
            display: none;
        }
    }
</style>

<script src="{{URL::asset('js/schoolProfileJS.js')}}"></script>

<div class="row displayOnScreen">
    <div class="col-xs-12">

        <div class="col-xs-4">
            <div class="pointer pointerDownRow1 pointerDown pointerRow1">
                <div id="line3" class="lineTopToDown"></div>
                <center style="padding-top: 30px"><h3>قدم سوم</h3><p>ثبت نام در آزمون</p></center>
            </div>
        </div>
        <div class="col-xs-4">
            <div class="pointer pointerLeftRow1 pointerRow1 pointerLeft">
                <div id="line2" class="lineRightToLeft"></div>
                <center><h3>قدم دوم</h3><p>دریافت نام کاربری و رمزعبور</p></center>
            </div>
        </div>
        <div class="col-xs-4">
            <div class="pointer pointerLeftRow1 pointerRow1 pointerLeft">
                <div id="line1" class="lineRightToLeft"></div>
                <center><h3>قدم اول</h3><p>ثبت لیست دانش آموزان</p></center>
            </div>
        </div>
    </div>

    <div class="col-xs-12" style="margin-top: 50px">
        <div class="col-xs-4">
            <div class="pointer pointerRight pointerRow2">
                <div id="line4" class="lineLeftToRight"></div>
                <center><h3>قدم چهارم</h3><p>برگزاری آزمون</p></center>
            </div>
        </div>
        <div class="col-xs-4">
            <div class="pointer pointerRight pointerRow2">
                <div id="line5" class="lineLeftToRight"></div>
                <center><h3>قدم پنجم</h3><p>اسکن پاسخ برگ ها</p></center>
            </div>
        </div>
        <div class="col-xs-4">
            <div class="pointer pointerDown pointerDownRow2 pointerRow2">
                <div id="line6" class="lineTopToDown"></div>
                <center style="padding-top: 30px"><h3>قدم ششم</h3><p>تصحیح پاسخ برگ ها</p></center>
            </div>
        </div>
    </div>

    <div class="col-xs-12" style="margin-top: 50px">

        <div class="col-xs-4">
            <div class="pointer pointerRow3">
                <center><h3>قدم نهم</h3><p>تحلیل و مشاهده</p></center>
            </div>
        </div>
        <div class="col-xs-4">
            <div class="pointer pointerLeftRow3 pointerLeft pointerRow3">
                <div id="line8" class="lineRightToLeft"></div>
                <center><h3>قدم هشتم</h3><p>مشاهده کارنامه</p></center>
            </div>
        </div>
        <div class="col-xs-4">
            <div class="pointer pointerLeftRow3 pointerLeft pointerRow3">
                <div id="line7" class="lineRightToLeft"></div>
                <center><h3>قدم هفتم</h3><p>ارسال نتایج</p></center>
            </div>
        </div>
    </div>
</div>