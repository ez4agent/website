<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>用户登录-EZ4Agent</title>
<link rel="stylesheet" type="text/css" href="/Public/front/css/share.css">
<link rel="stylesheet" type="text/css" href="/Public/front/css/main.css">
<script src="/Public/js/jquery.js" type="text/javascript"></script>
<script src="/Public/front/js/common.js" type="text/javascript"></script>
<!--[if lt IE 9]>
    <script src="/Public/js/html5.js"></script>
<![endif]-->
</head>
<body>

<section class="main_wrap">
	<!--头部（用于用户登陆、注册）-->
<section class="divMade head_top">
  <div style="float:left; margin:12px 25px"><a href="/" style="font-size: 24px"><strong>EZ4Agent</strong></a></div>
  <div class="divMadeInner clearfix">
      <div class="fl login_nav">
          <a href="<?php echo U('Home/Index/aboutUS');?>">关于我们</a>
          <a href="<?php echo U('Home/Index/contacts');?>">联系我们</a>
      </div>
  </div>
</section>	
  <section class="banner_wrap">
    <div class="login_con">
      <div class="login_inner">
          <div class="login_made">
          <form id="login_form">
            <div class="login_made_inner">
              <div class="input_wrap">
                <input id="username" name="username" value="" type="text" class="login_input" autocomplete="off"/>
                <span>用户名</span>
              </div>
              <div class="input_wrap">
                <input id="pwd" name="pwd" value="" type="password" class="login_input"/>
                <span>密码</span>
              </div>
              <p><label><input name="checkbox1" type="checkbox" id="check" value="1"> <em>记住我的登录状态</em></label></p>
              <p class="login_btwrap">
                <a href="javascript:void(0);" class="login_bt" id="submit_login">登录</a>
                <a href="<?php echo U('Home/Register/index');?>" class="regsiter_bt">注册</a>
              </p>
              <p class="wj_pwd"><a href="#">忘记密码? </a></p>
            </div>
          </form>
          </div>
       </div>
      </div>
      <div class="banner_inner"><img src="/Public/front/images/bannerImg.jpg" width="1680" height="704" alt="" /></div>
    </section>      
	<!--通用底部-->
<section class="bottom">
	<div class="inner">
		<em class="icon1"><i class="icon"></i><b>company<br/>The lader</b></em>
		<em class="icon2"><i class="icon"></i><b>recruit<br/>call  me</b></em>
		<em><b>(c) 上海瓯丽信息科技有限公司. All rights reserved.</b></em>
		<p class="copy">Copyright @ 上海瓯丽信息科技有限公司 沪ICP14049149</p>
	</div>
</section>		
</section>
<script type="text/javascript">

$(document).ready(function(){


 $("#login_form").keydown(function(e){
   var e = e || event,
   keycode = e.which || e.keyCode;
   if (keycode==13) {
      $("#submit_login").trigger("click");
   }
});
				   
	//验证登陆
	$('#submit_login').click(function(){
				
		var username = $('#username').val();
		var pwd = $('#pwd').val();
		if(username=="" || pwd==""){
			alert('请输入用户名或者密码！');
			return false;
		}
		
		//判断checkbox是否被选中
		var checked =$("input[type=checkbox]:checked").val(); 
		//ajax
		$.ajax({
             type: 'post',
             url: "<?php echo U('Home/Login/checkLogin');?>",
             data: {username:username,pwd:pwd,checked:checked},
             dataType: "json",
             success: function(data){
				if(data.status=='yes')
				{
					window.location.href=data.url;
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

</script>
</body>
</html>