<div class="layui-tab layui-tab-brief" lay-filter="flow4_step5_tab">
  	<ul class="layui-tab-title">
	    <li class="layui-this" lay-id="1">待录入<span id="flow4_step5_tabli1" style="display: none;"></span></li>
	    <li lay-id="2">考试通过<span id="flow4_step5_tabli2" style="display: none;"></span></li>
	    <li lay-id="3">考试不通过<span id="flow4_step5_tabli3" style="display: none;"></span></li>
	    <li lay-id="4">所有<span id="flow4_step5_tabli4" style="display: none;"></span></li>
  	</ul>
  	<span style="float: right;display: inline-block;margin-top: -30px;margin-right: 10px;">合格线设置：<span style="color: red;" id="flow4_step5_stant1">未设置</span>【面试成绩占比（%）：<span style="color: red;" id="flow4_step5_stant2">--</span>；笔试成绩占比（%）：<span style="color: red;" id="flow4_step5_stant3"></span>；合格线：<span style="color: red;" id="flow4_step5_stant4">--</span>】</span>
  	<div class="layui-tab-content" style="padding: 0;">
	    <div class="layui-tab-item layui-show" id="flow4_step5_datagrid_parentdiv">
	     	<div id="flow4_step5_datagrid">
	
			</div>
	    </div>
  </div>
</div>
<div id="flow4_step5_toolbar" style="padding:5px">
	<div class="layui-form">
		<div class="layui-form-item" style="margin-bottom: 0;">
		    <div class="layui-inline" style="margin-bottom: 0;">
		      	<label class="layui-form-label" style="width: auto;font-size: 12px;">姓名</label>
		      	<div class="layui-input-inline" style="margin-right: 0;width: 120px;">
		        	<input id="perName" name="perName" class="layui-input">
		      	</div>
		    </div>
		    
		    <div class="layui-inline" style="margin-bottom: 0;">
		      	<label class="layui-form-label" style="width: auto;font-size: 12px;">身份证号</label>
		      	<div class="layui-input-inline" style="margin-right: 0;width: 120px;">
		        	<input id="perIDCard" name="perIDCard" class="layui-input">
		      	</div>
		    </div>
		    
		    <div class="layui-inline" style="margin-bottom: 0;">
		      	<label class="layui-form-label" style="width: auto;font-size: 12px;">准考证号</label>
		      	<div class="layui-input-inline" style="margin-right: 0;width: 120px;">
		        	<input id="perTicketNo" name="perTicketNo" class="layui-input">
		      	</div>
		    </div>
		    
		    <div class="layui-inline" style="margin-bottom: 0;">
		      	<label class="layui-form-label" style="width: auto;font-size: 12px;">考试结果</label>
		      	<div class="layui-input-inline" style="margin-right: 0;width: 120px;">
			        <select id="perExamResult" name="perExamResult"  lay-search="">
			          	<option value=""></option>
			          	<option value="0">待处理</option>
			          	<option value="1">通过</option>
			          	<option value="2">不通过</option>
			          	<option value="3">无成绩</option>
			        </select>
		      	</div>
		    </div>
		    <div class="layui-inline" style="margin-bottom: 0;float: right;">
			  	<div class="layui-btn-group">
				    <button class="layui-btn" onclick="init_flow4_step5_datagrid();"><i class="layui-icon">&#xe615;</i></button>
				    <button class="layui-btn layui-btn-primary" onclick="init_flow4_step5_cancle();"><i class="layui-icon">&#xe640;</i></button>
			 	</div>
		 	</div>
	  	</div>
	</div>
</div>

<ul class="tabsMoreList" id="flow4_step5_msgsend_tips" style="margin-left:0px;right:0px;bottom:53px;top:auto">
	<li rel="flow4_step5_msgsend_tips"><a href="javascript:;" onclick="flow4_step5_msgsend_tips(0)" title="通知通过人员">通知通过人员</a></li>
	<li rel="flow4_step5_msgsend_tips"><a href="javascript:;" onclick="flow4_step5_msgsend_tips(1)" title="通知未通过人员（包含无成绩人员）">通知未通过人员</a></li>
