@extends('layouts.form')

@section('head')
    @parent
    <style>

        .blink {
            color: black;
            -webkit-animation: blink 3s step-end infinite;
            animation: blink 3s step-end infinite
        }
        @-webkit-keyframes blink {
            80% { opacity: 0 }
        }

        @keyframes blink {
            80% { opacity: 0 }
        }

        .uploadBtn {
            width: 30%;
            margin: 0;
        }

        @media only screen and (max-width: 767px) {

            .uploadBtn {
                width: 45%;
                margin: 0;
            }
        }
    </style>

    <script>
        function displayName() {
            $("#addFile").empty().append($("#group").val());
        }
    </script>
@stop


@section('caption')
    <div class="title">ثبت نام گروهی
    </div>
@stop

@section('main')

    <div class="col-xs-12" style="margin-top: 20px">
        <center>
            <h4>لطفا فایل اکسل زیر را دانلود کرده و آن را به دقت پر نمایید و سپس آن را آپلود نمایید.</h4>

            <div style="margin-top: 40px">
                @if(Auth::user()->level == getValueInfo('schoolLevel'))
                    <span>مرحله اول</span><span>&nbsp;</span><div style="width: 200px" class="btn btn-warning"><a target="_blank" href="{{URL::asset('gach-sefid-form2.xlsx')}}" download>دریافت نمونه فایل اکسل</a></div>
                @else
                    <span>مرحله اول</span><span>&nbsp;</span><div style="width: 200px" class="btn btn-warning"><a target="_blank" href="{{URL::asset('gach-sefid-form.xlsx')}}" download>دریافت نمونه فایل اکسل</a></div>
                @endif
            </div>

            <form method="post" action="{{route('doGroupRegistry')}}" style="margin-top: 10px" enctype="multipart/form-data">
                {{csrf_field()}}
                <span>مرحله دوم</span><span>&nbsp;</span>
                <input id="group" onchange="displayName()" name="group" type="file" style="display: none">
                <label for="group" class="uploadBtn" style="width: 200px">
                    <div id="addFile" class="btn btn-primary" style="width: 100%;">آپلود فایل اکسل</div>
                </label>

                <div style="margin-top: 10px">
                    <span>مرحله سوم</span><span>&nbsp;</span>
                    <input style="width: 200px" type="submit" value="ارسال فایل" class="btn btn-success">
                </div>
            </form>

            <div style="margin-top: 10px">
                @if($err != "")
                    @if($err == "getFile")
                        <span>مرحله آخر</span><span>&nbsp;</span>
                        <div style="width: 200px" class="btn btn-danger"><a href='{{URL::asset('registrations/report_' . Auth::user()->id . '.xlsx')}}' download>دانلود فایل اکسل گزارش ثبت نام</a></div>
                    @else
                        <p class="errorText">{{$err}}</p>
                    @endif
                @endif
            </div>


            <p style="color: #963019; margin-top: 20px; max-width: 400px; text-align: justify">
                توجه: پس از ارسال لیست دانش‌آموزان، حتما فایل نام کاربری و رمز را ذخیره کنید و در اختیار دانش‌آموزان قرار دهید.
                این فایل فقط یک بار تولید می‌شود و امکان دسترسی مجدد به آن وجود ندارد.
            </p>

        </center>
    </div>
@stop