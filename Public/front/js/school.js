/* 学院搜索信息 */


/* 删除搜索*/
function del_search(field)
{
	var myurl=new objURL(window.location.href);
	
	if(field=='country_id')
	{
		myurl.remove('area_id');
		myurl.remove('city_id');
	}	
	else if(field=='area_id')
	{
		myurl.remove('city_id');	
	}
	
	myurl.remove(field);
	location.href=myurl.url();
}




$(function () { 

	//搜索
	$(".countryid").on('click',function(){ 

		var myurl=new objURL(window.location.href);
		alert(myurl);
		return false;
		var value = $(this).attr('country_id');
		myurl.set('country_id',value);
		//alert(myurl.url());
		window.location.href=myurl.url();
	});

	$(".aid").on('click',function(){ 

		var myurl=new objURL(window.location.href);
		var value = $(this).attr('area_id');
		myurl.set('area_id',value);
		//alert(myurl.url());
		window.location.href=myurl.url();
	});

	$(".cityid").on('click',function(){ 

		var myurl=new objURL(window.location.href);
		var value = $(this).attr('city_id');
		myurl.set('city_id',value);
		//alert(myurl.url());
		window.location.href=myurl.url();
	});		

	$(".education").click(function(){
		var myurl=new objURL(window.location.href);
		var value = $(this).attr('education');
		myurl.set('education',value);
		//alert(myurl.url());
		window.location.href=myurl.url();
	});

	$("#icon_search").click(function(){
		
		var myurl=new objURL(window.location.href);
		var keywords = $("#search_college_name").val();
		if(!keywords)
		{
			alert('请输入搜索关键字！');
			return false;
		}

		myurl.set('keywords',keywords);
		window.location.href=myurl.url();
	})

	var select =$('input[name="edu"]:checked').val();
	var college_id = $("#college_id").val();
	var stu_id = $("#stu").val();
	get_sharebyselectapply(select,college_id,stu_id);
	
	//获取学历redio 选中的值
	$(".apply_id").click(function(){ 

		var select =$('input[name="edu"]:checked').val();
		var college_id = $("#college_id").val();
		var stu_id = $("#stu").val();
		get_sharebyselectapply(select,college_id,stu_id);

	})

	//申请页面学生
	var stu_id = $("#stu_info1").val();
	var url = $("#apply_get_stu_file").val();
	get_file(stu_id,url);
	
	$("#stu_info1").change(function(){ 

		var stu_id = $("#stu_info1").val();
		
		if(stu_id)
		{ 
			get_file(stu_id,url);
		}

	});

	//提交院校申请
	$("#apply_act").bind('click', function(event){ 

		var stu_id = $("#stu_info1").val();
		var profession = $("#profession").val();
		var start_time = $("#start_time").val();

		if(!stu_id)
		{ 
			alert('请选择学生！');
			return false;
		}

		if(!profession)
		{ 
			alert('请填写专业！');
			return false;
		}

		if(!start_time)
		{ 
			alert('请填写入学日期');
			return false;
		}
		else if(!checkDate(start_time))
		{
			alert('入学日期不得早于或等于今日！');
			return false;
		}

		//选择文档
		 if($('.checkitem:checked').length == 0){    //没有选择
            alert('请勾选或上传所需文档!');
            return false;    
        }else{ 

        	/* 获取选中的项 */
        	var items = '';
        	$('.checkitem:checked').each(function(){
            	items += this.value + ',';
        	});
        	items = items.substr(0, (items.length - 1));
        }


		var apply_url = $("#submit_url1").val();
		var college_id = $("#college_id").val();
		var commission_id = $("#commission_id").val();
		var content = $("#content").val();
		
		$("#apply_act").html('申请提交中...');
		$(this).unbind('click');
		$.post(apply_url,{stu_id:stu_id,college_id:college_id,commission_id:commission_id,profession:profession,
			start_time:start_time,content:content,items:items},function(data){ 
			if(data.status=='yes')
			{ 
				alert(data.msg);
				window.location.href = data.url;
				return true;
			}
			else
			{
				$("#apply_act").html('提交申请');
				alert(data.msg);
				return false;
			}


		},'json');

	});


	//院校帮助

	$("#help_college").click(function(){ 

		var college_id = $("#help_college_id").val();
		var remark = $("#remark").val();

		if(!remark || remark=='')
		{ 
			alert('为了我们能够更好地帮助你,请填写内容!');
			return false;
		}

		$.ajax({ 
			 type:"post",
             url: $("#help_url").val(),
             data: $("#post_form").serialize(),
             dataType: "json",
             success: function(data){
                  
                 if(data.status=='ok')
                 {
                 	alert('提交成功，我们会在2-3个工作日给您回复!');
                 	window.location.reload();
                 	return true;
                 }
                 else
                 { 
                 	alert(data.msg);
                 	return false;
                 }      
             }

		});

	});

})

