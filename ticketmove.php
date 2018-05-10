<?php
require_once('Connections/SQL.php');
require_once('config.php');
require_once('include/view.php');

if(!isset($_SESSION['Center_Username'])){
    header("Location: index.php?login");
    exit;
}

if((!isset($_GET['id']))or(abs($_GET['id'])=='')){
    header("Location: ticket.php");
	exit;
}

$post = $SQL->query("SELECT * FROM ticket WHERE id = '%d' AND posted = '%s'",array($_GET['id'],$_SESSION['Center_Username']));
$post_row = $post->fetch_assoc();
$post_num_rows = $post->num_rows;

//if($post_num_rows<1){
	//header("Location: ticket.php");
	//exit;
//}

if(isset($_POST['status'])){
	$_auth = $SQL->query("SELECT * FROM `ticket_status` WHERE id = '%d'",array(abs($_POST['status'])))->num_rows;
	if($_auth>0){
		$post = $SQL->query("UPDATE `ticket` SET `status` = '%d' WHERE  `id` = '%d'",array($_POST['status'],$_GET['id']));
		header("Location: ticketview.php?statusmoving&fid=".$_POST['status']."&id=".$post_row['id']);
	}
}


$view = new View('view/new_theme.html','include/nav.php',$center['site_name'],'帖子移動');
$view->addScript("https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js");
$view->addScript("include/js/channel.js");
$view->addScript("include/js/notice.js");

$_status_query=$SQL->query("SELECT * FROM `ticket_status` ORDER BY `position` ASC");
$_status =$_status_query->fetch_assoc();

?>
<div class="main">
<h2>Post Move</h2>
<form action="ticketmove.php?id=<?php echo $post_row['id'] ?>" method="POST" name="form1">
		<div class="controls">
			Let<b><?php echo $post_row['post_title']; ?></b>
		</div>
		<div class="controls">
			Move to&nbsp;
			<select class="input-medium" name="status" required="required">
			<?php do{ ?>
				<option value="<?php echo $_status['id']; ?>"><?php echo $_status['statusname']; ?></option>
			<?php }while ($_status =  $_status_query->fetch_assoc()); ?>
			</select>
		</div>
		
	<input type="submit" name="button" class="btn btn-primary" value="Move" />
</form>
</div>
<?php
$view->render();
?>