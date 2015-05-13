<?php
namespace Think\Template\TagLib;
use Think\Template\TagLib;
defined('THINK_PATH') or exit();
/**
 * AdminUi库标签解析
 */
class Admin extends TagLib{
    // 标签定义
    protected $tags   =  array(
        // 标签定义： attr 属性列表 close 是否闭合（0 或者1 默认1） alias 标签别名 level 嵌套层次
        'row'    => array('','close'=>1,'level'=>5),
        'grid'    => array('attr'=>'item','close'=>1,'level'=>5),
        'panel'    => array('attr'=>'title,icon','close'=>1,'level'=>5),
        'form'    => array('attr'=>'url,method,id,class','close'=>1,'level'=>5),
        'formsubmit'    => array('attr'=>'','close'=>0,'level'=>5),
        'formrow'    => array('attr'=>'title,tip','close'=>1,'level'=>5),
        'text'    => array('attr'=>'name,id,class,size,value,len,ext,datatype,errormsg','close'=>0,'level'=>5),
        'textarea' => array('attr'=>'name,id,class,size,rows,ext,datatype,errormsg','close'=>1,'level'=>5),
        'radio'    => array('attr'=>'name,item,value,checked,ext','close'=>0,'level'=>5),
        'checkbox'  => array('attr'=>'name,item,value,checked,ext','close'=>0,'level'=>5),
        'select'    => array('attr'=>'name,item,value,select,ext','close'=>0,'level'=>5),
        'assigndown' => array('attr'=>'target,id,name,data,item','close'=>0,'level'=>5),
        'table'      => array('attr'=>'id,style,show','close'=>1,'level'=>5),
        'tabletool'    => array('attr'=>'filter','close'=>1,'level'=>5),
        'tablefoot'    => array('attr'=>'action','close'=>0,'level'=>5),
        );

    /**
     * grid标签解析
     * 格式： <admin:grid item="6"></admin:grid>
     * @access public
     * @param array $tag 标签属性
     * @return string|void
     */
    public function _grid($tag,$content) {

        $item      = $tag['item'];                //分割
        $class = $tag['class'];
        $html = '
        <div class="g-col-'.$item. ' '.$class.' ">
          '.$content.'
        </div>';
        return $html;
    }

    /**
     * fluid标签解析
     * 格式： <admin:row></admin:row>
     * @access public
     * @param array $tag 标签属性
     * @return string|void
     */
    public function _row($tag,$content) {
        $html = '
          <div class="g-grid">
          '.$content.'
          </div>
        ';
        return $html;
    }

    /**
     * panel标签解析
     * 格式： <admin:panel title="" icon=""></admin:panel>
     * @access public
     * @param array $tag 标签属性
     * @return string|void
     */
    public function _panel($tag,$content) {
        $title      = $tag['title'];                //标题
        $body      = $tag['body']?false:true;  //body区域
        $class      = isset($tag['class'])?$tag['class']:'';                //样式名
        $icon      = isset($tag['icon'])?'<span class="icon"><i class="u-icon-'.$tag['icon'].'"></i></span>':'';
        
        $html = '
        <div class="m-panel '.$class.'">';
        if(!empty($title)||!empty($tag['icon'])){
          $html .= '<div class="panel-header"> '.$icon.' '.$title.' </div>';
        }
        if($body){
          $html.='
            <div class="panel-body">
            '.$content.'
            </div>';
        }else{
          $html.= $content;
        }
       $html.=' </div>';
        return $html;
    }

    /**
     * form标签解析
     * 格式： <admin:form url="" method="" type="" id="" class="" ></admin:form>
     * @access public
     * @param array $tag 标签属性
     * @return string|void
     */
    public function _form($tag,$content) {

        $url      = $tag['url'];                //URL
        $method      = $tag['method'];                //提交方式
        $id      = $tag['id'];                //id
        $class      = $tag['class'];                //附加样式
        $html = '
        <form action="'.$url.'" method="'.$method.'" id="'.$id.'" class="m-form '.$class.'">
        <fieldset>
          '.$content.'
        </fieldset>
        </form>';
        return $html;
    }

