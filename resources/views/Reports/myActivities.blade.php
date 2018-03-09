@extends('layouts.form2')

@section('head')
    @parent
@stop


@section('caption')
    <div class="title">فعالیت های من
    </div>
@stop

@section('main')

    <style>
        td {
            padding: 10px;
        }
    </style>

    <center style="margin-top: 10px">
        <table style="padding: 10px">
            <tr>
                <td><center>تاریخ</center></td>
                <td><center>مقدار</center></td>
                <td><center>نوع تراکنش</center></td>
            </tr>

            @foreach($transactions as $itr)
                <tr>
                    <td><center>{{$itr->date}}</center></td>
                    <td><center style="direction: ltr">{{abs($itr->amount)}}</center></td>
                    <td><center>{{$itr->kindTransactionId}}</center></td>
                </tr>
            @endforeach
        </table>
    </center>
@stop