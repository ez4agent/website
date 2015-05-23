<?php

namespace Common\Util;

class Auth {

	const AUTH_REMAMBER_NAME='cl_remember';
    const AUTH_TIMEOUT_VAR='__timeout';

    private $_timeout;

    private $_sessin_name = "member";

    private $_secret_key;

    public function __construct(){

        $this->_timeout =  3600 * 24 * 7;
        $this->_secret_key = C('SALT');
        if($this->isGuest()){
            $cookie = $this->getAuthCookie();
            
            if($cookie){
                  $key = $this->authorization_key( $cookie[0], $cookie[1] );

                  if ($key!==false and $key===$cookie[2]){
                    $this->logging($cookie[0], true);
                  }else{
                    setcookie(self::AUTH_REMAMBER_NAME, '', 0, '/');
                  }
            }
        }else{
            $this->updateState();
        }
    }

    public function __get($name){
        if($this->hasState($name))
            return $this->getState($name);
        return null;
    }

    public function __set($name,$value){
        if($this->hasState($name))
            $this->setState($name,$value);
    }

    public function hasState($key){
        $m = session($this->_sessin_name);
        return isset($m[$key]);
    }

    public function getState($key,$defaultValue=null){
        $m = session($this->_sessin_name);
        return isset($m[$key]) ? $m[$key] : $defaultValue;
    }

    public function setState($key,$value,$defaultValue=null){
        $m = session($this->_sessin_name);
        if(!$m){
            $m = array();
        }

        $m[$key] = $value;
        session($this->_sessin_name,$m);
    }

    public function getStates(){
        return (array)session($this->_sessin_name);
    }
	
	public function clearStates(){
		session($this->_sessin_name,null);
	}

    public function updateState(){
        
        if($this->isGuest()){
            return;
        }

        $member = M('member')->where(array('member_id'=>$this->member_id))->field('*')->find();

        foreach($member as $k => $v){
            $this->setState($k, $v);
        }
        //more...
    }

    public function logging($user_id, $remember_me){
		if ($remember_me){
			$now = time();
			$key = $this->authorization_key($user_id, $now);

			if ($key!==false){
			    $cookie = $user_id.'-'.$now.'-'.$key;
			    setcookie(
                    self::AUTH_REMAMBER_NAME,
                    $cookie,
                    time()+$this->_timeout,
			        '/',
                    '',
                    ini_get('session.cookie_secure'),
			        ini_get('session.cookie_httponly')
			    );
			}
		}else{
			setcookie(self::AUTH_REMAMBER_NAME, '', 0,'/');
		}

		$this->setState('member_id',(int)$user_id);

        $this->updateState();
    }

    public function logout($destroySession=true){
		
    	$this->clearStates();
		session_unset();
		session_destroy();
		setcookie(session_name(),'',0,'/');
		setcookie(self::AUTH_REMAMBER_NAME, '', 0,'/');
    }

    function authorization_key($user_id, $time){
    	
    	$row = M('member')->where(array('member_id'=>$user_id))->field('username,pwd')->find();
        if (!empty($row)){
            $username = stripslashes($row['username']);
            $data = $time.$user_id.$username;
            $key = base64_encode( hash_hmac('sha1', $data, $this->_secret_key.$row['pwd'],true) );
            return $key;
        }
	    return false;
	}

    function getAuthCookie(){
        if (isset( $_COOKIE[self::AUTH_REMAMBER_NAME] ) && !empty( $_COOKIE[self::AUTH_REMAMBER_NAME])){
            $cookie = explode('-', stripslashes($_COOKIE[self::AUTH_REMAMBER_NAME]));
            if ( count($cookie)===3
                and is_numeric(@$cookie[0])
                and is_numeric(@$cookie[1])
                and time()- 5184000 <=@$cookie[1]
                and time()>=@$cookie[1] )
            {
                return $cookie;
            }
        }

        return false;
    }

    function isGuest(){
        return $this->member_id == 0;
    } 

}