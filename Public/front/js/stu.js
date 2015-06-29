/*
 *	学生管理js
 *  作者：syl
 */

 $(document).ready(function(){

	 //申请院校 查到院校(跳转到该院校页面)
	 $("#apply").click(function(){

	 	if(stu_id==0)
	 	{ 
	 		alert('您还没有添加学生，请先添加学生后申请院校');
	 		add_stu(0,add_stu_url);
	 		return false;
	 	}
	 	else
	 	{
	 		var suggest_input = $.trim($("#suggest_input").val());
		 	if(!suggest_input)
		 	{ 
		 		alert('请输入院校名称！');
		 		return false;
		 	}
		 	var apply_url = $.trim($("#apply_url").val());
		 	$.post(apply_url,{suggest_input:suggest_input},function(data){ 

		 		if(data.status=='yes')
		 		{ 
		 			var url = data.college_url+'&stu_id='+stu_id+'&flag=apply';
		 			window.location.href = url;
		 			return false;
		 		}
		 		else
		 		{ 
		 			alert(data.msg);
		 			return false;
		 		}

		 	},'json');
	    }

	 });

	//点拒绝的时候
	$("#refuse").click(function(){ 

		$("#act1").hide();
		$("#act2").show();
		$("#reason_content").val('');
		$("#refuse_reason").show();
	})

	//点返回
	$("#go_back").click(function(){ 

		$("#act1").show();
		$("#act2").hide();
		$("#reason_content").val('');
		$("#refuse_reason").hide();

	});

    //学生搜索
     $("#search_student_byname").keydown(function(e){
         var e = e || event,keycode = e.which || e.keyCode;
         var name = $(this).val();

         if (keycode==13 && name != '') {
             $.post('/index.php?m=Home&c=Student&a=searchbyname',{value:name},function(data){
                 if(data.info!=0) {
                     $("#stu_list").html('');
                     var html1="";
                     for (var i = 0; i < data.info.length; i++) {

                         html1+="<li> <i class='icon'></i> <em>"+data.info[i].name+"</em> <a href='"+data.info[i].url+"'>查	看</a> </li>";
                     };

                     $("#stu_list").html(html1);
                     return true;
                 }
                 else
                 {
                     alert('您输入的学生姓名关键字没有匹配相应的学生,请重新搜索！');
                     return false;
                 }

             },'json');
         }
     });


	//交流
	$("#message_form_sub").click(function(){ 

		var data = $("#message_info").serialize();
		var url = $("#message_form_url").val();
		ajax_submit(url,data);
	})

	//申请结果
	$("#apply_result_sub").click(function(){ 

		var status = $('input[name="status"]:checked').val();
		if(!status)
		{ 
			alert('请选择!');
			return false;
		} 
		else
		{ 
			var data = $("#result_form").serialize();
			var url = $("#apply_result_url").val();
			ajax_submit(url,data);

		}
	})

	//签证结果
	$("#visa_form_sub").click(function(){ 

		var status =$('input[name="visa_status"]:checked').val();
		if(!status)
		{ 
			alert('请选择！');
			return false;
		} 
		else
		{ 
			var data = $("#submit_visa_result_form").serialize();
			var url = $("#visa_form_url").val();
			ajax_submit(url,data);
		}

	});

	//终止
	$("#apply_end_sub").click(function(){ 

		var status =$('select[name="end_status"] option:selected').val();
		if(!status)
		{ 
			alert('请选择原因！');
			return false;
		} 
		else
		{ 
			var data = $("#apply_end_from").serialize();
			var url = $("#apply_end_url").val();
			ajax_submit(url,data);
		}

	});

	//获取

 });


 /*
	汉字转英文
*/
function chinesetoEN( xin ,id)
{

	$.post('index.php?m=Home&c=Student&a=character_change',{xin:xin},function(data){
		
		if(data.status=='ok'){
			$("#"+id).val(data.info);
			return true;
		}else{
			return false;
		}
	},'json');
}
/*
 *	添加学生
 */
 function add_stu(stu_id,form_url)
 {
     layer_area('填写学生基本信息(*必填)','stu_form',800,340);
 	/*
 	$.post(form_url,{stu_id:stu_id},function(data){ 

 		if(data.status=='yes')
 	    { 
 	    	if(data.info.length==0)
 	        { 
 	        	document.getElementById("stu_info").reset();
 	        	$("#country_id").val("");
 	        	layer_area('填写学生基本信息(*必填)','stu_form',800,340);
 	        }
 	        else
 	        { 
 	        	$("#xin").val(data.info.xing);
 				$("#mingzi").val(data.info.mingzi);
 				$("#xin_pinyin").val(data.info.xing_pinyin);
 				$("#mingzi_pinyin").val(data.info.minzi_pinyin);
 				$("#birthday").val(data.info.birthday);
 				if(data.info.sex == 1){ 
 			    	$("input[name='sex'][value=1]").attr("checked",true); 
 			    }else{ 
 			    	$("input[name='sex'][value=2]").attr("checked",true);
 			    }
 			    
 			    //国家。省州、城市
 			    $("#country_id").val(data.info.countryid);
				get_area(data.info.countryid,data.info.areaid);
				get_city(data.info.areaid,data.info.cityid);
 				$("#stu_id2").val(data.info.stu_id);
 				$("#id2").val(data.info.id);
 				$("#remark").val(data.info.remark);
 				$("#stu_submit").html("修&nbsp;&nbsp;改")
 				layer_area('修改学生基本信息','stu_form',800,340);
 	        }
 	    }

 	},'json');
 	*/
 }

 /* 
  * 查看输送学生
  */
  function receive_see( receive_id )
  { 
	 	var url = $("#receive_url").val();
	 	if(receive_id)
	 	{ 
	 		$.post(url,{receive_id:receive_id},function(data){ 

	 			if(data.status=="yes")
	 			{ 
	 				if(data.info!="")
	 				{ 
	 					$("#stu_name").html(data.info.stu_name);
	 					$("#zhongjie").html(data.info.intermediary_name);
	 					$("#college_name").html(data.info.college_name);
	 					$("#apply_name").html(data.info.apply_name);
	 					$("#profession").html(data.info.profession);
	 					$("#start_time").html(data.info.start_time);

	 					if(data.info.file.length>0)
	 					{ 
	 						var html = '';
	 						for (var i = 0; i < data.info.file.length; i++) {
	 							html+="<strong>"+data.info.file[i].file_name+"</strong>：";
	 							html+='<a href='+data.info.file[i].file_url+' class="file_a opacity8" target="_blank"><span>查看/下载</span></a>&nbsp;';
	 						};

	 						$("#file").html(html);
	 					}

	 					$("#receive_id").val(data.info.receive_id);
	 				}

                    if(data.address!=""){
                        if(data.address.length>0)
                        {
                            var html = '';
                            for (var i = 0; i < data.address.length; i++) {
                                var checked = '';
                                if(i == 0){
                                    checked = 'checked';
                                }
                                html+='<p><input type="radio" name="address" value="'+data.address[i].address_id+'" '+
                                        checked +
                                        ' disabled /> '+data.address[i].address+
                                '&nbsp;&nbsp;&nbsp;<strong>'+data.address[i].contact+'</strong>&nbsp;[ '+data.address[i].phone+' ]</p>';
                            };

                            $("#address_list").html(html);
                        }
                    }

	 				$("#act1").show();
					$("#act2").hide();
					$("#reason_content").val('');
					$("#refuse_reason").hide();

	 				layer_area('查看学生输送信息','stu_receive_info','700px','450px');
	 			}
	 			else
	 			{ 
	 				alert(data.msg);
	 				window.location.reload();
	 				return false;
	 			}

	 		},'json');
	 	}
  }



