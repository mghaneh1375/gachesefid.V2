@extends('layouts.form')

@section('head')
    @parent
    <link href="{{URL::asset('css/form.css')}}" rel="stylesheet" type="text/css">
@stop

@section('caption')
    <div class="title">پیکربندی</div>
@stop

@section('main')
    <form method="post" action="{{route('config')}}">
        {{csrf_field()}}
        <center class="myRegister">
            <div class="row data">
                <div class="col-xs-12">
                    <div class="col-xs-7">
                        <input type="number" name="advisorPercent" min="0" value="{{$config->advisorPercent}}" required>
                    </div>
                    <div class="col-xs-5">
                        <span>درصد مشاور از تراکنش ها</span>
                    </div>
                </div>
                <div class="col-xs-12">
                    <div class="col-xs-7">
                        <input type="number" name="questionMin" min="0" value="{{$config->questionMin}}" required>
                    </div>
                    <div class="col-xs-5">
                        <span>حداقل تعداد سوال برای هوشمند سازی سطح سختی سوالات</span>
                    </div>
                </div>
                <div class="col-xs-12">
                    <div class="col-xs-7">
                        <input type="number" name="rankInQuiz" min="0" value="{{$config->rankInQuiz}}" required>
                    </div>
                    <div class="col-xs-5">
                        <span>تعداد نفرات برتر آزمون ها</span>
                    </div>
                </div>
                <div class="col-xs-12">
                    <div class="col-xs-7">
                        <input type="number" name="likeMin" min="0" value="{{$config->likeMin}}" required>
                    </div>
                    <div class="col-xs-5">
                        <span>حداقل تعداد لایک برای امتیاز دهی به سوال</span>
                    </div>
                </div>
                <div class="col-xs-12">
                    <div class="col-xs-7">
                        <input type="number" name="moneyMin" min="0" value="{{$config->moneyMin}}" required>
                    </div>
                    <div class="col-xs-5">
                        <span>حداقل مقدار پول قابل دریافت</span>
                    </div>
                </div>

                <div class="col-xs-12">
                    <div class="col-xs-7">
                        <input type="number" name="makeQuestionMin" min="0" value="{{$config->makeQuestionMin}}" required>
                    </div>
                    <div class="col-xs-5">
                        <span>حداقل امتیاز برای طراحی سوال</span>
                    </div>
                </div>

                <div class="col-xs-12">
                    <div class="col-xs-7">
                        <input type="number" name="percentOfPackage" min="0" value="{{$config->percentOfPackage}}" required>
                    </div>
                    <div class="col-xs-5">
                        <span>درصد تخفیف برای خرید بسته ای آزمون ها</span>
                    </div>
                </div>

                <div class="col-xs-12">
                    <div class="col-xs-7">
                        <input type="number" name="percentOfQuizes" min="0" value="{{$config->percentOfQuizes}}" required>
                    </div>
                    <div class="col-xs-5">
                        <span>درصد تخفیف برای از آزمون ها</span>
                    </div>
                </div>

                <div class="col-xs-12" style="margin-top: 10px">
                    <input type="submit" value="تایید" name="submitConfig" class="btn btn-default">
                </div>
            </div>
        </center>
    </form>
@stop