    /**
     * formsubmit标签解析
     * 格式： <admin:formsubmit/>
     * @access public
     * @param array $tag 标签属性
     * @return string|void
     */
    public function _formsubmit($tag) {
        $html = '
        <div class="formitm form-submit">
        <div class="ipt">
            <div class="tip" id="tips"></div>
            <button class="u-btn u-btn-success u-btn-large" type="submit" id="btn-submit">保存</button>
            <button class="u-btn u-btn-large" type="reset">重置</button>
        </div>
        </div>';
        return $html;
    }

    /**
     * formrow标签解析
     * 格式： <admin:formrow title="" tip="" ></admin:formrow>
     * @access public
     * @param array $tag 标签属性
     * @return string|void
     */
    public function _formrow($tag,$content) {
        $title      = $tag['title'];                //标题
        $tip      = $tag['tip'];                //提示信息
        $html = '
        <div class="formitm">
            <label class="lab">'.$title.'</label>
            <div class="ipt">
                '.$content.'
                <p class="help-block">'.$tip.'</p>
            </div>
        </div>';
        return $html;
    }

    /**
     * text标签解析
     * 格式： <admin:text name="" value="" />
     * @access public
     * @param array $tag 标签属性
     * @return string|void
     */
    public function _text($tag) {
        $name       = $tag['name'];                //名称
        $id         = isset($tag['id'])?$tag['id']:$name;                //ID
        $class      = isset($tag['class'])?$tag['class']:'';                //样式名
        $value      = $tag['value'];                //值
        $len     = isset($tag['len'])?'maxlength="'.$tag['len'].'"':''; //最大长度
        $width = isset($tag['width'])?'u-width-'.$tag['width']:'u-width-large';  //文本框宽度
        $ext      = $tag['ext'];                //扩展信息 
        $type      = isset($tag['type'])?$tag['type']:'text';         //输入框属性
        $datatype     = isset($tag['datatype'])?'datatype="'.$tag['datatype'].'"':''; //表单验证
        $errormsg     = isset($tag['errormsg'])?'errormsg="'.$tag['errormsg'].'"':''; //验证失败
        $html = '<input name="'.$name.'" type="'.$type.'"  class="form-element '.$width. ' ' .$class.' " id="'.$id.'" value="'.$value.'" '.$len.' '.$ext .' '.$datatype.' '.$errormsg.'>';
        return $html;
    }

    /**
     * textarea标签解析
     * 格式： <admin:textarea name="" value="" />
     * @access public
     * @param array $tag 标签属性
     * @return string|void
     */
    public function _textarea($tag,$content) {
        $name       = $tag['name'];                //名称
        $id         = isset($tag['id'])?$tag['id']:$name;                //ID
        $class      = isset($tag['class'])?$tag['class']:'';                //样式名
        $content      = $content;                //值
        $rows     = isset($tag['rows'])?'rows="'.$tag['rows'].'"':''; //行数
        $width = isset($tag['width'])?'u-width-'.$tag['width']:'u-width-large';  //文本框宽度
        $ext      = $tag['ext'];                //扩展信息 
        $datatype     = isset($tag['datatype'])?'datatype="'.$tag['datatype'].'"':''; //表单验证
        $errormsg     = isset($tag['errormsg'])?'errormsg="'.$tag['errormsg'].'"':''; //验证失败
        $html = '<textarea name="'.$name.'" type="text"  class="form-element '.$width. ' ' .$class.'" id="'.$id.'" value="'.$value.'" '.$rows.' '.$ext .' '.$datatype.' '.$errormsg.'>'.$content.'</textarea>';
        return $html;
    }

