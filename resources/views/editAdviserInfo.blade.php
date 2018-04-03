@extends('layouts.form')

@section('head')
    @parent

    <link href="{{URL::asset('css/form.css')}}" rel="stylesheet" type="text/css">

    <script>

        function validate(evt) {
            var theEvent = evt || window.event;
            var key = theEvent.keyCode || theEvent.which;
            key = String.fromCharCode( key );
            var regex = /[0-9]|\./;
            if( !regex.test(key) ) {
                theEvent.returnValue = false;
                if(theEvent.preventDefault) theEvent.preventDefault();
            }
        }

    </script>
@stop

@section('caption')
    <div class="title">ویرایش اطلاعات کاربری
    </div>
@stop

@section('main')

    <center class="myRegister">
        <div class="data row">
            <div id="containerDiv" style="margin-top: 20px">

                <form method="post" action="{{route('doEditAdviserInfo')}}">

                    <div class="col-xs-12">
                        <div class="col-xs-7">
                            <input type="text" name="firstName" value="{{$user->firstName}}" maxlength="40" required autofocus>
                        </div>
                        <div class="col-xs-5">
                            <span>نام<span class="required">*</span></span>
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <div class="col-xs-7">
                            <input type="text" name="lastName" value="{{$user->lastName}}" maxlength="40" required>
                        </div>
                        <div class="col-xs-5">
                            <span>نام خانوادگی<span class="required">*</span></span>
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <div class="col-xs-7">
                            <input type="text" name="username" value="{{$user->username}}" maxlength="40" required>
                        </div>
                        <div class="col-xs-5">
                            <span>نام کاربری<span class="required">*</span></span>
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <div class="col-xs-7">
                            <input type="text" onkeypress="validate(event)" name="phoneNum" value="{{$user->phoneNum}}" maxlength="40" required>
                        </div>
                        <div class="col-xs-5">
                            <span>شماره تلفن<span class="required">*</span></span>
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <div class="col-xs-7">
                            <select class="mySelect" id="states" onchange="getCities(this.value)"></select>
                        </div>
                        <div class="col-xs-5">
                            <span>استان</span>
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <div class="col-xs-7">
                            <select class="mySelect" id="cities" name="cityId"></select>
                        </div>
                        <div class="col-xs-5">
                            <span>شهر</span>
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <div class="col-xs-7">
                            <select class="mySelect" id="field" onchange="changeField(this.value)" name="field">
                                @if($adviserInfo->field == getValueInfo('konkurAdvise'))
                                    <option selected value="{{getValueInfo('konkurAdvise')}}">کنکور</option>
                                @else
                                    <option value="{{getValueInfo('konkurAdvise')}}">کنکور</option>
                                @endif

                                @if($adviserInfo->field == getValueInfo('olympiadAdvise'))
                                    <option selected value="{{getValueInfo('olympiadAdvise')}}">المپیاد</option>
                                @else
                                    <option value="{{getValueInfo('olympiadAdvise')}}">المپیاد</option>
                                @endif

                                @if($adviserInfo->field == getValueInfo('doore1Advice'))
                                    <option selected value="{{getValueInfo('doore1Advice')}}">متوسطه دوره اول</option>
                                @else
                                    <option value="{{getValueInfo('doore1Advice')}}">متوسطه دوره اول</option>
                                @endif

                                @if($adviserInfo->field == getValueInfo('doore2Advice'))
                                    <option selected value="{{getValueInfo('doore2Advice')}}">متوسطه دوره دوم</option>
                                @else
                                    <option value="{{getValueInfo('doore2Advice')}}">متوسطه دوره دوم</option>
                                @endif

                                @if($adviserInfo->field == getValueInfo('baliniAdvice'))
                                    <option selected value="{{getValueInfo('baliniAdvice')}}">بالینی</option>
                                @else
                                    <option value="{{getValueInfo('baliniAdvice')}}">بالینی</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-xs-5">
                            <span>تخصص</span>
                        </div>
                    </div>

                    <div class="col-xs-12" id="gradesDiv">
                        <div class="col-xs-7">
                            <div id="grades"></div>
                        </div>
                        <div class="col-xs-5">
                            <span>رشته تحصیلی</span>
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <div class="col-xs-7">
                            <select class="mySelect" name="lastCertificate">
                                @if($adviserInfo->lastCertificate == getValueInfo('diplom'))
                                    <option selected value="{{getValueInfo('diplom')}}">دیپلم</option>
                                @else
                                    <option value="{{getValueInfo('diplom')}}">دیپلم</option>
                                @endif

                                @if($adviserInfo->lastCertificate == getValueInfo('foghDiplom'))
                                    <option selected value="{{getValueInfo('foghDiplom')}}">فوق دیپلم</option>
                                @else
                                    <option value="{{getValueInfo('foghDiplom')}}">فوق دیپلم</option>
                                @endif

                                @if($adviserInfo->lastCertificate == getValueInfo('lisans'))
                                    <option selected value="{{getValueInfo('lisans')}}">لیسانس</option>
                                @else
                                    <option value="{{getValueInfo('lisans')}}">لیسانس</option>
                                @endif

                                @if($adviserInfo->lastCertificate == getValueInfo('foghLisans'))
                                    <option selected value="{{getValueInfo('foghLisans')}}">فوق لیسانس</option>
                                @else
                                    <option value="{{getValueInfo('foghLisans')}}">فوق لیسانس</option>
                                @endif

                                @if($adviserInfo->lastCertificate == getValueInfo('phd'))
                                    <option selected value="{{getValueInfo('phd')}}">دکترا</option>
                                @else
                                    <option value="{{getValueInfo('phd')}}">دکترا</option>
                                @endif

                            </select>
                        </div>

                        <div class="col-xs-5">
                            <span>آخرین مدرک تحصیلی</span>
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <div class="col-xs-7">
                            <textarea style="float: right; margin: 10px" name="honors" placeholder="حداکثر 1000 کاراکتر">{{(isset($adviserInfo->honors)) ? $adviserInfo->honors : ''}}</textarea>
                        </div>
                        <div class="col-xs-5">
                            <span>افتخارات علمی</span>
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <div class="col-xs-7">
                            <textarea style="float: right; margin: 10px" name="essay" placeholder="حداکثر 1000 کاراکتر">{{isset($adviserInfo->essay) ? $adviserInfo->essay : ''}}</textarea>
                        </div>
                        <div class="col-xs-5">
                            <span>تالیفات و ترجمه ها</span>
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <div class="col-xs-7">
                            <textarea style="float: right; margin: 10px" name="schools" placeholder="حداکثر 1000 کاراکتر">{{isset($adviserInfo->schools) ? $adviserInfo->schools : ''}}</textarea>
                        </div>
                        <div class="col-xs-5">
                            <span>مدارس فعال</span>
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <div class="col-xs-7">
                            <select name="workYears">
                                @for($i = 1330; $i <= substr(getToday()['date'], 0, 4); $i++)
                                    @if(isset($adviserInfo->workYears) && $adviserInfo->workYears == $i)
                                        <option selected value="{{$i}}">{{$i}}</option>
                                    @else
                                        <option value="{{$i}}">{{$i}}</option>
                                    @endif
                                @endfor
                            </select>
                        </div>
                        <div class="col-xs-5">
                            <span>سال شروع به کار</span>
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <div class="col-xs-7">
                            <select name="birthDay">
                                @for($i = 1330; $i <= substr(getToday()['date'], 0, 4); $i++)
                                    @if(isset($adviserInfo->birthDay) && $adviserInfo->birthDay == $i)
                                        <option selected value="{{$i}}">{{$i}}</option>
                                    @else
                                        <option value="{{$i}}">{{$i}}</option>
                                    @endif
                                @endfor
                            </select>
                        </div>
                        <div class="col-xs-5">
                            <span>سال تولد</span>
                        </div>
                    </div>

                    <div class="col-xs-12" style="margin-bottom: 10px">
                        <center>
                            <input type="submit" name="editInfo" value="ویرایش">
                            @if(isset($msg) && !empty($msg))
                                <p class="errorText">نام کاربری وارد شده در سامانه موجود است</p>
                            @endif
                        </center>
                    </div>

                </form>
            </div>
        </div>
    </center>

    <script>

        var first = true;
        var fields = {!! $adviserFields !!};
        var stateId = '{{$city->stateId}}';
        var cityId = '{{$city->id}}';

        function changeField(val) {

            $.ajax({
                type: 'post',
                url: '{{route('getGradesOfField')}}',
                data: {
                    'field': val
                },
                success: function (response) {

                    response = JSON.parse(response);

                    var newElement = "";

                    for(i = 0; i < response.length; i++) {
                        allow = true;
                        for(j = 0; j < fields.length; j++) {
                            if(fields[j].gradeId == response[i].id) {
                                allow = false;
                                newElement += "<div style='float: right; padding: 5px'><label for='grade_" + response[i].id + "'>" + response[i].name + "</label><input checked id='grade_" + response[i].id + "' type='checkbox' value='" + response[i].id + "' name='grades[]'></div>";
                                break;
                            }
                        }
                        if(allow)
                            newElement += "<div style='float: right; padding: 5px'><label for='grade_" + response[i].id + "'>" + response[i].name + "</label><input id='grade_" + response[i].id + "' type='checkbox' value='" + response[i].id + "' name='grades[]'></div>";
                    }

                    if(response.length == 0) {
                        $("#gradesDiv").addClass('hidden');
                    }
                    else {
                        $("#gradesDiv").removeClass('hidden');
                    }

                    $("#grades").empty().append(newElement);

                }
            });
        }

        function getStates() {

            $.ajax({
                type: 'post',
                url: '{{route('getStates')}}',
                success: function(response) {

                    response = JSON.parse(response);
                    var newElement = "";

                    for(i = 0; i < response.length; i++) {
                        if(response[i].id == stateId)
                            newElement += "<option selected value='" + response[i].id + "'>" + response[i].name + "</option>";
                        else
                            newElement += "<option value='" + response[i].id + "'>" + response[i].name + "</option>";
                    }

                    $("#states").empty().append(newElement);

                    if(response.length > 0)
                        getCities(response[0].id);

                }
            });

        }

        function getCities(val) {

            $.ajax({
                type: 'post',
                url: '{{route('getCities')}}',
                data: {
                    'stateId': val
                },
                success: function (response) {

                    response = JSON.parse(response);

                    var newElement = "";

                    for(i = 0; i < response.length; i++) {
                        if(response[i].id == cityId)
                            newElement += "<option selected value='" + response[i].id +"'>" + response[i].name + "</option>";
                        else
                            newElement += "<option value='" + response[i].id +"'>" + response[i].name + "</option>";
                    }

                    $("#cities").empty().append(newElement);

                }
            });

        }

        $(document).ready(function () {
            getStates();
            changeField($("#field").val());
            first = false;
        });

    </script>
@stop