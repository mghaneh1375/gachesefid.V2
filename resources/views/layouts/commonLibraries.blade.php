<meta charset="UTF-8">
<title>گچ سفید</title>
<meta name="viewport" content="width=device-width" />
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="{{URL::asset('css/commonCSS.css')}}">
<link href="{{URL::asset('css/myFont.css')}}" rel="stylesheet" type="text/css">
<link rel="icon" href="{{URL::asset('images/ICON-GACH-50.png')}}">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="{{URL::asset('js/persianumber.js')}}"></script>
<link rel="stylesheet" href="{{URL::asset('css/mobileMenuCSS.css')}}">
<script src = {{URL::asset("js/mobileMenu.js") }}></script>
<meta name="csrf-token" content="{{ csrf_token() }}" />

<link rel='stylesheet' href='{{URL::asset('css/grid.css')}}' type='text/css' media='all' />
<link rel='stylesheet' href='{{URL::asset('css/themeStyle.css')}}' type='text/css' media='all' />
<link rel='stylesheet' href='{{URL::asset('css/themePlugin.css')}}' type='text/css' media='all' />
<link rel='stylesheet' href='{{URL::asset('css/blocks.css')}}' type='text/css' media='all' />
<link rel='stylesheet' href='{{URL::asset('css/googleFont.css')}}' type='text/css' media='all' />
<link rel="stylesheet" href="{{URL::asset('css/rtl.css')}}" type="text/css" media="screen" />

{{--<script type="text/javascript">window.$crisp=[];window.CRISP_WEBSITE_ID="214cf062-f382-445e-b5f1-e755d9a01365";(function(){d=document;s=d.createElement("script");s.src="https://client.crisp.chat/l.js";s.async=1;d.getElementsByTagName("head")[0].appendChild(s);})();</script>--}}

<script>
    $(document).ready(function () {
        $(document.body).persiaNumber();
    });
</script>

<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
</script>