
$(function () {

	//获取浏览器与内容大小方法
    winPosition = function () {
        var winWidth = $(window).outerWidth();
        var winHeight = $(window).outerHeight();
        var bodyWidth = $(document).outerWidth();
        var bodyHeight = $(document).outerHeight();
        var bodyScroll = $(window).scrollTop();
        return {
            width: winWidth,
            height: winHeight,
            widthB: bodyWidth,
            heightB: bodyHeight,
            scroll: bodyScroll
        }
    };
		
		//导航下拉
	$(".nav_wrap ul li").hover(function(){
		$(this).addClass("hovers").find("dl").show();
	},function(){
		$(this).removeClass("hovers").find("dl").hide();
	})

	//学历与专业切换
	$("#yuanxiao_xueli").change(function(){
		var _ths = $(this).find("option:selected").text();
		if(_ths=="本科"){
			$(".yuanxiao_zhuanye").hide();
			$(".yuanxiao_zhuanye").eq(0).show();
		}
		if(_ths=="研究生"){
			$(".yuanxiao_zhuanye").hide();
			$(".yuanxiao_zhuanye").eq(1).show();
		}
	}) 

	//添加院校-搜索院校名称
	$("#scol_texts").click(function(){
		$(".con_item_layer2").hide();
		$("#scholl_ather_scholl").fadeIn();
		return false;
	})
	//添加院校-搜索院校名称-选择院校
	$(".js-scoll-stuList a").click(function(){
		var sTxtE = $(this).find(".s_en").text();
		var sTxtC = $(this).find(".font14").text();
		$(".con_item_layer2").fadeIn();
		$("#scholl_ather_scholl").hide();
		$("#name-yuanxiaoE").val(sTxtE);
		$("#name-yuanxiaoC").val(sTxtC);
	})

	//院校添加-合同分享
	$("#fenxiang_checkbox").click(function(){
		if($(this).is(':checked')){
			$("#fenxiang_div").show();
		}else{
		
			$("#fenxiang_div").hide();
		}
	})

	//查看中介信息
	$(".yj_con a.shows").click(function(){
		$("#user_infos").fadeIn();
		return false;
	})
	//关闭查看中介信息
	$(".close_user_infos").click(function(){
		$("#user_infos").hide();
		return false;
	})


	//用户信息基本信息修改
	$("#editA").click(function(){
		var attrText = $(this).attr("data-text");

		if(attrText=="1"){
			$("#editA").attr("data-text","2").text("返回");
			$(".user_infos").eq(0).hide();
			$(".user_infos").eq(1).show();
		}else{
			$("#editA").attr("data-text","1").text("修改");
			$(".user_infos").eq(1).hide();
			$(".user_infos").eq(0).show();
		}
	})


	 //取屏幕宽度
    /*function _win() {
        if ($(".main_wrap").length >= 1) {
            if ($(window).outerWidth() < 1000) {
                $(".main_wrap").width(1000);
            }else if($(window).outerWidth() > 1680){
				 $(".main_wrap").width(1680);
			}else{
				$(".main_wrap").width($(window).outerWidth());
			}
        }
    }

	_win();
	
	$(window).resize(function(){
		_win();
	});*/


	 //登录密码提示
    var _thisText = "";
    $(".input_wrap .login_input").focus(function () {
        $(this).next("span").stop(true, true).animate({ "opacity": "0.3" }, 200);
        $(this).keyup(function () {
            _thisText = $.trim($(this).val().length);
            if (_thisText > 0) {
                $(this).next("span").fadeOut(150);
            } else {
                $(this).next("span").fadeIn(150);
            }
        });
    }).blur(function () {
        $(this).next("span").stop(true, true).animate({ "opacity": "1" }, 200);
    });

	//购买空间类型选择
	$("#ac-a a").click(function(){
		var _this = $(this);
		_this.siblings("a").removeClass("cur")
		_this.addClass("cur")
		return false;
	})
	
	//院校列表搜索
	var _timer;
	$(".select_item2 .select_item > a").hover(function(){
		clearTimeout(_timer);
		$(".select_item a").removeClass("hovers");
		$(this).addClass("hovers");
		var _thisTop = $(this).position().top+22;
		$(".select_ac_list").show().css("top",_thisTop);
		
		/*字母*/
		var zimu = $(this).attr('zimu');
		var s_type = $(this).attr('type');
		var country_id = $(this).attr('country');
		var area = $(this).attr('area');

		$.post('/index.php?m=Home&c=School&a=zimuchange',{zimu:zimu,s_type:s_type,country_id:country_id,area:area},function(data){
			
			if(data.status==1)
			{
				$('.select_ac_list_inner').html(data.str);
				$(".countryid").on('click',function(){ 

					var myurl=new objURL(window.location.href);
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
			}
		},'json');
		
	},function(){
		$(".select_ac_list").hide();
		_timer = setTimeout(function(){
			if($(".select_ac_list").is(":hidden")){
				$(".select_item a").removeClass("hovers");
			}
		},50);
		
	});
	$(".select_ac_list").hover(function(){
		$(this).show();
	},function(){
		$(this).hide();
		$(".select_item a").removeClass("hovers");
	});

	$(".select_ac_list_inner b").click(function(){
		$(".select_item a").removeClass("hovers");
		$(".select_ac_list").hide();
	});

	//左侧学生列表下拉
	$(".c_tem_text_inner").click(function(){
		if($(this).next(".c_tem_list").is(":hidden")){
			$(this).find("span").text('');
			$(this).next(".c_tem_list").show();
		}else{
			$(this).next(".c_tem_list").hide();
		}
	});
	$(".c_tem_list a").click(function(){
		var _thisText = $(this).find("span").text();
		var data_id = $(this).find("span").attr('data-id');
		var stu_list_url = $("#stu_list_url").val();
		$(this).parents(".c_tem_list").prev(".c_tem_text_inner").find("span").text(_thisText);
		$(this).parents(".c_tem_list").hide();
		$('#stu_list li').each(function(){
    	    $(this).remove();
		});
		//获取数据
		$.post(stu_list_url,{data:data_id},function(data){ 
			if(data.status=='yes')
			{ 
				var html='';
				if(data.list)
			    { 
			    	for (var i = 0; i < data.list.length; i++) {
			    		html+="<li> <i class='icon'></i> <em>"+data.list[i].name+"</em> <a href='"+data.list[i].url+"'>查	看</a> </li>";
			    	};
			    }
			    else
			    { 

			    	html+="<li><em>暂无学生数据！</em></li>";
			    }
			    $("#stu_list").append(html);
			    return true;

			}

		},'json');

		return false;
	});
	$(document).click(function(event){
		var doc = $(event.target);
		if(doc.parents(".c_tem_text").length<=0){
			$(".c_tem_list").hide();
		}
	});
	

	//日程提醒切换
	$(".rc_con_tab li").click(function(){
		$(".rc_con_inner .rc_list_con_stud").hide().eq($(".rc_con_tab li a").removeClass("active").index($(this).find("a").addClass("active"))).show();
	});

    //账户记录切换
    $(".tabUg li").click(function () {
        $(".accountDiv").hide().eq($(".tabUg li").removeClass("cur").index($(this).addClass("cur"))).show();
    }); 

	//工作流程下拉
	$(".dq_zt").hover(function(){
		$(this).find(".dq_zt_list").show();		
	},function(){
		$(this).find(".dq_zt_list").hide();
	});
	$(".dq_zt_list a").click(function(){
		var _thisText = $(this).text();
		$(this).parents(".dq_zt_list").hide().prev(".dq_zt_text").find("b").text(_thisText);
	});

	$(".step_con li").click(function(){
		$(this).addClass("active").parents(".step_ls").addClass("step_ls_select");
	});



	//左侧学校选择
	$(".school_type").click(function(){
		if($(this).next(".school_list_left").is(":hidden")){
			$(".school_type").find(".icon").removeClass("active");
			$(".school_list_left").stop(true,true).slideUp();
			$(this).find(".icon").addClass("active");
			$(this).next(".school_list_left").stop(true,true).slideDown();
		}else{
			$(".school_list_left").stop(true,true).slideUp();
			$(".school_type").find(".icon").removeClass("active");
		}
	});
	

	
	
	//插入背景透明层
	function addBg(){
		var _bg = $("<div class='addBg'></div>");
		 _bg.css({
            "width": winPosition().width,
            "height": winPosition().heightB,
            "z-index": "9999",
            "position": "absolute",
            "left": "0px",
            "top": "0px",
            "background": "#000",
            "opacity": "0.6"
        }).appendTo("body");
		$(".con_item_layer").css({
			"top":winPosition().scroll
		});
	
	
		//关闭透明层
		_bg.click(function(){
			if($(".addBg").length>0){
				$("#scholl_ather_scholl,#ather_scholl,#select_scholl,#xueshengNext,.con_item_layer1,.con_item_layer2,.rc_layer,.rc_layer_jilu,.file_layer,.rc_layer_wd").fadeOut();
				$(this).remove();				
			}
		});
	}

	//学生页面-提交学生-选择院校
	$("#stuInfo_To").click(function(){
		if($(".addBg").length<=0){
			addBg();
			$("#select_scholl").fadeIn();
		}
		return false;
	});

	//学生页面-查看院校
	$(".js-class-stuList a").click(function(){
		
		$("#select_scholl").hide();
		$("#ather_scholl").hide();
		$(".con_item_layer1").fadeIn();
	})

	//学生页面-选择院校-其他院校
	$("#next-ather-scholl").click(function(){
		$("#select_scholl").hide();
		$("#ather_scholl").fadeIn();
		return false;
	});

	/*学院查看点击
	$(".school_left_ac li a,.school_list_con li a").click(function(){
		if($(".addBg").length<=0){
			addBg();
			$(".con_item_layer1").fadeIn();
		}
	});*/

	//学校信息到选择学生
	$("#next-xuesheng").click(function(){

		$(".con_item_layer1").hide();
		$("#xueshengNext").fadeIn();
		return false;
	});

	//选择学生重新选择院校
	$("#yuanxiaoPre").click(function(){
		$("#schollInfo").fadeIn();
		$("#xueshengNext").hide();
		return false;
	});
	/*学院添加点击*/
	$(".school_add").click(function(){
		if($(".addBg").length<=0){
			addBg();
			$(".con_item_layer2").fadeIn();
		}
	});
		
	//日程计划左侧
	$(".rc_list_t").click(function(){
		if($(this).next(".rc_second").is(":hidden")){
			$(".rc_list_t").find(".icon").removeClass("active");
			$(".rc_second").stop(true,true).slideUp();
			$(".school_list_left").stop(true,true).slideUp();
			$(this).find(".icon").addClass("active");
			$(this).next(".rc_second").stop(true,true).slideDown();
		}else{
			$(".rc_second").stop(true,true).slideUp();
			$(".school_list_left").stop(true,true).slideUp();
			$(".rc_list_t").find(".icon").removeClass("active");
		}
	});

	$(".rc_second_list").click(function(){
		if($(this).next(".school_list_left").is(":hidden")){
			$(".school_list_left").stop(true,true).slideUp();
			$(this).next(".school_list_left").stop(true,true).slideDown();
		}else{
			$(".school_list_left").stop(true,true).slideUp();
		}
		return false;
	});
	

	//收支插片
	$(".sz_tab li").click(function(){
		$(".sz_con .sz_inner").hide().eq($(".sz_tab li a").removeClass("active").index($(this).find("a").addClass("active"))).show();
	});
	
	
	$(".email_bt").click(function(){
		if($(this).parents(".message_rc").next(".baseInfor").is(":hidden")){
			$(this).parents(".message_rc").next(".baseInfor").show();
		}else{
			$(this).parents(".message_rc").next(".baseInfor").hide();
		}
	})

});


