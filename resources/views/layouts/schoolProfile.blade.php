<style>
    .pointer {
        width: 250px;
        height: 120px;
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

    .pointer > center > h4 {
        color: white;
        padding: 10px;
    }

    .pointer > center > p {
        color: white;
        padding: 5px;
    }

    .pointerLeft:after {
        content: "";
        position: absolute;
        right: 0;
        bottom: 0;
        width: 0;
        height: 0;
        border-right: 20px solid white;
        border-top: 60px solid transparent;
        border-bottom: 60px solid transparent;
    }

    .pointerLeftRow1:before {
        content: "";
        position: absolute;
        right: 250px;
        bottom: 0;
        width: 0;
        height: 0;
        border-right: 40px solid #5b206c;
        border-top: 60px solid transparent;
        border-bottom: 60px solid transparent;
    }

    .pointerLeftRow3:before {
        content: "";
        position: absolute;
        right: 250px;
        bottom: 0;
        width: 0;
        height: 0;
        border-right: 40px solid #c96504;
        border-top: 60px solid transparent;
        border-bottom: 60px solid transparent;
    }

    .pointerRight:after {
        content: "";
        position: absolute;
        left: 0;
        bottom: 0;
        width: 0;
        height: 0;
        border-left: 20px solid white;
        border-top: 60px solid transparent;
        border-bottom: 60px solid transparent;
    }

    .pointerRight:before {
        content: "";
        position: absolute;
        left: 250px;
        bottom: 0;
        width: 0;
        height: 0;
        border-left: 40px solid #7c0812;
        border-top: 60px solid transparent;
        border-bottom: 60px solid transparent;
    }

    .pointerDown:after {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 0;
        height: 0;
        border-top: 20px solid white;
        border-left: 125px solid transparent;
        border-right: 125px solid transparent;
    }

    .pointerDownRow1:before {
        content: "";
        position: absolute;
        top: 120px;
        left: 0;
        width: 0;
        height: 0;
        border-top: 40px solid #5b206c;
        border-left: 125px solid transparent;
        border-right: 125px solid transparent;
    }

    .pointerDownRow2:before {
        content: "";
        position: absolute;
        top: 120px;
        left: 0;
        width: 0;
        height: 0;
        border-top: 40px solid #7c0812;
        border-left: 125px solid transparent;
        border-right: 125px solid transparent;
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
                <center style="padding-top: 3px"><h4>قدم سوم</h4><p>ثبت نام در آزمون</p></center>
            </div>
        </div>
        <div class="col-xs-4">
            <div class="pointer pointerLeftRow1 pointerRow1 pointerLeft">
                <div id="line2" class="lineRightToLeft"></div>
                <center><h4>قدم دوم</h4><p>دریافت نام کاربری و رمزعبور</p></center>
            </div>
        </div>
        <div class="col-xs-4">
            <div class="pointer pointerLeftRow1 pointerRow1 pointerLeft">
                <div id="line1" class="lineRightToLeft"></div>
                <center><h4>قدم اول</h4><p>ثبت لیست دانش آموزان</p></center>
            </div>
        </div>
    </div>

    <div class="col-xs-12" style="margin-top: 50px">
        <div class="col-xs-4">
            <div class="pointer pointerRight pointerRow2">
                <div id="line4" class="lineLeftToRight"></div>
                <center><h4>قدم چهارم</h4><p>برگزاری آزمون</p></center>
            </div>
        </div>
        <div class="col-xs-4">
            <div class="pointer pointerRight pointerRow2">
                <div id="line5" class="lineLeftToRight"></div>
                <center><h4>قدم پنجم</h4><p>اسکن پاسخ برگ ها</p></center>
            </div>
        </div>
        <div class="col-xs-4">
            <div class="pointer pointerDown pointerDownRow2 pointerRow2">
                <div id="line6" class="lineTopToDown"></div>
                <center style="padding-top: 3px"><h4>قدم ششم</h4><p>تصحیح پاسخ برگ ها</p></center>
            </div>
        </div>
    </div>

    <div class="col-xs-12" style="margin-top: 50px">

        <div class="col-xs-4">
            <div class="pointer pointerRow3">
                <center><h4>قدم نهم</h4><p>تحلیل و مشاهده</p></center>
            </div>
        </div>
        <div class="col-xs-4">
            <div class="pointer pointerLeftRow3 pointerLeft pointerRow3">
                <div id="line8" class="lineRightToLeft"></div>
                <center><h4>قدم هشتم</h4><p>مشاهده کارنامه</p></center>
            </div>
        </div>
        <div class="col-xs-4">
            <div class="pointer pointerLeftRow3 pointerLeft pointerRow3">
                <div id="line7" class="lineRightToLeft"></div>
                <center><h4>قدم هفتم</h4><p>ارسال نتایج</p></center>
            </div>
        </div>
    </div>
</div>