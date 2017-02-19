<?php
	include_once 'header.php';
	$cObj = new ChartController();
	$_date = Run::req('day')?Run::req('day'):date('Y-m-d');
	$idsRes = $cObj->getInsDayService($_date);
?>
<script language="javascript" type="text/javascript" src="<?php echo APP_WEBSITE,'public/js/';?>My97DatePicker/WdatePicker.js"></script>

					<div style="border:solid 1px;border-color:#BFBFBF;">
					<input type="text" name="day" id="day" onFocus="WdatePicker({dateFmt:'yyyy-MM-dd',maxDate:'#F{\'2050-10-01\'}'})" value="<?php echo $_date;?>"/>
					<input type="button" onclick="window.location.href='?v=chart.ins.day.service&day='+$('#day').val()" value="查询"/>
					<!-- 为ECharts准备一个具备大小（宽高）的Dom -->
    				<div id="user_day" style="height:400px;"></div>
    				</div>
    				<!-- ECharts单文件引入 -->
    				<script type="text/javascript">
			        function getX(){
				        var _tmpRes = eval('('+'<?php echo json_encode($idsRes['x']);?>'+')');
				        var _str = new Array();
				        for(i=0;i<_tmpRes.length;i++){
				        	_str[i] = _tmpRes[i];
				        }
				        return _str;
			        }
			        function getY(){
			        	var _tmpRes = eval('('+'<?php echo json_encode($idsRes['y']);?>'+')');
				        var _str = new Array();
				        for(i=0;i<_tmpRes.length;i++){
				        	_str[i] = _tmpRes[i];
				        }
				        return _str;
			        }
			        require.config({
			            paths: {
			                echarts: '../js/chart/'
			            }
			        });			        
				     // 使用
			        require(
			            [
			                'echarts',
			                'echarts/chart/bar', // 使用柱状图就加载bar模块，按需加载
			                'echarts/chart/line' // 使用柱状图就加载bar模块，按需加载
			            ],
			            function (ec) {
			                // 基于准备好的dom，初始化echarts图表
			                var myChart = ec.init(document.getElementById('user_day'));
			                var option = {
			                	    title : {
			                	        text: '每天的签单量',
			                	        subtext: ''
			                	    },
			                	    tooltip : {
			                	        trigger: 'axis'
			                	    },
			                	    legend: {
			                	        data:['最高签单量']
			                	    },
			                	    toolbox: {
			                	        show : true,
			                	        feature : {
			                	            magicType : {show: true, type: ['line', 'bar']}
			                	        }
			                	    },
			                	    calculable : true,
			                	    xAxis : [
			                	        {
			                	            type : 'category',
			                	            boundaryGap : false,
			                	            data : getX()
			                	        }
			                	    ],
			                	    yAxis : [
			                	        {
			                	            type : 'value',
			                	            axisLabel : {
			                	                formatter: '{value} 单'
			                	            }
			                	        }
			                	    ],
			                	    series : [
			                	        {
			                	            name:'最高签单量',
			                	            type:'line',
			                	            data:getY(),
			                	            markPoint : {
			                	                data : [
			                	                    {type : 'max', name: '最大值'},
			                	                    {type : 'min', name: '最小值'}
			                	                ]
			                	            }
			                	        }
			                	    ]
			                	};
			        
			                // 为echarts对象加载数据 
			                myChart.setOption(option); 
			            }
			        );
			    	</script>
			    	<a href="javascript:history.go(-1);"  class="easyui-linkbutton" iconCls="icon-back" >返回</a> 