    /**
     * radio标签解析
     * 格式： <admin:radio $name="name" $item=""  $value="" />
     * @access public
     * @param array $tag 标签属性
     * @return string|void
     */
    public function _radio($tag) {
        $name       = $tag['name'];                //名称
        $item      = isset($tag['item'])?$tag['item']:'';                 //项目名
        $value      = isset($tag['value'])?$tag['value']:'';                //选项值
        $checked    = $tag['checked'];    //选中值
        $ext      = $tag['ext'];                //扩展信息 
        $isset    = $tag['isset'];    //检测存在
        if(!empty($isset)){
          $isset = 'if(!isset('.$checked.')){ '.$checked.'= "'.$isset.'"; }';
        }

        if(empty($item)||empty($value)){
            return ;
        }
        $html = '';
        $items = explode(',', $item);
        $value = explode(',', $value);
        foreach ($items as $key => $vo) {
          $html .= '<label>
                      <input type="radio" name="'.$name.'" id="'.$name.$key.'" value="'.$value[$key].'" '.$ext.'  <?php '.$isset.' if('.$value[$key].' == '.$checked.'){ ?> checked="checked" <?php } ?> > <span>'.$vo.'</span>
                    </label> ';
        }
        return $html;
    }

    /**
     * checkbox标签解析
     * 格式： <admin:checkbox $name="name" $item=""  $value="" />
     * @access public
     * @param array $tag 标签属性
     * @return string|void
     */
    public function _checkbox($tag) {
        $name       = $tag['name'];                //名称
        $item      = isset($tag['item'])?$tag['item']:'';                 //项目名
        $value      = isset($tag['value'])?$tag['value']:'';                //选项值
        $checked    = $tag['checked'];    //选中值
        $ext      = $tag['ext'];                //扩展信息 
        $isset    = $tag['isset'];    //检测存在

        if(empty($item)||empty($value)){
            return ;
        }
        $html = '';
        $items = explode(',', $item);
        $value = explode(',', $value);
        foreach ($items as $key => $vo) {
          $html .= '<label>
                    <?php $'.$name.' = explode(",", "'.$checked.'"); ?>
                    <input type="checkbox" name="'.$name.'[]" id="'.$name.$key.'" value="'.$value[$key].'" '.$ext.'  <?php '.$isset.' if(in_array('.$value[$key].',$'.$name.')){ ?> checked="checked" <?php } ?> > <span>'.$vo.'</span>
                    </label> ';
        }
        return $html;
    }

    public function _select($tag) {
        $name       = $tag['name'];                //名称
        $id         = isset($tag['id'])?$tag['id']:$name;                //ID
        $item      = isset($tag['item'])?$tag['item']:'';                //项目名
        $value      = isset($tag['value'])?$tag['value']:'';                //选项值
        $selected      = $tag['selected'];              //选中值
        $class      = isset($tag['class'])?$tag['class']:'';                //样式名
        $ext      = $tag['ext'];                //扩展信息 
        if(empty($item)||empty($value)){
            return ;
        }
        $html = '<select name="'.$name.'" id="'.$id.'" '.$ext.' class="form-element '.$class.'">';
          $items = explode(',', $item);
          $value = explode(',', $value);
          foreach ($items as $key => $vo) {
              $html .= '<option value="'.$value[$key].'" <?php if('.$value[$key].' == '.$selected.'){ ?> selected="selected"  <?php } ?> >'.$vo.'</option>';
          }
        $html .= '</select>';
        return $html;
    }
    

    /**
     * assigndown标签解析
     * 格式： <admin:assigndown  target="" data="" id="" name="" />
     * @access public
     * @param array $tag 标签属性
     * @return string|void
     */
    public function _assigndown($tag) {
        $target     = isset($tag['target'])?$tag['target']:'请选择';                //赋值目标
        $name     = $tag['name'];                //按钮名
        $id      = $tag['id'];                //id
        $data      = $tag['data'];           //下拉数据
        $item = explode(',', $tag['item']);       //显示数据
        $class      = isset($tag['class'])?$tag['class']:'';                //样式名
        $html = '
        <select class="form-element dux-assign '.$class.'" target="'.$target.'" id="'.$id.'" >
            <option value =" ">'.$name.'</option>
            <foreach name="'.$data.'" item="vo" >
            <option value="{$vo.'.$item[0].'}">{$vo.'.$item[1].'}</option>
            </foreach>
        </select>';
        return $html;
    }

