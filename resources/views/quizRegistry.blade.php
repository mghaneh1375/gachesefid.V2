@extends('layouts.form')

@section('head')
    @parent
    <style>
        td {
            padding: 10px;
        }
    </style>
@stop

@section('caption')
    @if($mode == 'system')
        <div class="title">چالش سرعتیِ پای تخته</div>
    @else
        <div class="title">پشت میزت محکم بشین و امتحان بده</div>
    @endif
@stop

@section('main')

    <style>

        .color1 {
            background-color: #67e6dc;
            padding: 5px;
        }
        .color2 {
            background-color: #18dcff;
            padding: 5px;
        }

    </style>

    <center class="row" style="margin: 50px">
        @if(count($composes) == 0)
            <div class="col-xs-12">
                <center>آزمونی برای ثبت نام وجود ندارد</center>
            </div>
        @elseif($mode == 'system')

            <?php $i = 1; ?>
            @foreach($composes as $compose)

                <div class="{{ 'color' . ($i % 5)  }}">

                    <center style="font-size: 20px; font-weight: 500">
                        <p><span>نام بسته: </span><span>{{$compose->name}}</span></p>
                        <p><span>هرینه کل بسته: </span><span>{{$compose->totalPrice}} تومان</span></p>
                        <span>
                            <button onclick="document.location.href = '{{route('doComposeQuizRegistry', ['composeId' => $compose->id])}}'" class="btn btn-primary">ثبت نام در تمامی موارد
                            </button>
                        </span>
                    </center>
                    <table>
                        <tr>
                            <td></td>
                            <td><center>نام آزمون:</center></td>
                            <td><center>تاریخ برگزاری:</center></td>
                            <td><center>ساعت برگزاری:</center></td>
                            <td><center>تاریخ شروع ثبت نام:</center></td>
                            <td><center>تاریخ اتمام ثبت نام:</center></td>
                            <td><center>هزینه آزمون:</center></td>
                        </tr>

                        @foreach($compose->registerable as $quiz)
                            <tr class="{{ 'color' . ($i % 5)  }}">
                                <td></td>
                                <td><center>{{$quiz->name}}</center></td>
                                <td><center>{{$quiz->startDate}}</center></td>
                                <td><center>{{$quiz->startTime}}</center></td>
                                <td><center>{{$quiz->startReg}}</center></td>
                                <td><center>{{$quiz->endReg}}</center></td>
                                <td><center>{{$quiz->price}} تومان</center></td>
                                <td>
                                    <center>
                                        <button onclick="document.location.href = '{{route('doQuizRegistry', ['quizId' => $quiz->id, 'mode' => 'system'])}}'" class="btn btn-primary">ثبت نام در آزمون</button>
                                        <input onclick="calcTotal()" value="{{$quiz->price}}" type="checkbox" name="selectedQuiz">
                                    </center>
                                </td>
                            </tr>

                        @endforeach

                    </table>
                    <?php $i++; ?>

                </div>
            @endforeach
        @else

            <?php $i = 1; ?>
            @foreach($composes as $compose)

                <div class="{{ 'color' . ($i % 5)  }}">

                    <center style="font-size: 20px; font-weight: 500">
                        <p><span>نام بسته: </span><span>{{$compose->name}}</span></p>
                        <p><span>هرینه کل بسته: </span><span>{{$compose->totalPrice}} تومان</span></p>
                        <span>
                            <button onclick="document.location.href = '{{route('doComposeQuizRegistry', ['composeId' => $compose->id])}}'" class="btn btn-primary">ثبت نام در تمامی موارد
                            </button>
                        </span>
                    </center>
                    <table>
                        <tr>
                            <td><center>نام آزمون:</center></td>
                            <td><center>تاریخ برگزاری:</center></td>
                            <td><center>تاریخ اتمام:</center></td>
                            <td><center>ساعت برگزاری:</center></td>
                            <td><center>ساعت اتمام:</center></td>
                            <td><center>تاریخ شروع ثبت نام:</center></td>
                            <td><center>تاریخ اتمام ثبت نام:</center></td>
                            <td><center>هزینه آزمون:</center></td>
                        </tr>

                        @foreach($compose->registerable as $quiz)
                            <tr class="{{ 'color' . ($i % 5)  }}">
                                <td><center>{{$quiz->name}}</center></td>
                                <td><center>{{$quiz->startDate}}</center></td>
                                <td><center>{{$quiz->endDate}}</center></td>
                                <td><center>{{$quiz->startTime}}</center></td>
                                <td><center>{{$quiz->endTime}}</center></td>
                                <td><center>{{$quiz->startReg}}</center></td>
                                <td><center>{{$quiz->endReg}}</center></td>
                                <td><center>{{$quiz->price}} تومان</center></td>
                                <td>
                                    <center>
                                        <button onclick="document.location.href = '{{route('doQuizRegistry', ['quizId' => $quiz->id, 'mode' => 'regular'])}}'" class="btn btn-primary">ثبت نام در آزمون</button>
                                        <input onclick="calcTotal()" value="{{$quiz->price}}" name="selectedQuiz" type="checkbox">
                                    </center>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            @endforeach
        @endif

        @if(count($composes) > 0)
            <center style="padding: 5px"><p><span>جمع کل: </span><span id="totalSum">0</span></p></center>
        @endif
    </center>

    <script>

        var percentOfQuizes = '{{$percentOfQuizes}}';

        $(".quiz").mouseenter(function () {
            val = $(this).attr('data-val');

            $(".quiz").css('background-color', '#ccc').css('border-color', 'black');

            $("#" + val).css('background-color', '#fafef5').css('border-color', '#7ed321');

        });

        $(".quiz").mouseleave(function () {
            $(".quiz").css('background-color', '#ccc').css('border-color', 'black');
        });
        
        function calcTotal() {

            var sum = 0;
            var counter = 0;
            var initPrice = 0;

            $("input:checkbox[name=selectedQuiz]:checked").each(function () {
                sum += ($(this).val() * (100 - percentOfQuizes) / 100);
                initPrice = $(this).val();
                counter++;
            });

            if(counter == 1)
                sum += (initPrice * (percentOfQuizes) / 100);

            $("#totalSum").empty().append(sum);
        }
        
    </script>
@stop