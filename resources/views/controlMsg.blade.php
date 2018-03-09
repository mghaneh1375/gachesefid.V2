@extends('layouts.form')

@section('head')

    @parent

    <link rel="stylesheet" href="{{URL::asset('css/MsgCSS.css')}}">


    <script>

        var getAccpetedMsgs = '{{route('acceptedMsgs')}}';
        var getRejectedMsgs = '{{route('rejectedMsgs')}}';
        var getPendingMsgs = '{{route('pendingMsgs')}}';
        var selfPage = '{{route('controlMsg')}}';
        var acceptMsgDir = '{{route('acceptMsgs')}}';
        var rejectMsgDir = '{{route('rejectMsgs')}}';

        $(document).ready(function () {

            pendingMode();

        });

    </script>

    <script src="{{URL::asset('js/jsNeededForControlMsg.js')}}"></script>

@stop

@section('main')

    <div class="main">
        <div class="subMain">
            <div class="wrpHeader">
            </div>
            <h1 class="wrap">پیام ها</h1>

            <div class="main_content">
                <table width="100%" border="0" cellpadding="0" cellspacing="0" class="mb_12">
                    <tr>
                        <td>
                            <div class="floatRight">
                                <div class="saveLeftNav">
                                    <div>
                                        <div id="pendingFolder" onclick="pendingMode()"  class="menu_bar">
                                            <strong>
                                                <span>پیام های بررسی نشده </span>
                                                <span>(</span>
                                                <span>{{$pendingCount}}</span>
                                                <span>)</span>
                                            </strong>
                                        </div>
                                    </div>
                                    <div id="acceptedFolder" onclick="acceptMode()" class="menu_bar">
                                        <div class="displayFolder">
                                            <a onclick="" class="saveLink">
                                                <strong>
                                                    <span>پیام های تایید شده </span>
                                                    <span>(</span>
                                                    <span>{{$acceptedCount}}</span>
                                                    <span>)</span>
                                                </strong>
                                            </a>
                                        </div>
                                    </div>
                                    <div id="rejectedFolder" onclick="rejectMode()" class="menu_bar">
                                        <div class="displayFolder">
                                            <a class="saveLink">
                                                <strong>
                                                    <span>پیام های تایید نشده </span>
                                                    <span>(</span>
                                                    <span>{{$rejectedCount}}</span>
                                                    <span>)</span>
                                                </strong>
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
                                                    <div onclick="showConfirmationForDelete()" class="m2m_copy">
                                                        <img src="{{URL::asset('images') . '/deleteIcon.gif'}}" border="0" alt="Delete" align="absmiddle" style="margin-left:8px;"/>
                                                        <span>حذف</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>

                                    <div class="messagingButton operationButton hidden" id="acceptBtn">
                                        <a class="buttonLink">
                                            <div class="m2m_link">
                                                <div>
                                                    <div onclick="accept()" class="m2m_copy">
                                                        <img src="{{URL::asset('images') . '/deleteIcon.gif'}}" border="0" alt="Delete" align="absmiddle" style="margin-left:8px;"/>
                                                        <span>تایید</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>

                                    <div class="messagingButton operationButton hidden" id="rejectBtn">
                                        <a class="buttonLink">
                                            <div class="m2m_link">
                                                <div>
                                                    <div onclick="reject()" class="m2m_copy">
                                                        <img src="{{URL::asset('images') . '/deleteIcon.gif'}}" border="0" alt="Delete" align="absmiddle" style="margin-left:8px;"/>
                                                        <span>رد</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>

                                    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="clear" style="margin-top: 5px; height: 30px">

                                        <tr>
                                            <td class="subItemTable" style="width: 15%">
                                                <a>ارسال شده از / به</a>
                                            </td>
                                            <td class="subItemTable" style="width: 55%">
                                                <a>موضوع</a>
                                            </td>
                                            <td class="subItemTable" style="width: 15%; cursor: pointer">
                                                <a onclick="sortByDate()" title="Sort by: Date">تاریخ <img src="{{URL::asset('images') . '/blackNavArrowUp.gif'}}" width="7" height="4" hspace="10" border="0" align="absmiddle"/></a>
                                            </td>
                                            <td class="subItemTable" id="select-title">
                                                <a>انتخاب</a>
                                            </td>
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

            <style>
                #sendMsgTable td {
                    padding: 7px;
                }
            </style>

            <span id="showMsgContainer" class="ui_overlay subMsgItems hidden" style="position: fixed; left: 40%; right: auto; top: 174px; bottom: auto"></span>

            <span class="ui_overlay ui_modal editTags subMsgItems hidden" id="deleteMsg" style="position: fixed; left: 37%; right: auto; top: 29%; bottom: auto;width: 26%;">
                <p>آیا از پاک کردن پیام اطمینان دارید ؟</p>
                <br><br>
                <div class="body_text">

                    <div class="submitOptions">
                        <button style="background-color: #4dc7bc;border-color:#4dc7bc;" onclick="deleteMsg()" class="btn btn-success">بله </button>
                        <input type="submit" onclick="hideConfirmationPane()" value="خیر" class="btn btn-default">
                    </div>
                </div>

                <div onclick="hideConfirmationPane()" class="ui_close_x"></div>

            </span>

        </div>
    </div>
@stop
