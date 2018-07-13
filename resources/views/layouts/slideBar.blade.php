<script>

    var currSlider = 0;
    var myTimeOut;

    function next() {
        clearTimeout(myTimeOut);

        if(currSlider == sliders - 1)
            tmp = 0;
        else
            tmp = currSlider + 1;
        transition(1, tmp);
    }

    function back() {

        clearTimeout(myTimeOut);

        if(currSlider == 0)
            tmp = sliders - 1;
        else
            tmp = currSlider - 1;

        $("#slider_" + currSlider).css('left', '0%').css('right', '');
        $("#slider_" + tmp).css('right', '100%').css('left', '');

        transitionBack(1, tmp);
    }

    function transition(val, idx1) {

        if(val == 100) {

            $(".sliderBtns").css('background-color', 'rgb(114, 112, 115)');
            $("#slider_btn_" + idx1).css('background-color', 'white');
            $("#slider_" + currSlider).css('left', "100%").css('right', '');

            currSlider = idx1;

            return setTimeout('showSlideBar()', 1);
        }

        $("#slider_" + currSlider).css('right', val + "%");
        $("#slider_" + idx1).css('left', 100 - val + "%");
        val++;

        myTimeOut = setTimeout("transition(" + val + ", " + idx1 + ")", 10);
    }

    function fastTransition(val, idx1, final) {

        if(val == 100) {

            $(".sliderBtns").css('background-color', 'rgb(114, 112, 115)');
            $("#slider_btn_" + idx1).css('background-color', 'white');
            $("#slider_" + currSlider).css('left', "100%").css('right', '');

            currSlider = idx1;
            if(currSlider == final)
                return setTimeout('showSlideBar()', 1);

            if(currSlider == sliders - 1)
                return fastTransition(0, 0, final);

            return fastTransition(0, currSlider + 1, final);
        }

        $("#slider_" + currSlider).css('right', val + "%");
        $("#slider_" + idx1).css('left', 100 - val + "%");
        val += 10;

        myTimeOut = setTimeout("fastTransition(" + val + ", " + idx1 + ", " + final + ")", 10);
    }

    function transitionBack(val, idx1) {

        if(val == 100) {

            $(".sliderBtns").css('background-color', 'rgb(114, 112, 115)');
            $("#slider_btn_" + idx1).css('background-color', 'white');
            $("#slider_" + currSlider).css('left', "100%").css('right', '');
            currSlider = idx1;

            return setTimeout('showSlideBar()', 1);
        }

        $("#slider_" + currSlider).css('left', val + "%");
        $("#slider_" + idx1).css('right', 100 - val + "%");
        val++;

        myTimeOut = setTimeout("transitionBack(" + val + ", " + idx1 + ")", 10);
    }

    function showSlideBar() {

        if(currSlider == sliders - 1)
            tmp = 0;
        else
            tmp = currSlider + 1;

        $(".sliders").css('left', '100%').css('right', '');
        $("#slider_" + currSlider).css('right', '0%');

        myTimeOut = setTimeout("transition(1, " + tmp + ")", 5000);
    }
    
    function changeTitle(val) {
        $(".titleBar").removeClass('focus');
        $("#" + val).addClass('focus');

        $("#rss").empty();

        if(val == "irysc") {
            $.ajax({
                type: 'post',
                url: "{{route('showRSSIrysc')}}",
                success: function (response) {
                    $("#rss").append(response).persiaNumber();
                }
            });
        }
        else {
            $.ajax({
                type: 'post',
                url: "{{route('showRSSGach')}}",
                success: function (response) {
                    $("#rss").append(response).persiaNumber();
                }
            });
        }
    }

    function JMP(idx) {

        clearTimeout(myTimeOut);
        if(currSlider == sliders - 1)
            return fastTransition(0, 0, idx);

        fastTransition(0, currSlider + 1, idx);
    }

</script>

