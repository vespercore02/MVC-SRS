
$(document).ready(function () {
    let sr_date_request, sr_site, sr_date_set, sr_rqst, suggested_date;
    sr_date_request = $('input[name=sr_date_request]').val();



    $('input[name=sr_local]').keypress(function (event) {
        if ((event.which != 46) && (event.which < 48 || event.which > 57)) {
            //$(".errmsg").html("Digits Only").show();
            alert("local number are 3 digits only!");
            return false;
        }
    });

    $('input[name=sr_date_set_cpr]').datepicker({
        beforeShowDay: $.datepicker.noWeekends
        
    });

    $('input[name=sr_date_set_cpr]').change(function () {

        $.datepicker.formatDate("yy-mm-dd")

        sr_date_set_cpr = $(this).val();
    });

    $('input[name=sr_date_set_drr]').datepicker({
        beforeShowDay: $.datepicker.noWeekends
        
    });
    $('input[name=sr_date_set_drr]').change(function () {

        $.datepicker.formatDate("yy-mm-dd")

        sr_date_set_drr = $(this).val();
    });


    $('input[name=sr_date_set]').datepicker({
        beforeShowDay: $.datepicker.noWeekends,
        minDate: 0
    });

    $('input[name=sr_date_set]').change(function () {

        $.datepicker.formatDate("yy-mm-dd")

        sr_date_set = $(this).val();


        checkRequest(sr_date_request, sr_site, sr_date_set, sr_rqst)
    });

    $('input[name=sr_site').change(function () {
        sr_site = $(this).val();

        checkRequest(sr_date_request, sr_site, sr_date_set, sr_rqst)
    });

    $('select[name=sr_rqst]').change(function () {
        sr_rqst = $(this).val();

        if (sr_rqst == "Account Request") {

            $('.csr-form').append(

                '<div class="account-request mt-5" >' +
                '<div class="form-name">' +
                '<h4>Account Request From</h4>' +
                '</div>' +
                '<div class="user-side-row">' +
                '<div class="div3">' +
                '<h6>User Information</h6>' +
                '</div>' +
                '<div class="div1">' +
                '<h6>Request</h6>' +
                '</div>' +
                '<div class="div2">' +
                '<h6>System Request</h6>' +
                '</div>' +
                '<div><i class="fas fa-2x fa-align-right"></i></div>' +
                '</div>' +


                '<div class="user-side-row"></div>' +
                '</div>');

            $('.csr-form').append(

                '<div class="user-side-row account-request">' +

                '<div class="div1">' +
                '<input type="text" name="account_id_no[0]" class="group-input" placeholder="I.D No" required>' +
                '</div>' +
                '<div class="div1">' +

                '<input type="text" name="account_fname[0]" class="group-input" placeholder="First Name" required>' +

                '<input type="text" name="account_minitial[0]" class="group-input" placeholder="Middle Initial">' +

                '<input type="text" name="account_lname[0]" class="group-input" placeholder="Last Name" required>' +

                '</div>' +
                '<div class="div1">' +

                '<input type="text" name="account_department[0]" class="group-input" placeholder="Department">' +

                '</div>' +
                '<div class="div1">' +
                '<div class="site">' +
                '<input class="" type="radio" name="account_request[0]" id="account_add0" value="Add" required>' +
                '<label class="" for="account_add0">Add</label>' +

                '<input class="" type="radio" name="account_request[0]" id="account_edit0" value="Edit" required>' +
                '<label class="" for="account_edit0">Edit</label>' +

                '<input class="" type="radio" name="account_request[0]" id="account_delete0" value="Delete" required>' +
                '<label class="" for="account_delete0">Delete</label>' +
                '</div>' +
                '</div>' +
                '<div class="div2">' +
                '<div class="system-request">' +
                '<input class="" type="checkbox" name="account_system[0][]" id="windows0" value="Windows" > <label class="" for="windows0">Windows</label>' +

                '<input class="" type="checkbox" name="account_system[0][]" id="email0" value="Email" > <label class="" for="email0">Email</label>' +

                '<input class="" type="checkbox" name="account_system[0][]" id="seine20" value="Seine2" > <label class="" for="seine20">Seine2</label>' +

                '<input class="" type="checkbox" name="account_system[0][]" id="cmms0" value="CMMS" > <label class="" for="cmms0">CMMS</label>' +

                '<input class="" type="checkbox" name="account_system[0][]" id="oms0" value="OMS" > <label class="" for="oms0">OMS</label>' +

                '<input class="" type="checkbox" name="account_system[0][]" id="pnas0" value="PNAS" > <label class="" for="pnas0">PNAS</label>' +

                '<input class="" type="checkbox" name="account_system[0][]" id="dms0" value="DMS" > <label class="" for="dms0">DMS</label>' +

                '<input class="" type="checkbox" name="account_system[0][]" id="mds0" value="MDS" > <label class="" for="mds0">MDS</label>' +

                '<input class="" type="checkbox" name="account_system[0][]" id="mtf0" value="MTF" > <label class="" for="mtf0">MTF</label>' +

                '<input class="" type="checkbox" name="account_system[0][]" id="tbac0" value="TBAC" > <label class="" for="tbac0">TBAC</label>' +

                '<input class="" type="checkbox" name="account_system[0][]" id="vcad0" value="VCAD" > <label class="" for="vcad0">VCAD</label>' +

                '<input class="" type="checkbox" name="account_system[0][]" id="wcad0" value="WCAD" > <label class="" for="wcad0">WCAD</label>' +
                '</div>' +
                '</div>' +
                '<i class="far fa-2x fa-plus-square plus-account"></i>' +
                '</div>');

            let i = 0;


            $('.plus-account').click(function () {



                if (i == 10) {
                    alert("Maximum of 10 fields only");

                    return false;

                } else {

                    ++i;

                    $('.csr-form').append(

                        '<div class="user-side-row account-request">' +

                        '<div class="div1">' +
                        '<input type="text" name="account_id_no[' + i + ']" class="group-input" placeholder="I.D No" required>' +
                        '</div>' +
                        '<div class="div1">' +

                        '<input type="text" name="account_fname[' + i + ']" class="group-input" placeholder="First Name" required>' +

                        '<input type="text" name="account_minitial[' + i + ']" class="group-input" placeholder="Middle Initial">' +

                        '<input type="text" name="account_lname[' + i + ']" class="group-input" placeholder="Last Name" required>' +

                        '</div>' +
                        '<div class="div1">' +

                        '<input type="text" name="account_department[' + i + ']" class="group-input" placeholder="Department" required>' +

                        '</div>' +
                        '<div class="div1">' +
                        '<div class="request">' +
                        '<input class="" type="radio" name="account_request[' + i + ']" id="account_add' + i + '" value="Add" required>' +
                        '<label class="" for="account_add' + i + '">Add</label>' +

                        '<input class="" type="radio" name="account_request[' + i + ']" id="account_edit' + i + '" value="Edit" required>' +
                        '<label class="" for="account_edit' + i + '">Edit</label>' +

                        '<input class="" type="radio" name="account_request[' + i + ']" id="account_delete' + i + '" value="delDeleteete" required>' +
                        '<label class="" for="account_delete' + i + '">Delete</label>' +
                        '</div>' +
                        '</div>' +
                        '<div class="div2">' +
                        '<div class="system-request">' +
                        '<input class="" type="checkbox" name="account_system[' + i + '][]" id="windows' + i + '" value="Windows" > <label class="" for="windows' + i + '">Windows</label>' +

                        '<input class="" type="checkbox" name="account_system[' + i + '][]" id="email' + i + '" value="Email" > <label class="" for="email' + i + '">Email</label>' +

                        '<input class="" type="checkbox" name="account_system[' + i + '][]" id="seine2' + i + '" value="Seine2" > <label class="" for="seine2' + i + '">Seine2</label>' +

                        '<input class="" type="checkbox" name="account_system[' + i + '][]" id="cmms' + i + '" value="CMMS" > <label class="" for="cmms' + i + '">CMMS</label>' +

                        '<input class="" type="checkbox" name="account_system[' + i + '][]" id="oms' + i + '" value="OMS" > <label class="" for="oms' + i + '">OMS</label>' +

                        '<input class="" type="checkbox" name="account_system[' + i + '][]" id="pnas' + i + '" value="PNAS" > <label class="" for="pnas' + i + '">PNAS</label>' +

                        '<input class="" type="checkbox" name="account_system[' + i + '][]" id="dms' + i + '" value="DMS" > <label class="" for="dms' + i + '">DMS</label>' +

                        '<input class="" type="checkbox" name="account_system[' + i + '][]" id="mds' + i + '" value="MDS" > <label class="" for="mds' + i + '">MDS</label>' +

                        '<input class="" type="checkbox" name="account_system[' + i + '][]" id="mtf' + i + '" value="MTF" > <label class="" for="mtf' + i + '">MTF</label>' +

                        '<input class="" type="checkbox" name="account_system[' + i + '][]" id="tbac' + i + '" value="TBAC" > <label class="" for="tbac' + i + '">TBAC</label>' +

                        '<input class="" type="checkbox" name="account_system[' + i + '][]" id="vcad' + i + '" value="VCAD" > <label class="" for="vcad' + i + '">VCAD</label>' +

                        '<input class="" type="checkbox" name="account_system[' + i + '][]" id="wcad' + i + '" value="WCAD" > <label class="" for="wcad' + i + '">WCAD</label>' +
                        '</div>' +
                        '</div>' +

                        '<i class="far fa-2x fa-minus-square minus-account"></i>' +
                        '</div>');


                }
            });

            $("body").on("click", ".minus-account", function () {

                $(this).parents(".user-side-row").remove();

                --i;

            });

            $('.access-request').remove();


        } else if (sr_rqst == "Access Request") {


            $('.account-request').remove();


            $('.csr-form').append('<div class="access-request mt-5" >' +
                '<div class="form-name">' +
                '<h4>Access Request From</h4>' +
                '</div>' +
                '<div class="user-side-row">' +
                '<div class="div3">' +
                '<h6>User Information</h6>' +
                '</div>' +
                '<div class="div1">' +
                '<h6>Request</h6>' +
                '</div>' +
                '<div class="div2">' +
                '<h6>Path Information</h6>' +
                '</div>' +
                '<div><i class="fas fa-2x fa-align-right"></i></div>' +
                '</div>' +


                '<div class="user-side-row"></div>' +
                '</div>');

            $('.csr-form').append(
                
                
                '<div class="user-side-row access-request">' +

                '<div class="div3">' +
                '<input class="access_name" type="text" name="access_name[0]" class="group-input" placeholder="Name">' +


                '</div>' +
                '<div class="div1">' +
                '<div class="request">' +
                '<input class="" type="radio" name="access_request[0]" id="access_add0" value="add" > <label class="" for="access_add0">Add</label>' +

                '<input class="" type="radio" name="access_request[0]" id="access_edit0" value="edit" > <label class="" for="access_edit0">Edit</label>' +

                '<input class="" type="radio" name="access_request[0]" id="access_delete0" value="delete" > <label class="" for="access_delete0">Delete</label>' +
                '</div>' +
                '</div>' +
                '<div class="div2"> <div class="system-request"> <input class="" type="text" name="access_path[0]" > </div> </div>' +
                '<i class="far fa-2x fa-plus-square plus-access"></i>' +
                '</div>');

            $(".access_name").autocomplete({
                source: "/account/get-Name",
                minLength: 2
            });

            let i = 0;


            $('.plus-access').click(function () {


                if (i == 10) {
                    alert("Maximum of 10 fields only");

                    return false;

                } else {

                    ++i;

                    $('.csr-form').append('<div class="user-side-row access-request">' +


                        '<div class="div3">' +
                        '<input class="access_name' + i + '" type="text" name="access_name[' + i + ']" class="group-input" placeholder="Name">' +


                        '</div>' +
                        '<div class="div1">' +
                        '<div class="site">' +
                        '<input type="radio" name="access_request[' + i + ']" id="access_add' + i + '" value="add" > <label for="access_add' + i + '">Add</label>' +

                        '<input type="radio" name="access_request[' + i + ']" id="access_edit' + i + '" value="edit" > <label for="access_edit' + i + '">Edit</label>' +

                        '<input type="radio" name="access_request[' + i + ']" id="access_delete' + i + '" value="delete" > <label for="access_delete' + i + '">Delete</label>' +
                        '</div>' +
                        '</div>' +
                        '<div class="div2"> <div class="system-request"> <input type="text" name="access_path[' + i + ']" > </div> </div>' +
                        '<i class="far fa-2x fa-minus-square minus-access"></i>' +
                        '</div>');

                    $(".access_name" + i).autocomplete({
                        source: "/account/get-Name",
                        minLength: 2
                    });
                }
            });



            $("body").on("click", ".minus-access", function () {

                $(this).parents(".user-side-row").remove();

            });


        } else {
            $('.access-request').remove();
            $('.account-request').remove();
        }

        checkRequest(sr_date_request, sr_site, sr_date_set, sr_rqst)


    });


    $('.submit-request').on('click', function () {

        for (let a = 0; a < 10; a++) {
            if ($("input[name='account_system[" + a + "][]']").length) {
                checked = $("input[name='account_system[" + a + "][]']:checked").length;

                if (!checked) {
                    alert("You must check at least one checkbox in line " + a + ".");
                    return false;
                }
            }
        }
    });


});

