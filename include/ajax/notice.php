<?php
if(!session_id()){
	session_start();
}

set_include_path('../');
$includepath = true;

require_once('../../Connections/SQL.php');
require_once('../../config.php');
if(!isset($_SESSION['Center_Username'])){
    exit;
}
if(isset($_GET['read'])){
	$SQL->query("UPDATE `notice` SET `status` = '1' WHERE `send_to`='%s' AND `status`='0' ORDER BY `ptime` DESC LIMIT 5",array($_SESSION['Center_Username']));
	die;
}


if(isset($_GET['unread'])){
	echo $SQL->query("SELECT * FROM `notice` WHERE `send_to`='%s' AND `status`='0'",array($_SESSION['Center_Username']))->num_rows;
	die;
}


$notice['all_rows']=$SQL->query("SELECT * FROM `notice` WHERE `send_to`='%s'",array($_SESSION['Center_Username']))->num_rows;

if($notice['all_rows']>20){
	$SQL->query("DELETE FROM `notice` WHERE `send_to`='%s' AND `status`='1' ORDER BY `ptime` ASC LIMIT 1",array($_SESSION['Center_Username']));
}

$notice['query']=$SQL->query("SELECT * FROM `notice` WHERE `send_to`='%s' ORDER BY `ptime` DESC  LIMIT 0,10",array($_SESSION['Center_Username']));
$notice['row']=$notice['query']->fetch_assoc();
$notice['num_rows']=$notice['query']->num_rows;


if($notice['num_rows']>0){
?>
<h4>通知</h4>
<?php
do{
	if($notice['row']['status']!=1){
?>
		<div class="notifications new">
<?php }else{ ?>
		<div class="notifications">
<?php } ?>
			<img src="include/avatar.php?id=<?php echo $notice['row']['send_from']; ?>" class="mini_avatar">
			<a href="<?php echo $notice['row']['url']; ?>">
				<span class="time"><?php echo $notice['row']['ptime']; ?></span>
				<p><?php echo $notice['row']['content']; ?></p>
			</a>
			<br class="clearfix">
		</div>
<?php }while($notice['row']=$notice['query']->fetch_assoc());} ?>