/*
 *	删除学生附件
 */
 function del_file_stu(url,id)
 {
 	if(confirm('是否要删除该附件！'))
 	{
	 	$.post(url,{id:id},function(data){

	 		if(data.status=='yes'){
				window.location.reload();
			}else{
				alert(data.msg);
				return false;
			}

	 	},'json');
    }
 }


 /*
  *	接收输送学生
  */
  function receive_act(url)
  { 
  	var id=$("#receive_id").val();
  	if(id)
  	{
        var data = $("#sto_receive_user_form").serialize();
	  	$.post(url,data,function(data){

	  		if(data.status=='yes')
	  		{ 
	  			alert('接收成功!');
	  			window.location.href='index.php?m=Home&c=Student&a=index';
	  			return true;
	  		}
	  		else
	  		{ 
	  			alert(data.msg);
	  			window.location.reload();
	  			return false;
	  		}

	  	},'json');
  	}
  	else
  	{ 
  		return false;
  	}
  } 

  /*
   * 拒绝接收输送学生
   */
   function refuse_act(url)
   { 
   	  var id=$("#receive_id").val();
   	  var content = $("#reason_content").val();
   	  
   	  if(!content)
   	  { 
   	  	alert('请填写拒绝原因！')
   	  	return false;
   	  }
   	  else
   	  {
   	  	$.post(url,{id:id,content:content},function(data){ 
   	  		if(data.status=="yes")
   	  		{ 
   	  			alert('提交成功！');
   	  			window.location.reload();
   	  			return true;
   	  		}
   	  		else
   	  		{ 
   	  			alert(data.msg);
   	  			window.location.href='index.php?m=Home&c=Student&a=index';
   	  			return false;
   	  		}

   	  	},'json')
   	 }
   }

    //提交审核材料
    function submit_school_apply(receive_member_id,stu_apply_id)
    {
        if(receive_member_id && stu_apply_id)
        {
            layer_area('提交审核材料','submit_school_apply',540,450);
        }
    }

    //留言
    function message_info(receive_member_id,stu_apply_id)
    { 
    	if(receive_member_id && stu_apply_id)
    	{ 
    		document.getElementById("message_info").reset();
    		layer_area('交 流','message_form',740,340);
    	}
    }

    //申请结果
    function apply_result(receive_member_id,stu_apply_id)
    { 
    	if(receive_member_id && stu_apply_id) {
            layer_area('申请结果', 'apply_result_form', 740, 340);
        }
    }

    //委托签证
    function visa_apply(receive_member_id,stu_apply_id)
    {
        if(receive_member_id && stu_apply_id)
        {
            layer_area('签证服务','visa_apply',540,500);

            $.post(hl_url,{price:$('#visa_price').val()},function(data){
                if(data.status=="yes")
                {
                    $('#visa_rmb_price').html('RMB ¥'+data.price);
                    $('#hl').html('当前汇率 '+data.hl);
                    return true;
                }
                else
                {
                    alert(data.msg);
                    return false;
                }

            },'json')
        }
    }

    //签证结果
    function visa_results(receive_member_id,stu_apply_id)
    {
    	if(receive_member_id && stu_apply_id)
    	{ 
    		layer_area('签证结果','visa_results_form',740,340);
    	}
    }

    //终止
    function apply_end(receive_member_id,stu_apply_id) 
    { 
    	if(receive_member_id && stu_apply_id)
    	{ 
    		layer_area('终 止','apply_end_form',740,340);
    	}
    }


    //查看留言
    function get_message(log_id)
    { 
    	if(log_id)
    	{ 
    		$("#see_content").val("");
    		$.post(get_log_url,{log_id:log_id},function(data){ 

    			if(data.status=='yes')
    			{ 
    				$("#see_content").html(data.info1);
    				layer_area('查看留言','see_message',400,340);
    			}
    			else
    			{ 
    				alert(data.msg);
    				return false;
    			}

    		},'json');
    	}
    }

//查看留言
function get_files(log_id)
{
    if(log_id)
    {
        $("#see_files").html("");
        $.post(get_log_url,{log_id:log_id},function(data){

            if(data.status=='yes')
            {
                $.each(data.files,function(i,v){
                    $("#see_files").append("<li><a href='"+v.file_url+"' target='_blank'>"+ v.file_name+"</a></li>");
                });

                layer_area('查看附件','see_files_pop',400,340);
            }
            else
            {
                alert(data.msg);
                return false;
            }

        },'json');
    }
}
