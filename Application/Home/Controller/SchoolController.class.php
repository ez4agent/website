<?php

/**
 *  CRM系统--院校管理 
 */
namespace Home\Controller;
use Common\Controller\FrontbaseController;

class SchoolController extends FrontbaseController
{
    var $school_mod;
    var $partner_mod; 
    var $education; //学历配置
    var $member_id;
    
    public function __construct()
    {
        parent::__construct();
        //实例化院校模型
        $this->school_mod = D('School');
        $this->partner_mod = D('Partner');
        $this->education = C('Education_TYPE');
        $this->member_id = $this->auth()->member_id;
    }
    
    /**
     *  查询条件 
     */
    public function _query_condition()
    {
        //查询数据库的筛选条件
        $college_ids = array();
        $where['condition']=array();
        $where['webshow']=array();
        //国家
        $country = I('param.country_id',0,'intval');
        $where['webshow']['country_id']=$country;
        if(!empty($country))
        {
            $where['condition']['country_id']=$country;
        }
        
        //省州
        $area = I('param.area_id',0,'intval');
        $where['webshow']['area_id'] = $area;
        if(!empty($area))
        {
            $where['condition']['area_id'] = $area;
        }
        
        //城市
        $city = I('param.city_id',0,'intval');
        $where['webshow']['city_id']=$city;
        if(!empty($city))
        {
            $where['condition']['city_id']=$city;
        }
        
        //学历
        $education = I('param.education',0,'intval');
        $where['webshow']['education'] = $education;
        if(!empty($education))
        {
            $college_id = M('college_education')->where('education='.$education)->field('college_id')->select();
            if(!empty($college_id))
            {
                foreach($college_id as $key=>$val){
                    $college_ids[] = $val['college_id'];
                }
            }
        }
        
        //关键字
        $keyword=I('param.keywords','','trim');
        $where['webshow']['keyword'] = $keyword;
        if(!empty($keyword))
        {
            $where['condition']['_string'] = ' (cname like "%'.$keyword.'%")  OR ( ename like "%'.$keyword.'%") ';
        }

        //有合作的院校
        $partnerRows = M('partner_college')->group('college_id')->select();
        $partner_college_ids = array();
        foreach($partnerRows as $v){
            $partner_college_ids[] = $v['college_id'];
        }

        if(!empty($college_ids)){
            $college_ids = array_intersect($partner_college_ids,$college_ids);
        }else{
            $college_ids = $partner_college_ids;
        }

        $where['condition']['college_id'] = array('in',$college_ids);


        return $where;
    }
    
    /**
     *  院校筛选 
     */
    public function index()
    {
        //查询条件
        $where = $this->_query_condition();
        //统计个数
        $count = $this->school_mod->get_count($where['condition']);
        //分页
        $Page = new \Think\Page($count,30);
        //院校数据
        $limit = $Page->firstRow.','.$Page->listRows;
        $list = $this->school_mod->get_college_list($where['condition'],$limit,'college_id asc');
        if(!empty($where['condition'])){
            foreach($where['condition'] as $key=>$val){
                $Page->parameter[$key] = urlencode($val);
            }
        }        
        //显示筛选条件
        $this->select_info($where['webshow']);
        //合作院校
        $this->assign('partner',$this->get_partner_info($this->member_id));
        $this->assign('view',$this->get_view_college($this->member_id));
        $this->assign('apply',$this->get_apply_college($this->member_id));
        $this->assign('list',$list);
        $this->assign('page',$Page->show());
        $this->assign('typeid',I('param.type',0,'intval'));
        $this->display();
    }
    
