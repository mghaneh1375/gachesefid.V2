

var rootDir;
var nodes;
var perPage;
var currPage;
var container;

function init(r, n, p, c, cont) {
    rootDir = r;
    nodes = n;
    perPage = p;
    currPage = parseInt(c);
    container = cont;

    createPaging();
}

function changePage(page) {

    document.location.href = rootDir + '/' + page;

}

function createPaging() {

    limit = Math.ceil(nodes / perPage);
    passPage = false;
    newElement = "";

    if(limit == 0) {
        newElement += '<h3 style="color: #963019">موردی یافت نشده</h3>';
    }

    newElement += "<div style='text-align: center; display: inline-block; margin: 20px 0; overflow: hidden;'>";

    if(currPage != limit && limit != 0) {
        newElement += "<button onclick='changePage(this.value)' value='" + (currPage + 1) + "' name='pageNum' class='btn btn-success' style='float: right !important; background-color: #4DC7BC !important; border-color: #4DC7BC !important; margin: 10px'>";
        newElement += "بعدی";
        newElement += "</button>";
    }
    if(currPage != 1 && limit != 0) {
        newElement += "<button onclick='changePage(this.value)' value='" + (currPage - 1) + "' name='pageNum' class='btn btn-success' style='float: left !important; background-color: #4DC7BC !important; border-color: #4DC7BC !important; margin: 10px'>";
        newElement += "قبلی";
        newElement += "</button>";
    }

    for(i = 1; i <= limit; i++) {
        if(Math.abs(currPage - i) < 4 || i == 1 || i == limit) {
            if(i == currPage) {
                newElement += "<span class='btn btn-success' style='background-color: #4DC7BC !important; float: left; border: none; margin: 5px;'>" + i + "</span>";
            }
            else if(i != limit) {
                newElement += "<button onclick='changePage(this.value)' value='" + i + "' name='pageNum' class='btn btn-success' style='float: left; background-color: transparent; border: 2px solid #454545; border-radius: 5px; margin: 5px; color: #963019'>" + i + "</button>";
            }
            else
                newElement += "<button onclick='changePage(this.value)' value='" + i + "' name='pageNum' class='btn btn-success' style='float: right; background-color: transparent; border: 2px solid #454545; border-radius: 5px; margin: 5px; color: #963019'>" + i + "</button>";
        }
        else if(i < currPage) {
            newElement += "<span class='separator'>&hellip;</span>";
        }
        else if(i > currPage && !passPage) {
            passPage = true;
            newElement += "<span class='separator'>&hellip;</span>";
        }
    }
    newElement += "</div>";

    $("#" + container).empty().append(newElement);

}