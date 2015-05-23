/*	
 *	CRM系统--js公用函数
 *  作者：syl
 */

/* 
JS 操作 URL 函数使用说明：
初始化 var myurl=new objURL(); //也可以自定义URL： var myurl=new objURL('http://iulog.com/?sort=js'); 
读取url参数值 var val=myurl.get('abc'); // 读取参数abc的值
设置url参数 myurl.set("arg",data); // 新增/修改 一个arg参数的值为data
移除url参数 myurl.remove("arg"); //移除arg参数
获取处理后的URL myurl.url();//一般就直接执行转跳 location.href=myurl.url();
调试接口：myurl.debug(); //修改该函数进行调试
 */
function objURL(url){
	var ourl=url||window.location.href;
	var href="";//?前面部分
	var params={};//url参数对象
	var jing="";//#及后面部分
	var init=function(){
		var str=ourl;
		var index=str.indexOf("#");
		if(index>0){
			jing=str.substr(index);
			str=str.substring(0,index);
		}
		index=str.indexOf("?");
		if(index>0){
			href=str.substring(0,index);
			str=str.substr(index+1);
			var parts=str.split("&");
			for(var i=0;i<parts.length;i++){
				var kv=parts[i].split("=");
				params[kv[0]]=kv[1];
			}
		}else{
			href=ourl;
			params={};
		}
	};
	this.set=function(key,val){
		params[key]=encodeURIComponent(val);
	};
	this.remove=function(key){
		if(key in params) params[key]=undefined;
	};
	this.get=function(key){
		return params[key];
	};
	this.url=function(key){
		var strurl=href;
        var objps=[];
        for(var k in params){
            if(params[k]){
                objps.push(k+"="+params[k]);
            }
        }
        if(objps.length>0){
            strurl+="?"+objps.join("&");
        }
        if(jing.length>0){
            strurl+=jing;
        }
        return strurl;
	};
	this.debug=function(){
		// 以下调试代码自由设置
		var objps=[];
		for(var k in params){
			objps.push(k+"="+params[k]);
		}
		alert(objps);//输出params的所有值
	};
	init();
}


//用户注册
function user_reg()
{ 
	//获取注册用户类型
	var user_type = $("input[name='member_type']:checked").val();
	//验证用户名
	if(!check_username(check_username_url))
	{
		return false;
	}
	//验证用户密码
	var pwd = $('#pwd').val();
	if(!pwd || pwd=='')
	{
		alert('请输入密码!');
		return false;
	}

	//所在地
	var country_id = $("#country_id").val();
	var area_id = $("#area_id").val();
	var city_id = $("#city_id").val();

	if(!country_id || !area_id || !city_id)
	{ 
		alert('请选择所在地！');
		return false;
	}

	if(user_type==1)
	{ 
		//如果是企业，填写企业名称
		var company = $("#company").val();
		if(!company || company=='')
		{ 
			alert('请填写企业名称！');
			return false;
		}
	}

	//联系人
	var contact = $("#contact").val();
	if(!contact|| contact=='')
	{ 
		alert('请填写联系人姓名！');
		return false;
	}

	//验证固定电话和移动电话
	if(!check_phone(user_type))
	{ 
		return false;
	}
	//邮箱
	if(!check_email(check_email_url))
	{
			return false;
	}

	var data = $("#Regfrom").serialize();
	//ajax提交
	ajax_submit(submit_url,data);

}





//验证用户名
function check_username(url)
{
	var username = $("#username").val();
	if(!username || username=='')
	{
		alert('请输入用户名！');
		return false;
	}
	else
	{
		//验证用户重名
		flag = check_unique(url,"username",username,false);
		if(flag==1)
		{
			alert('该用户已被注册，请重新输入!');
			return false;
		}
		else
		{ 
			return true;
		}
	}

	return true;
}


//验证固定电话和移动电话
function check_phone(type)
{
	
	var reg1 = /^[1-9]\d{0,3}$/;
	var reg2 = /^[0-9]*$/; 

	if(type==1)//企业
	{ 
		var country_num_qiye = $("#country_num_qiye").val();
		var qu_num_qiye =$("#qu_num_qiye").val();
		var phone_qiye =$("#phone_qiye").val();

		if(!reg1.test(country_num_qiye))
		{ 
			alert('国家编号不得超过三位数！');
			return false;
		}

		if(!reg1.test(qu_num_qiye))
		{
			alert('区号不得超过三位数！');
			return false; 
		}

		if(!reg2.test(phone_qiye))
		{
			alert('请输入正确的固定电话号码！');
			return false; 
		}

	}
	else if(type==2) //个人
	{ 
		var country_num_geren1= $("#country_num_geren1").val();
		var moblie_num_geren1= $("#moblie_num_geren1").val();

		if(!reg1.test(country_num_geren1))
		{ 
			alert('国家编号不得超过三位数！');
			return false;
		}

		if(!reg2.test(moblie_num_geren1))
		{
			alert('请输入正确的移动电话！');
			return false; 
		}
	}

	return true;
} 
 

