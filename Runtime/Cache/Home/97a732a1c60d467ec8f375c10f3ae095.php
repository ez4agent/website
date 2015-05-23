<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>日程计划--EZ4Agent</title>
<link rel="stylesheet" type="text/css" href="/Public/front/css/share.css">
<link rel="stylesheet" type="text/css" href="/Public/front/css/main.css">
<link rel="stylesheet" type="text/css" href="/Public/front/css/common.css">
<link rel="stylesheet" href="/Public/front/js/skin/layer.css" />
<link rel="stylesheet" type="text/css" href="/Public/js/time/jquery.datetimepicker.css">
<script src="/Public/js/jquery.js" type="text/javascript"></script>
<script src="/Public/front/js/gobal.js" type="text/javascript"></script>
<script src="/Public/front/js/event.js" type="text/javascript"></script>
<script type="text/javascript" src="/Public/js/time/jquery.datetimepicker.js"></script>
<script src="/Public/front/js/layer.min.js" type="text/javascript"></script>
<!--[if lt IE 9]>
    <script src="/Public/js/html5.js"></script>
<![endif]-->
<style type="text/css">
#widget_calendar{
    width: 100%;
}
.th1 {
    background: none repeat scroll 0 0 #f5f5f5;
    border-bottom: 1px solid #e5e5e5;
    border-right: 1px solid #e5e5e5;
    font-size: 15px;
    font-weight: normal;
    text-align:center;
	height:30px;
}

.td1 {
	text-align: center;
    border-bottom: 1px solid #e5e5e5;
    border-right: 1px solid #e5e5e5;
	padding: 5px 2px;
	height:30px;
}

