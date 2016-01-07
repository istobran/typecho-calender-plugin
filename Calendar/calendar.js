var NON_SELECTED = -1;
var SELECT_YEAR = 1;
var SELECT_MONTH = 2;

var checkSelected = function() {
    if (!$("#cal_year").hasClass("selected") && !$("#cal_month").hasClass("selected")) {
        return NON_SELECTED;
    } else {
        return $("#cal_year").hasClass("selected") ? SELECT_YEAR : SELECT_MONTH;
    }
}

var move = function(direction) {
    if (direction != 'left' && direction != 'right') {
        return false;
    }
    var changeVal;
    switch(checkSelected()) {
        case NON_SELECTED:
            //未选择则默认切换月份
            changeVal = "month";
            break;
        case SELECT_YEAR:
            changeVal = "year";
            break;
        case SELECT_MONTH:
            changeVal = "month";
            break;
    }
    $.post(window.php_Url, {
        year: $("#cal_year b").html(),
        month: $("#cal_month b").html(),
        action: direction,
        selected: changeVal
    }, function(data) {
        if (data.success) {
            $("#cal_plugin tbody").html(data.tbody);
            $("#cal_year b").html(data.year);
            $("#cal_month b").html(data.month);
        }
    }, "json");
}

//控制选中年份还是月份
$("#cal_year").click(function() {
    if ($("#cal_year").hasClass("selected")) {
        $("#cal_year").removeClass("selected");
    } else {
        $("#cal_month").removeClass("selected");
        $("#cal_year").addClass("selected");
    }
});
$("#cal_month").click(function() {
    if ($("#cal_month").hasClass("selected")) {
        $("#cal_month").removeClass("selected");
    } else {
        $("#cal_year").removeClass("selected");
        $("#cal_month").addClass("selected");
    }
});