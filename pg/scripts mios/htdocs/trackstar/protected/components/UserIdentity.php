<?php
/**
* UserIdentity represents the data needed to identity a user.
* It contains the authentication method that checks if the provided
* data can identify the user.
*/
class UserIdentity extends CUserIdentity
{
	private $_id;
/**
* Authenticates a user.
* The example implementation makes sure if the username andpassword
* are both 'demo'.
* In practical applications, this should be changed to authenticate
* against some persistent user identity storage (e.g. database).
* @return boolean whether authentication succeeds.
*/
	public function authenticate()
	{
		$user=User::model()->find('LOWER(USERNAME)=?',array(strtolower($this->username)));

		if($user===null)
			$this->errorCode=self::ERROR_USERNAME_INVALID;
		else if($this->hashPassword($this->password) != $user->PASSWORD)
			$this->errorCode=self::ERROR_PASSWORD_INVALID;
		else
		{
			$this->_id=$user->ID;
			$this->username=$user->USERNAME;
			$this->setState('lastLogin', date("m/d/y g:i A", strtotime($user->LAST_LOGIN_TIME)));
			$user->saveAttributes(array(
				'LAST_LOGIN_TIME'=>date("Y-m-d H:i:s", time()),
			));
			$this->errorCode=self::ERROR_NONE;
		}
		return $this->errorCode==self::ERROR_NONE;
		/*$options = Yii::app()->params['ldap'];
		$dc_string = "dc=" . implode(",dc=",$options['dc']);
		 
		$connection = ldap_connect($options['host']);
		ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($connection, LDAP_OPT_REFERRALS, 0);
		 
		if($connection)
		{
			echo $this->username;
			echo $this->password;
			echo  ldap_get_dn ( $connection );
		    // Note: in general it is bad to hide errors, however we're checking for an error below
		    $bind = @ldap_bind($connection, "uid={$this->username},ou={$options['ou']},{$dc_string}", $this->password);
		 
		    if(!$bind) $this->errorCode = self::ERROR_PASSWORD_INVALID;
		    else $this->errorCode = self::ERROR_NONE;
		}
		return !$this->errorCode;*/
	}

    public function getId()
    {
        return $this->_id;
    }

    public function hashPassword($password)
	{
		return md5($password);
	}

}