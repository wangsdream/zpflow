<div class="headinfo">
	<div class="layui-form head-select">
		<div class="layui-inline" style="margin-bottom: 0;">
	      	<label class="layui-form-label" style="width: auto;font-size: 12px;padding: 5px 10px 5px 2px;"><b>招聘批次</b></label>
	      	<div class="layui-input-inline" style="margin-right: 0;width: auto;height: 30px;">
		        <select id="recID_id" name="recID_name" lay-filter="recID_REC" class="input1" onchange="selRecruitID(this)">
		          	<?php foreach($pcInfo as $pc){ ?>
			        	<option defaultplace="<?php echo $pc['recViewPlace']; ?>" recend="<?php echo $pc['recend']; ?>" code="<?php echo $pc['code']; ?>" value="<?php echo $pc['id']; ?>"><?php echo $pc['value']; ?></option>
			        <?php } ?>
		        </select>
	      	</div>
	    </div>
	    <div class="layui-inline" style="margin-bottom: 0;">
	    	<label class="layui-form-label" style="width: auto;font-size: 12px;padding: 5px 10px 5px 2px;">
	      		<span id="stepIndex_six_head_pubinfo" style="color: red;"></span>
	      	</label>
	    </div>
	</div>
</div>
<div id="stepIndex_six_toolbar" style="padding:5px">
	<div class="layui-form">
		<div class="layui-form-item" style="margin-bottom: 0;">
		    <div class="layui-inline" style="margin-bottom: 0;">
		      	<label class="layui-form-label" style="width: auto;font-size: 12px;">姓名</label>
		      	<div class="layui-input-inline" style="margin-right: 0;width: auto;">
		        	<input id="perName" name="perName" class="layui-input">
		      	</div>
		    </div>
		    <div class="layui-inline" style="margin-bottom: 0;">
		      	<label class="layui-form-label" style="width: auto;font-size: 12px;">性别</label>
		      	<div class="layui-input-inline" style="margin-right: 0;width: auto;">
			        <select id="perGender" name="perGender"  lay-search="">
			          	<option value=""></option>
				        <option value="1">男</option>
				        <option value="2">女</option>
			        </select>
		      	</div>
		    </div>
		    <div class="layui-inline" style="margin-bottom: 0;">
		      	<label class="layui-form-label" style="width: auto;font-size: 12px;">身份证号</label>
		      	<div class="layui-input-inline" style="margin-right: 0;width: auto;">
		        	<input id="perIDCard" name="perIDCard" class="layui-input">
		      	</div>
		    </div>
		    
		    <div class="layui-inline" style="margin-bottom: 0;float: right;">
			  	<div class="layui-btn-group">
				    <button class="layui-btn" onclick="init_stepIndex_six_grid()"><i class="layui-icon">&#xe615;</i></button>
				    <button class="layui-btn layui-btn-primary" onclick="stepIndex_six_clear()"><i class="layui-icon">&#xe640;</i></button>
			 	</div>
		 	</div>
	  	</div>
	  	
	</div>
</div>

<hr class="layui-bg-blue" style="margin: 1px 0;">
<div class="layui-tab layui-tab-brief" lay-filter="stepIndex_six_tab">
  	<ul class="layui-tab-title" id="stepIndex_six_tab">
	    <li class="layui-this" lay-id="1">待审核<span id="stepIndex_six_tabli1" style="display: none;"></span></li>
	    <li lay-id="2">审核通过<span id="stepIndex_six_tabli2" style="display: none;"></span></li>
	    <li lay-id="3">审核不通过<span id="stepIndex_six_tabli3" style="display: none;"></span></li>
	    <li lay-id="4">放弃政审<span id="stepIndex_six_tabli4" style="display: none;"></span></li>
	    <li lay-id="5">所有<span id="stepIndex_six_tabli5" style="display: none;"></span></li>
  	</ul>
  	<div class="layui-tab-content" style="padding: 0;"> 
	    <div class="layui-tab-item layui-show">
	     	<div id="stepIndex_six">
				
			</div>
	    </div>
  </div>
</div>

