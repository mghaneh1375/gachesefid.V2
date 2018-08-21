@extends('layouts.form')

@section('head')
    @parent
@stop

@section('caption')
    <div class="title">تکمیل فرآیند خرید</div>
@stop

@section('main')
    <center style="margin-top: 50px">

        <div class="col-xs-12" <?php echo ($toPay <= $total) ? "onclick='doChangePayment(\"myAccount\")' id='myAccountDiv'" : ""?> style="cursor: pointer; border: 3px solid; background-color: #ccc">
            <div class="col-xs-3">
                <p><span>موجودی حساب : </span><span>{{$total}}</span><span> تومان</span></p>
                <div style="margin-top: 5px" id="offCode1"></div>
            </div>
            <div class="col-xs-8">
                <p style="font-weight: bolder; font-size: 20px">پرداخت از حساب کاربری</p>
                <p>با شارژ حساب کاربری خود سریعتر و با اطمینان بیشتر خرید خود را انجام دهید</p>
            </div>
            <div class="col-xs-1">
                @if($toPay > $total)
                    <input type="radio" onchange="changePayment()" value="myAccount" name="fromWhere" disabled>
                @else
                    <input type="radio" onchange="changePayment()" value="myAccount" name="fromWhere">
                @endif
            </div>
        </div>

        <div class="col-xs-12" onclick="doChangePayment('online')" id="onlineDiv" style="margin-top: 10px; cursor: pointer; border: 3px solid; background-color: #ccc">
            <div class="col-xs-3">
                <div style="margin-top: 5px" id="offCode2"></div>
            </div>
            <div class="col-xs-8">
                <p style="font-weight: bolder; font-size: 20px">پرداخت اینترنتی</p>
                <p>می توانید با تمامی کارت های عضو شتاب کل هزینه را پرداخت کنید</p>
            </div>
            <div class="col-xs-1">
                <input type="radio" value="online" onchange="changePayment()" name="fromWhere" checked>
            </div>
        </div>

        <div class="col-xs-12" style="margin-top: 10px">
            <center>
                <h4><span>جمع کل:&nbsp;&nbsp;&nbsp;</span><span id="toPay">{{$toPay}}&nbsp;</span><span>تومان</span></h4>
                <h4><span>موجودی حساب:&nbsp;&nbsp;&nbsp;</span><span id="myCharge">{{$total}}&nbsp;</span><span>تومان</span></h4>
                <h4><span>قابل پرداخت:&nbsp;&nbsp;&nbsp;</span><span id="toPay2">{{($total > $toPay) ? 0 : $toPay - $total}}&nbsp;</span><span>تومان</span></h4>
            </center>
        </div>

        <div class="col-xs-12" style="margin-top: 10px">
            <center>
                <button onclick="document.location.href = '{{$backURL}}'" class="btn btn-primary">بازگشت به مرحله ی قبل</button>
                <button onclick="endPayment()" class="btn btn-primary">تایید و هدایت به صفحه ی پرداخت</button>
                <p id="errMsg" style="margin-top: 10px" class="errorText"></p>
            </center>
        </div>
    </center>

    <span id="confirmationPane" class="ui_overlay item hidden" style="position: fixed; left: 30%; right: 30%; top: 170px; bottom: auto">
        <div class="header_text">وضعیت خرید</div>
        <div class="body_text">
            <h5>خرید شما با موفقیت انجام پذیرفت</h5>
            <div class="col-xs-12" style="margin-top: 10px">
                <center>
                    <button onclick="document.location.href= '{{$url}}'" class="btn btn-primary">