function checkRequest(sr_date_request, sr_site, sr_date_set, sr_rqst) {

    //alert(sr_date_request + " " + sr_site + " " + sr_date_set + " " + sr_rqst)

    if (sr_date_request != null && sr_site != null && sr_date_set != null && sr_rqst != null) {
        //alert(sr_date_request + " " + sr_site + " " + sr_date_set + " " + sr_rqst);
        let d0 = new Date(sr_date_request);
        sr_date_request = d0.getFullYear() + '-' + ('0' + (d0.getMonth() + 1)).slice(-2) + '-' + ('0' + d0.getDate()).slice(-2);
        let d1 = new Date(sr_date_set);
        sr_date_set = d1.getFullYear() + '-' + ('0' + (d1.getMonth() + 1)).slice(-2) + '-' + ('0' + d1.getDate()).slice(-2);

        let workingDays = workingDaysBetweenDates(sr_date_request, sr_date_set);

        //console.log(workingDays);

        $.get("/manages/checkrequestconfig", { request: sr_rqst, site: sr_site }).done(function (data) {
            let requestConfig = jQuery.parseJSON(data);

            //console.log(requestConfig);

            var requestConfigInfo = {
                request_name: requestConfig[0]['request_name'],
                site: requestConfig[0]['site'],
                working_days: requestConfig[0]['estimate_days']
            };

            //console.log(requestConfigInfo.working_days);

            if (requestConfigInfo.working_days > workingDays) {

                suggested_date = returnfinaldate(sr_date_request, requestConfigInfo.working_days);

                //console.log(formatDate(suggested_date));



                alert(sr_rqst + ' for ' + sr_site + ' is ' + requestConfigInfo.working_days + ' working days. Suggest date will apply');



            } else {

                suggested_date = returnfinaldate(sr_date_request, workingDays);

                //console.log(formatDate(suggested_date));

            }

            $('input[name=sr_date_set]').val(formatDate(suggested_date))
        });

    }

}