</ul>

<script>
var __flow4_step5_datagrid_flag__ = "1";
var __flow3_to__ = "<?= $flow3_to; ?>";
var __flow4_step5_total_flag__ = 0;
var __flow4_step5_export_condition_info__ = [];
var __flow4_step5_all_data__ = {};
var __flow4_step5_stt_info__ = {};
var __flow4_step5_msg_content__ = "";
var __flow4_step5_slite_result_perIDs__ = [];
$(function(){
	layui.use(['element','form','layer'], function(){
		var element = layui.element,
			layer = layui.layer,
			form = layui.form;
		  	element.on('tab(flow4_step5_tab)', function(){
	    	__flow4_step5_datagrid_flag__ = this.getAttribute('lay-id');
	    	init_flow4_step5_datagrid();
	  	});
	  	
	  	form.render('select');
	  	
	  	<?php if($flow3_to > 0){ ?>
	  		return layer.alert('资格审查环节存在未公示的结果');
	  	<?php } ?>
	});
	init_flow4_step5_datagrid();
});

function init_flow4_step5_datagrid(){
	var perName = $("#perName").val().trim();
	var perIDCard = $("#perIDCard").val().trim();
	var perTicketNo = $("#perTicketNo").val().trim();
	var perExamResult = $("#perExamResult").val();
	
	$('#flow4_step5_datagrid').datagrid({
        width:'auto',
        height:'auto',
	    url:"<?= yii\helpers\Url::to(['examinee/exam-result-list']); ?>",
	    method: "post",
	    queryParams: {'recID':__flow4_recID__,'flag':__flow4_step5_datagrid_flag__,'perName':perName,'perIDCard':perIDCard,'perTicketNo':perTicketNo,'perExamResult':perExamResult},
	    striped: true,
	    fixed: true,
	    fitColumns: false,
	    singleSelect: false,
        pagination: true,  
	    rownumbers: true, 
	    pageNumber:1,
	    pageSize:20,
	    pageList:[20,50,100,200],
	    sortName:'perIndex',
	    sortOrder:'ASC',
	    toolbar:"#flow4_step5_toolbar",
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
	        {field:'perTicketNo',title:'准考证号',width:'100',align:'center',rowspan:2,sortable:true},
	        {field:'perGroupSet',title:'组别名称',width:'100',align:'center',rowspan:2,sortable:true},
	        {field:'perViewScore',title:'面试成绩',width:'100',align:'center',rowspan:2,
	        	editor:{
						type:'numberbox',
						options:{
							min:0,
							max:200,
							precision:2
						}
					}
	        },
	        {field:'perPenScore',title:'笔试成绩',width:'100',align:'center',rowspan:2,sortable:true,
	        	editor:{
					type:'numberbox',
					options:{
						min:0,
						max:200,
						precision:2
					}
				}
	        },
	        {field:'perViewPenScore',title:'综合成绩',width:'100',align:'center',rowspan:2},
	        {field:'perGradePub',title:'成绩公示',width:'100',align:'center',rowspan:2},
	        {field:'perExamResult',title:'考试结果',width:'100',align:'center',rowspan:2,
	        	formatter:function(value,row,index){
	        		if(value == "0"){
	        			return "待处理";
	        		}else if(value == "1"){
	        			return "通过";
	        		}else if(value == "2"){
	        			return "不通过";
	        		}else{
	        			return "无成绩";
	        		}
	        	}
	        },
	        {field:'perRead3',title:'通知阅读情况',width:'100',align:'center',rowspan:2,sortable:true},
	        
	        {field:'perTJ',title:'体检反馈情况',width:'300',colspan:3,align:'center'}
	        ],[
		    	{field:'perReResult3',title:'反馈结果',width:'10%',align:'center',sortable:true},
		    	{field:'perReGiveup3',title:'放弃原因',width:'10%',align:'center'},
		    	{field:'perReTime3',title:'反馈时间',width:'130',align:'center',sortable:true,
		    		formatter:function(value,row,index){
		        		return value == "0000-00-00 00:00:00" ? "" : value;
		        	}
			    }
	    ]],
	    onBeforeEdit: function (rowIndex, rowData, changes) {
            return true;
        },
        onDblClickRow: function (rowIndex, rowData) {
        	if(__flow4_show_flag__ == "0"){
	    		return;
	    	}
        	
        	if(rowData.perPub3 == 1){
        		return;
        	}
        	
        	layui.use('layer',function(){
        		if(rowData.perViewPenScore == '' || rowData.perViewPenScore == null){
        			return layer.msg('未参加考试人员，不允许修改');
        		}else if(rowData.perGradePub1 == 1)
        			return layer.msg('成绩已经公示了，不允许修改');
        		else{
        			$('#flow4_step5_datagrid').datagrid("beginEdit", rowIndex);
        		}
        	});
        },
        onLoadSuccess: function(data){
        	var stt_info = data.stt_info;
        	__flow4_step5_stt_info__ = data.stt_info;
        	$("#flow4_step5_stant1").html("");
        	$("#flow4_step5_stant2").html("");
        	$("#flow4_step5_stant3").html("");
        	$("#flow4_step5_stant4").html("");
        	if(stt_info == "" || stt_info == null){
        		$("#flow4_step5_stant1").html("未设置");
	        	$("#flow4_step5_stant2").html("--");
	        	$("#flow4_step5_stant3").html("--");
	        	$("#flow4_step5_stant4").html("--");
        	}else{
        		$("#flow4_step5_stant1").html('已设置');
	        	$("#flow4_step5_stant2").html(stt_info.sttView);
	        	$("#flow4_step5_stant3").html(stt_info.sttPen);
	        	$("#flow4_step5_stant4").html(stt_info.sttFinalScore);
        	}
        	
      		$("#stepIndex_four_head_pubinfo").html('');
			$("#stepIndex_four_head_pubinfo").html('公示状态：'+ (data.pub_flag == 0 ? '未公示' : (data.pub_flag == 1 ? '暂无数据' : '已公示')));
        	
        	$("#flow4_step5_tabli1").html("");
        	$("#flow4_step5_tabli2").html("");
        	$("#flow4_step5_tabli3").html("");
        	$("#flow4_step5_tabli4").html("");
        	var headInfo = data.headInfo;
        	$("#flow4_step5_tabli1").html("("+headInfo.tab1+")");
        	$("#flow4_step5_tabli2").html("("+headInfo.tab2+")");
        	$("#flow4_step5_tabli3").html("("+headInfo.tab3+")");
        	$("#flow4_step5_tabli4").html("("+headInfo.tab4+")");
        	$("#flow4_step5_tabli1").css("display","");
        	$("#flow4_step5_tabli2").css("display","");
        	$("#flow4_step5_tabli3").css("display","");
        	$("#flow4_step5_tabli4").css("display","");
        	
        	__flow4_step5_total_flag__ = data.total;
        	__flow4_step5_export_condition_info__ = data.exportInfo.condition;
        	__flow4_step5_all_data__ = headInfo;
        	
			$('#flow4_step5_datagrid').datagrid('resize',{
	    		height: $(window).height()-124-25-100
	    	});
	    	
	    	if(__flow4_show_flag__ == "1"){
	    		if(data.pub_flag == 1){
	    			$("#flow4_step5_datagrid").datagrid('getPager').pagination({});
	    		}else if(data.pub_flag == 2){
	    			$("#flow4_step5_datagrid").datagrid('getPager').pagination({
			    		buttons:[{
				   			iconCls:'icon-tip',text:'短信提醒',
						   	handler:function(){
						   		manager_showMore(this,'flow4_step5_msgsend_tips');
							}
				   		},'-',{
						  	iconCls:'icon-export',
						   	text:'Excel导出',
						   	handler:function(){
						   		flow4_step5_exam_export();
						   	}
					   	}]
					});
	    		}else{
	    			$("#flow4_step5_datagrid").datagrid('getPager').pagination({
			    		buttons:[{
						  	iconCls:'icon-import',
						   	text:'Excel导入',
						   	handler:function(){
						   		layui.use('layer',function(){
						   			var layer = layui.layer;
						   			layer.open({
							    		type:2,
							    		title:'导入考试成绩',
							    		area:["500px",'350px'],
							    		content:"<?= yii\helpers\Url::to(['exam/import-step5']); ?>"+"&recID="+__flow4_recID__,
							    		btn:['上传','取消'],
							    		yes: function(){
							    			$("iframe[id*='layui-layer-iframe'")[0].contentWindow.step5_import_data_sure(); 
								        },
							    		btn2:function(){
							    			layer.closeAll();
							    		}
								    });
						   		});
						   	}
					   	},'-',{
					   		iconCls:'icon-save',text:'保存',
						   	handler:function(){
						   		flow4_step5_datagrid_edit_to_save();
							}
				   		},'-',{
					   		iconCls:'icon-undo',text:'取消',
						   	handler:function(){
						   		init_flow4_step5_datagrid();
							}
				   		},'-',{
						  	iconCls:'icon-edit',
						   	text:'微调',
						   	handler:function(){
						   		layui.use('layer',function(){
						   			var layer = layui.layer;
									var rows = $("#flow4_step5_datagrid").datagrid('getSelections');
									var len = rows.length;
									if(len == 0){
										return layer.alert("请勾选要微调的人员");
									}
									var flag = 0;
									for(var i = 0 ; i < len ; i++){
										if(rows[i]['perViewScore'] == "" || rows[i]['perViewScore'] == null){
											flag = 1;
											break;
										}
										if(rows[i]['perPenScore'] == "" || rows[i]['perPenScore'] == null){
											flag = 1;
											break;
										}
								        __flow4_step5_slite_result_perIDs__.push(rows[i]['perID']);
									}
									if(flag == 1){
										return layer.alert("不可微调！所选人员中存在面试或笔试成绩为空的人员");
									}
									layer.open({
								  		type:2,
								  		title:'微调',
								  		area: ['400px', '250px'],
								  		content:"<?= yii\helpers\Url::to(['exam/slite-result-step5']); ?>"+"&recID="+__flow4_recID__,
								  		btn:['确定','关闭'],
								  		yes: function(){
								  			$("iframe[id*='layui-layer-iframe'")[0].contentWindow.flow4_step5_slite_result_sure(); 
									    },
								  		btn2:function(){
								  			layer.close(layer.getFrameIndex(window.name));
								  		}
								    });
						   		});
						   	}
					   	},'-',{
						  	iconCls:'icon-filter',
						   	text:'设置合格线',
						   	handler:function(){
						   		layer.open({
							  		type:2,
							  		title:'设置合格线',
							  		area:["360px",'300px'],
							  		content:"<?= yii\helpers\Url::to(['exam/stant-line-step5']); ?>"+"&recID="+__flow4_recID__,
							  		btn:['保存','取消'],
							  		yes: function(){
							  			$("iframe[id*='layui-layer-iframe'")[0].contentWindow.flow4_step5_stant_line_save(); 
								    },
							  		btn2:function(){
							  			layer.close(layer.getFrameIndex(window.name));
							  		}
							    });
						   	}
					   	},'-','-','-',{
				   			iconCls:'icon-pub',text:'成绩公示',
						   	handler:function(){
						   		layui.use('layer',function(){
						   			if(data.headInfo.tab1 > 0){
						   				return layer.alert('存在未录入成绩的人员，请全部录入后再公示成绩');
						   			}
						   			
						   			layer.confirm('确定要公示成绩么？',function(){
						   				$.post("<?= yii\helpers\Url::to(['examinee/exam-result-pub-grade']); ?>",{'recID':__flow4_recID__},function(json){
						   					if(json.result){
						   						layer.msg(json.msg);
						   						init_flow4_step5_datagrid();
						   					}else{
						   						layer.alert(json.msg);
						   					}
						   				},'json');
						   			});
						   			
						   		});
							}
				   		},'-',{
				   			iconCls:'icon-pub',text:'结果公示',
						   	handler:function(){
						   		layui.use('layer',function(){
						   			if(data.headInfo.tab1 > 0){
						   				return layer.alert('存在未录入成绩的人员，请全部录入后再公示成绩');
						   			}
						   			
						   			if(data.stt_info == "" || data.stt_info == null){
						   				return layer.alert('合格线还未设置，请先设置');
						   			}
						   			
						   			layer.confirm('确定要公示考试结果么？',function(){
						   				$.post("<?= yii\helpers\Url::to(['examinee/exam-result-pub-result']); ?>",{'recID':__flow4_recID__},function(json){
						   					if(json.result){
						   						layer.msg(json.msg);
						   						init_flow4_step5_datagrid();
						   					}else{
						   						layer.alert(json.msg);
						   					}
						   				},'json');
						   			});
						   			
						   		});
							}
				   		},'-',{
				   			iconCls:'icon-tip',text:'短信提醒',
						   	handler:function(){
						   		manager_showMore(this,'flow4_step5_msgsend_tips');
							}
				   		},'-',{
						  	iconCls:'icon-export',
						   	text:'Excel导出',
						   	handler:function(){
						   		flow4_step5_exam_export();
						   	}
					   	}]
					});
	    		}
	    	}else{
	    		$("#flow4_step5_datagrid").datagrid('getPager').pagination({
		    		buttons:[{
			   			iconCls:'icon-tip',text:'短信提醒',
					   	handler:function(){
					   		manager_showMore(this,'flow4_step5_msgsend_tips');
						}
			   		},'-',{
					  	iconCls:'icon-export',
					   	text:'Excel导出',
					   	handler:function(){
					   		flow4_step5_exam_export();
					   	}
				   	}]
				});
	    	}
        }
    });
}

