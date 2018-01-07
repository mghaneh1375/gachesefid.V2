@extends('layouts.form2')

@section('head')
    @parent
@stop


@section('caption')
    <div class="title">گزارش گیری جزئی از آزمون {{$quiz->name}}
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
                <td><center>نام مدرسه</center></td>
                <td><center>تعداد دانش آموزان</center></td>
                <td><center>نوع ثبت نام</center></td>
                <td><center>شهر</center></td>
                <td><center>استان</center></td>
            </tr>

            <?php $sum1 = 0; $sum2 = 0; ?>

            @foreach($nonOnline as $itr)
                <tr style="cursor: pointer" onclick="document.location.href = '{{route('doublePartialQuizReport', ['quizId' => $quiz->id, 'sId' => $itr->id, 'online' => 0])}}'">
                    <td><center>{{$itr->name}}</center></td>
                    <td><center>{{$itr->countNum}}</center></td>
                    <?php $sum1 += $itr->countNum; ?>
                    <td><center>حضوری</center></td>
                    <td><center>{{$itr->cityName}}</center></td>
                    <td><center>{{$itr->stateName}}</center></td>
                </tr>
            @endforeach

            <tr style="cursor: pointer" onclick="document.location.href = '{{route('doublePartialQuizReport', ['quizId' => $quiz->id, 'sId' => -1, 'online' => 0])}}'">
                <td><center>نامشخص</center></td>
                <td><center>{{$totalNonOnline - $sum1}}</center></td>
                <td><center>حضوری</center></td>
            </tr>

            @foreach($online as $itr)
                <tr style="cursor: pointer" onclick="document.location.href = '{{route('doublePartialQuizReport', ['quizId' => $quiz->id, 'sId' => $itr->id, 'online' => 1])}}'">
                    <td><center>{{$itr->name}}</center></td>
                    <td><center>{{$itr->countNum}}</center></td>
                    <?php $sum2 += $itr->countNum; ?>
                    <td><center>آنلاین</center></td>
                    <td><center>{{$itr->cityName}}</center></td>
                    <td><center>{{$itr->stateName}}</center></td>
                </tr>
            @endforeach

            <tr style="cursor: pointer" onclick="document.location.href = '{{route('doublePartialQuizReport', ['quizId' => $quiz->id, 'sId' => -1, 'online' => 1])}}'">
                <td><center>نامشخص</center></td>
                <td><center>{{$totalOnline - $sum2}}</center></td>
                <td><center>آنلاین</center></td>
            </tr>
        </table>

        <div style="margin-top: 10px">
            <button onclick="document.location.href = '{{route('quizPartialReportExcel', ['quizId' => $quiz->id])}}'" class="btn btn-success">دانلود فایل اکسل</button>
        </div>
    </center>
@stop