function purchase(site, category, datestart) {

    if (category == 'Hardware Purchase') {
        $('.account_rqst').hide();
        $('.access_rqst').hide();

        if (site == 'HO') {
            suggested_date = returnfinaldate(datestart, 65);
        } else if (site == 'BO') {
            suggested_date = returnfinaldate(datestart, 70);
        }

    } else if (category == 'Software Purchase' || category === 'Renewal License') {
        suggested_date = returnfinaldate(datestart, 30);

    } else {
        suggested_date = returnfinaldate(datestart, 1);
    }

    return suggested_date;
}



function workingDaysBetweenDates(d0, d1) {
    var holidays = ['2019-02-05', '2019-02-25', '2019-04-09', '2019-04-18', '2019-04-19', '2019-05-01', '2019-06-12'];
    var startDate = parseDate(d0);
    var endDate = parseDate(d1);
    // Validate input

    if (endDate < startDate) {
        return 0;
    }
    // Calculate days between dates
    var millisecondsPerDay = 86400 * 1000; // Day in milliseconds
    startDate.setHours(0, 0, 0, 1);  // Start just after midnight
    endDate.setHours(23, 59, 59, 999);  // End just before midnight
    var diff = endDate - startDate;  // Milliseconds between datetime objects
    var days = Math.ceil(diff / millisecondsPerDay);

    // Subtract two weekend days for every week in between
    var weeks = Math.floor(days / 7);
    days -= weeks * 2;

    // Handle special cases
    var startDay = startDate.getDay();
    var endDay = endDate.getDay();

    // Remove weekend not previously removed.
    if (startDay - endDay > 1) {
        days -= 2;
    }
    // Remove start day if span starts on Sunday but ends before Saturday
    if (startDay == 0 && endDay != 6) {
        days--;
    }
    // Remove end day if span ends on Saturday but starts after Sunday
    if (endDay == 6 && startDay != 0) {
        days--;
    }
    /* Here is the code */
    for (var i in holidays) {
        if ((holidays[i] >= d0) && (holidays[i] <= d1)) {
            days--;
        }
    }
    return days - 1;
}

