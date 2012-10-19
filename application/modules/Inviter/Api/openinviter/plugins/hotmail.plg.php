<?php
$_pluginInfo=array(
	'name'=>'Live/Hotmail',
	'version'=>'1.6.6',
	'description'=>"Get the contacts from a Windows Live/Hotmail account",
	'base_version'=>'1.8.0',
	'type'=>'email',
	'check_url'=>'http://login.live.com/login.srf?id=2',
	'requirement'=>'email',
	'allowed_domains'=>array('/(hotmail)/i','/(live)/i','/(msn)/i','/(chaishop)/i'),
	'imported_details'=>array('first_name','email_1'),
	);
/**
 * Live/Hotmail Plugin
 * 
 * Imports user's contacts from Windows Live's AddressBook
 * 
 * @author OpenInviter
 * @version 1.5.8
 */
class hotmail extends openinviter_base
	{
	private $login_ok=false;
	public $showContacts=true;
	public $internalError=false;
	protected $timeout=30;
		
	public $debug_array=array(
				'initial_get'=>'SavePasswordCheckBox',
				'login_post'=>'SummaryForm',
				'first_redirect'=>'self.location.href',
				'url_inbox'=>'peopleUrlDomain',
				'message_at_login'=>'peopleUrlDomain',
				'url_sent_to'=>'ContactList.aspx',
				'get_contacts'=>'\x26\x2364\x3',
				);
	
	/**
	 * Login function
	 * 
	 * Makes all the necessary requests to authenticate
	 * the current user to the server.
	 * 
	 * @param string $user The current user.
	 * @param string $pass The password for the current user.
	 * @return bool TRUE if the current user was authenticated successfully, FALSE otherwise.
	 */
	public function login($user,$pass)
		{
		$this->resetDebugger();
		$this->service='hotmail';
		$this->service_user=$user;
		$this->service_pass=$pass;
		if (!$this->init()) return false;
		$res=$this->get("https://mid.live.com/si/login.aspx",true);
		if ($this->checkResponse('initial_get',$res))
			$this->updateDebugBuffer('initial_get',"https://mid.live.com/si/login.aspx",'GET');
		else
			{
			$this->updateDebugBuffer('initial_get',"https://mid.live.com/si/login.aspx",'GET',false);
			$this->debugRequest();
			$this->stopPlugin();
			return false;
			}

    $form_action=$this->getElementString($res,'method="post" action="','"');
    $form_action='https://mid.live.com/si/' . str_replace('https://mid.live.com/si/', '', $form_action);

    $post_elements=$this->getHiddenElements($res);$post_elements["SavePasswordCheckBox"]=0;$post_elements["PasswordSubmit"]='Sign+in';$post_elements["LoginTextBox"]=$user;$post_elements["PasswordTextBox"]=$pass;
		$res=$this->post($form_action,$post_elements,true);

		$url_redirect=$this->getElementString($res,'http-equiv="refresh" content="0;url=','"');
		$res=$this->get($url_redirect,true);

		if ($this->checkResponse('login_post',$res))
			$this->updateDebugBuffer('login_post',"{$form_action}",'POST',true,$post_elements);
		else
			{
			$this->updateDebugBuffer('login_post',"{$form_action}",'POST',false,$post_elements);
			$this->debugRequest();
			$this->stopPlugin();
			return false;
			}

		$this->login_ok="http://mpeople.live.com/default.aspx";
		return true;
		}

	/**
	 * Get the current user's contacts
	 * 
	 * Makes all the necesarry requests to import
	 * the current user's contacts
	 * 
	 * @return mixed The array if contacts if importing was successful, FALSE otherwise.
	 */	
	public function getMyContacts()
		{
		if (!$this->login_ok)
			{
			$this->debugRequest();
			$this->stopPlugin();
			return false;
			}
		else $base_url=$this->login_ok;

    $count = 0;
    $content = '';

    do {
      $res=$this->get($base_url . '?pg=' . $count, true);

      $count++;
      $last_page = (strpos($res,'/default.aspx?pg=' . $count)===false);
      $content .= $res;
    } while(!$last_page && $res && $count < 100);

    $content = @html_entity_decode($content);
    $content = urldecode($content);
    $content = str_replace('%40', '@', $content);

    $parts = explode('contactinfo.aspx', $content);
    $contacts = array();
    foreach ($parts as $part) {
      if (strpos($part, 'compose&to=') !== false) {
        $contact_email = $this->getElementString($part, 'compose&to=', '&');
        $contact_name = array('first_name' => $this->getElementString($part, '">', '</a>'), 'email_1' => $contact_email);
        $contacts[$contact_email] = $contact_name;
      }
    }

		foreach ($contacts as $email=>$name) if (!$this->isEmail($email)) unset($contacts[$email]);
		return $this->returnContacts($contacts);
		}

	/**
	 * Terminate session
	 * 
	 * Terminates the current user's session,
	 * debugs the request and reset's the internal 
	 * debudder.
	 * 
	 * @return bool TRUE if the session was terminated successfully, FALSE otherwise.
	 */	
	public function logout()
		{
		if (!$this->checkSession()) return false;
		if (file_exists($this->getLogoutPath()))
			{
			$url=file_get_contents($this->getLogoutPath());
			$url_logout=$url."mail/logout.aspx";
			$res=$this->get($url_logout,true);
			}
		$this->debugRequest();
		$this->resetDebugger();
		$this->stopPlugin();
		return true;
		}
		
	}
?>