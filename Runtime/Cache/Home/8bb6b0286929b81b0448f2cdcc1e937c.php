<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>用户注册-EZ4Agent</title>
<link rel="stylesheet" type="text/css" href="/Public/front/css/share.css">
<link rel="stylesheet" type="text/css" href="/Public/front/css/main.css">
<link rel="stylesheet" type="text/css" href="/Public/front/css/common.css">
<script src="/Public/js/jquery.js" type="text/javascript"></script>
<script src="/Public/front/js/area.js" type="text/javascript"></script>
<script src="/Public/front/js/gobal.js" type="text/javascript"></script>
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
    <div class="banner_inner"><img src="/Public/front/images/bannerImg.jpg" width="1680" height="704" alt="" /></div>
  </section>
  <section class="regsiter_con">
    <div class="register_inner">
        <div class="login_made">
            <div class="login_made_inner">
                <h2>用户信息</h2>
                <form id="Regfrom">
                <ul>
                <li class="clearfix">
                   <em><span style="color:#ff0000; margin-right:3px;">*</span>类型：</em>
                   <label><input name="member_type" type="radio" value="1" checked />&nbsp;公司</label>　
                   <label><input type="radio" name="member_type" value="2" />&nbsp;个人</label>
                </li>
                <li class="clearfix">
                   <em><span style="color:#ff0000; margin-right:3px;">*</span>用户名：</em>
                   <input id='username' name="username" type="text" class="regsiter_input"/> 
                </li>
                <li class="clearfix">
                   <em><span style="color:#ff0000; margin-right:3px;">*</span>密码：</em>
                   <input id="pwd" name="pwd" type="password" class="regsiter_input" /> 
                </li>
                <li class="clearfix">
                    <em><span style="color:#ff0000; margin-right:3px;">*</span>所在地区：</em>
                    <select id="country_id" class="reg_siter" name="country_id">
                        <option value="">==请选择==</option>
                        <?php if(is_array($country)): $i = 0; $__LIST__ = $country;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo['countryid']); ?>"><?php echo ($vo['name']); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                        </select>&nbsp;
                        <select id="area_id" class="reg_siter" name="area_id">
                        <option value="">==请选择==</option>
                        </select>&nbsp;
                        <select id="city_id" class="reg_siter" name="city_id">
                        <option value="">==请选择==</option>
                        </select>
                </li>
                <li class="clearfix">
                  <em><span style="color:#ff0000; margin-right:3px;">*</span>以下信息：</em>
          <label><input type="radio" name="is_show"  value="0" />&nbsp;不公开</label> &nbsp;&nbsp;                  
                    <label><input type="radio" name="is_show"  value="2" checked />&nbsp;仅对合作方显示</label> &nbsp;&nbsp;
                    <label><input type="radio" name="is_show"  value="1" />&nbsp;对所有人显示</label>　
                      
                </li>
                <li class="clearfix" id="company_show" style="display:none">
                   <em><span style="color:#ff0000; margin-right:3px;">*</span>公司名称：</em>
                   <input id="company" name="company" type="text" class="regsiter_input"/>
                </li>
                <li class="clearfix">
                   <em><span style="color:#ff0000; margin-right:3px;">*</span>联系人：</em>
                   <input id="contact" type="text" name="contact" class="regsiter_input"/>
                </li>
                <div id="campany_form"> 
                <li class="clearfix">
                   <em><span style="color:#ff0000; margin-right:3px;">*</span>固定电话：</em>
                   + <input id="country_num_qiye" type="text" class="baseInfor_input" style="width:50px" name="country_num_qiye" value="国家编号"
                     onFocus="if(this.value=='国家编号')this.value='';" onblur="if(this.value=='')this.value='国家编号';"
                     />-
                   <input id="qu_num_qiye" type="text" class="baseInfor_input" style="width:50px" name="qu_num_qiye" value="区号"
                     onFocus="if(this.value=='区号')this.value='';" onblur="if(this.value=='')this.value='区号';"
                   />-
                   <input id="phone_qiye" type="text" class="regsiter_input" name="phone_qiye"/>
                </li>
                <li class="clearfix">
                   <em>移动电话：</em>
                   + <input id="country_num_qiye1" type="text" class="baseInfor_input" style="width:50px" name="country_num_qiye1" value="国家编号"
                     onFocus="if(this.value=='国家编号')this.value='';" onblur="if(this.value=='')this.value='国家编号';"
                   />-
                   <input id="moblie_num_qiye1" type="text" class="regsiter_input" name="moblie_num_qiye1"/>
                </li>
                </div>
                <div id="geren_form" style="display:none"> 
                <li class="clearfix">
                   <em>固定电话：</em>
                   + <input id="country_num_geren" type="text" class="baseInfor_input" style="width:50px" name="country_num_geren" value="国家编号"/>-
                   <input id="qu_num_geren" type="text" class="baseInfor_input" style="width:50px" name="qu_num_geren" value="区号"/>-
                   <input id="phone_geren" type="text" class="regsiter_input" name="phone_geren"/>
                </li>
                <li class="clearfix">
                   <em><span style="color:#ff0000; margin-right:3px;">*</span>移动电话：</em>
                   + <input id="country_num_geren1" type="text" class="baseInfor_input" style="width:50px" name="country_num_geren1" value="国家编号"/>-
                   <input id="moblie_num_geren1" type="text" class="regsiter_input" name="moblie_num_geren1"/>
                </li>
                </div>
                <li class="clearfix">
                    <em><span style="color:#ff0000; margin-right:3px;">*</span>邮箱：</em>
                    <input id="email" name="email" type="text" class="regsiter_input"/>
                </li>
               
                <li class="clearfix">
                    <em>详细地址：</em>
                    <input type="text" class="regsiter_input address" name="address"/> 
                </li>
                <li class="clearfix">
                        <span class="fl"><em class="info_text">介&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;绍：</em><textarea name="introduction" class="regsiter_input address info"/></textarea> </span>
                </ul>
                <p class="regsiter_bt_wrap">
                <a href="javascript:void(0);" class="login_bt" onClick="user_reg();">注册</a>
                <a class="regsiter_bt" href="<?php echo U('Home/Login/index');?>">返回</a>
                </p>
                </form>
            </div>
        </div>
    </div>
  </section>
	<!--通用底部-->
