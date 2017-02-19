<?php
exit();
	include_once 'header.php';
	$cObj = new ChartController ();
	$_mduDate = Run::req ( 'date' ) ? Run::req ( 'date' ) : date ( 'm' );
	$_mduDateRes = date ( 'Y' ) . '-' . $_mduDate;
	$mduRes = $cObj->getMonthDayUser ( $_mduDateRes );
?>
<div style="border:solid 1px;border-color:#BFBFBF;">
					<!-- 为ECharts准备一个具备大小（宽高）的Dom -->
					<select onchange="locationHref(this.value)" name="month">
						<option value="1">1月</option><option value="2">2月</option><option value="3">3月</option>
						<option value="4">4月</option><option value="5">5月</option><option value="6">6月</option>
						<option value="7">7月</option><option value="8">8月</option><option value="9">9月</option>
						<option value="10">10月</option><option value="11">11月</option><option value="12">12月</option>
					</select>
					<script>
					var _mduMonth='<?php echo $_mduDate;?>';
					$('select[name="month"] option').each(function(){
						if($(this).val()==_mduMonth){
							$(this).attr('selected','true');
						}
					});
					function locationHref(val){		
						window.location.href='?date='+val;
					}
					</script>
    				<div id="user_day" style="height:400px;"></div>
    				</div>
    				<!-- ECharts单文件引入 -->    				
    				<script type="text/javascript">
			        function getDaysStr(){
				        var _iC = getDays();
				        var _str = new Array();
				        for(i=0;i<_iC;i++){
				        	_str[i] = i+1;
				        }
				        return _str;
			        }
			        function getDaysData(){
			        	var _mduRes = eval('('+'<?php echo $mduRes;?>'+')');
				        var _iC = getDays();
				        var _str = new Array();
				        for(i=0;i<_iC;i++){
				        	_str[i] = _mduRes[i+1];
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
			                	        text: '每月每天的注册量',
			                	        subtext: ''
			                	    },
			                	    tooltip : {
			                	        trigger: 'axis'
			                	    },
			                	    legend: {
			                	        data:['最高注册量']
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
			                	            data : getDaysStr()
			                	        }
			                	    ],
			                	    yAxis : [
			                	        {
			                	            type : 'value',
			                	            axisLabel : {
			                	                formatter: '{value} 人'
			                	            }
			                	        }
			                	    ],
			                	    series : [
			                	        {
			                	            name:'最高注册量',
			                	            type:'line',
			                	            data:getDaysData(),
			                	            markPoint : {
			                	                data : [
			                	                    {type : 'max', name: '最大值'},
			                	                    {type : 'min', name: '最小值'}
			                	                ]
			                	            },
			                	            markLine : {
			                	                data : [
			                	                    {type : 'average', name: '平均值'}
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
    				
    				
    				
    				<div style="height:100px;">
    				<p>点击查看相应的统计图表</p>
    				<a href="chart.php"  class="easyui-linkbutton" iconCls="icon-tip" >每天的注册量</a> | 
    				<a href="chart.ins.day.service.php" class="easyui-linkbutton" iconCls="icon-tip" >每人每天的保险单统计</a> |
    				<a href="chart.ins.sync.user.php" class="easyui-linkbutton" iconCls="icon-tip" >绑定与非绑定保单统计</a> |
    				<a href="chart.sign.user.php" class="easyui-linkbutton" iconCls="icon-tip" >每天签到统计</a> |    
    				</div>
    				
    				<div id="resultLoad"></div>