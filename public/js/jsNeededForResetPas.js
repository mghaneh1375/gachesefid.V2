
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

function resetPas(noticePane) {

    if($("#username").val() == "")
        return;

    if($("#email").val() == "" && $("#phone").val() == "")
        return;

    mode = 2;
    username = $("#username").val();

    if($("#email").val() != "") {
        mode = 1;
        val = $("#email").val();
    }

    else {
        val = $("#phone").val();
    }

    $("#msg").css("visibility", "visible");

    $.ajax({
        type: 'post',
        url: resetPasPath,
        data:{
            'mode' : mode,
            'val' : val,
            'username' : username
        },
        success: function (response) {

            if(response == "ok")
                $("#" + noticePane).css("display", '');
            else {
                $("#msg").empty();
                $("#msg").append(response);
            }
        }
    });
    
}