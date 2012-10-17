<?php
$oks=array();$ers=array();$contents="";
$local_debug_array=array('ALWAYS'=>1,'NEVER'=>0,'ON ERROR'=>'on_error');
$transport_array=array('curl'=>'curl','wget'=>'wget');
$remote_debug_array=array('YES'=>1,'NO'=>0);
if ($_SERVER['REQUEST_METHOD']=='POST')
	{
	if (empty($_POST['username'])) $ers['username']="Username for OpenInviter empty";
	elseif (strlen($_POST['username'])<5) $ers['username']="Username for OpenInviter too short";
	if (empty($_POST['private_key'])) $ers['private_key']="Private key for OpenInviter empty";
	elseif (!is_md5($_POST['private_key'])) $ers['private_key']="Private key is not correct";
	if (empty($_POST['cookie_path'])) $ers['cookie_path']="Cookie Path is empty";
	if (empty($_POST['message_body'])) $ers['body_message']="Body Message empty";
	if (empty($_POST['message_subject'])) $ers['message_subject']="Empty Message subject";
	if (!isset($_POST['local_debug'])) $ers['local_debug']="You didn't choose Local Debug";
	if (!isset($_POST['local_debug'])) $ers['local_debug']="You didn't choose Remote Debug";
	if (empty($_POST['transport'])) $ers['transport']="You didn't choose Transport";
	if (count($ers)==0)
		{			
		foreach($_POST as $key=>$val) $openinviter_settings_array[$key]=$val;
		$file_contents="<?php\n";
		$file_contents.="\$openinviter_settings=array(\n".row2text($openinviter_settings_array)."\n);\n";
		$file_contents.="?>";
		file_put_contents("config.php",$file_contents);
		$oks['Config']="OpenInviter succesfuly Edited";
		}
	}
include_once("config.php");
$_POST['username']=$openinviter_settings['username'];$_POST['private_key']=$openinviter_settings['private_key'];$_POST['cookie_path']=$openinviter_settings['cookie_path'];
$_POST['message_body']=$openinviter_settings['message_body'];$_POST['message_subject']=$openinviter_settings['message_subject'];
$_POST['transport']=$openinviter_settings['transport'];$_POST['local_debug']=$openinviter_settings['local_debug'];
if (count($ers)>0) $contents.=ers($ers);
if (count($oks)>0) $contents.=oks($oks);
$contents.='
<h2>Openinviter Settings</h2>
<br><br>
 <form  method="post">
	<table cellspacing="0" cellpadding="4" border="0" align="left" width="50%">
		<tr>
			<td  class="header" colspan="2">Opeinviter Settings</td>
		</tr>
		<tr >
			<td  align="left" style="width: 30ex;"><label for="username">Username : </label></td><td ><input type="text" class="text" value="'.$_POST['username'].'" name="username"/></td>
		</tr>
		<tr class="windowbg2">
			<td  align="left" style="width: 30ex;"><label for="private_key">Private key : </label></td><td ><input size="40" type="text" class="text" value="'.$_POST['private_key'].'" name="private_key"/></td>
		</tr>
		<tr >
			<td align="left" style="width: 30ex;"><label for="cookie_path">Cookie path : </label></td><td ><input type="text" class="text" value="'.$_POST['cookie_path'].'" name="cookie_path"/></td>
		</tr>
		<tr >
			<td align="left" style="width: 30ex;" valign="top"><label for="message_body" >Message body : </label></td><td ><textarea name="message_body">'.$_POST['message_body'].'</textarea></td>
		</tr>
		<tr >
			<td  align="left" style="width: 30ex;"><label for="message_subject">Message subject : </label></td><td ><input type="text" class="text" value="'.$_POST['message_subject'].'" name="message_subject"/></td>
		</tr>';
	$contents.='	
		<tr >
			<td  align="left" style="width: 10ex;">Local Debug : </td><td > 
				<select name="local_debug">
		';
	foreach ($local_debug_array as $key=>$val)
		{
		$contents.="<option value='{$val}'";
		if ($val==$_POST['local_debug']) $contents.='selected="selected"';
		$contents.=">{$key}</option>";
		}
		$contents.='
				</select>
			</td>
		</tr>';
	$contents.='
		<tr >
			<td  align="left" style="width: 10ex;">Remote Debug : </td><td > 
				<select name="remote_debug">
		';
	foreach ($remote_debug_array as $key=>$val)
		{
		$contents.="<option value='{$val}'";
		if ($val==$_POST['remote_debug']) $contents.='selected="selected"';
		$contents.=">{$key}</option>";
		}
		$contents.='
				</select>
			</td>
		</tr>';
	$contents.='	
		<tr >
			<td   align="left" style="width: 10ex;">Trasport : </td><td > 
				<select name="transport">
		';
	foreach ($transport_array as $key=>$val)
		{
		$contents.="<option value='{$val}'";
		if ($val==$_POST['transport']) $contents.='selected="selected"';
		$contents.=">{$key}</option>";
		}
		$contents.='
				</select>
			</td>
		</tr>
		<tr >
			<td align="center" valign="middle" style="padding-top: 2ex; padding-bottom: 2ex;" colspan="3"><input type="submit" value="Save" name="submit"/></td>
		</tr>
	</table>	
</form>
  ';

function oks($array)
	{
	$contents='<table cellspacing="0" cellpadding="4" border="0" align="center" width="80%">';
	foreach($array as $key=>$val)
		$contents.='
			 <tr>
				<td class="header" align="center" width="40%"><b>'.$val.'</b></td>
			</tr>';
	$contents.='</table><br>';
	return $contents;
	}

function ers($array)
	{
	$contents='<table cellspacing="0" cellpadding="4" border="0" align="center" width="80%" >';
	foreach($array as $key=>$val)
		$contents.='
			 <tr>
				<td class="header" align="center" width="40%"><b>'.$val.'</b></td>
			</tr>';
	$contents.='</table>';
	return $contents;	
	}

function is_md5($string)
	{
	$valid_chars=array('0','1','2','3','4','5','6','7','8','9','a','b','c','d','e','f');
	$string=strtolower($string);
	if (strlen($string)!=32)
		return false;
	for($i=0;$i<32;$i++)
		if (!in_array($string{$i},$valid_chars))
			return false;
	return true;
	}
  
 function row2text($row,$dvars=array())
	{
	reset($dvars);
	while(list($idx,$var)=each($dvars))
		unset($row[$var]);
	$text='';
	reset($row);
	$flag=0;
	$i=0;
	while(list($var,$val)=each($row))
		{
		if($flag==1)
			$text.=", ";
		elseif($flag==2)
			$text.=",\n";
		$flag=1;
		//Variable
		if(is_numeric($var))
			if($var{0}=='0')
				$text.="'$var'=>";
			else
				{
				if($var!==$i)
					$text.="$var=>";
				$i=$var;
				}
		else
			$text.="'$var'=>";
		$i++;
		//letter
		if(is_array($val))
			{
			$text.="array(".row2text($val,$dvars).")";
			$flag=2;
			}
		else
			$text.="'$val'";
		}
	return($text);
	}
echo $contents;
?>