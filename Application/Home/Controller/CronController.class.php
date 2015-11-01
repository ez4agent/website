<?php
namespace Home\Controller;
use Think\Controller;
class CronController extends Controller {

	function index(){
	}
/*
    function import(){

        $path = isset($_GET['path']) ? $_GET['path'] : '';
        if(!$path){
            exit;
        }

        $file = ROOT_PATH."Public/xls/".$path;
        //liujinlong.xlsx
        if(!is_file($file)){
            exit('no file : '.$file);
        }

        $username = pathinfo($file,PATHINFO_FILENAME);

        $username = trim($username,'_a');
        $username = trim($username,'_x');

        $user = M('member')->where(array('username'=>$username))->find();
        if(!$user){
            exit('no user found');
        }

		$exceArray = $this->readExcel($file);
        foreach($exceArray as $school){
            if(!$this->add_partner($user['member_id'],$school)){
                echo "faild",PHP_EOL;
            }
        }

        echo "done",PHP_EOL;
    }

	function readExcel($path){

        import("Common.Util.PHPExcel");        
        import("Common.Util.PHPExcel.IOFactory");

        $ext = pathinfo($path,PATHINFO_EXTENSION);
        if($ext == 'xls'){
            $reader = \PHPExcel_IOFactory::createReader('Excel5');
        }else{
            $reader = \PHPExcel_IOFactory::createReader('Excel2007'); //设置以Excel5格式(Excel97-2003工作簿)
        }

        $PHPExcel = $reader->load($path); // 载入excel文件
        $sheet = $PHPExcel->getSheet(0); // 读取第一個工作表
        $highestRow = $sheet->getHighestRow(); // 取得总行数
        $highestColumm = $sheet->getHighestColumn(); // 取得总列数

        $group = array();
        $tmp = '';
        for ($row = 9; $row <= $highestRow; $row++){//行数是以第1行开始

            $index = 'A'.$row;
            $v = trim($sheet->getCell($index)->getValue());

            if(!$v && $tmp) {
                $group[$tmp]['list'][] = $row;
            }elseif($v == '备注' && $tmp){
                $group[$tmp]['desc'] = $row;
                $tmp = '';
            }else{
                $group[$index] = array();
                $group[$index]['name'] = $v;
                $group[$index]['desc'] = '';
                $group[$index]['list'] = array();
                $group[$index]['list'][] = $row;
                $tmp = $index;
            }
        }

        $education = C('Education_TYPE');

        $mapping = array(
            'B' => 'education',
            'C' => 'pay_type',
            'D' => 'share_ratio',
            'E' => 'share_length',
            'H' => 'set_price',
            'I' => 'pay_cycle'
        );

        $pay_type = array(
            '1'=>'按学年',
            '2'=>'按学期',
            '3'=>'按课程长度'
        );

        foreach($group as $i=>$school){
            if(empty($school['name']) || $school['name'] == '为大学提供语言，预科和国际大一课程的院校'){
                unset($group[$i]);
                continue;
            }

            $college_info= M('college')->where(array('ename'=>$school['name']))->find();
            if(empty($college_info)){
                echo "no college found : ".$school['name'],PHP_EOL;
                unset($group[$i]);
                continue;
            }

            $school['college_id'] = $college_info['college_id'];

            if(empty($school['list'])){
                continue;
            }

            $newlist = array();
            foreach($school['list'] as $k=>$num){
                $item = array();

                for ($column = 'B'; $column <= $highestColumm; $column++) {

                    if(!isset($mapping[$column])){
                        continue;
                    }

                    $key = $mapping[$column];
                    $v = $sheet->getCell($column.$num)->getValue();
                    if($key == 'education'){
                        $item['education'] = $v;
                        $item['apply_id'] = array_search($v,$education);
                    }elseif($key == 'pay_type'){
                        $item[$key] = array_search($v,$pay_type);
                    }elseif($key == 'share_ratio'){
                        $item[$key] = sprintf('%d',(float)$v * 100);
                    }elseif($key == 'share_length'){
                        $item[$key] = (int)$v;
                    }elseif($key == 'set_price'){
                        $v = trim($v,'$');
                        $v = trim($v,'澳币');
                        $item[$key] = (float)$v;
                    }else{
                        $item[$key] = $v;
                    }
                }

                $newlist[] = $item;
            }

            $school['list'] = $newlist;


            $group[$i] = $school;

        }

        return $group;
	}

    function add_partner($member_id,$school){

        $partner_info = M('partner_college')->where(array('member_id'=>$member_id,'college_id'=>$school['college_id']))->find();

        if($partner_info){
            echo "has partner",PHP_EOL;
            return false;
        }

        $insert = array(
            'member_id' => $member_id,
            'college_id' => $school['college_id'],
            'addtime' => time()
        );

        $partner_id = M('partner_college')->add($insert);
        if(!$partner_id){
            return false;
        }

        $update_arr = array();
        foreach($school['list'] as $data){
            if(!$data['education'] || !$data['share_ratio'] || !$data['pay_type'] || !$data['share_length']){
                continue;
            }
            $data['member_id'] = $member_id;
            $data['college_id'] = $school['college_id'];
            $data['partner_id'] = $partner_id;
            $update_arr[] = $data;
        }

        $model = M('partner_college_commission');
        if($model->addAll($update_arr,array(),true)){
            return true;
        }

        return false;

    }
*/