    /**
     *  显示筛选条件 
     */
    public function select_info($where)
    {
        $select = array();
        //国家
        if($where['country_id']==0){          
            $this->assign('country',$this->school_mod->get_country_Attribute($where));
        }else{
            $cname = M('country')->where('countryid='.$where['country_id'])->getField('cname');
    		$select['cuntry']=array('type'=>'country_id','name'=>$cname);
        }  
        //省州
        if($where['area_id']!=0){
            $cname = M('area')->where('aid='.$where['area_id'])->getField('cname');
            $select['area']=array('type'=>'area_id','name'=>$cname);
            $this->assign('city',$this->school_mod->get_city_Attribute($where));
            $this->assign('area_id',$where['area_id']);
            $this->assign('country_id',$where['country_id']);
        }else{
            if($where['country_id']!=0)
            {
                $this->assign('area',$this->school_mod->get_area_Attribute($where));
                $this->assign('country_id',$where['country_id']);
            }
        }
        
        //城市
        if($where['city_id']!=0)
        {
            $cname = M('city')->where('cid='.$where['city_id'])->getField('cname');
            $select['city']=array('type'=>'city_id','name'=>$cname);
            $this->assign('city',array());
        }
        
        //学历
        if($where['education']==0)
        {
            $edu = C('Education_TYPE');
            //$this->assign('education',$this->school_mod->get_CollegeType_Attribute($where));
            $this->assign('education',$edu);
        }
        else 
        {
            $edu = C('Education_TYPE');
            $select['education']=array('type'=>'education','name'=>$edu[$where['education']]);
        }

        if($where['keyword']){
            $this->assign('keyword',$where['keyword']);
        }

        $this->assign('select',$select);
    }
    
    //字母切换
    public function zimuchange()
    {
        if(IS_AJAX)
        {
            $zimu = I('post.zimu','','trim');
            $_type = I('post.s_type','','trim');
            $_country = I('post.country_id','0','intval');
            $_area = I('post.area','0','intval');
            $str ='';
            
            if($_type=='country') //国家
            {
               $field = 'country_id';
               $country = M('country')->where(array('zimu'=>$zimu,'countryid'=>array('NEQ',1)))->select();
               foreach($country as $key=>$val)
               {
                   $str.="<a href='javascript:void(0)' class='countryid'  country_id='".$val['countryid']."'>".$val['cname']."</a>";
               }
               $str.='<b>x</b>';
            }
            elseif($_type=='area') //省/州
            {
                $field = 'area_id';
                $area = M('area')->alias('a')->field('a.aid,a.cname')->join('__COLLEGE__ c ON c.area_id = a.aid')
                                 ->group('a.cname')->where(array('zimu'=>$zimu,'countryid'=>$_country))->select();
                foreach($area as $key=>$val)
                {
                    $str.="<a href='javascript:void(0)' class='aid'  area_id='".$val['aid']."'>".$val['cname']."</a>";
                }
                $str.='<b>x</b>';
            }
            else //城市
            {
                $field = 'city_id';
                $city = M('city')->alias('a')->field('a.cid,a.cname')->join('__COLLEGE__ c ON c.city_id = a.cid')
                                 ->group('a.cname')->where(array('zimu'=>$zimu,'countryid'=>$_country,'a.area_id'=>$_area))->select();
                foreach($city as $key=>$val)
                {
                    $str.="<a href='javascript:void(0)'class='cityid'  city_id='".$val['cid']."'>".$val['cname']."</a>";
                }
                $str.='<b>x</b>';
            }
            
            $this->ajaxReturn(array('status'=>1,'str'=>$str));
        }
    }
    
