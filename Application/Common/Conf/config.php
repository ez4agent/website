<?php
//ini_set('session.cookie_domain', ".domain.com");
return array(
	//'配置项'=>'配置值'
    'TMPL_TEMPLATE_SUFFIX'=>'.html',
    'LOAD_EXT_CONFIG' => 'performance,shield,db,system',
    'SESSION_AUTO_START'=>TRUE,
    'SESSION_TYPE'=>'Db',
    'VAR_SESSION_ID' => 'session_id',
	'SITE_URL' => '',
    'APP_AUTOLOAD_PATH' => '@.TagLib',
    'DEFAULT_CONTROLLER' => 'Schedule',
	);