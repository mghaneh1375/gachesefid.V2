@extends('layouts.form')

@section('head')
    @parent
    <script src = {{URL::asset("js/calendar.js") }}></script>
    <script src = {{URL::asset("js/calendar-setup.js") }}></script>
    <script src = {{URL::asset("js/calendar-fa.js") }}></script>
    <script src = {{URL::asset("js/jalali.js") }}></script>
    <link rel="stylesheet" href = {{URL::asset("css/calendar-green.css") }}>
    <script>
        var groupRegistry = '{{getValueInfo('regularQuizGroupTransaction')}}';
        var regularRegistry = '{{getValueInfo('regularQuizTransaction')}}';
        var systemRegistry = '{{getValueInfo('systemQuizTransaction')}}';
        var charge = '{{getValueInfo('chargeTransaction')}}';
    </script>
    <style>
        .calendar {
            z-index: 100001 !important;
        }
    </style>
    
    <script>

        function doFilter() {

            var sum = 0;
            var counter = 0;

            var startDate = $("#date_input").val();
            var endDate = $("#end_date_input").val();
            var groupRegistryStatus = ($("#groupRegistry").is(":checked")) ? true : false;
            var regularRegistryStatus = ($("#regularRegistry").is(":checked")) ? true : false;
            var systemRegistryStatus = ($("#systemRegistry").is(":checked")) ? true : false;
            var chargeStatus = ($("#charge").is(":checked")) ? true : false;

            $(".transaction").addClass('hidden');

            if(endDate != "" && startDate != "") {

                $(".transaction").each(function () {

                   if($(this).attr('data-date') >= startDate && $(this).attr('data-date') <= endDate) {

                       if((groupRegistryStatus &&
                           $(this).attr('data-category') == groupRegistry) ||
                           (regularRegistryStatus &&
                           $(this).attr('data-category') == regularRegistry) ||
                           (systemRegistryStatus &&
                           $(this).attr('data-category') == systemRegistry) ||
                           (chargeStatus &&
                           $(this).attr('data-category') == charge)
                       ) {
                           sum += parseInt($(this).attr('data-amount'));
                           $(this).removeClass('hidden');
                       }
                   }
                });
            }

            else {

                $(".transaction").each(function () {

                    if (($("#groupRegistry").is(":checked") &&
                            $(this).attr('data-category') == groupRegistry) ||
                            ($("#regularRegistry").is(":checked") &&
                            $(this).attr('data-category') == regularRegistry) ||
                            ($("#systemRegistry").is(":checked") &&
                            $(this).attr('data-category') == systemRegistry) ||
                            ($("#charge").is(":checked") &&
                            $(this).attr('data-category') == charge)
                    ) {
                        sum += parseInt($(this).attr('data-amount'));
                        $(this).removeClass('hidden');
                    }

                });
            }

            $("#totalSum").empty().append(sum);
//
//            if($("#groupRegistry").is(":checked")) {
//                $(".transaction[data-category=" + groupRegistry + "]").removeClass('hidden');
//            }
//            if($("#regularRegistry").is(":checked")) {
//                $(".transaction[data-category=" + regularRegistry + "]").removeClass('hidden');
//            }
//            if($("#systemRegistry").is(":checked")) {
//                $(".transaction[data-category=" + systemRegistry + "]").removeClass('hidden');
//            }
        }
    </script>
@stop


@section('caption')
    <div class="title">گزارش گیری مالی
    </div>
@stop

@section('main')

    <style>
        td {
            padding: 10px;
        }
    </style>

    <?php $sum = 0; ?>

    <center style="margin-top: 10px">
        <center class="btn btn-success" onclick="$('#filterPane').removeClass('hidden')">اعمال فیلتر</center>
        <table style="padding: 10px">
            <tr>
                <td><center>تاریخ</center></td>
                <td><center>نام پرداخت کننده</center></td>
                <td><center>مقدار</center></td>
                <td><center>نوع تراکنش</center></td>
            </tr>

            @foreach($transactions as $itr)
                <tr class="transaction" data-amount="{{($itr->amount < 0) ? -$itr->amount : 0}}" data-date="{{$itr->date}}" data-category="{{$itr->kindTransactionId}}">
                    <td><center>{{$itr->date}}</center></td>
                    <td><center>{{$itr->userId}}</center></td>
                    <td><center>{{-$itr->amount}}</center></td>
                    <td><center>{{$itr->kindTransaction}}</center></td>

                    <?php if($itr->amount < 0) $sum += (-$itr->amount) ?>
                </tr>
            @endforeach
        </table>

        {{ $transactions->links() }}

        <center><span>جمع کل:</span><span>&nbsp;</span><span id="totalSum"></span></center>
    </center>


    <span id="filterPane" class="ui_overlay item hidden" style="position: fixed; left: 20%; right: 20%; top: 40px; bottom: auto">
    <div class="header_text">نمایش آزمون</div>
    <div onclick="$('#filterPane').addClass('hidden')" class="ui_close_x"></div>
    <center class="body_text">
        <div class="col-xs-12" style="margin-top: 5px">
            <label>
                <span>از</span>
            </label>
            <input type="text" style="max-width: 200px" class="form-detail" id="date_input" readonly>
            <input type="button" style="border: none; width: 30px; height: 30px; background: url({{ URL::asset('images/calendar-flat.png') }}) repeat 0 0; background-size: 100% 100%;" id="date_btn">
            <script>
                Calendar.setup({
                    inputField: "date_input",
                    button: "date_btn",
                    ifFormat: "%Y/%m/%d",
                    dateType: "jalali"
                });
            </script>
        </div>

        <div class="col-xs-12" style="margin-top: 5px">
            <label>
                <span>تا</span>
            </label>
            <input type="text" style="max-width: 200px" class="form-detail" id="end_date_input" readonly>
            <input type="button" style="border: none; width: 30px; height: 30px; background: url({{ URL::asset('images/calendar-flat.png') }}) repeat 0 0; background-size: 100% 100%;" id="end_date_btn">
            <script>
                Calendar.setup({
                    inputField: "end_date_input",
                    button: "end_date_btn",
                    ifFormat: "%Y/%m/%d",
                    dateType: "jalali"
                });
            </script>
        </div>

        <div class="col-xs-12">
            <label for="groupRegistry">ثبت نام گروهی در سنجش پشت میز</label>
            <input id="groupRegistry" type="checkbox" checked>
        </div>
        <div class="col-xs-12">
            <label for="regularRegistry">ثبت نام در سنجش پشت میز</label>
            <input id="regularRegistry" type="checkbox" checked>
        </div>
        <div class="col-xs-12">
            <label for="systemRegistry">ثبت نام در سنجش پای تخته</label>
            <input id="systemRegistry" type="checkbox" checked>
        </div>
        <div class="col-xs-12">
            <label for="systemRegistry">شارژ حساب</label>
            <input id="charge" type="checkbox" checked>
        </div>

        <div class="col-xs-12">
            <input type="submit" value="تایید" class="btn btn-success" onclick="doFilter(); $('#filterPane').addClass('hidden')">
        </div>

    </center>
</span>

    <script>
        $(document).ready(function () {
            $("#totalSum").empty().append('{{$sum}}');
        });
    </script>
@stop