    /**
     * table标签解析
     * 格式： <html:table id="" name="" show="" />
     * @access public
     * @param array $tag 标签属性
     * @return string
     */
    public function _table($tag,$content) {

        $id         = $tag['id'];                       //表格ID
        $class      = $tag['class'];                    //样式名

        if(substr($tag['show'],0,1)=='$') {
            $show   = $this->tpl->get(substr($tag['show'],1));
        }else {
            $show   = $tag['show'];
        }
        $show       = explode(',',$show);                //列表显示字段列表
        //计算表格的列数
        $colNum     = count($show);
        //显示开始
        $parseStr   = '<div class="m-table-mobile"><table id="'.$id.'" class="m-table '.$class.'">';
        $parseStr  .= '<thead><tr>';
        //列表需要显示的字段
        $fields = array();
        foreach($show as $val) {
            $fields[] = explode(':',$val);
        }
        foreach($fields as $field) {//显示指定的字段
            $property = explode('|',$field[0]);
            if(isset($property[1])) {
                $parseStr .= '<th width="'.$property[1].'">';
            }else {
                $parseStr .= '<th>';
            }
            $parseStr .= $property[0].'</th>';
        }
        $parseStr .= '</tr></thead><tbody>';
        $parseStr .= $content;
        $parseStr   .= '</tbody></table></div>';
        return $parseStr;
    }

    /**
     * 表格工具解析
     * 格式： <admin:tabletool />
     * @access public
     * @param array $tag 标签属性
     * @return string $html
     */
    public function _tabletool($tag,$content) {
        $url = isset($tag['url'])?$tag['url']:'{:U()}';
        $filter = string_to_bool($tag['filter']);
        $searchContent = $tag['search_html'];
        if(isset($tag['search'])){
            $search = string_to_bool($tag['search']);
        }else{
            $search = true;
        }
        $keyword = $tag['keyword'];
        if($filter){
            $filterHtml = ' 
            <div class="tool-filter f-cb">
                <form action="'.$url.'" method="post">
                    '.$content.'
                    <button class="u-btn u-btn-primary" type="submit">筛选</button>
                </form>
            </div>';
        }
        if($search){
            $searchHtml = '
            <div class="tool-search f-cb">';
            if($searchContent){
                $searchHtml .= $searchContent;
            }else{
                $searchHtml .= '
                    <form action="'.$url.'" method="post">
                        <input type="text" class="form-element" name="keyword" value="'.$keyword.'" />
                        <button class="u-btn u-btn-primary" type="submit">搜索</button>
                    </form>';
            }
            $searchHtml .= '</div>
            ';
        }
        $html = '<div class="m-table-tool f-cb">'.$searchHtml.$filterHtml.'</div>';
        return $html;
    }

    /**
     * 表格操作解析
     * 格式： <admin:tablefoot />
     * @access public
     * @param array $tag 标签属性
     * @return string $html
     */
    public function _tablefoot($tag) {
        $action = string_to_bool($tag['action']);
        $item  = isset($tag['item'])?$tag['item']:'';  //项目名
        $value      = isset($tag['value'])?$tag['value']:''; //选项值
        if(!empty($item)){
            $selectHtml = '<select name="selectAction" id="selectAction" class="form-element">';
              $items = explode(',', $item);
              $value = explode(',', $value);
              foreach ($items as $key => $vo) {
                  $selectHtml .= '<option value="'.$value[$key].'">'.$vo.'</option>';
              }
            $selectHtml .= '</select> ';
        }
        $html = '<div class="m-table-bar">';
        if($action){
            $html .= '
            <div class="bar-action">
            <a class="u-btn u-btn-primary" href="javascript:;" id="selectAll">选择</a>
             '.$selectHtml.' 
            <a class="u-btn u-btn-success" href="javascript:;" id="selectSubmit">执行</a>
            </div>';
        }
        $html .= '
            <div class="bar-pages">
              <div class="m-page">
                {$page}
              </div>
            </div>
            <div class="f-cb"></div>
        </div>';
        return $html;
    }

}