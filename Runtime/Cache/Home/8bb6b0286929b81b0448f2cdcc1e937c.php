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
                   <input id='input_username' name="username" type="text" class="regsiter_input"/>
                   <span id="username_inpt" class="popover"></span>
                </li>
                <li class="clearfix">
                   <em><span style="color:#ff0000; margin-right:3px;">*</span>密码：</em>
                   <input id="input_pwd" name="pwd" type="password" class="regsiter_input" />
                    <span id="pwd_inpt" class="popover"></span>
                </li>
                <li class="clearfix">
                   <em><span style="color:#ff0000; margin-right:3px;">*</span>确认密码：</em>
                   <input id="input_pwd_confirm" name="pwd_confirm" type="password" class="regsiter_input" /> 
                   <span id="pwd_confirm_inpt" class="popover"></span>
                </li>    
                <li class="clearfix">
                    <em><span style="color:#ff0000; margin-right:3px;">*</span>所在地区：</em>
                    <select id="country_id" class="reg_siter" name="country_id">
                        <option value="0">==请选择==</option>
                        <?php if(is_array($country)): $i = 0; $__LIST__ = $country;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo['countryid']); ?>"><?php echo ($vo['name']); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                        </select>&nbsp;
                        <select id="area_id" class="reg_siter" name="area_id">
                        <option value="0">==请选择==</option>
                        </select>&nbsp;
                        <select id="city_id" class="reg_siter" name="city_id">
                        <option value="0">==请选择==</option>
                    </select>
                    <span id="from_inpt" class="popover"></span>
                </li>
                <li class="clearfix">
                  <em><span style="color:#ff0000; margin-right:3px;">*</span>以下信息：</em>
                    <label><input type="radio" name="is_show"  value="0" />&nbsp;不公开</label> &nbsp;&nbsp;                  
                    <label><input type="radio" name="is_show"  value="2" checked />&nbsp;仅对合作方显示</label> &nbsp;&nbsp;
                    <label><input type="radio" name="is_show"  value="1" />&nbsp;对所有人显示</label>　
                </li>
                <li class="clearfix" id="company_show">
                   <em><span style="color:#ff0000; margin-right:3px;">*</span>公司名称：</em>
                   <input id="input_company" name="company" type="text" maxlength="20" class="regsiter_input"/>
                   <span id="company_inpt" class="popover"></span>
                </li>
                <li class="clearfix">
                   <em><span style="color:#ff0000; margin-right:3px;">*</span>联系人：</em>
                   <input id="input_contact" type="text" maxlength="12" name="contact" class="regsiter_input"/>
                   <span id="contact_inpt" class="popover"></span>
                </li>
                <li class="clearfix">
                   <em>固定电话：</em>
                   + <input id="country_num" type="text" maxlength="3" class="baseInfor_input" style="width:50px" placeholder="国家编号" name="country_num" value="" />-
                   <input id="qu_num" type="text" maxlength="3" class="baseInfor_input" style="width:50px" placeholder="区号" name="qu_num" value="" />-
                   <input id="phone" type="telephone" maxlength="8" class="regsiter_input" name="phone"/>
                </li>
                <li class="clearfix">
                   <em>移动电话：</em>
                   + <input type="text" maxlength="3" class="baseInfor_input" style="width:50px"  placeholder="国家编号" name="mobile_country_num" value="" />-
                   <input id="moblie_num" type="text" maxlength="11" class="regsiter_input" name="mobile_num"/>
                </li>
                <li class="clearfix">
                    <em><span style="color:#ff0000; margin-right:3px;">*</span>邮箱：</em>
                    <input id="input_email" name="email" type="email" class="regsiter_input"/>
                    <span id="email_inpt" class="popover"></span>
                </li>
                <li class="clearfix">
                    <em>详细地址：</em>
                    <input type="text" maxlength="50" class="regsiter_input address" name="address"/> 
                </li>
                <li class="clearfix">
                    <span class="fl"><em class="info_text">介&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;绍：</em><textarea name="introduction" class="regsiter_input address info"/></textarea> </span>
                </ul>
                <p class="regsiter_bt_wrap">
                  <a href="javascript:void(0);" class="login_bt" id="login_bt" >注册</a>
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
		<p class="copy">Copyright @ 上海瓯丽信息科技有限公司 沪ICP14049149</p>
	</div>
