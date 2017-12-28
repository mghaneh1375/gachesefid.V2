@extends('layouts.form')

@section('head')
    @parent

    <script language="javascript" type="text/javascript">

        var refIdValue = '{{$refIdValue}}';

        $(document).ready(function () {
            postRefId();
        });

        function postRefId () {
            var form = document.createElement("form");
            form.setAttribute("method", "POST");
            form.setAttribute("action", "https://bpm.shaparak.ir/pgwchannel/startpay.mellat");
            form.setAttribute("target", "_self");
            var hiddenField = document.createElement("input");
            hiddenField.setAttribute("name", "RefId");
            hiddenField.setAttribute("value", refIdValue);
            form.appendChild(hiddenField);

            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        }
    </script>
@stop

@section('main')
@stop