//获取学生附件

function get_file(stu_id,url)
{ 
	$("#upload_info").attr('stu_id',stu_id);
	$.post(url,{stu_id:stu_id},function(data){ 
				
				if(data.status=='yes')
				{
					$("#stu_file").empty();
					var html1=''; 
					if(data.info)
					{ 
						for (var i = 0; i < data.info.length; i++) {
							
							var path = 'Uploads'+data.info[i].file_path;

							html1+="<li style='width:150px;'><input name='checkbox[]' class='checkitem' type='checkbox' value="+data.info[i].id+">&nbsp;";
							html1+="<a href='"+path+"' target='_blank'><span style='text-align:left' class='title' title="+data.info[i].file_name+">"+data.info[i].file_name+"</span></a></li>";
						};
					}
					else
					{ 
						html1="<li><span style='font-size:14px; color:#999'>暂无附件,请上传!</span></li>";
					}

					$("#stu_file").html(html1);
					return true;
				}
				else
				{ 
					alert(data.msg);
					return false;
				}

			},'json');

}


//选择申请信息,获得相应的中介分享
function get_sharebyselectapply(select,college_id,stu_id)
{ 
	if(select && college_id)
    {
    	var url=$("#select_share_url").val();
    	$.post(url,{select:select,college_id:college_id,stu_id:stu_id},function(data){ 

    		if(data.status=='yes')
    		{ 
    		    document.getElementById("share_div").innerHTML="";
    		    $("#share_div").append(data.str);

                $("#share_div").find('td a.desc_show').on('click',function(){
                    var html = $(this).parent().find('.share_desc').html();
                    layer.alert(html,-1,'备注');
                });

    		    $("#total").val(data.total);
    		}

    	},'json');
    }

}


//添加合作院校
function add_partner(college_id,url)
{
	if(isNaN(college_id))
	{
		alert('参数错误！');
		return false;
	}
	else
	{
		if(confirm("是否要把该院校添加为合作院校?"))
		{
			$.post(url,{college_id:college_id},function(data){
			
				if(data.status == 1)
				{
					window.location.href=data.url;
				}
				else
				{
					alert(data.msg);
					return false;
				}
			},'json');
		}
		else
		{
			return false;
		}
	}
}

//取消合作院校的申请
function cancel_partner(college_id,url)
{
	if(isNaN(college_id))
	{ 
		alert('参数错误！');
		return false;
	}
	else
	{ 
		if(confirm("是否要取消该合作院校?"))
		{ 
			$.post(url,{college_id:college_id},function(data){

				if(data.status == 1)
				{ 
					window.location.href=data.url; 
				}

			},'json');
		}
	}
}


//获取申请跳转链接
function college_apply_header(college_id ,commission_id)
{ 
	if(!college_id)
	{ 
		alert('参数错误！');
		return false;
	}
	var apply_id = $('input[name="edu"]:checked').val();
	if(!apply_id)
	{ 
		alert('请选择申请学历！');
		return false;
	}

	var stu = $("#stu").val();
	var reg_from_url = $('#reg_from_url').val();

	$.post(reg_from_url,{college_id:college_id,apply_id:apply_id,commission_id:commission_id,stu:stu},function(data){ 

		if(data.status=='yes')
		{ 
			window.location.href = data.url;
			return true;
		}
		else
	    { 
	    	alert(data.msg);
	    	return false;
	    }

	},'json');
}


//院校帮助
function college_help(college_id)
{ 
	if (college_id) 
	{
		$("#help_college_id").val(college_id);
		document.getElementById("post_form").reset();

		layer_area('寻求帮助','college_help','600px','350px');
	}
	else
	{ 
		alert('该院校不存在！');
		return false;
	}


} 





