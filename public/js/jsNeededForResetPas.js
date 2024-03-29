
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

    var username = $("#username").val();
    if(username.length == 0)
        return;

    if($("#email").val() == "" && $("#phone").val() == "")
        return;

    var mode = 2;
    var val;

    if($("#email").val() != "") {
        mode = 1;
        val = $("#email").val();
    }

    else {
        val = $("#phone").val();
    }

    $("#msg").css("visibility", "visible");

    $(".dark").removeClass('hidden');
    $(".animatedContainer").removeClass('hidden');

    $.ajax({
        type: 'post',
        url: resetPasPath,
        data:{
            'mode' : mode,
            'val' : val,
            'username' : username
        },
        success: function (response) {

            $(".animatedContainer").addClass('hidden');
            $(".dark").addClass('hidden');

            if(response != "ok") {
                $("#msg").empty().append(response);
            }
            else {
                $("#notice").removeClass('hidden');
            }
        }
    });
    
}