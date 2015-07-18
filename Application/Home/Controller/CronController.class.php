<?php
namespace Home\Controller;
use Think\Controller;
class CronController extends Controller {

	function index(){
	}

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
}