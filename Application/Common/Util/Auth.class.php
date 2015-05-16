<?php

namespace Common\Util;

class Auth {

	const AUTH_REMAMBER_NAME='cl_remember';
    const AUTH_TIMEOUT_VAR='__timeout';

    private $_timeout = 6400;

    private $_prefix = "cl_m_";

    private $_secret_key;

    public function __construct(){

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
        $key=$this->_prefix.$key;
        return isset($_SESSION[$key]);
    }

    public function getState($key,$defaultValue=null){
        $key=$this->_prefix.$key;
        return isset($_SESSION[$key]) ? $_SESSION[$key] : $defaultValue;
    }

    public function setState($key,$value,$defaultValue=null){
        $key=$this->_prefix.$key;
        if($value===$defaultValue)
            unset($_SESSION[$key]);
        else
            $_SESSION[$key]=$value;
    }

    public function getStates(){
        $tmp = array();
        foreach($_SESSION as $k => $v){
            if(strpos($k, $this->_prefix) !== false){
                $k = str_replace($this->_prefix,"",$k);
                $tmp[$k] = $v;
            }
        }

        return $tmp;
    }
	
	public function clearStates(){
		$keys=array_keys($_SESSION);
		$prefix=$this->_prefix;
		$n=strlen($prefix);
		foreach($keys as $key){
			if(!strncmp($key,$prefix,$n))
				unset($_SESSION[$key]);
		}
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