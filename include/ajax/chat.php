<?php
if(!session_id()){
	session_start();
}

$user = $_SESSION['Center_Username'];
session_write_close();

set_include_path('../');
$includepath = true;

require_once('../../Connections/SQL.php');
require_once('channel.php');

class Chat extends Channel {
	protected $type = 'public';
	protected $user = null;
	
	public function __construct(){
		parent::__construct();
		if(isset($_GET['type'])){
			$this->type = $_GET['type'];
		}
		if(isset($_GET['user'])){
			$this->user = $_GET['user'];
		}
	}
	
	public function update(){
		global $SQL;
		global $user;
		
		if($this->payload === null){
			$this->payload = array(
				'last' => 0
			);
		}
		
		if($this->payload['last'] > 0){
			if($this->type == "public"){
				$result = $SQL->query("SELECT * FROM `chat` WHERE `ptime` > '%s'",array(
					date("Y-m-d H:i:s",$this->payload['last'])
				));
			}
			else {
				if($this->user !== null){
					$result = $SQL->query("SELECT * FROM `private_chat` WHERE `ptime` > '%s' AND (`room` = '%s' OR `name` = '%s')",array(
						date("Y-m-d H:i:s",$this->payload['last']),
						$this->user,
						$user
					));
				}
			}
		}
		else {
			if($this->type == "public"){
				$result = $SQL->query("SELECT * FROM `chat` ORDER BY `ptime` ASC",array());
			}
			else {
				if($this->user !== null){
					$result = $SQL->query("SELECT * FROM `private_chat` WHERE (`room` = '%s' OR `name` = '%s') ORDER BY `ptime` ASC",array(
						$this->user,
						$user
					));
				}
			}
		}
		
		return $this->prepare($result);
	}
	
	public function prepare($result){
		if($result->num_rows == 0){
			return false;
		}
		
		$results = array();
		
		while($rows = $result->fetch_assoc()){
			$this->payload['last'] = strtotime($rows['ptime']);
			$t = strtotime($rows['ptime']);
			if(date('d') == date('d', $t)){
				$rows['ptime'] = date('H:i:s',$t);
			}
			$results[] = $rows;
		}
		
		return $results;
	}
}

$chat = new Chat();
$chat->start();