    function school(){
        //$path = isset($_GET['path']) ? $_GET['path'] : '';
        //if(!$path){
        //    exit;
        //}
        $path = 'school.xlsx';
        $file = ROOT_PATH."Public/xls/".$path;
        //liujinlong.xlsx
        if(!is_file($file)){
            exit('no file : '.$file);
        }

        import("Common.Util.PHPExcel");
        import("Common.Util.PHPExcel.IOFactory");

        $ext = pathinfo($file,PATHINFO_EXTENSION);
        if($ext == 'xls'){
            $reader = \PHPExcel_IOFactory::createReader('Excel5');
        }else{
            $reader = \PHPExcel_IOFactory::createReader('Excel2007'); //设置以Excel5格式(Excel97-2003工作簿)
        }

        $PHPExcel = $reader->load($file); // 载入excel文件
        $sheet = $PHPExcel->getSheet(0); // 读取第一個工作表
        $highestRow = $sheet->getHighestRow(); // 取得总行数
        $highestColumm = $sheet->getHighestColumn(); // 取得总列数

        $group = array();

        for ($row = 2; $row <= $highestRow; $row++){//行数是以第1行开始

            $tmp = array();
            for ($column = 'A'; $column <= $highestColumm; $column++) {

                $index = $column.$row;
                $v = trim($sheet->getCell($index)->getValue());

                if(empty($v)){
                   continue;
                }

                if($column == 'A'){
                    $tmp['country'] = (string)$v;
                }elseif($column == 'D'){
                    $tmp['cn_name'] = (string)$v;
                }elseif($column == 'E'){
                    $tmp['en_name'] = (string)$v;
                }elseif($column == 'F'){
                    $tmp['type'] = $v == '公立' ? 5 : 6;
                }elseif($column == 'G'){
                    $tmp['education'] = explode('，',$v);
                }elseif($column == 'I'){
                    $tmp['website'] = (string)$v;
                }
            }

            $group[] =$tmp;
        }

        $education = C('Education_TYPE');

        $member_id = C('SYSTEM_PARTNER_MEMBER');

        $country = array();
        foreach($group as $r){

            $college_info= M('college')->where(array('ename'=>$r['en_name']))->find();
            if(!empty($college_info)){
                echo "college exist : ".$r['en_name'],PHP_EOL;
                continue;
            }

            if(!isset($country[$r['country']])){
                $country_info= M('country')->where(array('cname'=>$r['country']))->find();
                if(!$country_info){
                    echo "no country found : ".$r['country'],PHP_EOL;
                    continue;
                }

                $country[$r['country']] =$country_info['countryid'];
            }

            $college_info = array(
                'cname'=> $r['cn_name'] ? $r['cn_name'] : '',
                'ename'=> $r['en_name'],
                'country_id'=> $country[$r['country']],
                'area_id'=> 0,
                'city_id'=> 0,
                'apply_price'=> 0,
                'schoolbadge'=> '',
                'introduction'=> '',
                'website'=> $r['website'] ? $r['website'] : ''
            );
            $college_id = M('college')->add($college_info);
            //echo M('college')->getLastSql();
            //exit;
            if(!$college_id){
                echo "add college faild : ".$r['en_name'],PHP_EOL;
                continue;
            }

            $partner_info = M('partner_college')->where(array('member_id'=>$member_id,'college_id'=>$college_id))->find();
            if($partner_info){
                echo "has partner",PHP_EOL;
                return false;
            }

            $this->add_type($college_id,$r['type']);
            $this->add_partner($member_id,$college_id);

            foreach ($r['education'] as $str) {

                $id = array_search($str,$education);
                if($id !== false){
                    $this->add_education($college_id,$id);
                }

            }
        }
    }

