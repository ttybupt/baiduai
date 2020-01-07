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
            // alert(reply.data);
            var content = {
                title: {
                    text: '净值曲线'
                },
                tooltip: {
                    trigger: 'axis'
                },
                legend: {
                    data:['单位净值', '累计净值'],
                },
                grid: {
                    left: '10%',
                    right: '15%',
                    bottom: '10%',
                    // containLabel: true
                },
                toolbox: {
                    feature: {
                        dataView: {
                            show: true,
                            readOnly: false
                        }
                    }
                },
                xAxis: {
                    type: 'category',
                    boundaryGap: false,
                    data: reply.data.date,
                },
                yAxis: [
                    {
                        type: 'value',
                        name: '单位净值',
                        scale: true,
                        position: 'left',
                    }, {
                        type: 'value',
                        name: '累计净值',
                        position: 'right',
                        scale: true,
                    }],
                series: [
                    {
                        name:'单位净值',
                        type:'line',
                        data:reply.data.unitNetWorth,
                    },
                    {
                        name:'累计净值',
                        type:'line',
                        yAxisIndex: 1,
                        data:reply.data.cumulativeNetWorth,
                    }
                ]
            };

            // 使用刚指定的配置项和数据显示图表。
            thirtyDays.setOption(content);
            $("#buyStatus").html(reply.buyStatus);
            $("#saleStatus").html(reply.saleStatus);
            $("#autoInvestYield-30").html(reply.aMonth.autoInvestYield+"%");
            $("#autoInvestYield-90").html(reply.threeMonths.autoInvestYield+"%");
            $("#autoInvestYield-180").html(reply.halfYear.autoInvestYield+"%");
            $("#autoInvestYield-365").html(reply.oneYear.autoInvestYield+"%");
            $("#autoInvestYield-all").html(reply.all.autoInvestYield+"%");

            $("#maxRetreatRate-30").html(reply.aMonth.maxRetreatRate+"%");
            $("#maxRetreatRate-90").html(reply.threeMonths.maxRetreatRate+"%");
            $("#maxRetreatRate-180").html(reply.halfYear.maxRetreatRate+"%");
            $("#maxRetreatRate-365").html(reply.oneYear.maxRetreatRate+"%");
            $("#maxRetreatRate-all").html(reply.all.maxRetreatRate+"%");

            $("#upAndDown").html(reply.data.upAndDown.up+" - "+reply.data.upAndDown.down);

            $("#maxRetreatRate").html("最大回撤率：\n"+reply.data.maxRetreatRate+"%");
            $("#autoInvestYield").html("定投收益率：\n"+reply.data.autoInvestYield+"%");

            $("#myModal").modal("show");
            $(function () { $('#collapseTwo').collapse('hide')});
            $(function () { $('#collapseOne').collapse('hide')});
        },
        error:function(response){
        }

    });
}

