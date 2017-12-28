$(".menuItem").mouseenter(function () {
    val = $(this).attr('data-val');
    $(".subItem").addClass('hidden');
    $(".subSubItem").addClass('hidden');
    $("." + val).removeClass('hidden');
});

$(".sub_item").mouseenter(function () {
    val = $(this).attr('data-val');
    $(".subSubItem").addClass('hidden');
    $("." + val).removeClass('hidden');
});

$(".sub_item").mouseleave(function () {
    $(".subSubItem").addClass('hidden');
});

$(".subSubItem").mouseleave(function () {
    $(".subItem").addClass('hidden');
    $(".subSubItem").addClass('hidden');
});/**
 * Created by asus on 9/15/2017.
 */
