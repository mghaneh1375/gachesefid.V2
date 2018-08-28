
<script>

    var timeVal1, timeVal2, timeVal3, timeVal4, timeVal5, timeVal6;
    var money = '{{$money}}';
    var myQuizNo = '{{$myQuizNo}}';
    var nextQuizNo = '{{$nextQuizNo}}';
    var questionNo = '{{$questionNo}}';
    var rate = '{{$rate}}';
    var rank = '{{$rank}}';


    $(document).ready(function () {

        timeVal1 = (money > 0) ? Math.floor(money / 100) : 1;
        countMoney(-1);

        timeVal2 = (myQuizNo > 0) ? Math.floor(1000 / myQuizNo) : 1;
        countMyQuizNo(-1);

        timeVal4 = (nextQuizNo > 0) ? Math.floor(1000 / nextQuizNo) : 1;
        countNextQuizNo(-1);

        timeVal3 = (questionNo > 0) ? Math.floor(1000 / questionNo) : 1;
        countQuestionNo(-1);

        timeVal5 = (rate > 0) ? Math.floor(1000 / rate) : 1;
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

        if(idx == rank) {
            if(idx == -1)
                $("#rank").css('font-size', '12px').empty().append("<span>شما امتیازی در این</span><br/><span>بخش کسب نکرده اید.</span>");
            return;
        }

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

        if(idx + timeVal1 < money) {
            idx += timeVal1;
        }
        else
            idx++;

        $("#money").empty().append(idx).persiaNumber();

        setTimeout("countMoney(" + idx + ")", 10);
    }

    function countMyQuizNo(idx) {

        if(idx == myQuizNo)
            return;

        if(idx + 10 < myQuizNo) {
            idx += 10;
        }
        else
            idx++;

        $("#myQuizNo").empty().append(idx).persiaNumber();

        setTimeout("countMyQuizNo(" + idx + ")", timeVal2);
    }

    function countNextQuizNo(idx) {

        if(idx == nextQuizNo)
            return;

        if(idx + 10 < nextQuizNo) {
            idx += 10;
        }
        else
            idx++;

        $("#nextQuizNo").empty().append(idx).persiaNumber();

        setTimeout("countNextQuizNo(" + idx + ")", timeVal4);
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
        margin-left: -10%;
    }

    @media only screen and (max-width:767px) {
        .textOfPhoto {
            margin-left: 0 !important;
        }
    }

</style>

<div class="col-xs-12">
    <div class="col-xs-12" style="margin-top: 100px">
        <center class="col-md-4 col-xs-12">
            <img style="width: 230px" src="{{URL::asset('images/u-future-exam.png')}}">
            <center class="textOfPhoto" id="nextQuizNo"></center>
        </center>
        <center class="col-md-4 col-xs-12" style="cursor:pointer;" onclick="document.location.href = '{{route('chargeAccount')}}';">
            <img style="width: 230px" src="{{URL::asset('images/u-money.png')}}">
            <center class="textOfPhoto" id="money"></center>
        </center>
        <center class="col-md-4 col-xs-12" style="cursor:pointer;" onclick="document.location.href = '{{route('myQuizes')}}';">
            <img style="width: 230px" src="{{URL::asset('images/u-past-exam.png')}}">
            <center class="textOfPhoto" id="myQuizNo"></center>
        </center>
    </div>
    <div class="col-xs-12">
        <center class="col-md-4 col-xs-12">
            <img style="width: 230px" src="{{URL::asset('images/u-question.png')}}">
            <center class="textOfPhoto" id="questionNo"></center>
        </center>
        <center class="col-md-4 col-xs-12">
            <img style="width: 230px" src="{{URL::asset('images/u-rank.png')}}">
            <center class="textOfPhoto" id="rate"></center>
        </center>
        <center class="col-md-4 col-xs-12" style="cursor:pointer;" onclick="document.location.href = '{{route('studentsRanking', ['k' => 1, 'page' => 1])}}';">
            <img style="width: 230px" src="{{URL::asset('images/u-ranking.png')}}">
            <center class="textOfPhoto" id="rank"></center>
        </center>
    </div>
</div>