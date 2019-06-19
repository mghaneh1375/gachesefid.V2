@extends('layouts.form')

@section('head')
    @parent
    <script src="{{URL::asset('js/jsNeededForQuestionInfo.js')}}"></script>
    <script>
        var limit = '{{count($questions)}}';
        var getQuestionInfo = '{{route('questionInfo')}}';
        var likeQuestionDir = '{{route('likeQuestion')}}';
    </script>

    <style>
        .fa-heart {
            color: red;
        }
    </style>
@stop

@section('main')
    <div class="col-xs-12">

        <div class="col-xs-12">
            <center style="margin-top: 20px;">
                <table style="min-width: 100px;">
                    <?php
                    $counter = 0;
                    $numQ = count($questions);
                    for($i = 0; $i < $numQ; $i++) {
                        if($counter == 0)
                            echo "<tr>";
                        $counter++;
                        echo "<td data-val='" . $questions[$i]->questionId . "' id='td_$i' onclick='JUMP($i)' style='cursor: pointer; background-color: white; width: 30px; border: 2px solid black;'><center>".($i + 1)."</center></td>";
                        if($counter == 15 || $i == $numQ - 1) {
                            echo "</tr>";
                            $counter = 0;
                        }
                    }
                    ?>
                </table>
            </center>
        </div>

        <div class="col-xs-12" style="margin-top: 20px">
            <div class="col-xs-1" id="likeDiv">
            </div>
            <div class="col-xs-9">
                <div style="min-height: 50vh; border: 2px solid black; border-radius: 6px">
                    <div class="col-xs-12" style="min-height: 45vh" id="questionPane"></div>
                    <div class="col-xs-12" id="subQInfo" style="border: 2px solid #ccc; border-radius: 6px; min-height: 3vh"></div>
                    <p id="msg" style="position: absolute; top: 25vh"></p>
                    <button id="prevQ" onclick="prevQ()" style="position: absolute; left: 0; top: 25vh">قبلی</button>
                    <button id="nextQ" onclick="nextQ()" style="position: absolute; right: 0; top: 25vh">بعدی</button>
                </div>

                {{--<div style="margin-top: 30px">--}}
                    {{--<center>--}}
                        {{--<button id="discussion" data-val="" onclick="goToDiscussionRoom()">ورود به تالار گفتمان</button>--}}
                    {{--</center>--}}
                {{--</div>--}}
            </div>
            <div class="col-xs-2" style="margin-top: 50px">
                <div class="col-xs-12">
                    <p><span id="likesNo"></span><span>&nbsp;&nbsp;&nbsp;<i class="fa fa-thumbs-up" aria-hidden="true"></i></span></p>
                    <p><span>تعداد پاسخ گویی:&nbsp;&nbsp;</span><span id="totalAns"></span></p>
                    <p><span>تعداد جواب صحیح:&nbsp;&nbsp;</span><span id="correctNo"></span></p>
                    <p><span>تعداد جواب ناصحیح:&nbsp;&nbsp;</span><span id="incorrectNo"></span></p>
                    <p><span>تعداد جواب بدون پاسخ:&nbsp;&nbsp;</span><span id="whiteNo"></span></p>
                    <p><span>درصد پاسخ گویی:&nbsp;&nbsp;</span><span id="percent"></span></p>
                    <p><span>سطح سختی:&nbsp;&nbsp;</span><span id="qLevel"></span></p>
                    <p><span>ناظر:&nbsp;&nbsp;</span><span id="controller"></span></p>
                    <p><span>طراح:&nbsp;&nbsp;</span><span id="author"></span></p>
                </div>
            </div>
        </div>
    </div>
@stop