<?php
namespace Think\Template\TagLib;
use Think\Template\TagLib;
defined('THINK_PATH') or exit();
/**
 * Dux库标签解析
 */
class Dux extends TagLib{
    // 标签定义
    protected $tags   =  array(
        // 标签定义： attr 属性列表 close 是否闭合（0 或者1 默认1） alias 标签别名 level 嵌套层次
        'list'    => array('attr'=>' ','close'=>1,'level'=>5),
        'get'    => array('attr'=>' ','close'=>0,'level'=>5),
        'echo'    => array('attr'=>' ','close'=>0,'level'=>5),
        'curl'    => array('attr'=>' ','close'=>0,'level'=>5),
        'aurl'    => array('attr'=>' ','close'=>0,'level'=>5),
        );

    /**
     * list标签解析
     * 格式： <dux:list ></dux:list>
     * @access public
     * @param array $tag 标签属性
     * @return string|void
     */
    public function _list($tag,$content) {
        $app = $tag['app'];         //APP模块
        $label = $tag['label'];     //调用标签
        $item = $tag['item'];       //循环前缀
        if(empty($app)){
            return '请指定APP模块参数';
        }
        if(empty($label)){
            return '请指定标签参数';
        }
        if(empty($item)){
            return '请指定循环变量';
        }
        //参数处理
        $parameter = '';
        foreach ($tag as $key => $value) {
            $value = trim($value);
            if('$'==substr($value,0,1) || $key == 'where') {
                $parameter .= '"'.$key.'"=>'.$value.',';
            }else{
                $parameter .= '"'.$key.'"=>"'.$value.'",';
            }
        }
        //获取标签数据
        $html  = '<?php $'.$item.'List = service("'.$app.'","Label","'.$label.'",array('.$parameter.')); if(is_array($'.$item.'List)): foreach($'.$item.'List as $'.$item.'): ?>';
        $html .= $this->tpl->parse($content);
        $html .= '<?php endforeach; endif; ?>';
        return $html;
    }

    /**
     * get标签解析
     * 格式： <dux:echo />
     * @access public
     * @param array $tag 标签属性
     * @return string|void
     */
    public function _get($tag) {
        $app = $tag['app'];         //APP模块
        $label = $tag['label'];     //调用标签
        $item = $tag['item'];       //调用前缀
        if(empty($app)){
            return '请指定APP模块参数';
        }
        if(empty($label)){
            return '请指定标签参数';
        }
        if(empty($item)){
            return '请指定赋值变量';
        }
        //参数处理
        $parameter = '';
        foreach ($tag as $key => $value) {
            $value = trim($value);
            if('$'==substr($value,0,1) || $key == 'where') {
                $parameter .= '"'.$key.'"=>'.$value.',';
            }else{
                $parameter .= '"'.$key.'"=>"'.$value.'",';
            }
        }
        //获取标签数据
        $html  = '<?php $'.$item.' = service("'.$app.'","Label","'.$label.'",array('.$parameter.')); ?>';
        return $html;
    }

    /**
     * echo标签解析
     * 格式： <dux:echo />
     * @access public
     * @param array $tag 标签属性
     * @return string|void
     */
    public function _echo($tag) {
        $app = $tag['app'];         //APP模块
        $label = $tag['label'];     //调用标签
        if(empty($app)){
            return '请指定APP模块参数';
        }
        if(empty($label)){
            return '请指定标签参数';
        }
        //参数处理
        $parameter = '';
        foreach ($tag as $key => $value) {
            $value = trim($value);
            if('$'==substr($value,0,1) || $key == 'where') {
                $parameter .= '"'.$key.'"=>'.$value.',';
            }else{
                $parameter .= '"'.$key.'"=>"'.$value.'",';
            }
        }
        //获取标签数据
        $html  = '<?php echo service("'.$app.'","Label","'.$label.'",array('.$parameter.')); ?>';
        return $html;
    }

    /**
     * aurl标签解析
     * 格式： <dux:aurl content_id="1" />
     * @access public
     * @param array $tag 标签属性
     * @return string|void
     */
    public function _aurl($tag,$content) {
        if(empty($tag['content_id'])){
            return '请指定content_id参数';
        }
        //获取标签数据
        $html  = '<?php echo service("DuxCms","Label","aurl",array("content_id" => '.$tag['content_id'].')); ?>';
        return $html;
    }

    /**
     * curl标签解析
     * 格式： <dux:curl class_id="1" />
     * @access public
     * @param array $tag 标签属性
     * @return string|void
     */
    public function _curl($tag,$content) {
        if(empty($tag['class_id'])){
            return '请指定class_id参数';
        }
        //获取标签数据
        $html  = '<?php echo service("DuxCms","Label","curl",array("class_id" => '.$tag['class_id'].')); ?>';
        return $html;
    }

}