function flow4_step5_datagrid_edit_to_save(){
	layui.use('layer',function(){
		var layer = layui.layer;
		var check = before_save_checked('flow4_step5_datagrid', 'flow4_step5_datagrid_parentdiv');
	  	if(check != 0) {
	    	return;
	  	}
	  	
	  	var rows = $("#flow4_step5_datagrid").datagrid("getRows");
		for(var i = 0; i < rows.length; i++){
			$('#flow4_step5_datagrid').datagrid("endEdit", i);
		}
	  	
	  	layer.confirm('确定要保存么', function(index){
	  		var data_infos = get_datagrid_save_info();
	  		$.post("<?= yii\helpers\Url::to(['examinee/exam-result-mod-save']); ?>", {"data_infos": data_infos,"recID":__flow4_recID__}, function (json) {
	            if(json.result){
	            	layer.msg(json.msg);
	            	init_flow4_step5_datagrid();
	            }else{
	            	layer.alert(json.msg);
	            }
	        });
	  	},function(){
	  		init_flow4_step5_datagrid();
	  	});
  	});
}

function get_datagrid_save_info() {
    var grid_infos = [];
    var grid_obj = {};
    var rows = $("#flow4_step5_datagrid").datagrid("getRows");
    var grid_edit_columns = ["perID","perViewScore", "perPenScore"];
    
    for (var i = 0; i < rows.length; i++) {
        grid_obj = {};
        grid_obj['perID'] = rows[i]['perID'];
        grid_obj['perViewScore'] = rows[i]['perViewScore'];
        grid_obj['perPenScore'] = rows[i]['perPenScore'];
        for (var j = 0; j < grid_edit_columns.length; j++) {
            grid_obj[grid_edit_columns[j]] = rows[i][grid_edit_columns[j]] == "" ? "" : rows[i][grid_edit_columns[j]];
        }
        grid_infos.push(grid_obj);
    }
    return grid_infos;
}

