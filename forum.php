<?php
require_once('Connections/SQL.php');
require_once('config.php');
require_once('include/view.php');

if(!isset($_SESSION['Center_Username'])){
    header("Location: index.php?login");
    exit;
}

$view = new View('view/new_theme.html','include/nav.php',$center['site_name'],'Support Center');
$view->addCSS("include/js/cleditor/jquery.cleditor.css");
$view->addScript("https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js");
$view->addScript("include/js/cleditor/jquery.cleditor.js");
$view->addScript("include/js/cleditor/jquery.cleditor.icon.js");
$view->addScript("include/js/cleditor/jquery.cleditor.table.js");
$view->addScript("include/js/cleditor/jquery.cleditor.serverImg.js");
$view->addScript("include/js/jquery.validate.js");
$view->addScript("include/js/channel.js");
$view->addScript("include/js/notice.js");

if(isset($_POST['title']) && isset($_POST['post']) && trim(htmlspecialchars($_POST['title'])) != '' && trim($_POST['post'],"&nbsp;") != '') {

	if($SQL->query("SELECT * FROM `forum_block` WHERE `id`='%d'",array(abs(intval($_POST['block']))))->num_rows>0){
	
		$SQL->query("INSERT INTO `forum` (`post_title`, `post`,`block`, `posted`, `ptime`) VALUES ('%s', '%s','%d', '%s', '%s')",array(
			htmlspecialchars($_POST['title']),
			sc_xss_filter($_POST['post']),
			abs($_POST['block']),
			$_SESSION['Center_Username'],
			date("Y-m-d H:i:s")
		));
		header("Location: forum.php?posting&fid=".$_POST['block']);
	}
}
if(isset($_GET['fid'])){
	if($SQL->query("SELECT * FROM `forum_block` WHERE `id`='%d'",array(abs(intval($_GET['fid']))))->num_rows<1){
		header("Location: forum.php");
	}
	if(isset($_GET['page'])){
		$limit_start = abs(intval(($_GET['page']-1)*20));
		$post_sql = sprintf("SELECT * FROM `forum` WHERE `block`='%d' ORDER BY `ptime` DESC LIMIT %d,20",abs(intval($_GET['fid'])),$limit_start);
	} else {
		$limit_start=0;
		$post_sql = sprintf("SELECT * FROM `forum` WHERE `block`='%d' ORDER BY `ptime` DESC LIMIT %d,20",abs(intval($_GET['fid'])),$limit_start);
	}
	$_block = $SQL->query("SELECT * FROM `forum_block` WHERE `id`='%d'",array(abs(intval($_GET['fid']))))->fetch_assoc();
}else{
	$post_sql = sprintf("SELECT * FROM `forum_block` ORDER BY `position` ASC");
}

$post = $SQL->query($post_sql);
$post_row = $post->fetch_assoc();
$post_totalrows = $post->num_rows;
?>
<div class="main">
<?php if(isset($_GET['posting'])){?>
	<div class="alert alert-success"><span class="prompt">Publishing success!</span></div>
<?php }
if(isset($_GET['newpost'])) {
?>
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
<h2>Post Discussions</h2>
<form action="forum.php?newpost" method="POST" name="form1">
	<input name="title" class="input-block-level" type="text" placeholder="標題">
	<div class="controls">
		<label for="block">Block：</label>
		<select class="input-medium" name="block" required="required">
		<?php do{ ?>
			<option value="<?php echo $post_row['id']; ?>"><?php echo $post_row['blockname']; ?></option>
		<?php }while ($post_row = $post->fetch_assoc());  ?>
        
        
		</select>	</div>
	<textarea id="cleditor" name="post" class="input-block-level" rows="10"></textarea>
	<br><input id="button" name="button" class="btn btn-primary" type="submit" value="Published posts！">
</form>
<?php } elseif(isset($_GET['fid'])){ ?>
<h2><?php echo $_block['blockname']; ?></h2>
<?php
if($post_totalrows == 0){
?>
<div class="alert alert-error"><span class="prompt">No posts！</span></div>
<?php
}else{
?>
		<table class="table table-striped table-hover">
			<thead>
				<tr>
					<th>Title</th>
					<th><span class="author">Author</span>/Published</th>
					<th>Reply</th>
					<th>Last Reply</th>
				</tr>
			</thead>
			<tbody>
				<?php do{
					$_post_reply_query = $SQL->query("SELECT * FROM `forum_reply` WHERE `post`='%d' ORDER BY `ptime` DESC",array($post_row['id']));
					$_post_reply_row=$_post_reply_query->fetch_assoc();
					$_post_reply_num_rows = $_post_reply_query->num_rows;
				?>
				<tr>
					<td>
						<a href="forumview.php?id=<?php echo $post_row['id']; ?>">
						<?php echo $post_row['post_title']; ?>
						</a>
					</td>
					<td style="line-height:0.8em;font-size:92%;">
						<?php echo $post_row['posted']; ?>
						<br><span style="font-size:66%;"><?php echo date('Y-m-d H:i',strtotime($post_row['ptime'])); ?></span>
					</td>
					<td>
						<?php echo $_post_reply_num_rows; ?>
					</td>
					<td>
						<?php
							if($_post_reply_num_rows>0){
								
								echo '<div style="line-height:0.8em;font-size:92%;">'.$_post_reply_row['posted'].'<br><span style="font-size:66%;">'.date('Y-m-d H:i',strtotime($_post_reply_row['ptime'])).'</span></div>';
							}else{
								echo '無';
							}
						?>
						
					</td>
				</tr>
				<?php }while ($post_row = $post->fetch_assoc()); ?>
			</tbody>
		</table>
<?php
$nav_totalrows = $SQL->query("SELECT * FROM forum WHERE block = '%d'",array($_block['id']))->num_rows;

$pageTotal=ceil($nav_totalrows / 20);

if($pageTotal > 1){
	echo '<div class="pagination"><ul>';
	for($i=1;$i<=$pageTotal;$i++){
		if(@$_GET['page'] != $i){
				echo '<li><a href="forum.php?page='.$i.'&fid='.$_block['id'].'">'.$i.'</a></li>';
			}else{
				echo '<li class="active">'.$i.'</li>';
		}
	}
	echo '<br class="clearfix" /></ul></div>';
}}
}else{ ?>
<h2>Support Center</h2>
<?php
if($post_totalrows == 0){
?>
<div class="alert alert-error">No Classification!!！</div>
<?php }else{ ?>
	<table class="table table-striped">
		<thead>
			<tr>
				<th>Classification</th>
				<th>Amount</th>
				<th>Last Post</th>
			</tr>
		</thead>
		<tbody>
			<?php do{
				$_block_post_query = $SQL->query("SELECT * FROM `forum` WHERE `block`='%d' ORDER BY `ptime` DESC",array($post_row['id']));
				$_block_post_row=$_block_post_query->fetch_assoc();
				$_block_post_num_rows = $_block_post_query->num_rows;
			?>
			<tr>
				<td>
					<a href="forum.php?fid=<?php echo $post_row['id']; ?>">
					<?php echo $post_row['blockname']; ?>
					</a>
				</td>
				<td><?php echo $_block_post_num_rows; ?></td>
				<td>
					<?php
					if($_block_post_num_rows>0){
						echo date('Y-m-d H:i',strtotime($_block_post_row['ptime']));
					}else{
						echo '無';
					}?>
				</td>
			</tr>
			<?php }while($post_row = $post->fetch_assoc()); ?>
		</tbody>
    </table>
<?php } ?>
<?php } ?>
</div>
<?php
$view->render();
?>