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
        $this->member_id = session('member_id');
    }
    
    /**
     *  查询条件 
     */
    public function _query_condition()
    {
        //查询数据库的筛选条件
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
                $html = '';
                foreach($college_id as $key=>$val)
                {
                    $html.=$val['college_id'].',';
                }
            }
            $where['condition']['college_id']=array('in',$html);
        }
        
        //关键字
        $keyword=I('param.keywords','','trim');
        if(!empty($keyword))
        {
            $where['condition']['_string'] = ' (cname like "%'.$keyword.'%")  OR ( ename like "%'.$keyword.'%") ';
        }
        
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
            $this->assign('education',$this->school_mod->get_CollegeType_Attribute($where));
        }
        else 
        {
            $edu = C('Education_TYPE');
            $select['education']=array('type'=>'education','name'=>$edu[$where['education']]);
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

        $this->assign('add',$is_partner?1:0);
        $this->assign('info',$this->school_mod->get_college_info($id));
        $this->assign('partner',$this->get_partner_info($this->member_id));
        $this->assign('view',$this->get_view_college($this->member_id));
        $this->assign('apply',$this->get_apply_college($this->member_id));
        $this->assign('edu_info',$edu_info);
        $this->assign('share',!empty($share)?$share:0);
        $this->assign('stu_id',$stu_id);
        $this->assign('flag',I('get.flag','','trim'));
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
           $map = array('college_id'=>$college_id,'is_share'=>1,'apply_id'=>$select);
           $total = M('partner_college_commission')->where($map)->count();
           //获取commission_id
           $array = $this->partner_mod->get_share_college($college_id, $select,$page,$pagesize);
          
           $str ="<table width='100%'>
                    <thead>
                         <tr>
                            <th width='15%' height='25px'><strong>中介</strong></th>
                            <th width='15%' height='25px'><strong>学历</strong></th>
                            <th width='40%' height='25px' style='padding:0px;'>
                            <div style='width:100%; height:20px; border-bottom:1px #eee solid; padding:5px 0 3px 0;'>
                            <span><strong>分享比例</strong></span></div>
                            <table width='100%' style='border:1px #eee solid'>
                            <tr>
                            <th width='25%' height='20px'><strong>第1学年</strong></th>
                            <th width='25%' height='20px'><strong>第2学年</strong></th>
                            <th width='25%' height='20px'><strong>第3学年</strong></th>
                            <th width='25%' height='20px'><strong>第4学年</strong></th>
                            </tr>
                            </table>
                            </th>
                            <th width='20%' height='25px'><strong>支付方式/周期</strong></th>
                            <th width='20%' height='25px'><strong>操  作</strong></th>
                            </tr>
                         </thead>
                         <tbody>";
           if(empty($array))
           {
               if($stu_id!=0)
               {
                   $str.="<tr><td colspan='5' height='30px' align='center'><strong>无分享信息,请点
                          <input id='upload_info' class='updatabtn' type='button' value='帮助' onClick='college_help(".$college_id.");' />
                          &nbsp;&nbsp;<a href='".U('Home/Student/index',array('stu'=>$stu_id))."'><input class='updatabtn' type='button' value='返回'/></a> 
                          </strong></td></tr>";
               }
               else 
               {
                   $str.="<tr><td colspan='5' height='30px' align='center'><strong>无分享信息,请点
                          <input id='upload_info' class='updatabtn' type='button' value='帮助' onClick='college_help(".$college_id.");' />
                          &nbsp;&nbsp;<a href='".U('Home/Student/index')."'><input class='updatabtn' type='button' value='返回'/></a>
                          </strong></td></tr>";
               }    
           }
           else 
           {
              foreach($array as $key=>$val)
              {
                  $str.="<tr>
                             <td width='15%' height='30px'><strong><a href='javascript:void(0);' onclick='view_member(".$val['member_id'].");'>".$val['username']."</a></strong></td>
                             <td width='15%' height='30px'><strong>".$val['education']."</strong></td>
                             <td width='40%' height='30px' style='padding:0px;'><table width='100%'><tr>";
                  
                  for($i=1;$i<5;$i++)
                  {
                        $str.="<th width='25%' height='35px;' style='padding:0px;'>".$array[$val['commission_id']]['value1'][$i]['sharing_ratio1']."</th>";  
                  }
                  $str.=" </tr></table></td><td width='20%' height='30px'>";
                  if($val['pay_type']&& $val['pay_cycle'])
                  {
                     $str.="<strong>".$val['type_name'].' / '.$val['pay_cycle'].$val['unit']."</strong>";
                  }
                  else
                  {
                      $str.="<strong>--</strong>";
                  }  
                  $str.="</td>";
                  $str.="<td width='20%'><input id='upload_info' class='updatabtn' type='button' value='申请'
                    onClick='college_apply_header(".$college_id.",".$val['commission_id'].");'
                  /></td>";
                  $str.="</tr>";
              } 
           }
           $str.="</tboby></table>";
           $this->ajaxReturn(array('status'=>'yes','str'=>$str,'total'=>$total));
        }
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
        
        $college_info = D('School')->get_college_info($college_id);
        //拥金信息
        $commission =$this->partner_mod->get_partner_list('partner_college_commission',
                     array('member_id'=>$this->member_id,'partner_id'=>$parter_id));
        if(!empty($commission))
        {
            //获取分享信息
            foreach ($commission as $key=>$val)
            {
                if($val['is_share']==1)
                {
                    $commission[$key]['share_value'] = $this->partner_mod->get_partner_list('share_value',array(
                        'commission_id'=>$val['commission_id'],
                    ));
                }
            }
        } 
        
        $this->assign('partner',$this->get_partner_info($this->member_id));
        $this->assign('view',$this->get_view_college($this->member_id));
        $this->assign('apply',$this->get_apply_college($this->member_id));
        $this->assign('info',$college_info);
        $this->assign('commission',$commission);
        $this->assign('parter_id',$parter_id);
        $this->assign('paytype',C('pay_type'));
        $this->display('partnerinfo');
    }
    
    //更新佣金
    public function edit_value()
    {
        if(IS_AJAX)
        {
            $data = $_POST;
            
            //先还原
            $college_id = I('get.college_id','0','intval');
            M('share_value')->where(array('college_id'=>$college_id,'member_id'=>$this->member_id))->setField('sharing_ratio',0);
            
            if(!empty($data['sharing_ratio']))
            {
                //首先把没填的数据清掉
                foreach($data['sharing_ratio'] as $key=>$val)
                {
                    if(!$val['pay_type'] && !$val['pay_cycle'])
                    {
                        unset($data['sharing_ratio'][$key]);
                    }
                }   
            }
            
            if(!empty($data['value']))
            {
                foreach($data['value'] as $key1=>$val1)
                {
                    foreach ($val1 as $key2=>$val3)
                    {
                        if(empty($val3['sharing_ratio']))
                        {
                            unset($data['value'][$key1][$key2]);
                        }
                    }
                }
                
                foreach($data['value'] as $key1=>$val1)
                {
                    if(empty($val1))
                    {
                        unset($data['value'][$key1]);
                    }
                    else 
                    {
                        if($data['sharing_ratio'][$key1])
                        {
                            //更新佣金比例
                            foreach($val1 as $key4=>$val4)
                            {
                                if($val4['sharing_ratio'] && floatval($val4['sharing_ratio']))
                                {
                                   //验证（1-99，递减或者递增0.5）
                                   if(!preg_match('/^[0-9]{0,2}+(\.5)*$/',$val4['sharing_ratio']))
                                   {
                                       $this->ajaxReturn(array('status'=>'no','msg'=>'分享比例必须在1-99之间,可以有0.5增减,且一位小数'));
                                       exit;
                                   }
                                   else 
                                   {
                                       if(intval($data['sharing_ratio'][$key1]['pay_type'])!=0 && intval($data['sharing_ratio'][$key1]['pay_cycle'])!=0)
                                       {
                                           M('share_value')->where('value_id='.$key4)->save($val4);
                                           if($data['sharing_ratio'][$key1]['pay_cycle']>1 
                                               && $data['sharing_ratio'][$key1]['pay_cycle']<52
                                               && preg_match('/^[0-9]*$/', $data['sharing_ratio'][$key1]['pay_cycle'])
                                           )
                                           {
                                               M('partner_college_commission')->where('commission_id='.$key1)
                                                                               ->save($data['sharing_ratio'][$key1]);
                                           }
                                           else 
                                           {
                                               $this->ajaxReturn(array('status'=>'no','msg'=>'周期必须在1-52的整数'));
                                               exit;
                                           }
                                       }
                                       else 
                                       {
                                           $this->ajaxReturn(array('status'=>'no','msg'=>'请填写正确的分享信息！'));
                                           exit;
                                       }
                                   } 
                                }
                                else 
                                {
                                    $this->ajaxReturn(array('status'=>'no','msg'=>'请填写正确的分享信息！'));
                                    exit;
                                }
                            }

                        }
                        else 
                        {
                            $this->ajaxReturn(array('status'=>'no','msg'=>'分享信息请填写完整！'));
                            exit;
                        }
                    }
                }
            }
            $this->ajaxReturn(array('status'=>'ok'));
            exit;

        }
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