$('.sub').on('click', function() {
    var arrRequest = {};

    arrRequest["taskId"] = $(this).data('taskid');
    arrRequest["mod"] = $(this).attr('name');
    var ajax = $.ajax({
        url : "/baiduai/Main/process",
        type: "post",
        data: arrRequest,
        success:function(response){
            // var reply = JSON.parse(response);

        },
        error:function(response){
        }

    });

});



function ajaxDetails(fundId, days){
    // 基于准备好的dom，初始化echarts实例
    // var arrRequest = 123123;
    var arrRequest = {};

    arrRequest["days"] = days;
    arrRequest["fundId"] = fundId;
    var thirtyDays = echarts.init(document.getElementById('details'));
    var ajax = $.ajax({
        url : "/fund/Details/index",
        type: "post",
        data: arrRequest,
        success:function(response){
            // alert(response);
            var reply = JSON.parse(response);
        },
        error:function(response){
        }

    });
}

