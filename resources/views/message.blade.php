@extends('layouts.form')

@section('head')

    @parent

    <script src="{{URL::asset('js/jsNeededForMsg.js')}}"></script>

    <link rel="stylesheet" href="{{URL::asset('css/MsgCSS.css')}}">

    <script>

        var getListOfMsgs = '{{route('getListOfMsgs')}}';
        var messageDir = '{{route('message')}}';
        var deleteMsgDir = '{{route('opOnMsgs')}}';

        $(document).ready(function () {

            err = "{{(isset($err) && !empty($err)) ? "err" : ""}}";

            if(err == "")
                inboxMode('inboxFolder', 'inbox', 'tableId', 'outbox', 'sendMsgDiv', 'showMsgContainer');
            else
                sendMode('sendFolder', 'inbox', 'sendMsgDiv', 'showMsgContainer');
        });

    </script>

@stop

@section('main')

    <div class="main">
        <div class="subMain">
            <div class="wrpHeader">
            </div>
            <h1 class="wrap">پیام های من</h1>

            <div class="main_content">
                <table width="100%" border="0" cellpadding="0" cellspacing="0" class="mb_12">
                    <tr>
                        <td>
                            <div class="floatRight">
                                <div class="saveLeftNav">
                                    <div>
                                        <div id="inboxFolder" onclick="inboxMode('inboxFolder', 'inbox', 'tableId', 'outbox', 'sendMsgDiv', 'showMsgContainer')" class="menu_bar">
                                            <strong>
                                                <span>صندوق ورودی </span>
                                                <span>(</span>
                                                <span>{{$inMsgCount}}</span>
                                                <span>)</span>
                                            </strong>
                                        </div>
                                    </div>
                                    <div id="outboxFolder" onclick="outboxMode('outboxFolder', 'inbox', 'tableId', 'sendMsgDiv', 'showMsgContainer')" class="menu_bar">
                                        <div class="displayFolder">
                                            <a onclick="" class="saveLink">
                                                <strong>
                                                    <span>صندوق خروجی </span>
                                                    <span>(</span>
                                                    <span>{{$outMsgCount}}</span>
                                                    <span>)</span>
                                                </strong>
                                            </a>
                                        </div>
                                    </div>
                                    <div id="sendFolder" onclick="sendMode('sendFolder', 'inbox', 'sendMsgDiv', 'showMsgContainer')" class="menu_bar">
                                        <div class="displayFolder">
                                            <a class="saveLink">
                                                <strong>ارسال پیام</strong>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>

                        <td class="inbox" id="inbox">
                            <div class="alignLeft">
                                <div class="p5">

                                    <div class="messagingButton">
                                        <a rel="nofollow" class="buttonLink" onclick="setAllChecked()">
                                            <div class="m2m_link">
                                                <div>
                                                    <div class="m2m_copy">
                                                        <img id="selectAllImg" src="{{URL::asset('images') . '/selectAll.gif'}}" border="0" alt="Select all" align="absmiddle" style="margin-left:8px;"/>
                                                        <span id="selectAll">انتخاب همه</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>

                                    <div class="messagingButton">
                                        <a class="buttonLink">
                                            <div class="m2m_link">
                                                <div>
                                                    <div onclick="deleteMsg()" class="m2m_copy">
                                                        <img src="{{URL::asset('images') . '/deleteIcon.gif'}}" border="0" alt="Delete" align="absmiddle" style="margin-left:8px;"/>
                                                        <span>حذف</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>

                                    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="clear" style="margin-top: 5px;">

                                        <tr class="inboxHeaders">
                                            <th class="subItem" style="width: 15%">
                                                <a>ارسال شده از / به</a>
                                            </th>
                                            <th class="subItem" style="width: 55%">
                                                <a>موضوع</a>
                                            </th>
                                            <th class="subItem" style="width: 15%; cursor: pointer">
                                                <a onclick="sortByDate()" title="Sort by: Date">تاریخ <img src="{{URL::asset('images') . '/blackNavArrowUp.gif'}}" width="7" height="4" hspace="10" border="0" align="absmiddle"/></a>
                                            </th>
                                            <th class="subItem" id="select-title">
                                                <a>انتخاب</a>
                                            </th>
                                        </tr>
                                    </table>

                                    <table id="tableId" width="100%" border="0" cellspacing="0" cellpadding="0" class="clear" style="margin-top: 5px">
                                    </table>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>

            <center id="sendMsgDiv" style="visibility: hidden; margin-top: -50px">
                <div class="row">
                    <form method="post" action="{{route('sendMsg')}}">
                        <div class="col-xs-12">
                            <label>
                                <span>نام کاربری مقصد</span>
                                <input type="text" name="destUser" required maxlength="40">
                            </label>
                        </div>
                        <div class="col-xs-12">
                            <label>
                                <span>موضوع</span>
                                @if(isset($subject) && !empty($subject))
                                    <input type="text" name="subject" value="{{$subject}}" required maxlength="40">
                                @else
                                    <input type="text" name="subject" required maxlength="40">
                                @endif
                            </label>
                        </div>
                        <div class="col-xs-12">
                            <p style="margin-top: 20px">پیام</p>
                            @if(isset($currMsg) && !empty($currMsg))
                                <textarea name="msg" style="width: 800px; height: 200px" maxlength="1000" placeholder="حداکثر 1000 کاراکتر">{{$currMsg}}</textarea>
                            @else
                                <textarea name="msg" style="width: 800px; height: 200px" maxlength="1000" placeholder="حداکثر 1000 کاراکتر"></textarea>
                            @endif
                        </div>
                        <div class="col-xs-12">
                            <input type="submit" value="ارسال" name="sendMsg" class="btn btn-success">
                            @if(isset($err) && !empty($err))
                                <p style="color: red; margin-top: 20px">{{$err}}</p>
                            @endif
                        </div>
                    </form>
                </div>
            </center>

            <span id="showMsgContainer" class="ui_overlay" style="visibility: hidden; position: fixed; left: 40%; right: auto; top: 174px; bottom: auto"></span>

        </div>
    </div>
@stop
