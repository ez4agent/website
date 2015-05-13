<?php
/**
 *  前台公用上传模型 
 */

namespace Home\Model;
use Think\Model;
use Think\Think;

class UploadModel extends Model
{
    /**
     *  文件上传
     *  @param string type string upload_path
     *  @retrun array
     */
    public function file_upload($type,$upload_path)
    {
        $result= array('error'=>0,'info'=>'','message'=>'');
        //图片上传路径
        $php_path = './';
        $php_url = __ROOT__. '/';
        
        $ext_arr = array(
            'image' => array('gif','jpg','jpeg','png','bmp'),
            'flash' => array('swf','flv'),
            'media' => array('swf','flv','mp3','wav','wma','wmv','mid','avi','mpg','asf','rm','rmvb'),
            'file'  => array('gif','jpg','jpeg','png','doc','docx','xls','xlsx','pdf'),
            'file1' => array('gif','jpg','jpeg','png','pdf'),
        );
        
        if(empty($ext_arr[$type]))
        {
            $result = array('error'=>1,'message'=>'未知错误');
            return $result;
            exit();
        }
        
        $php_path .= $upload_path."/".$type."/";
        $php_url .= $upload_path."/".$type."/";
        
        $upload = new \Think\Upload();
        $upload->autoSub = true; //自动建子目录
        $upload->subName = array('date','Ymd'); //按日期创建文件夹
        $upload->exts = $ext_arr[$type]; //允许上传类型
        $upload->savePath = $php_path;
        
        $info = $upload->upload();
        
        if(!$info) {// 上传错误提示错误信息
           $result= array('error'=>1,'message'=>$upload->getError());
        }else{// 上传成功 获取上传文件信息
           $result = array('error'=>0,'info'=>$info);
        }
        
        return $result;
    }
}



?>