<style>

    @media only screen and (min-width:767px) {
        .sliderBtnsContainer {
            min-width: {{count($sliders) * 4}}% !important;
            left: {{50 - 2 * count($sliders)}}% !important;
        }
    }

    @media only screen and (max-width:767px) {

        .rssContainer {
            margin-top: 10px !important;
        }
        .sliderContainer {
            margin-top: 10px !important;
        }

    }

    .rssContainer {
        height: 300px;
    }

    .sliderBtnsContainer {
        height: 10px;
        position: absolute;
        min-width: {{count($sliders) * 10}}%;
        left: {{50 - 5 * count($sliders)}}%;
        right: auto;
        top: 90%;
    }

    .focus {
        color: #555 !important;
        background-color: #fff !important;
        border: 1px solid #636363 !important;
        border-bottom-color: transparent !important;
        cursor: default !important;
    }

    .titleBar {
        margin-left: auto;
        margin-right: -2px;
        border-radius: 4px 4px 0 0;
        line-height: 1.42857143;
        border: 1px solid transparent;
        position: relative;
        padding: 10px 15px;
        color: #0699d4;
        text-decoration: none !important;
        font-style: normal;
        cursor: pointer;
        font-size: 100%;
        font-family: inherit;
    }

    .titleBarPane {
        background-color: #ddd;
        border-radius: 6px;
        padding: 10px;
        height: 50px;
    }

    #rss > li {
        padding: 5px;
        border-bottom: 2px solid #636363;
    }

    #rss > li > a {
        font-size: 14px;
        font-weight: normal;
    }

    .titleBarPane > a {
        padding: 6px 15px !important;
    }

    @media only screen and (max-width: 767px) {
        .titleBarPane > a {
            padding: 6px 6px !important;
        }
    }

</style>

<Div class="col-xs-12">
    <div class="col-md-2 hiddenOnMobile"></div>
    <div class="col-md-4 col-md-push-6 col-xs-12 sliderContainer" style="overflow: hidden; border: 2px solid black; padding: 0; min-height: 300px; border-radius: 6px">
        <div style="width: 100%; padding-top: 100%; position: relative">
            <?php $left = 55 - 5 * count($sliders); $counter = 0; ?>
            @foreach($sliders as $slider)
                @if(!empty($slider->link))
                    <div class="sliders" id="slider_{{$counter}}" onclick="document.location.href = '{{$slider->link}}'" style="left: 100%; background: url('{{$slider->pic}}'); background-size: 100% 100%; background-repeat: no-repeat no-repeat; cursor: pointer; width: 100%; height: 100%; position: absolute; top: 0;"></div>
                @else
                    <div class="sliders" id="slider_{{$counter}}" style="left: 100%; background: url('{{$slider->pic}}'); background-size: 100% 100%; background-repeat: no-repeat no-repeat; width: 100%; height: 100%; position: absolute; top: 0;"></div>
                @endif
                <?php $counter++; ?>
            @endforeach

            <div class="fa fa-chevron-right" onclick="next()" style="color: white; position: absolute; top: 45%; cursor: pointer; right: 2%; z-index: 100; font-size: 30px"></div>
            <div class="fa fa-chevron-left" onclick="back()" style="color: white; position: absolute; top: 45%; cursor: pointer; z-index: 100; left: 2%; font-size: 30px"></div>
            <div class="sliderBtnsContainer">
                @for($i = 0; $i < count($sliders); $i++)
                    @if($i == 0)
                        <center onclick="JMP('{{$i}}')" class="sliderBtns" id="slider_btn_{{$i}}" style="cursor: pointer; background-color: white; border-radius: 50%; width: 12px; height: 12px; float: left; margin-right: 8px;"></center>
                    @else
                        <center onclick="JMP('{{$i}}')" class="sliderBtns" id="slider_btn_{{$i}}" style="cursor: pointer; background-color: rgb(114, 112, 115); border-radius: 50%; width: 12px; height: 12px; float: left; margin-right: 8px;"></center>
                    @endif
                @endfor
            </div>
        </div>
    </div>
    <div class="col-md-6 col-md-pull-4 col-xs-12 rssContainer">
        <div class="titleBarPane">
            <a id="gach" onclick="changeTitle('gach')" class="titleBar focus">اخبار گچ سفید</a>
            <a id="irysc" onclick="changeTitle('irysc')" class="titleBar">اخبار آیریسک</a>
        </div>
        <div id="rss" style="background-color: white; height: 250px; border-radius: 6px; border-top: 0; overflow: auto">
        </div>
    </div>
</Div>

{{--<script>--}}

    {{--window.chage--}}

    {{--x1 = $('.sliderContainer').css('width').split("px");--}}
    {{--if(x1.length != 2)--}}
        {{--x1 = $('.sliderContainer').css('width').split("%")[0] / 100 * window.innerWidth;--}}
    {{--else--}}
        {{--x1 = x1[0];--}}

    {{--alert(x1);--}}

    {{--$('.sliderContainer').css('height', x1 + 'px');--}}
{{--</script>--}}