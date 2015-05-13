<?php

/**
 *  获取某个字段值 
 */
function getField_value($table,$field,$where=array())
{ 
    $value = M($table)->where($where)->getField($field);
    return $value;
}

/**
 *  获取国家列表 
 */
function country($where='')
{
    $country1 = array();
    $country =M('country')->where($where)->select();
    foreach($country as $key=>$val)
    {
        $country1[$key]['countryid'] = $val['countryid'];
        if($val['countryid']==1)
        {
            $country1[$key]['name'] = $val['cname'];
        }
        else 
        {
            $country1[$key]['name']=$val['ename'];
        }
    }
    return $country1;
}

/**
 *  根据国家ID 获取州数据
 *  @param $countryid
 *  @return array 
 */
function area_list($countryid)
{
    $area1 = array();
    $map = array('countryid'=>$countryid);
    $area = M('area')->where($map)->field('aid,countryid,cname,ename')->select();
    if($area)
    {
        foreach($area as $key=>$val)
        {
            $area1[$key]['aid'] = $val['aid'];
            if($val['countryid']==1)
            {
                $area1[$key]['name']=$val['cname'];
            }
            else 
            {
                $area1[$key]['name']=$val['ename'];
            }
        }
    }
    return $area1;
}

/**
 *  根据国家ID 获取州数据
 *  @param $countryid
 *  @return array
 */
function city_list($pid)
{
    $area = array();
    $map = array('area_id'=>$pid);
    $area = M('city')->where($map)->field('cid,countryid,cname,ename')->select();
    if($area)
    {
        foreach($area as $key=>$val)
        {
            $area1[$key]['cid'] = $val['cid'];
            if($val['countryid']==1)
            {
                $area1[$key]['name']=$val['cname'];
            }
            else
            {
                $area1[$key]['name']=$val['ename'];
            }
        }
    }
    return $area1;
}

/**
 *  获取国家地区，城市 名称 
 */
function get_namebyarea($country,$area,$city)
{
    if($country!=1)
    {
        $array['country_name'] = M('country')->where('countryid='.$country)->getField('ename');
        $array['area_name']=M('area')->where('aid='.$area)->getField('ename');
        $array['city_name']=M('city')->where('cid='.$city)->getField('ename');
    }
    else 
    {
        $array['country_name'] = M('country')->where('countryid='.$country)->getField('cname');
        $array['area_name']=M('area')->where('aid='.$area)->getField('cname');
        $array['city_name']=M('city')->where('cid='.$city)->getField('cname');
    }
    return $array;
    
}

/**
 *  根据城市ID 获取院校数据
 *  @param $city_id
 *  @return array 
 */
function get_college_bycity($cityid)
{
    $college = array();
    $map = array('city_id'=>$cityid);
    $college = M('college')->where($map)->field('college_id,cname')->select();
    return $college;
}

/**
 *  按学校ID 获取相应的学历 
 */
function get_educationbycollege_id($college_id)
{
    $_edu = C('Education_TYPE');
    $edu = M('college_education')->field('education')->where('college_id='.$college_id)->select();
    if($edu)
    {
        foreach ($edu as $key=>$val)
        {
            $edu1[]=array(
                'id'=>$val['education'],
                'name'=>$_edu[$val['education']],
            );
        }
    }
    return $edu1;
}
/**
 *  用于字段重名验证
 *  @param string $table,string $field,string $value
 *  @return true; 
 */
function checkfield($table,$field,$value)
{
    $count = M($table)->where(array($field=>$value))->count();
    if($count)
    {
        return false;
    }
    return true;
}

/**
 *  获取所有模块Service 
 *  @param string $name 指定Service名
 *  @return ServiceList
 */
function getAllService($name,$method,$vars=array())
{
    if(empty($name)) return null;
    $apiPath = APP_PATH.'*/Service/'.$name.'Service.class.php';
    $apiList = glob($apiPath);
    if(empty($apiList)){
        return ;
    }
    
    $appPathStr = strlen(APP_PATH);
    $method = 'get'.$method.$name;
    $data = array();
    foreach ($apiList as $value)
    {
        $path = substr($value, $appPathStr,-10);
        $path = str_replace('\\', '/',  $path);
        $AppName = explode('/', $path);
        $AppName = $AppName[0];
        $class = A($AppName.'/'.$name,'Service');
        if(method_exists($class,$method)){
            $data[$AppName] = $class->$method($vars);
        }
    }
    
    return $data;
}

/**
 *  获取指定模块Service
 *  @param string $name 指定Service名
 *  @return Service
 */
function service($AppName,$name,$method,$vars=array())
{
    $class = A($AppName.'/'.$name,'Service');
    if(method_exists($class, $method))
    {
        return $class->$method($vars);
    }
}


/**
 * 调用指定模块的API
 * @param string $api
 * @return Api
 */
function api($module,$api,$method,$vars=array())
{
    return A("$module/$api","Api")->$method($vars);
}

/**
 *  二维数组排序 
 */
