<?php
	include_once 'header.php';
	$cObj = new ChartController();
	$idsRes = $cObj->getInsSyncUser();
?>
<script language="javascript" type="text/javascript" src="<?php echo APP_WEBSITE,'public/js/';?>My97DatePicker/WdatePicker.js"></script>

<div style="border:solid 1px;border-color:#BFBFBF;">
<!-- 为ECharts准备一个具备大小（宽高）的Dom -->
    				<div id="user_day" style="height:400px;"></div>
    				</div>
    				<!-- ECharts单文件引入 -->
    				<script type="text/javascript">
				        require.config({
				            paths: {
				                echarts: '../js/chart/'
				            }
				        });			        
					     // 使用
				        require(
				            [
				                'echarts',
				                'echarts/chart/pie',
				                'echarts/chart/funnel'
				            ],
				            function (ec) {
				                // 基于准备好的dom，初始化echarts图表
				                var myChart = ec.init(document.getElementById('user_day'));
				                option = {
				                	    title : {
				                	        text: '绑定用户与非绑定用户',
				                	        subtext: '',
				                	        x:'center'
				                	    },
				                	    tooltip : {
				                	        trigger: 'item',
				                	        formatter: "{a} <br/>{b} : {c} ({d}%)"
				                	    },
				                	    legend: {
				                	        orient : 'vertical',
				                	        x : 'left',
				                	        data:['绑定用户','非绑定用户']
				                	    },
				                	    toolbox: {
				                	        show : true,
				                	        feature : {
				                	            mark : {show: true},
				                	            dataView : {show: true, readOnly: false},
				                	            magicType : {
				                	                show: true, 
				                	                type: ['pie', 'funnel'],
				                	                option: {
				                	                    funnel: {
				                	                        x: '25%',
				                	                        width: '50%',
				                	                        funnelAlign: 'left',
				                	                        max: <?php echo $idsRes['syncNoNum']>$idsRes['syncNum']?$idsRes['syncNoNum']:$idsRes['syncNum'];?>
				                	                    }
				                	                }
				                	            },
				                	            restore : {show: true},
				                	            saveAsImage : {show: true}
				                	        }
				                	    },
				                	    calculable : true,
				                	    series : [
				                	        {
				                	            name:'保单量',
				                	            type:'pie',
				                	            radius : '55%',
				                	            center: ['50%', '60%'],
				                	            data:[
				                	                {value:<?php echo $idsRes['syncNum'];?>, name:'绑定用户'},
				                	                {value:<?php echo $idsRes['syncNoNum'];?>, name:'非绑定用户'}
				                	            ]
				                	        }
				                	    ]
				                	};
				        
				                // 为echarts对象加载数据 
				                myChart.setOption(option); 
				            }
				        );
				    	</script>
				    	<a href="javascript:history.go(-1);"  class="easyui-linkbutton" iconCls="icon-back" >返回</a>