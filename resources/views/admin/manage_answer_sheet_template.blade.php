@extends('layouts.form')

@section('head')
    @parent
    
    <script>
        function editAnswerTemplate(templateId) {

            if($("#row_" + templateId).val() == "" || $("#col_" + templateId).val() == "")
                return;

            $.ajax({
                type: 'post',
                url: '{{route('edit_answer_answer_sheet_template')}}',
                data: {
                    'templateId': templateId,
                    'answer_sheet_template_id': '{{$answer_sheet_template->id}}',
                    'row': $("#row_" + templateId).val(),
                    'col': $("#col_" + templateId).val()
                },
                success: function (response) {

                    response = JSON.parse(response);

                    if(response.status == "nok") {
                        $("#col_" + templateId).val(response.col);
                        $("#row_" + templateId).val(response.row);
                        $("#err").empty().append(response.err);
                    }
                    $("#row_" + templateId).attr('disabled', 'disabled');
                    $("#col_" + templateId).attr('disabled', 'disabled');
                    $("#edit_" + templateId).addClass('hidden');
                    $("#activeEdit_" + templateId).removeClass('hidden');
                }
            });

        }    
    </script>
@stop

@section('caption')
    <div class="title">مدیریت پاسخ های قالب پاسخ نامه
    </div>
@stop

@section('main')
    <center style="margin-top: 10px">
        <div class="row">

            <div class="col-xs-3">امکانات</div>
            <div class="col-xs-3">ستون</div>
            <div class="col-xs-3">ردیف</div>
            <div class="col-xs-3">شماره پاسخ</div>
            <?php $i = 1; ?>
            @foreach($answer_templates as $answer_template)
                <div class="col-xs-12" style="margin-top: 10px">
                    <div class="col-xs-3">

                        <button onclick="document.location.href = '{{route('delete_answer_answer_sheet_template', ['{answer_template}' => $answer_template->id])}}'" class="btn btn-danger" data-toggle="tooltip" title="حذف پاسخ">
                            <span class="glyphicon glyphicon-remove"></span>
                        </button>

                        <button onclick="$('#col_{{$answer_template->id}}').removeAttr('disabled'); $('#row_{{$answer_template->id}}').removeAttr('disabled'); $('#edit_{{$answer_template->id}}').removeClass('hidden'); $('#activeEdit_{{$answer_template->id}}').addClass('hidden')" id="activeEdit_{{$answer_template->id}}" class="btn btn-primary" data-toggle="tooltip" title="ویرایش پاسخ برگ">
                            <span class="glyphicon glyphicon-edit"></span>
                        </button>

                        <button id="edit_{{$answer_template->id}}" onclick="editAnswerTemplate('{{$answer_template->id}}')" class="btn btn-primary hidden">
                            ثبت
                        </button>
                    </div>
                    <div class="col-xs-3"><input id="col_{{$answer_template->id}}" type="number" disabled value="{{$answer_template->column}}" max="{{$answer_sheet_template->column_count}}"></div>
                    <div class="col-xs-3"><input id="row_{{$answer_template->id}}" type="number" disabled value="{{$answer_template->row}}" max="{{$answer_sheet_template->row_count}}"></div>
                    <div class="col-xs-3">{{$i}}</div>
                </div>
                <?php $i++ ?>
            @endforeach


            <form method="post" action="{{route('add_answer_answer_sheet_template', ['answer_sheet_template' => $answer_sheet_template->id])}}">
                {{csrf_field()}}
                <div class="col-xs-12" style="margin-top: 10px">
                    <div class="col-xs-3">
                        <button class="btn btn-success">افزودن</button>
                    </div>
                    <div class="col-xs-3">
                        <input type="number" max="{{$answer_sheet_template->column_count}}" name="col">
                    </div>
                    <div class="col-xs-3">
                        <input type="number" max="{{$answer_sheet_template->row_count}}" name="row">
                    </div>
                    <div class="col-xs-3">{{$i}}</div>
                </div>
            </form>

            <div class="col-xs-12">
                <center id="err" class="errorText">{{$err}}</center>
            </div>
        </div>

    </center>
@stop