//验证邮箱
function check_email(url)
{
	var email = $("#email").val();
	if(!email || email=='')
	{
		alert('请输入邮箱!');
		return false;
	}
	else
	{
		var mailreg = /^(\w-*\.*)+@(\w-?)+(\.\w{2,})+$/;
    	if(!mailreg.test(email))
    	{
        	alert("请输入正确的邮箱！");
			return false;
    	}
    	else
    	{
    		//验证邮箱重复
			flag = check_unique(url,"email",email,false);
			if(flag==1)
			{
				alert('该邮箱已存在，请重新输入!');
				return false;
			}
    	}
	}
	return true;
}

//验证日期
function RQcheck(RQ) 
{
	var a = /^\d{4}\/\d{2}\/\d{2}$/;
	if (!a.test(RQ)) { 
		return false; 
	} else {
		return true;	
	} 
	 
}

//判断填写的日期不能小于当前日期
function checkDate(date)
{ 
	var arr = date.split('/');
	var myDate = new Date();
	var today = new Date();
	myDate.setFullYear(arr[0],arr[1]-1,arr[2]);
	if(myDate > today)
	{
		return true;
	}
	else 
	{
		return false;
	}
}


/**
*	验证字段重名
*/
function check_unique( url,field,value,async)
{
	var flag=0;
	
	$.ajax({
    	type : "POST",
		url  : url,
	   data  : field+"="+value,
	   async : async, 
     success : function(data){
		 
			if(data.status=='no')
			{
				flag=1;
			}
		} 
	});
	return flag;
}


//AJAX提交
function ajax_submit(url,data )
{
	$.ajax({ 
	  type : "POST", 
	   url : url, 
	  data : data, 
	  success : function(data)
	  { 
		  if(data.status=='yes')
		  {
			  alert(data.msg);
			  if(data.url && data.msg)
			  {
			  	window.location.href=data.url;
			  }
			  else
			  { 
			  	window.location.href=window.location.href;
			  }
		  }
		  else
		  {
			  alert(data.msg);
			  return false;
		  }
	  }
	});
}

//layer弹出层
function layer_area(title,id,width,height)
{
	return $.layer({
        type: 1,
        title: title,
        border : [5, 0.5, '#666'],
        area: [width,height],
        shadeClose: true,
        page: {dom: '#'+id}
    });
}


//添加事件
function add_event(stu_id,event_id,url)
{ 
	document.getElementById("event_form").reset();

	$.post(url,{stu_id:stu_id,event_id:event_id},function(data){ 
		if(data.status=='yes')
		{ 
			$("#stu_id1").append(data.info);
			if(data.event!='')
		    {
				if(data.event.is_use==0)
				{
		    		$("#title").val(data.event.title);
    	    		$("#date").val(data.event.date_value);
    	    		$("#content").val(data.event.content);
    	    		$("#events_id").val(data.event.event_id);
					$("[name='is_use']").removeAttr("checked");
					$("#sub_events_act").html("修&nbsp;&nbsp;改");
					layer_area('查看提醒','add_event','680px','350px');
				}
				else
				{ 
					$("#event_title").html(data.event.title);
					$("#event_stu").html(data.info);
					$("#event_date_value").html(data.event.date_value);
					$("#event_content").html(data.event.content);
					layer_area('查看提醒','view_event','680px','350px');
				}
		    }
		    else
		    { 
		    	$("#title").val('');
    	    	$("#content").val('');
		    	layer_area('添加提醒','add_event','680px','350px');
		    }
				
		} 
	},'json');
}


//查看用户信息
function view_member(member_id)
{
	if(!member_id || member_id ==0)
	{ 
		alert('该用户不存在！');
		return false;
	}
	else
	{ 

		$.post(member_view_url,{member_id:member_id},function(data){ 

			if(data.status=='1')
			{ 
				$("#member_info1").empty();
				$("#member_info1").append(data.member_info);
				layer_area('查看中介信息','member_info','680px','350px');
			}

		},'json');
	}

}





