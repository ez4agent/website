<?php


if (!function_exists('cal_days_in_month'))
{
    function cal_days_in_month($calendar, $month, $year)
    {
        return date('t', mktime(0, 0, 0, $month, 1, $year));
    }
} 

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
            if(!isset($_edu[$val['education']])){
                continue;
            }
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

function send_email($to,$title,$content,$file=array(),$type = 'info')
{
    //header('Content-type:text/html;charset=utf-8');
    import("Common.Util.PHPMailer");
    //Vendor('PHPMailer.PHPMailerAutoload');
    $mail_config = C('EMAIL');
    $config = $mail_config[$type];

    try {
        $mail=new \PHPMailer(true);
        // 设置PHPMailer使用SMTP服务器发送Email
        $mail->IsSMTP();
        $mail->CharSet = 'utf-8';
        //Enable SMTP debugging
        // 0 = off (for production use)
        // 1 = client messages
        // 2 = client and server messages
        $mail->SMTPDebug = 0;
        //Ask for HTML-friendly debug output
        //$mail->Debugoutput = 'html';

        $mail->Host = $config['HOST'];
        $mail->Port = 25;
        $mail->SMTPAuth = true;
        $mail->Username = $config['USER'];
        $mail->Password = $config['PASSWORD'];//"EZ4agent1";

        $mail->setFrom($config['FROM'], $config['FROMNAME']);
        $mail->AddAddress($to);

        // 设置邮件标题
        $mail->Subject = $title;
        $mail->Body = $content;

        if (!empty($file)) {
            foreach ($file as $key => $val) {
                $houzui = substr(strrchr($val['file_path'], '.'), 1);
                $mail->AddAttachment('./Uploads' . $val['file_path'], $val['file_name'] . '.' . $houzui);
            }
        }

        $mail->IsHTML(true);

        // 发送邮件。
        $mail->Send();
    } catch (\phpmailerException $e) {
        //echo $e->errorMessage();
        return false;
    } catch (\Exception $e) {
        //echo $e->getMessage();
        return false;
    }

    return true;
}



function authcode($string, $operation = 'DECODE', $key = '', $expiry = 3600) {

    $ckey_length = 4;
    // 随机密钥长度 取值 0-32;
    // 加入随机密钥，可以令密文无任何规律，即便是原文和密钥完全相同，加密结果也会每次不同，增大破解难度。
    // 取值越大，密文变动规律越大，密文变化 = 16 的 $ckey_length 次方
    // 当此值为 0 时，则不产生随机密钥

    $key = md5($key ? $key : 'default_key'); //这里可以填写默认key值
    $keya = md5(substr($key, 0, 16));
    $keyb = md5(substr($key, 16, 16));
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

    $cryptkey = $keya.md5($keya.$keyc);
    $key_length = strlen($cryptkey);

    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
    $string_length = strlen($string);

    $result = '';
    $box = range(0, 255);

    $rndkey = array();
    for($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }

    for($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }

    for($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }

    if($operation == 'DECODE') {
        if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
            return substr($result, 26);
        } else {
            return '';
        }
    } else {
        return $keyc.str_replace('=', '', base64_encode($result));
    }

}

function generate_key($size,$number_only=false){
    if($number_only){
        $alphabet = '0123456789';
    }else{
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    }

    $l = strlen($alphabet)-1;
    $key = '';
    for ($i=0; $i<$size; $i++){
        $key.= $alphabet[mt_rand(0, $l)];
    }
    return $key;
}


function get_hl(){
    $url = 'http://qq.ip138.com/hl.asp?from=AUD&to=CNY&q=1';
    $ch = curl_init();
    $timeout = 2;

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1)");
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    $htmldata = curl_exec($ch);
    curl_close($ch);

    $content_start = "<tr bgcolor=#ffffff align=center><td>1<\/td>";
    $content_end = "<\/tr><\/table><\/tr><\/table><br\/><br\/>";

    preg_match("/(" . $content_start . ")(.*?)(" . $content_end . ")/is", $htmldata, $m);
    preg_match("/<td>(.*?)<\/td>/is", $m[2], $td);

    if(isset($td[1])){
        return $td[1];
    }

    return false;
}