    //添加合作院校
    public function add_partner()
    {
        if(IS_AJAX)
        {
            $college_id = I('post.college_id',0,'intval');
            $_info = M('partner_college')->where(array('member_id'=>$this->member_id,'college_id'=>$college_id))->find();

            if($_info){
                $this->ajaxReturn(array('status'=>0,'msg'=>'该院校已经被添加为合作院校'));
            }else{
                $insert=array(
                    'member_id'=>$this->member_id,
                    'college_id'=>$college_id,
                    'addtime'=>time(),
                );
                $partner_id=$this->partner_mod->add_partner_info($insert);
                if($partner_id)
                {
                    $this->ajaxReturn(array('status'=>1,'url'=>U('School/partner',array('partner_id'=>$partner_id))));
                }
                else 
                {
                    $this->ajaxReturn(array('status'=>0,'msg'=>'合作院校添加失败！'));
                }
            }   
        }
    }
    //取消合作院校
    public function cancel_partner()
    {
        if(IS_AJAX)
        {
            $college_id = I('post.college_id',0,'intval');
            //判断是否是合作院校。
            $is_partner= M('partner_college')->where(array('college_id'=>$college_id,'member_id'=>$this->member_id))->count();
            
            if($is_partner)
            {
                //执行取消操作
                if($this->partner_mod->cancel_partner($college_id,$this->member_id))
                {
                    $this->ajaxReturn(array('status'=>1,'msg'=>'取消成功！','url'=>U('Home/School/index')));
                }
            }
            else 
            {
                $this->ajaxReturn(array('status'=>0,'msg'=>'该院校不是合作院校'));
                exit();
            }
        }
    }
    //查看院校信息
    public function college_view()
    {
        $id = I('get.id',0,'intval');
        $stu_id = I('get.stu_id',0,'intval');
        
        if(!$id){
            $this->error('该院校信息不存在');exit();
        }
        
        //读出该校的学历列表
        $edu_info = get_educationbycollege_id($id);
        //读出该院校佣金分享
        $share = $this->partner_mod->get_share_college($id);
        //判断是否是合作院校
        $is_partner= M('partner_college')->where(array('college_id'=>$id,'member_id'=>$this->member_id))->count();
        //记录浏览过的院校
        if(!M('college_view')->where(array('member_id'=>$this->member_id,'college_id'=>$id))->count())
        {
            M('college_view')->add(array('member_id'=>$this->member_id,'college_id'=>$id));
        }

        $attachArr = D('CollegeAttach')->loadList(array('college_id'=>$id));
        $url = "http://".$_SERVER['HTTP_HOST'];
        foreach($attachArr as $k =>$v){
            $attachArr[$k]['file_url'] = $url.$v['file_path'];
        }


        $this->assign('add',$is_partner?1:0);
        $this->assign('info',$this->school_mod->get_college_info($id));
        $this->assign('partner',$this->get_partner_info($this->member_id));
        $this->assign('view',$this->get_view_college($this->member_id));
        $this->assign('apply',$this->get_apply_college($this->member_id));
        $this->assign('edu_info',$edu_info);
        $this->assign('share',!empty($share)?$share:0);
        $this->assign('stu_id',$stu_id);
        $this->assign('flag',I('get.flag','','trim'));
        $this->assign('attachArr',$attachArr);
        $this->display();
    }