<script>
var __flow6_recID__ = "";
var __flow6_show_flag__ = "";
var __flow6_datagrid_flag__ = "1";
var __flow6_condition__ ;
var __flow6_total__ = 0;
var __flow6_headInfo__ ;
var __flow6_msg_content__ = "";
$(function(){
	__flow6_recID__ = $("#recID_id").val();
	__flow6_show_flag__ = $("#recID_id option:selected").attr("code");
	init_stepIndex_six_grid();
	layui.use(['element','form','layer', 'laydate'], function(){
		var element = layui.element;
		var form = layui.form;
		  	element.on('tab(stepIndex_six_tab)', function(){
	    	__flow6_datagrid_flag__ = this.getAttribute('lay-id');
	    	init_stepIndex_six_grid();
	  	});
	  	
	  	form.render('select');
	  	form.on('select(recID_REC)', function(data){
	  		__flow6_recID__ = data.value;
			__flow6_show_flag__ = $("#recID_id option:selected").attr("code");
			init_stepIndex_six_grid();
		});
	});
});

function init_stepIndex_six_grid(){
	var perName = $("#perName").val().trim();
	var perGender = $("#perGender").val();
	var perIDCard = $("#perIDCard").val().trim();
	
	$('#stepIndex_six').datagrid({
        width:'auto',
        height:'auto',
	    fitColumns: false,
	    singleSelect: false,
	    rownumbers: true,
	    method: "post",
	    queryParams: {'perName':perName,'perGender':perGender,'perIDCard':perIDCard,'recID':__flow6_recID__,'flag':__flow6_datagrid_flag__},
      	url:"<?= yii\helpers\Url::to(['careful/list-info']); ?>",
      	rownumbers: true, 
      	sortName:'',
	    sortOrder:'',
	    pagination: true, 
	    toolbar:'#stepIndex_six_toolbar',
        frozenColumns:[[
    		{field:'ck',checkbox:true},
	        {field:'perIndex',title:'报名序号',width:'80',align:'center',sortable:true},
	        {field:'perName',title:'姓名',width:'70',align:'center',sortable:true},
        ]], 
        columns:[[
	        {field:'perIDCard',title:'身份证号',width:'180',align:'center',rowspan:2,sortable:true},
	        {field:'perGender',title:'性别',width:'5%',align:'center',rowspan:2,sortable:true},
	        {field:'perBirth',title:'出生年月',width:'100',align:'center',rowspan:2,sortable:true},
	        {field:'perJob',title:'应聘岗位性质',width:'8%',align:'center',rowspan:2,sortable:true},
	        {field:'perPhone',title:'手机号码',width:'100',align:'center',rowspan:2},
	        {field:'perCarefulStatus',title:'政审结果',width:'100',align:'center',rowspan:2,sortable:true},
	        {field:'perCarefulReson',title:'政审不通过原因',width:'200',align:'center',rowspan:2},
	        {field:'perPub6',title:'公示结果',width:'100',align:'center',rowspan:2,sortable:true},
	        {field:'perRead6',title:'通知阅读情况',width:'100',align:'center',rowspan:2,sortable:true},
	        {field:'perZSFKJG',title:'政审反馈结果',width:'300',colspan:3,align:'center'},
	        {field:'perCarefulMark',title:'备注',width:'300',rowspan:2,align:'center'}
	        ],[
		    	{field:'perReResult5',title:'反馈结果',width:'10%',align:'center',sortable:true},
		    	{field:'perReGiveup5',title:'放弃原因',width:'200',align:'center'},
		    	{field:'perReTime5',title:'反馈时间',width:'130',align:'center',sortable:true,
		    		formatter:function(value,row,index){
		        		return value == "0000-00-00 00:00:00" ? "" : value;
		        	}
			   	}
	    ]],
        onLoadSuccess: function(data){
        	if(data.pub_flag == 1){
        		$("#stepIndex_six_head_pubinfo").html('');
				$("#stepIndex_six_head_pubinfo").html('暂无数据');
        	}else{
        		$("#stepIndex_six_head_pubinfo").html('');
				$("#stepIndex_six_head_pubinfo").html('【已公示：'+data.pub_info.ygs+'人，未公示：'+data.pub_info.wgs+'人】');
        	}
        	
			__flow6_condition__ = data.exportInfo.condition;
			__flow6_total__ = data.total;
			__flow6_headInfo__ = data.headInfo;
			
			$("#stepIndex_six_tabli1").html("");
        	$("#stepIndex_six_tabli2").html("");
        	$("#stepIndex_six_tabli3").html("");
        	$("#stepIndex_six_tabli4").html("");
        	$("#stepIndex_six_tabli5").html("");
        	
        	$("#stepIndex_six_tabli1").html("("+data.headInfo.tab1+")");
        	$("#stepIndex_six_tabli2").html("("+data.headInfo.tab2+")");
        	$("#stepIndex_six_tabli3").html("("+data.headInfo.tab3+")");
        	$("#stepIndex_six_tabli4").html("("+data.headInfo.tab4+")");
        	$("#stepIndex_six_tabli5").html("("+data.headInfo.tab5+")");
        	
        	$("#stepIndex_six_tabli1").css("display","");
        	$("#stepIndex_six_tabli2").css("display","");
        	$("#stepIndex_six_tabli3").css("display","");
        	$("#stepIndex_six_tabli4").css("display","");
        	$("#stepIndex_six_tabli5").css("display","");
			
        	$('#stepIndex_six').datagrid('resize',{
	    		height: $(window).height()-124-25-50
	    	});
	    	
	    	if(__flow6_show_flag__ == "1"){
	    		if(data.pub_flag == 1){
	    			$("#stepIndex_six").datagrid('getPager').pagination({});
	    		}else if(data.pub_flag == 0){
	    			if(data.flow_to.flow3_to == 0 && data.flow_to.flow4_to == 0 && data.flow_to.flow5_to == 0){
	    				$("#stepIndex_six").datagrid('getPager').pagination({
				    		buttons:[{
					   			iconCls:'icon-ok',text:'审核通过',
							   	handler:function(){
							   		stepIndex_six_check(1);
								}
					   		},'-',{
					   			iconCls:'icon-cancel',text:'审核不通过',
							   	handler:function(){
							   		stepIndex_six_check(2);
								}
					   		},'-',{
					   			iconCls:'icon-import',text:'Excel导入',
							   	handler:function(){
							   		stepIndex_six_import();
								}
					   		},'-',{
					   			iconCls:'icon-pub',text:'结果公示',
							   	handler:function(){
							   		layui.use('layer',function(){
										var layer = layui.layer;
										if(__flow6_headInfo__.tab5 == 0){
											return layer.alert("暂无考生信息，不需要公布");
										}
										
										if(__flow6_headInfo__.tab1 != 0){
											return layer.alert("存在未审核的人员，请全部审核后再公示");
										}
										
										parent.layer.confirm('您确定公示政审结果么？', function(index){
											$.post("<?= yii\helpers\Url::to(['careful/pub-info']) ?>",{
													'recID':__flow6_recID__
												},function(json){
												if(json.result){
													init_stepIndex_six_grid();
													parent.layer.msg(json.msg);
													parent.layer.closeAll();
												}else{
													parent.layer.alert(json.msg);
												}
											},'json');
										});
									});
								}
					   		},'-',{
					   			iconCls:'icon-tip',text:'短信提醒',
							   	handler:function(){
							   		stepIndex_six_sendmsg_info();
								}
					   		},'-',{
							  	iconCls:'icon-export',
							   	text:'Excel导出',
							   	handler:function(){
							   		stepIndex_six_export_info();
							   	}
						   	}]
						});
	    			}else{
	    				$("#stepIndex_six").datagrid('getPager').pagination({
				    		buttons:[{
					   			iconCls:'icon-ok',text:'审核通过',
							   	handler:function(){
							   		stepIndex_six_check(1);
								}
					   		},'-',{
					   			iconCls:'icon-cancel',text:'审核不通过',
							   	handler:function(){
							   		stepIndex_six_check(2);
								}
					   		},'-',{
					   			iconCls:'icon-import',text:'Excel导入',
							   	handler:function(){
							   		stepIndex_six_import();
								}
					   		},'-',{
					   			iconCls:'icon-tip',text:'短信提醒',
							   	handler:function(){
							   		stepIndex_six_sendmsg_info();
								}
					   		},'-',{
							  	iconCls:'icon-export',
							   	text:'Excel导出',
							   	handler:function(){
							   		stepIndex_six_export_info();
							   	}
						   	}]
						});
	    			}
	    		}else{
	    			$("#stepIndex_six").datagrid('getPager').pagination({
			    		buttons:[{
				   			iconCls:'icon-tip',text:'短信提醒',
						   	handler:function(){
						   		stepIndex_six_sendmsg_info();
							}
				   		},'-',{
						  	iconCls:'icon-export',
						   	text:'Excel导出',
						   	handler:function(){
						   		stepIndex_six_export_info();
						   	}
					   	}]
					});
	    		}
	    	}else{
	    		$("#stepIndex_six").datagrid('getPager').pagination({
		    		buttons:[{
			   			iconCls:'icon-tip',text:'短信提醒',
					   	handler:function(){
					   		stepIndex_six_sendmsg_info();
						}
			   		},'-',{
					  	iconCls:'icon-export',
					   	text:'Excel导出',
					   	handler:function(){
					   		stepIndex_six_export_info();
					   	}
				   	}]
				});
	    	}
	    }
    });
}

