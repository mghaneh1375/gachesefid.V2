@extends('layouts.form')

@section('head')
    @parent
@stop


@section('caption')
    <div class="title">رتبه بندی مشاوران
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
                <td><center>نام</center></td>
                <td><center>میانگین امتیاز</center></td>
                <td><center>تعداد دانش آموزان</center></td>
                <td><center>کد مشاور</center></td>
            </tr>

            @if(Auth::check())
                @foreach($advisers as $itr)
                    <tr>
                        <td onclick="document.location.href = '{{route('adviserInfo', ['adviserId' => $itr->id])}}'" style="cursor: pointer"><center>{{$itr->firstName . ' ' . $itr->lastName}}</center></td>
                        <td><center style="direction: ltr">{{$itr->rate}}</center></td>
                        <td><center>{{$itr->studentsNo}}</center></td>
                        <td><center>{{$itr->invitationCode}}</center></td>
                        <?php $allow = (\Illuminate\Support\Facades\Auth::user()->level == getValueInfo('studentLevel')) ? true : false; ?>
                        @foreach($myAdvisers as $myAdviser)
                            @if($itr->id == $myAdviser->adviserId)
                                <?php $allow = false; ?>
                                @if($myAdviser->status == 1)
                                    <td><center onclick="document.location.href = '{{route('myAdviser')}}';" style="cursor: pointer; color: #00AF87">مشاور فعلی من</center></td>
                                @else
                                    <td><center onclick="document.location.href = '{{route('myAdviser')}}';" style="cursor: pointer; color: #00AF87">در انتظار تایید مشاور</center></td>
                                @endif
                            @endif
                        @endforeach
                        @if($allow)
                            <td><center><button onclick="setAsMyAdviser('{{$itr->id}}')" class="btn btn-primary">انتخاب به عنوان مشاور من</button></center></td>
                        @endif
                    </tr>
                @endforeach
            @else
                @foreach($advisers as $itr)
                    <tr>
                        <td onclick="document.location.href = '{{route('adviserInfo', ['adviserId' => $itr->id])}}'" style="cursor: pointer"><center>{{$itr->firstName . ' ' . $itr->lastName}}</center></td>
                        <td><center style="direction: ltr">{{$itr->rate}}</center></td>
                        <td><center>{{$itr->studentsNo}}</center></td>
                        <td><center>{{$itr->invitationCode}}</center></td>
                    </tr>
                @endforeach
            @endif
        </table>
    </center>
    
    <script>
        function setAsMyAdviser(adviserId) {
            
            $.ajax({
                type: 'post',
                url: '{{route('setAsMyAdviser')}}',
                data: {
                    'adviserId': adviserId
                },
                success: function (response) {
                    if(response == "ok") {
                        document.location.href = '{{route('advisersList')}}';
                    }
                    else {
                        $("#errMsg").empty().append("خطایی در انجام عملیات مورد نظر رخ داده است");
                    }
                }
            });
            
        }
    </script>
    
@stop