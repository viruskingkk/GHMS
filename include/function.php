<?php
require_once('htmlpurifier/HTMLPurifier.auto.php');
function deletedir($dir) {
    if ($handle = opendir($dir)) {
        while(false !== ($item = readdir($handle))) {
            if ($item != "." && $item != "..") {
                if (is_dir($dir."/".$item)) {
                    deletedir($dir."/".$item);
                } else {
                    unlink($dir."/".$item);
                }
            }
        }    
        closedir($handle);
        rmdir($dir);
      }
}

function lt_replace($str){ 
    return preg_replace("/<([^\/[:alpha:]])/", '&lt;\\1', $str); 
} 

function removal_escape_string($data){
	$data = lt_replace($data);
    return stripslashes($data);
}

function sc_keygen($_value=''){
	return str_shuffle(base64_encode(mt_rand(100,999).time()).sha1(mt_rand().md5($_value).uniqid()));
}
function sc_login($_username,$_password){
	global $SQL;
	if (isset($_username)&&isset($_password)) {
		$login = $SQL->query("SELECT `name`, `password`, `level` FROM `member` WHERE (name = '%s' OR email = '%s') AND password = '%s'",array(
			$_username,
			$_username,
			sc_password($_password,$_username)
		));
		
		
		
		if ($login->num_rows <1) {
			$login = $SQL->query("SELECT `name`, `password`, `level` FROM `member` WHERE (name = '%s' OR email = '%s') AND password = '%s'",array(
			$_username,
			$_username,
			md5(sha1($_password))
			));
			if($login->num_rows > 0){
				$SQL->query("UPDATE `member` SET `password` = '%s' WHERE `name` = '%s'",array(sc_password($_password,$_username),$_username));
			}
		}
		
		
		if ($login->num_rows > 0) {
			$info = $login->fetch_assoc();
			$group = $info['level'];
			
			$last_login= $SQL->query("UPDATE `member` SET `last_login` = now() WHERE `name` = '%s'",array($info['name']));
			
			$_SESSION['Center_Username'] = strtolower($_username);
			$_SESSION['Center_UserGroup'] = $group;	      
			setcookie("login", time(), time()+7200);
			return 1;
		}
		else {
			return -1;
		}
		die();
	}
}
function sc_loginout(){
	$_SESSION['Center_Username'] = NULL;
	$_SESSION['Center_UserGroup'] = NULL;
	unset($_SESSION['Center_Username']);
	unset($_SESSION['Center_UserGroup']);
	setcookie("login", "", time()-7200);
	return 1;
}

function sc_register($_username,$_password,$_email,$_web_site='',$_level='user'){
	global $SQL;
	global $center;
	if($center['register'] == 1){
		if(isset($_username) && (trim(sc_namefilter($_username)) != '') && isset($_password) && (trim($_password) != '')&& filter_var($_email, FILTER_VALIDATE_EMAIL)){
			if($_web_site!='' && !filter_var($_web_site, FILTER_VALIDATE_URL)){
				return -2;
			}
			
			$_username=sc_namefilter($_username);
			
			$auth_name = $SQL->query("SELECT name FROM member WHERE name = '%s' OR email = '%s'", array($_username,$_email));
			if($auth_name->num_rows > 0){
				return -1;
				exit;
			}
			
			$add_user = $SQL->query("INSERT INTO member (name, password, email, web_site, avatar, rekey, level , joined ,last_login) VALUES ('%s', '%s', '%s', '%s', '../images/default_avatar.png', '%s', '%s', now(), now())",array(
				sc_namefilter($_username),
				sc_password($_password,$_username),
				$_email,
				$_web_site,
				substr(sc_keygen($_username),0,16),
				$_level
			));
			
			return 1;
		}else{
			return -2;
		}
	}else{
		return -3;
	}
}


function sc_get_member_data($_username){
	global $SQL;
	$member['query'] = $SQL->query("SELECT * FROM member WHERE name = '%s'",array($_username));
	$member['row'] = $member['query']->fetch_assoc();
	$member['num_rows'] = $member['query']->num_rows;
	if($member['num_rows']=1){
		return $member;
	}else{
		return -1;
	}
}

function sc_namefilter($_value){
	$_array=array('/' => '' , '\\' => '' , '*' => '' ,':' => '' , '?' => '' , '<'  => '' , '>' => '','│' => '');
	return strtr($_value,$_array);
}

function sc_password($_value,$_salt){
	$salt=substr(sha1(strrev($_value).$_salt),0,24);
	return hash('sha512',$salt.$_value);
}

function sc_get_headurl(){
	$url="http://{$_SERVER['HTTP_HOST']}{$_SERVER['PHP_SELF']}";
	$po= strripos($url,'/');
	return substr($url,0,$po).'/';
}

function sc_addnotice($_url,$_content,$_send_from,$_send_to){
	global $SQL;
	$SQL->query("INSERT INTO notice ( url,content, status, send_from,send_to,ptime) VALUES ('%s','%s',0,'%s','%s',now())",array($_url,$_content,$_send_from,$_send_to));
	return 1;
}
function sc_xss_filter($content){
    $purifier = new HTMLPurifier();
    $filterContent = $purifier->purify($content);
    return $filterContent;
}