    //获取佣金分享按学历
    public function share_info_byselect()
    {
        if(IS_AJAX)
        {
           $select = I('post.select',0,'intval');
           $college_id = I('post.college_id',0,'intval');
           $stu_id = I('post.stu_id',0,'intval');
           $page = isset($_POST['page'])?intval($_POST['page']):1;
           $pagesize =isset($_POST['items_per_page'])?intval($_POST['items_per_page']):8;
           //获取总条数
           $map = array('college_id'=>$college_id,'apply_id'=>$select);
           $total = M('partner_college_commission')->where($map)->count();
           //获取commission_id
           $array = $this->partner_mod->get_share_college($college_id, $select);
            $pay_type = C('pay_type');

           $str ="<table width='100%'>
                    <thead>
                         <tr>
                            <th width='15%' height='25px'><strong>中介</strong></th>
                            <th width='15%' height='25px'><strong>支付方式</strong></th>
                            <th width='40%' height='25px' style='padding:0px;'>
                                <div style='width:100%; height:20px; border-bottom:1px #eee solid; padding:5px 0 3px 0;'>
                                    <span><strong>佣金分享</strong></span>
                                </div>
                                <table width='100%' style='border:none'>
                                    <tr>
                                        <th width='25%' height='20px' style='border:none;border-right:1px #eee solid;'><strong>百分比</strong></th>
                                        <th width='25%' height='20px' style='border:none;border-right:1px #eee solid;'><strong>长度</strong></th>
                                        <th width='25%' height='20px' style='border:none;border-right:1px #eee solid;'><strong>规则</strong></th>
                                        <th width='25%' height='20px' style='border:none;'><strong>固定金额</strong></th>
                                    </tr>
                                </table>
                            </th>
                            <th width='10%' height='25px'><strong>支付周期</strong></th>
                            <th width='10%' height='25px'><strong>备注</strong></th>
                            <th width='10%' height='25px'><strong>操  作</strong></th>
                            </tr>
                         </thead>
                         <tbody>";
           if(empty($array))
           {
               if($stu_id!=0)
               {
                   $str.="<tr><td colspan='7' height='30px' align='center'><strong>无分享信息,请点
                          <input id='upload_info' class='updatabtn' type='button' value='帮助' onClick='college_help(".$college_id.");' />
                          &nbsp;&nbsp;<a href='".U('Home/Student/index',array('stu'=>$stu_id))."'><input class='updatabtn' type='button' value='返回'/></a> 
                          </strong></td></tr>";
               }
               else 
               {
                   $str.="<tr><td colspan='7' height='30px' align='center'><strong>无分享信息,请点
                          <input id='upload_info' class='updatabtn' type='button' value='帮助' onClick='college_help(".$college_id.");' />
                          &nbsp;&nbsp;<a href='".U('Home/Student/index')."'><input class='updatabtn' type='button' value='返回'/></a>
                          </strong></td></tr>";
               }    
           }
           else 
           {
              foreach($array as $key=>$val)
              {
                  $share_length = "--";
                  if($val['pay_type'] == 2){
                      $share_length = $val['share_length']. "学期";
                  }elseif($val['pay_type'] == 1){
                      $share_length = $val['share_length']. "学年";
                  }

                  $share_desc = !empty($val['share_desc']) ? '<a href="javascript:;" class="desc_show">查看</a>' : '无';

                  $str.="<tr>
                             <td height='30px'><strong><a href='javascript:void(0);' onclick='view_member(".$val['member_id'].");'>".$val['username']."</a></strong></td>
                             <td height='30px'><strong>".$pay_type[$val['pay_type']]."</strong></td>
                             <td height='30px' style='padding:0px;'>
                                 <table width='100%'  style='border:none;'>
                                 <tr>
                                     <th width='25%' height='35px;' style='border:none;border-right:1px #eee solid;padding:0px;'>
                                     ".$val['share_ratio']." %
                                     </th>
                                     <th width='25%' height='35px;' style='border:none;padding:0px;border-right:1px #eee solid;'>
                                     ".$share_length."
                                     </th>
                                     <th width='25%' height='35px;' style='border:none;padding:0px;border-right:1px #eee solid;'>
                                     且不高于
                                     </th>
                                     <th width='25%' height='35px;' style='border:none;padding:0px;'>
                                     ".($val['set_price'] > 0 ? "$".$val['set_price'] : '')."
                                     </th>
                                 </tr>
                                 </table>
                             </td>
                             <td height='30px'>".$val['pay_cycle']." 周</td>
                             <td height='30px'>".$share_desc."<div style='display:none' class='share_desc'>".stripslashes (htmlspecialchars ($val['share_desc']))."</div></td>";
                  $str.="<td><input id='upload_info' class='updatabtn' type='button' value='申请'
                    onClick='college_apply_header(".$college_id.",".$val['commission_id'].");'
                  /></td>";
                  $str.="</tr>";
              } 
           }
            $str.="</tboby></table>";
           $this->ajaxReturn(array('status'=>'yes','str'=>$str,'total'=>$total));
        }
    }


