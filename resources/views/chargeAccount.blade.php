@extends('layouts.form')

@section('head')
    @parent
    <link rel="stylesheet" href="{{URL::asset('css/chargeCSS.css')}}">

@stop


@section('caption')
    <div class="title">شارژ حساب کاربری
    </div>
@stop

@section('main')
    <center style="margin-top: 10px">
        <h4>با شارژ حساب خود ، دیگر نیازی به وارد کردن اطلاعات کارت بانکی نخواهید داشت.</h4>
        <div class="btn btn-primary" style="margin: 15px;">میزان اعتبار فعلی {{$total}} تومان</div>
        <div style="margin: 15px;">
            <label for="requestedAmount"><span>مبلغ مورد نظر (تومان)</span></label>
            <br>
            <input class="btn btn-primary" id="requestedAmount" placeholder="... تومان">
        </div>
        <div style="margin: 15px;">
            <div class="btn-group" id="level">
                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" style="border-radius: 4px 0px 0px 4px;">
                    <span class="caret"></span>
                </button>
                <button type="button" class="btn btn-primary" style="border-radius: 0px 4px 4px 0px;">مبالغ پیشنهادی</button>
                <ul class="dropdown-menu" role="menu" style="right: 0 !important; text-align: right !important;">
                    <li><a href="#" data-val="2000" class="suggestionBox">2000 تومان</a></li>
                    <li><a href="#" data-val="5000" class="suggestionBox">5000 تومان</a></li>
                    <li><a href="#" data-val="10000" class="suggestionBox">10000 تومان</a></li>
                    <li><a href="#" data-val="20000" class="suggestionBox">20000 تومان</a></li>
                </ul>
            </div>
        </div>
        <div style="margin: 15px;">
            <center>
                <button onclick="showGiftPane()" class="btn btn-primary">تایید و انتقال به صفحه ی پرداخت</button>
            </center>
        </div>

        <div style="margin-top: 15px">
            <p id="msgText" class="errorText"></p>
        </div>

        {{--<div style="margin: 15px;">--}}
            {{--<center style="max-width: 400px; background-color: #ccc; border-radius: 6px; padding: 10px">--}}
                {{--<h3 style="color: #286090"><span style="margin-left: 5px" class="glyphicon glyphicon-gift"></span>--}}
                    {{--<span style="cursor: pointer" onclick="showGiftPane()">شارژ حساب کاربری توسط کد هدیه</span>--}}
                {{--</h3>--}}
            {{--</center>--}}
        {{--</div>--}}
    </center>

    <script>

        var chargeWithGiftCardDir = '{{route('chargeWithGiftCard')}}';
        var chargeAccountDir = '{{route('chargeAccount')}}';

        $(".suggestionBox").click(function () {
            $("#requestedAmount").val($(this).attr('data-val'));
        });

        $(document).ready(function () {

            @if($status == "finish")
                $("#msgText").empty().append('حساب شما با موفقیت شارژ شد');
            @elseif($status == 'err')
                $("#msgText").empty().append('خطایی در انجام تراکنش مورد نظر رخ داده است');
            @endif

        });

        function chargeWithGiftCard() {

            if($("#giftCode").val() == "")
                return doCharge("");

            $.ajax({
                type: 'post',
                url: chargeWithGiftCardDir,
                data: {
                    'giftCode': $("#giftCode").val()
                },
                success: function (response) {
                    if(response == "ok") {
                        doCharge($("#giftCode").val());
                    }
                    else if(response == "nok1") {
                        $("#errMsg").empty().append('کد مورد نظر معتبر نمی باشد');
                    }
                }
            });
        }
        
        function hideElement() {
            $(".item").addClass('hidden');
        }


        function postRefId (refIdValue) {
            var form = document.createElement("form");
            form.setAttribute("method", "POST");
            form.setAttribute("action", "https://bpm.shaparak.ir/pgwchannel/startpay.mellat");
            form.setAttribute("target", "_self");
            var hiddenField = document.createElement("input");
            hiddenField.setAttribute("name", "RefId");
            hiddenField.setAttribute("value", refIdValue);
            form.appendChild(hiddenField);

            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        }

        function doCharge(gift) {

            $.ajax({
                type: 'post',
                url: '{{route('doChargeAccount')}}',
                data: {
                    'amount': $("#requestedAmount").val(),
                    'giftCode': gift
                },
                success: function (response) {

                    response = JSON.parse(response);

                    if(response.status == "ok2") {
                        document.location.href = chargeAccountDir;
                    }
                    else if(response.status == "ok") {
                        postRefId(response.refId);
                    }
                    else {
                        $("#errMsg").empty().append("خطایی در پرداخت رخ داده است");
                    }
                }
            });

        }

        function showGiftPane() {

            if($("#requestedAmount").val() == "" || $("#requestedAmount").val() == 0)
                return;

            hideElement();
            $("#errMsg").empty();
            $("#giftCode").val("");
            $('#giftCardPane').removeClass('hidden');
        }

    </script>

    <span id="giftCardPane" class="ui_overlay item hidden" style="position: fixed; left: 30%; right: 30%; top: 170px; bottom: auto">
        <div class="header_text">آیا کد هدیه دارید؟</div>
        <div onclick="hideElement()" class="ui_close_x"></div>
        <div class="body_text">
            <div class="col-xs-12" style="margin-top: 10px">
                <input id="giftCode" type="text" style="width: 100%; padding: 5px" placeholder="اگر کد هدیه دارید در اینجا وارد نمایید" maxlength="40">
            </div>
            <div class="col-xs-12" style="margin-top: 10px">
                <center>
                    <button onclick="chargeWithGiftCard()" class="btn btn-primary">
ادامه
                    </button>
                    <p style="margin-top: 5px" class="errorText" id="errMsg"></p>
                </center>
            </div>
        </div>
    </span>

@stop