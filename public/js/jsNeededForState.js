
function hideElement(element) {
    $("#msg").empty();
    $("#" + element).css("visibility", 'hidden');
}

function showElement(element) {
    $(".item").css("visibility", 'hidden');
    $("#" + element).css("visibility", 'visible');
}

function doAddNewState() {

    $("#msg").empty();

    if($("#stateName").val() == "") {
        $("#msg").append("لطفا نام استان مورد نظر خود را وارد نمایید");
        return;
    }
    
    $.ajax({
        type: 'post',
        url: addState,
        data: {
            'stateName' : $("#stateName").val()
        },
        success: function (response) {
            if(response == "ok")
                document.location.href = states;
            else {
                $("#msg").append("استان مورد نظر در سامانه موجود است");
            }
        }
    });
}