    //获取佣金分享按学历
    public function share_info_byselect2()
    {
        if(IS_AJAX)
        {
            $select = I('post.select',0,'intval');
            $college_id = I('post.college_id',0,'intval');
            $apply = I('post.apply',1,'intval');

            $map = array('college_id'=>$college_id,'education'=>$select);
            $total = D('CollegeCommision')->countList($map);
            $array = D('CollegeCommision')->loadList($map);

            $pay_type = C('pay_type');

            $str ="<table width='100%'>
                    <thead>
                         <tr>
                            <th width='15%' height='25px'><strong>日期</strong></th>
                            <th width='10%' height='25px'><strong>申请数量</strong></th>
                            <th width='10%' height='25px'><strong>实际入学</strong></th>
                            <th width='10%' height='25px'><strong>首年佣金</strong></th>
                            <th width='10%' height='25px'><strong>首年服务费</strong></th>
                            <th width='10%' height='25px'><strong>后续年佣金</strong></th>
                            <th width='10%' height='25px'><strong>后续服务费</strong></th>
                            <th width='7%' height='25px'><strong>奖励</strong></th>
                            <th width='8%' height='25px'><strong>详情</strong></th>
                            </tr>
                         </thead>
                         <tbody>";
            if(empty($array))
            {
                $str.="<tr><td colspan='9' height='30px' align='center'><strong>无分享信息,请点
                      <input id='upload_info' class='updatabtn' type='button' value='帮助' onClick='college_help(".$college_id.");' />
                      &nbsp;&nbsp;<a href='".U('Home/Student/index')."'><input class='updatabtn' type='button' value='返回'/></a>
                      </strong></td></tr>";
            }
            else
            {

                $receive_group = array();
                $stu_receive_count = M('stu_receive')->where(array('college_id'=>$college_id,'education_id'=>$select,'status'=>1))->count();


                foreach($array as $key=>$val) {
                    $str.="<tr>
                             <td height='30px'>".$val['enroll_time_start'] .' - '.$val['enroll_time_end']."</td>
                             <td height='30px'><strong>".$stu_receive_count."</strong></td>
                             <td height='30px'>0</td>
                             <td height='30px'><strong>".$val['first_pay']."</strong>%</td>
                             <td height='30px'><strong>".$val['first_service_price']."</strong>%</td>
                             <td height='30px'><strong>".$val['after_pay']."</strong>%</td>
                             <td height='30px'><strong>".$val['after_service_price']."</strong>%</td>
                             <td height='30px'><strong>".$val['ext_price']."</strong></td>
                             <td height='30px'><a href='javascript:college_commision_view(\"".$val['id']."\");' class='desc_show'>查看</a></td>
                           </tr>";
                }

                if($apply){
                    $str.="<td colspan='9' height='30px' align='center'>
                    <input id='upload_info' class='updatabtn' type='submit' value='申请' /></td>";
                    $str.="</tr>";
                }
            }
            $str.="</tboby></table>";



            $this->ajaxReturn(array('status'=>'yes','str'=>$str,'total'=>$total));
        }
    }

    public function commision_view(){
        $commision_id = I('param.commision',0,'intval');

        $CollegeCommision = D('CollegeCommision')->get_info($commision_id);
        if(!$CollegeCommision){
            echo '指定记录不存在';
            exit;
        }

        $stu_receive_count = M('stu_receive')->where(array('college_id'=>$CollegeCommision['college_id'],'education_id'=>$CollegeCommision['education'],'status'=>1))->count();
        $stu_receive_finish_count = M('stu_receive')->where(array('college_id'=>$CollegeCommision['college_id'],'education_id'=>$CollegeCommision['education'],'is_finish'=>1,'status'=>1))->count();

        $stu_receives = M('stu_receive')->where(array('college_id'=>$CollegeCommision['college_id'],'education_id'=>$CollegeCommision['education'],'status'=>1))->order('add_time desc')->select();
        $mids = $member_infos = array();
        foreach($stu_receives as $r){
            $mids[] = $r['from_member_id'];
        }

        if($mids){
            $mids = array_unique($mids);
            //print_r($mids);exit;
            $member_rows = M('member')->where("member_id IN (".join(',',$mids).")")->select();
            foreach($member_rows as $r){
                if($r['member_type'] == 1){
                    $member_infos[$r['member_id']] = $r['username'];
                }else{
                    $member_infos[$r['member_id']] = $r['username'];
                }
            }
        }


        $str ="
            <div class='commision_view'>
            <table width='100%'>
                  <tr>
                    <th>开学日期：</th><td>".$CollegeCommision['enroll_time_start']."</td>
                  </tr>
                  <tr>
                    <th>申请数量：</th><td>".$stu_receive_count."</td>
                  </tr>
                  <tr>
                    <th>实际入学：</th><td>".$stu_receive_finish_count."</td>
                  </tr>
                  <tr>
                    <td colspan='2'>
                        <table  width='100%'>
                              <tr>
                                <th>首年佣金：</th><td>".$CollegeCommision['first_pay']." %</td>
                                <th>首年服务费：</th><td>".$CollegeCommision['first_service_price']." %</td>
                              </tr>
                              <tr>
                                <th>后续年佣金：</th><td>".$CollegeCommision['after_pay']." %</td>
                                <th>后续服务费：</th><td>".$CollegeCommision['after_service_price']." %</td>
                              </tr>
                        </table>
                    </td>
                  </tr>
                  <tr>
                    <th>奖励：</th><td>".$CollegeCommision['ext_price']."</td>
                  </tr>
              </table>
              <h1>申请名单</h1>
              <table width='100%' class='apply_commision_list'>
                  <tr>
                    <th>中介</th>
                    <th>申请日期</th>
                    <th>入学</th>
                  </tr>";
                  foreach($stu_receives as $r){
                      $str .= "<tr>
                        <th>".$member_infos[$r['from_member_id']]."</th>
                        <th>".date('Y-m-d',$r['add_time'])."</th>
                        <th>".($r['is_finish']? "是": "否")."</th>
                      </tr>";
                  }
        $str .= "</table></div>";
        echo $str;
        exit;
    }