    function add_partner($member_id,$college_id){
        $insert = array(
            'member_id' => $member_id,
            'college_id' => $college_id,
            'addtime' => time()
        );

        return M('partner_college')->add($insert);
    }

    function add_type($college_id,$type){

        $insert = array(
            'type' => $type,
            'college_id' => $college_id
        );

        return M('college_type')->add($insert);
    }

    function add_education($college_id,$education){

        $insert = array(
            'education' => $education,
            'college_id' => $college_id
        );

        return M('college_education')->add($insert);
    }


    function commision(){
        $path = 'commision.xlsx';
        $file = ROOT_PATH."Public/xls/".$path;
        //liujinlong.xlsx
        if(!is_file($file)){
            exit('no file : '.$file);
        }

        import("Common.Util.PHPExcel");
        import("Common.Util.PHPExcel.IOFactory");

        $ext = pathinfo($file,PATHINFO_EXTENSION);
        if($ext == 'xls'){
            $reader = \PHPExcel_IOFactory::createReader('Excel5');
        }else{
            $reader = \PHPExcel_IOFactory::createReader('Excel2007'); //设置以Excel5格式(Excel97-2003工作簿)
        }

        $PHPExcel = $reader->load($file); // 载入excel文件
        $sheet = $PHPExcel->getSheet(0); // 读取第一個工作表
        $highestRow = $sheet->getHighestRow(); // 取得总行数
        $highestColumm = $sheet->getHighestColumn(); // 取得总列数

        $group = array();

        $education = C('Education_TYPE');

        for ($row = 2; $row <= $highestRow; $row++){//行数是以第1行开始

            $tmp = array();
            for ($column = 'A'; $column <= $highestColumm; $column++) {

                $index = $column.$row;
                $v = trim($sheet->getCell($index)->getValue());

                if(empty($v)){
                    //continue;
                }

                if($column == 'A'){
                    $tmp['college'] = (string)$v;
                }elseif($column == 'B') {

                    $id = array_search($v,$education);
                    if($id !== false){
                        $tmp['education'] = $id;
                    }else{
                        $tmp['education'] = 0;
                    }

                }elseif($column == 'C'){
                    $tmp['rule_id'] = (string)$v;
                }elseif($column == 'D'){
                    $tmp['pay_length'] = (string)$v;
                }elseif($column == 'E'){
                    $tmp['commision_type'] = $v == '比例' ? 1 : 2;
                }elseif($column == 'F'){
                    $tmp['apply_min'] = (int)$v;
                }elseif($column == 'G'){
                    $tmp['apply_max'] = (int)$v;
                }elseif($column == 'H'){
                    $tmp['enroll_time_start'] = '2016-01-01';
                }elseif($column == 'I'){
                    $tmp['enroll_time_end'] = '9999-12-31';
                }elseif($column == 'J'){
                    $tmp['first_pay'] = $tmp['commision_type'] == 1 && $v > 0 ? $v * 100 : 0;
                }elseif($column == 'K'){
                    $tmp['first_service_price'] = $tmp['commision_type'] == 1 && $v > 0 ? $v * 100 : 0;;
                }elseif($column == 'L'){
                    $tmp['after_pay'] = $tmp['commision_type'] == 1 && $v > 0 ? $v * 100 : 0;;
                }elseif($column == 'M'){
                    $tmp['after_service_price'] = 0;
                }elseif($column == 'N'){

                    if(!$v){
                        $tmp['ext_price'] = 0;
                        $tmp['ext_price_unit'] = '$';
                    }elseif(strpos($v,'NZD') !== false){
                        $tmp['ext_price'] = trim($v,'NZD');
                        $tmp['ext_price_unit'] = 'NZD';
                    }else{
                        $tmp['ext_price'] = $v;
                        $tmp['ext_price_unit'] = '$';
                    }
                }
            }

            $group[] =$tmp;
        }



        $member_id = C('SYSTEM_PARTNER_MEMBER');

        $country = array();
        foreach($group as $r) {

            $college_info = M('college')->where(array('ename' => $r['college']))->find();
            if (empty($college_info)) {
                echo "college not exist : " . $r['en_name'], PHP_EOL;
                continue;
            }

            unset($r['college']);
            $r['college_id'] = $college_info['college_id'];

            if(!M('college_commision')->add($r)){
                echo "add college_commision faild : ".$r['college'],PHP_EOL;
                continue;
            }
        }

        echo 'done';
    }
}