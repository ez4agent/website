<?php

/**
 *  系统配置 
 */
return array(
    
	'AUTH'=>'CRMSYSTEM',	
		
    //学历类别
    'Education_TYPE'=>array(
        
        '1'=>'小学',
        '2'=>'初中',
    	'3'=>'高中',
    	'4'=>'专科',
    	'5'=>'本科',
    	'6'=>'研究生',
    	'7'=>'博士',
        '8'=>'预科',
        '9'=>'语言',
        '10'=>'MBA',
        '11'=>'研究生证书与文凭',
    ),
    
    //付款方式
    'pay_type'=>array(
       
       '1'=>'按年付',
       '2'=>'按半年付',
       '3'=>'按课程长度付', 
     ),
    
    /**
     *  一个学生最多提出5次申请 
     */
    'MAX_APPLY_TIMES'=>5,
    'MAX_COLLEGE_NUM'=>2,
    
    /**
     *  申请他人的合作学校 Others
     */
    'APPLY_STATUS_OTHERS'=>array(
        'is_msm'=>9,              
        'is_Receive'  =>5,                //接收学生,等待回馈
        'no_Receive'  =>8,                //拒绝接收学生   
    ),
    
    'IS_Conditions_Admission'=>30,    //有条件录取
    'IS_NO_Conditions_Admission'=>40, //无条件录取
    'OFFER_UPDATE'=>50, 
    //签证
    'VISA_SUCCESS'=>60,
    'VISA_FAILURE'=>70,
    //中止
    'END1'=>75,
    'END2'=>80,
    'END3'=>90,
   
    'IS_EMAIL'=>20,                   //是否发送邮件
    'Apply_START' =>10,               //提出申请
    'COLLEGE_Refuse'=>99,             //申请失败
    
    //邮件设置
    'EMAIL'=>array(
        
        'HOST'=>'smtp.ez4agent.com',
        'PORT'=>'465',
        'USER'=>'info@ez4agent.com',//自己免费smtp服务器的的用户名
        'PASSWORD'=>'!QAZ2wsx',	//密码
        'FROM'=>'info@ez4agent.com',//发件人
        'FROMNAME'=>'EZ4Agent',
    ),

);

?>