    //读取合作院校信息列表
    public function get_partner_info($member_id)
    {
        $_partner = array();
        $_partner =$this->partner_mod->get_partner_listbymemberId($member_id);
        return $_partner;
    }
    
    //读取参看过的院校的名称
    public function get_view_college($member_id)
    {
        $_view= array();
        $_view = $this->school_mod->get_view_college_list($member_id);
        return $_view;
    }
    
    //读取申请院校
    public function get_apply_college($member_id)
    {
        $_view= array();
        $_view = $this->school_mod->get_apply_college_list($member_id);
        return $_view;
    }
    
    //查看合作院校
    public function partner()
    {
        $parter_id = I('get.partner_id',0,'intval');
        
        $map = array('partner_id'=>$parter_id,'member_id'=>$this->member_id);
        $college_id = getField_value('partner_college','college_id',$map);
        if(!$college_id){
            $this->error('您查看的合作院校不存在！');
            exit();
        }

        $user_commission = array();
        $user_commission_rows = M('partner_college_commission')->where(array('member_id'=>$this->member_id,'partner_id'=>$parter_id))->select();
        foreach($user_commission_rows as $user_value){
            $user_commission[$user_value['apply_id']] = $user_value;
        }

        $college_info = D('School')->get_college_info($college_id);

        $commission = array();
        $sharing_desc = '';

        foreach($college_info['edu'] as $edu){
            $tmp = array(
                'education_id' => $edu['id'],
                'education_name' => $edu['name'],
                'payment_type' => isset($user_commission[$edu['id']]['pay_type']) ? $user_commission[$edu['id']]['pay_type'] : 0,
                'sharing_ratio' => isset($user_commission[$edu['id']]['share_ratio']) ? $user_commission[$edu['id']]['share_ratio'] : '',
                'length' =>  isset($user_commission[$edu['id']]['share_length']) ? $user_commission[$edu['id']]['share_length'] : '',
                'cycle' => isset($user_commission[$edu['id']]['pay_cycle']) ? $user_commission[$edu['id']]['pay_cycle'] : '',
                'commission_id' =>  isset($user_commission[$edu['id']]['commission_id']) ? $user_commission[$edu['id']]['commission_id'] : 0,
                'set_price' =>  isset($user_commission[$edu['id']]['set_price']) ? $user_commission[$edu['id']]['set_price'] : '',
            );

            $sharing_desc = empty($sharing_desc) && isset($user_commission[$edu['id']]['share_desc']) ? htmlspecialchars($user_commission[$edu['id']]['share_desc']) : $sharing_desc;

            $commission[] = $tmp;
        }

        $need_update_contact = 'false';
        $contact = D('Member')->get_Member_Info($this->member_id);

        if(!empty($contact['company']) &&
            !empty($contact['contact']) &&
            (!empty($contact['telephone']) || !empty($contact['mobile'])) &&
            !empty($contact['address'])){
            $need_update_contact = 'true';
        }

        $this->assign('contact',$contact);
        $this->assign('need_update_contact',$need_update_contact);

        $this->assign('commission',$commission);
        $this->assign('sharing_desc',$sharing_desc);

        $this->assign('partner',$this->get_partner_info($this->member_id));
        $this->assign('view',$this->get_view_college($this->member_id));
        $this->assign('apply',$this->get_apply_college($this->member_id));
        $this->assign('info',$college_info);

        $this->assign('partner_id',$parter_id);
        $this->assign('paytype',C('pay_type'));
        $this->display('partnerinfo');
    }
    