.fontb {color:white; background:#ff9600;}

</style>
</head>
<body>
<section class="main_wrap main_wrap_inner">
	<!--公用方法-->
    <!--公用栏目-->

<section class="divMade head_wrap">
    <div class="divMadeInner clearfix"> <a href="#">EZ4Agent</a> </div>
</section>
<nav class="nav_wrap">
  <div class="nav_wrap_inner clearfix">
    <ul class="clearfix">
      <?php if(is_array($meun)): $i = 0; $__LIST__ = $meun;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li <?php if($vo['select'] == 1): ?>class="active"<?php endif; ?>>
           <a href="<?php echo ($vo["url"]); ?>" class="firstA"><?php echo ($vo["name"]); ?></a> 
           <?php if(!empty($vo['child'])): ?><dl class="nav_dl4">
                <?php if(is_array($vo['child'])): $i = 0; $__LIST__ = $vo['child'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo1): $mod = ($i % 2 );++$i;?><dd>
                      <a href="<?php echo ($vo1["url"]); ?>"><span><?php echo ($vo1["name"]); ?></span></a>
                    </dd><?php endforeach; endif; else: echo "" ;endif; ?>
            </dl><?php endif; ?>
          <?php if(($vo['name'] == '提醒') and ($event_num > 0)): ?><i><?php echo ($event_num); ?></i><?php endif; ?>
      </li><?php endforeach; endif; else: echo "" ;endif; ?>
    </ul>
  </div>
</nav>
    <section class="content_wrap clearfix">
    <!--左边-->
    <div class="content_left fl">
        <div class="con_item">
            <h2 class="con_item_tit clearfix">
                <em class="font18 ffyh fl">日历</em>
            </h2>
            <div class="rili_xc">
             <div style="width:100%; border:1px solid #999" >
              <?php
 use Common\Util\Calendar; $Calendar1 = new Calendar(); $Calendar1->out(); ?>
             </div>
            </div>
        </div>
    </div>
    <!--右边-->
    <div class="content_right fr">
          <div class="con_item">
              <h2 class="con_item_tit clearfix"><em class="font18 ffyh">日程列表</em>
              <a href="javascript:void(0);" onClick="add_event(0,0,'<?php echo U('Home/Schedule/event_form');?>');"><i class="icon icon2 fr"></i></a></h2>             
              <div class="con_item_c clearfix">
                  <div class="rc_list_tit1">
                      <em class="font14"><a href="<?php echo U('Home/Schedule/index',array('type'=>'now'));?>">
                      <font <?php if($type == now): ?>color="#ff9600"<?php endif; ?>><strong>今日待办</strong></font>&nbsp;
                      <span><?php echo ($now); ?></span></a></em>
                      <em class="font14"><a href="<?php echo U('Home/Schedule/index',array('type'=>'plan'));?>">
                      <font <?php if($type == plan): ?>color="#ff9600"<?php endif; ?>><strong>计划提醒</strong></font>&nbsp;
                      <span><?php echo ($plan_count); ?></span></a></em>
                      <em class="font14"><a href="<?php echo U('Home/Schedule/index',array('type'=>'over'));?>">
                      <font <?php if($type == over): ?>color="#ff9600"<?php endif; ?>><strong>历史记录</strong></font>&nbsp;
                      <span><?php echo ($past); ?></span></a></em>   
                  </div>
                  <div class="today_rc_con">
                      <div class="today_con_hd">
                          <span class="col1">日&nbsp;&nbsp;期</span>   
                          <span class="col2">
                          <select id="stuid" name="stuid" class="address_gj">
                            <option value="0">全部学生</option>
                          <?php echo ($str); ?>
                          </select></span>   
                          <span class="col3">主&nbsp;&nbsp;题</span>   
                          <span class="col4">完成日期</span>
                      </div>
                      <div class="rc_con_tabel">
                          <ul>
                          	<?php if(empty($list)): ?><li style="text-align:center;"><span style="font-size:14px; font-weight:bold;">今日无日程安排!</span></li>
                            <?php else: ?>
                          	<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li <?php if($vo['is_use'] == 0): ?>class="stat_on"<?php else: ?> class="stat_on1"<?php endif; ?>>
                                  <span class="rc_time col1">
                                      <b><?php echo ($vo['date_value']); ?></b>
                                  </span>
                                  <span class="rc_st_name col2"><?php echo ($vo['stu_name']); ?></span>
                                  <span class="rc_zhuti col3">
                                      <a href="javascript:void(0);" onClick="add_event(<?php echo ($vo['stu_id']); ?>,<?php echo ($vo['event_id']); ?>,'<?php echo U('Home/Schedule/event_form');?>');">
                                      <em><?php echo ($vo['title']); ?></em>
                                      </a>
                                  </span>
                                  <?php if($vo['is_use'] == 1): ?><span class="rc_time col4"><b>[完成]</b> <?php echo ($vo['finishtime']); ?></span>
                                  <?php else: ?>
                                  <span class="rc_time col4">
                                     <a href="javascript:void(0);" class="is_use" data_attr="<?php echo ($vo['event_id']); ?>" uri="<?php echo U('Home/Schedule/change_use');?>"><b style="color:#ff9900; ">[未完成]</b></a>
                                  </span><?php endif; ?>
                              </li><?php endforeach; endif; else: echo "" ;endif; endif; ?>
                          </ul>
                           <div class="peg_ing"><?php echo ($page); ?></div>
                      </div>
                  </div>
              </div>
          </div>			
	</div>
    </section>
    <!--添加事件-->
    <div id="add_event" style="display:none">
     <form id="event_form">
    	<div class="add_school baseInfor" style="padding-top:20px;" >
            <table width="100%">
            <tbody>
            <tr>									
                <td colspan="2" height="25px">主&nbsp;&nbsp;题： 
                <input type="text" class="baseInfor_input school_name"  style="width:360px;" name="title" id="title" />
                </td>
            </tr>
            <tr>									
                <td width="40%" height="25px">学&nbsp;&nbsp;生： 
                <select id="stu_id1" name="stu_id" class="address_gj">
                <option value="0">==选择学生==</option>
                {str}
                </select>
                </td>
            	<td width="60%" height="25px">日&nbsp;&nbsp;期：
                    <input name="date_value" id="date" type="text" class="baseInfor_input school_name"  style="width:112px;" />
                </td>
            </tr>
            <tr>									
                <td colspan="2"><p>内&nbsp;&nbsp;容：</p> <textarea class="baseInfor_input textarea_js" style="width:600px" name="content" id="content"></textarea></td>
            </tr>
            <tr>
            	<td colspan="2" height="25px"><input name="is_use" type="checkbox" value="1">&nbsp;已完成</td>
            </tr>
        </tbody>
        </table>
    </div>
    <div class="blod_js">
		<input id="events_url" type="hidden" value="<?php echo U('Home/Schedule/add_events');?>" />
        <input id="member_id" name="member_id" type="hidden" value="<?php echo ($member_id); ?>" />
        <input id="events_id" type="hidden" name="event_id" value="" />
        <a href="javascript:void(0);" id="sub_events_act" >添&nbsp;&nbsp;加</a>
	</div>
    </form>
    </div>
    <!--查看事件-->
    <div id="view_event" style="display:none">
     	<div class="add_school baseInfor" style="padding-top:20px;" >
         <table width="100%">
            <tbody>
            <tr>									
                <td colspan="2" height="25px">主&nbsp;&nbsp;题：<span id="event_title"></span></td>
            </tr>
            <tr>									
                <td width="40%" height="25px">学&nbsp;&nbsp;生：<span id="event_stu"></span></td>
            	<td width="60%" height="25px">日&nbsp;&nbsp;期：<span id="event_date_value"></span></td>
            </tr>								
            <tr>									
                <td colspan="2"><p style="margin-bottom:5px;">内&nbsp;&nbsp;容：</p> 
                <div id="event_content" style="word-break:break-all;width:600px; padding-right:20px;"></div>
                </td>
            </tr>
            <tr>
            	<td colspan="2" height="25px"><span id="event_is_use"></span></td>
            </tr>
        	</tbody>
        </table>
    	</div>
    </div>
    <!--查看事件-->
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
//时间
$('#date').datetimepicker({
	timepicker:false,
	format:'Y/m/d',
	formatDate:'',
});
</script>
</body>
</html>