function parseDate(input) {
    // Transform date from text to date
    var parts = input.match(/(\d+)/g);
    // new Date(year, month [, date [, hours[, minutes[, seconds[, ms]]]]])
    return new Date(parts[0], parts[1] - 1, parts[2]); // months are 0-based
}



function returnfinaldate(cdate, days) {
    var holidays = ['2019-02-05', '2019-02-25', '2019-04-09', '2019-04-18', '2019-04-19', '2019-05-01', '2019-06-12'];
    //holiday[0] = new Date(2017, 9, 5);// holiday 1
    //holiday[1] = new Date(2017, 9, 6);//holiday 2
    var startDate = new Date(cdate);
    var endDate = new Date(cdate);
    //startDate = cdate;// '8/1/2017';
    noOfDaysToAdd = days, count = 0;
    endDate = startDate;
    //use below code to include start date in count else comment below code
    //  if (endDate.getDay() != 0 && endDate.getDay() != 6 && !isHoliday(endDate, holiday)) {
    //   count++;
    // }
    while (count < noOfDaysToAdd) {
        endDate.setDate(endDate.getDate() + 1)
        // Date.getDay() gives weekday starting from 0(Sunday) to
        // 6(Saturday)
        if (endDate.getDay() != 0 && endDate.getDay() != 6 && !isHoliday(endDate, holidays)) {
            count++;
        }
    }
    return endDate;
}


function isHoliday(dt, arr) {
    var bln = false;
    for (var i = 0; i < arr.length; i++) {
        if (compare(dt, arr[i])) { //If days are not holidays
            bln = true;
            break;
        }
    }
    return bln;
}

function compare(dt1, dt2) {
    var dt1 = new Date(dt1);
    var dt2 = new Date(dt2);
    var equal = false;
    if (dt1.getDate() === dt2.getDate() && dt1.getMonth() === dt2.getMonth() && dt1.getFullYear() === dt2.getFullYear()) {
        equal = true;
    }
    return equal;
}

function formatDate(date) {
    var d = new Date(date),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();

    if (month.length < 2) month = '0' + month;
    if (day.length < 2) day = '0' + day;

    return [month, day, year].join('/');
}