    //更新佣金
    public function edit_value()
    {
        if(!IS_AJAX || !$this->member_id) {
            exit;
        }
/*
        $contact = D('Member')->get_Member_Info($this->member_id);

        if(empty($contact['company']) ||
            empty($contact['contact']) ||
            (empty($contact['telephone']) && empty($contact['mobile'])) ||
            empty($contact['address'])){
            $this->ajaxReturn(array('status'=>'no','msg'=>'邮寄联系方式信息不完整'));
            exit;
        }

*/
        $data = $_POST;

        if(!isset($data['education_id']) || !isset($data['partner_id'])){
            exit;
        }

        $partner_college = M('partner_college')->where(array('partner_id' => intval($data['partner_id'])))->find();
        if(empty($partner_college) || $partner_college['member_id'] != $this->member_id){
            $this->ajaxReturn(array('status'=>'no','msg'=>'没有权限， 请重新登陆'));
            exit;
        }

        $education = C('Education_TYPE');

        $update_arr = array();
        foreach($data['education_id'] as $education_id){
            $update = array();
            $update['apply_id'] = $education_id;
            $update['education'] = $education[$education_id];

            if(isset($data['commission_id'][$education_id]) && $data['commission_id'][$education_id] > 0){
               // $update['commission_id'] = $data['commission_id'][$education_id];
            }

            $update['pay_type'] = isset($data['payment_type'][$education_id]) ? $data['payment_type'][$education_id] : 0;
            $update['pay_cycle'] = isset($data['cycle'][$education_id]) ? $data['cycle'][$education_id] : 0;
            $update['set_price'] = isset($data['set_price'][$education_id]) ? floatval($data['set_price'][$education_id]) : 0;
            $update['share_length'] = isset($data['length'][$education_id]) ? $data['length'][$education_id] : 0;
            $update['share_desc'] = isset($data['share_desc']) ? addslashes(strip_tags($data['share_desc'])) : '';
            $update['share_ratio'] = isset($data['sharing_ratio'][$education_id]) ? $data['sharing_ratio'][$education_id] : 0.0;

            if(!$update['pay_type']){
                continue;
            }

            if(!is_numeric($update['share_ratio'])){
                //$this->ajaxReturn(array('status'=>'no','msg'=>'佣金分享百分比格式不正确'));
                //exit;
            }

            if($update['pay_type'] !=3 && !is_numeric($update['share_length'])){
                //$this->ajaxReturn(array('status'=>'no','msg'=>'佣金分享长度格式不正确'));
                //exit;
            }elseif($update['pay_type'] ==3){
                $update['share_length'] = 0;
            }

            if(!preg_match('/^[0-9]{0,2}+(\.5)*$/',$update['share_ratio']))
            {
                //$this->ajaxReturn(array('status'=>'no','msg'=>'佣金分享百分比必须在1-99之间,可以有0.5增减,且一位小数'));
                //exit;
            }

            if(!is_numeric($update['pay_cycle']) || intval($update['pay_cycle']) > 52 || intval($update['pay_cycle']) < 1){
                //$this->ajaxReturn(array('status'=>'no','msg'=>'周期必须在1-52的整数'));
                //exit;
            }

            //$update['share_length'] = 0;

            $update['member_id'] = $this->member_id;
            $update['college_id'] = $partner_college['college_id'];
            $update['partner_id'] = $partner_college['partner_id'];
            $update_arr[] = $update;
        }

        if(empty($update_arr)){
            $this->ajaxReturn(array('status'=>'no','msg'=>'请输入佣金信息'));
            exit;
        }

        $model = M('partner_college_commission');
        if($model->addAll($update_arr,array(),true)){

            $college_info = M('college')->where(array('college_id' => $partner_college['college_id']))->find();
            if($college_info['country_id'] == 2){
                $visa_info = M('visa_service')->where(array('country_id' => 2,'member_id'=>$this->member_id))->find();
                if(empty($visa_info)){
                    $this->ajaxReturn(array('status'=>'ok','has_visa'=>0));
                    exit;
                }
            }

            $this->ajaxReturn(array('status'=>'ok','has_visa'=>'true'));
            exit;
        }else{
            $this->ajaxReturn(array('status'=>'no','msg'=>'更新失败'));
            exit;
        }

        $this->ajaxReturn(array('status'=>'ok'));
        exit;

    }
    
    
    /**
     *  院校帮助 
     */
    public function college_help()
    {
        if(IS_AJAX)
        {
            $insert =array(
                'college_id'=>intval($_POST['college_id']),
                'member_id'=>$this->member_id,
                'title'=>trim($_POST['title']),
                'content'=>trim($_POST['remark']),
                'is_read'=>0,
                'addtime'=>time(),
            );
            
            $id = $this->partner_mod->add_info('college_help',$insert);
            if(!$id)
            {
                $this->ajaxReturn(array('status'=>'no','msg'=>'提交失败!'));
                exit();
            }
            $this->ajaxReturn(array('status'=>'ok'));
            exit();
        }
    }
    
