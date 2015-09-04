/*
 *   CRM系统会员JS
 *   作者：syl
 */

 $(document).ready(function(){
							
	$("#upload_info").click(function(){ 
		
		var view = $("#view").css('display');
		if(view=='block')
		{
			$("#upload_info").val('返 回');
			$("#view").hide();
			$("#update").show();
		}
		else
		{ 
			$("#upload_info").val('修 改');
			$("#view").show();
			$("#update").hide();
		}
		
	});

 	//更新用户基本信息
 	$("#set_info_act").click(function(){
 		//密码
 		var newpwd = $("#newpwd").val();
		var confirmpwd = $("#confirmpwd").val();
		if( newpwd && confirmpwd )
		{
			if(newpwd != confirmpwd)
			{
				alert('两次密码输入不一致！');
				return false;
			}
		}

		var type = $("#type").val();
		var reg1 = /^[1-9]\d{0,3}$/;
		var reg2 = /^[0-9]*$/;
		
		if(type==1)
		{ 
			//验证固定电话
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
		else if(type==2)
		{
			//验证移动电话
			var country_num_qiye1 = $("#country_num_qiye1").val();
			var moblie_num_qiye1 = $("#moblie_num_qiye1").val();
			if(!reg1.test(country_num_qiye1))
			{ 
				alert('国家编号不得超过三位数！');
				return false;
			}
	
			if(!reg2.test(moblie_num_qiye1))
			{
				alert('请输入正确的移动电话！');
				return false; 
			}
		}
		

		//邮箱
		if(!check_email(email_url))
		{
			return false;
		}

		var act_url = $("#set_info_url").val();
		var data = $("#member_info").serialize();

		ajax_submit(act_url,data);


 	});

 	//更新账户信息
 	$("#set_account").click(function(){

 		var act_url = $("#set_account_url").val();
		var data = $("#setting").serialize();
		ajax_submit(act_url,data);
 	})


     $("#upload_bank").click(function(){

         var view = $("#viewBank").css('display');
         if(view=='block')
         {
             $("#upload_bank").val('返 回');
             $("#viewBank").hide();
             $("#updateBank").show();
         }
         else
         {
             $("#upload_bank").val('修 改');
             $("#viewBank").show();
             $("#updateBank").hide();
         }

     });


     $("#set_bank_act").click(function(){

         var act_url = $("#set_bank_url").val();
         var data = $("#member_bank").serialize();

         ajax_submit(act_url,data);


     });
 });