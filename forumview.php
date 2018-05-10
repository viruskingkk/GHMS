<?php
require_once('Connections/SQL.php');
require_once('config.php');
require_once('include/view.php');

if(!isset($_SESSION['Center_Username'])){
    header("Location: index.php?login");
    exit;
}

if((!isset($_GET['id']))or($_GET['id']=='')){
    header("Location: forum.php");
	exit;
}

$post = $SQL->query("SELECT * FROM forum WHERE id = '%s'",array($_GET['id']));
$post_row = $post->fetch_assoc();
$post_totalrows = $post->num_rows;

if($post_totalrows<=0){
	header("Location: forum.php");
	exit;
}
$_block = $SQL->query("SELECT * FROM `forum_block` WHERE `id`='%d'",array(abs(intval($post_row['block']))))->fetch_assoc();

if(isset($_GET['page'])){
	$limit_start = abs(intval(($_GET['page']-1)*20));
	$reply_sql = sprintf("SELECT * FROM forum_reply WHERE post = '%s' ORDER BY id ASC LIMIT %d,20",$_GET['id'],$limit_start);
} else {
	$limit_start = 0;
	$reply_sql = sprintf("SELECT * FROM forum_reply WHERE post = '%s' ORDER BY id ASC LIMIT %d,20",$_GET['id'],$limit_start);
}
$reply_query = $SQL->query($reply_sql);
$reply_totalrows = $reply_query->num_rows;


if((isset($_GET['reply']))&& isset($_POST['reply']) && trim($_POST['reply'],"&nbsp;") != ''){
	$SQL->query("INSERT INTO forum_reply ( post,reply, ptime, posted) VALUES ('%s','%s','%s','%s')",array(
		$_GET['id'],
		sc_xss_filter($_POST['reply']),
		date("Y-m-d H:i:s"),
		$_SESSION['Center_Username']
	));
	if($_SESSION['Center_Username']!=$post_row['posted']){
		 sc_addnotice(
			"forumview.php?replying&id=".$_GET['id'],
			$_SESSION['Center_Username']."在您的帖子中發表回覆",
			$_SESSION['Center_Username'],
			$post_row['posted']
		);
	}
	header("Location: forumview.php?replying&id=".$_GET['id']);
}

$view = new View('view/new_theme.html','include/nav.php',$center['site_name'],$post_row['post_title']);
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
	<div class="alert alert-success">Reply Success！</div>
<?php }elseif(isset($_GET['blockmoving'])){ ?>
	<div class="alert alert-success">Move success！</div>
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
<form action="forumview.php?id=<?php echo $_GET['id']; ?>&reply" method="POST" name="form1">
	<div class="controls">
		<label for="reply">Reply Content：</label>
	</div>
	<div style="margin:auto;">
		<textarea name="reply" cols="65" rows="10" id="reply" required="required"></textarea>
	</div>
	<p><input type="submit" name="button" class="btn btn-primary" value="Reply" /></p>
</form>
<?php } else { ?>
<ul class="breadcrumb">
	<li><a href="forum.php">Support Center</a><span class="divider">/</span></li>
	<li><a href="forum.php?fid=<?php echo $_block['blockname']; ?>"><?php echo $_block['blockname']; ?></a><span class="divider">/</span></li>
	<li class="active"><?php echo lt_replace($post_row['post_title']); ?></li>
</ul>
<h2>
	<?php echo lt_replace($post_row['post_title']); ?>&nbsp;<a href="forumview.php?id=<?php echo $post_row['id']; ?>&reply" class="btn btn-primary btn-mini">New Reply</a>
</h2>
<div id="1" class="post">
	<ul class="inline">
		<li><img src="include/avatar.php?id=<?php echo $post_row['posted']; ?>" class="avatar"></li>
		<li style="font-size:120%;"><?php echo $post_row['posted']; ?></li>
		<li>Published in&nbsp;<?php echo $post_row['ptime']; ?></li>
		<li>1&nbsp;Piece
		  <?php if($post_row['posted'] == $_SESSION['Center_Username']){ ?>
		</li>
		<li>
			<a href="forumedit.php?post&id=<?php echo $post_row['id']; ?>" class="btn btn-info btn-small">Edit</a>
			<a href="forummove.php?id=<?php echo $post_row['id']; ?>" class="btn btn-warning btn-small">Move</a>
			<a href="javascript:if(confirm('Delete？'))location='mypost.php?del=<?php echo $post_row['id']; ?>'" class="btn btn-danger btn-small">Delete</a>	
		</li>
		<?php } ?>
	</ul>
    <div class="con"><?php echo removal_escape_string($post_row['post']); ?></div>
</div>
<?php
if($reply_totalrows != 0){
	$reply_floor = 1+$limit_start;
	while ($reply_row = $reply_query->fetch_assoc()){
		$reply_floor++;
?>

<div id="<?php echo $reply_floor; ?>" class="post">
	<ul class="inline">
		<li><img src="include/avatar.php?id=<?php echo $reply_row['posted']; ?>" class="avatar"></li>
		<li style="font-size:130%;"><?php echo $reply_row['posted']; ?></li>
		<li>Published in&nbsp;<?php echo $reply_row['ptime']; ?></li>
		<li><?php echo $reply_floor; ?>&nbsp;Piece</li>
		<?php if($reply_row['posted']==$_SESSION['Center_Username']){ ?>
		<li><a href="forumedit.php?reply&id=<?php echo $reply_row['id']; ?>" class="btn btn-info btn-small">Edit</a></li>
		<?php } ?>
	</ul>
	<div class="con"><?php echo removal_escape_string($reply_row['reply']); ?></div>
</div>
<?php
	}
}
$nav = $SQL->query("SELECT * FROM forum_reply WHERE post = '%s'",array($_GET['id']));
$pageTotal = ceil($nav->num_rows / 20);

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
<?php } ?>
</div>
<?php
$view->render();
?>