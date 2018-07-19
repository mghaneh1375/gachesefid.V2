
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

    username = $("#username").val();
    if(username.length == 0)
        return;

    if($("#email").val() == "" && $("#phone").val() == "")
        return;

    mode = 2;

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

            $(".animatedContainer").removeClass('hidden');

            if(response == "ok")
                $("#" + noticePane).removeClass('hidden');
            else {
                $(".dark").removeClass('hidden');
                $("#msg").empty().append(response);
            }
        }
    });
    
}