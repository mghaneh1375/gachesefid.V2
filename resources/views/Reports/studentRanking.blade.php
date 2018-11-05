@extends('layouts.form')

@section('head')
    @parent
@stop


@section('caption')
    <div class="title">رتبه بندی دانش آموزان
    </div>
@stop

@section('main')

    <style>
        td {
            padding: 10px;
        }
    </style>

    <center style="margin-top: 10px">

        @if($myRank == -1)
            <p class="errorText">شما هنوز امتیازی در این قسمت بدست نیاورده اید.</p>
        @elseif($myRank != -1 && $myRank != -2)
            <p class="errorText">رتبه شما: {{$myRank}}</p>
        @endif

        <table style="padding: 10px">
            <tr>
                <td><center>رتبه</center></td>
                <td><center>نام و نام خانوادگی</center></td>
                <td><center>شهر</center></td>
                <td><center>مدرسه</center></td>
                <td><center>پایه/رشته</center></td>
                <td><center>امتیاز</center></td>
            </tr>

            <?php $oldVal = -1; $inc = 1; ?>
            @foreach($users as $itr)
                <?php if($oldVal != $itr->totalSum) { $k += $inc; $oldVal = $itr->totalSum; $inc = 1; } else $inc++; ?>
                <tr>
                    <td><center>{{$k}}</center></td>
                    <td><center>{{$itr->firstName . ' ' . $itr->lastName}}</center></td>
                    <td><center>{{$itr->cityName}}</center></td>
                    <td><center>{{$itr->schoolName}}</center></td>
                    <td><center>{{$itr->grade}}</center></td>
                    <td><center style="direction: ltr">{{$itr->totalSum}}</center></td>
                </tr>
            @endforeach
        </table>

{{--        <script src="{{URL::asset('js/paging.js')}}"></script>--}}

        {{--<div class="col-xs-12" id="pageBar"></div>--}}
        {{--<script>--}}
            {{--init('{{route('studentsRanking')}}', '{{$total}}', 10, '{{$page}}', 'pageBar');--}}
        {{--</script>--}}
    </center>
@stop