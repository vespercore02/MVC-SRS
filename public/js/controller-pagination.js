
function pad(num, size) {
    var s = num + "";
    while (s.length < size) s = "0" + s;
    return s;
}

function linkup(link) {
    //window.location.href = $(link).attr("value");

    window.open($(link).attr("value"));
}

function getAccountData(id) {

    $.get(controllerData, id).done(function (data) {
        accountData = jQuery.parseJSON(data);

        tbl_info(accountData);

    });
}

function getAccountPage(data) {

    $.get(controllerPage, data).done(function (data) {
        pages = jQuery.parseJSON(data);

        page_pages(pages, id, 0, 10)
    });
}

function getData(id, start, limit, filter_name, controllerData, status) {
    var requestData

    if (status !== "") {
        $.get(controllerData, { id: id, start: start, limit: limit, filter_name: filter_name, status: status }).done(function (data) {
            requestData = jQuery.parseJSON(data);

            tbl_info(requestData);
            //console.log(requestData) 
        });
    } else {
        $.get(controllerData, { id: id, start: start, limit: limit, filter_name: filter_name }).done(function (data) {
            requestData = jQuery.parseJSON(data);

            tbl_info(requestData);
            //console.log(requestData) 
        });
    }

}

function getPage(id, start, limit, filter_name, controllerPage, status) {
    let pages
    $('.page_list').empty();

    if (status !== "") {
        $.get(controllerPage, { page: "1", filter_name: filter_name, status: status }).done(function (data) {
            pages = jQuery.parseJSON(data);
            page_pages(pages, id, start, limit)
        });
    } else {
        $.get(controllerPage, { page: "1", filter_name: filter_name }).done(function (data) {
            pages = jQuery.parseJSON(data);

            page_pages(pages, id, start, limit)
        });
    }
}

function page_pages(pages, id, start, limit) {
    //console.log(pages);

    page_list = Math.ceil(pages / limit);

    //alert(pages);
    //console.log(page_list);

    var currentpage = id;

    if (currentpage > 3) {
        page = $('<li class="page-item"><a class="page-link" data-page="1"><<</a></li>');
        $('.page_list').append(page);
        if (id >= 11) {
            var prevpage = currentpage - 10;
            page = $('<li class="page-item"><a class="page-link" data-page=' + prevpage + '>' + prevpage + '</a></li>');
            $('.page_list').append(page);
            page = $('<li class="page-item disabled"><i class="page-link" >...</i></li>');
            $('.page_list').append(page);
        }
    }

    if (currentpage > 1) {
        var prevpage = currentpage - 1;
        if (page_list != 0) {
            page = $('<li class="page-item"><a class="page-link"  data-page=' + prevpage + '>Previous</a></li>');
            $('.page_list').append(page);
        } else {
            var onlefet = " ";
        }
    } else {
        var onlefet = " ";
    }

    var range = 2;

    for (var a = (currentpage - range); a < ((currentpage + range) + range); a++) {
        var liList;
        $('.page_list').append(liList);
        //var liList = $('<li class="page-item disabled">');
        if ((a > 0) && (a <= page_list)) {

            if (a == id) {
                liList = $("<li class='page-item disabled'> <b class='page-link bg-success'>" + a + "</b> </li> ");
            } else {
                liList = $("<li class='page-item '><a class='page-link'  data-page=" + [a] + ">" + [a] + "</a>");
            }
        }
        //liList.append('</li>');
    }

    if (currentpage != page_list) {
        var nextpage = currentpage + 1;
        if (page_list != 1) {
            page = $('<li class="page-item"><a class="page-link" data-page=' + nextpage + '>Next</a></li>');
            $('.page_list').append(page);
        } else {
            var onright = " ";
        }
    } else {
        var onright = " ";
    }

    console.log(currentpage);

    if ((page_list - currentpage) > 2) {
        if (page_list > 11) {
            var nextpage = currentpage + 10;

            if (nextpage > page_list) {

            } else {
                page = $('<li class="page-item disabled"><i class="page-link" >...</i></li>');
                $('.page_list').append(page);
                page = $('<li class="page-item"><a class="page-link" data-page=' + nextpage + '>' + nextpage + '</a></li>');
                $('.page_list').append(page);
            }
        }

        page = $('<li class="page-item"><a class="page-link" data-page=' + page_list + '>>></a></li>');
        $('.page_list').append(page);
    }
}