برای ادامه کلیک کنید
                    </button>
                </center>
            </div>
        </div>
    </span>

    <span id="giftCardPane" class="ui_overlay item hidden" style="position: fixed; left: 30%; right: 30%; top: 170px; bottom: auto">
        <div class="header_text">شارژ حساب کاربری توسط کد هدیه</div>
        <div onclick="hideElement()" class="ui_close_x"></div>
        <div class="body_text">
            <div class="col-xs-12" style="margin-top: 10px">
                <input id="giftCode" type="text" placeholder="کد هدیه ی خود را وارد نمایید" maxlength="40">
            </div>
            <div class="col-xs-12" style="margin-top: 10px">
                <center>
                    <button onclick="checkGiftCard()" class="btn btn-primary">
                        ثبت
                    </button>
                    <p style="margin-top: 5px" class="errorText" id="errMsgGift"></p>
                </center>
            </div>
        </div>
    </span>

    <script>

        var checkGiftCardDir = '{{route('checkGiftCard')}}';
        var offCode;
        var total = '{{$total}}';
        total = parseInt(total);

        function doChangePayment(val) {
            $("input[name='fromWhere'][value=" + val +"]").prop('checked', 'checked');
            changePayment();
        }

        function changePayment() {

            val = $("input[name='fromWhere']:checked").val();
            if(val == "online") {
                $("#onlineDiv").css('background-color', '#fafef5');
                $("#onlineDiv").css('border-color', '#7ed321');
                $("#myAccountDiv").css('background-color', '#ccc');
                $("#myAccountDiv").css('border-color', 'black');
                $("#offCode1").empty();
                $("#offCode2").empty().append(offCode);
            }
            else {
                $("#myAccountDiv").css('background-color', '#fafef5');
                $("#myAccountDiv").css('border-color', '#7ed321');
                $("#onlineDiv").css('background-color', '#ccc');
                $("#onlineDiv").css('border-color', 'black');
                $("#offCode2").empty();
                $("#offCode1").empty().append(offCode);
            }
        }

        $(document).ready(function () {


            @if($status == "finish")
                hideElement();
            $("#confirmationPane").removeClass('hidden');
            @elseif($status == "err")
                $("#errMsg").empty().append("خطایی در پرداخت رخ داده است");
            @endif

                    offCode = "<h5 style='cursor: pointer; color: red' onclick='showGiftPane()'>کد تخفیف دارید؟</h5>";
            changePayment();
        });

        function endPayment() {

            val = $("input[name='fromWhere']:checked").val();

            if(val == "myAccount") {
                $.ajax({
                    type: 'post',
                    url: '{{$payURL}}',
                    data: {
                        'giftCode': $("#giftCode").val(),
                        'quizId': '{{$quizId}}'
                    },
                    success: function (response) {
                        response = JSON.parse(response);
                        if(response.status == "ok") {
                            hideElement();
                            $("#confirmationPane").removeClass('hidden');
                        }
                        else if(response.status == "nok1") {
                            $("#errMsg").empty().append("موجودی شما کافی نیست");
                        }
                        else if(response.status == "nok2") {
                            $("#errMsg").empty().append("شما قبلا این آزمون را خریداری کرده اید");
                        }
                        else if(response.status == "nok5" || response.status == "nok3" || response.status == "nok6") {
                            $("#errMsg").empty().append("اشکالی در انجام عملیات مورد نظر رخ داده است");
                        }
                    }
                });
            }
            else {
                $.ajax({
                    type: 'post',
                    url: '{{$payURL2}}',
                    data: {
                        'giftCode': $("#giftCode").val(),
                        'quizId': '{{$quizId}}'
                    },
                    success: function (response) {

                        response = JSON.parse(response);

                        if(response.status == "ok2") {
                            hideElement();
                            $("#confirmationPane").removeClass('hidden');
                        }
                        else if(response.status == "ok") {
                            postRefId(response.refId);
                        }
                        else if(response.status == "nok1") {
                            $("#errMsg").empty().append("خطایی در پرداخت رخ داده است");
                        }
                    }
                });
            }
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

        function checkGiftCard() {

            if($("#giftCode").val() == "")
                return;

            $.ajax({
                type: 'post',
                url: checkGiftCardDir,
                data: {
                    'giftCode': $("#giftCode").val(),
                    'total': '{{$toPay}}'
                },
                success: function (response) {

                    response = JSON.parse(response);

                    if(response.status == "ok") {
                        $("#toPay").empty().append(response.total + "&nbsp;");
                        if(response.total < total)
                            $("#toPay2").empty().append("0&nbsp;");
                        else
                            $("#toPay2").empty().append(response.total - total + "&nbsp;");

                        $("#errMsgGift").empty().append('کد مورد نظر اعمال شد');
                    }
                    else if(response.status == "nok") {
                        $("#errMsgGift").empty().append('کد مورد نظر معتبر نمی باشد');
                    }
                }
            });
        }

        function hideElement() {
            $(".item").addClass('hidden');
        }

        function showGiftPane() {
            hideElement();
            $("#errMsgGift").empty();
            $("#giftCode").val("");
            $('#giftCardPane').removeClass('hidden');
        }

        function showConfirmationPane() {
            hideElement();
            $("#confirmationPane").removeClass('hidden');
        }

    </script>
@stop