
function hideElement(element) {
    $("#msg").empty();
    $("#" + element).css("visibility", 'hidden');
}

function showElement(element) {
    $(".item").css("visibility", 'hidden');
    $("#" + element).css("visibility", 'visible');
}

function addCity() {
    $.ajax({
        type: 'post',
        url: getStates,
        success: function (response) {

            response = JSON.parse(response);

            $("#states").empty();
            newElement = "";

            for(i = 0; i < response.length; i++)
                newElement += "<option value='" + response[i].id + "'>" + response[i].name + "</option>";

            $("#states").append(newElement);

            showElement('newCityContainer');
        }
    });
}

function doAddNewCity() {

    $("#msg").empty();

    if($("#cityName").val() == "") {
        $("#msg").append("لطفا نام شهر مورد نظر خود را وارد نمایید");
        return;
    }

    $.ajax({
        type: 'post',
        url: addCityDir,
        data: {
            'stateId': $("#states").val(),
            'cityName': $("#cityName").val()
        },
        success: function (response) {
            if(response == "ok")
                document.location.href = cities;
            else {
                $("#msg").append("شهر مورد نظر در سامانه موجود است");
            }
        }
    });
}
