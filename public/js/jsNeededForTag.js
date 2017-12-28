var selectedTag = "";

function doAddNewTag() {

    if($("#tagName").val() == "")
        return;

    $.ajax({
        type: 'post',
        url: addTagDir,
        data: {
            'tagName': $("#tagName").val()
        },
        success:function (response) {
            if(response == "ok")
                document.location.href = tags;
            else {
                $("#errMsg").empty();
                $("#errMsg").append(response);
            }
        }
    });

}

function doEditTag() {

    if(selectedTag == "" || $("#newName").val() == "")
        return;

    $.ajax({
        type: 'post',
        url: editTagDir,
        data: {
            'tagId': selectedTag,
            'newName': $("#newName").val()
        },
        success: function (response) {
            if(response == "ok")
                document.location.href = tags;
            else {
                $("#errMsgEdit").empty();
                $("#errMsgEdit").append(response);
            }
        }
    });
}

function editTag(tagId) {

    selectedTag = tagId;

    $('.item').addClass('hidden');
    $("#editTagPane").removeClass('hidden');
}

function showAddTag() {
    $('.item').addClass('hidden');
    $("#addNewTagPane").removeClass('hidden');
}

function deleteTag(tagId) {
    
    $.ajax({
        type: 'post',
        url: deleteTagDir,
        data: {
            'tagId': tagId
        },
        success: function (response) {
            if(response == "ok")
                document.location.href = tags;
            else
                alert(response);
        }
    });
    
}