function stepIndex_six_check(type){
	var msg = ['0','审核通过','审核不通过'];
	layui.use('layer',function(){
		var layer = layui.layer;
		var rows = $("#stepIndex_six").datagrid('getSelections');
		var len = rows.length;
		if(len == 0){
			layer.alert("请勾选要"+msg[type]+"的人员！");
			return;
		}
		var perIDs = [];
		for(var i = 0; i < len; i++){
			perIDs.push(rows[i]['perID']);
		}
		
		if(type == 1){
			layer.confirm('确定要审核通过勾选的人员么？',function(index){
				$.post("<?= yii\helpers\Url::to(['careful/check-careful']); ?>",{'perIDs':perIDs,'recID':__flow6_recID__,'perCarefulStatus':type,'perCarefulReson':''},function(json){
					if(json.result){
						layer.msg(json.msg);
						init_stepIndex_six_grid();
						layer.close(index);
					}else{
						layer.alert(json.msg);
					}
				},'json');
			});
		}else{
			layer.confirm('您确定要审核不通过勾选的人员么？', function(index){
				layer.prompt({
				  	formType: 2,
				  	value: '',
				  	title: '审核不通过原因',
				  	area: ['300px', '150px']
				}, function(value, index, elem){
				  	$.post("<?= yii\helpers\Url::to(['careful/check-careful']); ?>",{'perIDs':perIDs,'recID':__flow6_recID__,'perCarefulStatus':type,'perCarefulReson':value},function(json){
						if(json.result){
							layer.msg(json.msg);
							init_stepIndex_six_grid();
							layer.closeAll();
						}else{
							layer.alert(json.msg);
						}
					},'json');
				});
			}); 
		}
	});
}