function array_order($array,$key,$type='asc',$reset = false)
{
    if (empty($array) || !is_array($array)) {
        return $array;
    }
    foreach ($array as $k => $v) {
        $keysvalue[$k] = $v[$key];
    }
    if ($type == 'asc') {
        asort($keysvalue);
    } else {
        arsort($keysvalue);
    }
    $i = 0;
    foreach ($keysvalue as $k => $v) {
        $i++;
        if ($reset) {
            $new_array[$k] = $array[$k];
        } else {
            $new_array[$i] = $array[$k];
        }
    }
    return $new_array;
}

//检查路径
function make_dir($folder)
{
    $reval = false;
    if (!file_exists($folder))
    {
        @umask(0);
        preg_match_all('/([^\/]*)\/?/i', $folder, $atmp);
        $base = ($atmp[0][0] == '/') ? '/' : '';
        foreach ($atmp[1] AS $val)
        {
            if ('' != $val)
            {
                $base .= $val;

                if ('..' == $val || '.' == $val)
                {
                    $base .= '/';
                    continue;
                }
            }
            else
            {
                continue;
            }
            $base .= '/';

            if (!file_exists($base))
            {
                if (@mkdir(rtrim($base, '/'), 0777))
                {
                    @chmod($base, 0777);
                    $reval = true;
                }
            }
        }
    }
    else
    {
        $reval = is_dir($folder);
    }
    clearstatcache();
    return $reval;
}

/**
 * 字符串转布尔
 * @param string $directoty 路径
 * @return bool
 */
function string_to_bool($val)
{
    switch ($val) {
        case 'true':
            return true;
            break;
        case 'false':
            return false;
            break;

        default:
            return $val;
            break;
    }
}

/**
 * 数据签名认证
 * @param  array  $data 被认证的数据
 * @return string       签名
 */
function data_auth_sign($data) {
    //数据类型检测
    if(!is_array($data)){
        $data = (array)$data;
    }
    ksort($data); //排序
    $code = http_build_query($data); //url编码并生成query字符串
    $sign = sha1($code); //生成签名
    return $sign;
}


/**
 * 写入配置文件
 * @param  array $config 配置信息
 */
function write_config($file, $config){
    if(is_array($config)){
        //读取配置内容
        $conf = file_get_contents($file);
        //替换配置项
        foreach ($config as $key => $value) {
            if (is_string($value) && !in_array($value, array('true','false'))){
                if (!is_numeric($value)) {
                    $value = "'" . $value . "'"; //如果是字符串，加上单引号
                }
            }
            $conf = preg_replace("/'" . $key . "'\s*=\>\s*(.*?),/iU", "'".$key."'=>".$value.",", $conf);
        }
        //写入应用配置文件
        if(!IS_WRITE){
            return false;
        }else{
            if(file_put_contents($file, $conf)){
                return true;
            } else {
                return false;
            }
            return '';
        }

    }
}

//html代码输入
function html_in($str){
    $str=htmlspecialchars($str);
    if(!get_magic_quotes_gpc()) {
        $str = addslashes($str);
    }
    return $str;
}

//html代码输出
function html_out($str){
    if(function_exists('htmlspecialchars_decode')){
        $str=htmlspecialchars_decode($str);
    }else{
        $str=html_entity_decode($str);
    }
    $str = stripslashes($str);
    return $str;
}


/**
 *  发邮件
 *  string $to      收件人
 *  string $content 正文
 *  string $title   邮件标题
 *  array  $file    附件
 *  return boolean
 */
function send_email($to,$title,$content,$file=array())
{
    header('Content-type:text/html;charset=utf-8');
    vendor("PHPMailer.class#phpmailer");
    
    $config = C('EMAIL');
    $mail=new \PHPMailer();
    
    // 设置PHPMailer使用SMTP服务器发送Email
    $mail->IsSMTP();
    $mail->SMTPSecure = "ssl";    				//打开SSL
    $mail->SMTPDebug  = 1;                     // 启用SMTP调试功能
    // 设置邮件的字符编码，若不指定，则为'UTF-8'
    $mail->CharSet='UTF-8';
     
    // 添加收件人地址，可以多次使用来添加多个收件人
    $mail->AddAddress($to);

    // 设置邮件正文
    $mail->Body=$content;
     
    // 设置邮件头的From字段。
    $mail->From=$config['FROM'];
     
    // 设置发件人名字
    $mail->FromName=$config['FROMNAME'];
     
    // 设置邮件标题
    $mail->Subject=$title;
     
    // 设置SMTP服务器。
    $mail->Host=$config['HOST'];
    $mail->Port= $config['PORT'];
    // 设置为"需要验证"
    $mail->SMTPAuth=true;
     
    // 设置用户名和密码。
    $mail->Username=$config['USER'];
    $mail->Password=$config['PASSWORD'];
    
    //如果附件
    if(!empty($file))
    { 
        foreach($file as $key=>$val)
        {
            $houzui = substr(strrchr($val['file_path'], '.'), 1);
            $mail->AddAttachment('./Uploads'.$val['file_path'],$val['file_name'].'.'.$houzui);
        }
    }
    
    // 发送邮件。
    if($mail->Send()){
       return true;
    }else{
       return false;
    }
}



