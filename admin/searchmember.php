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

if(isset($_GET['search'])){
	if($_POST['level']=='admin'){
		$_level= "AND `level` = 'admin'";
	}elseif($_POST['level']=='user'){
		$_level= "AND `level` = 'user'";
 	}else{
		$_level='';
	}
	$POST_joined['0']=strtotime($_POST['joined']['0']);
	$POST_joined['1']=strtotime($_POST['joined']['1']);
	$POST_last_login['0']=strtotime($_POST['last_login']['0']);
	$POST_last_login['1']=strtotime($_POST['last_login']['1']);
	if($POST_joined['0']>0&&$POST_joined['1']>0){
		$_joined=sprintf(" AND `joined` BETWEEN '%s' AND '%s'",
					date('Y-m-d H:i:s',$POST_joined['0']),
					date('Y-m-d H:i:s',$POST_joined['1']));
	}elseif($POST_joined['0']>0){
		$_joined=sprintf(" AND `joined` > '%s'",
					date('Y-m-d H:i:s',$POST_joined['0']));
	}elseif($POST_joined['1']>0){
		$_joined=sprintf(" AND `joined` < '%s'",
					date('Y-m-d H:i:s',$POST_joined['1']));
	}
	else{
		$_joined='';
	}
	if($POST_last_login['0']>0&&$POST_last_login['1']>0){
		$_last_login=sprintf(" AND `last_login` BETWEEN '%s' AND '%s'",
					date('Y-m-d H:i:s',$POST_last_login['0']),
					date('Y-m-d H:i:s',$POST_last_login['1']));
	}elseif($POST_last_login['0']>0){
		$_last_login=sprintf(" AND `last_login` > '%s'",
					date('Y-m-d H:i:s',$POST_last_login['0']));
	}elseif($POST_last_login['1']>0){
		$_last_login=sprintf(" AND `last_login` < '%s'",
					date('Y-m-d H:i:s',$POST_last_login['1']));
	}
	else{
		$_last_login='';
	}
	
	//echo vsprintf("SELECT * FROM member WHERE `name` LIKE '%%%s%%' AND `email` LIKE '%%%s%%' AND `web_site` LIKE '%%%s%%' $_last_login $_joined $_level ORDER BY id ASC",array(sc_namefilter($_POST['name']),$_POST['email'],$_POST['web_site']));die();
	
	$member_query = $SQL->query("SELECT * FROM member WHERE `name` LIKE '%%%s%%' AND `email` LIKE '%%%s%%' AND `web_site` LIKE '%%%s%%' $_last_login $_joined $_level ORDER BY id ASC",array(sc_namefilter($_POST['name']),$_POST['email'],$_POST['web_site']));
	$member_row = $member_query->fetch_assoc();
	$member_totalrows = $member_query->num_rows;
}

$view = new View('../view/new_theme.html','../include/admin_nav.php',$center['site_name'],'會員管理',true);
$view->addScript("https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js");
$view->addScript("../include/js/jquery.validate.js");
$view->addScript("../include/js/channel.js");

?>
<?php if(!isset($_GET['search'])){ ?>
<div class="main">
<h2 class="subtitle">會員搜尋</h2>
<form id="form1" name="form1" class="form-horizontal" action="searchmember.php?search" method="POST">	
	<div class="control-group">
		<label class="control-label" for="name">帳號：</label>
		<div class="controls">
			<input name="name" type="text" class="input-xlarge" id="name">
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="email">電子信箱：</label>
		<div class="controls">	
			<input name="email" type="text" id="email" class="input-xlarge" />
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="web_site">個人網站：</label>
		<div class="controls">	
			<input name="web_site" type="text" id="web_site" class="input-xlarge" />
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="joined">註冊日期：</label>
		<div class="controls">
			<input name="joined[]" type="date" class="input-small" /> - 
			<input name="joined[]" type="date" class="input-small" />(YYYY-MM-DD)
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="last_login">最後登入：</label>
		<div class="controls">
			<input name="last_login[]" type="date" class="input-small" /> - 
			<input name="last_login[]" type="date" class="input-small" />(YYYY-MM-DD)
		</div>
	</div><div class="control-group">
		<label class="control-label" for="level">權限：</label>
		<div class="controls">
			<select name="level" id="level" class="input-xlarge">
				<option value="all">所有</option>
			<option value="user">普通會員</option>
			<option value="admin">管理員</option>
			</select>
		</div>
	</div>
	<div class="control-group">
		<div class="controls">
			<input name="button" type="submit" id="button" class="btn btn-info btn-large" value="開始搜尋" />
		</div>
	</div>
</form>
<?php }else{if ($member_totalrows>0){ ?>
<h2 class="subtitle">會員搜尋</h2>
<table class="table table-striped table-hover">
  <tr>
    <th width="5%" bgcolor="#C7E4FF">ID</th>
    <th width="20%" bgcolor="#C7E4FF">帳號名稱</th>
	<th width="15%" bgcolor="#C7E4FF">電子信箱</th>
    <th width="15%" bgcolor="#C7E4FF">電話</th>
	<th width="15%" bgcolor="#C7E4FF">個人網站</th>
	<th width="15%" bgcolor="#C7E4FF">註冊日期</th>
	<th width="15%" bgcolor="#C7E4FF">最後登入</th>
    <th width="5%" bgcolor="#C7E4FF">權限</th>
    <th width="10%" bgcolor="#C7E4FF">管理</th>
  </tr>
<?php do { ?>
  <tr>
    <td><?php echo $member_row['id'] ;?></td>
    <td><?php echo $member_row['name'] ;?></td>
	<td><small><?php echo $member_row['email'] ;?></small></td>
    <td><small><?php echo $member_row['tel'] ;?></small></td>
	<td><small><?php echo $member_row['web_site'] ;?></small></td>
	<td style="line-height:0.8em;"><small><?php echo $member_row['joined'] ;?></small></td>
	<td style="line-height:0.8em;"><small><?php echo $member_row['last_login'] ;?></small></td>
    <td><?php echo $member_row['level'] ;?></td>
    <td><a href="member.php?edit=<?php echo $member_row['id'] ;?>">編輯</a>│<a href="javascript:if(confirm('確定刪除此會員？'))location='member.php?del=<?php echo $member_row['id'] ;?>'">刪除</a></td>
  </tr>
<?php } while ($member_row = $member_query->fetch_assoc()); ?>
</table>

<?php }else{ ?>
	<div class="alert alert-error">很抱歉，沒有符合的資料！</div>
<?php }} ?>
</div>
<?php
$view->render();
?>