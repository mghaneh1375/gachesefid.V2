@extends('layouts.form')

@section('head')
    @parent

    <style>
        td {
            padding: 10px;
        }
        .checked {
            color: #4DC7BC;
        }
        .fa-star {
            cursor: pointer;
        }
    </style>
@stop


@section('caption')
    <div class="title">مشاور من
    </div>
@stop

@section('main')

    <div class="row">
        @if($myAdvisers == null || count($myAdvisers) == 0)
            <center style="padding: 10px">
                <p class="errorText">مشاوری برای شما ثبت نشده است</p>
                <a href="{{route('advisersList')}}" class="btn btn-primary">رفتن به صفحه لیست مشاوران</a>
            </center>
        @else
            @foreach($myAdvisers as $myAdviser)
                <center class="col-xs-12">
                    <table>
                        <tr>
                            <td onclick="document.location.href = '{{route('adviserInfo', ['adviserId' => $myAdviser->id])}}'" style="cursor: pointer"><center>{{$myAdviser->firstName . " " . $myAdviser->lastName}}</center></td>
                        </tr>
                    </table>
                    @if(!$myAdviser->status)
                        <div><center>درخواست شما برای مشاور ارسال شده است و باید منتظر رای مشاور باشید</center></div>
                    @else

                        @foreach($myAdviser->questions as $question)
                            <div class="adviserDiv" data-questionId="{{$question->id}}" data-id="{{$myAdviser->id . '_' . $question->id}}" data-rate="{{$question->rate}}" style="direction: ltr; font-size: 30px; width: fit-content">
                                <span data-id="{{$myAdviser->id . '_' . $question->id}}" onmousemove="rate(1, '{{$myAdviser->id . '_' . $question->id}}')" class="adviser_{{$myAdviser->id . '_' . $question->id}} fa fa-star rate1 rate2 rate3 rate4 rate5"></span>
                                <span data-id="{{$myAdviser->id . '_' . $question->id}}" onmousemove="rate(2, '{{$myAdviser->id . '_' . $question->id}}')" class="adviser_{{$myAdviser->id . '_' . $question->id}} fa fa-star rate2 rate3 rate4 rate5"></span>
                                <span data-id="{{$myAdviser->id . '_' . $question->id}}" onmousemove="rate(3, '{{$myAdviser->id . '_' . $question->id}}')" class="adviser_{{$myAdviser->id . '_' . $question->id}} fa fa-star rate3 rate4 rate5"></span>
                                <span data-id="{{$myAdviser->id . '_' . $question->id}}" onmousemove="rate(4, '{{$myAdviser->id . '_' . $question->id}}')" class="adviser_{{$myAdviser->id . '_' . $question->id}} fa fa-star rate4 rate5"></span>
                                <span data-id="{{$myAdviser->id . '_' . $question->id}}" onmousemove="rate(5, '{{$myAdviser->id . '_' . $question->id}}')" class="adviser_{{$myAdviser->id . '_' . $question->id}} fa fa-star rate5"></span>
                                <span>{{$question->name}}</span>
                            </div>
                        @endforeach

                        <button onclick="document.location.href = '{{route('sendMessage', ['dest' => $myAdviser->id])}}'" class="btn btn-success">پیام به مشاور</button>
                        <button onclick="document.location.href = '{{route('showInboxSpecificMsgs', ['selectedUser' => $myAdviser->id])}}'" class="btn btn-info">پیام های ارسالی به مشاور</button>
                        <button onclick="document.location.href = '{{route('showOutboxSpecificMsgs', ['selectedUser' => $myAdviser->id])}}'" class="btn btn-warning">پیام های دریافتی از مشاور</button>
                        <button onclick="document.location.href = '{{route('cancelAdviser', ['adviserId' => $myAdviser->id])}}'" class="btn btn-danger">لغو مشاور</button>
                    @endif

                    <button onclick="document.location.href = '{{route('cancelAdviser', ['adviserId' => $myAdviser->id])}}'" class="btn btn-danger">لغو مشاور</button>
                </center>
            @endforeach
        @endif
    </div>

    <script>

        var selectedRate = -1;
        var updatedVal = -1;
        var oldDiv = -1;

        $(document).ready(function () {

            $(".adviserDiv").each(function () {
                rate($(this).attr('data-rate'), $(this).attr('data-id'));
            });
        });

        function rate(val, id) {
            if(oldDiv != id)
                selectedRate = -1;
            updatedVal = val;
            $(".adviser_" + id + ".fa-star").removeClass('checked').filter(".rate" + val).addClass('checked');
        }

        $('.fa-star').click(function () {
            selectedRate = updatedVal;
            $(this).parent().attr('data-rate', selectedRate);
            $.ajax({
                type: 'post',
                url: '{{route('submitRate')}}',
                data: {
                    'rate': selectedRate,
                    'adviserId': $(this).attr('data-id'),
                    'studentId': '{{Auth::user()->id}}',
                    'questionId': $(this).parent().attr('data-questionId')
                }
            });
        });
        
        $(".adviserDiv").mouseleave(function () {
            if(selectedRate == -1) {
                if($(this).attr('data-rate') == -1)
                    $(".adviser_" + $(this).attr('data-id') +".fa-star").removeClass('checked');
                else
                    rate($(this).attr('data-rate'), $(this).attr('data-id'));
            }
            else
                rate(selectedRate, $(this).attr('data-id'));
        });

    </script>
@stop