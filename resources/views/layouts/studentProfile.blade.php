
<script>

    var timeVal1, timeVal2, timeVal3, timeVal4, timeVal5, timeVal6;
    var money = '{{$money}}';
    var regularQuizNo = '{{$regularQuizNo}}';
    var systemQuizNo = '{{$systemQuizNo}}';
    var questionNo = '{{$questionNo}}';
    var rate = '{{$rate}}';
    var rank = '{{$rank}}';


    $(document).ready(function () {

        timeVal1 = Math.floor(1000 / money);
        countMoney(-1);

        timeVal2 = Math.floor(1000 / regularQuizNo);
        countRegularQuizNo(-1);

        timeVal4 = Math.floor(1000 / systemQuizNo);
        countSystemQuizNo(-1);

        timeVal3 = Math.floor(1000 / questionNo);
        countQuestionNo(-1);

        timeVal5 = Math.floor(1000 / rate);
        countRate(-1);

        if(rank != 0) {
            timeVal6 = Math.floor(1000 / rank);
            countRank(-1);
        }

    });

    function countQuestionNo(idx) {

        if(idx == questionNo)
            return;

        if(idx + 10 < questionNo) {
            idx += 10;
        }
        else
            idx++;

        $("#questionNo").empty().append(idx).persiaNumber();

        setTimeout("countQuestionNo(" + idx + ")", timeVal3);
    }

    function countRate(idx) {

        if(idx >= rate)
            return;

        if(idx + 10 < rate) {
            idx += 10;
        }
        else
            idx++;

        $("#rate").empty().append(idx).persiaNumber();

        setTimeout("countRate(" + idx + ")", timeVal5);
    }

    function countRank(idx) {

        if(idx == rank)
            return;

        if(idx + 10 < rank) {
            idx += 10;
        }
        else
            idx++;

        $("#rank").empty().append(idx).persiaNumber();

        setTimeout("countRank(" + idx + ")", timeVal6);
    }

    function countMoney(idx) {

        if(idx >= money) {
            $("#money").empty().append(money).persiaNumber();
            return;
        }

        if(idx + 10 < money) {
            idx += 10;
        }
        else
            idx++;

        $("#money").empty().append(idx).persiaNumber();

        setTimeout("countMoney(" + idx + ")", timeVal1);
    }

    function countRegularQuizNo(idx) {

        if(idx == regularQuizNo)
            return;

        if(idx + 10 < regularQuizNo) {
            idx += 10;
        }
        else
            idx++;

        $("#regularQuizNo").empty().append(idx).persiaNumber();

        setTimeout("countRegularQuizNo(" + idx + ")", timeVal2);
    }

    function countSystemQuizNo(idx) {

        if(idx == systemQuizNo)
            return;

        if(idx + 10 < systemQuizNo) {
            idx += 10;
        }
        else
            idx++;

        $("#systemQuizNo").empty().append(idx).persiaNumber();

        setTimeout("countSystemQuizNo(" + idx + ")", timeVal4);
    }

</script>

<style>

    .col-md-4 {
        height: 200px;
    }

    .textOfPhoto {
        font-weight: bolder;
        color: black;
        margin-top: -100px;
        font-size: 40px;
        margin-left: 30%;
    }

    @media only screen and (max-width:767px) {
        .textOfPhoto {
            margin-left: 0 !important;
        }
    }

</style>

<div class="col-xs-12">
    <div class="col-xs-12" style="margin-top: 100px">
        <div class="col-md-4 col-xs-12">
            <img style="width: 200px" src="{{URL::asset('images/u-future-exam.png')}}">
            <center class="textOfPhoto" id="systemQuizNo"></center>
        </div>
        <div class="col-md-4 col-xs-12">
            <img style="width: 200px" src="{{URL::asset('images/u-money.png')}}">
            <center class="textOfPhoto" id="money"></center>
        </div>
        <div class="col-md-4 col-xs-12">
            <img style="width: 200px" src="{{URL::asset('images/u-past-exam.png')}}">
            <center class="textOfPhoto" id="regularQuizNo"></center>
        </div>
    </div>
    <div class="col-xs-12">
        <div class="col-md-4 col-xs-12">
            <img style="width: 200px" src="{{URL::asset('images/u-question.png')}}">
            <center class="textOfPhoto" id="questionNo"></center>
        </div>
        <div class="col-md-4 col-xs-12">
            <img style="width: 200px" src="{{URL::asset('images/u-rank.png')}}">
            <center class="textOfPhoto" id="rate"></center>
        </div>
        <div class="col-md-4 col-xs-12">
            <img style="width: 200px" src="{{URL::asset('images/u-ranking.png')}}">
            <center class="textOfPhoto" id="rank"></center>
        </div>
    </div>
</div>