<section class="bottom">
	<div class="inner">
		<em class="icon1"><i class="icon"></i><b>company<br/>The lader</b></em>
		<em class="icon2"><i class="icon"></i><b>recruit<br/>call  me</b></em>
		<em><b>(c) 上海瓯丽信息科技有限公司. All rights reserved.</b></em>
		<p class="copy">Copyright @ 上海瓯丽信息科技有限公司 京ICP10035687</p>
	</div>
</section>
</section>
<script type="text/javascript">
var area_url = "<?php echo U('Home/area/get_area');?>";
var city_url = "<?php echo U('Home/area/get_city');?>";
var check_username_url = "<?php echo U('Home/Register/check_unique_Name');?>";
var check_email_url = "<?php echo U('Home/Register/check_unique_Email');?>";
var submit_url = "<?php echo U('Home/Register/regact');?>";

//获取当前类型的选中值
var type = $("input[name='member_type']:checked").val();
if(type==2)
{  $("#company_show").hide();	
	$("#campany_form").hide();
	$("#geren_form").show();
}
else
{ 
	$("#company_show").show();
	$("#campany_form").show();
	$("#geren_form").hide();
}

//点击radio
$('input[name="member_type"]').click(function(){ 
	
	var type1=$("input[name='member_type']:checked").val();
	if(type1==2)
	{
		$("#company_show").hide();	
		$("#campany_form").hide();
		$("#geren_form").show();
	}
	else
	{ 
		$("#company_show").show();
		$("#campany_form").show();
		$("#geren_form").hide()
	}
})


</script>
</body>
</html>