    //查看个人信息
    public function get_user_info()
    {
        if(IS_AJAX)
        {
            $member_id = I('member_id',0,'intval');
            $info = D('Member')->get_Member_Info($member_id);
            if(!$info)
            {
                $this->ajaxReturn(array('status'=>0,'msg'=>'无该用户信息！'));
                exit();   
            }
            //获取该用户有多少合作院校
            $num=0;
            $list = $this->partner_mod->get_partner_list('partner_college',array('member_id'=>$member_id));
            if(!empty($list))
            { 
                foreach($list as $key=>$val)
                { 
                    $map = array('college_id'=>$val['college_id'],'member_id'=>$member_id,'sharing_ratio'=>array('neq',0));
                    $data = M('share_value')->where($map)->select();
                    if(empty($data))
                    { 
                        unset($list[$key]);
                    }
                }
                
                $num = count($list);
            }
            
            $member = array(
                'username'=>$info['username'],
                'member_type'=>$info['member_type'],
                'type'=>($info['member_type']==1)?'企业':'个人',
                'area'=>$info['city_name'].','.$info['area_name'].','.$info['country_name'],
                'company'=>($info['member_type']==1)?$info['company']:'',
                'contact'=>$info['contact'],
                'address'=>$info['address'],
                'introduction'=>$info['introduction'],
                'is_show'=>$info['is_show'],
                'count'=>$num,
            );
            
            if($member['is_show']==2)
            {
               //判断该用户是否为合作用户
               $condition = array('member_id'=>$this->member_id,'receive_member'=>$member_id);
               $count = M('stu_apply')->where($condition)->count();
               if($count && $count>0)
               {
                   $show = 1;
               } 
            }
            
            //会员内容
            $html = '';
            $html.='<table width="100%"><tbody>';
            $html.='<tr><td width="50%" height="25px">类&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;别：<strong>'.$member['type'].'</strong></td>
                    <td width="50%" height="25px">用&nbsp; 户&nbsp; 名：<strong>'.$member['username'].'</strong></td></tr>';
            $html.='<tr><td width="50%" height="25px">地&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;区： '.$member['area'].'</td>
                    <td width="50%" height="25px">合作院校：<strong><font color="#FF0000">'.$member['count'].'</font></strong>所</td></tr>';
            
            if($member['is_show']==0 )
            {
                $html.='<tr><td width="50%" height="40px"colspan="2" align="center"><strong>该用户未对您开放其他相关信息</strong></td></tr>';
            }
            elseif($member['is_show']==2 && $show==1) 
            {
                if($member['member_type']==1) //企业
                {
                    $html.='<tr><td width="50%" height="25px" colspan="2">企业名称：'.$member['company'].'</td</tr>';
                }
                $html.='<tr><td width="50%" height="25px" colspan="2">详细地址：'.$member['address'].'</td</tr>';
                $html.='<tr><td colspan="2"><p>介&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;绍：</p>
                        <p style="font-size:12px;">'.$member['introduction'].'</p></td></tr>';
            }
            elseif($member['is_show']==1)
            {
                if($member['member_type']==1) //企业
                {
                    $html.='<tr><td width="50%" height="25px" colspan="2">企业名称：'.$member['company'].'</td</tr>';
                }
                $html.='<tr><td width="50%" height="25px" colspan="2">详细地址：'.$member['address'].'</td</tr>';
                $html.='<tr><td colspan="2"><p>介&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;绍：</p>
                        <p style="font-size:12px;">'.$member['introduction'].'</p></td></tr>';
            }
           
            $html.='</tbody></table>';
            $html.='<p class="send_zhongj"><a href="'.U('Home/Letter/sentLetter',array('member_id'=>$member_id)).'">发消息给他</a></p>';
            
            $this->ajaxReturn(array('status'=>1,'member_info'=>$html));
            exit();
        }
    }
    
    
}


?>