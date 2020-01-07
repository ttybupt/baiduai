function ajaxMain(){
	// 基于准备好的dom，初始化echarts实例

    var arrRequest = {};
	// var getData = new Array();
    // $("#days").on("change",function() {


    // arrRequest["fields"] = new Array();
    //     $("#usertype option:selected").each(function(){
    //         arrRequest["fields"].push($(this).attr('value'));
    //     });
    arrRequest["days"] = $("#days option:selected").val();
    arrRequest["start"] = $("#dtp_input2").val();
    arrRequest["end"] = $("#dtp_input3").val();

	var stackImg = echarts.init(document.getElementById('stackImg'));
	var totalImg = echarts.init(document.getElementById('totalImg'));
	var pieImg = echarts.init(document.getElementById('pieImg'));
	var calendarImg = echarts.init(document.getElementById('calendarImg'));
	// var GEM = echarts.init(document.getElementById('GEM'));
	var ajax = $.ajax({
		url : "/study/Main/ajaxGetStackData",
		type: "post",
		data: arrRequest,
		success:function(response){
			var reply = JSON.parse(response);
			var stackImg_content = {

				tooltip : {
					trigger: 'axis',
					axisPointer: {
						type: 'cross',
						label: {
							backgroundColor: '#6a7985'
						}
					}
				},
				legend: {
					data:reply.legend
				},
				toolbox: {
					feature: {
						saveAsImage: {}
					}
				},
				grid: {
					left: '3%',
					right: '4%',
					bottom: '3%',
					containLabel: true
				},
				xAxis : [
					{
						type : 'category',
						boundaryGap : false,
						data : reply.date
					}
				],
				yAxis : [
					{
						type : 'value'
					}
				],
				series : reply.stack.series
			};
			var totalHisto_Content = {
                color: ['#3398DB'],
                tooltip : {
                    trigger: 'axis',
                    axisPointer : {            // 坐标轴指示器，坐标轴触发有效
                        type : 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
                    }
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '3%',
                    containLabel: true
                },
                xAxis : [
                    {
                        type : 'category',
                        data : reply.legend,
                        axisTick: {
                            alignWithLabel: true
                        }
                    }
                ],
                yAxis : [
                    {
                        type : 'value'
                    }
                ],
                series : [
                    {
                        name:'学习时间',
                        type:'bar',
                        barWidth: '60%',
                        data:reply.histogram
                    }
                ]
            };
            var pie_content = {

                tooltip : {
                    trigger: 'item',
                    formatter: "{a} <br/>{b} : {c}小时 ({d}%)"
                },
                legend: {
                    type: 'scroll',
                    orient: 'horizontal',
                    data: reply.legend,
                },
                series : [
                    {
                        name: '学习时间',
                        type: 'pie',
                        radius : '60%',
                        center: ['50%', '60%'],
                        data: reply.pie,
                        itemStyle: {
                            emphasis: {
                                shadowBlur: 10,
                                shadowOffsetX: 0,
                                shadowColor: 'rgba(0, 0, 0, 0.5)'
                            }
                        }
                    }
                ]
            };
            var calendar_content = {
                tooltip: {
                    position: 'top',
                },
                visualMap: {
                    min: 0,
                    max: 10,
                    orient: 'vertical',
                    left: 'left',
                    top: 'center'
                },

                calendar: [
                    {
                        orient: 'vertical',
                        range: [reply.start, reply.end],
                        cellSize: ['auto', 'auto']
                    }],

                series: [{
                    type: 'heatmap',
                    coordinateSystem: 'calendar',
                    calendarIndex: 0,
                    data: reply.calendar
                }]
            };
			// 使用刚指定的配置项和数据显示图表。
			stackImg.setOption(stackImg_content);
            totalImg.setOption(totalHisto_Content);
            pieImg.setOption(pie_content);
            calendarImg.setOption(calendar_content);
		},
		error:function(response){
		}

	});
}

