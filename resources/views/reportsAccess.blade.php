@extends('layouts.form')

@section('head')
    @parent

    <script>
        var changeReportStatusDir = '{{route('changeReportStatus')}}';

        function changeReportStatus(id) {
            $.ajax({
                type: 'post',
                url: changeReportStatusDir,
                data: {
                    'id': id
                }
            });
        }
    </script>
@stop

@section('main')

    <div class="col-xs-12" style="margin-top: 100px">
        @foreach($reportsAccess as $itr)
            <center class="col-xs-12">
                <p>گزارش {{$itr->reportNo}}</p>
                @if($itr->status == 1)
                    <label class="switch"><input onchange="changeReportStatus('{{$itr->id}}')" checked type="checkbox"><span class="slider round"></span></label>
                @else
                    <label class="switch"><input onchange="changeReportStatus('{{$itr->id}}')" type="checkbox"><span class="slider round"></span></label>
                @endif

                <div style="border-bottom: 2px dashed #919191; width: 20%"></div>
            </center>
        @endforeach
    </div>

@stop