</section>
</section>
<script type="text/javascript">
var area_url = "<?php echo U('Home/area/get_area');?>";
var city_url = "<?php echo U('Home/area/get_city');?>";
var check_username_url = "<?php echo U('Home/Register/check_unique_Name');?>";
var check_email_url = "<?php echo U('Home/Register/check_unique_Email');?>";
var submit_url = "<?php echo U('Home/Register/regact');?>";

$('input[name="member_type"]').click(function(){ 
	
	var type=$("input[name='member_type']:checked").val();
	if(type==2) {
		$("#company_show").hide();	
	} else { 
		$("#company_show").show();
	}
})

function show_err(name, msg) {
    var inpt = $("#"+name + "_inpt");
    inpt.html(msg);
    inpt.show();
}

function hide_err(name) {
    $("#"+name + "_inpt").hide();
}

var error_str = {
    'require_username':"请输入用户名",
    'require_pwd':"请输入密码",
    'require_company':"请输入公司名称",
    'require_contact':"请输入联系人",
    'require_from':"请选择地区",
    'vaild_pwd':'密码格式小于6位',
    'vaild_email':'邮箱格式不正确'
};

function passport_ajax(url,btn,callback){

    $('.popover').hide();

    var has_error = 0;
    var data = {};

    var type=$("input[name='member_type']:checked").val();
    data['member_type']=type;

    var fieldarr = ['username', 'pwd','company','contact','email'];
    $.each(fieldarr,function(i,k){
        var field = $('#Regfrom').find('#input_'+k);
        if(!field.length){
            return true;
        }

        if(type == 2 && k == 'company'){
           return true;
        }

        var value = field.val();
        if ($.trim(value) == ""){
            has_error = 1;
            show_err(k, error_str['require_'+k]);
            return false;
        }

        if(k == 'pwd'){
            if (value.length < 6){
                has_error = 1;
                show_err(k, error_str['vaild_'+k]);
                return false;
            }
        }

        if(k == 'email'){
            if (!~value.indexOf("@")){
                has_error = 1;
                show_err(k, error_str['vaild_'+k]);
                return false;
            }
        }

        data[k] = value;
    });

    data['pwd_confirm']=$("input[name='pwd_confirm']").val();
    data['country_id']=$("select[name=country_id]").val();
    data['area_id']= $("select[name=area_id]").val();
    data['city_id']=$("select[name=city_id]").val();

    if(data['country_id'] == 0 || data['area_id'] == 0 || data['city_id'] == 0){
        has_error = 1;
        show_err('from', error_str['require_from']);
    }

    data['is_show']=$("input[name='is_show']:checked").val();
    data['country_num']=$("input[name='country_num']").val();
    data['qu_num']=$("input[name='qu_num']").val();
    data['phone']=$("input[name='phone']").val();

    data['mobile_country_num']=$("input[name='mobile_country_num']").val();
    data['mobile_num']=$("input[name='mobile_num']").val();
    data['address']=$("input[name='address']").val();
    data['introduction']=$("textarea[name='introduction']").val();

    if(has_error){
        return;
    }

    btn.attr('disabled',true);

    $.ajax(url, {
        type: 'POST',
        data: data,
        dataType:'json',
        success:function(json){
            if(json.error){
                
                if($.isArray(json.response)){
                    $.each(json.response,function(i,error){
                        show_err(error.label, error.message);
                    });
                }else{
                    alert(json.response);
                }

            }else if(json.response){
                callback(json.response);
            }
        },
        complete:function(xhr, textStatus){
            btn.attr('disabled',false);
        }
    });
}

$('#login_bt').on('click',function(){
    passport_ajax(submit_url,$(this),function(res){
        setTimeout(function() {
            window.location.href = res;
        },1e3);
    });
});

</script>
</body>
</html>