function stepIndex_six_import(){
	layui.use('layer',function(){
		var layer = layui.layer;
		layer.open({
    		type:2,
    		title:'导入政审结果',
    		area:["500px",'350px'],
    		content:"<?= yii\helpers\Url::to(['careful/import']); ?>"+"&recID="+__flow6_recID__,
    		btn:['上传','取消'],
    		yes: function(){
    			$("iframe[id*='layui-layer-iframe'")[0].contentWindow.flow6_import_data_sure(); 
	        },
    		btn2:function(){
    			layer.closeAll();
    		}
	    });
	});
}

function stepIndex_six_sendmsg_info(){
	layui.use('layer',function(){
		var layer = layui.layer;
		layer.prompt({
		  	formType: 2,
		  	value: '',
		  	title: '编辑短信通知内容',
		  	area: ['300px', '150px']
		}, function(value, index, elem){
			  	__flow6_msg_content__ = value;
	    	 	layer.open({
			  		type:2,
			  		title:'确认短信发送',
			  		area:[$(window).width()*3/4+"px",'520px'],
			  		content:"<?= yii\helpers\Url::to(['careful/send-msg']); ?>"+"&recID="+__flow6_recID__,
			  		btn:['发送','关闭'],
			  		yes: function(){
			  			$("iframe[id*='layui-layer-iframe'")[0].contentWindow.flow6_send_msg_sure(); 
				    },
			  		btn2:function(){
			  			layer.close(layer.getFrameIndex(window.name));
			  		}
			    });
		});
	});
}

function stepIndex_six_export_info(){
	layui.use('layer',function(){
		var layer = layui.layer;
		if(__flow6_total__ == 0){
			return layer.alert("当前没有任何数据，不需要导出");
		}else{
			window.open("<?= yii\helpers\Url::to(['careful/export-info']); ?>"+"&recID="+__flow6_recID__+"&condition="+JSON.stringify(__flow6_condition__));
		}
	});
}

function stepIndex_six_clear(){
	layui.use('form', function(){
		var form = layui.form;
		$("#perName").val("");
		$("#perGender").val("");
		$("#perIDCard").val("");
	  	form.render('select');
	  	init_stepIndex_six_grid();
	});
}

function selRecruitID(th){
	__flow6_recID__ = $(th).val();
	__flow6_show_flag__ = $("#recID_id option:selected").attr("code");
	init_stepIndex_six_grid();
}