<?php
set_include_path('../include/');
$includepath = true;

require_once('../Connections/SQL.php');
require_once('../config.php');
require_once('view.php');

if(!isset($_SESSION['Center_Username']) or $_SESSION['Center_UserGroup'] != 'admin'){
    header("Location: ../index.php");
    exit;
}

if((!isset($_GET['id']))or($_GET['id']=='')){
    header("Location: forum.php");
	exit;
}

if(isset($_GET['delreply']) && $_GET['delreply'] != ''){
	$SQL->query("DELETE FROM forum_reply WHERE id = %d",array($_GET['delreply']));
	header("Location: forumview.php?delreply&id=".$_GET['id']);
}


if(isset($_POST['post']) && trim(strip_tags($_POST['post']),"&nbsp;") != ''){
	$SQL->query("UPDATE forum SET post = '%s' WHERE id = '%s'",array(sc_xss_filter($_POST['post']),$_GET['id']));
	
	for($i = 0; $i < count($_POST['reply']); $i++){
		$SQL->query("UPDATE forum_reply SET reply = '%s' WHERE id = '%s'",array(sc_xss_filter($_POST['reply'][$i]),$_POST['id'][$i]));
	}
	header("Location: forumview.php?edit&id=".$_GET['id']);
}

$post_query = $SQL->query("SELECT * FROM forum WHERE id = '%d'",array($_GET['id']));
$post_row = $post_query->fetch_assoc();
$post_totalrows = $post_query->num_rows;

if($post_totalrows<=0){
	header("Location: forum.php");
	exit;
}

if(isset($_GET['page'])){
	$limit_start = abs(intval(($_GET['page']-1)*20));
	$reply_sql = sprintf("SELECT * FROM forum_reply WHERE post = '%s' ORDER BY id ASC LIMIT %d,20",$_GET['id'],$limit_start);
} else {
	$limit_start = 0;
	$reply_sql = sprintf("SELECT * FROM forum_reply WHERE post = '%s' ORDER BY id ASC LIMIT %d,20",$_GET['id'],$limit_start);
}
$reply_query = $SQL->query($reply_sql);
$reply_totalrows = $reply_query->num_rows;

$view = new View('../view/new_theme.html','../include/admin_nav.php',$center['site_name'],$post_row['post_title'],true);
$view->addCSS("../include/js/cleditor/jquery.cleditor.css");
$view->addScript("https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js");
$view->addScript("../include/js/cleditor/jquery.cleditor.js");
$view->addScript("../include/js/cleditor/jquery.cleditor.icon.js");
$view->addScript("../include/js/cleditor/jquery.cleditor.table.js");
$view->addScript("../include/js/jquery.validate.js");
$view->addScript("../include/js/channel.js");
?>
<script type="text/javascript">
	$(document).ready(function() {
		$("#form1 textarea").cleditor({width:"100%", height:250, useCSS:true});
	});
</script>
<div class="main">
<?php if(isset($_GET['edit'])){?>
	<div class="alert alert-success">編輯成功！</div>
<?php }
if(isset($_GET['delreply']) && $_GET['delreply'] == ''){?>
	<div class="alert alert-success">刪除成功！</div>
<?php } ?>
<h2><?php echo $post_row['post_title']; ?></h2>
<form id="form1" name="form1" action="forumview.php?id=<?php echo $_GET['id']; ?>" method="POST">
	<input type="submit" name="button" class="btn btn-large btn-warning" value="編輯" />
	<div class="post">
		<ul class="inline">
			<li><img src="../include/avatar.php?id=<?php echo $post_row['posted']; ?>" class="avatar"></li>
			<li style="font-size:120%;"><?php echo $post_row['posted']; ?></li>
			<li>發表於&nbsp;<?php echo $post_row['ptime']; ?></li>
			<li>1&nbsp;樓</li>
		</ul>
		<div class="con">
			<textarea name="post" cols="65" rows="10" required="required"><?php echo removal_escape_string($post_row['post']); ?></textarea>
		</div>
	</div>
<?php
if($reply_totalrows!=0){
	$reply_floor = 1+$limit_start;
	while ($reply_row = $reply_query->fetch_assoc()) {
		$reply_floor++;
?>
	<div class="post">
		<ul class="inline">
			<li><img src="../include/avatar.php?id=<?php echo $reply_row['posted']; ?>" class="avatar"></li>
			<li style="font-size:130%;"><?php echo $reply_row['posted']; ?></li>
			<li>發表於&nbsp;<?php echo $reply_row['ptime']; ?></li>
			<li><?php echo $reply_floor; ?>&nbsp;樓</li>
			<li>
				<a href="forumview.php?delreply=<?php echo $reply_row['id']; ?>&id=<?php echo $_GET['id']; ?>" class="btn btn-danger btn-small">刪除此回覆</a>
			</li>
		</ul>
		<div class="con">
			<textarea name="reply[]" cols="65" rows="10" required="required"><?php echo removal_escape_string($reply_row['reply']); ?></textarea>
			<input name="id[]" type="hidden" value="<?php echo $reply_row['id']; ?>">
		</div>
	</div>
<?php
	}
}
?>
<input type="submit" name="button" class="btn btn-large btn-warning" value="編輯" />
</form>
<?php
$nav_totalrows = $SQL->query("SELECT * FROM forum_reply WHERE post = %s",array($_GET['id']))->num_rows;
$pageTotal = ceil($nav_totalrows / 20);

if($pageTotal>1){
	echo '<div class="pagination"><ul>';
	for($i=1;$i<=$pageTotal;$i++){
		if(@$_GET['page']!=$i){
				echo '<li><a href="forumview.php?id='.$_GET['id'].'&page='.$i.'">'.$i.'</a></li>';
			}else{
				echo '<li class="active">'.$i.'</li>';
		}
	}
	echo '<br class="clearfix" /></ul></div>';
}
?>
</div>
<?php
$view->render();
?>