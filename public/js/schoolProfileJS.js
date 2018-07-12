var lineWidth;
var counter;

$(document).ready(function () {
    lineWidth = $(".col-xs-4").css('width').split('px')[0] - 200;
    startDrawLine();
});


function startDrawLine() {
    counter = 0;
    setTimeout("draw1()", 50);
}

function draw1() {

    if(lineWidth <= counter) {
        counter = 0;
        $("#line1").append("<center><span style='color: #4DC7BC; margin-top: -30px; font-size: 2em; border: 2px solid #4DC7BC; border-radius: 50%; padding: 4px;' class='glyphicon glyphicon-ok'></span></center>");
        setTimeout("draw2()", 100);
        return;
    }

    $("#line1").css('width', counter + "px");
    counter += 10;
    setTimeout("draw1()", 50);
}

function draw2() {

    if(lineWidth <= counter) {
        counter = 0;
        $("#line2").append("<center><span style='color: #4DC7BC; margin-top: -30px; font-size: 2em; border: 2px solid #4DC7BC; border-radius: 50%; padding: 4px;' class='glyphicon glyphicon-ok'></span></center>");
        setTimeout("draw3()", 100);
        return;
    }

    $("#line2").css('width', counter + "px");
    counter += 10;
    setTimeout("draw2()", 50);
}

function draw3() {

    if(80 <= counter) {
        counter = 0;
        setTimeout("draw4()", 100);
        return;
    }

    $("#line3").css('height', counter + "px");
    counter += 10;
    setTimeout("draw3()", 50);
}

function draw4() {

    if(lineWidth <= counter) {
        counter = 0;
        $("#line4").append("<center><span style='color: #4DC7BC; margin-top: -30px; font-size: 2em; border: 2px solid #4DC7BC; border-radius: 50%; padding: 4px;' class='glyphicon glyphicon-ok'></span></center>");
        setTimeout("draw5()", 100);
        return;
    }

    $("#line4").css('width', counter + "px");
    counter += 10;
    setTimeout("draw4()", 50);
}

function draw5() {

    if(lineWidth <= counter) {
        counter = 0;
        $("#line5").append("<center><span style='color: #4DC7BC; margin-top: -30px; font-size: 2em; border: 2px solid #4DC7BC; border-radius: 50%; padding: 4px;' class='glyphicon glyphicon-ok'></span></center>");
        setTimeout("draw6()", 100);
        return;
    }

    $("#line5").css('width', counter + "px");
    counter += 10;
    setTimeout("draw5()", 50);
}

function draw6() {

    if(80 <= counter) {
        counter = 0;
        setTimeout("draw7()", 100);
        return;
    }

    $("#line6").css('height', counter + "px");
    counter += 10;
    setTimeout("draw6()", 50);
}

function draw7() {

    if(lineWidth <= counter) {
        counter = 0;
        $("#line7").append("<center><span style='color: #4DC7BC; margin-top: -30px; font-size: 2em; border: 2px solid #4DC7BC; border-radius: 50%; padding: 4px;' class='glyphicon glyphicon-ok'></span></center>");
        setTimeout("draw8()", 100);
        return;
    }

    $("#line7").css('width', counter + "px");
    counter += 10;
    setTimeout("draw7()", 50);
}

function draw8() {

    if(lineWidth <= counter) {
        $("#line8").append("<center><span style='color: #4DC7BC; margin-top: -30px; font-size: 2em; border: 2px solid #4DC7BC; border-radius: 50%; padding: 4px;' class='glyphicon glyphicon-ok'></span></center>");
        counter = 0;
        return;
    }

    $("#line8").css('width', counter + "px");
    counter += 10;
    setTimeout("draw8()", 50);
}