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

if(isset($_POST['del']) && $_POST['del'] != ''){
    $del_sql[] = sprintf("DELETE FROM forum WHERE id IN (%s)",implode(",",$_POST['del']));
    $del_sql[] = sprintf("DELETE FROM forum_reply WHERE post IN (%s)",implode(",",$_POST['del']));
    foreach($del_sql as $val){
		$SQL->query($val);
	}
	header("Location: forum.php?del&fid=".$_GET['fid']);
}elseif(isset($_GET['delblock']) && abs($_GET['delblock']) != ''){
	$_post_query=$SQL->query("SELECT `id` FROM `forum` WHERE block = '%d'",array(abs($_GET['delblock'])));
	if($_post_query->num_rows>0){
		$_post =$_post_query->fetch_assoc();
		$del_sql[] = sprintf("DELETE FROM forum_reply WHERE post IN (%s)",implode(",",$_post));
	}
	$del_sql[] = sprintf("DELETE FROM forum_block WHERE id =%d",abs($_GET['delblock']));
    $del_sql[] = sprintf("DELETE FROM forum WHERE block = %d",abs($_GET['delblock']));
    foreach($del_sql as $val){
		$SQL->query($val);
	}
	header("Location: forum.php?del");
}elseif(isset($_GET['newblock'])&&sc_namefilter($_POST['blockname'])!=''){

	$SQL->query("INSERT INTO `forum_block` (`blockname`, `position`, `ptime`) VALUES ('%s', '1', now())",array(sc_namefilter($_POST['blockname']),1));
	
}elseif(isset($_GET['rename']) &&abs($_GET['rename'])!='' && isset($_POST['rename'])){
	$SQL->query("UPDATE forum_block SET blockname = '%s' WHERE id = '%d'",array($_POST['rename'],abs($_GET['rename'])));
	$_GET['rename']=false;
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

$view = new View('../view/new_theme.html','../include/admin_nav.php',$center['site_name'],'論壇管理',true);
$view->addScript("https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js");
$view->addScript("../include/js/channel.js");
?>
<div class="main">
<?php if(isset($_GET['del'])){?>
	<div class="alert alert-success">刪除成功！</div>
<?php } ?>
<?php if(isset($_GET['fid'])){ ?>
<h2><?php echo $_block['blockname']; ?></h2>
<?php
if($post_totalrows == 0){
?>
<div class="alert alert-error">沒有帖子！</div>
<?php
}else{
?>
<form id="form1" name="form1" action="forum.php?fid=<?php echo abs($_GET['fid']); ?>" method="POST">
<input type="button" name="button" class="btn btn-danger" value="刪除" onClick="if(window.confirm('確定刪除所選取的帖子？\n\n提醒您，該帖子的回覆也會一並刪除！')){document.getElementById('form1').submit();}">
		<table class="table table-striped table-hover">
			<thead>
				<tr>
					<th></th>
					<th>帖子</th>
					<th>作者/發表時間</th>
					<th>回覆</th>
					<th>最後回覆</th>
				</tr>
			</thead>
			<tbody>
				<?php do{
					$_post_reply_query = $SQL->query("SELECT * FROM `forum_reply` WHERE `post`='%d' ORDER BY `ptime` DESC",array($post_row['id']));
					$_post_reply_row=$_post_reply_query->fetch_assoc();
					$_post_reply_num_rows = $_post_reply_query->num_rows;
				?>
				<tr>
					<td><input name="del[]" type="checkbox" id="del[]" value="<?php echo $post_row['id']; ?>" /></td>
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
?>
</form>
<?php
}elseif(isset($_GET['rename'])&& abs($_GET['rename']) != ''){
	if($SQL->query("SELECT * FROM `forum_block` WHERE `id`='%d'",array(abs(intval($_GET['rename']))))->num_rows<1){
		die;
	}
	$_block=$SQL->query("SELECT * FROM `forum_block` WHERE `id`='%d'",array(abs(intval($_GET['rename']))))->fetch_assoc();
?>
<h2>重新命名</h2>
<form id="rename" name="rename" action="forum.php?rename=<?php echo $_block['id']; ?>" method="POST">
		<div class="controls">
			<input name="rename" type="text" class="input-xlarge" required="required" value="<?php echo $_block['blockname']; ?>">
		</div>
		<div class="controls">
			<input class="btn btn-success" type="submit" value="改名">
		</div>
</form>
<?php }else{ ?>
<h2>論壇管理</h2>
<form id="newblock" name="newblock" action="forum.php?newblock" method="POST">
	<div class="controls">
		<input name="blockname" type="text" class="input-large" required="required">
		<input type="submit" class="btn btn-success" value="新增區塊">
	</div>
</form>
	<table class="table table-striped">
		<thead>
			<tr>
				<th>區塊</th>
				<th>帖數</th>
				<th>最後發帖</th>
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
				<td>
					&nbsp;<a class="btn btn-warning" href="forum.php?rename=<?php echo $post_row['id']; ?>">改名</a>
					<?php if($post_totalrows>1){ ?>
					<a class="btn btn-danger" href="javascript:if(window.confirm('確定刪除所選取的區域？\n\n提醒您，該區域的帖子也會一並刪除！')){document.location.href='forum.php?delblock=<?php echo $post_row['id']; ?>'}">刪除</a>
					<?php } ?>
					
				</td>
			</tr>
			<?php }while($post_row = $post->fetch_assoc()); ?>
		</tbody>
    </table>
<?php } ?>
</div>
<?php
$view->render();
?>