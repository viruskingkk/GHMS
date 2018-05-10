<?php
require_once('Connections/SQL.php');
require_once('config.php');
require_once('include/view.php');

if(!isset($_SESSION['Center_Username'])){
    header("Location: index.php?login");
    exit;
}

if((!isset($_GET['id']))or(abs($_GET['id'])=='')){
    header("Location: forum.php");
	exit;
}

$post = $SQL->query("SELECT * FROM forum WHERE id = '%d' AND posted = '%s'",array($_GET['id'],$_SESSION['Center_Username']));
$post_row = $post->fetch_assoc();
$post_num_rows = $post->num_rows;

if($post_num_rows<1){
	header("Location: forum.php");
	exit;
}

if(isset($_POST['block'])){
	$_auth = $SQL->query("SELECT * FROM `forum_block` WHERE id = '%d'",array(abs($_POST['block'])))->num_rows;
	if($_auth>0){
		$post = $SQL->query("UPDATE `forum` SET `block` = '%d' WHERE  `id` = '%d'",array($_POST['block'],$_GET['id']));
		header("Location: forumview.php?blockmoving&fid=".$_POST['block']."&id=".$post_row['id']);
	}
}


$view = new View('view/new_theme.html','include/nav.php',$center['site_name'],'帖子移動');
$view->addScript("https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js");
$view->addScript("include/js/channel.js");
$view->addScript("include/js/notice.js");

$_block_query=$SQL->query("SELECT * FROM `forum_block` ORDER BY `position` ASC");
$_block =$_block_query->fetch_assoc();

?>
<div class="main">
<h2>Post Move</h2>
<form action="forummove.php?id=<?php echo $post_row['id'] ?>" method="POST" name="form1">
		<div class="controls">
			Let<b><?php echo $post_row['post_title']; ?></b>
		</div>
		<div class="controls">
			Move to&nbsp;
			<select class="input-medium" name="block" required="required">
			<?php do{ ?>
				<option value="<?php echo $_block['id']; ?>"><?php echo $_block['blockname']; ?></option>
			<?php }while ($_block =  $_block_query->fetch_assoc()); ?>
			</select>
		</div>
		
	<input type="submit" name="button" class="btn btn-primary" value="Move" />
</form>
</div>
<?php
$view->render();
?>