function before_save_checked(gridId,parentDivId){
	var rows = $("#"+gridId).datagrid("getRows");
	if(rows.length == 0){
		layer.alert("当前表格数据为空，无需保存！");
		return 1;
	}
	$editNodes = $(".datagrid-editable-input");
	if($editNodes == null || $editNodes == undefined || $editNodes.length == 0){
		layer.alert("表格数据没有编辑，无需保存！");
		return 1;
	}
	var check = 0;
	var field = "";
	var perentObj = null;
	var _curRow = null;
	var tdobj = null;
	var _val = '';
	var _index = '';
	var alertcontent = '';
	var checkMsg = '';
	$("#"+parentDivId).find(".datagrid-btable").find("tr").each(function (n){
		_curRow = $(this);
		tdobj = $(this).find("td[field]");
		$(tdobj).find(".datagrid-editable-input").each(function(){
			perentObj = $(this).parent().parent().parent().parent().parent().parent();
			field = perentObj.attr("field");
	 		_val=$(this).val();
			fieldyh=$(".datagrid-view2").find(".datagrid-header").find("tbody td[field='"+field+"']").find("span").html();
			_index=_curRow.attr("datagrid-row-index");
		    if(_val==''){
		    	checkMsg = "不允许为空！";
        		check = 1;
				alertcontent +="第"+(parseInt(_index)+1)+"行记录中“"+fieldyh+"”，"+checkMsg+"<br>";
			}
		});
	});
	if(check == 1){
		layer.alert(alertcontent);
	}
	return check;
}

