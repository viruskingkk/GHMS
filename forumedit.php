<?php
require_once('Connections/SQL.php');
require_once('config.php');
require_once('include/view.php');

if(!isset($_SESSION['Center_Username'])){
    header("Location: index.php?login");
	exit;
}

if(!isset($_GET['id']) || $_GET['id'] == ''){
	header("Location: forum.php");
	exit;
}

if(isset($_GET['post'])){
	if(isset($_GET['reply'])){
		header("Location: forum.php");
		exit;
	}
	
	$post = $SQL->query("SELECT * FROM forum WHERE id = '%d' AND posted = '%s'",array($_GET['id'],$_SESSION['Center_Username']));
	$post_row = $post->fetch_assoc();
	$post_totalrows = $post->num_rows;

	if($post_totalrows<=0){
		header("Location: forum.php");
		exit;
	}
	
	if(isset($_POST['title']) && isset($_POST['post']) && trim(htmlspecialchars($_POST['title'])) != '' && trim(strip_tags($_POST['post'],$center['html_tag']),"&nbsp;") != '') {
		$SQL->query("UPDATE forum SET post_title = '%s', post = '%s' WHERE id = '%d' AND posted = '%s'",array(
			htmlspecialchars($_POST['title']),
			sc_xss_filter($_POST['post']),
			$_GET['id'],
			$_SESSION['Center_Username']
		));
		header("Location: forumview.php?id=".$post_row['id']);
	}
	
}elseif(isset($_GET['reply'])) {
	if(isset($_GET['post'])){
		header("Location: forum.php");
		exit;
	}
	
	$reply = $SQL->query("SELECT * FROM forum_reply WHERE id = '%d' AND posted='%s'",array($_GET['id'],$_SESSION['Center_Username']));
	$reply_row = $reply->fetch_assoc();
	$reply_totalrows = $reply->num_rows;

	if($reply_totalrows<=0){
		header("Location: forum.php");
		exit;
	}
	
	if(isset($_POST['reply']) && trim(strip_tags($_POST['reply']),"&nbsp;") != '') {
	$SQL->query("UPDATE forum_reply SET reply = '%s' WHERE id = '%d' AND posted = '%s'",array(
		sc_xss_filter($_POST['reply']),
		$_GET['id'],
		$_SESSION['Center_Username']
	));
	header("Location: forumview.php?id=".$reply_row['post']);
	}
	
}else{
	header("Location: forum.php");
	exit;
}


$view = new View('view/new_theme.html','include/nav.php',$center['site_name'],'論壇編輯');
$view->addCSS("include/js/cleditor/jquery.cleditor.css");
$view->addScript("https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js");
$view->addScript("include/js/cleditor/jquery.cleditor.js");
$view->addScript("include/js/cleditor/jquery.cleditor.icon.js");
$view->addScript("include/js/cleditor/jquery.cleditor.table.js");
$view->addScript("include/js/cleditor/jquery.cleditor.serverImg.js");
$view->addScript("include/js/jquery.validate.js");
$view->addScript("include/js/channel.js");
$view->addScript("include/js/notice.js");
?>
<div class="main">
<?php if(isset($_GET['replying'])){?>
	<div class="prompt">Reply Success！</div>
<?php }
if(isset($_GET['reply'])){
?>
<script type="text/javascript">
$(function(){
	$("#reply").cleditor({width:'99%', height:300, useCSS:true})[0].focus();
	$("#form1").validate({
		rules:{
			reply:{required:true}
		}
	});
});
</script>
<h2>Edit Reply</h2>
<form action="forumedit.php?reply&id=<?php echo $_GET['id']; ?>" method="POST" name="form1">
	<div class="controls">
		<label for="reply">Reply  content：</label>
	</div>
	<div style="margin:auto;">
		<textarea name="reply" cols="65" rows="10" id="reply" required="required"><?php echo removal_escape_string($reply_row['reply']); ?></textarea>
	</div>
	<p><input type="submit" name="button" class="btn btn-primary" value="Edit Reply" /></p>
</form>
<?php } elseif(isset($_GET['post'])){ ?>
<script type="text/javascript">
$(function(){
    $("#cleditor").cleditor({width:'99%', height:350, useCSS:true})[0].focus();
	$("#form1").validate({
		rules:{
			title:{required:true},
			post:{required:true}
		}
	});
});
</script>
<h2>Edit Post</h2>
<form action="forumedit.php?post&id=<?php echo $_GET['id']; ?>" method="POST" name="form1">
	<input name="title" class="input-block-level" type="text" value="<?php echo $post_row['post_title']; ?>" required="required" placeholder="標題">
	<textarea id="cleditor" name="post" class="input-block-level" rows="10"><?php echo htmlspecialchars(removal_escape_string($post_row['post'])); ?></textarea>
	</div>
	<p><input type="submit" name="button" class=" btn btn-primary" value="Edit Post" /></p>
</form>
<?php } ?>
</div>
<?php
$view->render();
?>