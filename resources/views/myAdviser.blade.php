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
        @if($myAdviser == null)
            <p class="errorText">مشاوری برای شما ثبت نشده است</p>
        @else
            <center class="col-xs-12">
                <table>
                    <tr>
                        <td><center>{{$myAdviser->firstName . " " . $myAdviser->lastName}}</center></td>
                        <td><center><span>میانگین امتیاز: </span><span>{{$avgRate}}</span></center></td>
                    </tr>
                </table>
                @if($rate == -2)
                    <div><center>درخواست شما برای مشاور ارسال شده است و باید منتظر رای مشاور باشید</center></div>
                @else
                    <div onmouseleave="cursorOutOfBand()" style="direction: ltr; font-size: 30px; width: fit-content">
                        <span onmousemove="rate(1)" class="fa fa-star rate1 rate2 rate3 rate4 rate5"></span>
                        <span onmousemove="rate(2)" class="fa fa-star rate2 rate3 rate4 rate5"></span>
                        <span onmousemove="rate(3)" class="fa fa-star rate3 rate4 rate5"></span>
                        <span onmousemove="rate(4)" class="fa fa-star rate4 rate5"></span>
                        <span onmousemove="rate(5)" class="fa fa-star rate5"></span>
                    </div>

                    <button onclick="document.location.href = '{{route('sendMessage', ['dest' => $myAdviser->id])}}'" class="btn btn-success">پیام به مشاور</button>

                    <button onclick="document.location.href = '{{route('showInboxSpecificMsgs', ['selectedUser' => $myAdviser->id])}}'" class="btn btn-info">پیام های ارسالی به مشاور</button>

                    <button onclick="document.location.href = '{{route('showOutboxSpecificMsgs', ['selectedUser' => $myAdviser->id])}}'" class="btn btn-warning">پیام های دریافتی از مشاور</button>
                @endif
            </center>
        @endif
    </div>

    <script>

        var selectedRate = '{{$rate}}';
        var updatedVal = -1;

        $(document).ready(function () {
            rate('{{$rate}}');
        });

        function rate(val) {
            updatedVal = val;
            $(".fa-star").removeClass('checked').filter(".rate" + val).addClass('checked');
        }

        $('.fa-star').click(function () {
            selectedRate = updatedVal;
            $.ajax({
                type: 'post',
                url: '{{route('submitRate')}}',
                data: {
                    'rate': selectedRate,
                    'adviserId': '{{$myAdviser->id}}',
                    'studentId': '{{Auth::user()->id}}'
                }
            });
        });
        
        function cursorOutOfBand() {
            if(selectedRate == -1)
                $(".fa-star").removeClass('checked');
            else
                rate(selectedRate);
        }

    </script>
@stop