function flow4_step5_msgsend_tips(type){
	layui.use('layer',function(){
		var layer = layui.layer;
		if(__flow4_step5_all_data__.tab4 == 0){
			return layer.alert("没有考生，不需要短信通知");
		}
		
		if(type == 0){
			if(__flow4_step5_all_data__.tab2 == 0){
				return layer.alert("没有考试通过的考生，不需要短信通知");
			}
		}else{
			if(__flow4_step5_all_data__.tab3 == 0){
				return layer.alert("没有考试不通过（包含无成绩）的考生，不需要短信通知");
			}
		}
		
		layer.prompt({
		  	formType: 2,
		  	value: '',
		  	title: '编辑短信通知内容',
		  	area: ['300px', '150px']
		}, function(value, index, elem){
			  	__flow4_step5_msg_content__ = value;
	    	 	layer.open({
			  		type:2,
			  		title:'确认短信发送',
			  		area:[$(window).width()*3/4+"px",'520px'],
			  		content:"<?= yii\helpers\Url::to(['exam/send-msg-step5']); ?>"+"&recID="+__flow4_recID__+"&type="+type,
			  		btn:['发送','关闭'],
			  		yes: function(){
			  			$("iframe[id*='layui-layer-iframe'")[0].contentWindow.flow4_step5_send_msg_sure(); 
				    },
			  		btn2:function(){
			  			layer.close(layer.getFrameIndex(window.name));
			  		}
			    });
		});
	});
}


function flow4_step5_exam_export(){
	layui.use('layer',function(){
		var layer = layui.layer;
		if(__flow4_step5_total_flag__ == 0){
			return layer.alert('当前没有数据，不需要导出');
		}
		window.open("<?= yii\helpers\Url::to(['examinee/exam-result-export']); ?>"+"&recID="+__flow4_recID__+"&condition="+JSON.stringify(__flow4_step5_export_condition_info__));
	});
}

function init_flow4_step5_cancle(){
	layui.use('form', function(){
		var form = layui.form;
		$("#perName").val("");
		$("#perIDCard").val("");
		$("#perTicketNo").val("");
		$("#perExamResult").val("");
	  	form.render('select');
	  	init_flow4_step5_datagrid();
	});
}
</script>