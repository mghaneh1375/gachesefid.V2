
function changeState(val, selectedCity) {

    $.ajax({
        type: 'post',
        url: getCities,
        data: {
            'stateId': val
        },
        success: function (response) {

            response = JSON.parse(response);
            newElement = "";

            $("#cities").empty();

            for(i = 0; i < response.length; i++) {
                if(selectedCity == -1 || selectedCity == response[i].id)
                    newElement += "<option selected value='" + response[i].id + "'>" + response[i].name + "</option>";
                else
                    newElement += "<option value='" + response[i].id + "'>" + response[i].name + "</option>";
            }

            $("#cities").append(newElement);

        }
    });

}
